# JournalGrid — Double-Entry Accounting & General Ledger System

[![Laravel Framework](https://img.shields.io/badge/Laravel-v13-red.svg)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-v4-purple.svg)](https://livewire.laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3%20%7C%208.5-blue.svg)](https://php.net)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)

**JournalGrid** is a double-entry journal voucher and general ledger accounting application built with Laravel 13 and Livewire 4. It showcases [`unnathianalytics/laragrid`](https://github.com/unnathianalytics/laragrid) — an Excel-style Livewire datagrid — tailored for accounting workflows requiring high-speed data entry, strict balance enforcement, and immutable audit trails.

---

## Key Features

- **Excel-Style Line Entry Grid (`VoucherEntry`)**: Interactive editable datagrid powered by LaraGrid with auto-appending rows and real-time double-entry balance guards ($\Sigma\text{Debit} = \Sigma\text{Credit}$).
- **Readonly Server-Side Register (`VoucherList`)**: 5-item paginated voucher register with global search, status/type filters, operator saved views, and CSV/XLSX export options.
- **Trial Balance Report (`TrialBalance`)**: Display-mode computed grid & 5-item paginated summary aggregating net balances across postable accounts.
- **General Ledger (`GeneralLedger`)**: Per-account transaction inspection with 5-item pagination and automatic running balances.
- **Strict Segregation of Duties**: Role-based access control (RBAC) separating voucher creation (Accountant) from posting/reversal authorization (Approver).
- **Immutable Accounting Integrity**: Posted vouchers are locked against edits; adjustments are handled via mirrored append-only reversal entries (`reversal_of_id`).
- **Minor-Unit Monetary Correctness**: All monetary values are stored as `bigInteger` minor units (kobo/cents) and represented at domain boundaries using `Brick\Money\Money` value objects.

---

## Technology Stack

- **Framework:** Laravel 13 & Livewire 4
- **Datagrid:** `unnathianalytics/laragrid`
- **Authentication & RBAC:** `laravel/fortify` & `spatie/laravel-permission`
- **Audit Trails:** `spatie/laravel-activitylog`
- **Money Handling:** `brick/money`
- **Database:** MySQL 8.0 (via Docker)
- **Frontend & Styling:** TailwindCSS v4 & Vite
- **Testing:** PHPUnit / Pest

---

## Developer Setup Guide

Follow these steps to get JournalGrid running on your local machine:

### 1. Prerequisites
Ensure you have the following installed locally:
- **PHP** 8.3 or 8.5 with `pdo_mysql` extension
- **Composer**
- **Node.js** (v18+) & **npm**
- **Docker** & **Docker Compose**

### 2. Clone Repository & Install Dependencies
```bash
git clone https://github.com/your-username/JournalGrid.git
cd JournalGrid

# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration
Copy the example environment file and generate the application key:
```bash
cp .env.example .env
php artisan key:generate
```

Ensure your `.env` contains the MySQL configuration:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=journalgrid
DB_USERNAME=journalgrid
DB_PASSWORD=secret
```

### 4. Start MySQL Database via Docker
Start the MySQL 8.0 container defined in `docker-compose.yml`:
```bash
docker-compose up -d
```

### 5. Run Database Migrations & Seeders
Run migrations and populate the database with default roles, permissions, chart of accounts, and demo users:
```bash
php artisan migrate:fresh --seed
```

### 6. Build Frontend Assets
Build Vite assets for production or start the Vite dev server:
```bash
npm run build
```

### 7. Launch Application
Start the Laravel local development server:
```bash
php artisan serve --port=8080
```

Open your browser and visit: **[http://127.0.0.1:8080](http://127.0.0.1:8080)**

---

## Demo Login Credentials

The application comes pre-seeded with four test user accounts representing different accounting roles:

| Role | Email | Password | Allowed Capabilities |
| :--- | :--- | :--- | :--- |
| **Accountant** | `accountant@journalgrid.com` | `password` | Create draft vouchers, view registers & reports |
| **Approver** | `approver@journalgrid.com` | `password` | Post draft vouchers, reverse posted vouchers |
| **Auditor** | `auditor@journalgrid.com` | `password` | Read-only inspection of registers and GL reports |
| **Admin** | `admin@journalgrid.com` | `password` | Full system administration and chart of accounts management |

*Tip: The login page includes quick-fill buttons for instant single-click demo logins.*

---

## Running Tests & Code Quality

### Test Suite
JournalGrid includes full unit and feature tests covering balance enforcement, immutability, segregation of duties, and Livewire LaraGrid interactions.

Tests execute on an isolated `journalgrid_test` database, so running tests will never reset or alter your local development database.

Run the test suite via PHPUnit:
```bash
vendor/bin/phpunit
# or: php artisan test
```

### Code Formatting
Ensure all code matches project style conventions using Laravel Pint:
```bash
vendor/bin/pint --format agent
```

---

## Architectural Highlights (SOLID)

- **Invokable Action Classes (`app/Actions/Vouchers/`)**:
  - `CreateJournalVoucherAction`: Handles draft creation and server-side balance validation.
  - `PostJournalVoucherAction`: Authorizes and locks vouchers into immutable posted state.
  - `ReverseJournalVoucherAction`: Generates mirrored reversal vouchers.
- **Single Source of Authorization (`app/Policies/JournalVoucherPolicy.php`)**: Policy methods govern access across LaraGrid RPCs, Livewire components, and controllers alike.
- **Contract Abstractions (`app/Contracts/VoucherNumberGenerator.php`)**: Swappable voucher numbering service injected into Actions via Laravel's container.

---

## License

JournalGrid is open-source software owned by Chijindu Nwokeohuru and licensed under the [MIT License](LICENSE).
