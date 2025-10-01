# 🏨 FlexiStay – Guest House Management System

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?logo=javascript&logoColor=black)](https://developer.mozilla.org/docs/Web/JavaScript)
[![XAMPP](https://img.shields.io/badge/XAMPP-Localhost-F37623?logo=xampp&logoColor=white)](https://www.apachefriends.org/)
[![License](https://img.shields.io/badge/License-MIT-green)](./LICENSE)

---

FlexiStay is a comprehensive guest house management system designed to streamline the booking and administration process for companies managing multiple properties. Built with PHP and MySQL, it offers a centralized platform to handle reservations, room allocation, and user management, reducing manual overhead and improving efficiency for admins, clerks, and guests.


---

## ✨ Key Features

-   🔐 **Role-Based Access Control:** A secure system with three distinct user roles: **Admin**, **Clerk**, and **Guest**, each with a dedicated dashboard and permissions.
-   🏢 **Centralized Admin Dashboard:**
    -   Manage multiple guest houses and their rooms.
    -   Oversee all user accounts (clerks and guests).
    -   View a comprehensive list of all bookings across all properties.
-   👤 **Guest Self-Service Portal:**
    -   Browse available guest houses and view room details.
    -   Book rooms based on availability.
    -   View and manage personal booking history.
-   📋 **Clerk Management Tools:**
    -   Check real-time room and bed availability status.
    -   Perform manual room allotments for guests.
    -   Generate and download invoices for bookings.

---

## 🛠️ Tech Stack

-   **Backend:** PHP (7.4+)
-   **Database:** MySQL
-   **Frontend:** HTML5, CSS3, Vanilla JavaScript
-   **Local Development Environment:** XAMPP (Apache, MySQL)

---

## ⚙️ Getting Started

Follow these instructions to set up the project on your local machine.

### 1. Set Up The Environment

1.  **Install XAMPP:** Download and install [XAMPP](https://www.apachefriends.org/index.html).
2.  **Start Services:** Launch the XAMPP Control Panel and start the **Apache** and **MySQL** services.

### 2. Get The Code

1.  **Clone the repository** into your XAMPP `htdocs` directory:
    ```bash
    cd C:/xampp/htdocs
    git clone https://github.com/garvit811/FlexiStay-Guest-House-Management-System
    ```
2.  **Rename the folder** (optional) for a cleaner URL:
    ```bash
    mv FlexiStay-Guest-House-Management-System FlexiStay
    ```

### 3. Configure The Database

1.  **Open phpMyAdmin** by navigating to `http://localhost/phpmyadmin` in your browser.
2.  **Create a new database** and name it `flexistay`.
3.  **Import the schema:** Select the `flexistay` database, click on the **Import** tab, and upload the `flexistay.sql` file located inside the `database` folder of the project.
4.  **Update connection details:** Open the `includes/dbh.inc.php` file and ensure the database credentials match your local setup (the default XAMPP credentials are shown below).
    ```php
    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "flexistay";
    ```

### 4. Run The Application

🎉 You're all set! Open your browser and navigate to **`http://localhost/FlexiStay`** to access the application.

---

## 🚀 Usage & Demo Credentials

After setup, you can explore the different roles:

-   **Admin:**
    -   **Username:** `soni@admin.com`
    -   **Password:** `admin`
-   **Clerk & Guest:**
    -   You can register new Clerk and Guest accounts directly from the application's registration page.

---

## 🗺️ Roadmap

Here are some planned features for future development:

-   [ ] Online payment gateway integration (e.g., Razorpay, PayPal).
-   [ ] Automated email notifications for booking confirmations and reminders.
-   [ ] An analytics dashboard for the Admin to track revenue and occupancy rates.
-   [ ] A more modern, responsive UI using a framework like Bootstrap or Tailwind CSS.

---

## 🤝 Contributing

Contributions are what make the open-source community such an amazing place. Any contributions you make are **greatly appreciated**.

1.  Fork the Project
2.  Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the Branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

---

## 📜 License

This project is licensed under the MIT License. See the `LICENSE` file for more details.
