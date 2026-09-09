# Agence Voyage CMS

Agence Voyage CMS is a Laravel-based content management system for travel
agencies. It combines administrative content management with a visual website
builder, reusable site templates, themes, media management, and public travel
content pages.

## Features

- Visual page builder with reusable layout and content components
- Page creation, editing, previewing, publishing, duplication, and builder Undo/Redo
- Destination and travel offer management
- Media library with reusable uploaded assets
- Site templates and customizable themes
- Guided agency onboarding
- User, role, and permission administration
- Account profile and preference management
- Public destination, offer, and generated page routes
- Optional demonstration travel content
- Multi-agency data isolation

## Technology Stack

- PHP 8.2+
- Laravel 12
- MySQL or MariaDB
- Blade and Tailwind CSS
- Vite
- Node.js 20.19+ or 22.12+
- Composer 2
- PHPUnit

Dependency versions are locked through `composer.lock` and
`package-lock.json`.

## Requirements

Ensure the following tools are installed:

- PHP 8.2 or newer
- PHP extensions required by Laravel, including PDO MySQL, OpenSSL, Fileinfo,
  and XML
- MySQL 8+ or a compatible MariaDB release
- Composer 2
- Node.js 20.19+ or 22.12+
- npm
- Git

## Installation

Clone the repository and install the PHP and JavaScript dependencies:

```bash
git clone https://github.com/hassan-ettamry/agence-voyage-cms.git
cd agence-voyage-cms
composer install
npm ci
```

Create the local environment file and generate an application key:

```bash
cp .env.example .env
php artisan key:generate
```

On Windows PowerShell, use:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

## Database Configuration

Create an empty MySQL database:

```sql
CREATE DATABASE agence_voyage_cms
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

Update the database settings in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agence_voyage_cms
DB_USERNAME=root
DB_PASSWORD=
```

Run the migrations and seed the required application data:

```bash
php artisan migrate --seed
```

The seeders install:

- Application permissions
- Website builder components
- Theme presets
- Site templates

Create the public storage link:

```bash
php artisan storage:link
```

## Build and Run

Build the frontend assets:

```bash
npm run build
```

Start the Laravel development server:

```bash
php artisan serve
```

The application is available by default at:

```text
http://127.0.0.1:8000
```

Open `/register` to create the first agency and administrator account. The
guided onboarding flow will then configure the agency profile, site template,
and theme.

## Development

Run Laravel and Vite in separate terminals:

```bash
php artisan serve
```

```bash
npm run dev
```

The project also provides a combined Composer development command that starts
the application server, queue listener, log viewer, and Vite:

```bash
composer run dev
```

Because the queue connection uses the database driver by default, keep a queue
worker running when developing queued features:

```bash
php artisan queue:listen
```

## Demo Content

After registering an agency, administrators can optionally add demonstration
travel content from the dashboard. The operation creates sample destinations,
offers, and media for the active agency and is designed to be idempotent.

## Testing

Run the complete PHP test suite:

```bash
php artisan test
```

Or use the Composer test script:

```bash
composer test
```

Run the JavaScript regression tests:

```bash
npm test
```

An optional isolated Chromium scenario exercises the production builder
modules against a mocked render endpoint. It is a frontend integration check,
not a complete Laravel end-to-end test with authentication and a real
database. Make a local Playwright module available through
`PLAYWRIGHT_MODULE_PATH`, then run:

```bash
node tests/browser/builder-phase1.mjs
```

Validate the production frontend build:

```bash
npm run build
```

Check the migration state:

```bash
php artisan migrate:status
```

## Useful Commands

Clear cached application state:

```bash
php artisan optimize:clear
```

Rebuild the database for local testing:

```bash
php artisan migrate:fresh --seed
```

> This command deletes all existing database data and should only be used in a
> disposable development or testing environment.

## Project Structure

```text
app/                    Application services, models, policies, and controllers
database/migrations/    Database schema
database/seeders/       Permissions, components, themes, and templates
resources/js/builder/   Visual builder runtime
resources/views/        Blade views and builder components
public/images/          Version-controlled template preview assets
storage/app/public/     Runtime uploads, excluded from Git
tests/                  Unit and feature tests
tests/browser/          Optional isolated Chromium frontend checks
```

## Security

- Never commit `.env` or production credentials.
- Keep `APP_DEBUG=false` in production.
- Store runtime uploads outside version control.
- Review application permissions before deploying to a public environment.
