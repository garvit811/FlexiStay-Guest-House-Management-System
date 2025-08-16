# 🏨 FlexiStay – Guest House Management System

A web-based guest house booking and management platform developed using **PHP**, **MySQL**, **HTML**, **CSS**, and **JavaScript**, running locally on **XAMPP**. The system allows a company to manage multiple guest houses and supports three user roles: **Admin**, **Clerk**, and **Guest**.

---

## 🚀 Features

### 👤 Role-Based Access
- **Admin**: Full control over guesthouses, rooms, users, and bookings.
- **Clerk**: Allot rooms to guests manually and check availability.
- **Guest**: View available guesthouses, book rooms, and manage bookings.

### 📋 Admin Capabilities
- Add/deactivate guest houses
- Add/view rooms and beds
- View all users (guests and clerks)
- View all bookings sorted by guesthouse

### 🛏️ Guest Features
- Browse available guest houses
- View room details and prices
- Book rooms and track bookings

### 🧾 Clerk Tools
- View room/bed status
- Manually allot rooms to guests

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScrip
- **Backend**: PHP (7.4+)
- **Database**: MySQL
- **Environment**: XAMPP

---

## ⚙️ Setup Instructions

1. **Clone this repository**:
   ```bash
   git clone https://github.com/garvit811/FlexiStay-Guest-House-Management-System
   ```
2. **Move project to XAMPP**:
Copy the project folder into your htdocs directory (e.g., C:/xampp/htdocs/FlexiStay).

3. **Start XAMPP**:
Run Apache and MySQL services from the XAMPP Control Panel.

4. **Create the database**:
- Open phpMyAdmin
- Create a database (e.g., flexistay)
- Import the SQL file from the project folder (database.sql)

5. **Configure database connection**:
- Open includes/dbh.inc.php
- Update database credentials
```bash
$host = "localhost";
$user = "root";
$password = "";
$dbname = "flexistay";
```

6. **Access the app**:
Open ``` http://localhost/FlexiStay ``` in your browser 🎉

---

## 📂 Project Structure
<pre>
   FlexiStay-Guest-House-Management-System/
│── admin/ # Admin dashboard (manage guesthouses, rooms, users, bookings)
│── assets/ # CSS, JS, images, and frontend assets
│── auth/ # Authentication (login, signup, sessions)
│── clerk/ # Clerk dashboard (room allotment, availability check)
│── database/ # Database schema (flexistay.sql)
│── guest/ # Guest dashboard (browse, book, manage bookings)
│── includes/ # Reusable PHP includes (db connection, etc.)
│── vendor/ # External libraries(for downloading invoice)
│── index.php # Landing page
│── login.php # Login page
│── readme.md # Project documentation
</pre>

---

## 👥 User Roles (Demo Accounts)
### Admin: 
- Username : soni@admin.com
- Password : admin
### Clerk:
Register directly from the app
### Guest:
Register directly from the app

---

## 🚀 Future Enhancements
- Online payment integration (Razorpay/PayPal)
- Email notifications for bookings
- Analytics dashboard for Admin
- Responsive UI with Bootstrap/Tailwind
