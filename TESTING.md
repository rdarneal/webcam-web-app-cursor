# Backend API Testing Guide

This document explains how to run and understand the comprehensive test suite for the Webcam Web App backend API functionality.

## Test Structure

### Unit Tests
- **`UserModelTest`** - Tests User model methods for API key management
- **`UserApiKeyModelTest`** - Tests UserApiKey model encryption, relationships, and scopes

### Feature Tests
- **`AuthControllerTest`** - Tests registration, login, logout, and user authentication
- **`ApiKeyControllerTest`** - Tests API key storage, validation, testing, and deletion
- **`ImageProcessingControllerTest`** - Tests image processing with user keys and proxy functionality
- **`ApiAuthenticateTest`** - Tests authentication middleware

### Integration Tests
- **`FullImageProcessingFlowTest`** - End-to-end tests covering complete user journeys

## Running Tests

### Prerequisites
1. Ensure you have PHP 8.1+ and Composer installed
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env.testing` and configure for testing

### Quick Start
```bash
# Make the test script executable
chmod +x run-tests.sh

# Run all tests
./run-tests.sh
```

### Manual Test Execution
```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test tests/Unit
php artisan test tests/Feature
php artisan test tests/Feature/Auth
php artisan test tests/Feature/Integration

# Run specific test file
php artisan test tests/Feature/Auth/AuthControllerTest.php

# Run with verbose output
php artisan test --verbose

# Run specific test method
php artisan test --filter test_user_can_register
```

## Test Configuration

### Environment
Tests use a separate `.env.testing` configuration:
- SQLite in-memory database for fast, isolated tests
- Disabled external API calls (mocked using HTTP fake)
- Separate cache and session configuration

### Database
Tests use `RefreshDatabase` trait to:
- Create fresh database for each test
- Run migrations automatically
- Clean up after each test

### External API Mocking
All external API calls (OpenAI, ElevenLabs) are mocked using Laravel's HTTP fake:
```php
Http::fake([
    'api.openai.com/*' => Http::response([...], 200),
    'api.elevenlabs.io/*' => Http::response([...], 200),
]);
```

## Test Coverage

### Authentication & Authorization
- ✅ User registration with validation
- ✅ User login/logout
- ✅ Session management
- ✅ Authentication middleware
- ✅ Protected route access

### API Key Management
- ✅ Storing API keys (individual and bulk)
- ✅ Retrieving API key status
- ✅ Testing API key validity
- ✅ Deleting API keys
- ✅ API key encryption/decryption
- ✅ Last used timestamp tracking

### Image Processing
- ✅ Image upload and validation
- ✅ Processing with user API keys
- ✅ Custom parameters (model, prompt, voice)
- ✅ Error handling for invalid API keys
- ✅ Audio file generation and storage
- ✅ Temporary file cleanup

### Error Handling
- ✅ Validation errors
- ✅ Authentication errors
- ✅ External API errors
- ✅ Missing API key errors
- ✅ File upload errors

## Test Helpers

The base `TestCase` includes helpful methods:

```php
// Create authenticated user
$user = $this->createAuthenticatedUser();

// Create user with API keys
$user = $this->createAuthenticatedUserWithApiKeys();

// Mock external APIs
$this->mockExternalApis();

// Mock failed external APIs
$this->mockFailedExternalApis();
```

## Assertions Used

### Response Assertions
```php
$response->assertStatus(200);
$response->assertJson(['success' => true]);
$response->assertJsonStructure(['user' => ['id', 'name']]);
$response->assertJsonValidationErrors(['email']);
```

### Database Assertions
```php
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseMissing('user_api_keys', ['service_name' => 'openai']);
```

### Authentication Assertions
```php
$this->assertAuthenticated();
$this->assertGuest();
$this->assertAuthenticatedAs($user);
```

### Custom Assertions
```php
$this->assertTrue($user->hasApiKey('openai'));
$this->assertEquals('test-key', $user->getApiKey('openai'));
```

## Best Practices

1. **Isolation**: Each test is independent and doesn't affect others
2. **Descriptive Names**: Test method names clearly describe what they test
3. **Arrange-Act-Assert**: Tests follow clear structure
4. **Edge Cases**: Tests cover both success and failure scenarios
5. **External Dependencies**: All external APIs are mocked
6. **Data Factories**: Use factories for consistent test data

## Troubleshooting

### Common Issues

**Database Issues**
```bash
# Clear and refresh test database
php artisan migrate:fresh --env=testing
```

**Cache Issues**
```bash
# Clear application cache
php artisan cache:clear
php artisan config:clear
```

**Permission Issues**
```bash
# Ensure storage directories are writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### Debug Failed Tests
```bash
# Run specific failing test with verbose output
php artisan test tests/Feature/Auth/AuthControllerTest.php::test_user_can_register --verbose

# Enable debugging in .env.testing
APP_DEBUG=true
LOG_LEVEL=debug
```

## Adding New Tests

When adding new functionality:

1. **Unit Tests** for model methods and business logic
2. **Feature Tests** for HTTP endpoints and user interactions
3. **Integration Tests** for complete user workflows

Follow the existing patterns and use the provided test helpers for consistency. 