<?php
require_once 'includes/auth.php';

// Require login but not subscription
$auth->requireLogin();

// Handle subscription form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planType = in_array($_POST['plan_type'] ?? '', ['free', 'monthly', 'yearly']) ? $_POST['plan_type'] : 'free';
    
    // Here you would integrate with a payment processor like Stripe or PayPal
    // For this example, we'll just create a subscription directly
    
    $result = $auth->createSubscription($_SESSION['user_id'], $planType);
    
    if ($result['success']) {
        // Redirect to the learning page
        header("Location: learn.html");
        exit;
    } else {
        $error = $result['message'];
    }
}

// Check if user already has subscription
if ($auth->checkSubscription()) {
    // Redirect to learning page
    header("Location: learn.html");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Subscribe to access premium Qlik tutorials and learning resources for beginners.">
    <meta name="keywords" content="Qlik subscription, Qlik Sense premium content">
    <title>Subscribe to Premium Content | Qlik Beginners</title>
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
                <h1>Subscribe to Premium Content</h1>
                <p>Unlock advanced tutorials and resources for Qlik beginners</p>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="subscription-container">
            <?php if (isset($error)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 1rem; margin-bottom: 1rem; border-radius: 5px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <p>Choose a subscription plan that works best for you to unlock all premium content, including advanced tutorials, sample apps, and more.</p>
            
            <form method="post" action="subscription.php" id="subscription-form">
                <div class="subscription-options">
                    <div class="subscription-option" data-plan="free">
                        <h3>Free Plan</h3>
                        <div class="subscription-price">$0<span style="font-size: 0.9rem; color: #6c757d;"> Forever</span></div>
                        <ul class="benefits-list">
                            <li>Basic learning resources</li>
                            <li>Preview of premium content</li>
                            <li>Community access</li>
                        </ul>
                        <input type="radio" name="plan_type" value="free" id="free-plan" style="display:none;">
                    </div>
                    <div class="subscription-option" data-plan="monthly">
                        <h3>Monthly Plan</h3>
                        <div class="subscription-price">$<?php echo MONTHLY_PRICE; ?>/month</div>
                        <ul class="benefits-list">
                            <li>Access to all premium chapters</li>
                            <li>New tutorials every month</li>
                            <li>Cancel anytime</li>
                        </ul>
                        <input type="radio" name="plan_type" value="monthly" id="monthly-plan" style="display:none;">
                    </div>
                    <div class="subscription-option" data-plan="yearly">
                        <h3>Yearly Plan</h3>
                        <div class="subscription-price">$<?php echo YEARLY_PRICE; ?>/year <span style="font-size: 0.9rem; color: #6c757d;">Save 17%</span></div>
                        <ul class="benefits-list">
                            <li>Access to all premium chapters</li>
                            <li>New tutorials every month</li>
                            <li>Priority support</li>
                            <li>Exclusive webinars</li>
                        </ul>
                        <input type="radio" name="plan_type" value="yearly" id="yearly-plan" style="display:none;">
                    </div>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="btn-primary">Subscribe Now</button>
                </div>
            </form>
            
            <div class="subscription-benefits">
                <h2>What You'll Get With Premium Access</h2>
                <p>Upgrade to premium to accelerate your Qlik learning journey with these exclusive benefits:</p>
                
                <div class="benefits-grid">
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-book"></i></div>
                        <div>
                            <strong>Complete Learning Path</strong>
                            <p>Access all 8 chapters covering everything from basics to advanced dashboard creation.</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-file-code"></i></div>
                        <div>
                            <strong>Script Samples</strong>
                            <p>Get ready-to-use load script examples for common data loading scenarios.</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-laptop-code"></i></div>
                        <div>
                            <strong>Sample Applications</strong>
                            <p>Download complete Qlik apps that you can explore and modify yourself.</p>
                        </div>
                    </div>
                    <div class="benefit-item">
                        <div class="benefit-icon"><i class="fas fa-headset"></i></div>
                        <div>
                            <strong>Priority Support</strong>
                            <p>Get your questions answered faster with our premium support channels.</p>
                        </div>
                    </div>
                </div>
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

    <script>
        // Update copyright year
        document.getElementById('year').textContent = new Date().getFullYear();
        
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
        
        // Set monthly as default
        document.querySelector('.subscription-option[data-plan="monthly"]').click();
    </script>
</body>
</html>