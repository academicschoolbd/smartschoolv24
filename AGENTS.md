# Smart School v24 — Agent / Session Notes

This file is read automatically by Devin, Cursor, Claude Code, and other
AI coding assistants at the start of every session. Keep it current — it's
the fastest way to bring a fresh session up to speed on this project.

## 1. What this is

**Smart School v24** is a multi-tenant CodeIgniter 3 school management
system (originally Ramom School v6.x by Envato seller "RamomCoder").
It's deployed at https://smartschool.bd with one production tenant at
https://ngps.smartschool.bd. Every tenant shares **one** codebase, one
MySQL database, one set of files — tenancy is row-level via
`branch_id` and host-level via the `custom_domain` table.

- Stack: PHP 7.x/8.x · CodeIgniter 3 · MySQL · jQuery · Bootstrap 3 theme (Porto)
- DB dump committed at the repo root: `zgruhjabaz_smartschoolbd.sql` (~2.4MB)
- Original distribution zip: `v24now.zip` (104MB, Git LFS)
- Public test instance: `https://ngps.smartschool.bd`

## 2. Layout — what lives where

```
application/
├── config/           # CI config (DB, autoload, routes…)
├── controllers/      # One file per top-level URL segment
├── core/MY_Controller.php   # Admin_Controller (login + subdomain isolation)
├── helpers/general_helper.php  # is_loggedin, get_permission, translate, ...
├── libraries/        # Csvimport (CSV), App_lib (DB helpers), …
├── models/           # *_model.php — DB access layer
└── views/            # Blade-less PHP templates
    ├── layout/{index,sidebar,topbar,header,footer}.php
    └── <feature>/…
assets/               # CSS, JS, vendor packs
system/               # CodeIgniter 3 core (don't edit)
uploads/              # User uploads (per-branch)
v24now.zip            # Original distribution (LFS)
zgruhjabaz_smartschoolbd.sql  # DB dump for bootstrap
```

`application/controllers/` is the entry point for **every** request.
URL `/foo/bar/123` → `controllers/Foo.php` → `bar(123)` method. CI3 routing.

## 3. Multi-tenant model — the rules

- Every business table has a `branch_id` column. Always scope queries
  with `$this->application_model->get_branch_id()` (auto-loaded).
- The `branch` table is the tenant list. The `custom_domain` table maps
  hostnames → `school_id` (= `branch_id`). `Admin_Controller`'s
  `enforce_subdomain_branch_isolation()` redirects logged-in users to
  authentication if their session branch doesn't match the host.
- The `schoolyear` table is **global** (no `branch_id`). Same year list
  for every tenant. Active year is per-user via `set_session_id` in
  user data, read by `get_session_id()`.
- Super admin (role.id = 1) is exempt from subdomain pinning unless
  `strict_subdomain_isolation` config is on.
- Roles: `1=Super Admin, 2=Admin, 3=Teacher, 4=Accountant, 5=Librarian,
  6=Receptionist, 7=Student, 8=Parent` (verify against `roles` table).
  Role IDs `1-6` write to `staff` + `login_credential`. Role `7` writes
  to `student` + `enroll` + `login_credential`. Role `8` writes to
  `parents` + `login_credential`.

## 4. Auto-loaded helpers / libraries

`application/config/autoload.php` auto-loads these — call them freely
in any controller/view without `$this->load->…`:

- Libraries: `database, session, pagination, xmlrpc, form_validation, upload, app_lib`
- Helpers: `url, file, form, security, directory, general` (general_helper.php)
- Models: `application_model`

Common helpers from `general_helper.php`:
`is_loggedin()`, `is_superadmin_loggedin()`, `get_permission($module, $action)`,
`access_denied()`, `set_alert($type, $msg)`, `translate($key)`,
`get_session_id()`, `app_generate_hash()`.

## 5. Conventions to follow

- **Controllers** extend `Admin_Controller` (admin pages),
  `User_Controller` (student/parent portal), `Authentication_Controller`
  (login flows), or `Frontend_Controller` (public website).
- **Permission gate**: first line of every controller action is
  `if (!get_permission('module', 'is_view')) { access_denied(); }`.
  Module names come from the `permission` table.
- **Forms**: validate via CI `form_validation` library, render with
  `form_open()`, `form_dropdown()`, `form_error()` from the form helper.
- **i18n**: every user-visible string is wrapped in `translate('key')`.
  The helper auto-inserts missing keys into the `languages` table on
  first hit — no code deploy is needed to add a new label. Edit labels
  through **System Settings → Translations**.
- **Sidebar menu items**: add a `<li>` block in
  `application/views/layout/sidebar.php`, gated by `get_permission(...)`.
- **DB writes**: `$this->db->insert(table, array(...))` / `update` /
  `delete`. Avoid raw SQL. Read with `->select(...)->where(...)->get('table')`.
- Sample CSV downloads for bulk import pages are served as
  `application/csv` from PHP `php://output`.

## 6. Recent PRs (what was added, why)

| # | Branch | What |
|---|---|---|
| 1 | `add-v24now-zip-and-sql` | Initial commit — zip + SQL via Git LFS |
| 2 | `extract-v24now-source` | Unpacked the zip into source files in repo |
| 3 | `quick-admission` | Single-student form (Roll, Full Name, Class, Section) under **Admission → Quick Admission**, controller method `Student::quick_add()` |
| 4 | `quick-bulk-import` | Multi-entity bulk import (Students / Teachers / Staff / Subjects / Classes) from CSV / TXT / `.xlsx` at **Admission → Quick Bulk Import**. Controller `Bulk_admission`, view `bulk_admission/index.php`, library `Quick_xlsx_reader` (dependency-free `.xlsx` reader using PHP's bundled `ZipArchive` + `SimpleXMLElement`) |

A drop-in zip for PRs #3 + #4 (no merge required) was generated at
`/home/ubuntu/work/v24-quick-features.zip` and delivered to the user.

## 7. Useful tables (skim before designing new features)

- `student` (profile) + `enroll` (class/section/session) + `login_credential`
- `staff` + `login_credential` (role != 7)
- `class` + `section` + `sections_allocation` (class↔section many-to-many)
- `subject` + `subject_assign` (subject↔class)
- `exam` + `mark_distribution` + `mark` (JSON-encoded per-assessment marks)
- `timetable_detail` (Exam timetable; **required** for mark-entry UI to render)
- `branch` (tenants), `custom_domain` (host pinning), `schoolyear` (global)
- `roles`, `permission`, `permission_category` (RBAC)
- `languages` (i18n; key/value per locale; auto-seeded by `translate()`)

## 8. Common gotchas (real-world bugs seen in this session)

1. **Mark Entry page renders blank rows** → almost always missing
   `timetable_detail` row for that exam+subject. Create one via
   Timetable → Set Examwise before marks can be entered.
2. **Subject dropdown shows duplicates** → the NCTB seeder de-dupes
   by `(name, subject_code, subject_type)`. A manually-added "English"
   without a code coexists with an imported "English" with `ENG101`.
   Clean up via **Academic → Subjects** (delete cascades to
   `subject_assign` but NOT to `mark` — re-point first if marks exist).
3. **Session switcher menu missing** → it's the calendar icon in the
   top-right of the admin layout, always rendered. Student/parent
   portal uses a different topbar and intentionally doesn't have it.
4. **"Session not available"** dropdown → only one row in `schoolyear`.
   Add more from **System Settings → Session Settings** as Super Admin.
   `schoolyear` is global, so a single SQL insert seeds every tenant.
5. **CRLF in views** — many view files have Windows line endings.
   When editing programmatically, match on `\r\n` not `\n`, or use
   `sed`/`python` with byte-mode rather than text-mode tools.
6. **PHP `php -l` not in the dev VM** → don't rely on local PHP lint;
   review the code carefully and rely on the live deploy / CI.

## 9. Local development (not yet wired)

There is no `composer.json`, no Docker setup, no CI workflow in the
repo today. To bring up a local instance:

1. `git lfs install && git clone <repo>` (LFS pulls the zip + SQL).
2. Import `zgruhjabaz_smartschoolbd.sql` into a fresh MySQL DB.
3. Extract `v24now.zip` or use the unpacked `application/` tree
   directly. Serve the repo root with Apache / `php -S 0.0.0.0:8080`.
4. Edit `application/config/database.php` with your DB credentials.
5. Default super admin is in the SQL dump (check `login_credential`).

A future PR should add a `docker-compose.yml` + a CI workflow that
at least runs `php -l` over `application/`. Track this in an issue.

## 10. Where this file goes wrong, fix it

If a future session discovers something this file claims is wrong,
or learns a new convention worth recording, append/edit it in the
same PR that fixes the bug or adds the feature. The whole point is
that the file stays accurate over time.
