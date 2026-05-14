## Medical Terminologies

A Laravel-based service that stores and exposes common medical terminologies through a unified search API. Terminologies
are managed via a Filament admin panel and indexed in Elasticsearch (via Laravel Scout) for fast full-text search.

### Supported Terminologies

| Terminology   | Domain                              | Localized |
|---------------|-------------------------------------|-----------|
| LOINC codes   | Lab & clinical observations         | Yes       |
| LOINC answers | Answer lists for LOINC observations | Yes       |
| ICD-10 codes  | Diseases & diagnoses                | Yes       |
| Orpha codes   | Rare diseases                       | Yes       |
| ATC codes     | Medications                         | Yes       |

Localized terminologies are translatable into the locales configured in `config/app.php` under `supported_locales` (
currently `en`, `de`, `de_SI`, `tr`). For LOINC codes and LOINC answers, the code itself stays untranslated while the
human-readable label fields (e.g. LOINC long common name, LOINC answer display text) are stored per locale.

### Search API

All search endpoints are exposed under the `/api/terminologies` prefix and return paginated Eloquent API resources.

| Method | Endpoint                                  | Controller                    |
|--------|-------------------------------------------|-------------------------------|
| GET    | `/api/terminologies/loincs/search`        | `SearchLoincController`       |
| GET    | `/api/terminologies/loinc-answers/search` | `SearchLoincAnswerController` |
| GET    | `/api/terminologies/icd10s/search`        | `SearchIcd10Controller`       |
| GET    | `/api/terminologies/orphas/search`        | `SearchOrphaController`       |
| GET    | `/api/terminologies/atcs/search`          | `SearchAtcController`         |

#### Query parameters

| Parameter  | Type    | Required | Description                                                                                                                                                                               |
|------------|---------|----------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `search`   | string  | yes      | Search term (1–255 characters).                                                                                                                                                           |
| `per_page` | integer | no       | Page size, 1–100. Defaults to `25`.                                                                                                                                                       |
| `locale`   | string  | no       | One of the configured `supported_locales`. Applies to the localized terminologies (ICD-10, Orpha, ATC) and selects which translation is matched and returned. Defaults to the app locale. |

#### Example

```http
GET /api/terminologies/icd10s/search?search=diabetes&locale=de&per_page=10
```

### Admin Panel

The Filament admin panel provides resources for browsing each terminology, along with overview widgets summarizing the
dataset.

#### Creating the Initial Admin

```bash
docker compose -f compose.prod.yaml exec mt-app php artisan mt:create-initial-admin
```

This creates a user with email `admin@medical-terminologies` and password `password`. The command is idempotent —
running it again when the admin already exists is a no-op. Change the password immediately after first sign-in.

### Importing Terminologies

The application does not ship with any terminology data. Each operator hosting the app is responsible for sourcing the
upstream files (LOINC, LOINC answers, ICD-10, Orpha, ATC) under the licensing terms of the respective publishers and
providing them as CSVs to the importers.

Each terminology can then be bulk-loaded into the database by uploading a CSV file through its Filament resource page.
Imports run via Filament's importer pipeline, which validates rows, persists them in chunks, and updates the
Scout/Elasticsearch index so the data becomes searchable through the API immediately after import completes.

#### Deprecation Imports

In addition to the standard importer, every terminology (LOINC codes, LOINC answers, ICD-10, Orpha, ATC) exposes a
separate deprecation importer on its Filament resource. Uploading a CSV of identifiers through this importer
soft-deletes the matching records, which removes them from the Scout/Elasticsearch index and the search API while
keeping the rows in the database for historical lookups.

### Running with Docker

#### Development

The repository ships with a `Dockerfile` and `compose.yaml` that spin up the full stack — the Laravel app, a PostgreSQL
database, Elasticsearch, and Kibana (for inspecting indices).

```bash
docker compose up -d
```

The app is then available on `http://localhost:${APP_PORT:-80}`, Elasticsearch on `http://localhost:9200`, and Kibana on
`http://localhost:5601`. Database credentials, ports, and database name can be overridden via the standard `DB_*` and
`APP_PORT` environment variables.

#### Production

A dedicated `Dockerfile.prod` and `compose.prod.yaml` are used for production-style runs. The prod image bakes
`composer install --no-dev --optimize-autoloader` into the build (no source bind mount), Elasticsearch runs with
`xpack.security` enabled, and a separate `worker` container handles queued jobs via `php artisan queue:work`.

Populate `.env` (see `.env.example`) with at least:

- `APP_KEY` — generate with `php artisan key:generate --show`
- `APP_URL`, `APP_ENV=production`, `APP_DEBUG=false`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `ELASTIC_PASSWORD` — used inside the elasticsearch container and also embedded as basic auth into the app's
  `ELASTIC_HOST`. URL-encode `@ : / # ?` if present.

Optional: `APP_PORT` (default `80`), `DB_PORT` (default `5432`), `ES_HEAP_SIZE` (default `1g`).

Build and start the stack:

```bash
docker compose -f compose.prod.yaml up -d --build
docker compose -f compose.prod.yaml logs -f mt-app mt-worker
```

Services:

| Service            | Description                                                                                                                                                                   |
|--------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `mt-app`           | nginx + php-fpm serving the Laravel app on `${APP_PORT}`                                                                                                                      |
| `mt-worker`        | `php artisan queue:work` processing queued jobs, recycled hourly                                                                                                              |
| `mt-db`            | PostgreSQL 17, reachable only from the compose network on `${DB_PORT}`; postgres listens on `${DB_PORT}` internally so multiple stacks can coexist on the same docker network |
| `mt-elasticsearch` | Elasticsearch 9 with `xpack.security` enabled, reachable only from the compose network on `9200`                                                                              |

Only the `mt-app` port is published to the host. To poke at the db or ES from outside the network (e.g. for ad-hoc
inspection), exec into a container or temporarily add a `ports` block.

To scale the worker (`docker compose -f compose.prod.yaml up -d --scale mt-worker=N`), remove the
`container_name: mt-worker` line first, since fixed names cannot be reused across replicas.

### Tech Stack

- PHP 8.4, Laravel 13
- Filament 5 (admin panel)
- Laravel Scout 11 + Elasticsearch (via `babenkoivan/elastic-scout-driver`) for search indexing
- `spatie/laravel-translatable` for localized fields
