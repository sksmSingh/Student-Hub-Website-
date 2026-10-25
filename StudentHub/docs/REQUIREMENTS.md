# Project Requirements — StudentHub

## Problem statement
College information is spread across pages and manual processes. StudentHub provides one portal for public college information, student accounts, event discovery and registration, and administrative record management.

## Target users
- Visitors: view courses, events, placements and contact information.
- Students: register, sign in, view their profile and register for events.
- Administrators: view dashboard statistics and manage student/event records.

## Functional requirements
1. Display accessible, responsive public pages and a shared navigation menu.
2. Support interactive FAQ, modal, content slider, dismissible announcement, mobile menu and light/dark theme.
3. Validate registration fields in JavaScript and PHP.
4. Reject duplicate usernames/emails and store password hashes, never plain passwords.
5. Process contact messages and store records in CSV and JSON.
6. Connect PHP to MySQL/MariaDB through MySQLi.
7. Authenticate users using sessions and `password_verify()`; support role checks, inactivity timeout and optional remember-me token.
8. Let students view their profile and register for events.
9. Let admins search/filter and CRUD student records.
10. Let admins CRUD events and upload validated posters.
11. Provide JSON API endpoints and Fetch-based CRUD without full page reload.
12. Show dynamic admin statistics and latest registrations.
13. Provide local XAMPP setup and GitHub submission instructions.

## Non-functional requirements
- Responsive on common desktop, tablet and mobile widths.
- Use semantic HTML, accessible labels, keyboard focus indicators and status/error announcements.
- Use prepared SQL statements for user-controlled database inputs.
- Escape user-provided output before rendering it as HTML.
- Validate uploaded image content, MIME type and size on the server.
- Protect privileged pages and API mutations on the server, not only by hiding menu items.
- Keep credentials and real student records out of source control.

## Technology choices
HTML5, CSS3, JavaScript, PHP 8.1+, MySQL/MariaDB, MySQLi, Fetch API, JSON, CSV, Apache/XAMPP, Git and GitHub.
