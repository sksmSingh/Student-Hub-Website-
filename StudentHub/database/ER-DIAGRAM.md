# StudentHub ER Model

```mermaid
erDiagram
    USERS ||--o| STUDENTS : "has profile"
    USERS ||--o{ EVENTS : "creates"
    STUDENTS ||--o{ REGISTRATIONS : "makes"
    EVENTS ||--o{ REGISTRATIONS : "receives"
    USERS ||--o{ REMEMBER_TOKENS : "owns"

    USERS {
        int id PK
        varchar full_name
        varchar username UK
        varchar email UK
        char mobile
        varchar password_hash
        enum role
        boolean is_active
        timestamp created_at
    }
    STUDENTS {
        int id PK
        int user_id FK
        varchar full_name
        varchar email UK
        char mobile
        varchar course
        tinyint year_of_study
        varchar gender
    }
    EVENTS {
        int id PK
        varchar title
        varchar description
        enum category
        date event_date
        varchar poster_path
        int created_by FK
    }
    REGISTRATIONS {
        int id PK
        int student_id FK
        int event_id FK
        timestamp registered_at
    }
    REMEMBER_TOKENS {
        bigint id PK
        int user_id FK
        char selector UK
        char token_hash
        datetime expires_at
    }
```

`students.user_id` is nullable so administrators can manage standalone student records as well as profiles created through registration. `registrations` has a unique constraint on `(student_id, event_id)` to prevent duplicate event registrations. Passwords and remember-me validators are stored only as hashes.
