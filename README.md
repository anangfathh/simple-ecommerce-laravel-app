# Simple E-Commerce Laravel Application

A full-stack e-commerce platform built with Laravel (backend) and Vue.js with Shadcn UI (frontend).

## Features

### Backend (Laravel)
- **User Authentication**: Registration, login, logout with Laravel Sanctum
- **Authorization**: Role-based access control (admin/user)
- **Product CRUD**: Full create, read, update, delete operations
- **Category Management**: Product categories with relationships
- **Image Upload**: Product image upload and storage
- **Data Validation**: Request validation with proper error responses
- **RESTful API**: Clean API design following REST principles
- **Swagger Documentation**: Auto-generated API documentation
- **Unit Tests**: Comprehensive test coverage with PHPUnit

### Frontend (Vue.js + Shadcn UI)
- **Modern UI**: Beautiful, responsive design with Tailwind CSS
- **Product Browsing**: Grid/list view, search, filtering, pagination
- **Category Filtering**: Filter products by category or price range
- **Authentication**: Login/register with protected routes
- **Admin Dashboard**: Product and category management
- **Dark Mode Ready**: CSS variables for theme support
- **Micro-animations**: Smooth transitions and hover effects

## Tech Stack

### Backend
- Laravel 11
- Laravel Sanctum (API Authentication)
- L5-Swagger (API Documentation)
- PostgreSQL
- PHPUnit

### Frontend
- Vue.js 3 (Composition API)
- TypeScript
- Tailwind CSS
- Pinia (State Management)
- Vue Router
- Axios (HTTP Client)
- Lucide Icons

---

## 🚀 Installation

Pilih salah satu metode instalasi di bawah ini:

- [Opsi 1: Instalasi dengan Docker](#opsi-1-instalasi-dengan-docker-recommended) (Recommended)
- [Opsi 2: Instalasi di Lokal](#opsi-2-instalasi-di-lokal)

---

## Opsi 1: Instalasi dengan Docker (Recommended)

Metode ini paling mudah karena semua dependencies sudah dikonfigurasi di dalam container.

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- Docker Compose (sudah termasuk di Docker Desktop)

### Langkah-langkah

#### 1. Clone Repository

```bash
git clone <repository-url>
cd simple-ecommerce-laravel-app
```

#### 2. Buat File Environment

Buat file `.env` di root folder (untuk Docker Compose):

```bash
# Windows
copy .env.docker .env

# Linux/Mac
cp .env.docker .env
```

Pastikan file `backend/.env` memiliki konfigurasi database untuk Docker:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=ecommerce
DB_USERNAME=postgres
DB_PASSWORD=secret
```

> ⚠️ **PENTING**: Untuk Docker, `DB_HOST` harus `db` (nama service) dan `DB_PORT` harus `5432` (port internal container).

#### 3. Jalankan Docker Compose

```bash
docker-compose up -d
```

Tunggu hingga semua container berjalan. Untuk melihat status:

```bash
docker-compose ps
```

#### 4. Jalankan Migration & Seeding

```bash
docker-compose exec backend php artisan migrate:fresh --seed
```

#### 5. Generate Swagger Documentation

```bash
docker-compose exec backend php artisan l5-swagger:generate
```

#### 6. Akses Aplikasi

| Service | URL |
|---------|-----|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8000 |
| Swagger Docs | http://localhost:8000/api/documentation |

#### Perintah Docker Berguna

```bash
# Lihat logs
docker-compose logs -f

# Lihat logs service tertentu
docker-compose logs -f backend

# Masuk ke container backend
docker-compose exec backend sh

# Jalankan artisan command
docker-compose exec backend php artisan <command>

# Jalankan tests
docker-compose exec backend php artisan test

# Stop semua container
docker-compose down

# Stop dan hapus volume (reset database)
docker-compose down -v
```

---

## Opsi 2: Instalasi di Lokal

Gunakan metode ini jika Anda ingin development tanpa Docker.

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- npm
- PostgreSQL (atau MySQL/SQLite)

### Langkah-langkah

#### 1. Clone Repository

```bash
git clone <repository-url>
cd simple-ecommerce-laravel-app
```

#### 2. Setup Backend

```bash
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Edit file .env dan sesuaikan konfigurasi database
```

Edit file `backend/.env` sesuai database lokal Anda:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ecommerce
DB_USERNAME=postgres
DB_PASSWORD=your_password
```

> 💡 **Tips**: Jika menggunakan Docker untuk PostgreSQL saja, gunakan port `5433` (lihat docker-compose.yml).

```bash
# Generate app key
php artisan key:generate

# Run migrations and seed data
php artisan migrate:fresh --seed

# Create storage link
php artisan storage:link

# Generate Swagger docs
php artisan l5-swagger:generate

# Start the server
php artisan serve
```

Backend akan berjalan di `http://localhost:8000`

#### 3. Setup Frontend

Buka terminal baru:

```bash
cd frontend

# Install dependencies
npm install

# Start dev server
npm run dev
```

Frontend akan berjalan di `http://localhost:5173`

#### 4. Akses Aplikasi

| Service | URL |
|---------|-----|
| Frontend | http://localhost:5173 |
| Backend API | http://localhost:8000 |
| Swagger Docs | http://localhost:8000/api/documentation |

---

## 🔐 Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| User | user@example.com | password |

---

## 📚 API Documentation

Swagger documentation tersedia di: `http://localhost:8000/api/documentation`

### API Endpoints

#### Authentication
- `POST /api/register` - Register a new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user (requires auth)
- `GET /api/user` - Get current user (requires auth)

#### Products
- `GET /api/products` - List all products (with filters)
- `GET /api/products/{id}` - Get single product
- `POST /api/products` - Create product (admin only)
- `PUT /api/products/{id}` - Update product (admin only)
- `DELETE /api/products/{id}` - Delete product (admin only)

#### Categories
- `GET /api/categories` - List all categories
- `GET /api/categories/{id}` - Get single category
- `POST /api/categories` - Create category (admin only)
- `PUT /api/categories/{id}` - Update category (admin only)
- `DELETE /api/categories/{id}` - Delete category (admin only)

---

## 🧪 Running Tests

```bash
# Jika menggunakan Docker
docker-compose exec backend php artisan test

# Jika instalasi lokal
cd backend
php artisan test
```

---

## 📁 Project Structure

```
simple-ecommerce-laravel-app/
├── backend/                    # Laravel API
│   ├── app/
│   │   ├── Http/Controllers/  # API Controllers
│   │   ├── Models/            # Eloquent Models
│   │   └── Http/Middleware/   # Custom Middleware
│   ├── database/
│   │   ├── factories/         # Model Factories
│   │   ├── migrations/        # Database Migrations
│   │   └── seeders/           # Database Seeders
│   ├── routes/
│   │   └── api.php            # API Routes
│   └── tests/
│       └── Feature/           # Feature Tests
│
├── frontend/                  # Vue.js SPA
│   ├── src/
│   │   ├── components/ui/     # Shadcn UI Components
│   │   ├── views/             # Page Views
│   │   ├── stores/            # Pinia Stores
│   │   ├── router/            # Vue Router
│   │   └── services/          # API Services
│   └── index.html
│
├── docker-compose.yml         # Docker Compose configuration
└── README.md
```

---

## ⚠️ Troubleshooting

### Error: Connection refused to database

**Jika menggunakan Docker:**
- Pastikan `DB_HOST=db` dan `DB_PORT=5432` di `backend/.env`
- Jalankan `docker-compose exec backend php artisan config:clear`

**Jika instalasi lokal:**
- Pastikan `DB_HOST=127.0.0.1` dan `DB_PORT` sesuai dengan PostgreSQL Anda
- Pastikan PostgreSQL service berjalan

### Port conflict dengan PostgreSQL lokal

Jika Anda memiliki PostgreSQL lokal di port 5432, Docker menggunakan port 5433 untuk menghindari konflik:
- Akses database dari Windows/host: `127.0.0.1:5433`
- Akses dari dalam Docker container: `db:5432`

### Clear cache Laravel

```bash
# Docker
docker-compose exec backend php artisan config:clear
docker-compose exec backend php artisan cache:clear
docker-compose exec backend php artisan route:clear

# Lokal
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

---

## 📄 License

MIT
