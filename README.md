# Travel_Api

Travel_API is a backend API project developed throughout the entire[Laravel MentorSHIP: Travel API](https://www.youtube.com/playlist?list=PLdXLsjL7A9k2utMAieXUnUP8zyxaDA3mP) course on YouTube.

---
## Table of Contents

- [Features](#features)
- [Installation](#installation)

---

## Features

- **Travel Destinations:**
    - View tours available for specific destinations.
- **Tours for Travel:**
    - View tours available for specific destinations.
- **Admin Access:**
    - Admin users can add new travels and tours.
- **Role-based Access Control:**
    - Admin and editor roles can update travel information.
- **Login Endpoint:**
    - Route for user login and session management.
---


## Installation

Follow these steps to set up and run the CareerBloom on your local machine.

### 1. Clone the repository:

```bash
git clone https://github.com/AhmedTawfikkk/Travel_Api
cd Travel_Api
``` 

### 2. Install backend dependencies:

```bash
composer install
```

### 3. Set up the environment file:

```bash
cp .env.example .env
```
### 4. Generate the application key:

```bash
php artisan key:generate
```
### 5. Set up the database:
Create a new MySQL database (or any other supported database) and configure the connection in your **.env** file:

```env
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password```
```
### 6. Run the database migrations:

```bash
php artisan migrate
```
### 7. (Optional) Seed the database with sample data:

```bash
php artisan db:seed
```

### 8. Run the application:

```bash
php artisan serve
