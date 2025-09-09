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
    cd path-to-project

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

    chmod -R 770 storage bootstrap/cache

Configure the Web Server
Point the web server's document root to the public directory of the application.

Scheduler:
Add this cron job:

    * * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1

Application Optimization
Run the following commands to optimize the application for production:

    php artisan config:cache
    php artisan route:cache
    php artisan view:cache

Supervisor Configuration (Production)

Create the first worker configuration file:

    sudo apt update
    sudo apt install supervisor

Create Supervisor Configuration
Add a configuration file for queue workers

    sudo nano /etc/supervisor/conf.d/worker1.conf

Paste the following (replace path-to-project with real path):

    [program:laravel-worker1]
    process_name=%(program_name)s_%(process_num)02d
    command=php /home/path-to-project/artisan queue:work --queue=default --sleep=3 --tries=3 --timeout=300
    autostart=true
    autorestart=true
    user=rtcmarket
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/home/path-to-project/storage/logs/worker1.log
    stopwaitsecs=3600

Add a configuration file for queue worker 2

    sudo nano /etc/supervisor/conf.d/worker2.conf

Paste the following (replace path-to-project with real path):

    [program:laravel-worker2]
    process_name=%(program_name)s_%(process_num)02d
    command=php /home/path-to-project/artisan queue:work --queue=emails --sleep=3 --tries=3 --timeout=300
    autostart=true
    autorestart=true
    user=rtcmarket
    numprocs=1
    redirect_stderr=true
    stdout_logfile=/home/path-to-project/storage/logs/worker2.log
    stopwaitsecs=3600


Reload Supervisor
Apply the new configuration:

    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start laravel-worker1:*
    sudo supervisorctl start laravel-worker2:*


Troubleshooting

    Logs are available in the storage/logs directory.
    Always back up the .env file and database before making changes.
    Use php artisan config:clear to reset cached configurations if changes to .env are not taking effect.

Create Storage Symlink
    
    php artisan storage:link

Set Folder Permissions

Ensure Laravel can write to necessary directories:

    chmod -R 770 storage bootstrap/cache
    chown -R rtcmarket:rtcmarket storage bootstrap/cache
    chown -h rtcmarket:rtcmarket public/storage
