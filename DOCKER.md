# Docker Setup for RimHotel Laravel Application

## Services Included

- **App (PHP 8.2 + Nginx)**: Laravel application running on port 8000
- **PostgreSQL 16**: Database server on port 5432
- **Redis**: Cache and queue server on port 6379
- **pgAdmin**: Database management tool on port 8080

## Quick Start

### 1. Build and Start Containers

```bash
docker-compose up -d --build
```

### 2. Install Dependencies

```bash
docker-compose exec app composer install
```

### 3. Set Up Environment

Copy the Docker environment configuration:

```bash
# On Windows
copy .env.docker .env

# On Linux/Mac
cp .env.docker .env
```

Or manually update your `.env` file with these values:

```env
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=rimhotel_db
DB_USERNAME=rimhotel_user
DB_PASSWORD=rimhotel_password

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### 4. Generate Application Key

```bash
docker-compose exec app php artisan key:generate
```

### 5. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### 6. Set Permissions

```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

## Access Points

- **Application**: http://localhost:8000
- **pgAdmin**: http://localhost:8080
  - Email: `admin@rimhotel.com`
  - Password: `admin`
  - After login, add server with:
    - Host: `postgres`
    - Port: `5432`
    - Database: `rimhotel_db`
    - Username: `rimhotel_user`
    - Password: `rimhotel_password`

## Useful Commands

### View Logs

```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
```

### Execute Artisan Commands

```bash
docker-compose exec app php artisan [command]
```

### Access Container Shell

```bash
docker-compose exec app bash
```

### Stop Containers

```bash
docker-compose down
```

### Stop and Remove Volumes

```bash
docker-compose down -v
```

### Rebuild Containers

```bash
docker-compose up -d --build --force-recreate
```

## Database Credentials

### PostgreSQL
- Host: `localhost` (or `postgres` from within containers)
- Port: `5432`
- Database: `rimhotel_db`
- Username: `rimhotel_user`
- Password: `rimhotel_password`

## Troubleshooting

### Permission Issues

If you encounter permission issues:

```bash
docker-compose exec app chown -R www-data:www-data /var/www
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Clear Cache

```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

### Rebuild from Scratch

```bash
docker-compose down -v
docker-compose up -d --build
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```
