# Real-World Examples

Complete collection of real-world CRUD examples for common use cases.

## Table of Contents
- [E-commerce](#e-commerce)
- [Blog & CMS](#blog--cms)
- [User Management](#user-management)
- [Inventory](#inventory)
- [Booking System](#booking-system)
- [Social Media](#social-media)
- [Education](#education)
- [Healthcare](#healthcare)

---

## E-commerce

### 1. Product Management

```bash
php artisan make:crud Product \
  --fields="name:string:unique,slug:string:unique,sku:string:unique,description:text:nullable,short_description:text:nullable,price:decimal:default(0),sale_price:decimal:nullable,cost:decimal:nullable,stock:integer:default(0),weight:decimal:nullable,featured_image:image:nullable,gallery:text:nullable,meta_title:string:nullable,meta_description:text:nullable,status:enum(draft,active,inactive):default('draft'),is_featured:boolean:default(0),published_at:datetime:nullable" \
  --belongsTo=Category \
  --belongsTo=Brand \
  --hasMany=Review \
  --softdelete \
  --auth \
  --tests
```

**Features:**
- Complete product fields with SEO
- Image upload
- Category and Brand relationships
- Stock management
- Pricing with sale price
- Featured products
- Soft deletes

### 2. Order Management

```bash
php artisan make:crud Order \
  --fields="order_number:string:unique,subtotal:decimal:default(0),tax:decimal:default(0),shipping:decimal:default(0),total:decimal:default(0),discount:decimal:default(0),status:enum(pending,processing,shipped,delivered,cancelled):default('pending'),payment_status:enum(pending,paid,failed,refunded):default('pending'),payment_method:string:nullable,shipping_address:text,billing_address:text,notes:text:nullable,ordered_at:datetime" \
  --belongsTo=User \
  --hasMany=OrderItem \
  --softdelete \
  --auth
```

### 3. Category Management

```bash
php artisan make:crud Category \
  --fields="name:string:unique,slug:string:unique,description:text:nullable,image:image:nullable,parent_id:integer:nullable,sort_order:integer:default(0),is_active:boolean:default(1)" \
  --hasMany=Product \
  --softdelete \
  --auth
```

### 4. Customer Management

```bash
php artisan make:crud Customer \
  --fields="first_name:string,last_name:string,email:email:unique,phone:string:nullable,company:string:nullable,address:text:nullable,city:string:nullable,state:string:nullable,zip:string:nullable,country:string:nullable,notes:text:nullable" \
  --hasMany=Order \
  --softdelete \
  --auth
```

---

## Blog & CMS

### 1. Blog Post

```bash
php artisan make:crud Post \
  --fields="title:string:unique,slug:string:unique,excerpt:text:nullable,content:text,featured_image:image:nullable,meta_title:string:nullable,meta_description:text:nullable,meta_keywords:text:nullable,status:enum(draft,published,scheduled,archived):default('draft'),published_at:datetime:nullable,views:integer:default(0),reading_time:integer:nullable,is_featured:boolean:default(0)" \
  --belongsTo=User \
  --belongsTo=Category \
  --hasMany=Comment \
  --softdelete \
  --auth \
  --tests
```

### 2. Page Management

```bash
php artisan make:crud Page \
  --fields="title:string:unique,slug:string:unique,content:text,template:string:nullable,meta_title:string:nullable,meta_description:text:nullable,status:enum(draft,published):default('draft'),sort_order:integer:default(0)" \
  --softdelete \
  --auth
```

### 3. Comment System

```bash
php artisan make:crud Comment \
  --fields="content:text,author_name:string,author_email:email,author_url:url:nullable,ip_address:string:nullable,status:enum(pending,approved,spam):default('pending'),approved_at:datetime:nullable" \
  --belongsTo=Post \
  --belongsTo=User \
  --softdelete \
  --auth
```

### 4. Media Library

```bash
php artisan make:crud Media \
  --fields="title:string,filename:string,filepath:string,mime_type:string,size:integer,alt_text:string:nullable,caption:text:nullable,description:text:nullable" \
  --belongsTo=User \
  --softdelete \
  --auth
```

---

## User Management

### 1. Employee Management

```bash
php artisan make:crud Employee \
  --fields="employee_id:string:unique,first_name:string,last_name:string,email:email:unique,phone:string:nullable,date_of_birth:date:nullable,hire_date:date,photo:image:nullable,address:text:nullable,city:string:nullable,state:string:nullable,zip:string:nullable,salary:decimal:nullable,status:enum(active,on_leave,terminated):default('active')" \
  --belongsTo=Department \
  --belongsTo=Position \
  --softdelete \
  --auth
```

### 2. Department Management

```bash
php artisan make:crud Department \
  --fields="name:string:unique,code:string:unique,description:text:nullable,budget:decimal:nullable,location:string:nullable" \
  --belongsTo=User \
  --hasMany=Employee \
  --softdelete \
  --auth
```

### 3. Role & Permission

```bash
php artisan make:crud Role \
  --fields="name:string:unique,display_name:string,description:text:nullable,is_admin:boolean:default(0)" \
  --hasMany=User \
  --auth
```

---

## Inventory

### 1. Warehouse Management

```bash
php artisan make:crud Warehouse \
  --fields="name:string:unique,code:string:unique,address:text,city:string,state:string,zip:string,phone:string:nullable,email:email:nullable,capacity:integer:nullable,status:enum(active,inactive):default('active')" \
  --hasMany=Product \
  --softdelete \
  --auth
```

### 2. Stock Movement

```bash
php artisan make:crud StockMovement \
  --fields="reference:string:unique,type:enum(in,out,transfer,adjustment),quantity:integer,from_location:string:nullable,to_location:string:nullable,notes:text:nullable,movement_date:datetime" \
  --belongsTo=Product \
  --belongsTo=Warehouse \
  --belongsTo=User \
  --auth
```

### 3. Supplier Management

```bash
php artisan make:crud Supplier \
  --fields="name:string:unique,company:string,email:email:unique,phone:string,website:url:nullable,address:text,city:string,state:string,zip:string,contact_person:string:nullable,notes:text:nullable,rating:integer:nullable" \
  --hasMany=Product \
  --softdelete \
  --auth
```

---

## Booking System

### 1. Appointment Booking

```bash
php artisan make:crud Appointment \
  --fields="appointment_number:string:unique,appointment_date:datetime,duration:integer:default(30),status:enum(pending,confirmed,cancelled,completed):default('pending'),notes:text:nullable,reminder_sent:boolean:default(0)" \
  --belongsTo=User \
  --belongsTo=Service \
  --softdelete \
  --auth
```

### 2. Room Booking

```bash
php artisan make:crud RoomBooking \
  --fields="booking_number:string:unique,check_in:date,check_out:date,guests:integer:default(1),amount:decimal,paid_amount:decimal:default(0),status:enum(pending,confirmed,checked_in,checked_out,cancelled):default('pending'),special_requests:text:nullable" \
  --belongsTo=User \
  --belongsTo=Room \
  --softdelete \
  --auth
```

### 3. Event Registration

```bash
php artisan make:crud EventRegistration \
  --fields="registration_number:string:unique,attendee_name:string,attendee_email:email,attendee_phone:string:nullable,ticket_type:string,quantity:integer:default(1),total_amount:decimal,payment_status:enum(pending,paid,refunded):default('pending'),registration_date:datetime" \
  --belongsTo=Event \
  --belongsTo=User \
  --auth
```

---

## Social Media

### 1. User Profile

```bash
php artisan make:crud Profile \
  --fields="username:string:unique,bio:text:nullable,avatar:image:nullable,cover_photo:image:nullable,website:url:nullable,location:string:nullable,date_of_birth:date:nullable,gender:enum(male,female,other):nullable,is_verified:boolean:default(0),followers_count:integer:default(0),following_count:integer:default(0)" \
  --belongsTo=User \
  --auth
```

### 2. Social Post

```bash
php artisan make:crud SocialPost \
  --fields="content:text,image:image:nullable,video:string:nullable,link:url:nullable,status:enum(draft,published,archived):default('draft'),likes_count:integer:default(0),comments_count:integer:default(0),shares_count:integer:default(0),is_pinned:boolean:default(0),published_at:datetime:nullable" \
  --belongsTo=User \
  --hasMany=Comment \
  --softdelete \
  --auth
```

### 3. Follow System

```bash
php artisan make:crud Follow \
  --fields="follower_id:integer,following_id:integer,followed_at:datetime" \
  --belongsTo=User \
  --auth
```

---

## Education

### 1. Course Management

```bash
php artisan make:crud Course \
  --fields="title:string:unique,slug:string:unique,description:text,short_description:text:nullable,thumbnail:image:nullable,price:decimal:default(0),discount_price:decimal:nullable,duration:integer,level:enum(beginner,intermediate,advanced):default('beginner'),language:string:default('English'),requirements:text:nullable,what_you_learn:text:nullable,status:enum(draft,published,archived):default('draft'),enrolled_count:integer:default(0),rating:decimal:nullable" \
  --belongsTo=Instructor \
  --belongsTo=Category \
  --hasMany=Lesson \
  --hasMany=Enrollment \
  --softdelete \
  --auth
```

### 2. Student Management

```bash
php artisan make:crud Student \
  --fields="student_id:string:unique,first_name:string,last_name:string,email:email:unique,phone:string:nullable,date_of_birth:date,gender:enum(male,female,other),address:text:nullable,city:string:nullable,state:string:nullable,zip:string:nullable,photo:image:nullable,enrollment_date:date,status:enum(active,suspended,graduated):default('active')" \
  --hasMany=Enrollment \
  --softdelete \
  --auth
```

### 3. Assignment Management

```bash
php artisan make:crud Assignment \
  --fields="title:string,description:text,due_date:datetime,max_score:integer:default(100),attachments:text:nullable,status:enum(draft,published):default('draft')" \
  --belongsTo=Course \
  --belongsTo=Instructor \
  --hasMany=Submission \
  --softdelete \
  --auth
```

---

## Healthcare

### 1. Patient Management

```bash
php artisan make:crud Patient \
  --fields="patient_id:string:unique,first_name:string,last_name:string,email:email:nullable,phone:string,date_of_birth:date,gender:enum(male,female,other),blood_group:enum(A+,A-,B+,B-,O+,O-,AB+,AB-):nullable,address:text,city:string,state:string,zip:string,emergency_contact:string:nullable,emergency_phone:string:nullable,medical_history:text:nullable,allergies:text:nullable,photo:image:nullable" \
  --hasMany=Appointment \
  --softdelete \
  --auth
```

### 2. Doctor Management

```bash
php artisan make:crud Doctor \
  --fields="doctor_id:string:unique,first_name:string,last_name:string,email:email:unique,phone:string,specialization:string,qualification:string,experience:integer,consultation_fee:decimal,photo:image:nullable,bio:text:nullable,status:enum(active,on_leave,inactive):default('active')" \
  --belongsTo=Department \
  --hasMany=Appointment \
  --softdelete \
  --auth
```

### 3. Prescription Management

```bash
php artisan make:crud Prescription \
  --fields="prescription_number:string:unique,diagnosis:text,medicines:text,dosage_instructions:text,tests_recommended:text:nullable,follow_up_date:date:nullable,notes:text:nullable,prescribed_at:datetime" \
  --belongsTo=Patient \
  --belongsTo=Doctor \
  --softdelete \
  --auth
```

---

## API Examples

### RESTful API

```bash
php artisan make:crud Product \
  --fields="name:string,price:decimal,stock:integer" \
  --api
```

**API Endpoints Generated:**
- `GET /api/products` - List all
- `POST /api/products` - Create
- `GET /api/products/{id}` - Show one
- `PUT /api/products/{id}` - Update
- `DELETE /api/products/{id}` - Delete

---

## Testing Examples

All examples above can include tests:

```bash
# Add --tests flag
php artisan make:crud Product --fields="..." --tests

# Or use Pest
php artisan make:crud Product --fields="..." --pest
```

---

**Need more examples? Open an issue on GitHub!** 🚀