# Test Medify - Master Items Management System

A Laravel-based web application for managing master items (products) and categories with advanced filtering, export capabilities, and PDF generation.

## Features

### Master Items Management

-   **CRUD Operations**: Create, Read, Update, and Delete master items
-   **Advanced Filtering**: Filter items by:
    -   Kode (Item Code)
    -   Nama (Item Name)
    -   Kategori (Category)
    -   Harga Min/Max (Price Range)
-   **Picture Upload**: Upload and manage item pictures
-   **Multi-Category Assignment**: Assign multiple categories to each item
-   **Excel Export**: Download filtered data as Excel file (`Data Items YYYY-MM-DD.xlsx`)
    -   Exports include: No, Nama items, Kategori, Nama supplier, Harga, Laba, Harga jual
-   **Detail View**: View complete item information including associated categories
-   **DataTables Integration**: Interactive table with search, sort, and pagination

### Categories (Kategoris) Management

-   **CRUD Operations**: Full create, read, update, and delete functionality
-   **Auto-Generated Code**: Automatic code generation from category name
    -   Single word: First 3 letters
    -   Multiple words: First 3 letters of each word separated by dash
    -   Short words: Uses word as-is if less than 3 letters
-   **Sorting**: Automatic sorting by name (ascending)
-   **PDF Export**: Download category details with associated items as PDF
    -   Includes: Category name, code, items table, and print date/time
-   **Many-to-Many Relationship**: Categories linked to multiple items

### Additional Features

-   **User Authentication**: Laravel UI authentication system
-   **Active Navigation**: Highlighted active menu items
-   **Responsive Design**: Bootstrap-based responsive UI
-   **Soft Deletes**: Logical deletion with soft delete support
-   **Modern UI Components**: Chip-based category selector with search functionality

## Requirements

-   **PHP**: 8.2 or higher
-   **Composer**: Latest version
-   **Node.js & NPM**: For frontend assets
-   **Database**: MySQL, PostgreSQL, or SQLite
-   **Web Server**: Apache or Nginx (or use `php artisan serve`)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd test-medify
```

### 2. Install PHP Dependencies

**Important**: This project uses PHP 8.2. If you have multiple PHP versions, use PHP 8.2 for composer:

```bash
# Using php82 command (Arch Linux)
php82 /usr/bin/composer install

# Or if php82 is in your PATH
php82 composer install

# Standard installation (if PHP 8.2 is default)
composer install
```

### 3. Install Frontend Dependencies

```bash
npm install

npm run build
```

### 4. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php82 artisan key:generate
```

### 5. Configure Database

Edit `.env` file and set your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test_medify
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. Run Migrations

```bash
php82 artisan migrate
```

### 7. Seed Database (Optional)

```bashg
php82 artisan db:seed
```

This will populate the database with sample data for:

-   Master Items
-   Categories (Kategoris)
-   Category-Item relationships

### 8. Create Storage Link

```bash
php82 artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` for accessing uploaded files.

## Running the Project

### Development Server

```bash
# Using PHP 8.2
php82 artisan serve

# Or if PHP 8.2 is default
php artisan serve
```

The application will be available at `http://localhost:8000`

### Production Deployment

For production, configure your web server (Apache/Nginx) to point to the `public` directory as the document root.

## Usage

### Accessing the Application

1. Navigate to `http://localhost:8000`
2. Register a new account or login with existing credentials
3. You'll be redirected to the Master Items page
4. For default after seeding the database there is account with this credentials

```
  email: medify@example.com,
  password: password
```

### Master Items

-   **View All Items**: Navigate to "Master Items" from the top menu
-   **Create New Item**: Click "+ Master Items Baru" button
-   **Filter Items**: Use the filter section to search by kode, nama, kategori, or price range
-   **Update Item**: Click the edit icon in the table row
-   **Delete Item**: Click the delete icon and confirm
-   **View Details**: Click the view icon to see complete item information
-   **Export to Excel**: Click "Download Excel" button to export filtered data

### Categories

-   **View All Categories**: Navigate to "Kategoris" from the top menu
-   **Create Category**: Click "+ Kategori Baru" button
-   **Auto-Generated Code**: Leave code field empty to auto-generate from name
-   **Update Category**: Click edit icon in the table
-   **Delete Category**: Click delete icon and confirm
-   **View Details**: Click view icon to see category details and associated items
-   **Download PDF**: Click "Download PDF" button on category detail page

## Project Structure

```
test-medify/
├── app/
│   ├── Exports/              # Excel export classes
│   ├── Http/Controllers/      # Application controllers
│   └── Models/                # Eloquent models
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   └── views/
│       ├── kategoris/         # Category views
│       ├── master_items/      # Master item views
│       └── layouts/           # Layout templates
├── routes/
│   ├── web.php               # Web routes
│   └── api.php               # API routes
└── storage/
    └── app/public/            # Public storage (uploaded files)
```

## Technologies Used

-   **Backend**: Laravel 9.19
-   **Frontend**: Bootstrap 5, jQuery, DataTables
-   **PDF Generation**: barryvdh/laravel-dompdf
-   **Excel Export**: maatwebsite/excel
-   **Database**: Eloquent ORM
-   **Authentication**: Laravel UI

## Key Packages

-   `barryvdh/laravel-dompdf` (^3.1) - PDF generation
-   `maatwebsite/excel` (^3.1) - Excel export functionality
-   `laravel/ui` (^4.0) - Authentication scaffolding

## API Endpoints

### Master Items API

-   `PUT /api/master-items/{id}` - Update master item
-   `POST /api/master-items/{id}` - Update master item (with file upload)
-   `DELETE /api/master-items/{id}` - Delete master item
-   `GET /master-items/search` - Search/filter master items

### Categories API

-   `GET /api/kategoris` - Get all categories
-   `PUT /api/kategoris/{id}` - Update category
-   `DELETE /api/kategoris/{id}` - Delete category
-   `GET /kategoris/search` - Search categories

## Database Schema

### master_items

-   id, kode, nama, harga_beli, laba, supplier, jenis, picture, timestamps, deleted_at

### kategoris

-   id, kode, nama, timestamps, deleted_at

### kategori_items (Pivot Table)

-   id, master_item_id, kategori_id, timestamps, deleted_at

## Notes

-   **PHP Version**: This project specifically requires PHP 8.2. Use `php82` command when running artisan or composer commands if you have multiple PHP versions installed.
-   **Storage**: Make sure to run `php artisan storage:link` to enable file uploads.
-   **Soft Deletes**: Both master_items and kategoris use soft deletes, so deleted records are not permanently removed from the database.

## Troubleshooting

### Composer Issues

If you encounter PHP version issues with composer:

```bash
php82 /usr/bin/composer install
```

### Storage Link Issues

If images are not displaying:

```bash
php82 artisan storage:link
```

### Permission Issues

Ensure storage and cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
