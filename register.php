<?php
require_once 'includes/auth.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $planType = in_array($_POST['plan_type'] ?? '', ['free', 'monthly', 'yearly']) ? $_POST['plan_type'] : 'free';
    
    // Validate input
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email.";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    
    if (empty($fullName)) {
        $errors[] = "Full name is required.";
    }
    
    if (empty($errors)) {
        // Register the user
        $result = $auth->register($email, $password, $fullName);
        
        if ($result['success']) {
            // Create subscription
            $subscriptionResult = $auth->createSubscription($result['user_id'], $planType);
            
            if ($subscriptionResult['success']) {
                // Log the user in
                $auth->login($email, $password);
                
                // Redirect to learning page
                header("Location: learn.html");
                exit;
            } else {
                $error = $subscriptionResult['message'];
            }
        } else {
            $error = $result['message'];
        }
    } else {
        $error = implode("<br>", $errors);
    }
}

// Include the registration form (use the same login.html with a parameter)
header("Location: login.html?tab=subscribe");
exit;