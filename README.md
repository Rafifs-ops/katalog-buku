# SB Admin — Katalog Buku

A Laravel book-catalog application with an SB Admin 2 (Bootstrap 4) admin panel and a public catalog, backed by a Sanctum token API. Books belong to users, are grouped by global categories, and each book can carry an uploaded cover image.

- Admin panel: session login, book CRUD, dashboard.
- Roles: every user has a `role` — `guest` (default) or `super admin`. Super admin sees all books read-only and owns category CRUD.
- Public catalog: no login required.
- API: token-based (Bearer) auth for register/login/current user.
- Interactive API documentation: `/docs/api` (Scramble).

## Tech Stack

| Layer | Technology |
| --- | --- |
| Language / framework | PHP 8.5, Laravel 13 (`laravel/framework ^13`) |
| Database | SQLite (`database/database.sqlite`) |
| Auth (web) | Session via `auth:sanctum` guard |
| Auth (API) | Laravel Sanctum personal access tokens (Bearer) |
| Frontend | Blade templates, SB Admin 2 theme (Bootstrap 4), Font Awesome, jQuery |
| CSS/JS assets | Served statically from `public/` (`css/sb-admin-2.min.css`, `js/sb-admin-2.min.js`) |
| Build tooling | Vite 8 + Tailwind 4 (present in `package.json`, but no rendered view references it) |
| API docs | `dedoc/scramble` at `/docs/api` |
| Tests / style | Pest 5 (`php artisan test`), Laravel Pint (`vendor/bin/pint`) |

## Architecture

Single Laravel application (MVC, not a monorepo):

```
Browser ──────────────┐                API client
   │                  │                   │
   ▼                  ▼                   ▼
routes/web.php                       routes/api.php
   ├─ guest ────── Login/Register (session auth)
   ├─ auth:sanctum ─ admin pages (dashboard, books CRUD)
   └─ public ────── katalog pages
   │                                  │
   ▼                                  ▼
Controllers ── Form Requests      Api\AuthController
   │        (validation)              │
   ├──── Policies (BookPolicy)        ▼
   ▼                            Sanctum Bearer token ── JSON
Eloquent models (User, Book, Category) ── SQLite
   │
   ▼
Blade views (layouts/admin | layouts/public + components) ── HTML
```

Key wiring:

- **`routes/web.php`** — all page routes. The dashboard (`GET /dashboard`, named `admin`) is an inline closure querying counts/books directly (no controller). `GET /{book}` (katalog detail) is a single-segment wildcard and must stay the **last** route, otherwise it swallows `/books` and `/dashboard` (Laravel matches routes in registration order).
- **`routes/api.php`** — `POST api/login`, `POST api/register` (public), `GET api/user` (`auth:sanctum`). The `/api` prefix is added by Laravel's default routing configuration.
- **Controllers** (`app/Http/Controllers/`): `BookController` (CRUD, scoped to the authenticated user — super admin sees all books), `CategoryController` (category CRUD, super admin only), `KatalogController` (public list/detail), `LoginController`, `RegisterController`, `Api\AuthController` (JSON auth).
- **Validation** lives in Form Requests: `BookRequest` (title/description/content `min:20`, cover image `≤ 2048 KB`, `category_id` must exist), `CategoryRequest` (`name` 3–255 chars, unique), and `RegisterRequest`.
- **Authorization**: `App\Policies\BookPolicy` — `update`/`delete` are owner-only (so a super admin can still edit their own books); `view` allows the owner **or** a super admin. `App\Policies\CategoryPolicy` — every ability requires the `super admin` role. Both are enforced with `Gate::authorize()` in the controllers; denials return `403`.
- **Models**: `User` — `hasMany` books; `Book` — `belongsTo` user & category; `Category` — `hasMany` books (global reference data, seeded). `User::$role` holds `guest` / `super admin` (constants `User::ROLE_GUEST`, `User::ROLE_SUPER_ADMIN`, helper `isSuperAdmin()`); the column is deliberately **not** mass-assignable, so it can never be set from request input.
- **Storage**: covers are stored on the `public` disk (`storage/app/public/covers/...`) and served through the `public/storage` symlink as `storage/covers/...`.
- **Database**: SQLite; `php artisan migrate:fresh --seed` rebuilds it (8 categories, 3 users — one super admin — and 3 books per user).

### Schema (simplified)

| Table | Columns |
| --- | --- |
| `users` | `id`, `name`, `email`, `password`, `role` (default `guest`), timestamps |
| `categories` | `id`, `name`, timestamps |
| `books` | `id`, `title`, `description`, `content`, `cover_url` (nullable), `user_id` (FK → users), `category_id` (FK → categories), timestamps |
| `personal_access_tokens` | Sanctum tokens for the API |

## Getting Started

Requirements: PHP 8.5, Composer, Node.js (recent LTS).

### Quick setup

```bash
composer setup
```

Runs: `composer install` → copies `.env` → `php artisan key:generate` → `php artisan migrate --force` → `npm install --ignore-scripts` → `npm run build`.

### Manual setup

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
npm install
npm run build
```

### Run the development server

```bash
composer dev
```

Starts three processes in one command (`php artisan serve`, `php artisan queue:listen`, `npm run dev`) via `npx concurrently`. The app is then available at `http://localhost:8000`.

> The Tailwind/Vite pipeline builds `resources/css/app.css` + `resources/js/app.js`, but no rendered page uses them — admin and public pages load the SB Admin 2 assets from `public/` directly. If you edit `resources/css|js/sb-admin-2*`, mirror the change into `public/css|public/js` (they are duplicate copies).

### Demo accounts

Seeded by `DatabaseSeeder` / `BookSeeder`:

| Email | Password | Role |
| --- | --- | --- |
| `test@example.com` | `password` | `super admin` |
| + 2 factory users | `password` | `guest` |

Re-seed at any time with `php artisan migrate:fresh --seed`.

### Tests & code style

```bash
php artisan test --compact          # full suite
php artisan test --compact tests/Feature/BookTest.php   # single file
php artisan test --compact --filter=methodName           # single test
vendor/bin/pint                     # format PHP (run before committing)
```

## API Endpoints

Base URL: `/api`. Authenticated endpoints require the header `Authorization: Bearer <access_token>`. All responses are JSON.

| Method | Endpoint | Name / purpose | Auth | Parameters |
| --- | --- | --- | --- | --- |
| POST | `/api/register` | Create account, returns token | Public | Body: `name`, `email`, `password`, `password_confirmation` |
| POST | `/api/login` | Authenticate, returns token | Public | Body: `email`, `password` |
| GET | `/api/user` | Current authenticated user | Bearer token | — |

> These routes have no Laravel route names; use the paths above. The same endpoints are documented interactively at `/docs/api`.

### POST `/api/register`

Request body:

```json
{
    "name": "Jane Doe",
    "email": "jane@example.com",
    "password": "rahasia123",
    "password_confirmation": "rahasia123"
}
```

Validation: `name` required string max 255; `email` required, valid, unique in `users`; `password` required + confirmed.

Success — `201 Created`:

```json
{
    "token_type": "Bearer",
    "access_token": "1|................................",
    "user": {
        "id": 4,
        "name": "Jane Doe",
        "email": "jane@example.com",
        "role": "guest"
    },
    "message": "berhasil register"
}
```

Validation failure — `400 Bad Request` (field-keyed error lists):

```json
{
    "email": ["The email has already been taken."]
}
```

### POST `/api/login`

Request body:

```json
{
    "email": "test@example.com",
    "password": "password"
}
```

Validation: `email` required + valid format; `password` required.

Success — `200 OK`:

```json
{
    "token_type": "Bearer",
    "access_token": "1|................................",
    "user": {
        "id": 1,
        "name": "Test User",
        "email": "test@example.com",
        "role": "super admin"
    },
    "message": "berhasil login"
}
```

Errors: `400` unknown email (`{"message": "Email User Salah"}`) or validation failure; `401` wrong password (`{"success": false, "message": "Password salah"}`).

### GET `/api/user`

Headers: `Authorization: Bearer <access_token>`, `Accept: application/json`.

Success — `200 OK`, the token's user object:

```json
{
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "role": "super admin"
}
```

Without (or with an invalid) token — `401 Unauthorized`: `{"message": "Unauthenticated."}`.

Example:

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'

curl http://localhost:8000/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|YOUR_TOKEN"
```

## Frontend Page Structure

Two Blade layouts, both loading the SB Admin 2 assets from `public/` (never Vite):

```
resources/views/
├── layouts/
│   ├── admin.blade.php        # authed panel shell
│   │   ├── <x-sidebar>        # brand, Dashboard, Books, Kategori (super admin only)
│   │   ├── <x-topbar>         # sidebar toggle + user dropdown (name, logout)
│   │   └── @yield('content')  # page body
│   │       ├── dashboard/index    # stat card + "Daftar Buku" card list
│   │       ├── books/index        # book table (5/page); Edit/Hapus for owners, Lihat otherwise
│   │       ├── books/create       # create form (multipart)
│   │       ├── books/edit         # edit form (multipart)
│   │       ├── books/show         # read-only detail (super admin on other users' books)
│   │       └── categories/        # index/create/edit — super admin only
│   └── public.blade.php       # guest shell
│       ├── navbar             # "Daftar Buku"; auth: Dashboard + Logout | else: Login + Daftar
│       └── @yield('content')
│           ├── katalog/index  # public grid, 9/page, latest first
│           └── katalog/show   # book detail (breadcrumb, description, content)
├── auth/                      # login.blade.php, register.blade.php
├── components/                # sidebar, topbar, stat-cards, latest-books, ...
└── books/  katalog/  dashboard/
```

### Pages

| URL | Route name | View | Access | Description |
| --- | --- | --- | --- | --- |
| `GET /login` | `login` | `auth/login` | guest | Login form (email + password) |
| `POST /login` | — | — | guest | Authenticate; redirects to `admin` |
| `GET /register` | `register` | `auth/register` | guest | Registration form |
| `POST /register` | — | — | guest | Create account; redirects to `admin` |
| `POST /logout` | `logout` | — | auth | Logout; redirects to `login` |
| `GET /` | `katalog.index` | `katalog/index` | public | Catalog grid, latest first, 9 per page, `?page=N` |
| `GET /{book}` | `katalog.show` | `katalog/show` | public | Book detail by ID; unknown ID → 404 |
| `GET /dashboard` | `admin` | `dashboard/index` | auth | Stat card (total user books) + single card list of books (cover, title, category) |
| `GET /books` | `books.index` | `books/index` | auth | Own books table, 5 per page (super admin: **all** books) |
| `GET /books/create` | `books.create` | `books/create` | auth | Create form |
| `POST /books` | `books.store` | — | auth | Store (multipart, cover required) |
| `GET /books/{book}` | `books.show` | — | auth | Owner → redirects to edit; super admin on someone else's book → read-only detail page |
| `GET /books/{book}/edit` | `books.edit` | `books/edit` | auth | Edit form (cover optional on update) |
| `PUT\|PATCH /books/{book}` | `books.update` | — | auth | Update (multipart) |
| `DELETE /books/{book}` | `books.destroy` | — | auth | Delete book + its cover file |
| `GET /categories` | `categories.index` | `categories/index` | super admin | Category table with book counts, 10 per page |
| `GET /categories/create` | `categories.create` | `categories/create` | super admin | Create form |
| `POST /categories` | `categories.store` | — | super admin | Store (validated `name`) |
| `GET /categories/{category}` | `categories.show` | — | super admin | Redirects to the edit form |
| `GET /categories/{category}/edit` | `categories.edit` | `categories/edit` | super admin | Edit form |
| `PUT\|PATCH /categories/{category}` | `categories.update` | — | super admin | Update (`name`) |
| `DELETE /categories/{category}` | `categories.destroy` | — | super admin | Delete — blocked with an error while books still use the category |

All `auth` rows are guarded by `auth:sanctum`; guests are redirected to `login`. For regular users, opening another user's book (`show`/`edit`/`update`/`destroy`) returns `403` (see `BookPolicy`); a super admin may open `show` (read-only) but still gets `403` on `edit`/`update`/`destroy`. `categories.*` requires the `super admin` role — anyone else gets `403`. Book form submissions are `multipart/form-data` because of the cover upload (`image`, jpeg/jpg/png/gif/webp, max 2048 KB).

## Features

**Roles & permissions**
- Two roles stored on `users.role`: `guest` (default for registrations) and `super admin` (seeded onto `test@example.com`).
- Super admin sees **every** book — the Books table lists all users' books and `books.show` opens a read-only detail page — but can only edit/delete their own books.
- Super admin alone can manage categories (list/create/rename/delete); the Kategori sidebar menu only renders for that role, and everyone else gets `403`.
- The role is never accepted from request input (not mass-assignable).

**Public**
- Browse the book catalog without an account (grid, latest first, 9 per page).
- Book detail page with description, content, author, category, and cover.
- Register and log in (session), or use the JSON API with Bearer tokens.

**Admin (authenticated)**
- Dashboard: total-books widget and one card listing your books (cover, title, category), latest first, with an empty state when you have none.
- Full book CRUD limited to your own books — editing or deleting another user's book returns `403`, super admin included.
- Books list shows Edit/Hapus for your own rows and a Lihat (read-only) button for other users' rows (super admin).
- Cover image upload with validation, stored on the public disk; replaced/removed on update/delete.
- Category select on the book form; categories themselves are managed from the Kategori menu (super admin) and can't be deleted while books still reference them.
- Validation with friendly errors (title/description/content min 20 chars, cover max 2 MB, category name 3–255 chars and unique).
- Flash success messages in Indonesian (`... berhasil ...`), consistent with the rest of the UI.

**API**
- `POST /api/register` and `POST /api/login` return Sanctum personal access tokens.
- `GET /api/user` returns the token's user (401 without a valid token).
- Interactive docs at `/docs/api` (Scramble).

**Quality of life**
- Seeded demo data (8 categories, 3 users, 9 books) via `php artisan migrate:fresh --seed`.
- Pest feature suite (`php artisan test --compact`) and Pint code style (`vendor/bin/pint`).
