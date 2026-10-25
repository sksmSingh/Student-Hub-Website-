# StudentHub Practical-to-File Mapping

This map identifies where each practical is implemented in the integrated source project.

## Practical 1: Project Initiation, Requirement Analysis, Sitemap, Wireframe and GitHub Setup
**Aim:** Define the StudentHub project, target users, functional/non-functional requirements, navigation structure and source-control workflow.
**Main files:** Original project wireframe is retained as `Student Portal website Wireframe.drawio` and `wireframe.png`. Project source and GitHub commands are documented in README.md.
**Verification:** Check that the sitemap/wireframe covers home, courses, events, contact, student portal and admin pages; initialize Git and verify the repository history.

## Practical 2: Semantic HTML5 Pages with Accessibility-Ready Structure
**Aim:** Build structured pages using semantic regions, accessible labels, meaningful headings, image alternative text and keyboard-visible focus.
**Main files:** `index.php`, `course.php`, `event.php`, `contact.php`, `placements.php`, `register.php`, `login.php`, shared `includes/header.php` and `includes/footer.php`.
**Verification:** Navigate with keyboard, use the skip link, check form labels and alt text, and confirm each page has a useful title and main landmark.

## Practical 3: Responsive UI Design using CSS Grid and Flexbox
**Aim:** Create a responsive interface using Grid, Flexbox, reusable cards, mobile breakpoints and theme-aware design tokens.
**Main files:** `style.css`; responsive layouts are used across public, student and admin pages.
**Verification:** Test at desktop, tablet and phone widths; check navigation, forms, tables, cards and dashboard statistics for overflow.

## Practical 4: JavaScript DOM Manipulation, Events and UI Interactivity
**Aim:** Implement a collapsible FAQ, modal popup, slider, dismissible announcement, hamburger menu and light/dark theme switcher.
**Main files:** `assets/js/app.js`, `includes/header.php`, `includes/footer.php`, `index.php`; theme preference is stored in localStorage.
**Verification:** Open/close the modal by button, backdrop and Escape; expand FAQ items; move between slides; dismiss announcement; toggle menu/theme and refresh.

## Practical 5: Student Registration Form with Frontend Validation
**Aim:** Use HTML5 fields and JavaScript Regex validation for full name, username, email, Indian mobile number, password, confirmation, course, year, gender and terms.
**Main files:** `register.php`, `assets/js/app.js`.
**Verification:** Test invalid name/email/mobile, weak password, password mismatch, missing course/year/gender and unchecked terms; confirm field-level errors and successful valid submission.

## Practical 6: Rendering External JSON Data using Fetch API, Search, Filter, Sorting and Pagination
**Aim:** Load events through the JSON API backed by MySQL with JSON seed fallback, load notices and sample profiles from JSON files, and render results dynamically.
**Main files:** `event.php`, `assets/js/events.js`, `directory.php`, `assets/js/directory.js`, `api/events.php`, `api/json-data.php`, `data/events.json`, `data/notices.json`, `data/students.json`, `data/faqs.json`.
**Verification:** Search for an event, filter by category, change sorting, navigate pages, inspect notice cards and search sample profiles; test empty/error states.

## Practical 7: PHP Form Processing with Server-Side Validation and CSV/JSON Storage
**Aim:** Process contact and registration submissions with server-side validation, safely escape output and store non-password record data in CSV and JSON files.
**Main files:** `contact.php`, `register.php`, `storage/contact-submissions.csv`, `storage/contact-submissions.json`, `storage/registration-records.csv`, `storage/registration-records.json`.
**Verification:** Submit valid and invalid contact data; verify success/error messages and records in both formats. Confirm no password or password hash is stored in audit files.

## Practical 8: MySQL Schema Design, ER Model and Database Connectivity
**Aim:** Design relational tables for users, students, events, registrations and remember-me tokens, with primary/foreign keys and indexes.
**Main files:** `database/studenthub.sql`, `config/database.php`, `config/config.php`.
**Verification:** Import the SQL file in phpMyAdmin, verify tables and foreign keys, run a simple SELECT query and load pages with MySQL running.

## Practical 9: Secure User Registration with MySQLi
**Aim:** Revalidate all registration fields server-side, reject duplicate email/username, use prepared statements and store passwords using password_hash().
**Main files:** `register.php`, `config/database.php`, `database/studenthub.sql`.
**Verification:** Register a valid account; repeat the same email/username; inspect users.password_hash to confirm the plain password is not stored.

## Practical 10: Secure Login/Logout with Sessions, RBAC, Timeout and Remember-Me
**Aim:** Verify credentials using password_verify(), regenerate session ID, protect routes by role, expire inactive sessions and use a hashed persistent remember token.
**Main files:** `login.php`, `logout.php`, `includes/bootstrap.php`, `includes/auth.php`, `includes/remember.php`, `database/studenthub.sql`.
**Verification:** Test valid/invalid login, logout, student access to admin route, session inactivity timeout and remember-me expiry/revocation.

## Practical 11: Student Management CRUD with Search and Filter
**Aim:** Allow authorized administrators to add, list, update and delete student records with MySQLi prepared statements, search and course/year filters.
**Main files:** `admin/students.php`, `api/students.php`, `admin/_sidebar.php`.
**Verification:** Add, edit and delete a sample student; search by name/email/mobile; filter by course/year; verify records in MySQL.

## Practical 12: Event Management CRUD with Poster Upload and Validation
**Aim:** Provide admin event creation, viewing, editing and deletion. Validate poster size, actual MIME type and image contents; generate random filenames.
**Main files:** `admin/events.php`, `assets/js/admin-events.js`, `api/events.php`, `storage/uploads/event-posters/`, `database/studenthub.sql`.
**Verification:** Create/edit/delete an event without full page reload; upload a valid image; test oversized and non-image files; verify the poster appears on Events page.

## Practical 13: PHP JSON API and Fetch-Based CRUD without Page Reload
**Aim:** Expose JSON endpoints for event CRUD and student operations. Use Fetch API, JSON responses, CSRF headers, authentication and server-side validation.
**Main files:** `api/events.php`, `api/students.php`, `assets/js/admin-events.js`, `admin/events.php`.
**Verification:** Inspect API calls in DevTools Network; create/update/delete an event and verify the list changes without full page reload; test unauthenticated and non-admin requests.

## Practical 14: Admin Dashboard with Role-Based Access Control and Dynamic Menu
**Aim:** Show live user/student/event/registration counts and latest user registrations; render navigation options based on the authenticated role.
**Main files:** `admin/dashboard.php`, `includes/header.php`, `includes/auth.php`, `admin/_sidebar.php`.
**Verification:** Sign in as admin and verify statistics; sign in as student and verify student menu; directly request admin pages as student and confirm access is denied.

## Practical 15: Final Integration, Testing, Documentation, GitHub Submission and Local Deployment
**Aim:** Connect public pages, forms, database, session security, student/event modules, API operations and documentation into one deployable project.
**Main files:** `README.md`, `docs/TESTING-CHECKLIST.md`, `database/studenthub.sql`, `.gitignore`, all PHP/CSS/JS/data files.
**Verification:** Follow README setup in XAMPP; complete the testing checklist; check responsive layouts, CRUD, auth, file uploads, API behavior and GitHub repository.
