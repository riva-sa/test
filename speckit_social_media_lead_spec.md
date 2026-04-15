## Speckit Specification: Social Media Lead Integration

This document defines the specification for integrating social media leads (from platforms like Snapchat, TikTok, etc.) into the Riva CRM system via Zapier webhooks. The goal is to automatically create `UnitOrder` records and send notifications to relevant users upon receiving new lead data.

### 1. **Feature Description**

**Name**: Social Media Lead Ingestion
**Goal**: To capture lead information from various social media platforms (e.g., TikTok, Snapchat, Facebook, Instagram, Google, Twitter, LinkedIn, WhatsApp) via Zapier webhooks and store it as `UnitOrder` records in the CRM, triggering notifications for sales and admin teams.

### 2. **API Endpoint Specification**

**Endpoint**: `/api/zapier/social-media-lead`
**Method**: `POST`
**Description**: Receives lead data from Zapier.

**Request Body (JSON)**:

| Field            | Type     | Required | Description                                     |
| :--------------- | :------- | :------- | :---------------------------------------------- |
| `name`           | `string` | Yes      | Full name of the lead                           |
| `email`          | `string` | Yes      | Email address of the lead                       |
| `phone`          | `string` | Yes      | Phone number of the lead                        |
| `message`        | `string` | No       | Optional message from the lead                  |
| `campaign_name`  | `string` | Yes      | Campaign name of the lead                       |
| `ad_squad`       | `string` | Yes      | Ad squad name of the lead                       |
| `ad_set`         | `string` | Yes      | Ad set name of the lead                         |
| `ad_name`        | `string` | Yes      | Ad name of the lead                             |
| `marketing_source` | `string` | Yes      | Source platform (e.g., `TikTok`, `Snapchat`)    |

**Validation Rules (Laravel)**:

*   `name`: `required|string|max:255`
*   `email`: `required|email|max:255`
*   `phone`: `required|string|max:20`
*   `message`: `nullable|string`
*   `marketing_source`: `required|string|in:TikTok,Snapchat,Facebook,Instagram,Google,Twitter,LinkedIn,WhatsApp`
*   `campaign_name`: `required|string|max:255`
*   `ad_squad`: `required|string|max:255`
*   `ad_set`: `required|string|max:255`
*   `ad_name`: `required|string|max:255`

**Response (JSON)**:

*   **Success (201 Created)**:
    ```json
    {
        "message": "Lead created successfully."
    }
    ```
*   **Validation Error (422 Unprocessable Entity)**:
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "field_name": [
                "Error message for field."
            ]
        }
    }
    ```

### 3. **Business Logic / Actions**

Upon successful receipt and validation of lead data:

1.  **Create `UnitOrder` Record**:
    *   A new `UnitOrder` entry will be created in the database.
    *   Map incoming fields to `UnitOrder` attributes:
        *   `name` -> `name`
        *   `email` -> `email`
        *   `phone` -> `phone`
        *   `message` -> `message`
        *   `marketing_source` -> `marketing_source`
        *   `order_source` -> `social_media` (new constant)
        *   `status` -> `0` (representing 'New' lead status)
        *   `campaign_name` -> `campaign_name`
        *   `ad_squad` -> `ad_squad`
        *   `ad_set` -> `ad_set`
        *   `ad_name` -> `ad_name`

2.  **Send Notification**:
    *   A `NewSocialMediaLead` notification will be dispatched.
    *   **Recipients**: Users with roles `admin` or `sales_manager` (configurable).
    *   **Channels**: `mail` and `database`.
    *   **Content (Email)**:
        *   Subject: "New Social Media Lead Received!"
        *   Greeting: "Hello!"
        *   Body: "A new lead has been generated from a social media campaign."
        *   Details: Lead Name, Email, Phone, Marketing Source.
        *   Action Button: "View Lead" linking to `/admin/unit-orders/{unitOrderId}`.
    *   **Content (Database)**:
        *   `unit_order_id`, `name`, `email`, `phone`, `marketing_source`, `campaign_name`, `ad_squad`, `ad_set`, `ad_name`.
        *   `message`: "New lead from {marketing_source}: {name}"
        *   `link`: `/admin/unit-orders/{unitOrderId}`.

### 4. **Database Changes (Migration)**

Ensure the `unit_orders` table has the following columns:

*   `marketing_source`: `string`, `nullable` (already present based on provided migration)
*   `order_source`: `string`, `default('legacy')`, `index` (already present, will need new constant)
*   `campaign_name`: `string`, `nullable`
*   `ad_squad`: `string`, `nullable`
*   `ad_set`: `string`, `nullable`
*   `ad_name`: `string`, `nullable`

### 5. **Model Updates (`UnitOrder.php`)**

*   Add `social_media` to `ORDER_SOURCE` constants.
*   Ensure `marketing_source`, `order_source`, `campaign_name`, `ad_squad`, `ad_set`, `ad_name` are in the `$fillable` array.

### 6. **Livewire Component Updates**

*   Display `marketing_source`, `campaign_name`, `ad_squad`, `ad_set`, `ad_name` in the `Manage Orders` Livewire component.
*   Integrate database notifications for new leads into the Livewire component for real-time updates.

### 7. **Zapier Configuration**

*   Set up a Zapier "Webhook" action to `POST` data to the `/api/zapier/social-media-lead` endpoint.
*   Map social media lead fields (name, email, phone, message, source) to the corresponding fields in the webhook request body.