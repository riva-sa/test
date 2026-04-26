# Quickstart Guide: Lead Form Enhancements

## Overview
This guide provides steps to implement the lead form enhancements feature in the Riva CRM system.

## Prerequisites
- PHP 8.2+
- Composer
- MySQL/PostgreSQL database
- Existing Laravel 11.x/Filament 3.x project

## Implementation Steps

### 1. Phone Number Normalization
Create an action to normalize phone numbers:
```bash
php artisan make:action NormalizePhoneAction
```
Implement the logic to:
- Remove all spaces and symbols
- Ensure the number starts with +966
- Flag numbers that cannot be formatted for manual review but still allow lead creation

### 2. Optional Email Field
Update the UnitOrder model migration to make email nullable:
```bash
php artisan make:migration make_email_optional_in_unit_orders_table
```
In the migration:
```php
$table->string('email')->nullable()->change();
```
Update validation rules to make email optional.

### 3. Sales Staff Order Editing
Update Livewire component policies to allow sales staff to edit customer and unit information:
- Modify the `update` method in the Order management Livewire component
- Add authorization logging for unauthorized attempts (but still allow per clarification)
- Update Blade views to show edit fields for sales staff role

### 4. Marketing Attribution Field Updates
- Update Blade views to change "Ad Group (Set)" label to "Ad Name"
- Create a middleware or event listener to auto-set marketing_source based on platform:
  - Detect platform from referral/user-agent or form field
  - Set to Snapchat, Instagram, TikTok, etc.
  - For unrecognized platforms, store the raw value as-is

### 5. Exclude New Orders from Homepage and Statistics
Modify the dashboard and statistics queries:
- Add a condition to exclude orders with status 'new' or created within last X minutes
- Update the ManagerDashboard Livewire component query
- Update statistics widgets to use the same filtered query

### 6. Order Status Notifications
Create a notification and event listener:
```bash
php artisan make:notification OrderStatusChangedNotification
php artisan make:event OrderStatusUpdated
```
- Listen for order status updates
- Dispatch notification to assigned employee via mail and database channels
- Implement structured JSON logging for the event

### 7. Sales Manager Statement Notifications
Similar to status notifications:
```bash
php artisan make:notification ManagerStatementAddedNotification
php artisan make:event ManagerStatementAdded
```
- Listen for statement creation on orders
- Notify assigned employees
- Log the event with structured JSON

### 8. Enhanced Order Receipt Mechanism
- Modify the lead ingestion controller/service to capture all form fields
- Concatenate additional fields into the basic_order_notes field
- Ensure the Basic Order Notes section in the order view displays this content

## Testing
Run the test suite to ensure nothing is broken:
```bash
php artisan pest
```

## Verification
1. Submit a lead with various phone formats and verify normalization
2. Create a lead without email and verify it's saved
3. Log in as sales staff and verify editing capabilities
4. Check that marketing source is set correctly for known platforms
5. Verify new orders don't appear in homepage statistics
6. Change an order status and verify notification is sent
7. Have a sales manager add a statement and verify notifications
8. Submit a lead with extra fields and verify they appear in Basic Order Notes

## Dependencies
- Existing UnitOrder model
- Existing notification system
- Existing Livewire components for order management
- Existing authentication and authorization system

## Notes
- All user-facing strings should use Laravel translation keys
- Follow Laravel PSR-12 coding standards
- Maintain backward compatibility with existing functionality