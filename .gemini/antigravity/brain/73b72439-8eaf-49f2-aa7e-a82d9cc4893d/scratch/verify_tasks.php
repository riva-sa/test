<?php

use App\Models\User;
use App\Models\UnitOrder;
use App\Models\Project;
use Spatie\Permission\Models\Role;

function testAccessibleByScope() {
    echo "--- Testing scopeAccessibleBy ---\n";
    
    // 1. Test Admin User
    $admin = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first();
    if ($admin) {
        $count = UnitOrder::accessibleBy($admin)->count();
        echo "Admin ({$admin->name}) see $count orders.\n";
    }

    // 2. Test Sales Manager
    $manager = User::whereHas('roles', fn($q) => $q->where('name', 'sales_manager'))->first();
    if ($manager) {
        $count = UnitOrder::accessibleBy($manager)->count();
        echo "Sales Manager ({$manager->name}) see $count orders.\n";
    }

    // 3. Test Sales User
    $sales = User::whereHas('roles', fn($q) => $q->where('name', 'sales'))->first();
    if ($sales) {
        $count = UnitOrder::accessibleBy($sales)->count();
        echo "Sales User ({$sales->name}) see $count orders.\n";
        
        // Detailed check for sales user
        $projectCount = Project::where('sales_manager_id', $sales->id)->count();
        echo " - User is manager of $projectCount projects.\n";
        
        $permissionCount = $sales->orderPermissions()->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count();
        echo " - User has $permissionCount active explicit permissions.\n";
    }
}

function testMiddlewareLogic() {
    echo "\n--- Testing CheckUserRole Middleware Logic Simulation ---\n";
    
    $middleware = new \App\Http\Middleware\CheckUserRole();
    $request = request();
    
    $rolesToTest = ['sales_manager', 'developer'];
    
    // Simulate a sales_manager user
    $manager = User::whereHas('roles', fn($q) => $q->where('name', 'sales_manager'))->first();
    if ($manager) {
        $request->setUserResolver(fn() => $manager);
        try {
            $middleware->handle($request, function($req) { return "Passed"; }, ...$rolesToTest);
            echo "Manager ({$manager->name}) access with roles " . implode(',', $rolesToTest) . ": PASSED\n";
        } catch (\Exception $e) {
            echo "Manager access: FAILED (" . get_class($e) . ": " . $e->getMessage() . ")\n";
        }
    }

    // Simulate a sales user (not in allowed roles)
    $sales = User::whereHas('roles', fn($q) => $q->where('name', 'sales'))->whereDoesntHave('roles', fn($q) => $q->whereIn('name', ['sales_manager', 'developer', 'Admin']))->first();
    if ($sales) {
        $request->setUserResolver(fn() => $sales);
        try {
            $middleware->handle($request, function($req) { return "Passed"; }, ...$rolesToTest);
            echo "Sales User ({$sales->name}) access with roles " . implode(',', $rolesToTest) . ": FAILED (Should have been blocked)\n";
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            echo "Sales User access with roles " . implode(',', $rolesToTest) . ": BLOCKED (Correct - 403)\n";
        }
    }
}

testAccessibleByScope();
testMiddlewareLogic();
