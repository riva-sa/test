# Research: Social Media Lead Ingestion

## Decisions

- **Decision 1: Use an Action class (`IngestSocialMediaLead`)**
    - **Rationale**: Keeps the controller thin and allows lead ingestion to be triggered from other sources (e.g., CLI for testing or bulk retry).
    - **Alternatives considered**: Putting logic in the controller (rejected per Constitution Principle II).

- **Decision 2: Extend `UnitOrder` model instead of a new table**
    - **Rationale**: Leads are logically `UnitOrders` in this system. Adding 4 fields is low impact compared to maintaining a join table.
    - **Alternatives considered**: Separate `SocialLeads` table (rejected due to complexity in joining with existing order management UI).

- **Decision 3: Use existing `UnitOrderNotification` as a base**
    - **Rationale**: Leverages established email templates and styling.
    - **Alternatives considered**: Custom Markdown email (rejected for consistency).

## Unknowns Resolved

- **Field length**: `campaign_name`, `ad_squad`, etc. will be `string(255)` and `nullable`.
- **Recipient Roles**: Will target `admin` and `sales_manager` roles as per specimen requirements.

## Next Steps
- Verify if `spatie-laravel-settings` can be used to manage notification recipients centrally instead of hardcoding roles.
