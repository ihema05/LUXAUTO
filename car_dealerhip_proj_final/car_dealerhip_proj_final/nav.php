<?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Function to check if a link is active
function isActive($page) {
    global $current_page;
    return ($current_page === $page) ? 'active' : '';
}

// Function to get the correct file extension
function getFileExtension($page) {
    return file_exists($page . '.php') ? '.php' : '.html';
}

// Array of luxury car brands
$brands = [
    'mercedes' => 'Mercedes-Benz',
    'bmw' => 'BMW',
    'audi' => 'Audi',
    'porsche' => 'Porsche',
    'lexus' => 'Lexus',
    'jaguar' => 'Jaguar',
    'land-rover' => 'Land Rover',
    'maserati' => 'Maserati',
    'bentley' => 'Bentley',
    'rolls-royce' => 'Rolls-Royce',
    'ferrari' => 'Ferrari',
    'lamborghini' => 'Lamborghini',
    'aston-martin' => 'Aston Martin',
    'mclaren' => 'McLaren',
    'bugatti' => 'Bugatti'
];
?>

<nav>
    <div class="nav-content">
        <a href="luxury-dealership<?php echo getFileExtension('luxury-dealership'); ?>" class="logo">LUX<span style="color: var(--accent-color)">AUTO</span></a>
        <div class="nav-links">
            <a href="luxury-dealership<?php echo getFileExtension('luxury-dealership'); ?>" class="<?php echo isActive('luxury-dealership.php'); ?>"><span>Home</span></a>
            <a href="CarDearlship.php" class="<?php echo isActive('CarDearlship.php'); ?>"><span>Sales</span></a>
            <a href="CarDearlship_featured.php" class="<?php echo isActive('CarDearlship_featured.php'); ?>"><span>Featured</span></a>
            <a href="about.html" class="<?php echo isActive('about.html'); ?>"><span>About</span></a>
            <a href="contact.html" class="<?php echo isActive('contact.html'); ?>"><span>Contact</span></a>
            <a href="user_carparts.php" class="<?php echo isActive('user_carparts.php'); ?>"><span>Parts</span></a>
            <div class="brands-dropdown">
                <button class="brands-btn">Brands <i class="fas fa-chevron-down"></i></button>
                <div class="brands-content">
                    <?php foreach ($brands as $brand_key => $brand_name): ?>
                        <a href="brand.php?brand=<?php echo $brand_key; ?>" class="<?php echo (isset($_GET['brand']) && $_GET['brand'] === $brand_key) ? 'active' : ''; ?>">
                            <?php echo $brand_name; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="warranty.php" class="<?php echo isActive('warranty.php'); ?>"><span>Warranty</span></a>
                <a href="user-credentials.php" class="<?php echo isActive('user-credentials.php'); ?>"><span>Profile</span></a>
                <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                    <a href="admin_dashboard.php" class="<?php echo isActive('admin_dashboard.php'); ?>"><span>Admin</span></a>
                <?php endif; ?>
                <a href="logout.php" class="login-btn"><span>Logout</span></a>
            <?php else: ?>
                <a href="login.php" class="login-btn"><span>Login</span></a>
                <a href="register.php" class="register-btn"><span>Register</span></a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.brands-dropdown {
    position: relative;
    display: inline-block;
}

.brands-btn {
    background: none;
    border: none;
    color: inherit;
    cursor: pointer;
    padding: 10px 15px;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 5px;
}

.brands-btn i {
    font-size: 0.8rem;
    transition: transform 0.3s ease;
}

.brands-dropdown:hover .brands-btn i {
    transform: rotate(180deg);
}

.brands-content {
    display: none;
    position: absolute;
    background-color: #fff;
    min-width: 200px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    z-index: 1000;
    border-radius: 8px;
    overflow: hidden;
    top: 100%;
    left: 0;
}

.brands-dropdown:hover .brands-content {
    display: block;
}

.brands-content a {
    color: #333;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    transition: background-color 0.3s ease;
}

.brands-content a:hover {
    background-color: var(--accent-color);
    color: white;
}

.brands-content a.active {
    background-color: var(--primary-color);
    color: white;
}

@media (max-width: 768px) {
    .brands-content {
        position: static;
        box-shadow: none;
        border-radius: 0;
    }
    
    .brands-btn {
        width: 100%;
        justify-content: space-between;
    }
}
</style> 