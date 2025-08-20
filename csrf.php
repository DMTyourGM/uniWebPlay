<?php
// csrf.php - CSRF token generation and validation utilities

/**
 * Generate a cryptographically secure CSRF token
 * @return string The generated token
 */
function generateCSRFToken() {
    return bin2hex(random_bytes(32));
}

/**
 * Get or create a CSRF token for the current session
 * @return string The CSRF token
 */
function getCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateCSRFToken();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a CSRF token
 * @param string $token The token to validate
 * @return bool True if valid, false otherwise
 */
function validateCSRFToken($token) {
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Clear the current CSRF token
 */
function clearCSRFToken() {
    unset($_SESSION['csrf_token']);
}
?>
