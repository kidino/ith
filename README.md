# ITH Ticketing System

This is a ticketing/helpdesk system built with [Laravel](https://laravel.com/).  
It is designed for IT, user, and vendor collaboration, with role-based access and management for tickets, users, departments, vendors, categories, and ticket statuses.

## Features

- Ticket creation, assignment, and status tracking
- Role-based access for Admin, IT, User, and Vendor
- Management modules for Users, Departments, Vendors, Categories, and Ticket Statuses
- Modern UI using Tailwind CSS (via Laravel Breeze)
- MIT Open Source License

## Installation

1. **Clone the repository:**
    ```bash
    git clone <your-repo-url>
    cd ith
    ```

2. **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

3. **Copy and configure your environment:**
    ```bash
    cp .env.example .env
    ```
    Edit `.env` and set your database and mail settings.

4. **Generate application key:**
    ```bash
    php artisan key:generate
    ```

5. **Run migrations and seeders:**
    ```bash
    php artisan migrate
    php artisan db:seed
    ```

6. **Build frontend assets:**
    ```bash
    npm run build
    ```

7. **Start the development server:**
    ```bash
    php artisan serve
    ```

8. **Access the application:**
    Open [http://localhost:8000](http://localhost:8000) in your browser.

## Development Notes

- This project is mostly developed using **GitHub Copilot** to demonstrate AI-assisted software development and for learning purposes.
- The codebase is intended for educational use and as a reference for building Laravel-based systems.

## License

This project is open-sourced under the [MIT license](LICENSE).
