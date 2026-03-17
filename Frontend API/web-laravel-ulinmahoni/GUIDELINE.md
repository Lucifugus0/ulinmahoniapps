# Project Development Guidelines

## Project Overview
**CRM-NETWORK / Ulin Mahoni** is a Laravel-based web application for property booking and management system with integrated payment processing.

---

## Technology Stack

### Backend
- **PHP**: ^8.0.2 (Currently running 8.3.27)
- **Laravel Framework**: 9.52.4
- **Database**: MySQL 5.7+
- **Authentication**: Laravel Jetstream + Sanctum + Socialite
- **API Client**: Guzzle HTTP ^7.2

### Frontend
- **Build Tool**: Vite 3.2.11
- **CSS Framework**: TailwindCSS ^3.1.0
- **JavaScript Framework**: Alpine.js ^3.0.6
- **UI Component**: Livewire ^2.5
- **Charts**: Chart.js ^3.8.0 with Moment adapter
- **Date Picker**: Flatpickr ^4.6.13
- **Lightbox**: GLightbox ^3.3.1
- **HTTP Client**: Axios ^0.25

### Key Laravel Packages
- **laravel/jetstream**: ^2.9 - Authentication scaffolding
- **laravel/sanctum**: ^3.0 - API authentication
- **laravel/socialite**: ^5.21 - OAuth authentication
- **livewire/livewire**: ^2.5 - Dynamic UI components
- **yajra/laravel-datatables**: 9.0 & 10.0 - DataTables integration
- **realrashid/sweet-alert**: ^5.1 - Alert notifications
- **google/apiclient**: ^2.14 - Google API integration
- **fruitcake/laravel-cors**: ^3.0 - CORS handling

### Development Tools
- **Testing**: PHPUnit ^9.5.10
- **Debugging**: Laravel Ignition, Laravel Tinker
- **Code Quality**: PHP CodeSniffer (via Laravel standards)
- **Container**: Laravel Sail ^1.0.1 (Docker)

---

## System Requirements

### Required
- PHP >= 8.0.2 (Recommended: 8.1 or higher)
- Composer 2.x
- Node.js >= 16.x
- NPM or Yarn
- MySQL >= 5.7 or MariaDB >= 10.3
- Web Server (Apache/Nginx)

### Optional
- Redis (for caching and queues)
- Memcached (alternative caching)
- Supervisor (for queue workers)
- Docker (Laravel Sail)

---

## Installation & Setup

### 1. Clone Repository
```bash
git clone <repository-url>
cd web-laravel-ulinmahoni
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup
```bash
# Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# Seed database (if available)
php artisan db:seed
```

### 5. Build Assets
```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start Development Server
```bash
php artisan serve
```

---

## Development Workflow

### Code Standards

#### PHP/Laravel Standards
1. **PSR-12** coding standard
2. Use **type hints** for function parameters and return types
3. Follow **Laravel naming conventions**:
   - Controllers: `PascalCase` with `Controller` suffix
   - Models: Singular `PascalCase`
   - Tables: Plural `snake_case`
   - Migrations: `snake_case` with descriptive names
   - Routes: `kebab-case` for URLs

#### Example Controller Structure
```php
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
        ]);

        $booking = Booking::create($validated);

        return response()->json($booking, 201);
    }
}
```

### JavaScript/Frontend Standards
1. Use **ES6+** syntax
2. Follow **Alpine.js** conventions for reactive components
3. Use **Tailwind utility classes** instead of custom CSS
4. Keep JavaScript in `resources/js/`
5. Keep styles in `resources/css/`

### Git Workflow
1. **Branch Naming**:
   - Feature: `feature/feature-name`
   - Bug Fix: `fix/bug-description`
   - Hotfix: `hotfix/issue-description`

2. **Commit Messages**:
   ```
   type: subject (max 50 chars)

   body (optional, wrap at 72 chars)

   footer (optional)
   ```
   Types: `feat`, `fix`, `docs`, `style`, `refactor`, `test`, `chore`

3. **Pull Request Process**:
   - Create PR to `staging` branch
   - Ensure all tests pass
   - Request code review
   - Merge after approval

---

## Project Structure

```
web-laravel-ulinmahoni/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Request handlers
│   │   ├── Middleware/      # Request filters
│   │   └── Requests/        # Form request validation
│   ├── Models/              # Eloquent models
│   ├── Services/            # Business logic
│   └── Helpers/             # Helper functions
├── config/                  # Configuration files
├── database/
│   ├── migrations/          # Database migrations
│   ├── seeders/            # Database seeders
│   └── factories/          # Model factories
├── public/                  # Public assets
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
├── routes/
│   ├── web.php             # Web routes
│   ├── api.php             # API routes
│   └── channels.php        # Broadcast channels
├── storage/                # Logs, cache, uploads
├── tests/                  # Automated tests
├── .env.example            # Environment template
├── composer.json           # PHP dependencies
├── package.json            # Node dependencies
├── vite.config.js         # Vite configuration
└── tailwind.config.js     # Tailwind configuration
```

---

## Key Features & Modules

### 1. Property Management
- House/Property listings
- Property details and amenities
- Photo gallery management
- Availability calendar

### 2. Booking System
- Real-time availability check
- Date range selection
- Booking validation
- Promo code support

### 3. Payment Integration
- Multiple payment gateways
- Payment notifications (Doku)
- Invoice generation
- Transaction history

### 4. Customer Management
- Customer profiles
- Booking history
- Authentication via OAuth

### 5. Dashboard & Analytics
- Booking statistics
- Revenue charts (Chart.js)
- Data visualization
- DataTables for listings

---

## Database Guidelines

### Migration Best Practices
1. **Always** use migrations for schema changes
2. Name migrations descriptively
3. Use `up()` and `down()` methods
4. Never edit published migrations

Example:
```php
public function up()
{
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('property_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->date('check_in');
        $table->date('check_out');
        $table->integer('total_days');
        $table->decimal('total_price', 10, 2);
        $table->string('status')->default('pending');
        $table->timestamps();
    });
}
```

### Model Conventions
1. Use **fillable** or **guarded** properties
2. Define **relationships** clearly
3. Use **accessors/mutators** for data transformation
4. Implement **scopes** for reusable queries

---

## API Development

### RESTful API Standards
- Use **resource controllers**
- Return **JSON responses**
- Use **HTTP status codes** correctly:
  - 200: Success
  - 201: Created
  - 400: Bad Request
  - 401: Unauthorized
  - 404: Not Found
  - 500: Server Error

### API Authentication
```php
// Using Sanctum for API authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('bookings', BookingController::class);
});
```

---

## Security Best Practices

1. **Never commit** `.env` files
2. **Always validate** user input
3. **Use CSRF protection** for forms
4. **Sanitize** database queries (use Eloquent/Query Builder)
5. **Implement rate limiting** for APIs
6. **Keep dependencies updated** regularly
7. **Use HTTPS** in production
8. **Store sensitive data** encrypted

### CORS Configuration
Already configured via `fruitcake/laravel-cors`

---

## Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter BookingTest

# Run with coverage
php artisan test --coverage
```

### Writing Tests
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_booking()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/bookings', [
            'property_id' => 1,
            'check_in' => '2024-01-01',
            'check_out' => '2024-01-05',
        ]);

        $response->assertStatus(201);
    }
}
```

---

## Performance Optimization

### Caching
```php
// Cache configuration results
$properties = Cache::remember('properties', 3600, function () {
    return Property::all();
});
```

### Query Optimization
1. Use **eager loading** to prevent N+1 queries
2. **Index** frequently queried columns
3. Use **pagination** for large datasets
4. Implement **database caching**

### Asset Optimization
```bash
# Minify and bundle assets
npm run build

# Optimize images before uploading
```

---

## Deployment

### Pre-deployment Checklist
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure production database
- [ ] Build production assets: `npm run build`
- [ ] Optimize application: `php artisan optimize`
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Clear caches: `php artisan cache:clear`
- [ ] Set proper file permissions

### Production Server Setup
```bash
# Storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Setup queue worker (optional)
php artisan queue:work --daemon
```

---

## Troubleshooting

### Common Issues

#### 1. Composer Dependencies
```bash
composer update --no-scripts
composer dump-autoload
```

#### 2. Node Module Issues
```bash
rm -rf node_modules package-lock.json
npm install
```

#### 3. Cache Issues
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### 4. Permission Issues
```bash
sudo chown -R $USER:www-data storage
sudo chmod -R 775 storage
```

---

## Useful Commands

### Artisan Commands
```bash
# Generate controller
php artisan make:controller BookingController --resource

# Generate model with migration
php artisan make:model Booking -m

# Generate migration
php artisan make:migration create_bookings_table

# Generate seeder
php artisan make:seeder BookingSeeder

# Generate request validator
php artisan make:request StoreBookingRequest

# Database operations
php artisan migrate
php artisan migrate:fresh --seed
php artisan db:seed

# Clear all caches
php artisan optimize:clear

# Run scheduler (for cron jobs)
php artisan schedule:run
```

### NPM Commands
```bash
# Development with hot reload
npm run dev

# Production build
npm run build

# Watch for changes
npm run dev -- --watch
```

---

## Additional Resources

### Official Documentation
- [Laravel 9.x Documentation](https://laravel.com/docs/9.x)
- [Livewire Documentation](https://laravel-livewire.com/docs/2.x/quickstart)
- [Alpine.js Documentation](https://alpinejs.dev/start-here)
- [TailwindCSS Documentation](https://tailwindcss.com/docs)
- [Jetstream Documentation](https://jetstream.laravel.com/2.x/introduction.html)

### Community Resources
- [Laravel News](https://laravel-news.com/)
- [Laracasts](https://laracasts.com/)
- [Laravel Daily](https://laraveldaily.com/)

---

## Contact & Support

For project-specific questions or issues, please contact the development team or create an issue in the project repository.

---

**Last Updated**: January 2026
**Laravel Version**: 9.52.4
**PHP Version**: 8.0.2 - 8.3.x
