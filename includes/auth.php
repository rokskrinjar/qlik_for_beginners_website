<?php
require_once 'db.php';

class Auth {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
        
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public function register($email, $password, $fullName) {
        // Check if user already exists
        $existingUser = $this->db->findOne("SELECT * FROM users WHERE email = :email", [
            ':email' => $email
        ]);
        
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'Email already registered'
            ];
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        // Insert user
        $userId = $this->db->insert('users', [
            'email' => $email,
            'password' => $hashedPassword,
            'full_name' => $fullName
        ]);
        
        return [
            'success' => true,
            'user_id' => $userId,
            'message' => 'Registration successful'
        ];
    }
    
    public function login($email, $password) {
        $user = $this->db->findOne("SELECT * FROM users WHERE email = :email", [
            ':email' => $email
        ]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        // Check if user has active subscription
        $subscription = $this->db->findOne(
            "SELECT * FROM subscriptions 
             WHERE user_id = :user_id 
             AND status = 'active' 
             AND (end_date IS NULL OR end_date > NOW())",
            [':user_id' => $user['id']]
        );
        
        $hasActiveSubscription = $subscription ? true : false;
        
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['is_logged_in'] = true;
        $_SESSION['has_subscription'] = $hasActiveSubscription;
        
        return [
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'email' => $user['email'],
                'full_name' => $user['full_name'],
                'has_subscription' => $hasActiveSubscription
            ],
            'message' => 'Login successful'
        ];
    }
    
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        return [
            'success' => true,
            'message' => 'Logout successful'
        ];
    }
    
    public function createSubscription($userId, $planType) {
        // Calculate subscription end date
        if ($planType == 'free') {
            // Free subscriptions don't expire
            $endDate = null;
        } else {
            $duration = ($planType == 'monthly') ? MONTHLY_DURATION : YEARLY_DURATION;
            $endDate = date('Y-m-d H:i:s', strtotime("+$duration days"));
        }
        
        // Insert subscription
        $subscriptionId = $this->db->insert('subscriptions', [
            'user_id' => $userId,
            'plan_type' => $planType,
            'status' => 'active',
            'end_date' => $endDate
        ]);
        
        // Update session if this is the current user
        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId) {
            $_SESSION['has_subscription'] = true;
            $_SESSION['subscription_type'] = $planType;
        }
        
        return [
            'success' => true,
            'subscription_id' => $subscriptionId,
            'message' => 'Subscription created successfully'
        ];
    }
    
    public function checkSubscription() {
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
            return false;
        }
        
        return isset($_SESSION['has_subscription']) && $_SESSION['has_subscription'] === true;
    }
    
    public function requireLogin() {
        if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
            header('Location: /login.html');
            exit;
        }
    }
    
    public function requireSubscription() {
        $this->requireLogin();
        
        if (!$this->checkSubscription()) {
            header('Location: /subscription.php');
            exit;
        }
    }
    
    public function generatePasswordResetToken($email) {
        $user = $this->db->findOne("SELECT * FROM users WHERE email = :email", [
            ':email' => $email
        ]);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Email not found'
            ];
        }
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Delete any existing tokens for this email
        $this->db->query(
            "DELETE FROM password_resets WHERE email = :email",
            [':email' => $email]
        );
        
        // Insert new token
        $this->db->insert('password_resets', [
            'email' => $email,
            'token' => $token,
            'expires_at' => $expiresAt
        ]);
        
        return [
            'success' => true,
            'token' => $token,
            'message' => 'Password reset token generated'
        ];
    }
    
    public function resetPassword($token, $newPassword) {
        $resetRequest = $this->db->findOne(
            "SELECT * FROM password_resets 
             WHERE token = :token 
             AND expires_at > NOW()",
            [':token' => $token]
        );
        
        if (!$resetRequest) {
            return [
                'success' => false,
                'message' => 'Invalid or expired token'
            ];
        }
        
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        // Update user's password
        $this->db->update(
            'users',
            ['password' => $hashedPassword],
            'email = :email',
            [':email' => $resetRequest['email']]
        );
        
        // Delete the used token
        $this->db->query(
            "DELETE FROM password_resets WHERE token = :token",
            [':token' => $token]
        );
        
        return [
            'success' => true,
            'message' => 'Password reset successful'
        ];
    }
}

$auth = new Auth($db);