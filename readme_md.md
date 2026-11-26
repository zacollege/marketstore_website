# Market Store Website (Warung Reza)

A web-based food ordering and store management application built with **PHP** and **SQL**. This project provides a complete storefront for customers to browse menus, manage a shopping cart, complete checkouts (including QRIS payment), and track orders, alongside an administrative dashboard for managing products, categories, and incoming orders.

## Purpose
This project was developed to streamline online food/product ordering and store operations through a lightweight, easy-to-deploy PHP and SQL web platform.

## Key Features

### Customer Side
- **Menu & Storefront (`index.php`)**: Browse available items and promotional banners (`banner.png`).
- **User Authentication (`login.php`, `sign_up.php`, `backend/proses_login.php`, `backend/proses_signup.php`, `backend/logout.php`)**: Account registration, login, and session management.
- **Shopping Cart & Checkout (`keranjang.php`, `backend/proses_keranjang.php`, `backend/proses_checkout.php`)**: Add, update, or remove items from the cart and submit orders.
- **Payment & Order Tracking (`pesanan.php`, `backend/proses_bayar.php`, `backend/proses_batal.php`, `modal_batal.php`, `qris.png`, `qris.svg`)**: View order history, track order status, complete payments via QRIS, and request order cancellations.
- **Notifications (`notifikasi.php`)**: Receive updates regarding order progress and account activity.

### Admin Panel
- **Admin Dashboard (`admin.php`)**: Central hub for store administration.
- **Menu Management (`admin_menu.php`, `backend/proses_menu.php`)**: Create, update, and remove store menu items.
- **Category Management (`admin_kategori.php`, `backend/proses_kategori.php`)**: Organize items into categories.
- **Order Management (`admin_pesanan.php`, `backend/proses_status.php`)**: Monitor customer orders and update order statuses.

## Tech Stack
- **Backend**: PHP
- **Frontend**: HTML, CSS (`style.css`)
- **Database**: MySQL / MariaDB (`warungreza.sql`)

## Project Structure
```text
├── backend/                # Database connection and form/action handlers
│   ├── koneksi.php
│   ├── logout.php
│   ├── proses_batal.php
│   ├── proses_bayar.php
│   ├── proses_checkout.php
│   ├── proses_kategori.php
│   ├── proses_keranjang.php
│   ├── proses_login.php
│   ├── proses_menu.php
│   ├── proses_signup.php
│   └── proses_status.php
├── admin.php               # Admin main dashboard
├── admin_kategori.php      # Admin category management page
├── admin_menu.php          # Admin menu management page
├── admin_pesanan.php       # Admin order management page
├── footer.php              # Reusable footer component
├── header.php              # Reusable header/navigation component
├── index.php               # Main storefront page
├── keranjang.php           # Shopping cart page
├── login.php               # User login page
├── modal_batal.php         # Order cancellation modal
├── notifikasi.php          # User notifications page
├── pesanan.php             # Customer order list & payment page
├── sign_up.php             # User registration page
├── style.css               # Main stylesheet
├── warungreza.sql          # Database schema and initial data
├── banner.png              # Storefront banner image
├── qris.png                # QRIS payment image
└── qris.svg                # QRIS vector asset
```

## Getting Started

1. **Clone or Extract the Project**
   Place the project folder inside your local web server's root directory (e.g., `htdocs` in XAMPP or `www` in Laragon).

2. **Set Up the Database**
   - Open your database manager (such as phpMyAdmin).
   - Create a new database.
   - Import the `warungreza.sql` file into the newly created database.

3. **Configure Database Connection**
   - Open `backend/koneksi.php`.
   - Update the database host, username, password, and database name to match your local environment.

4. **Run the Application**
   - Start Apache and MySQL on your local server.
   - Open your browser and navigate to `http://localhost/<your-project-folder>/`.