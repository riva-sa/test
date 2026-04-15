# Data Model: UnitOrder Extensions

## Entity: UnitOrder

The `UnitOrder` model represents a lead or order in the Riva CRM. It is stored in the `unit_orders` table.

### New Attributes

| Attribute | Type | Nullable | Description |
|-----------|------|----------|-------------|
| `campaign_name` | `string(255)` | Yes | Name of the social media campaign |
| `ad_squad` | `string(255)` | Yes | Ad squad identifier |
| `ad_set` | `string(255)` | Yes | Ad set identifier |
| `ad_name` | `string(255)` | Yes | Specific ad name |

### Constant Extensions

- `ORDER_SOURCE_SOCIAL_MEDIA` = `'social_media'`

### Relationships

- `project()` -> `BelongsTo` (Project) - Optional for social leads
- `user()` -> `BelongsTo` (User) - The creator of the lead (system/api)
- `assignedSalesUser()` -> `BelongsTo` (User) - Sales rep assigned to the lead

## State Transitions

- **Initial State**: `status = 0` (New)
- **Notification Trigger**: Creation of `UnitOrder` with `order_source = 'social_media'` triggers `NewSocialMediaLead` notification.
