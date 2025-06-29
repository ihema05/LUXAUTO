<?php
session_start();
require_once 'db_config.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== 1) {
    header('Location: login.php');
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = $_POST['name'];
                $type = $_POST['type'];
                $brand = $_POST['brand'];
                $model = $_POST['model'];
                $year = $_POST['year'];
                $price = $_POST['price'];

                $stmt = $conn->prepare("INSERT INTO car_parts (name, type, brand, model, year, price) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssid", $name, $type, $brand, $model, $year, $price);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Part added successfully!";
                } else {
                    $_SESSION['error'] = "Error adding part: " . $conn->error;
                }
                break;

            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM car_parts WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Part deleted successfully!";
                } else {
                    $_SESSION['error'] = "Error deleting part: " . $conn->error;
                }
                break;

            case 'edit':
                $id = $_POST['id'];
                $name = $_POST['name'];
                $type = $_POST['type'];
                $brand = $_POST['brand'];
                $model = $_POST['model'];
                $year = $_POST['year'];
                $price = $_POST['price'];

                $stmt = $conn->prepare("UPDATE car_parts SET name = ?, type = ?, brand = ?, model = ?, year = ?, price = ? WHERE id = ?");
                $stmt->bind_param("ssssidi", $name, $type, $brand, $model, $year, $price, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Part updated successfully!";
                } else {
                    $_SESSION['error'] = "Error updating part: " . $conn->error;
                }
                break;
        }
    }
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
    <title>Car Parts Management - Luxury Auto Gallery</title>
    <link rel="stylesheet" href="CarPartsCss.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav>
        <div class="nav-content">
            <a href="luxury-dealership.html" class="logo">LUX<span style="color: var(--accent-color)">AUTO</span></a>
            <div class="nav-links">
                <a href="luxury-dealership.html" class="active">Home</a>
                <a href="CarDearlship.php">Car Sales</a>
                <a href="CarDearlship.html">Featured</a>
                <a href="about.html">About</a>
                <a href="contact.html">Contact</a>
                <a href="user_carparts.php">Car Parts</a>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            </div>
        </div>
    </nav>
    <section class="parts-hero">
        <div class="hero-content">
            <h1>Premium Car Parts</h1>
            <p>Manage our exclusive collection of high-performance parts</p>
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
                    <button class="filter-btn">
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
                            </div>
                            <div class="part-actions">
                                <button class="edit-btn" onclick="editPart(<?php echo htmlspecialchars(json_encode($part)); ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $part['id']; ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this part?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="add-part-form">
                <h2>Add New Part</h2>
                <form method="POST" id="addPartForm">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="name">Part Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="engine">Engine</option>
                            <option value="transmission">Transmission</option>
                            <option value="suspension">Suspension</option>
                            <option value="brakes">Brakes</option>
                            <option value="interior">Interior</option>
                            <option value="exterior">Exterior</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brand">Brand</label>
                        <select id="brand" name="brand" required>
                            <option value="mercedes">Mercedes</option>
                            <option value="bmw">BMW</option>
                            <option value="audi">Audi</option>
                            <option value="porsche">Porsche</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="model">Model</label>
                        <input type="text" id="model" name="model" required>
                    </div>
                    <div class="form-group">
                        <label for="year">Year</label>
                        <input type="number" id="year" name="year" min="2000" max="2024" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Price ($)</label>
                        <input type="number" id="price" name="price" min="0" step="0.01" required>
                    </div>
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-plus"></i> Add Part
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>

    <script>
        // Edit part functionality
        function editPart(part) {
            const form = document.getElementById('addPartForm');
            form.action.value = 'edit';
            form.innerHTML += `<input type="hidden" name="id" value="${part.id}">`;
            
            document.getElementById('name').value = part.name;
            document.getElementById('type').value = part.type;
            document.getElementById('brand').value = part.brand;
            document.getElementById('model').value = part.model;
            document.getElementById('year').value = part.year;
            document.getElementById('price').value = part.price;
            
            document.querySelector('.submit-btn').innerHTML = '<i class="fas fa-save"></i> Update Part';
            document.querySelector('.submit-btn').style.background = 'var(--accent-color)';
            
            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth' });
        }

        // Reset form when adding new part
        document.getElementById('addPartForm').addEventListener('reset', function() {
            this.action.value = 'add';
            document.querySelector('.submit-btn').innerHTML = '<i class="fas fa-plus"></i> Add Part';
            document.querySelector('.submit-btn').style.background = 'var(--secondary-color)';
        });

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