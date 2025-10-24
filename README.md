"# CRUD Generator" 
# 🚀 Laravel CRUD Generator

<p align="center">
  <img src="https://img.shields.io/packagist/v/sndpbag/crud-generator.svg" alt="Latest Version">
  <img src="https://img.shields.io/packagist/dt/sndpbag/crud-generator.svg" alt="Total Downloads">
  <img src="https://img.shields.io/packagist/l/sndpbag/crud-generator.svg" alt="License">
  <img src="https://img.shields.io/github/stars/sndpbag/crud-generator.svg" alt="Stars">
</p>

<p align="center">
  <strong>Generate complete Laravel CRUD in seconds with a single command!</strong>
</p>

<p align="center">
  <a href="#-features">Features</a> •
  <a href="#-installation">Installation</a> •
  <a href="#-quick-start">Quick Start</a> •
  <a href="#-documentation">Documentation</a> •
  <a href="#-contributing">Contributing</a>
</p>

---

## 🎯 Why This Package?

Stop wasting time writing repetitive CRUD code! This package generates:

- ✅ Model with relationships
- ✅ Migration with all field types
- ✅ Controller with complete CRUD logic
- ✅ Form Request validation classes
- ✅ Beautiful responsive views (Bootstrap/Tailwind)
- ✅ Automatic route registration
- ✅ Feature tests
- ✅ And much more...

**All in just 5 seconds!** ⚡

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🎨 Smart Generation
- **26 Advanced Features**
- Smart field type detection
- Enum fields with dropdowns
- File & image uploads
- Relationship scaffolding

</td>
<td width="50%">

### 🔧 Developer Friendly
- Customizable stubs
- Bootstrap & Tailwind support
- API mode (JSON responses)
- Authentication support

</td>
</tr>
<tr>
<td>

### 🧪 Testing Ready
- PHPUnit test generation
- Pest test support
- Complete test coverage
- TDD workflow

</td>
<td>

### 📦 Production Ready
- Namespaced generation
- Soft deletes
- Search & sorting
- Pagination

</td>
</tr>
</table>

---

## 📦 Installation

```bash
composer require sndpbag/crud-generator
```

That's it! No configuration needed. Start generating CRUDs immediately.

---

## 🚀 Quick Start

### Basic Example

```bash
php artisan make:crud Product --fields="name:string,price:integer,description:text"
```

**Generated files in 5 seconds:**
- ✅ `app/Models/Product.php`
- ✅ `database/migrations/..._create_products_table.php`
- ✅ `app/Http/Controllers/ProductController.php`
- ✅ `app/Http/Requests/StoreProductRequest.php`
- ✅ `app/Http/Requests/UpdateProductRequest.php`
- ✅ `resources/views/products/*.blade.php`
- ✅ Routes in `routes/web.php`

Run migration and you're done:
```bash
php artisan migrate
```

Visit: `http://localhost/products` 🎉

---

## 💡 Advanced Usage

### E-commerce Product CRUD

```bash
php artisan make:crud Product \
  --fields="name:string:unique,slug:string:unique,price:decimal:default(0),stock:integer,image:image:nullable,status:enum(active,inactive):default('active'),is_featured:boolean:default(0)" \
  --belongsTo=Category \
  --belongsTo=Brand \
  --softdelete \
  --auth \
  --tests
```

### Admin Panel

```bash
php artisan make:crud Admin/Post \
  --fields="title:string:unique,content:text,featured_image:image:nullable" \
  --belongsTo=User \
  --belongsTo=Category \
  --auth
```

### API Endpoints

```bash
php artisan make:crud Product --fields="name:string,price:integer" --api
```

---

## 🎨 Field Types

| Type | HTML | Example |
|------|------|---------|
| string | `<input type="text">` | `name:string` |
| text | `<textarea>` | `description:text` |
| integer | `<input type="number">` | `age:integer` |
| decimal | `<input type="number">` | `price:decimal` |
| boolean | `<input type="checkbox">` | `is_active:boolean` |
| date | `<input type="date">` | `birth_date:date` |
| datetime | `<input type="datetime-local">` | `published_at:datetime` |
| email | `<input type="email">` | `email:email` |
| file | `<input type="file">` | `document:file` |
| image | `<input type="file" accept="image/*">` | `photo:image` |
| enum | `<select>` | `status:enum(active,inactive)` |

---

## 🔧 Modifiers

```bash
# Nullable field
--fields="description:text:nullable"

# Unique constraint
--fields="email:email:unique"

# Default value
--fields="status:enum(active,inactive):default('active')"

# Combine modifiers
--fields="slug:string:unique:nullable"
```

---

## 🔗 Relationships

```bash
# BelongsTo (generates dropdown in forms)
--belongsTo=User --belongsTo=Category

# HasMany
--hasMany=Comment --hasMany=Review
```

---

## 🎯 Command Flags

| Flag | Description | Example |
|------|-------------|---------|
| `--fields` | Define fields | `--fields="name:string,price:integer"` |
| `--belongsTo` | Add belongsTo relationship | `--belongsTo=User` |
| `--hasMany` | Add hasMany relationship | `--hasMany=Comment` |
| `--softdelete` | Enable soft deletes | `--softdelete` |
| `--auth` | Add auth middleware | `--auth` |
| `--api` | Generate API instead of web | `--api` |
| `--tests` | Generate PHPUnit tests | `--tests` |
| `--pest` | Generate Pest tests | `--pest` |

---

## 🗑️ Rollback

Delete all generated files:

```bash
php artisan crud:delete Product
```

---

## 📚 Documentation

- [📖 Complete Documentation](README.md)
- [⚡ Quick Start Guide](QUICK_START.md)
- [🔧 Installation Guide](INSTALLATION.md)
- [💡 Real-World Examples](EXAMPLES.md)
- [🤝 Contributing Guidelines](CONTRIBUTING.md)

---

## 🎥 Video Tutorial

Coming soon! Subscribe to our YouTube channel.

---

## 📊 Comparison

| Feature | Manual Coding | This Package |
|---------|--------------|--------------|
| Time Required | ~2 hours | 5 seconds |
| Model | ✋ Manual | ✅ Auto |
| Migration | ✋ Manual | ✅ Auto |
| Controller | ✋ Manual | ✅ Auto |
| Validation | ✋ Manual | ✅ Auto |
| Views | ✋ Manual | ✅ Auto |
| Routes | ✋ Manual | ✅ Auto |
| Tests | ✋ Manual | ✅ Auto |
| File Uploads | ✋ Manual | ✅ Auto |
| Relationships | ✋ Manual | ✅ Auto |

---

## 🌟 Real-World Examples

### Blog System
```bash
php artisan make:crud Post --fields="title:string:unique,content:text,status:enum(draft,published)" --belongsTo=User --hasMany=Comment --softdelete --auth --tests
```

### Inventory Management
```bash
php artisan make:crud Product --fields="sku:string:unique,name:string,stock:integer,price:decimal" --belongsTo=Category --belongsTo=Supplier --auth
```

### Booking System
```bash
php artisan make:crud Appointment --fields="appointment_date:datetime,status:enum(pending,confirmed,cancelled)" --belongsTo=User --belongsTo=Service --auth
```

See more in [EXAMPLES.md](EXAMPLES.md)

---

## ⚙️ Configuration

Publish config file:

```bash
php artisan vendor:publish --tag=crud-generator-config
```

Customize in `config/crud-generator.php`:

```php
return [
    'template' => 'bootstrap', // or 'tailwind'
    'storage_path' => 'public/uploads',
    'pagination' => 10,
    'alert_library' => 'sweetalert2',
];
```

---

## 🎨 Customize Templates

Publish stubs:

```bash
php artisan vendor:publish --tag=crud-generator-stubs
```

Edit files in `stubs/crud-generator/` to customize generated code.

---

## 🧪 Testing

Run package tests:

```bash
composer test
```

Generate tests for your CRUD:

```bash
php artisan make:crud Product --fields="name:string" --tests
php artisan test
```

---

## 🤝 Contributing

We love contributions! Please read our [Contributing Guide](CONTRIBUTING.md).

### Contributors

<a href="https://github.com/sndpbag/crud-generator/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=sndpbag/crud-generator" />
</a>

---

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for all changes.

---

## 🐛 Issues

Found a bug? [Open an issue](https://github.com/sndpbag/crud-generator/issues)

---

## 💬 Discussions

Have questions? [Start a discussion](https://github.com/sndpbag/crud-generator/discussions)

---

## ⭐ Star History

[![Star History Chart](https://api.star-history.com/svg?repos=sndpbag/crud-generator&type=Date)](https://star-history.com/#sndpbag/crud-generator&Date)

---

## 📄 License

The MIT License (MIT). See [LICENSE](LICENSE) for details.

---

## 💝 Support

If you find this package helpful, please consider:

- ⭐ Starring the repository
- 🐛 Reporting bugs
- 💡 Suggesting features
- 📖 Improving documentation
- ☕ [Buy me a coffee](https://buymeacoffee.com/yourname)

---

## 🙏 Acknowledgments

- Laravel Community
- All Contributors
- Open Source Community

---

<p align="center">
  <strong>Made with ❤️ for the Laravel Community</strong>
</p>

<p align="center">
  <sub>Built by <a href="https://github.com/sndpbag">sandipan kr bag</a></sub>
</p>