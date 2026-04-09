<?php

namespace App\Livewire\Developer;

use App\Models\Project;
use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DeveloperDashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        if (! $user->developer_id) {
            abort(403, 'لم يُربط حسابك بشركة مطور.');
        }
        
        $developerId = $user->developer_id;

        $projectIds = Project::query()
            ->where('developer_id', $developerId)
            ->pluck('id');

        $projectsCount = $projectIds->count();

        $ordersQuery = UnitOrder::query()->whereIn('project_id', $projectIds);

        $ordersTotal = (clone $ordersQuery)->count();

        $ordersByStatus = (clone $ordersQuery)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status');

        $ordersLast30 = (clone $ordersQuery)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $projectVisits = Project::query()
            ->where('developer_id', $developerId)
            ->sum('visits_count');

        $projectViews = Project::query()
            ->where('developer_id', $developerId)
            ->sum('views_count');

        $projectShows = Project::query()
            ->where('developer_id', $developerId)
            ->sum('shows_count');

        $unitVisits = DB::table('units')
            ->join('projects', 'units.project_id', '=', 'projects.id')
            ->where('projects.developer_id', $developerId)
            ->sum('units.visits_count');

        $unitViews = DB::table('units')
            ->join('projects', 'units.project_id', '=', 'projects.id')
            ->where('projects.developer_id', $developerId)
            ->sum('units.views_count');

        $stats = [
            'projects_count' => $projectsCount,
            'orders_total' => $ordersTotal,
            'orders_last_30' => $ordersLast30,
            'orders_by_status' => $ordersByStatus,
            'site_visits_total' => (int) $projectVisits + (int) $unitVisits,
            'views_total' => (int) $projectViews + (int) $unitViews,
            'shows_total' => (int) $projectShows,
        ];

        $recentProjects = Project::query()
            ->where('developer_id', $developerId)
            ->withCount([
                'units',
            ])
            ->orderByDesc('updated_at')
            ->limit(8)
            ->get();

        return view('livewire.developer.developer-dashboard', [
            'stats' => $stats,
            'recentProjects' => $recentProjects,
        ])->layout('layouts.custom');
    }
}
