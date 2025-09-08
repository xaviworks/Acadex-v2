# Acadex v2 — Web‑Based Grading and Student Records

Acadex v2 is a Laravel application for managing subjects, students, activities, and grades across academic periods. It supports multiple roles (Admin, Dean, Chairperson, Instructor) and an approval flow for new instructor accounts.

Key features
- Role‑based dashboards and permissions
- Academic period selection (1st, 2nd, Summer)
- Subject assignment to instructors
- Student roster management (add, update, drop)
- Activities and scoring, partial and final grade computation
- Excel import for student lists (Maatwebsite/Excel)
- Account approvals and login/logout auditing

Tech stack
- PHP 8.2+, Laravel 12.x
- Vite 6, Tailwind CSS, Alpine.js, Bootstrap 5
- Maatwebsite/Excel, jenssegers/agent

## Getting started

Prerequisites
- PHP 8.2+ with ext-pdo and a database (MySQL/MariaDB or SQLite)
- Composer
- Node.js 18+ and npm

Setup
1. Copy env and configure app/database/mail
	- cp .env.example .env
	- Set APP_URL, DB_*, MAIL_* values
2. Install dependencies
	- composer install
	- npm install
3. Generate app key
	- php artisan key:generate
4. Run migrations (seed if you maintain seeders)
	- php artisan migrate
5. Start the dev servers
	- php artisan serve
	- npm run dev

Open http://127.0.0.1:8000

Notes
- Tests use an in‑memory SQLite database by default (see phpunit.xml).
- If you prefer SQLite locally, point DB_CONNECTION=sqlite and DB_DATABASE to a .sqlite file.

## Using the app

Sign‑up and access
- New users register via the login screen. Chairperson/Admin reviews and approves accounts before full access.

Academic periods
- After login, select an active academic period (1st, 2nd, Summer) to unlock dashboards and routes.

Student import
- Instructors can import student lists from Excel at Instructor → Students → Import.
- Sample templates live under the repository folder: “Acadex Excel Student Testing/…”.

Grades
- Manage activities and scores per subject. Final grades can be generated once partials are complete.

## Testing

- php artisan test
- The phpunit.xml config sets DB_CONNECTION=sqlite and DB_DATABASE=:memory: for fast tests.

## Project status and contributions

This repository is maintained for internal academic use. Please open an issue for bugs or questions. Contributions are welcome via pull requests following PSR‑12 and Laravel conventions.

## Security

If you find a security issue, please avoid filing a public issue. Contact the maintainers privately and we’ll respond promptly.
