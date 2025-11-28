# AMS Laravel React - Financial Accounting Management System

## Project Overview
A comprehensive financial accounting management web application with dual portals (Admin and Customer) using Laravel backend, React + Inertia.js frontend, and Prisma database ORM. Features expense tracking, revenue records, profit/loss calculation, reports, reconciliation, analytics, and tax information management.

**Repository:** `git@github.com:ahmed-raza012003/ams-laravel-react.git`
**Tech Stack:** Laravel + Inertia.js + React + Prisma + PostgreSQL/MySQL + Tailwind CSS
**Currency:** GBP (£)
**Primary Color:** #2ca48b

## Current Status - November 28, 2025

### ✅ Completed
- PHP 8.2 and Node.js environment setup with dependencies
- Laravel project with Breeze (Inertia + React) authentication
- PostgreSQL database with Prisma ORM schema - fully configured
- All React components: Modal (with title support), DataTable, StatCard, AdminLayout, CustomerLayout
- Admin CRUD pages: Customers, Items, Invoices, Estimates, Expenses, Users (modal-based operations)
- Customer portal with dashboard and corresponding pages
- Vite build system configured and working on port 5000
- Concurrent workflow: Laravel (backend) + Vite dev server (frontend) running successfully
- Database fully switchable between PostgreSQL/MySQL via environment variables
- All Tailwind CSS styling with GBP currency formatting

### ⏳ Next Steps
- Run database seeders (RoleSeeder, admin user creation)
- Push code to GitHub repository
- Additional testing and optimization

## Architecture Decisions

### Modal-Based CRUD
All Create, Read, Update, Delete operations are performed in modals, not separate pages. This provides a consistent UX across the application.

### Environment-Driven Configuration
- **Database:** Controlled entirely via `DB_CONNECTION` and `DATABASE_URL` environment variables
- **Theme:** Primary color, company name, logo all configurable via environment
- Switch between PostgreSQL and MySQL by updating env vars - no code changes needed

### Authentication & Authorization
- Separate Admin (`/admin/*`) and Customer (`/customer/*`) portal routes
- Role-based middleware (`CheckRole.php`) enforcing access control
- Breeze + Inertia.js for session management

### Prisma Configuration
- Using Prisma 7 with datasource URL from `DATABASE_URL` env var
- Prisma client integrated via `PrismaService.php` for use in Laravel controllers
- Schema supports PostgreSQL and MySQL

### Frontend
- Vite configured to bind to `0.0.0.0` for Replit compatibility
- React components follow naming conventions and reusability patterns
- Tailwind CSS with forms and icons plugins for styling

## Key Files
- `prisma/schema.prisma` - Database models and relationships
- `app/Services/PrismaService.php` - Prisma client wrapper for Laravel
- `routes/web.php` - Route definitions for both portals
- `app/Http/Middleware/CheckRole.php` - Role-based access control
- `resources/js/Layouts/AdminLayout.jsx` - Admin portal layout
- `resources/js/Layouts/CustomerLayout.jsx` - Customer portal layout
- `resources/js/Components/Modal.jsx` - Reusable modal component
- `vite.config.js` - Vite configuration for frontend development
- `package.json` - Node.js dependencies and scripts

## Environment Variables (Required)
```
DB_CONNECTION=pgsql/mysql
DATABASE_URL=postgresql://user:pass@host:port/db or mysql://...
SESSION_SECRET=generated-secret-key
```

## Workflow
**Command:** `npm run start`
- Runs concurrent processes: Laravel dev server (backend) + Vite dev server (frontend)
- Frontend accessible at port 5000
- All CRUD operations use modal-based forms

## Important Notes
- Git operations are restricted in the Replit environment - user must perform git operations through shell or local machine
- Database seeders not yet executed - admin user and roles need to be created
- All secrets stored securely in environment variables
