# Changelog

All notable changes to `sndpbag/crud-generator` will be documented in this file.

## [1.0.0] - 2024-01-15

### Added - Initial Release 🎉

#### Core Features
- ✅ Artisan command `make:crud` for generating complete CRUD
- ✅ Smart field type detection (string, integer, text, boolean, enum, etc.)
- ✅ Dynamic validation rule generation
- ✅ Advanced field syntax with modifiers (nullable, unique, default)
- ✅ Enum field support with automatic dropdown generation

#### File Generation
- ✅ Model generation with fillable, casts, and relationships
- ✅ Migration generation with all field types and constraints
- ✅ Controller generation with complete CRUD logic
- ✅ Form Request classes (Store/Update) for validation
- ✅ Blade view generation (index, create, edit)
- ✅ Automatic route registration

#### Advanced Features
- ✅ Namespaced CRUD generation (e.g., Admin/Product)
- ✅ File and image upload handling with old file deletion
- ✅ BelongsTo relationship scaffolding (model + controller + views)
- ✅ HasMany relationship support
- ✅ Authentication middleware support (`--auth` flag)
- ✅ API mode for JSON responses (`--api` flag)
- ✅ Soft deletes support (`--softdelete` flag)
- ✅ PHPUnit test generation (`--tests` flag)
- ✅ Pest test generation (`--pest` flag)

#### View Features
- ✅ Search functionality in index page
- ✅ Sortable table columns
- ✅ Pagination support
- ✅ SweetAlert2 integration for success messages
- ✅ Bootstrap 5 templates
- ✅ Tailwind CSS templates
- ✅ File upload preview in edit forms
- ✅ Relationship dropdowns in forms

#### Developer Experience
- ✅ Rollback command `crud:delete` to remove generated files
- ✅ Customizable stub files
- ✅ Configuration file for default settings
- ✅ Beautiful console output with progress indicators
- ✅ Comprehensive error handling

#### Configuration
- ✅ Template selection (Bootstrap/Tailwind)
- ✅ Customizable storage paths
- ✅ Configurable namespaces
- ✅ Default pagination count
- ✅ Custom success messages
- ✅ Alert library selection

#### Testing
- ✅ Complete test suite for package functionality
- ✅ Auto-generated tests for CRUD operations

### Documentation
- ✅ Comprehensive README with examples
- ✅ Quick Start Guide
- ✅ Package structure documentation
- ✅ Contributing guidelines

---

## [Unreleased]

### Planned Features for v1.1.0
- [ ] Export functionality (Excel/PDF)
- [ ] Bulk actions (delete multiple records)
- [ ] Advanced filtering
- [ ] DataTables integration
- [ ] Factory generation
- [ ] Seeder generation
- [ ] Policy generation
- [ ] Event/Listener generation
- [ ] Vue.js/React component option
- [ ] Multi-language support
- [ ] Custom action buttons
- [ ] Livewire support

### Planned Features for v1.2.0
- [ ] HasOne relationship support
- [ ] BelongsToMany relationship support
- [ ] Polymorphic relationship support
- [ ] Advanced validation rules
- [ ] Custom stub templates per project
- [ ] API Resource generation
- [ ] API documentation generation
- [ ] GraphQL support
- [ ] Websocket/Broadcasting integration

---

## Version History

### Version Numbering
We follow [Semantic Versioning](https://semver.org/):
- **MAJOR** version for incompatible API changes
- **MINOR** version for new functionality in a backward compatible manner
- **PATCH** version for backward compatible bug fixes

### Support Policy
- Latest major version: Full support
- Previous major version: Security fixes only
- Older versions: No support

---

## Upgrade Guide

### From 0.x to 1.0
This is the initial stable release. No upgrade path needed.

---

## Breaking Changes

### Version 1.0.0
- None (initial release)

---

## Contributors

Thanks to all contributors who helped make this package better!

- [Your Name] - Initial development

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
