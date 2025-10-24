# Installation & Setup Guide

Complete guide to install and configure the Laravel CRUD Generator package.

## Requirements ✅

- PHP >= 8.1
- Laravel >= 10.0
- Composer

## Installation Methods

### Method 1: Via Composer (Recommended)

Once published to Packagist:

```bash
composer require sndpbag/crud-generator
```

### Method 2: Local Development

For testing or contributing:

1. **Clone the repository**
```bash
git clone https://github.com/sndpbag/crud-generator.git
cd crud-generator
```

2. **In your Laravel project's composer.json**, add:
```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../crud-generator"
        }
    ],
    "require": {
        "sndpbag/crud-generator": "*"
    }
}
```

3. **Install the package**
```bash
composer update sndpbag/crud-generator
```

## Post-Installation Steps

### 1. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=crud-generator-config
```

This creates `config/crud-generator.php` where you can customize:
- Default template (Bootstrap/Tailwind)
- Storage paths
- Namespaces
- Validation rules
- Success messages

### 2. Publish Stubs (Optional)

If you want to customize the generated code templates:

```bash
php artisan vendor:publish --tag=crud-generator-stubs
```

This creates `stubs/crud-generator/` directory with all template files.

### 3. Verify Installation

```bash
php artisan list | grep crud
```

You should see:
```
make:crud        Generate a complete CRUD with all necessary files
crud:delete      Delete all files generated for a CRUD
```

## Configuration

### Default Configuration

The package works out of the box with these defaults:

```php
// config/crud-generator.php
return [
    'template' => 'bootstrap',
    'storage_path' => 'public/uploads',
    'namespace' => [
        'model' => 'App\\Models',
        'controller' => 'App\\Http\\Controllers',
        'request' => 'App\\Http\\Requests',
    ],
    'pagination' => 10,
    'alert_library' => 'sweetalert2',
];
```

### Customizing Configuration

Edit `config/crud-generator.php`:

#### Change Template
```php
'template' => 'tailwind', // or 'bootstrap'
```

#### Change Storage Path
```php
'storage_path' => 'public/my-uploads',
```

#### Change Pagination
```php
'pagination' => 15,
```

#### Change Alert Library
```php
'alert_library' => 'toastr', // or 'sweetalert2' or 'native'
```

## Laravel Setup Requirements

### 1. Database Configuration

Ensure your database is configured in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 2. Authentication Setup (For --auth flag)

If you plan to use `--auth` flag, setup Laravel authentication:

```bash
composer require laravel/ui
php artisan ui bootstrap --auth
npm install && npm run dev
php artisan migrate
```

Or use Laravel Breeze/Jetstream.

### 3. Storage Link (For File Uploads)

Create symbolic link for public storage:

```bash
php artisan storage:link
```

### 4. File Permissions

Ensure these directories are writable:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Frontend Setup

### For Bootstrap Templates

If using Bootstrap templates, ensure Bootstrap is included in your layout:

```html
<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    @yield('content')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
```

### For Tailwind Templates

If using Tailwind, ensure it's configured:

```bash
npm install -D tailwindcss
npx tailwindcss init
```

Update `tailwind.config.js`:
```js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

## First CRUD Generation

Test your installation:

```bash
php artisan make:crud Product --fields="name:string,price:integer,description:text"
```

Run migration:
```bash
php artisan migrate
```

Visit:
```
http://localhost/products
```

## Troubleshooting

### Issue: Command not found

**Solution:**
```bash
composer dump-autoload
php artisan clear-compiled
php artisan cache:clear
```

### Issue: Class not found

**Solution:**
```bash
composer dump-autoload
php artisan config:clear
```

### Issue: Routes not working

**Solution:**
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Views not found

**Solution:**
```bash
php artisan view:clear
```

### Issue: File upload not working

**Solution:**
```bash
php artisan storage:link
chmod -R 775 storage
```

### Issue: Permission denied

**Solution:**
```bash
sudo chown -R $USER:www-data storage
sudo chown -R $USER:www-data bootstrap/cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Updating the Package

### Via Composer
```bash
composer update sndpbag/crud-generator
```

### Clear all caches after update
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Uninstalling

### Remove Package
```bash
composer remove sndpbag/crud-generator
```

### Clean up (optional)
```bash
rm config/crud-generator.php
rm -rf stubs/crud-generator
```

## Docker Setup

If using Docker/Laravel Sail:

```bash
# Install package
sail composer require sndpbag/crud-generator

# Run commands
sail artisan make:crud Product --fields="name:string,price:integer"

# Migrate
sail artisan migrate
```

## Production Deployment

### Before Deploying

1. **Remove dev dependencies**
```bash
composer install --no-dev --optimize-autoloader
```

2. **Cache everything**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Set permissions**
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### Environment Variables

In production `.env`:
```env
APP_ENV=production
APP_DEBUG=false
CRUD_TEMPLATE=bootstrap
CRUD_STORAGE_PATH=public/uploads
```

## IDE Integration

### VS Code

Install PHP Intelephense extension for better autocomplete:
```json
{
    "intelephense.stubs": [
        "laravel"
    ]
}
```

### PHPStorm

PHPStorm should automatically detect the package.

## Testing Installation

Run this test command:

```bash
php artisan make:crud TestModel \
  --fields="name:string,email:email,age:integer:nullable" \
  --tests

php artisan migrate

php artisan test
```

If all tests pass, you're good to go! ✅

## Next Steps

1. Read the [Quick Start Guide](QUICK_START.md)
2. Check out [Examples](README.md#examples)
3. Explore [Advanced Features](README.md#advanced-features)
4. Join our community (coming soon)

## Support

- 📧 Email: your.email@example.com
- 🐛 Issues: [GitHub Issues](https://github.com/sndpbag/crud-generator/issues)
- 📖 Docs: [Documentation](https://github.com/sndpbag/crud-generator)

---

**Happy Generating! 🚀**
