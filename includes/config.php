<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'univer78_admin');
define('DB_PASS', 'For33#Fun!');
define('DB_NAME', 'univer78_qlik');

// Site configuration
define('SITE_URL', 'https://universalsin.com/'); // Change to your domain
define('ADMIN_EMAIL', 'rok.skrinjar@gmail.com');

// Subscription prices (in USD)
define('MONTHLY_PRICE', 9.99);
define('YEARLY_PRICE', 99.00);

// Subscription durations (in days)
define('MONTHLY_DURATION', 30);
define('YEARLY_DURATION', 365);

// Security
define('HASH_COST', 12); // For password hashing

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // Use this if your site uses HTTPS

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 during development, 0 in production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');