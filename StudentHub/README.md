# StudentHub — Integrated College Portal

StudentHub extends the original Random College frontend into one PHP/MySQL application for Practicals 1–15. It includes responsive semantic pages, interactive JavaScript UI, validated registration, PHP form processing, CSV/JSON storage, MySQLi persistence, sessions, role-based access, student/event CRUD, poster uploads, a JSON API and an admin dashboard.

## Requirements
- XAMPP with Apache, PHP 8.1+ and MySQL/MariaDB
- A modern browser
- Optional: Git and GitHub account

## Run locally with XAMPP
1. Extract the `Practical` folder into `C:\xampp\htdocs\studenthub` (Windows) or the Apache document root.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open phpMyAdmin at `http://localhost/phpmyadmin`.
4. Import `database/studenthub.sql`. This creates the `studenthub` database and tables.
5. Check `config/database.php`. The default XAMPP settings are host `127.0.0.1`, user `root`, blank password, database `studenthub`. For other setups, set `STUDENTHUB_DB_HOST`, `STUDENTHUB_DB_USER`, `STUDENTHUB_DB_PASS`, `STUDENTHUB_DB_NAME`, and optionally `STUDENTHUB_DB_PORT` environment variables.
6. Create an administrator account from a terminal in the project folder:
   ```bash
   php scripts/create_admin.php admin@example.com "Choose-A-Strong-Password!" "Portal Administrator"
   ```
   Use a private password of at least 12 characters. The script only runs from the command line. The admin username is `admin`; sign in with either username or email.
7. Open `http://localhost/studenthub/`.
8. Register a student account from the Register page and test the student portal, event registration, admin CRUD and dashboard.

### Apache virtual host (optional)
Add the following to XAMPP's `apache/conf/extra/httpd-vhosts.conf` and make sure `httpd-vhosts.conf` is included in `httpd.conf`:

```apache
<VirtualHost *:80>
    ServerName studenthub.local
    DocumentRoot "C:/xampp/htdocs/studenthub"
    <Directory "C:/xampp/htdocs/studenthub">
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog "logs/studenthub-error.log"
    CustomLog "logs/studenthub-access.log" common
</VirtualHost>
```

Add `127.0.0.1 studenthub.local` to the system hosts file, restart Apache, then open `http://studenthub.local/`. The default `APP_BASE` is `/studenthub` for the XAMPP folder path above. For the virtual host at the web root, change `APP_BASE` in `config/config.php` to an empty string (`''`) before testing.

## Feature map
- `index.php`: semantic landing page, modal popup, notification banner, slider, collapsible FAQ, hamburger menu and theme switcher.
- `register.php`: HTML5 registration form, JavaScript Regex validation, server-side validation, duplicate username/email check, password hashing, MySQL insert and CSV/JSON audit data (no passwords stored in files).
- `contact.php`: PHP server validation and CSV + JSON contact storage.
- `event.php`: event listing via Fetch API, search, category filter, sorting, pagination and JSON notices.
- `api/events.php`: JSON endpoint for event listing and admin create/update/delete. Mutations require an authenticated admin and CSRF token.
- `api/students.php`: admin-only JSON endpoint for student list/create/update/delete.
- `admin/students.php`: MySQLi student CRUD, search and filters.
- `admin/events.php`: Fetch-based asynchronous event CRUD and validated poster uploads.
- `admin/dashboard.php`: live user/student/event/registration counts and latest accounts.
- `student/dashboard.php`: student profile and event registrations.
- `database/studenthub.sql`: schema and sample events for `users`, `students`, `events`, `registrations` and `remember_tokens`.
- `database/ER-DIAGRAM.md`: Mermaid ER model for the relational schema.
- `docs/REQUIREMENTS.md` and `docs/SITEMAP.md`: project initiation requirements and sitemap.
- `docs/StudentHub_Practical_Documentation.docx`: editable Word practical-file overview with a section for each practical and screenshot placeholders.
- `docs/PRACTICAL-MAPPING.md`: practical-to-source-file mapping.
- `data/events.json`, `data/notices.json`, `data/students.json`, `data/faqs.json`: external JSON data sources for Fetch API exercises.

## Practical test checklist
1. Check navigation links and narrow/mobile layout; test the hamburger and light/dark theme persistence.
2. Test slider controls, FAQ expansion, modal close button/backdrop/Escape, and dismissible announcement.
3. Submit registration with invalid name, email, mobile, weak/mismatched password, missing course/year/gender or unchecked terms. Verify field-level errors.
4. Register a valid student. Confirm duplicate email/username is rejected and the database stores a password hash, not the plain password.
5. Sign in and sign out. Try opening `/admin/dashboard.php` as a student; it must return access denied. Verify session inactivity timeout after 30 minutes.
6. As admin, add/edit/delete/search/filter students. Test event CRUD without a full page reload.
7. Upload a JPG/PNG/WebP poster under 2 MB; try an oversized file and a non-image file and verify rejection.
8. On Events, test search, category filter, sorting, pagination, notices loading and event registration.
9. Submit contact messages and check `storage/contact-submissions.csv` and `storage/contact-submissions.json`. Registration audit records are stored in `storage/registration-records.csv` and `.json`.
10. Check API responses in browser DevTools → Network and test unauthenticated API mutation returns 401/403.

## Security notes
- Use HTTPS for real deployment; local HTTP is only for the classroom demo.
- Change the local admin password and never commit credentials or real personal data to GitHub.
- Keep `storage/` and uploaded files non-executable. Validate uploaded MIME type, image contents and size on the server.
- This is an educational starter project, not a production-ready college information system. Add rate limiting, email verification, privacy/retention controls, stronger deployment configuration and operational monitoring before real-world use.

## GitHub submission
1. Create a repository named `StudentHub`.
2. From the project root run:
   ```bash
   git init
   git add .
   git commit -m "Integrate StudentHub practicals 1-15"
   git branch -M main
   git remote add origin https://github.com/YOUR-USERNAME/StudentHub.git
   git push -u origin main
   ```
3. Do not commit exported real student/contact records or passwords. The `storage` folder is intended for local generated data only.
