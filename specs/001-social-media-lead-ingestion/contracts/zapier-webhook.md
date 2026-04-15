# API Contract: Zapier Social Media Lead Webhook

## Endpoint
`POST /api/zapier/social-media-lead`

## Request Headers
- `Content-Type: application/json`
- `Accept: application/json`

## Request Body (JSON)

```json
{
  "name": "string",
  "email": "string",
  "phone": "string",
  "message": "string (optional)",
  "campaign_name": "string",
  "ad_squad": "string",
  "ad_set": "string",
  "ad_name": "string",
  "marketing_source": "string (Enum: TikTok, Snapchat, Facebook, Instagram, Google, Twitter, LinkedIn, WhatsApp)"
}
```

## Responses

### 201 Created
```json
{
  "message": "Lead created successfully."
}
```

### 422 Unprocessable Entity
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "marketing_source": ["The selected marketing source is invalid."]
  }
}
```

## Implementation Details
- Handled by `ZapierLeadController@store`.
- Validated via specific `SocialMediaLeadRequest`.
- Logic handled by `IngestSocialMediaLead` action.
