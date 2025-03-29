# Task Manager

<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
  </a>
</p>

## About Task Manager

Task Manager is a web application built with **Laravel** and **Vue.js** to help users efficiently manage their tasks. It provides a simple and intuitive interface for creating, reading, updating, and deleting tasks, with features like status filtering, sorting, and pagination. The backend leverages Laravel’s robust API capabilities, while the frontend uses Vue.js with Tailwind CSS for a modern, responsive design.

Key features include:
- **Task CRUD Operations**: Create, view, edit, and delete tasks.
- **Filtering**: Filter tasks by status (pending or completed).
- **Sorting**: Sort tasks by newest or oldest start date.
- **User Authentication**: Secure login and registration using Laravel Sanctum.
- **Persian Calendar Support**: Dates are displayed in the Persian (Shamsi) calendar format.

This project is ideal for individuals or teams looking for a lightweight task management solution with a focus on simplicity and usability.

## Installation

Follow these steps to set up the Task Manager application locally:

### Prerequisites
- **PHP** (>= 8.2)
- **Composer** (latest version)
- **Node.js** (>= 18.x) and **npm** (latest version)
- **MySQL** or another supported database
- **Git**

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/amidesfahani/laravel-vue3-task-manager.git
   cd task-manager
   ```

2. **Install PHP Dependencies**
   ```bash
   composer install
   ```
   This installs all Laravel dependencies listed in `composer.json`.

3. **Install JavaScript Dependencies**
   ```bash
   npm install
   ```
   This installs Vue.js, Tailwind CSS, and other frontend dependencies listed in `package.json`.

4. **Configure Environment**
   - Copy the `.env.example` file to `.env`:
     ```bash
     cp .env.example .env
     ```
   - Update `.env` with your database credentials and other settings:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=task_manager
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```
   - Generate an application key:
     ```bash
     php artisan key:generate
     ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```
   This sets up the database tables for users and tasks.

6. **Run Database Seeders** (Optional)
   ```bash
   php artisan db:seed
   ```
   This populates the database with sample data (e.g., users and tasks). Ensure you have seeders defined in `database/seeders/` if you want initial data.

7. **Compile Frontend Assets**
   ```bash
   npm run dev
   ```
   This builds the Vue.js components and Tailwind CSS styles. For production, use `npm run build`.

8. **Start the Development Server**
   - Run the Laravel server:
     ```bash
     php artisan serve
     ```
   - Access the app at `http://localhost:8000`.

   Alternatively, use Vite’s development server with hot module replacement:
   ```bash
   npm run dev
   ```
   Ensure your `.env` file has `APP_URL=http://localhost:8000` and Vite is configured in `vite.config.js`.

### Post-Installation
- **Login**: Register a user via `/register` or use seeded credentials if applicable.
- **API Testing**: Use tools like Postman to test API endpoints (e.g., `POST /api/tasks`).

## License

Task Manager is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT). Feel free to use, modify, and distribute it as per the license terms.