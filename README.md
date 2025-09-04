Project Overview
This is a Laravel-based application for importing and exporting data in multiple formats (CSV, JSON, XML). It includes abstract classes for importers/exporters, concrete implementations, traits for Eloquent models, background jobs for processing, and a controller with routes for export and import operations. The example uses the User model for demonstration.
The project supports:

Exporting data (e.g., all users) to CSV, JSON, or XML in the background.
Importing data from CSV or JSON files into the database in the background.
Batch export (bonus feature) for multiple formats.

This project was built as an assessment for Svytel Communication.
Requirements

PHP 8.1 or higher
Composer (for dependency management)
SQLite (used as the database; no server required)
Laravel 11.x (or compatible version)
Optional: Postman or curl for testing API endpoints
Optional: SQLite browser (e.g., DB Browser for SQLite) for viewing database data

Installation

Clone the Repository:
textgit clone https://github.com/your-username/svytel-communication-assessment.git
cd svytel-communication-assessment

Install Dependencies:
textcomposer install

Copy Environment File:
textcopy .env.example .env

Generate Application Key:
textphp artisan key:generate

Set Up Database:

Create the SQLite database file and directory if needed:
textmkdir database\sqlite
type nul > database\sqlite\database.sqlite

Update .env with the absolute path to the database file (relative paths may not work reliably on Windows due to path resolution issues):

Right-click on database\sqlite\database.sqlite in File Explorer, select "Copy as path", and paste it into .env as:
textDB_CONNECTION=sqlite
DB_DATABASE=C:\path\to\your\project\svytel-communication-assessment\database\sqlite\database.sqlite
Example: DB_DATABASE=C:\Users\ANKIT\Documents\svytel-communication-assessment\database\sqlite\database.sqlite
Remove any unnecessary DB settings like DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD.




Run Migrations:
textphp artisan migrate

Create Queue Table (for background jobs):
textphp artisan queue:table
php artisan migrate

Seed Test Data (Optional, for testing):
textphp artisan db:seed --class=UserSeeder

Create Exports Directory (for exported files):
textmkdir storage\app\exports


Running the Application

Start the Laravel Development Server:
textphp artisan serve

The application will be available at http://127.0.0.1:8000.


Start the Queue Worker (for background processing of export/import jobs):

Run this in a separate terminal window:
textphp artisan queue:work

Keep this running while testing, as jobs are processed asynchronously.



Testing the Export Feature
The export endpoint (/data/export) dispatches a DataExport job to export all users to the specified format (CSV, JSON, or XML) in the background.

Using curl (CSRF excluded or with token):

If CSRF is excluded (recommended, see app/Http/Middleware/VerifyCsrfToken.php):
textcurl -X POST http://127.0.0.1:8000/data/export -H "Content-Type: application/json" -d "{\"format\":\"csv\"}"

With CSRF token:

Get token: curl http://127.0.0.1:8000/csrf-token (copy csrf_token value).
Run: curl -X POST http://127.0.0.1:8000/data/export -H "Content-Type: application/json" -H "X-CSRF-TOKEN: your-csrf-token-here" -d "{\"format\":\"csv\"}"


Expected response: {"message": "Export job dispatched"}


Using Postman:

URL: http://127.0.0.1:8000/data/export
Method: POST
Body: raw JSON, {"format": "csv"} (or "json"/"xml")
Headers: Content-Type: application/json, X-CSRF-TOKEN: your-csrf-token-here (if CSRF enabled)


Where to Check Exported Files:

Exported files are saved in storage/app/exports/ (e.g., C:\Users\ANKIT\Documents\svytel-communication-assessment\storage\app\exports).
Example filename: export_1234567890.csv (timestamp-based).
Open the directory: dir storage\app\exports
View the file with a text editor or Excel (for CSV).



Testing the Import Feature
The import endpoint (/data/import) dispatches a DataImport job to import data from an uploaded CSV or JSON file into the users table in the background.

Prepare a Test File:

CSV example (users.csv):
textname,email,password
John Doe,john@example.com,secret123
Jane Smith,jane@example.com,secret456

JSON example (users.json):
text[
    {"name": "John Doe", "email": "john@example.com", "password": "secret123"},
    {"name": "Jane Smith", "email": "jane@example.com", "password": "secret456"}
]



Using curl (CSRF excluded or with token):

If CSRF excluded:
textcurl -X POST http://127.0.0.1:8000/data/import -F "file=@C:\path\to\users.csv" -F "format=csv"

With CSRF token:

Get token: curl http://127.0.0.1:8000/csrf-token.
Run: curl -X POST http://127.0.0.1:8000/data/import -H "X-CSRF-TOKEN: your-csrf-token-here" -F "file=@C:\path\to\users.csv" -F "format=csv"


Expected response: {"message": "Import job dispatched"}


Using Postman:

URL: http://127.0.0.1:8000/data/import
Method: POST
Body: form-data

Key: file, Value: Select your CSV/JSON file.
Key: format, Value: csv or json.


Headers: X-CSRF-TOKEN: your-csrf-token-here (if CSRF enabled), Accept: application/json.


Where to Check Imported Data:

Data is imported into the users table in the SQLite database (database/sqlite/database.sqlite).
Use Tinker to view:
textphp artisan tinker
App\Models\User::all()->toArray();

Or open the database with a SQLite browser and run SELECT * FROM users;.


Where to Check the Uploaded CSV File:

The uploaded file is stored temporarily in storage/app/temp/ (e.g., C:\Users\ANKIT\Documents\svytel-communication-assessment\storage\app\temp).
Example filename: temp/abc123.csv (unique).
Open the directory: dir storage\app\temp
Note: The file is deleted after the job processes it (via unlink in DataImport.php). To keep it, comment out the unlink line in the job.



Troubleshooting

No Data Imported/Exported: Ensure the queue worker is running (php artisan queue:work). Check storage/logs/laravel.log for errors:
texttype storage\logs\laravel.log

Database Path Issues: If relative path fails, always use the absolute path in .env as instructed.
CSRF Errors: Exclude endpoints in app/Http/Middleware/VerifyCsrfToken.php or include X-CSRF-TOKEN header.
Failed Jobs: Check failed_jobs table:
textphp artisan tinker
DB::table('failed_jobs')->get()->toArray();

Retry Failed Jobs: php artisan queue:retry all

Contributing
Fork the repo, make changes, and submit a pull request.
License
MIT License.