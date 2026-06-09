# Data Model: Public Website Localization

No database changes in this phase. All localization data is managed via files and runtime state.

## Conceptual Entities

### Locale
| Attribute | Type | Description |
|-----------|------|-------------|
| code | `string` | ISO 639-1 code: `ar` or `en` |
| direction | `string` | Text direction: `rtl` (Arabic) or `ltr` (English) |
| url_prefix | `string` | URL prefix: `/en`, `/ar`, or empty string for default Arabic |

**Uniqueness**: Two fixed values (`ar`, `en`) — no dynamic creation.

### Translation File
| Attribute | Type | Description |
|-----------|------|-------------|
| locale | `string` | Belongs to locale (`ar` or `en`) |
| namespace | `string` | Grouping key (e.g., `public`) |
| keys | `array<string, string>` | Key-value pairs of translatable strings |

**Location**: `lang/{locale}/public.php`

### Localized URL
| Attribute | Type | Description |
|-----------|------|-------------|
| path | `string` | Route path without prefix |
| locale | `string` | Target locale |
| full_url | `string` | Absolute URL with locale prefix and canonical domain |

**Rules**:
- Arabic: `https://riva.com/page` or `https://riva.com/ar/page`
- English: `https://riva.com/en/page`
- Canonical URL uses the same path without language prefix (`https://riva.com/page`)

## State Machine: Locale Resolution

```
Incoming Request
       │
       ▼
  URL has prefix? ──yes──▶ Validate locale ──valid──▶ Set locale from URL
       │                          │
       no                      invalid
       │                          │
       ▼                          ▼
  Cookie has locale? ──yes──▶ Set locale from cookie   Redirect to /
       │
       no
       │
       ▼
  Session has locale? ──yes──▶ Set locale from session
       │
       no
       │
       ▼
  Default to 'ar'
```

## Translation Key Naming Convention

```
{section}.{element}
```

Sections: `nav`, `home`, `projects`, `project`, `about`, `contact`, `footer`, `blog`, `search`, `common`, `status`, `unit`, `filter`

Examples:
- `nav.home` → Home
- `status.available` → Available / متاح
- `filter.price` → Price Range / نطاق السعر
- `contact.submit` → Send Message / إرسال
