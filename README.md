# 🌱 Farm Fresh — Smart Farming & Online Marketplace

A PHP and MySQL-based web application designed to connect farmers and buyers through an online marketplace for farm-fresh products.

## 📌 About the Project

**Farm Fresh** is a web-based farming marketplace that helps organize product listings, buyer orders, farmer activities, and delivery management in one system. It also includes administrative pages for managing users, products, orders, and analytics.

This project demonstrates how web technologies can support digital agriculture and simplify the process of buying and selling farm products.

## ✨ Features

### 👨‍🌾 Farmer
- Farmer registration and login
- Farmer dashboard
- Add and manage products
- View products and orders
- Access farmer reports

### 🛒 Buyer
- Buyer registration and login
- Browse available products
- Place and manage orders
- Track order status
- Wishlist and feedback features
- Receive buyer notifications

### 🚚 Delivery Management
- Delivery dashboard
- View assigned orders
- Update delivery status

### 🛡️ Admin
- Admin login and dashboard
- Manage farmers and buyers
- Manage products and orders
- Assign deliveries
- View analytics and reports
- 
## 🛠️ Technology Stack

- **Frontend:** HTML, CSS
- **Backend:** PHP
- **Database:** MySQL
- **Local Development Environment:** XAMPP
- **Version Control:** Git and GitHub

## ⚙️ Run Locally

### Prerequisites
- XAMPP or another compatible PHP/MySQL environment
- A web browser
- Git (optional, if downloading the ZIP instead)

### Installation Steps

1. Clone or download this repository.
2. If using XAMPP, copy the project folder into the `htdocs` directory.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Open phpMyAdmin at `http://localhost/phpmyadmin`.
5. Create a database named `farming_system`.
6. Import the project's SQL database file, if provided. If no SQL file is included, the database tables must be created before the application can work.
7. Configure the database connection in `db.php` using your local database settings.
8. Open the application in your browser:

   `http://localhost/Farm-Fresh-/`

   If you use a different folder name inside `htdocs`, replace `Farm-Fresh-` with that folder name.

## 📂 Project Structure

The repository contains PHP pages for the main application modules, including:

- `index.php` — application entry page
- `farmer_dashboard.php` — farmer dashboard
- `buyer_dashboard.php` — buyer dashboard
- `delivery_dashboard.php` — delivery dashboard
- `admin_dashboard.php` — admin dashboard
- `product_list.php` — product listing
- `db.php` — database connection
- `style.css` — stylesheet

Additional PHP files handle authentication, products, orders, delivery operations, and related features.

## 🔐 Security Note

Use your own local database credentials. Never commit real passwords, API keys, or private customer data to a public repository.

## 🚀 Future Enhancements

- Online deployment with secure database hosting
- Responsive UI improvements
- Secure payment integration
- Product search and filtering
- Enhanced order tracking and reporting

## 👩‍💻 Author

**Bhavana Gaikwad**

GitHub: [@bhavanagaikwad108-cpu](https://github.com/bhavanagaikwad108-cpu)

## 📎 Repository

[View Farm Fresh on GitHub](https://github.com/bhavanagaikwad108-cpu/Farm-Fresh-)

*Farm Fresh — Connecting Farmers, Buyers, and Fresh Products.*
