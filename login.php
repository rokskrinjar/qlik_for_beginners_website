<?php

require_once 'includes/auth.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Attempt login
        $result = $auth->login($email, $password);
        
        if ($result['success']) {
            // Redirect based on subscription status
            if ($result['user']['has_subscription']) {
                // Redirect to learning page or last visited protected page
                $redirect = $_SESSION['redirect_after_login'] ?? 'learn.html';
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
                exit;
            } else {
                // Redirect to subscription page
                header("Location: subscription.php");
                exit;
            }
        } else {
            $error = $result['message'];
        }
    }
}

// If this is a page access attempt that required login, save the intended URL
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sign in or subscribe to access premium Qlik tutorials and learning resources for beginners.">
    <meta name="keywords" content="Qlik login, Qlik subscription, Qlik Sense tutorial access">
    <title>Login or Subscribe | Qlik Beginners</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Font Awesome for icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
</head>
<body>
    <header>
        <div class="container">
            <div class="navbar">
                <a href="index.html" class="logo">Qlik Beginners</a>
                <div class="nav-links">
                    <a href="index.html">Home</a>
                    <a href="learn.html">Learn Basics</a>
                    <a href="learn.html#resources">Resources</a>
                    <a href="index.html#feedback">Feedback</a>
                </div>
            </div>
            <div class="page-header">
                <h1>Login or Subscribe</h1>
                <p>Access premium Qlik learning content for beginners</p>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="login-container">
            <?php if (isset($error)): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="login-tabs">
                <div class="login-tab <?php echo isset($_GET['tab']) && $_GET['tab'] === 'subscribe' ? '' : 'active'; ?>" data-tab="login">Login</div>
                <div class="login-tab <?php echo isset($_GET['tab']) && $_GET['tab'] === 'subscribe' ? 'active' : ''; ?>" data-tab="subscribe">Subscribe</div>
            </div>
            
            <div class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] === 'subscribe' ? '' : 'active'; ?>" id="login-tab">
                <form id="login-form" action="login.php" method="post">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-primary">Sign In</button>
                    </div>
                    <p style="text-align: center; margin-top: 1rem;">
                        <a href="#" style="color: var(--primary);">Forgot password?</a>
                    </p>
                </form>
            </div>
            
            <div class="tab-content <?php echo isset($_GET['tab']) && $_GET['tab'] === 'subscribe' ? 'active' : ''; ?>" id="subscribe-tab">
                <p style="margin-bottom: 1.5rem;">Choose the subscription plan that works best for you:</p>
                
                <form id="subscribe-form" action="register.php" method="post">
                    <div class="subscription-options">
                        <div class="subscription-option" data-plan="free">
                            <h3>Free</h3>
                            <div class="subscription-price">$0<span style="font-size: 0.9rem; color: #6c757d;"> Forever</span></div>
                            <ul class="benefits-list">
                                <li>Basic learning resources</li>
                                <li>Preview of premium content</li>
                                <li>Community access</li>
                            </ul>
                            <input type="radio" name="plan_type" value="free" id="free-plan" checked style="display:none;">
                        </div>
                        <div class="subscription-option" data-plan="monthly">
                            <h3>Monthly</h3>
                            <div class="subscription-price">$9.99/month</div>
                            <ul class="benefits-list">
                                <li>Access to all premium chapters</li>
                                <li>New tutorials every month</li>
                                <li>Cancel anytime</li>
                            </ul>
                            <input type="radio" name="plan_type" value="monthly" id="monthly-plan" style="display:none;">
                        </div>
                        <div class="subscription-option" data-plan="yearly">
                            <h3>Yearly</h3>
                            <div class="subscription-price">$99/year <span style="font-size: 0.9rem; color: #6c757d;">Save 17%</span></div>
                            <ul class="benefits-list">
                                <li>Access to all premium chapters</li>
                                <li>New tutorials every month</li>
                                <li>Priority support</li>
                                <li>Exclusive webinars</li>
                            </ul>
                            <input type="radio" name="plan_type" value="yearly" id="yearly-plan" style="display:none;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="sub-email">Email Address</label>
                        <input type="email" id="sub-email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="sub-password">Create Password</label>
                        <input type="password" id="sub-password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn-primary">Subscribe Now</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
            </div>
            <p>© <span id="year">2025</span> Qlik Beginners. All rights reserved.</p>
        </div>
    </footer>

    <a href="#" class="back-to-top" id="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <script>
        // Update copyright year
        document.getElementById('year').textContent = new Date().getFullYear();
        
        // Tab functionality
        document.querySelectorAll('.login-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs
                document.querySelectorAll('.login-tab').forEach(t => {
                    t.classList.remove('active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Hide all tab contents
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                
                // Show the corresponding tab content
                const tabId = this.getAttribute('data-tab') + '-tab';
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Subscription option selection
        document.querySelectorAll('.subscription-option').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.subscription-option').forEach(o => {
                    o.classList.remove('selected');
                });
                this.classList.add('selected');
                
                // Select the radio button
                const planType = this.getAttribute('data-plan');
                document.getElementById(planType + '-plan').checked = true;
            });
        });
        
        // Set default selected plan
        <?php if (isset($_GET['tab']) && $_GET['tab'] === 'subscribe'): ?>
            document.querySelector('.subscription-option[data-plan="free"]').classList.add('selected');
        <?php endif; ?>
        
        // Back to top functionality
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('back-to-top');
            if (window.pageYOffset > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        
        document.getElementById('back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>

<div class="subscription-container">
    <div class="subscription-plan">
        <h3>Free</h3>
        <p class="price">$0 Forever</p>
        <ul>
            <li>Basic learning resources</li>
            <li>Preview of premium content</li>
            <li>Community access</li>
        </ul>
        <button class="subscribe-button">Get Started</button>
    </div>
    
    <div class="subscription-plan">
        <h3>Monthly</h3>
        <p class="price">$9.99/month</p>
        <ul>
            <li>Access to all premium chapters</li>
            <li>New tutorials every month</li>
            <li>Cancel anytime</li>
        </ul>
        <button class="subscribe-button">Subscribe Monthly</button>
    </div>

    <div class="subscription-plan">
        <h3>Yearly</h3>
        <p class="price">$99/year <span style="font-size: 0.8rem; color: #888;">Save 17%</span></p>
        <ul>
            <li>Access to all premium chapters</li>
            <li>New tutorials every month</li>
            <li>Priority support</li>
            <li>Exclusive webinars</li>
        </ul>
        <button class="subscribe-button">Subscribe Yearly</button>
    </div>
</div>

</body>
</html>