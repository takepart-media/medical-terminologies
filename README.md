## Medical Terminologies

A Laravel-based service that stores and exposes common medical terminologies through a unified search API. Terminologies are managed via a Filament admin panel and indexed in Elasticsearch (via Laravel Scout) for fast full-text search.

### Supported Terminologies

| Terminology      | Domain                | Localized |
|------------------|-----------------------|-----------|
| LOINC codes      | Lab & clinical observations | No |
| LOINC answers    | Answer lists for LOINC observations | No |
| ICD-10 codes     | Diseases & diagnoses  | Yes |
| Orpha codes      | Rare diseases         | Yes |
| ATC codes        | Medications           | Yes |

Localized terminologies are translatable into the locales configured in `config/app.php` under `supported_locales` (currently `en`, `de`, `de_SI`, `tr`).

### Search API

All search endpoints are exposed under the `/api/terminologies` prefix and return paginated Eloquent API resources.

| Method | Endpoint                                | Controller |
|--------|-----------------------------------------|------------|
| GET    | `/api/terminologies/loincs/search`        | `SearchLoincController` |
| GET    | `/api/terminologies/loinc-answers/search` | `SearchLoincAnswerController` |
| GET    | `/api/terminologies/icd10s/search`        | `SearchIcd10Controller` |
| GET    | `/api/terminologies/orphas/search`        | `SearchOrphaController` |
| GET    | `/api/terminologies/atcs/search`          | `SearchAtcController` |

#### Query parameters

| Parameter  | Type    | Required | Description |
|------------|---------|----------|-------------|
| `search`   | string  | yes      | Search term (1–255 characters). |
| `per_page` | integer | no       | Page size, 1–100. Defaults to `25`. |
| `locale`   | string  | no       | One of the configured `supported_locales`. Applies to the localized terminologies (ICD-10, Orpha, ATC) and selects which translation is matched and returned. Defaults to the app locale. |

#### Example

```http
GET /api/terminologies/icd10s/search?search=diabetes&locale=de&per_page=10
```

### Admin Panel

The Filament admin panel provides resources for browsing each terminology, along with overview widgets summarizing the dataset.

### Importing Terminologies

Each terminology can be bulk-loaded into the database by uploading a CSV file through its Filament resource page. Imports run via Filament's importer pipeline, which validates rows, persists them in chunks, and updates the Scout/Elasticsearch index so the data becomes searchable through the API immediately after import completes.

### Running with Docker

The repository ships with a `Dockerfile` and `compose.yaml` that spin up the full stack — the Laravel app, a PostgreSQL database, Elasticsearch, and Kibana (for inspecting indices).

```bash
docker compose up -d
```

The app is then available on `http://localhost:${APP_PORT:-80}`, Elasticsearch on `http://localhost:9200`, and Kibana on `http://localhost:5601`. Database credentials, ports, and database name can be overridden via the standard `DB_*` and `APP_PORT` environment variables.

### Tech Stack

- PHP 8.4, Laravel 13
- Filament 5 (admin panel)
- Laravel Scout 11 + Elasticsearch (via `babenkoivan/elastic-scout-driver`) for search indexing
- `spatie/laravel-translatable` for localized fields
