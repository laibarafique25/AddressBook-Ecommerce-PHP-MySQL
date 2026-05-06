# Address Book - Modern E-Commerce Platform

Address Book is a premium, fully functional Core PHP e-commerce website designed for the fashion, jewelry, and cosmetics industry. This project was developed as a second-semester group project to demonstrate advanced web development concepts, including dynamic content management, secure authentication, and session-based user interactions.

## 🎓 Academic Context
- **Batch:** 2506E3
- **Course:** DISM (Diploma in Software Management) - 2nd Semester Project
- **Developed by:** 
  - Laiba Rafique 
Misbah Khurshid
  - Alishbah Shamim

---

## 🌟 Key Features

### 🛒 Shopping Experience
- **Dynamic Product Grid:** Browsing products by main categories, sub-categories, and child categories.
- **Advanced Search & Filtering:** Find products easily using the integrated search and filtering logic.
- **Guest Mode:** Browse, add to cart, and manage wishlists without needing an account.
- **Cart Merging:** Guest cart and wishlist data automatically syncs to your account upon login.
- **Product Reviews:** Users can submit ratings and reviews for products.

### 🔐 Security & Authentication
- **Secure Login:** robust authentication system with password hashing (BCrypt).
- **Session Management:** Secure session handling for persistent user data across pages.
- **Admin Panel:** Specialized dashboard for managing products, categories, orders, and users.

### 🎨 Design & UI
- **Premium Aesthetics:** Modern, clean UI with high-quality banners and smooth transitions.
- **Responsive Layout:** Fully optimized for Mobile, Tablet, and Desktop views.
- **Interactive Notifications:** SweetAlert2 integration for beautiful, user-friendly alerts.

---

## 🛠️ Technology Stack
- **Frontend:** HTML5, CSS3, JavaScript (jQuery), Bootstrap 4.
- **Backend:** Core PHP (Procedural/Modular).
- **Database:** MySQL.
- **Authentication:** PHP Sessions + Password Hashing.

---

## 📁 Project Structure
The project follows a professional modular architecture for better maintainability:

- `/assets/`: CSS, JS, Fonts, and Image assets.
- `/includes/`: Reusable components like `header.php`, `footer.php`, and `db.php`.
- `/pages/`: Main user-facing pages (`index`, `shop`, `checkout`, `cart`, etc.).
- `/auth/`: Authentication logic (`login`, `signup`, `logout`).
- `/actions/`: Backend functional handlers for AJAX and form submissions.
- `/admin/`: Full administrative control panel.

---

## 🚀 Installation & Setup

1. **Prerequisites:**
   - Install [XAMPP](https://www.apachefriends.org/) or any local PHP server.
   - Ensure MySQL is running.

2. **Clone/Copy Project:**
   - Place the project folder into your `htdocs` directory.

3. **Database Setup:**
   - Open PHPMyAdmin and create a database named `eproject`.
   - Import the provided `.sql` file (if available) or create the necessary tables for users, products, and categories.

4. **Configuration:**
   - Update `includes/db.php` with your database credentials if different from defaults.

5. **Run:**
   - Navigate to `http://localhost/address book 2/` in your browser.

---

## 📄 License
This project was built for academic purposes as part of the DISM curriculum.
