# RTC Market Project Management System

## Overview

The RTC Market Project Management System is a Laravel-based application designed to streamline the management of data for the RTC Market Project. This README provides guidance on setting up and running the system in both development and production environments.

## Prerequisites

Ensure the following software is installed on your system:

-   **PHP**: 8.2
-   **Composer**: Dependency management
-   **Node.js**: For frontend asset compilation
-   **npm**: Included with Node.js
-   **MySQL**: Database management
-   **Web Server**: Apache or Nginx

## Development Setup

1. **Clone the Repository**

    ```bash
    git clone <repository-url>
    cd cdms

    composer install
    npm install

    cp .env.example .env
    php artisan key:generate

    php artisan migrate --seed
    npm run dev

    php artisan serve
    ```

## Production Setup

Clone the Repository

git clone <repository-url>
cd cdms

Install Dependencies

composer install --no-dev --optimize-autoloader
npm install && npm run build

Environment Configuration

    Configure .env for production.
    Generate an application key:

    php artisan key:generate

Database Migration
Run migrations:

    php artisan migrate --force

Set File Permissions
Ensure the storage and bootstrap/cache directories are writable:

    chmod -R 775 storage bootstrap/cache

Configure the Web Server
Point the web server's document root to the public directory of the application.

Set Up Queues and Scheduler

    Queue Worker:

php artisan queue:work

Scheduler:
Add this cron job:

    * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1

Application Optimization
Run the following commands to optimize the application for production:

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

Supervisor Configuration (Production)

    Install Supervisor
    For Ubuntu:

sudo apt update
sudo apt install supervisor

Create Supervisor Configuration
Add a configuration file for the queue worker, e.g., /etc/supervisor/conf.d/rtc-queue-worker.conf:

    [program:rtc_queue_worker]
    process_name=%(program_name)s_%(process_num)02d
    command=php /path-to-your-project/artisan queue:work --sleep=3  --tries=3
    autostart=true
    autorestart=true
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/path-to-your-project/storage/logs/queue-worker.log

Reload Supervisor
Apply the new configuration:

    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start rtc_queue_worker:*

Troubleshooting

    Logs are available in the storage/logs directory.
    Always back up the .env file and database before making changes.
    Use php artisan config:clear to reset cached configurations if changes to .env are not taking effect.
