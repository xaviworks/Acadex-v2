## Security Requirements — Acadex v2

Version 1.0 • Last updated: 2025‑09‑08

Scope
- Web‑based grading and student records built on Laravel 12.x
- Roles: Admin, Dean, Chairperson, Instructor
- Modules: authentication, dashboards, academic periods, subjects, students, activities, grades, imports

### 1) Confidentiality
- Auth: Laravel authentication with bcrypt‑hashed passwords; HTTPS in production
- RBAC: Gate/Policies or middleware enforce role and department/subject scoping
- Least privilege: Instructors access only their assigned subjects and enrolled students; Deans read‑only across departments; Chairpersons manage within their department; Admins full access
- Sensitive data: student PII, grades, activity scores, tokens; do not log raw payloads
- API: protect API routes with sanctum/auth middleware; rate‑limit public endpoints

### 2) Integrity
- Validation: server‑side validation for all forms and uploads; reject unexpected columns in Excel imports
- ORM: use parameterized Eloquent queries to prevent SQL injection
- Constraints: enforce FKs for students, subjects, academic periods, and activity/grade tables
- Transactions: wrap multi‑step grade generation and imports in DB transactions
- Audit: log logins/logouts and key data changes where relevant

### 3) Availability
- Backups: daily DB backups; verify restore quarterly
- Errors: graceful error pages; avoid leaking stack traces in production
- Rate limiting: throttle brute‑force login attempts and noisy endpoints
- Dependencies: pin Composer/npm versions; track security advisories

### 4) Session and cookies
- Secure, HttpOnly, SameSite=strict cookies in production
- Session timeout after 30 minutes of inactivity

### 5) File handling
- Accept only expected file types for Excel (.xlsx/.xlsm) and size limits
- Scan and store uploads outside the public path; import via queued jobs when needed

### 6) Configuration
- No secrets in source control; use .env
- APP_DEBUG=false in production; LOG_LEVEL=info or warning
- Force HTTPS via trusted proxies if behind a load balancer

### 7) Incident response
- On suspected compromise: revoke sessions, rotate keys, force password resets, review logs
- Keep a short runbook with contacts and restore procedures

Owners: Acadex v2 maintainers • Review: every 12 months or after major feature/security changes 