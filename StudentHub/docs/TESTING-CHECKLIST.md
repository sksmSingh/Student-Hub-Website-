# StudentHub integration testing checklist

Record **Pass / Fail / Notes** after manually testing each item in XAMPP.

| Area | Test | Expected result | Status |
|---|---|---|---|
| Navigation | Open all main navigation links | Correct page opens | To test |
| Responsive UI | Test at desktop and mobile widths | Layout remains usable; menu toggles | To test |
| Theme | Switch theme and refresh | Preference persists | To test |
| Modal / FAQ / slider | Open, close, expand, change slide | Controls work with mouse and keyboard | To test |
| Registration | Invalid fields and weak passwords | Field-specific validation errors | To test |
| Registration | Valid data | User + student records created | To test |
| Duplicate check | Reuse email or username | Submission rejected | To test |
| Password | Inspect database | Only password hash stored | To test |
| Authentication | Wrong credentials | Generic login failure | To test |
| Access control | Student opens admin route | HTTP 403 / access denied | To test |
| Session | Logout / inactivity timeout | Session is ended | To test |
| Student CRUD | Add, edit, delete, search, filter | Records update in MySQL | To test |
| Event CRUD | Add/edit/delete from admin page | Fetch API updates list without full reload | To test |
| Poster upload | Valid image under 2 MB | File stored and displayed | To test |
| Poster upload | Fake image / oversized file | Server rejects upload | To test |
| JSON data | Search/filter/sort/page events | Visible records update correctly | To test |
| Notices | Load notices JSON | Notice cards render | To test |
| Contact | Valid message | CSV and JSON records created | To test |
| API | Unauthenticated mutation | 401/403 response | To test |
| Dashboard | Create sample accounts/events | Counts and latest users update | To test |
