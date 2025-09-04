# Laravel Data Import/Export Assessment

A Laravel-based application for importing and exporting data in multiple formats (CSV, JSON, XML). Includes abstract classes for importers/exporters, concrete implementations, Eloquent model traits, background jobs, and a controller with API routes for export/import operations. Uses the [`App\Models\User`](app/Models/User.php) model for demonstration.

---

## Features

- Export all users to CSV, JSON, or XML in the background
- Import users from CSV or JSON files in the background
- Batch export (bonus feature) for multiple formats

---

## Requirements

- PHP 8.1+
- [Composer](https://getcomposer.org/)
- SQLite (no server required)
- Laravel 11.x (or compatible)
- Optional: [Postman](https://www.postman.com/) or `curl` for API testing
- Optional: [DB Browser for SQLite](https://sqlitebrowser.org/) for viewing database data

---

## Getting Started

### 1. Clone the Repository

```sh
git clone https://github.com/your-username/svytel-communication-assessment.git
cd svytel-communication-assessment
```

### 2. Install Dependencies

```sh
composer install
```

### 3. Environment Setup

Copy the example environment file:

```sh
cp .env.example .env
```

Generate the application key:

```sh
php artisan key:generate
```

### 4. Database Setup

Create the SQLite database file and directory:

```sh
mkdir -p database/sqlite
touch database/sqlite/database.sqlite
```

Update `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/sqlite/database.sqlite
```
> **Tip:** Use an absolute path for `DB_DATABASE` (right-click the file in Explorer and "Copy as path").

Remove any unnecessary DB settings like `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD`.

### 5. Run Migrations

```sh
php artisan migrate
```

Create the queue table for background jobs:

```sh
php artisan queue:table
php artisan migrate
```

(Optional) Seed test data:

```sh
php artisan db:seed --class=UserSeeder
```

### 6. Create Exports Directory

```sh
mkdir -p storage/app/exports
```

---

## Running the Application

Start the Laravel development server:

```sh
php artisan serve
```
Visit: [http://127.0.0.1:8000](http://127.0.0.1:8000)

Start the queue worker (for background jobs):

```sh
php artisan queue:work
```
> Keep this running while testing import/export features.

---

## API Usage

### Export Users

**Endpoint:** `POST /data/export`

- Dispatches a background job to export all users to the specified format.

#### Example (CSV, no CSRF):

```sh
curl -X POST http://127.0.0.1:8000/data/export \
  -H "Content-Type: application/json" \
  -d '{"format":"csv"}'
```

#### Example (CSV, with CSRF):

Get token:

```sh
curl http://127.0.0.1:8000/csrf-token
```

Run export:

```sh
curl -X POST http://127.0.0.1:8000/data/export \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-csrf-token-here" \
  -d '{"format":"csv"}'
```

**Response:**
```json
{"message": "Export job dispatched"}
```

**Exported files:** [`storage/app/exports/`](storage/app/exports)

---

### Import Users

**Endpoint:** `POST /data/import`

- Dispatches a background job to import users from an uploaded CSV or JSON file.

#### Example CSV (`users.csv`):

```csv
name,email,password
John Doe,john@example.com,secret123
Jane Smith,jane@example.com,secret456
```
you can also file a sample_import.csv in project dir
#### Example JSON (`users.json`):

```json
[
  {"name": "John Doe", "email": "john@example.com", "password": "secret123"},
  {"name": "Jane Smith", "email": "jane@example.com", "password": "secret456"}
]
```

#### Example (no CSRF):

```sh
curl -X POST http://127.0.0.1:8000/data/import \
  -F "file=@/path/to/users.csv" \
  -F "format=csv"
```

#### Example (with CSRF):

Get token:

```sh
curl http://127.0.0.1:8000/csrf-token
```

Run import:

```sh
curl -X POST http://127.0.0.1:8000/data/import \
  -H "X-CSRF-TOKEN: your-csrf-token-here" \
  -F "file=@/path/to/users.csv" \
  -F "format=csv"
```

**Response:**
```json
{"message": "Import job dispatched"}
```

**Uploaded files:** [`storage/app/temp/`](storage/app/temp) (deleted after processing unless you comment out the unlink line in [`App\Jobs\DataImport`](app/Jobs/DataImport.php))

---

## Checking Imported Data

- Data is imported into the `users` table in [`database/sqlite/database.sqlite`](database/sqlite/database.sqlite).
- View with Tinker:
  ```sh
  php artisan tinker
  App\Models\User::all()->toArray();
  ```
- Or use a SQLite browser and run:
  ```sql
  SELECT * FROM users;
  ```

---

## Troubleshooting

- **No Data Imported/Exported:** Ensure the queue worker is running (`php artisan queue:work`). Check [`storage/logs/laravel.log`](storage/logs/laravel.log) for errors.
- **Database Path Issues:** Use an absolute path for `DB_DATABASE` in `.env`.
- **CSRF Errors:** Exclude endpoints in [`app/Http/Middleware/VerifyCsrfToken.php`](app/Http/Middleware/VerifyCsrfToken.php) or include the `X-CSRF-TOKEN` header.
- **Failed Jobs:** Check the `failed_jobs` table:
  ```sh
  php artisan tinker
  DB::table('failed_jobs')->get()->toArray();
  ```
  Retry failed jobs:
  ```sh
  php artisan queue:retry all
  ```

---

## Contributing

Fork the repo, make changes, and submit a pull request.

---

## License

[MIT License](LICENSE)