# 🏋️ Gym-Core Management System

A complete web-based **Gym Management System** developed using **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript** to streamline gym operations including member management, trainer management, subscriptions, supplement sales, and payment handling.

---

## 📖 Overview

Gym-Core is a centralized platform designed to simplify the day-to-day operations of a fitness center. The system provides separate functionalities for administrators, trainers, and gym members while maintaining an efficient workflow for managing memberships, subscriptions, supplements, and payments.

The application eliminates manual record keeping and provides an organized environment for handling gym activities digitally.

---

## ✨ Key Features

### 🔐 Authentication System

* User Registration
* User Login
* Session Management
* Role-Based Access Control
* Secure Logout Functionality

---

### 👨‍💼 Admin Features

* Manage Members
* Manage Trainers
* Manage Subscription Plans
* Manage Supplement Products
* Manage Supplement Orders
* Manage Payments
* Manage User Accounts
* Monitor Gym Activities
* Update Membership Information

---

### 🏋️ Trainer Features

* Trainer Dashboard
* View Assigned Members
* Manage Personal Information
* Track Member Progress

---

### 👤 Member Features

* Register and Login
* Update Profile
* View Membership Packages
* Subscribe to Gym Plans
* Purchase Supplements
* Track Orders
* Manage Personal Information

---

### 🥤 Supplement Management

* Add Supplements
* Edit Supplements
* Delete Supplements
* Manage Stock Availability
* Process Customer Orders
* Track Purchased Supplements

---

### 💳 Subscription & Payment Management

* Membership Registration
* Subscription Processing
* Package Management
* Payment Tracking
* Subscription Updates
* Membership Renewal Handling

---

## 🛠️ Technologies Used

### Backend

* PHP

### Database

* MySQL

### Frontend

* HTML5
* CSS3
* JavaScript

### Development Tools

* Visual Studio Code
* XAMPP / WAMP Server
* phpMyAdmin
* Git
* GitHub

---

## 📂 Project Modules

### Authentication Module

Handles user registration, login, logout, and session management.

### Member Management Module

Allows administrators to manage gym members and membership information.

### Trainer Management Module

Provides functionality for managing trainer records and trainer-related activities.

### Subscription Management Module

Handles membership packages, subscription plans, renewals, and updates.

### Supplement Management Module

Manages supplement inventory, supplement sales, and product information.

### Payment Management Module

Tracks payments, subscriptions, and order transactions.

### Profile Management Module

Allows users to update their profile information and account details.

---

## 📁 Project Structure

```text
Gym-Core/
│
├── uploads/
├── images/
├── admin_dashboard.php
├── user_dashboard.php
├── trainer_dashboard.php
├── login.php
├── register.php
├── logout.php
├── profile.php
├── supplement.php
├── subscription.php
├── cart.php
├── contact.php
├── index.php
└── database/
```

---

## ⚙️ Installation Guide

### Step 1: Clone Repository

```bash
git clone https://github.com/Lakindu/Gym-Core.git
```

### Step 2: Move Project Folder

For XAMPP:

```text
C:\xampp\htdocs\
```

For WAMP:

```text
C:\wamp64\www\
```

### Step 3: Start Services

Start:

* Apache
* MySQL

using XAMPP or WAMP Control Panel.

### Step 4: Create Database

Open:

```text
http://localhost/phpmyadmin
```

Create a database.

Example:

```sql
gym_core
```

Import the SQL file into the database.

### Step 5: Configure Database Connection

Update your database connection settings inside the project.

Example:

```php
$host = "localhost";
$user = "root";
$password = "";
$database = "gym_core";
```

### Step 6: Run Project

Open:

```text
http://localhost/Gym-Core
```

---

## 👥 System Users

### Administrator

The administrator has full access to the system including:

* Member Management
* Trainer Management
* Supplement Management
* Subscription Management
* Payment Monitoring

### Trainer

Trainers can:

* Access Trainer Dashboard
* Manage Assigned Information
* View Member Details

### Member

Members can:

* Register Accounts
* Login
* Purchase Supplements
* Subscribe to Packages
* Manage Profiles

---

## 🎯 Objectives

* Digitize gym operations
* Improve member management
* Reduce paperwork
* Simplify payment tracking
* Manage supplement sales efficiently
* Enhance user experience

---

## 🔒 Security Features

* Authentication System
* Session Validation
* Access Control
* Input Validation
* Secure User Management

---

## 👨‍💻 Developer

**Lakindu Ransika**

University Student | Software Developer

GitHub:
https://github.com/Lakindu

---

## ⭐ Project Status

Completed Academic Project

---

## 📄 License

This project was developed for educational and learning purposes.
