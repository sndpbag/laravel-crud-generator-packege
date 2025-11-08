<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Template
    |--------------------------------------------------------------------------
    | Choose your preferred CSS framework for generated views.
    | Options: 'bootstrap', 'tailwind'
    */
    'template' => env('CRUD_TEMPLATE', 'bootstrap'),

    /*
    |--------------------------------------------------------------------------
    | Default Storage Path
    |--------------------------------------------------------------------------
    | Default storage path for file uploads
    */
    'storage_path' => env('CRUD_STORAGE_PATH', 'uploads'),

    /*
    |--------------------------------------------------------------------------
    | Default Namespace
    |--------------------------------------------------------------------------
    | Default namespace for generated files
    */
    'namespace' => [
        'model' => 'App\\Models',
        'controller' => 'App\\Http\\Controllers',
        'request' => 'App\\Http\\Requests',
    ],

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    | Default path for generated views
    */
    'view_path' => 'resources/views',

    /*
    |--------------------------------------------------------------------------
    | Route File
    |--------------------------------------------------------------------------
    | Default route file for web routes
    */
    'route_file' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    | Default pagination count
    */
    'pagination' => 10,

    /*
    |--------------------------------------------------------------------------
    | Default Validation Rules
    |--------------------------------------------------------------------------
    | Default validation rules for common field types
    */
    'validation_rules' => [
        'string' => 'required|string|max:255',
        'text' => 'required|string',
        'integer' => 'required|integer',
        'float' => 'required|numeric',
        'decimal' => 'required|numeric',
        'boolean' => 'required|boolean',
        'date' => 'required|date',
        'datetime' => 'required|date',
        'time' => 'required',
        'email' => 'required|email|max:255',
        'file' => 'nullable|file|max:2048',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'enum' => 'required|in',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'file_upload' => [
        'max_size' => 2048, // in KB
        'allowed_image_types' => ['jpg', 'jpeg', 'png', 'gif'],
        'allowed_file_types' => ['pdf', 'doc', 'docx', 'xls', 'xlsx'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Success Messages
    |--------------------------------------------------------------------------
    */
    'messages' => [
        'created' => 'Record created successfully!',
        'updated' => 'Record updated successfully!',
        'deleted' => 'Record deleted successfully!',
    ],

    /*
    |--------------------------------------------------------------------------
    | Toast/Alert Library
    |--------------------------------------------------------------------------
    | Options: 'sweetalert2', 'toastr', 'native'
    */
    'alert_library' => 'sweetalert2',
];