# Scraper App

A Laravel-based web scraping application with a Go proxy service for enhanced scraping capabilities. This application allows you to scrape product data from websites and store it in a database.

## Features

- **Web Scraping**: Scrape product data (title, price, image) from websites
- **API Endpoints**: RESTful API to retrieve scraped products
- **Proxy Service**: Go-based proxy service for rotating proxy IPs
- **Database Storage**: Store scraped data in MySQL/SQLite database
- **Command Line Interface**: Artisan commands for scraping operations
- **Modern UI**: Built with Tailwind CSS and Vite

## Requirements

### System Requirements
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Node.js**: 18.x or higher
- **npm**: Latest version
- **Go**: 1.24.4 or higher (for proxy service)
- **Database**: MySQL 8.0+ or SQLite 3

### PHP Extensions
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

## Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd scraper-app
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Database
Edit the `.env` file and configure your database settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=scraper_app
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

For SQLite (development):
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### 6. Run Database Migrations
```bash
php artisan migrate
```

### 7. Build Frontend Assets
```bash
npm run build
```

## Running the Application

### Laravel Application

#### Development Mode
```bash
# Start the Laravel development server
php artisan serve

# In another terminal, start the queue worker (if using queues)
php artisan queue:work

# In another terminal, start Vite for frontend development
npm run dev
```

#### Production Mode
```bash
# Build assets for production
npm run build

# Start the server (configure your web server accordingly)
php artisan serve --host=0.0.0.0 --port=8000
```

### Go Proxy Service

The Go proxy service provides rotating proxy functionality for the scraper.

#### Navigate to Proxy Service Directory
```bash
cd go-proxy-service
```

#### Install Go Dependencies
```bash
go mod tidy
```

#### Run the Proxy Service
```bash
go run main.go
```

The proxy service will start on `http://localhost:8080` and provide the following endpoint:
- `GET /get-proxy` - Returns a random proxy from the configured list

#### Build Executable (Optional)
```bash
# For Windows
go build -o proxy-service.exe main.go

# For Linux/Mac
go build -o proxy-service main.go

# Run the executable
./proxy-service
```

## Usage

### Command Line Scraping
```bash
# Scrape a product from a URL
php artisan scrape:product "https://example.com/product-page"
```

### API Endpoints

#### Get All Products
```bash
GET /api/products
```

Response:
```json
{
    "data": [
        {
            "id": 1,
            "title": "Product Title",
            "price": "$99.99",
            "image_url": "https://example.com/image.jpg",
            "created_at": "2025-01-01T00:00:00.000000Z",
            "updated_at": "2025-01-01T00:00:00.000000Z"
        }
    ]
}
```

### Web Interface
Visit `http://localhost:8000` to access the web interface.

## Project Structure

```
scraper-app/
├── app/
│   ├── Console/Commands/     # Artisan commands
│   ├── Http/Controllers/     # API controllers
│   ├── Interfaces/          # Repository interfaces
│   ├── Models/              # Eloquent models
│   ├── Repositories/        # Data access layer
│   └── Services/            # Business logic
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── go-proxy-service/        # Go proxy service
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
└── routes/
    └── api.php             # API routes
```

## Configuration

### Proxy Service Configuration
Edit the proxy list in `go-proxy-service/main.go`:

```go
proxies := []string{
    "http://your-proxy1.com:8000",
    "http://your-proxy2.com:8000",
    "http://your-proxy3.com:8000",
}
```

### Scraper Configuration
The scraper service uses rotating user agents and can be configured in `app/Services/ScraperService.php`.

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
# Format PHP code
./vendor/bin/pint

# Format JavaScript/CSS
npm run build
```

### Database Seeding
```bash
php artisan db:seed
```

## Troubleshooting

### Common Issues

1. **Permission Denied**: Ensure proper file permissions for storage and bootstrap/cache directories
2. **Database Connection**: Verify database credentials in `.env` file
3. **Proxy Service Not Starting**: Check if port 8080 is available
4. **Composer Issues**: Clear composer cache with `composer clear-cache`

### Logs
- Laravel logs: `storage/logs/laravel.log`
- Application logs: Use `php artisan pail` for real-time logs

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions, please open an issue in the repository.
