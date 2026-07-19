# ☕ Cozy Café — Cafe Management Web Application

A full-stack, role-based web application for managing a café's online ordering system — built with **PHP**, **MySQL**, **JavaScript**, and **Chart.js**. Supports three distinct user roles (**Admin**, **Employee**, **Customer**), each with a dedicated dashboard and permission set, covering the complete flow from browsing the menu to checkout, order fulfillment, and sales analytics.

> 🎓 Built as a group project for the **Web Technologies** course, Department of Computer Science.

---

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Database Schema](#-database-schema)
- [Getting Started](#-getting-started)
- [User Roles & Access](#-user-roles--access)
- [Screenshots](#-screenshots)
- [Roadmap](#-roadmap)
- [Contributors](#-contributors)
- [License](#-license)

---

## 🧾 Overview

**Cozy Café** is a multi-role café management system that digitizes the day-to-day operations of a coffee shop — from customer-facing menu browsing and checkout, to employee order processing and receipt generation, to admin-level user/product management and sales reporting.

The system uses a **unified `all_users` table** with a role (`type`) column (`admin`, `employee`, `user`) to drive role-based access control and dashboard routing after login.

---

## ✨ Features

### 🔐 Common (All Users)
- User registration & secure login (hashed passwords via `password_verify`)
- Session-based authentication and logout
- Profile management — view, edit, and update account details
- Role-based dashboard redirection after login

### 🛠️ Admin
- **User Management** — create, update, and delete customer & employee accounts
- **Product Management** — add, update, and remove menu items with image uploads
- **Sales Reporting** — daily/monthly revenue charts (Chart.js), order counts, and completed vs. pending order breakdowns
- Employee account management with dedicated registration flow

### 👨‍🍳 Employee
- View and update customer orders (order processing & status updates)
- Handle customer interactions and order status changes
- Generate and view printable/emailable **receipts** for completed transactions

### 🛍️ Customer
- Browse the product catalog with category-based images
- Add items to cart, update quantities, and remove items
- **Checkout** with multiple payment options (Cash on Delivery, Credit/Debit Card, bKash, Nagad, Bank Transfer)
- View order history and track order status
- Manage profile and account settings

---

## 🧰 Tech Stack

| Layer            | Technology                                   |
|-------------------|-----------------------------------------------|
| **Frontend**      | HTML5, CSS3, JavaScript                        |
| **Backend**       | PHP (procedural, PDO & MySQLi)                 |
| **Database**      | MySQL / MariaDB                                |
| **Charts**        | Chart.js                                       |
| **Server**        | Apache (XAMPP / WAMP / LAMP compatible)        |
| **Version Control**| Git & GitHub                                  |

---

## 📁 Project Structure

```
cafeProject/
├── admin/                  # Admin panel — dashboard, user/product/order/sales management
│   ├── dashboard.php
│   ├── products.php
│   ├── orders.php
│   ├── sales_report.php
│   ├── users_accounts.php
│   ├── employee_accounts.php
│   └── ...
├── employee/                # Employee dashboard & receipt generation
│   ├── employee_dashboard.php
│   ├── show_transaction.php
│   └── receipt.php
├── customer/                 # Customer-facing dashboard, cart & checkout
│   ├── customer_dashboard.php
│   ├── customer_products.php
│   ├── cart.php
│   ├── checkout.php
│   ├── profile.php
│   └── settings.php
├── components/               # Shared includes (headers, footer, DB connections)
│   ├── connect.php           # PDO connection
│   ├── connection.php        # MySQLi connection
│   ├── user_header.php
│   ├── admin_header.php
│   └── footer.php
├── css/ , js/ , icon/         # Global static assets
├── images/ , project images/ # Site & marketing images
├── uploaded_img/              # User/product uploaded images
├── home.php / homepage.php    # Public landing page
├── login.php / signup.php     # Authentication pages
├── db.sql                     # Full database schema + seed data
└── README.md
```

---

## 🗄️ Database Schema

The MySQL schema (`db.sql`) defines the following core tables:

| Table       | Purpose                                                        |
|-------------|------------------------------------------------------------------|
| `all_users` | Unified table for admins, employees, and customers (role-based via `type` ENUM) |
| `products`  | Café menu items (coffee, food, desserts, etc.)                  |
| `cart`      | Active customer shopping cart items                              |
| `orders`    | Placed orders with payment method, status, and totals             |
| `messages`  | Customer contact/feedback messages                                |
| `rating`    | Product ratings and reviews                                       |

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) / WAMP / LAMP (PHP 7.4+ and MySQL/MariaDB)
- A web browser

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Dipta-karmakar/Cafe-Shop-Combined.git
   ```

2. **Move the project into your server's web root**
   ```bash
   # Example for XAMPP on Windows
   move Cafe-Shop-Combined C:\xampp\htdocs\cafeProject
   ```

3. **Create the database**
   - Start Apache & MySQL from your XAMPP/WAMP control panel
   - Open **phpMyAdmin** and import `db.sql` to create the `cafe_db` database with sample data

4. **Configure the database connection**
   - Update credentials in `components/connect.php` and `components/connection.php` if they differ from the defaults (`root` / no password / `localhost`)

5. **Run the app**
   ```
   http://localhost/cafeProject/home.php
   ```

### Default Sample Logins
> ⚠️ Passwords in `db.sql` are hashed sample values — for local testing, register a new account via `signup.php` or reset a password through phpMyAdmin.

---

## 👥 User Roles & Access

| Role      | Entry Point                          | Key Capabilities                                   |
|-----------|----------------------------------------|-----------------------------------------------------|
| Admin     | `admin/admin_login.php`               | Manage users, products, employees & view sales reports |
| Employee  | `employee/employee_dashboard.php`     | Process orders, manage transactions, generate receipts |
| Customer  | `customer/customer_dashboard.php`     | Browse menu, cart, checkout & track order history      |

---

## 📸 Screenshots

> _Add screenshots or a short demo GIF here to showcase the homepage, dashboards, and checkout flow — this section makes the biggest visual impression for interviewers._

```
| Homepage | Customer Dashboard | Admin Sales Report |
|----------|---------------------|----------------------|
| ![home](./images/home-img-1.png) | ![customer](path) | ![sales](path) |
```

---

## 🗺️ Roadmap

- [ ] Integrate real payment gateway APIs (bKash/Nagad/Stripe)
- [ ] Migrate all queries fully to PDO with prepared statements
- [ ] Add REST API layer for a future SPA/mobile front-end
- [ ] Add unit/integration tests
- [ ] Dockerize the stack (PHP + MySQL + Apache)

---

## 👨‍💻 Contributors

Project built for the **Web Technologies** course (Section H, Group 8):

| Student ID    | Name                     |
|---------------|---------------------------|
| 23-51069-1    | Dipta Prattoy Karmakar      |
| 23-50022-1    | Ehtesham Ferdous            |
| 21-45284-2    | MD Oakil Sarker            |

---

## 📄 License

This project was developed for academic purposes as part of a university course. Dont copy, feel free to fork and build on it for learning purposes.