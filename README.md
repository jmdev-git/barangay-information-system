# Barangay Information System

A Laravel-based Barangay Information System for managing residents, households, clearance requests, blotter records, announcements, reports, and REST API endpoints.

## Developers

- Mary Grado
- Juliana Marie D. Espenocilla
- Sheriene Mae F. Escaño

## Main Features

### Admin Module
- Dashboard statistics
- Manage residents
- Manage households
- Manage blotter records
- Approve or reject clearance requests
- View announcements
- Generate reports
- Export reports as PDF, XLSX, CSV, or JSON

### Resident Module
- Login and session-based access
- Request barangay clearance
- Track clearance request status
- View announcements 

### REST API
The API endpoints are separate from the web pages and use the `/api` prefix.

Examples:
- `GET /api/residents`
- `POST /api/clearances`
- `GET /api/blotters`
- `PUT /api/clearances/{clearance}/approve`
- `DELETE /api/blotters/{blotter}`

Web pages use normal routes like `/residents`, `/households`, and `/blotters`.

## Laravel Requirements Covered

- Authentication: login, logout, password hashing, sessions
- CRUD: residents, households, blotters, clearances
- Database: migrations, seeders, Eloquent ORM, relationships
- Blade: `x-layout`, reusable components, Bootstrap UI
- Middleware: admin-only protected routes
- REST API: GET, POST, PUT/PATCH, DELETE
- Reports: PDF, XLSX, CSV, JSON export
- Dashboard: statistics and quick actions
- Search/filter: residents, households, blotters, clearances

## Database Tables

- users
- residents
- households
- clearances
- blotters
- announcements
- personal_access_tokens
- sessions

## Important Relationships

- User has one Resident
- Resident belongs to User
- Resident belongs to Household
- Household has many Residents
- Resident has many Clearances
- Resident has many Blotter records as complainant/respondent

## Default Accounts

After running the seeders:

### Admin
- Email: `admin@gmail.com`
- Password: `password123`

### Resident
- Email: `resident@gmail.com`
- Password: `password123`

## Setup Instructions

1. Clone or download the project.
2. Open the project folder in VS Code.
3. Install PHP dependencies:

```bash
composer install
```

4. Install frontend dependencies:

```bash
npm install
```

5. Copy the environment file:

```bash
copy .env.example .env
```

6. Generate app key:

```bash
php artisan key:generate
```

7. Create or configure your database in `.env`.

For SQLite:

```env
DB_CONNECTION=sqlite
```

Then create the file:

```bash
type nul > database\database.sqlite
```

8. Run migrations and seeders:

```bash
php artisan migrate:fresh --seed
```

9. Start Laravel:

```bash
php artisan serve
```

10. Open the system:

```txt
http://127.0.0.1:8000/login
```

## Export Reports

Go to:

```txt
http://127.0.0.1:8000/reports
```

Available export formats:

- PDF
- XLSX
- CSV
- JSON

## GitHub and Deployment


```txt
https://github.com/gradomary9/barangay_information
```


```txt
https://barangay-information-gz3e.onrender.com
```

