 # Contributing to Laravel CRUD Generator

First off, thank you for considering contributing to Laravel CRUD Generator! 🎉

## Table of Contents
- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Coding Standards](#coding-standards)
- [Pull Request Process](#pull-request-process)
- [Testing](#testing)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to [your.email@example.com].

### Our Pledge
- Be respectful and inclusive
- Accept constructive criticism
- Focus on what is best for the community
- Show empathy towards other community members

## How Can I Contribute?

### Reporting Bugs 🐛

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples**
- **Describe the behavior you observed**
- **Explain which behavior you expected**
- **Include Laravel version, PHP version, and package version**

**Bug Report Template:**
```markdown
**Describe the bug**
A clear description of what the bug is.

**To Reproduce**
Steps to reproduce:
1. Run command '...'
2. See error

**Expected behavior**
What you expected to happen.

**Environment:**
 - Laravel Version: [e.g. 10.x]
 - PHP Version: [e.g. 8.2]
 - Package Version: [e.g. 1.0.0]

**Additional context**
Any other context about the problem.
```

### Suggesting Enhancements 💡

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a detailed description of the suggested enhancement**
- **Explain why this enhancement would be useful**
- **List some examples of how it would be used**

### Your First Code Contribution

Unsure where to begin? You can start by looking through `beginner` and `help-wanted` issues:

- **Beginner issues** - issues which should only require a few lines of code
- **Help wanted issues** - issues which should be a bit more involved

### Pull Requests

1. Fork the repo and create your branch from `main`
2. If you've added code, add tests
3. Ensure the test suite passes
4. Make sure your code follows the coding standards
5. Issue that pull request!

## Development Setup

### Prerequisites
- PHP 8.1 or higher
- Composer
- Laravel 10.x or higher

### Installation

1. **Fork and Clone**
```bash
git clone https://github.com/YOUR-USERNAME/crud-generator.git
cd crud-generator
```

2. **Install Dependencies**
```bash
composer install
```

3. **Link to Local Laravel Project**
In your Laravel project's `composer.json`:
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

Then run:
```bash
composer update sndpbag/crud-generator
```

4. **Make Changes**
Edit files in the package directory.

5. **Test Changes**
```bash
php artisan make:crud TestModel --fields="name:string,price:integer"
```

### Project Structure
```
src/
├── Commands/          # Artisan commands
├── Generators/        # File generators
├── Parsers/          # Field parsers
└── ServiceProvider   # Laravel service provider

stubs/                # Template files
tests/                # Test files
config/               # Configuration
```

## Coding Standards

### PHP Code Style

We follow **PSR-12** coding standards. Please ensure your code adheres to these standards.

**Key Points:**
- Use 4 spaces for indentation (no tabs)
- Use meaningful variable names
- Add type hints for parameters and return types
- Write clear and concise comments
- Follow Laravel naming conventions

**Example:**
```php
<?php

namespace Sndpbag\CrudGenerator\Generators;

use Illuminate\Support\Facades\File;

class ExampleGenerator extends BaseGenerator
{
    public function generate(): array
    {
        // Implementation
        return [
            'success' => true,
            'path' => $path
        ];
    }
}
```

### Naming Conventions

- **Classes:** PascalCase (e.g., `ModelGenerator`)
- **Methods:** camelCase (e.g., `generateModel()`)
- **Variables:** camelCase (e.g., `$modelName`)
- **Constants:** UPPER_SNAKE_CASE (e.g., `DEFAULT_PATH`)
- **Files:** PascalCase for classes, snake_case for configs

### Documentation

- Add PHPDoc blocks for all classes and methods
- Explain complex logic with inline comments
- Update README for new features

**Example:**
```php
/**
 * Generate model file
 *
 * @return array Returns array with success status and file path
 */
public function generate(): array
{
    // Implementation
}
```

## Pull Request Process

### Before Submitting

1. **Update Documentation**
   - Update README if you've added features
   - Update CHANGELOG with your changes
   - Add examples if applicable

2. **Write Tests**
   - Add unit tests for new functionality
   - Ensure all tests pass

3. **Check Code Quality**
```bash
# Run code style checker
composer check-style

# Fix code style issues
composer fix-style

# Run tests
composer test
```

### PR Template

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
How has this been tested?

## Checklist
- [ ] My code follows the style guidelines
- [ ] I have added tests
- [ ] All tests pass
- [ ] I have updated documentation
- [ ] My commits follow the commit message guidelines
```

### Commit Message Guidelines

We follow **Conventional Commits** specification:

```
<type>(<scope>): <subject>

<body>

<footer>
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding tests
- `chore`: Maintenance tasks

**Examples:**
```
feat(generator): add support for polymorphic relationships

- Added polymorphic relationship detection
- Updated stub files
- Added tests

Closes #123
```

```
fix(parser): handle enum fields with spaces

Fixed issue where enum values with spaces weren't parsed correctly

Fixes #45
```

## Testing

### Running Tests

```bash
# Run all tests
composer test

# Run specific test file
./vendor/bin/phpunit tests/Feature/MakeCrudCommandTest.php

# Run with coverage
composer test-coverage
```

### Writing Tests

Place tests in appropriate directory:
- `tests/Unit/` - Unit tests
- `tests/Feature/` - Feature tests

**Example Test:**
```php
<?php

namespace Sndpbag\CrudGenerator\Tests\Feature;

use Tests\TestCase;

class ModelGeneratorTest extends TestCase
{
    public function test_it_generates_model_file()
    {
        $generator = new ModelGenerator($options);
        $result = $generator->generate();
        
        $this->assertTrue($result['success']);
        $this->assertFileExists($result['path']);
    }
}
```

## Recognition

Contributors will be added to:
- README contributors section
- CHANGELOG for their contributions
- GitHub contributors page

## Questions?

Feel free to:
- Open an issue for questions
- Join our Discord server (coming soon)
- Email: your.email@example.com

## License

By contributing, you agree that your contributions will be licensed under the MIT License.