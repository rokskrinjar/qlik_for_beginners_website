<?php
// Include this file at the top of every protected page
require_once '../includes/auth.php';

// Check if user has a paid subscription
$auth->requireLogin();

// Get the user's subscription type
$subscriptionType = $_SESSION['subscription_type'] ?? '';

// For most premium content, require a paid subscription
if ($subscriptionType === 'free') {
    // Free users can only access specific free-tier content
    // You can create different middleware files for different access levels
    
    // Get the current script name
    $currentPage = basename($_SERVER['SCRIPT_NAME']);
    
    // List of pages that free users can access
    $freeAccessPages = [
        'free_preview.php',
        'community.php',
        'basic_resources.php'
        // Add any other free-tier pages here
    ];
    
    // If the current page is not in the free access list, redirect
    if (!in_array($currentPage, $freeAccessPages)) {
        header('Location: ../subscription.php');
        exit;
    }
}

// If the code reaches here, the user is authorized to view the content