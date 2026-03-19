# 🍽️ BITE — Best Instant Takeaway Experience

> A full-stack food delivery web application built as a DBMS mini project.

![BITE](https://img.shields.io/badge/BITE-Food%20Delivery-E63946?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)

---

## 🚀 About The Project

**BITE** is a Zomato-inspired food delivery website built using PHP, MySQL, HTML and CSS. It allows customers to browse restaurants, add items to cart, place orders and track delivery status in real time. Admins can manage orders and menu items from a dedicated dashboard.

---

## ✨ Features

### 👤 Customer Side
- 🔐 Register & Login with session management
- 🏪 Browse restaurants with search & filter
- 🍽️ View menu items by restaurant
- 🛒 Add to cart with quantity control
- 💳 Checkout with delivery details & payment method
- 📦 Real-time order tracking (Placed → Preparing → Out for Delivery → Delivered)
- 🧾 View full order history

### 🔧 Admin Side
- 📊 Dashboard with live stats (orders, revenue, customers)
- 🧾 Manage all orders & update delivery status
- 🍽️ Add new menu items & toggle availability
- 🔐 Secure admin login

---

## 🛠️ Tech Stack

| Technology | Usage |
|-----------|-------|
| PHP | Backend & server-side logic |
| MySQL | Database |
| HTML + CSS | Frontend structure & styling |
| Bootstrap 5 | Responsive UI components |
| Google Fonts (Inter) | Typography |
| XAMPP | Local development server |

---

## 🗄️ Database Schema

| Table | Description |
|-------|-------------|
| `customers` | Stores customer info & login |
| `restaurants` | Restaurant details & ratings |
| `menu_items` | Food items with price & category |
| `orders` | Order records with status |
| `order_items` | Individual items in each order |
| `delivery_persons` | Delivery staff details |
| `admins` | Admin login credentials |

---

## 📁 Project Structure
```
bite/
├── db.php              # Database connection
├── login.php           # Customer login
├── register.php        # Customer registration
├── index.php           # Home page with restaurants
├── menu.php            # Restaurant menu & cart
├── checkout.php        # Checkout page
├── place_order.php     # Order placement logic
├── track_order.php     # Live order tracking
├── my_orders.php       # Order history
├── logout.php          # Session logout
└── admin/
    ├── login.php       # Admin login
    ├── dashboard.php   # Admin dashboard
    ├── orders.php      # Order management
    ├── menu.php        # Menu management
    └── logout.php      # Admin logout
```

---

## ⚙️ How To Run Locally

1. Download and install **XAMPP** from [xampp.org](https://www.apachefriends.org)
2. Clone this repository into `C:\xampp\htdocs\bite`
3. Open **XAMPP Control Panel** and start **Apache** and **MySQL**
4. Open browser and go to `http://localhost/phpmyadmin`
5. Create a new database called `bite_db`
6. Import the SQL file or run the CREATE TABLE queries
7. Open `http://localhost/bite` in your browser

---

## 🔐 Default Login Credentials

### Customer
- Register a new account at `/register.php`

### Admin
| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `1234` |
| URL | `/admin/login.php` |

---

## 📸 Pages Overview

| Page | URL |
|------|-----|
| Home | `/index.php` |
| Login | `/login.php` |
| Register | `/register.php` |
| Menu | `/menu.php?id=1` |
| Track Order | `/track_order.php?id=1` |
| My Orders | `/my_orders.php` |
| Admin Panel | `/admin/login.php` |

---

## 👨‍💻 Developed By

**Tanmay** — DBMS Mini Project  
📍 Nagpur, Maharashtra  
🗓️ 2026

---

## 📝 Project Info

- **Subject:** Database Management System (DBMS)
- **Type:** Mini Project
- **Team Size:** 2 Students
- **Duration:** 15 Days

---

> *"Every bite, right on time"* 🍕
