<?php
/**
 * Configure WordPress to use MailCatcher for all emails
 *
 * This must-use plugin ensures all emails are sent to MailCatcher
 * running on mailcatcher:1025 within the Docker network
 */

// Configure PHPMailer to use MailCatcher
add_action('phpmailer_init', function($phpmailer) {
    // Use SMTP
    $phpmailer->isSMTP();

    // Set the MailCatcher host (Docker service name)
    $phpmailer->Host = 'mailcatcher';

    // Set the SMTP port
    $phpmailer->Port = 1025;

    // No authentication required for MailCatcher
    $phpmailer->SMTPAuth = false;
    
    // Disable encryption for MailCatcher
    $phpmailer->SMTPAutoTLS = false;
    $phpmailer->SMTPSecure = '';

    // Set timeout
    $phpmailer->Timeout = 10;

    // Set a valid from address
    $phpmailer->From = 'wordpress@local.dev';
    $phpmailer->FromName = 'WordPress Site';

    // Debug output for testing (uncomment to see SMTP conversation)
    // $phpmailer->SMTPDebug = 2;
});

// Override wp_mail from address to ensure valid format
add_filter('wp_mail_from', function($from_email) {
    // Ensure we always have a valid email format
    if (empty($from_email) || !is_email($from_email)) {
        return 'wordpress@local.dev';
    }
    return $from_email;
});

add_filter('wp_mail_from_name', function($from_name) {
    if (empty($from_name) || $from_name === 'WordPress') {
        return 'WordPress Development Site';
    }
    return $from_name;
});

// Log email attempts for debugging
add_action('wp_mail_failed', function($error) {
    error_log('WordPress Mail Failed: ' . $error->get_error_message());
});
