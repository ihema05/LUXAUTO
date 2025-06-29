<?php
require_once 'db_config.php';
session_start();

// Get the brand from URL parameter
$brand = isset($_GET['brand']) ? $_GET['brand'] : '';

// Array of luxury car brands (same as in nav.php)
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

// Validate brand
if (!array_key_exists($brand, $brands)) {
    header('Location: CarDearlship.php');
    exit();
}

// Get filter parameters
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : PHP_FLOAT_MAX;
$year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
$show_filters = isset($_GET['show_filters']) ? true : false;

// Debug: Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Debug: Print the brand being searched
echo "<!-- Debug: Searching for brand: " . $brands[$brand] . " -->";

// Build the query
$query = "SELECT * FROM cars WHERE make = ?";
$params = [$brands[$brand]];
$types = "s";

if ($min_price > 0) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price < PHP_FLOAT_MAX) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

if ($year > 0) {
    $query .= " AND year = ?";
    $params[] = $year;
    $types .= "i";
}

$query .= " ORDER BY created_at DESC";

// Debug: Print the query
echo "<!-- Debug: Query: " . $query . " -->";
echo "<!-- Debug: Parameters: " . print_r($params, true) . " -->";

// Get cars for the selected brand
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);

// Debug: Print number of cars found
echo "<!-- Debug: Number of cars found: " . count($cars) . " -->";

// Get available years for this brand
$years_stmt = $conn->prepare("SELECT DISTINCT year FROM cars WHERE make = ? ORDER BY year DESC");
$years_stmt->bind_param("s", $brands[$brand]);
$years_stmt->execute();
$years_result = $years_stmt->get_result();
$available_years = $years_result->fetch_all(MYSQLI_ASSOC);

// Debug: Print available years
echo "<!-- Debug: Available years: " . print_r($available_years, true) . " -->";

// Debug: Check if cars table exists and has data
$check_table = $conn->query("SHOW TABLES LIKE 'cars'");
if ($check_table->num_rows == 0) {
    echo "<!-- Debug: cars table does not exist -->";
} else {
    $count_cars = $conn->query("SELECT COUNT(*) as count FROM cars");
    $total_cars = $count_cars->fetch_assoc()['count'];
    echo "<!-- Debug: Total cars in database: " . $total_cars . " -->";
    
    // Get a sample of car makes in the database
    $sample_makes = $conn->query("SELECT DISTINCT make FROM cars LIMIT 5");
    $makes = $sample_makes->fetch_all(MYSQLI_ASSOC);
    echo "<!-- Debug: Sample makes in database: " . print_r($makes, true) . " -->";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $brands[$brand]; ?> - Luxury Auto Gallery</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="CarCss.css">
    <style>
        .brand-header {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('<?php echo "brands/{$brand}-header.jpg"; ?>');
            background-size: cover;
            background-position: center;
            color: white;
            margin-bottom: 40px;
        }
        .brand-title {
            font-size: 3rem;
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }
        .brand-description {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.2rem;
            line-height: 1.6;
        }
        .filter-toggle {
            max-width: 1200px;
            margin: 0 auto 20px;
            text-align: right;
            padding: 0 20px;
        }
        .filter-btn {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .filter-btn:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        .filter-btn i {
            transition: transform 0.3s ease;
        }
        .filter-btn.active i {
            transform: rotate(180deg);
        }
        .filters {
            max-width: 1200px;
            margin: 0 auto 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: <?php echo $show_filters ? 'block' : 'none'; ?>;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .filters form {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .apply-filters {
            background: var(--accent-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .apply-filters:hover {
            background: var(--primary-color);
        }
        .reset-filters {
            background: #f8f9fa;
            color: #666;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        .reset-filters:hover {
            background: #e9ecef;
        }
        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .vehicle-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
        }
        .vehicle-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .vehicle-info {
            padding: 20px;
        }
        .vehicle-info h3 {
            margin: 0 0 10px 0;
            color: var(--primary-color);
        }
        .price {
            color: var(--accent-color);
            font-weight: bold;
            font-size: 1.2rem;
            margin: 10px 0;
        }
        .year {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .no-cars {
            text-align: center;
            padding: 40px;
            font-size: 1.2rem;
            color: #666;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="brand-header">
        <h1 class="brand-title"><?php echo $brands[$brand]; ?></h1>
        <p class="brand-description">Discover our exclusive collection of <?php echo $brands[$brand]; ?> vehicles, combining luxury, performance, and innovation.</p>
    </div>

    <div class="filter-toggle">
        <a href="?brand=<?php echo $brand; ?>&show_filters=<?php echo $show_filters ? '0' : '1'; ?>" class="filter-btn <?php echo $show_filters ? 'active' : ''; ?>">
            <i class="fas fa-filter"></i>
            <?php echo $show_filters ? 'Hide Filters' : 'Show Filters'; ?>
        </a>
    </div>

    <div class="filters">
        <form method="GET" action="">
            <input type="hidden" name="brand" value="<?php echo $brand; ?>">
            <input type="hidden" name="show_filters" value="1">
            
            <div class="filter-group">
                <label for="min_price">Minimum Price</label>
                <input type="number" id="min_price" name="min_price" value="<?php echo $min_price; ?>" min="0" step="1000">
            </div>
            
            <div class="filter-group">
                <label for="max_price">Maximum Price</label>
                <input type="number" id="max_price" name="max_price" value="<?php echo $max_price < PHP_FLOAT_MAX ? $max_price : ''; ?>" min="0" step="1000">
            </div>
            
            <div class="filter-group">
                <label for="year">Year</label>
                <select id="year" name="year">
                    <option value="">All Years</option>
                    <?php foreach ($available_years as $y): ?>
                        <option value="<?php echo $y['year']; ?>" <?php echo $year === (int)$y['year'] ? 'selected' : ''; ?>>
                            <?php echo $y['year']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="apply-filters">Apply Filters</button>
            <a href="?brand=<?php echo $brand; ?>" class="reset-filters">Reset Filters</a>
        </form>
    </div>

    <div class="vehicles-grid">
        <?php if (count($cars) > 0): ?>
            <?php foreach ($cars as $car): ?>
                <div class="vehicle-card">
                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>" class="vehicle-image">
                    <div class="vehicle-info">
                        <h3><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h3>
                        <p class="year">Year: <?php echo htmlspecialchars($car['year']); ?></p>
                        <p class="price">$<?php echo number_format($car['price']); ?></p>
                        <p><?php echo htmlspecialchars($car['description']); ?></p>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <a href="purchase.php?item_id=<?php echo $car['id']; ?>&type=car" class="purchase-btn">Purchase Now</a>
                        <?php else: ?>
                            <a href="login.php" class="purchase-btn">Login to Purchase</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-cars">
                <p>No <?php echo $brands[$brand]; ?> vehicles match your criteria. Please try different filters or check back later.</p>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>
</body>
</html> 