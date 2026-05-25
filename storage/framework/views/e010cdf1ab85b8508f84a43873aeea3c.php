<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'NEET LMS - Master your medical entrance exam with comprehensive study materials, mock tests, and expert guidance.'); ?>">
    <title><?php echo $__env->yieldContent('title', 'NEET LMS - Medical Entrance Exam Preparation'); ?></title>

    <!-- External CSS Files -->
    <link rel="stylesheet" href="<?php echo e(asset('css/main.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/components.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/layout.css')); ?>">

    <!-- Alpine Icons (SVG Library) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/cdn@3.x.x/dist/cdn.min.js"></script>

    <?php echo $__env->yieldContent('extra_css'); ?>
</head>
<body>
    <header>
        <nav>
            <a href="<?php echo e(route('home')); ?>" class="nav-brand">NEET LMS</a>

            <button class="nav-toggle" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="<?php echo e(route('home')); ?>" <?php if(request()->routeIs('home')): ?> class="active" <?php endif; ?>>Home</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('features')); ?>" <?php if(request()->routeIs('features')): ?> class="active" <?php endif; ?>>Features</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('pricing')); ?>" <?php if(request()->routeIs('pricing')): ?> class="active" <?php endif; ?>>Pricing</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('about')); ?>" <?php if(request()->routeIs('about')): ?> class="active" <?php endif; ?>>About</a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo e(route('contact')); ?>" <?php if(request()->routeIs('contact')): ?> class="active" <?php endif; ?>>Contact</a>
                </li>
            </ul>

            <div class="nav-auth">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('student.dashboard')); ?>" class="btn btn-secondary btn-small">Dashboard</a>
                    <form action="<?php echo e(route('logout')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-secondary btn-small">Logout</button>
                    </form>
                <?php endif; ?>
                <?php if(auth()->guard()->guest()): ?>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-secondary btn-small">Login</a>
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-small">Sign Up</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>About NEET LMS</h4>
                    <p>Your comprehensive platform for medical entrance exam preparation with 3,000+ questions and expert guidance.</p>
                </div>

                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo e(route('home')); ?>">Home</a></li>
                        <li><a href="<?php echo e(route('features')); ?>">Features</a></li>
                        <li><a href="<?php echo e(route('pricing')); ?>">Pricing</a></li>
                        <li><a href="<?php echo e(route('about')); ?>">About Us</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Blog & Tips</a></li>
                        <li><a href="#">Study Guides</a></li>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h4>Legal</h4>
                    <ul>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms & Conditions</a></li>
                        <li><a href="#">Cookie Policy</a></li>
                        <li><a href="<?php echo e(route('contact')); ?>">Contact Us</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-divider"></div>

            <div class="footer-bottom">
                <div class="footer-copyright">
                    &copy; 2026 NEET LMS. All rights reserved. | Helping you achieve your medical dreams!
                </div>
                <div class="footer-social">
                    <a href="#" title="Twitter" aria-label="Follow us on Twitter">𝕏</a>
                    <a href="#" title="LinkedIn" aria-label="Follow us on LinkedIn">in</a>
                    <a href="#" title="Facebook" aria-label="Follow us on Facebook">f</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- External JavaScript Files -->
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <?php echo $__env->yieldContent('extra_js'); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\neet\resources\views/layouts/app.blade.php ENDPATH**/ ?>