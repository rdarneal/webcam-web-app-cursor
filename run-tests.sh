#!/bin/bash

echo "🧪 Running Backend API Tests for Webcam Web App"
echo "================================================"

# Check if vendor directory exists
if [ ! -d "vendor" ]; then
    echo "❌ Composer dependencies not installed. Running composer install..."
    composer install
fi

# Check if .env.testing exists, create it if not
if [ ! -f ".env.testing" ]; then
    echo "📝 Creating .env.testing file..."
    cp .env.example .env.testing
    
    # Set testing database to SQLite in memory
    sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=sqlite/' .env.testing
    sed -i 's/DB_DATABASE=.*/DB_DATABASE=:memory:/' .env.testing
    
    # Disable external API calls in testing
    echo "" >> .env.testing
    echo "# Testing Configuration" >> .env.testing
    echo "OPENAI_API_KEY=" >> .env.testing
    echo "ELEVEN_LABS_API_KEY=" >> .env.testing
fi

echo "🔑 Generating application key for testing..."
php artisan key:generate --env=testing

echo ""
echo "🏃‍♂️ Running all tests..."
echo ""

# Run specific test suites
echo "1️⃣ Running Unit Tests..."
php artisan test tests/Unit --env=testing

echo ""
echo "2️⃣ Running Feature Tests..."
php artisan test tests/Feature --env=testing

echo ""
echo "📊 Running all tests with coverage summary..."
php artisan test --env=testing

echo ""
echo "✅ Test run complete!" 