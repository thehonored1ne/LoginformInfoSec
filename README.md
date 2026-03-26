# Nexus - Information Security Management System

Nexus is a premium, secure web application built for an **Information Security** project. It showcases advanced authentication, session management, and defensive layers against common web vulnerabilities.

## 🚀 Key Features

*   **Custom JWT Authentication**: Lightweight JSON Web Token implementation for both Web (Cookie-based) and API (Bearer-based) sessions.
*   **Role-Based Access Control (RBAC)**: Distinct permissions and automatic redirection for `Admin` and `User` roles.
*   **Secure Password Reset**: Token-based password recovery integrated with Gmail SMTP and featuring 60-minute expiration.
*   **Interactive UI**: Modern, responsive design with glassmorphism, dynamic backgrounds, and real-time form validation.

## 🛡️ Security Implementation (OWASP Focused)

*   **Cryptography**: User passwords are secured using **SHA-256 with random 16-character salts** to prevent rainbow table attacks.
*   **Brute Force Protection**: 
    *   Multi-layer rate limiting on Login, Registration, and Password Reset.
    *   Smart UI countdowns and disabled buttons during lockout periods.
*   **Injection Defense**: 
    *   Full protection against **SQL Injection** through Eloquent ORM and Prepared Statements.
    *   Automatic **XSS (Cross-Site Scripting)** escaping via Blade Templating.
*   **JWT Integrity**: All sessions are signed with **HS256 HMAC** using a unique server-side secret.

## 🛠️ Installation & Setup

1.  **Clone the repository**:
    ```bash
    git clone https://github.com/thehonored1ne/LoginformInfoSec.git
    cd LoginformInfoSec
    ```

2.  **Install dependencies**:
    ```bash
    composer install
    npm install
    ```

3.  **Environment Configuration**:
    *   Duplicate `.env.example` to `.env`.
    *   Generate app key: `php artisan key:generate`.
    *   Configure your Database (SQLite by default).
    *   Configure **Gmail SMTP** for the mail system (requires an App Password).

4.  **Database Setup**:
    ```bash
    php artisan migrate
    ```

5.  **Run the application**:
    ```bash
    php artisan serve
    npm run dev
    ```

## 🧪 Security Testing & Audits

The project includes built-in security audit tests to demonstrate its defenses.

**Run All Tests**:
```bash
php artisan test
```

**Specific Security Audits**:
*   `php artisan test tests/Feature/SecurityAuditTest.php` (SQLi, XSS, RBAC, JWT)
*   `php artisan test tests/Feature/StressTest.php` (Rate Limiting & Flooding)
*   `php artisan test tests/Feature/PasswordResetTest.php` (Token logic)

## 📄 License
This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).
