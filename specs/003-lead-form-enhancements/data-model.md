# Data Model: Lead Form Enhancements

## Entities

### UnitOrder (Extended)
The core CRM entity representing a lead or order. Already exists in the system but will be enhanced with additional fields and behavior.

**Fields**:
- `id`: Primary key (auto-incrementing integer)
- `name`: Customer name (string, required)
- `email`: Customer email address (string, nullable, optional)
- `phone`: Customer phone number (string, formatted to +966XXXXXXXXXX)
- `marketing_source`: Source of the lead (string, values: Snapchat, Instagram, TikTok, etc.)
- `campaign_name`: Name of the marketing campaign (string)
- `ad_squad`: Ad Squad identifier (string)
- `ad_set`: Ad Set identifier (string)
- `ad_name`: Ad Name identifier (string)
- `order_source`: Source of the order (string, will be set to 'social_media' for leads from social media)
- `basic_order_notes`: Additional information from lead forms (text)
- `customer_information`: JSON or serialized customer details (text/json)
- `unit_information`: JSON or serialized unit details (text/json)
- `status`: Current order status (string, e.g., new, pending, completed)
- `created_at`: Timestamp
- `updated_at`: Timestamp

**Relationships**:
- Belongs to Project (if applicable)
- Has many Notifications
- Has many Order Status Changes
- Belongs to User (assigned sales representative)

**Validation Rules**:
- Phone numbers must be formatted to start with +966 after removing spaces/symbols
- Phone numbers that cannot be formatted should be flagged for manual review
- Email field is optional (nullable)
- Marketing source should be set according to platform (Snapchat, Instagram, TikTok, etc.)
- Unrecognized marketing sources should be stored as-is
- Basic order notes should capture all additional form fields

### Notification
A system event that alerts users to new activity such as order status changes or manager statements.

**Fields**:
- `id`: Primary key (auto-incrementing integer)
- `unit_order_id`: Foreign key to UnitOrder
- `user_id`: Foreign key to User (recipient)
- `type`: Notification type (string, e.g., 'order_status_change', 'manager_statement')
- `title`: Notification title (string)
- `body`: Notification body (text)
- `is_read`: Boolean flag
- `created_at`: Timestamp
- `updated_at`: Timestamp

**Relationships**:
- Belongs to UnitOrder
- Belongs to User

### User Role
Determines which team members can edit order information and receive notifications.
(This likely references Laravel's existing Role/Permission system)

**Fields** (if custom implementation needed):
- `id`: Primary key
- `name`: Role name (string, e.g., 'sales_staff', 'sales_manager', 'admin')
- `permissions`: Associated permissions (JSON or relationship)

**Relationships**:
- BelongsToMany Users
- BelongsToMany Permissions

### Order Status
Tracks the progress of an order through various stages.

**Fields**:
- `id`: Primary key
- `name`: Status name (string, e.g., 'new', 'pending', 'contacted', 'qualified', 'closed_won', 'closed_lost')
- `is_active`: Boolean flag
- `sort_order`: Integer for ordering
- `created_at`: Timestamp
- `updated_at`: Timestamp

**Relationships**:
- HasMany UnitOrder (through status changes)
- HasMany Notification (status change notifications)

## Database Considerations

**Indexes**:
- UnitOrder: phone (for lookup), marketing_source (for reporting), created_at (for sorting)
- Notification: unit_order_id, user_id, is_read, created_at
- User Role: name (unique)
- Order Status: name (unique), is_active

**Constraints**:
- UnitOrder.phone: Should follow +966 format after processing
- UnitOrder.email: Nullable (optional)
- Notification.user_id: Foreign key to users table
- Notification.unit_order_id: Foreign key to unit_orders table

## State Transitions (Order Status Flow)

1. **new** → Initial state when lead is created
2. **pending** → After initial contact or qualification attempt
3. **contacted** → After first successful contact
4. **qualified** → When lead meets criteria for sales pursuit
5. **closed_won** → When sale is successfully completed
6. **closed_lost** → When lead does not result in sale

Additional custom statuses may exist based on business processes.

## Data Flow for Lead Enhancements

1. **Lead Ingestion**:
   - Form data received via webhook/API
   - Phone number normalized (+966 format, spaces/symbols removed)
   - If phone cannot be normalized: flag for review but continue processing
   - Email stored as-is (nullable/optional)
   - Marketing source auto-detected from platform or stored as-is if unrecognized
   - Additional form fields saved to basic_order_notes
   - UnitOrder record created

2. **Order Editing by Sales Staff**:
   - Sales staff can edit customer/unit information via Livewire/Filament interface
   - Unauthorized edit attempts are logged but still permitted (per clarification)
   - Changes saved to UnitOrder record

3. **Status Changes**:
   - When order status changes: Notification dispatched to assigned employee
   - Structured JSON logging of the event

4. **Manager Statements**:
   - When sales manager adds statement: Notification dispatched to assigned employees
   - Structured JSON logging of the event

5. **Display**:
   - Basic order notes visible in order management interface
   - Marketing attribution fields displayed appropriately
   - Phone numbers displayed in standardized +966 format