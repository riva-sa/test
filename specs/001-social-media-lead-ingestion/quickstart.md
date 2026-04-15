# Quickstart: Testing Social Media Lead Ingestion

## Manual Testing (CURL)

To simulate a lead from Zapier, run the following command from your terminal:

```bash
curl -X POST http://localhost/api/zapier/social-media-lead \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{
       "name": "Jane Doe",
       "email": "jane@example.com",
       "phone": "+966500000000",
       "marketing_source": "TikTok",
       "campaign_name": "Summer_2026_Launch",
       "ad_squad": "Video_Ads",
       "ad_set": "Saudi_Riyadh",
       "ad_name": "Modern_Villa_Tour"
     }'
```

## Verification Steps

1. **Database Check**: Run `php artisan tinker` and check if the order exists:
   ```php
   App\Models\UnitOrder::where('email', 'jane@example.com')->first();
   ```
2. **Notification Check**: Check the `notifications` table or logs for the `NewSocialMediaLead` event.
3. **UI Check**: Log in to the Riva CRM dashboard and navigate to "Manage Orders". Ensure the new lead is visible and correctly attributed to TikTok.

## Automated Testing

Run the feature tests to ensure logic integrity:
```bash
./vendor/bin/pest tests/Feature/Api/ZapierLeadControllerTest.php
```
