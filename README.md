# Web Scraper Application

A Laravel-based web scraping application that extracts product information from various e-commerce platforms. The application follows SOLID principles, design patterns, and Laravel best practices.

## 🚀 Features

- **Multi-platform Scraping**: Support for Amazon and Jumia
- **Queue-based Processing**: Background job processing for large-scale scraping
- **Proxy Support**: Rotating proxy support to avoid rate limiting
- **RESTful API**: JSON API for product management
- **Error Handling**: Comprehensive error handling and logging
- **Repository Pattern**: Clean data access layer
- **Strategy Pattern**: Extensible scraper architecture

## 📋 Requirements

### System Requirements

- **PHP**: ^8.2 (PHP 8.2 or higher)
- **Laravel**: ^12.0 (Laravel 12.x)
- **Go**: ^1.24.4 (Go 1.24.4 or higher) - Required for the Go proxy service
- **Web Server**: Apache/Nginx (for production) or PHP built-in server (for development)
- **Database**: MySQL, PostgreSQL, SQLite, or SQL Server
- **Memory**: Minimum 512MB RAM (1GB+ recommended for large scraping operations)
- **Storage**: At least 100MB free space for logs and temporary files

### PHP Extensions

The following PHP extensions are required:
- `curl` - For HTTP requests
- `dom` - For HTML parsing
- `mbstring` - For string manipulation
- `xml` - For XML processing
- `json` - For JSON handling
- `pdo` - For database operations
- `openssl` - For HTTPS requests

### Dependencies

#### Core Dependencies
- **Laravel Framework**: ^12.0 - PHP web framework
- **Guzzle HTTP**: ^7.9 - HTTP client for making requests
- **Symfony DOM Crawler**: ^7.3 - HTML/XML parsing and navigation
- **Laravel Tinker**: ^2.10.1 - REPL for Laravel

#### Development Dependencies
- **Faker**: ^1.23 - Data generation for testing
- **Laravel Pail**: ^1.2.2 - Log viewer
- **Laravel Pint**: ^1.13 - PHP code style fixer
- **Laravel Sail**: ^1.41 - Docker development environment
- **Mockery**: ^1.6 - Mocking framework for testing
- **PHPUnit**: ^11.5.3 - Unit testing framework
- **Nunomaduro Collision**: ^8.6 - Error reporting

#### Frontend Dependencies (Optional)
- **Vite**: ^6.2.4 - Build tool
- **Tailwind CSS**: ^4.0.0 - CSS framework
- **Axios**: ^1.8.2 - HTTP client for JavaScript
- **Laravel Vite Plugin**: ^1.2.0 - Vite integration for Laravel

### Optional Requirements

- **Redis**: For queue processing (recommended for production)
- **Supervisor**: For process management in production
- **Proxy Service**: For rotating IP addresses during scraping
- **Go Proxy Service**: Custom proxy service included in the project

### Browser Requirements (for scraping)

The scrapers are designed to work with modern web browsers and may require:
- JavaScript rendering capabilities (for dynamic content)
- User-Agent rotation
- Cookie management
- Session handling

## 📁 Project Structure

```
scraper-app/
├── app/
│   ├── Console/Commands/
│   │   └── ScrapeProduct.php          # Artisan command for single product scraping
│   ├── Exceptions/
│   │   └── UnsupportedSourceException.php  # Custom exception for unsupported URLs
│   ├── Helpers/
│   │   └── ProxyHelper.php            # Proxy management utility
│   ├── Http/Controllers/Api/
│   │   └── ProductController.php      # RESTful API controller
│   ├── Interfaces/
│   │   └── ProductRepositoryInterface.php  # Repository interface
│   ├── Jobs/
│   │   └── ScrapeCategoryJob.php      # Queue job for category scraping
│   ├── Models/
│   │   └── Product.php                # Product Eloquent model
│   ├── Providers/
│   │   └── AppServiceProvider.php     # Service provider for DI
│   ├── Repositories/
│   │   └── ProductRepository.php      # Repository implementation
│   └── Services/Scrapers/
│       ├── AmazonScraper.php          # Amazon-specific scraper
│       ├── JumiaScraper.php           # Jumia-specific scraper
│       ├── ScraperInterface.php       # Scraper contract
│       └── ScraperService.php         # Scraper factory service
├── database/migrations/
│   └── 2025_06_25_053649_create_products_table.php
├── routes/
│   └── api.php                        # API routes
└── README.md
```

## 🏗️ Architecture

### Design Patterns Used

1. **Repository Pattern**: Abstracts data access logic
2. **Strategy Pattern**: Allows easy addition of new scrapers
3. **Factory Pattern**: Creates appropriate scrapers based on URL
4. **Dependency Injection**: Loose coupling between components

### SOLID Principles

- **Single Responsibility**: Each class has one clear purpose
- **Open/Closed**: Easy to extend with new scrapers
- **Liskov Substitution**: All scrapers implement the same interface
- **Interface Segregation**: Clean, focused interfaces
- **Dependency Inversion**: Depends on abstractions, not concretions

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd scraper-app
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   ```

5. **Proxy configuration** (optional)
   ```bash
   # Create proxy file
   touch storage/app/proxies.txt
   # Add proxies in format: http://host:port or https://host:port
   ```

## 📖 Usage

### Command Line

**Scrape a single product:**
```bash
php artisan scrape:product https://www.amazon.com/dp/B07XKXQL79"
```

**Scrape a category (via queue):**
```bash
php artisan queue:work
# Then dispatch the job programmatically or via command
```

### API Endpoints

**Get all products:**
```bash
GET /api/products
```

**Get specific product:**
```bash
GET /api/products/{external_id}
```

**Get statistics:**
```bash
GET /api/products/stats/statistics
```

### Queue Processing

Start the queue worker:
```bash
php artisan queue:work
```

## 🔧 Configuration

### Proxy Setup

Create `storage/app/proxies.txt` with one proxy per line:
```
http://proxy1.example.com:8080
http://proxy2.example.com:8080
https://proxy3.example.com:8080
```

### Go Proxy Service

**Prerequisite:** Go language must be installed on your server or local machine to run the Go proxy service.

The project includes a custom Go proxy service for rotating proxy management:

1. **Navigate to the Go proxy service directory:**
   ```bash
   cd go-proxy-service
   ```

2. **Run the Go proxy server:**
   ```bash
   go run main.go
   ```

3. **Access the proxy service:**
   - The service will be available at: `http://localhost:8080/get-proxy`
   - Returns a random proxy from the configured list in JSON format
   - Example response: `{"proxy": "http://185.217.143.123:3128"}`

4. **Requirements:**
   - Go 1.24.4 or higher
   - The service uses the standard Go HTTP library (no external dependencies)

**Note:** The Go proxy service runs independently from the Laravel application and provides a simple API endpoint for proxy rotation. You can modify the proxy list in `go-proxy-service/main.go` to add your own proxies.

### Queue Configuration

Configure your queue driver in `.env`:
```env
QUEUE_CONNECTION=database
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📝 Code Quality

### Documentation

- All classes and methods are fully documented with PHPDoc
- Clear inline comments explain complex logic
- README provides comprehensive usage instructions

### Error Handling

- Comprehensive try-catch blocks
- Detailed error logging
- Graceful failure handling
- User-friendly error messages

### Best Practices

- PSR-12 coding standards
- Type hints and return types
- Proper exception handling
- Dependency injection
- Interface segregation

## 🔄 Adding New Scrapers

1. **Create new scraper class:**
   ```php
   class NewPlatformScraper implements ScraperInterface
   {
       public function supports(string $url): bool
       {
           return str_contains($url, 'newplatform.');
       }
       
       public function scrapeProduct(string $url): array
       {
           // Implementation
       }
       
       public function scrapeCategory(string $url): array
       {
           // Implementation
       }
   }
   ```

2. **Register in AppServiceProvider:**
   ```php
   $scrapers = [
       new AmazonScraper(),
       new JumiaScraper(),
       new NewPlatformScraper(), // Add here
   ];
   ```

## 🚨 Error Handling

The application includes comprehensive error handling:

- **Network errors**: Retry logic with exponential backoff
- **Rate limiting**: Proxy rotation and delays
- **Invalid data**: Validation and sanitization
- **Missing elements**: Graceful fallbacks
- **Queue failures**: Job retry mechanisms

## 📊 Logging

All operations are logged with appropriate levels:
- `info`: Successful operations
- `warning`: Non-critical issues
- `error`: Critical failures
- `debug`: Detailed debugging information

## 🤝 Contributing

1. Follow PSR-12 coding standards
2. Add comprehensive documentation
3. Include error handling
4. Write tests for new features
5. Update README if needed

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For issues and questions:
1. Check the logs in `storage/logs/`
2. Review the documentation
3. Create an issue with detailed information
