# Widget REST API Documentation

A complete REST API for managing Widgets built with Laravel 12, demonstrating API design, service layer pattern, dependency injection, request validation, and comprehensive testing.

## Features

- Full CRUD operations (Create, Read, Update, Delete)
- Advanced filtering (status, price range, quantity range, search)
- Sorting and pagination
- Soft deletes
- JSON metadata support
- Request validation with clear error messages
- Consistent JSON API responses
- **Background job processing** with queue system
- **Automatic widget processing** on create/update
- **Delayed follow-up emails** (24 hours after creation)
- **Daily statistics reports** (scheduled)
- **Batch processing** capabilities
- Comprehensive test coverage

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── WidgetController.php          # API controller with CRUD operations
│   ├── Requests/
│   │   └── Api/
│   │       ├── StoreWidgetRequest.php        # Validation for creating widgets
│   │       ├── UpdateWidgetRequest.php       # Validation for full updates (PUT)
│   │       └── PartialUpdateWidgetRequest.php # Validation for partial updates (PATCH)
│   └── Resources/
│       ├── WidgetResource.php                # Single widget JSON transformation
│       └── WidgetCollection.php              # Collection with pagination metadata
├── Jobs/
│   ├── ProcessWidgetJob.php                  # Process individual widget
│   ├── SendWidgetFollowUpEmailJob.php        # Send follow-up email (24h delay)
│   ├── GenerateDailyWidgetReportJob.php       # Generate daily statistics report
│   └── ProcessWidgetBatchJob.php             # Process multiple widgets in batch
├── Mail/
│   ├── WidgetFollowUpEmail.php               # Follow-up email mailable
│   └── WidgetDailyReport.php                 # Daily report email mailable
├── Models/
│   └── Widget.php                            # Widget model with scopes and accessors
├── Services/
│   ├── WidgetService.php                     # Business logic layer
│   └── WidgetJobService.php                  # Job management utilities
└── Console/
    └── Commands/
        └── GenerateDailyWidgetReport.php     # Scheduled command for daily reports

routes/
├── api.php                                   # API routes with v1 prefix
└── console.php                               # Console routes with scheduled tasks

database/
├── factories/
│   └── WidgetFactory.php                     # Test data factory
├── migrations/
│   ├── YYYY_MM_DD_HHMMSS_create_widgets_table.php
│   └── YYYY_MM_DD_HHMMSS_add_processing_fields_to_widgets_table.php
└── seeders/
    └── WidgetSeeder.php                      # Optional seeder for sample data

resources/
└── views/
    └── emails/
        ├── widget-follow-up.blade.php         # Follow-up email template
        └── widget-daily-report.blade.php     # Daily report email template

tests/
├── Feature/
│   ├── Api/
│   │   └── WidgetApiTest.php                 # Comprehensive API endpoint tests
│   └── WidgetJobProcessingTest.php           # Job processing workflow tests
└── Unit/
    ├── Jobs/
    │   ├── ProcessWidgetJobTest.php           # ProcessWidgetJob unit tests
    │   ├── SendWidgetFollowUpEmailJobTest.php # Follow-up email job tests
    │   └── GenerateDailyWidgetReportJobTest.php # Daily report job tests
    ├── Models/
    │   └── WidgetTest.php                    # Model tests (scopes, accessors, casts)
    └── Services/
        └── WidgetServiceTest.php              # Service layer unit tests
```

## API Endpoints

All endpoints are prefixed with `/api/v1`.

### Base URL
```
http://localhost:8000/api/v1
```

### Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/widgets` | List widgets with filtering/pagination |
| GET | `/widgets/{id}` | Show single widget |
| POST | `/widgets` | Create new widget |
| PUT | `/widgets/{id}` | Full update widget |
| PATCH | `/widgets/{id}` | Partial update widget |
| DELETE | `/widgets/{id}` | Soft delete widget |

## Widget Model

### Fields

- `id` (integer) - Primary key
- `name` (string, required, unique) - Widget name
- `description` (text, nullable) - Widget description
- `price` (decimal, nullable) - Widget price
- `quantity` (integer, default: 0) - Available quantity
- `status` (enum: active, inactive, archived, default: active) - Widget status
- `metadata` (json, nullable) - Flexible JSON data (may include processing results, email addresses)
- `processed_at` (timestamp, nullable) - When widget was last processed by ProcessWidgetJob
- `email_sent_at` (timestamp, nullable) - When follow-up email was sent
- `created_at` (timestamp) - Creation timestamp
- `updated_at` (timestamp) - Last update timestamp
- `deleted_at` (timestamp, nullable) - Soft delete timestamp

## Example API Requests

### 1. List All Widgets

```bash
curl -X GET "http://localhost:8000/api/v1/widgets" \
  -H "Accept: application/json"
```

### 2. List Widgets with Pagination

```bash
curl -X GET "http://localhost:8000/api/v1/widgets?per_page=10&page=1" \
  -H "Accept: application/json"
```

### 3. Filter Widgets by Status

```bash
curl -X GET "http://localhost:8000/api/v1/widgets?status=active" \
  -H "Accept: application/json"
```

### 4. Filter Widgets by Price Range

```bash
curl -X GET "http://localhost:8000/api/v1/widgets?min_price=10&max_price=50" \
  -H "Accept: application/json"
```

### 5. Filter Widgets by Quantity Range

```bash
curl -X GET "http://localhost:8000/api/v1/widgets?min_quantity=20&max_quantity=100" \
  -H "Accept: application/json"
```

### 6. Search Widgets

```bash
curl -X GET "http://localhost:8000/api/v1/widgets?search=premium" \
  -H "Accept: application/json"
```

### 7. Sort Widgets

```bash
curl -X GET "http://localhost:8000/api/v1/widgets?sort=price&direction=asc" \
  -H "Accept: application/json"
```

### 8. Combined Filters

```bash
curl -X GET "http://localhost:8000/api/v1/widgets?status=active&min_price=20&max_price=40&min_quantity=40&sort=price&direction=asc&per_page=20" \
  -H "Accept: application/json"
```

### 9. Get Single Widget

```bash
curl -X GET "http://localhost:8000/api/v1/widgets/1" \
  -H "Accept: application/json"
```

### 10. Create Widget

```bash
curl -X POST "http://localhost:8000/api/v1/widgets" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Premium Widget",
    "description": "A high-quality widget with advanced features",
    "price": 29.99,
    "quantity": 50,
    "status": "active",
    "metadata": {
      "color": "blue",
      "size": "large",
      "weight": 2.5
    }
  }'
```

### 11. Create Widget (Minimal)

```bash
curl -X POST "http://localhost:8000/api/v1/widgets" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Basic Widget"
  }'
```

### 12. Full Update Widget (PUT)

```bash
curl -X PUT "http://localhost:8000/api/v1/widgets/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Widget Name",
    "description": "Updated description",
    "price": 39.99,
    "quantity": 75,
    "status": "active"
  }'
```

### 13. Partial Update Widget (PATCH)

```bash
curl -X PATCH "http://localhost:8000/api/v1/widgets/1" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "price": 35.99,
    "quantity": 60
  }'
```

### 14. Delete Widget (Soft Delete)

```bash
curl -X DELETE "http://localhost:8000/api/v1/widgets/1" \
  -H "Accept: application/json"
```

## Response Formats

### Success Response (Single Widget)

```json
{
  "data": {
    "id": 1,
    "name": "Premium Widget",
    "description": "A high-quality widget",
    "price": "29.99",
    "quantity": 50,
    "status": "active",
    "status_label": "Active",
    "metadata": {
      "color": "blue",
      "size": "large"
    },
    "created_at": "2025-01-15T10:30:00.000000Z",
    "updated_at": "2025-01-15T10:30:00.000000Z"
  }
}
```

### Success Response (Collection with Pagination)

```json
{
  "data": [
    {
      "id": 1,
      "name": "Widget 1",
      "description": "Description 1",
      "price": "10.00",
      "quantity": 25,
      "status": "active",
      "status_label": "Active",
      "metadata": null,
      "created_at": "2025-01-15T10:30:00.000000Z",
      "updated_at": "2025-01-15T10:30:00.000000Z"
    },
    {
      "id": 2,
      "name": "Widget 2",
      "description": "Description 2",
      "price": "20.00",
      "quantity": 50,
      "status": "active",
      "status_label": "Active",
      "metadata": null,
      "created_at": "2025-01-15T10:31:00.000000Z",
      "updated_at": "2025-01-15T10:31:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "http://localhost:8000/api/v1/widgets?page=1",
    "last": "http://localhost:8000/api/v1/widgets?page=7",
    "prev": null,
    "next": "http://localhost:8000/api/v1/widgets?page=2"
  }
}
```

### Error Response (404 Not Found)

```json
{
  "error": {
    "message": "Resource not found",
    "code": "NOT_FOUND",
    "status": 404
  }
}
```

### Error Response (422 Validation Error)

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "The name field is required."
    ],
    "price": [
      "The price must be a number."
    ]
  }
}
```

## Query Parameters

### List Endpoint Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| `search` | string | Search in name/description | `?search=premium` |
| `status` | enum | Filter by status | `?status=active` |
| `min_price` | float | Minimum price filter | `?min_price=10` |
| `max_price` | float | Maximum price filter | `?max_price=50` |
| `min_quantity` | integer | Minimum quantity filter | `?min_quantity=20` |
| `max_quantity` | integer | Maximum quantity filter | `?max_quantity=100` |
| `sort` | string | Sort field (name, price, quantity, created_at) | `?sort=price` |
| `direction` | string | Sort direction (asc, desc) | `?direction=asc` |
| `per_page` | integer | Items per page (default: 15) | `?per_page=20` |
| `page` | integer | Page number (default: 1) | `?page=2` |

## Validation Rules

### Create (POST)

- `name`: required, string, max:255, unique
- `description`: nullable, string, max:1000
- `price`: nullable, numeric, min:0, max:999999.99
- `quantity`: nullable, integer, min:0
- `status`: nullable, enum (active, inactive, archived)
- `metadata`: nullable, array

### Update (PUT)

- Same as Create, but all fields are optional (use `sometimes`)
- `name` unique rule ignores current widget

### Partial Update (PATCH)

- All fields optional
- Each field validated only if present
- `name` unique rule ignores current widget

## Background Job Processing

The Widget API includes comprehensive background job processing capabilities:

### Automatic Job Dispatching

Jobs are automatically dispatched when widgets are created or updated:

- **Creating a Widget** (POST `/api/v1/widgets`):
  - `ProcessWidgetJob` - Processes widget immediately (calculates statistics, updates metadata)
  - `SendWidgetFollowUpEmailJob` - Sends follow-up email after 24 hours (if email in metadata)

- **Updating a Widget** (PUT/PATCH `/api/v1/widgets/{id}`):
  - `ProcessWidgetJob` - Recalculates statistics after update

### Available Jobs

#### ProcessWidgetJob
- **Purpose**: Process individual widget, calculate statistics, update metadata
- **Queue**: `default`
- **Tries**: 3 attempts
- **Timeout**: 60 seconds
- **Actions**:
  - Calculates total value (price × quantity)
  - Determines if widget is high value (> $1000)
  - Updates `processed_at` timestamp
  - Stores processing data in metadata

#### SendWidgetFollowUpEmailJob
- **Purpose**: Send follow-up email 24 hours after widget creation
- **Queue**: `default`
- **Delay**: 24 hours
- **Tries**: 3 attempts
- **Timeout**: 30 seconds
- **Actions**:
  - Sends email if `email` or `contact_email` in metadata
  - Updates `email_sent_at` timestamp
  - Skips if email already sent

#### GenerateDailyWidgetReportJob
- **Purpose**: Generate and email daily statistics report
- **Queue**: `default`
- **Tries**: 2 attempts
- **Timeout**: 120 seconds
- **Schedule**: Daily at 9:00 AM (via scheduler)
- **Actions**:
  - Calculates daily statistics (created, updated, deleted, processed, emails sent)
  - Generates totals (active, inactive, archived)
  - Emails report to admin address

#### ProcessWidgetBatchJob
- **Purpose**: Process multiple widgets in batches
- **Queue**: `default`
- **Tries**: 2 attempts
- **Timeout**: 300 seconds
- **Actions**:
  - Processes up to 50 unprocessed widgets (or specified IDs)
  - Dispatches individual `ProcessWidgetJob` for each widget

### Queue Configuration

Jobs require a queue worker to execute. Configure in `.env`:

```env
# For immediate execution (development)
QUEUE_CONNECTION=sync

# For background processing (production)
QUEUE_CONNECTION=database
```

Then run the queue worker:
```bash
php artisan queue:work
```

### Scheduled Tasks

The daily report is automatically scheduled to run at 9:00 AM:

```bash
# Manual execution
php artisan widgets:generate-daily-report
```

### Job Testing

All jobs are fully tested. Run job tests:

```bash
# Run all job tests
php artisan test --filter="Job"

# Run specific job tests
php artisan test tests/Unit/Jobs/ProcessWidgetJobTest.php
php artisan test tests/Unit/Jobs/SendWidgetFollowUpEmailJobTest.php
php artisan test tests/Unit/Jobs/GenerateDailyWidgetReportJobTest.php
php artisan test tests/Feature/WidgetJobProcessingTest.php
```

## Testing

Run the test suite:

```bash
# Run all Widget tests
php artisan test --filter=Widget

# Run specific test suites
php artisan test tests/Feature/Api/WidgetApiTest.php
php artisan test tests/Unit/Services/WidgetServiceTest.php
php artisan test tests/Unit/Models/WidgetTest.php
php artisan test tests/Feature/WidgetJobProcessingTest.php
php artisan test tests/Unit/Jobs/
```

## Rate Limiting

API endpoints are rate-limited to 60 requests per minute per IP address.

## Database Migration

Run the migration to create the widgets table:

```bash
php artisan migrate
```

## Seeding (Optional)

Create sample widgets for testing:

```bash
php artisan db:seed --class=WidgetSeeder
```

Or use the factory in tinker:

```bash
php artisan tinker
>>> App\Models\Widget::factory()->count(10)->create();
```

## Architecture Patterns Demonstrated

1. **Service Layer Pattern**: Business logic separated from controllers
2. **Dependency Injection**: Services injected into controllers
3. **Form Request Validation**: Validation rules in dedicated request classes
4. **API Resources**: Consistent JSON transformation
5. **Soft Deletes**: Data retention with soft deletion
6. **Query Scopes**: Reusable query filters in the model
7. **Exception Handling**: Custom API error responses
8. **Route Model Binding**: Automatic model resolution
9. **Background Job Processing**: Async task execution with queue system
10. **Job Chaining**: Sequential job execution (create → process → email)
11. **Delayed Job Execution**: Scheduled jobs with time delays
12. **Scheduled Tasks**: Cron-like scheduled command execution
13. **Retry Logic**: Exponential backoff for failed jobs
14. **Batch Processing**: Efficient processing of multiple records

## Notes

- All deletions are soft deletes (records are marked as deleted, not removed)
- Deleted widgets are excluded from list endpoints
- The API uses JSON for all requests and responses
- Timestamps are returned in ISO 8601 format
- Price values are returned as strings to preserve decimal precision

