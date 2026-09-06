# TravelBook - Travel Booking Management System

A complete travel booking management system built with PHP, MySQL, Bootstrap 5, GSAP animations, and modern UI/UX design. Perfect for college/final-year projects.

## Features

### User Side
- ✅ User Registration & Login
- ✅ Search & Filter Packages (price, category, date, location)
- ✅ Package Details with Images, Itinerary, Reviews
- ✅ Book Packages with Traveler Details
- ✅ Demo Payment Gateway
- ✅ Booking Confirmation with Invoice/Ticket
- ✅ My Bookings with History & Cancel Option
- ✅ Wishlist / Favorites
- ✅ User Profile Management
- ✅ Reviews & Ratings System
- ✅ Contact Us Page
- ✅ Google Maps Integration
- ✅ Currency Converter
- ✅ Live Chat Support (UI)
- ✅ GSAP + AOS Animations

### Admin Side
- ✅ Admin Login
- ✅ Dashboard with Analytics (Users, Bookings, Revenue)
- ✅ Add/Edit/Delete Packages
- ✅ Manage Users (Activate/Ban)
- ✅ Manage Bookings (Approve/Reject)
- ✅ Manage Reviews (Approve/Reject/Delete)
- ✅ Manage Coupons & Discounts
- ✅ View Contact Messages

## Database Tables
users, admins, packages, destinations, bookings, payments, reviews, wishlist, coupons, contacts, travelers

## Installation (XAMPP)

1. Copy the project folder to `C:/xampp/htdocs/travel-booking`
2. Start Apache and MySQL in XAMPP
3. Open phpMyAdmin (http://localhost/phpmyadmin)
4. Create database `travel_booking`
5. Import `database/travel_booking.sql`
6. Update `config/constants.php` with your SITE_URL if different
7. Visit http://localhost/travel-booking

## Default Credentials

**User:** john@example.com / password  
**Admin:**  /   
(Admin panel: http://localhost/travel-booking/admin)

## Tech Stack
- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5.3
- GSAP 3.12
- AOS (Animate On Scroll)
- Bootstrap Icons
- Google Fonts (Poppins + Playfair Display)

## Folder Structure
```
travel-booking/
├── admin/          # Admin panel
├── ajax/           # AJAX handlers
├── assets/
│   ├── css/        # Stylesheets
│   └── js/         # JavaScript
├── auth/           # Login/Register/Logout
├── config/         # Database & app config
├── database/       # SQL file
├── includes/       # Header, Footer templates
├── uploads/        # User uploads
├── user/           # User dashboard pages
├── index.php       # Homepage
├── packages.php    # Package listing
├── package-details.php
├── booking.php     # Booking form
├── payment.php     # Payment page
├── booking-confirmation.php
├── about.php
├── contact.php
├── destinations.php
└── README.md
```

## Security
- Prepared statements (SQL injection prevention)
- Password hashing (bcrypt)
- CSRF tokens
- Input sanitization
- Session management
