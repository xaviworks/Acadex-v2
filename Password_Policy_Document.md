## Password Policy — Acadex v2

Version 1.0 • Last updated: 2025‑09‑08

This policy applies to all Acadex v2 users (Admins, Deans, Chairpersons, Instructors). It defines how passwords are created, stored, and maintained in the system.

### 1) Creating a password
- Minimum length: 12 characters
- Use three or more of: uppercase, lowercase, numbers, symbols
- Passphrases are encouraged (e.g., several unrelated words)
- Do not use: easy patterns (1234, qwerty), names of people/courses/schools, or anything based on your email

### 2) Storage and transport
- Passwords are hashed using bcrypt via Laravel’s Hash facade
- Passwords are never stored or logged in plain text
- Production traffic must use HTTPS/TLS end‑to‑end

### 3) Reuse and rotation
- Don’t reuse your last 8 passwords
- Rotation is only required after a suspected compromise or when prompted by an administrator

### 4) Lockouts and attempts
- Accounts lock for 10 minutes after 5 consecutive failed logins
- Repeated lockouts may trigger an admin review

### 5) Resets and recovery
- Reset links are sent to the registered email and expire after 30 minutes
- Temporary passwords/links are one‑time use and require setting a new password on first login
- Support will never ask for your password

### 6) Administrator expectations
- Admin and Chairperson accounts should enable MFA when made available
- Default or shared credentials are not allowed
- User provisioning must require an initial password change on first login

### 7) Auditing
- Successful and failed logins, logouts, and password resets are logged with timestamp and user ID

### 8) Reporting
- Report suspicious account activity to the project maintainer immediately

Owners: Acadex v2 maintainers • Review cycle: every 12 months or after major auth changes 