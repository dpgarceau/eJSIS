<?php
/**
 * eJSIS Email Configuration
 *
 * Copy this file to jsis_emailconfig.php and update with your settings
 */

// SMTP Settings (leave SMTP_USER empty to use PHP mail())
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');  // Leave empty to use PHP mail()
define('SMTP_PASS', '');

// From address
define('FROM_EMAIL', 'noreply@ejsis.isstrckr.com');
define('FROM_NAME', 'eJSIS System');

// Support email - receives copy of all submissions
define('SUPPORT_EMAIL', 'david.garceau@gemaire.com');
