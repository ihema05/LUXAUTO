<?php
session_start();
require_once 'db_config.php';

// Handle purchase request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_part'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please login or register to purchase parts.";
        header('Location: login.php');
        exit();
    }

    $part_id = $_POST['part_id'];
    
    // Redirect to purchase.php with part information
    header('Location: purchase.php?item_id=' . $part_id . '&type=part');
    exit();
}

// Get all parts with optional filters
$where = [];
$params = [];
$types = "";

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $where[] = "(name LIKE ? OR model LIKE ?)";
    $params[] = $search;
    $params[] = $search;
    $types .= "ss";
}

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $where[] = "type = ?";
    $params[] = $_GET['type'];
    $types .= "s";
}

if (isset($_GET['brand']) && !empty($_GET['brand'])) {
    $where[] = "brand = ?";
    $params[] = $_GET['brand'];
    $types .= "s";
}

if (isset($_GET['year']) && !empty($_GET['year'])) {
    $where[] = "year = ?";
    $params[] = $_GET['year'];
    $types .= "i";
}

$sql = "SELECT * FROM car_parts";
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY name ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$parts = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Parts - Luxury Auto Gallery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CarCss.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .parts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }

        .part-card {
            background: var(--background-color);
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            position: relative;
        }

        .part-card:hover {
            transform: translateY(-5px);
        }

        .part-info h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .part-details {
            margin-bottom: 1.5rem;
        }

        .part-details p {
            margin: 0.5rem 0;
            color: var(--text-color);
        }

        .part-details strong {
            color: var(--accent-color);
        }

        .part-details p:nth-child(5) {
            font-size: 1.2rem;
            color: var(--primary-color);
            font-weight: bold;
            margin: 1rem 0;
            padding: 0.5rem;
            background: rgba(197, 164, 126, 0.1);
            border-radius: 5px;
        }

        .part-details p:last-child {
            font-style: italic;
            color: var(--secondary-color);
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(197, 164, 126, 0.2);
        }

        .purchase-btn {
            background: var(--accent-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 1rem;
        }

        .purchase-btn:hover {
            background: var(--primary-color);
        }

        .stock-status {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-size: 0.9rem;
            margin-left: 0.5rem;
        }

        .stock-high {
            background: #d4edda;
            color: #155724;
        }

        .stock-medium {
            background: #fff3cd;
            color: #856404;
        }

        .stock-low {
            background: #f8d7da;
            color: #721c24;
        }

        .success, .error {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .search-filter-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .search-bar {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Playfair Display', serif;
            background: var(--light-color);
            transition: all 0.3s ease;
        }

        .search-bar input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(197, 164, 126, 0.1);
        }

        .search-bar i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
        }

        .filter-dropdown {
            position: relative;
        }

        .filter-btn {
            padding: 0.8rem 1.5rem;
            background: var(--secondary-color);
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Playfair Display', serif;
        }

        .filter-btn:hover {
            background: transparent;
            color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .filter-content {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border: 1px solid var(--secondary-color);
            border-radius: 8px;
            padding: 1.5rem;
            min-width: 250px;
            z-index: 1000;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 0.5rem;
        }

        .filter-content.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        .filter-content select {
            width: 100%;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            border: 1px solid var(--light-grey);
            border-radius: 8px;
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            background: var(--light-color);
            transition: all 0.3s ease;
        }

        .filter-content select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(197, 164, 126, 0.1);
        }

        .filter-content select:last-child {
            margin-bottom: 0;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .search-filter-container {
                flex-direction: column;
            }

            .search-bar {
                width: 100%;
            }

            .filter-dropdown {
                width: 100%;
            }

            .filter-btn {
                width: 100%;
                justify-content: center;
            }

            .filter-content {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav>
        <div class="nav-content">
            <a href="luxury-dealership.html" class="logo">LUX<span style="color: var(--accent-color)">AUTO</span></a>
            <div class="nav-links">
                <a href="luxury-dealership.php"><span>Home</span></a>
                <a href="CarDearlship.php"><span>Sales</span></a>
                <a href="CarDearlship.html"><span>Featured</span></a>
                <a href="about.html"><span>About</span></a>
                <a href="contact.html"><span>Contact</span></a>
                <a href="user_carparts.php" class="active"><span>Parts</span></a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="warranty.php"><span>Warranty</span></a>
                    <a href="user-credentials.php"><span>Profile</span></a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="admin_dashboard.php"><span>Admin</span></a>
                    <?php endif; ?>
                    <a href="logout.php" class="login-btn"><span>Logout</span></a>
                <?php else: ?>
                    <a href="login.php" class="login-btn"><span>Login</span></a>
                    <a href="register.php" class="register-btn"><span>Register</span></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <section class="parts-hero">
        <div class="hero-content">
            <h1>Premium Car Parts</h1>
            <p>Discover our exclusive collection of high-performance parts</p>
        </div>
    </section>

    <section class="parts-management">
        <div class="container">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="search-filter-container">
                <form method="GET" class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Search parts..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                </form>
                <div class="filter-dropdown">
                    <button type="button" class="filter-btn">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <div class="filter-content">
                        <form method="GET" id="filterForm">
                            <select name="type" onchange="this.form.submit()">
                                <option value="">All Types</option>
                                <option value="engine" <?php echo (isset($_GET['type']) && $_GET['type'] === 'engine') ? 'selected' : ''; ?>>Engine</option>
                                <option value="transmission" <?php echo (isset($_GET['type']) && $_GET['type'] === 'transmission') ? 'selected' : ''; ?>>Transmission</option>
                                <option value="suspension" <?php echo (isset($_GET['type']) && $_GET['type'] === 'suspension') ? 'selected' : ''; ?>>Suspension</option>
                                <option value="brakes" <?php echo (isset($_GET['type']) && $_GET['type'] === 'brakes') ? 'selected' : ''; ?>>Brakes</option>
                                <option value="interior" <?php echo (isset($_GET['type']) && $_GET['type'] === 'interior') ? 'selected' : ''; ?>>Interior</option>
                                <option value="exterior" <?php echo (isset($_GET['type']) && $_GET['type'] === 'exterior') ? 'selected' : ''; ?>>Exterior</option>
                            </select>
                            <select name="brand" onchange="this.form.submit()">
                                <option value="">All Brands</option>
                                <option value="mercedes" <?php echo (isset($_GET['brand']) && $_GET['brand'] === 'mercedes') ? 'selected' : ''; ?>>Mercedes</option>
                                <option value="bmw" <?php echo (isset($_GET['brand']) && $_GET['brand'] === 'bmw') ? 'selected' : ''; ?>>BMW</option>
                                <option value="audi" <?php echo (isset($_GET['brand']) && $_GET['brand'] === 'audi') ? 'selected' : ''; ?>>Audi</option>
                                <option value="porsche" <?php echo (isset($_GET['brand']) && $_GET['brand'] === 'porsche') ? 'selected' : ''; ?>>Porsche</option>
                            </select>
                            <select name="year" onchange="this.form.submit()">
                                <option value="">All Years</option>
                                <?php for($i = 2024; $i >= 2020; $i--): ?>
                                    <option value="<?php echo $i; ?>" <?php echo (isset($_GET['year']) && $_GET['year'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <div class="parts-grid">
                <?php foreach ($parts as $part): ?>
                    <div class="part-card">
                        <div class="part-info">
                            <h3><?php echo htmlspecialchars($part['name']); ?></h3>
                            <div class="part-details">
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($part['type']); ?></p>
                                <p><strong>Brand:</strong> <?php echo htmlspecialchars($part['brand']); ?></p>
                                <p><strong>Model:</strong> <?php echo htmlspecialchars($part['model']); ?></p>
                                <p><strong>Year:</strong> <?php echo htmlspecialchars($part['year']); ?></p>
                                <p><strong>Price:</strong> $<?php echo number_format($part['price'], 2); ?></p>
                                <p>
                                    <strong>Stock:</strong> <?php echo htmlspecialchars($part['stock']); ?> units
                                    <?php
                                    $stockClass = '';
                                    if ($part['stock'] > 10) {
                                        $stockClass = 'stock-high';
                                    } elseif ($part['stock'] > 5) {
                                        $stockClass = 'stock-medium';
                                    } else {
                                        $stockClass = 'stock-low';
                                    }
                                    ?>
                                    <span class="stock-status <?php echo $stockClass; ?>">
                                        <?php
                                        if ($part['stock'] > 10) {
                                            echo 'In Stock';
                                        } elseif ($part['stock'] > 5) {
                                            echo 'Limited Stock';
                                        } else {
                                            echo 'Low Stock';
                                        }
                                        ?>
                                    </span>
                                </p>
                            </div>
                            <form method="POST" style="margin-top: 1rem;">
                                <input type="hidden" name="part_id" value="<?php echo $part['id']; ?>">
                                <button type="submit" name="purchase_part" class="purchase-btn">
                                    <i class="fas fa-shopping-cart"></i> Purchase Now
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>

    <script>
        // Filter dropdown functionality
        const filterBtn = document.querySelector('.filter-btn');
        const filterContent = document.querySelector('.filter-content');
        
        filterBtn.addEventListener('click', function() {
            filterContent.classList.toggle('show');
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.filter-dropdown')) {
                filterContent.classList.remove('show');
            }
        });
    </script>
</body>
</html> 