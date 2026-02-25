# Hacki — Registration Web App

A 2-page registration app built with **HTML + CSS + vanilla JS** on the frontend and **PHP + MySQL** on the backend.

---

## Project Structure

```
Hacki/
├── index.html            ← Landing page
├── form.html             ← Registration form
├── assets/
│   ├── style.css         ← Design system
│   └── logo.png          ← Your logo (replace placeholder)
├── api/
│   ├── config.php        ← DB connection (edit credentials here)
│   └── register.php      ← POST endpoint for registration
├── database/
│   └── setup.sql         ← SQL to create DB & table
└── README.md             ← You are here
```

---

## Setup Instructions

### 1. Place Files

Copy the entire `Hacki/` folder into your web server's document root:

| Server   | Path                          |
|----------|-------------------------------|
| XAMPP    | `C:\xampp\htdocs\Hacki\`      |
| WAMP     | `C:\wamp64\www\Hacki\`        |
| MAMP     | `/Applications/MAMP/htdocs/Hacki/` |
| Linux    | `/var/www/html/Hacki/`        |

### 2. Create the Database

**Option A — phpMyAdmin**
1. Open `http://localhost/phpmyadmin`
2. Click **Import** → choose `database/setup.sql` → click **Go**

**Option B — MySQL CLI**
```bash
mysql -u root -p < database/setup.sql
```

### 3. Configure Database Credentials

Open **`api/config.php`** and update these values:

```php
$DB_HOST = 'localhost';      // your DB host
$DB_PORT = '3306';           // your DB port
$DB_NAME = 'hacki_app';      // database name
$DB_USER = 'root';           // your MySQL username
$DB_PASS = '';               // your MySQL password
```

### 4. Test End-to-End

1. Start Apache + MySQL (via XAMPP Control Panel, etc.)
2. Open `http://localhost/Hacki/index.html`
3. Click **Continue** → fill in the form → click **Create Account**
4. ✅ You should see a success message and be redirected after 3 seconds
5. Submit the same email/username again to verify the duplicate-error handling

---

## Notes

- **Logo**: Replace `assets/logo.png` with your own image. The page gracefully shows a colored circle if the image is missing.
- **Password storage**: Passwords are stored in **plain text** as specified. For production apps, always use `password_hash()`.
