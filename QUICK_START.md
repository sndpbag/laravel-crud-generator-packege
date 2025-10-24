# Quick Start Guide

## Installation

```bash
composer require sndpbag/crud-generator
```

## Basic Examples

### 1. Simple CRUD (Product)

```bash
php artisan make:crud Product --fields="name:string,price:integer,description:text"
```

**Generated Files:**
- ✅ `app/Models/Product.php`
- ✅ `database/migrations/xxxx_create_products_table.php`
- ✅ `app/Http/Controllers/ProductController.php`
- ✅ `app/Http/Requests/StoreProductRequest.php`
- ✅ `app/Http/Requests/UpdateProductRequest.php`
- ✅ `resources/views/products/index.blade.php`
- ✅ `resources/views/products/create.blade.php`
- ✅ `resources/views/products/edit.blade.php`
- ✅ Route registered in `routes/web.php`

**Run Migration:**
```bash
php artisan migrate
```

**Access:**
- Index: `http://localhost/products`
- Create: `http://localhost/products/create`

---

### 2. CRUD with Image Upload

```bash
php artisan make:crud Product --fields="name:string,price:decimal,image:image:nullable,description:text:nullable"
```

**Features:**
- ✅ Automatic file upload handling
- ✅ Image preview in edit form
- ✅ Old file deletion on update
- ✅ Validation for image types and size

---

### 3. CRUD with Enum (Status Dropdown)

```bash
php artisan make:crud Product --fields="name:string,price:integer,status:enum(active,inactive,draft):default('draft')"
```

**Features:**
- ✅ Automatic dropdown generation
- ✅ Default value set to 'draft'
- ✅ Validation with `in:active,inactive,draft`

---

### 4. Admin Panel CRUD (Namespaced)

```bash
php artisan make:crud Admin/Product --fields="name:string,price:integer" --auth
```

**Generated Files:**
- ✅ `app/Models/Admin/Product.php`
- ✅ `app/Http/Controllers/Admin/ProductController.php`
- ✅ `resources/views/admin/products/*.blade.php`
- ✅ Route: `/admin/products`
- ✅ Protected with `auth` middleware

---

### 5. CRUD with Relationships

```bash
php artisan make:crud Post --fields="title:string,content:text,published_at:date:nullable" --belongsTo=User --belongsTo=Category
```

**Features:**
- ✅ `user()` and `category()` methods in Post model
- ✅ `user_id` and `category_id` foreign keys
- ✅ Automatic dropdowns in create/edit forms
- ✅ Relationship data loaded in controller

---

### 6. Complete E-commerce Product CRUD

```bash
php artisan make:crud Product \
  --fields="name:string:unique,slug:string:unique,description:text:nullable,price:decimal:default(0),sale_price:decimal:nullable,stock:integer:default(0),sku:string:unique,image:image:nullable,status:enum(active,inactive):default('active'),featured:boolean:default(0)" \
  --belongsTo=Category \
  --softdelete \
  --auth \
  --tests
```

**Features:**
- ✅ 10 fields with proper types
- ✅ Unique constraints on name, slug, sku
- ✅ Image upload
- ✅ Category relationship with dropdown
- ✅ Soft deletes enabled
- ✅ Authentication required
- ✅ PHPUnit tests generated

---

### 7. API CRUD (JSON Responses)

```bash
php artisan make:crud Product --fields="name:string,price:integer" --api
```

**Features:**
- ✅ Routes in `routes/api.php`
- ✅ JSON responses (no views)
- ✅ RESTful endpoints:
  - GET `/api/products` - List all
  - POST `/api/products` - Create
  - GET `/api/products/{id}` - Show one
  - PUT `/api/products/{id}` - Update
  - DELETE `/api/products/{id}` - Delete

---

### 8. CRUD with All Modifiers

```bash
php artisan make:crud User \
  --fields="name:string:unique,email:email:unique,phone:string:nullable,bio:text:nullable,age:integer:nullable,salary:decimal:nullable,is_active:boolean:default(1),joined_at:date:default(CURRENT_DATE)"
```

**Modifiers Used:**
- `unique` - Database unique constraint
- `nullable` - Optional field
- `default(value)` - Default value

---

## Next Steps After Generation

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Create Sample Data (Optional)
```bash
php artisan tinker
>>> App\Models\Product::factory()->count(10)->create();
```

### 3. Visit Your Application
```
http://localhost/products
```

### 4. Customize Views (Optional)
Publish stubs and modify:
```bash
php artisan vendor:publish --tag=crud-generator-stubs
```

Edit files in `stubs/crud-generator/`

---

## Common Commands

### Delete Generated Files
```bash
php artisan crud:delete Product
```

### Publish Config
```bash
php artisan vendor:publish --tag=crud-generator-config
```

### Run Tests
```bash
php artisan test
```

---

## Tips & Tricks

### 1. Field Naming Convention
- Use snake_case: `first_name`, `email_address`
- Relationships: Use singular for belongsTo (`user_id`, `category_id`)

### 2. Field Types Quick Reference
```
string      → <input type="text">
text        → <textarea>
integer     → <input type="number">
email       → <input type="email">
date        → <input type="date">
boolean     → <input type="checkbox">
file        → <input type="file">
image       → <input type="file" accept="image/*">
enum        → <select> dropdown
```

### 3. Validation Rules
Auto-generated based on field type:
- `string` → `required|string|max:255`
- `email` → `required|email|max:255`
- `integer` → `required|integer`
- `image` → `nullable|image|mimes:jpg,jpeg,png,gif|max:2048`

### 4. Search Functionality
Automatically added to index page for:
- `string` fields
- `text` fields
- `email` fields

### 5. File Storage
Default path: `public/uploads`

Change in `config/crud-generator.php`:
```php
'storage_path' => 'public/my-custom-path',
```

---

## Troubleshooting

### Issue: Routes not working
**Solution:** Clear route cache
```bash
php artisan route:clear
php artisan route:cache
```

### Issue: Views not found
**Solution:** Clear view cache
```bash
php artisan view:clear
```

### Issue: Validation not working
**Solution:** Check Form Request classes in `app/Http/Requests/`

### Issue: File upload not working
**Solution:** Create storage link
```bash
php artisan storage:link
```

---

## Full Feature Example

Here's a complete blog post CRUD with all features:

```bash
php artisan make:crud Admin/Post \
  --fields="title:string:unique,slug:string:unique,excerpt:text:nullable,content:text,featured_image:image:nullable,status:enum(draft,published,archived):default('draft'),published_at:datetime:nullable,views:integer:default(0),is_featured:boolean:default(0)" \
  --belongsTo=User \
  --belongsTo=Category \
  --hasMany=Comment \
  --softdelete \
  --auth \
  --tests
```

This generates a complete admin panel blog CRUD with:
- ✅ 9 well-structured fields
- ✅ Image upload for featured image
- ✅ Status dropdown (draft, published, archived)
- ✅ User and Category relationships
- ✅ Comment relationship (hasMany)
- ✅ Soft deletes for recovery
- ✅ Authentication protection
- ✅ Complete test suite
- ✅ Search and sorting
- ✅ Pagination
- ✅ SweetAlert2 notifications

**Total time to create:** 5 seconds! ⚡

---

**Happy Coding! 🚀**