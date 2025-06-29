<?php
require_once 'db_config.php';
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit();
}

// Handle car deletion
if (isset($_POST['delete_car'])) {
    $car_id = $_POST['car_id'];
    
    // First check if the car has any appointments
    $stmt = $conn->prepare("SELECT COUNT(*) as appointment_count FROM appointments WHERE car_id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['appointment_count'] > 0) {
        $_SESSION['error'] = "Cannot delete this car because it has existing appointments. Please cancel or complete the appointments first.";
    } else {
        // If no appointments exist, proceed with deletion
        $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
        $stmt->bind_param("i", $car_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Car deleted successfully.";
        } else {
            $_SESSION['error'] = "Error deleting car. Please try again.";
        }
    }
    
    header('Location: admin_dashboard.php');
    exit();
}

// Handle adding new car
if (isset($_POST['add_car'])) {
    $make = filter_var($_POST['make'], FILTER_SANITIZE_STRING);
    $model = filter_var($_POST['model'], FILTER_SANITIZE_STRING);
    $year = (int)$_POST['year'];
    $price = (float)$_POST['price'];
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $image_url = '';
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        } else {
            $error = "Error uploading image";
        }
    }
    
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO cars (make, model, year, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssidss", $make, $model, $year, $price, $description, $image_url);
        
        if ($stmt->execute()) {
            $success = "Car added successfully";
        } else {
            $error = "Error adding car";
        }
        $stmt->close();
    }
}

// Fetch all cars
$cars = $conn->query("SELECT * FROM cars ORDER BY id DESC");

// Fetch all warranties
$warranties = $conn->query("
    SELECT w.*, c.make, c.model, c.year, u.email 
    FROM warranties w 
    JOIN cars c ON w.car_id = c.id 
    JOIN users u ON w.user_id = u.id 
    ORDER BY w.created_at DESC
");

// Fetch all car parts
$parts = $conn->query("SELECT * FROM car_parts ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Luxury Auto Gallery</title>
    <link rel="stylesheet" href="CarCss.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 0;
            padding: 20px;
        }
        .admin-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border: 1px solid rgba(197, 164, 126, 0.1);
        }
        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }
        .car-grid, .warranty-grid, .parts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .card-info {
            padding: 20px;
        }
        .card-info h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
            font-family: 'Playfair Display', serif;
        }
        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
        }
        .delete-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 600;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--light-grey);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(197, 164, 126, 0.1);
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .success {
            color: #155724;
            background: #d4edda;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .error {
            color: #721c24;
            background: #f8d7da;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .submit-btn {
            background: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .submit-btn:hover {
            background: var(--accent-color);
            transform: translateY(-2px);
        }
        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .tab-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .tab-btn.active {
            background: var(--secondary-color);
            color: var(--primary-color);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .button-group {
            display: flex;
            gap: 1.2rem;
            margin-top: 1.2rem;
        }
        
        .delete-btn, .edit-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-width: 100px;
        }
        
        .delete-btn {
            background: #e74c3c;
            color: white;
            border: 1px solid #c0392b;
        }
        
        .delete-btn:hover:not(:disabled) {
            background: #c0392b;
            border-color: #a93226;
        }
        
        .delete-btn:disabled {
            background: #95a5a6;
            border-color: #7f8c8d;
            cursor: not-allowed;
            opacity: 0.8;
        }
        
        .edit-btn {
            background: #2c3e50;
            color: white;
            border: 1px solid #34495e;
        }
        
        .edit-btn:hover {
            background: #34495e;
            border-color: #2c3e50;
        }
        
        .warning-text {
            color: #e74c3c;
            font-size: 0.9rem;
            margin-top: 0.8rem;
            font-style: italic;
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
                <a href="user_carparts.php"><span>Parts</span></a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="warranty.php"><span>Warranty</span></a>
                    <a href="user-credentials.php"><span>Profile</span></a>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                        <a href="admin_dashboard.php" class="active"><span>Admin</span></a>
                    <?php endif; ?>
                    <a href="logout.php" class="login-btn"><span>Logout</span></a>
                <?php else: ?>
                    <a href="login.php" class="login-btn"><span>Login</span></a>
                    <a href="register.php" class="register-btn"><span>Register</span></a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        <h1>Admin Dashboard</h1>
        
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <div class="tab-buttons">
            <button class="tab-btn active" onclick="showTab('cars')">Cars</button>
            <button class="tab-btn" onclick="showTab('warranties')">Warranties</button>
            <button class="tab-btn" onclick="showTab('parts')">Car Parts</button>
        </div>

        <div id="cars" class="tab-content active">
            <div class="admin-section">
                <h2>Add New Car</h2>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="make">Brand:</label>
                            <input type="text" id="make" name="make" required placeholder="e.g., Toyota, BMW">
                        </div>
                        
                        <div class="form-group">
                            <label for="model">Model:</label>
                            <input type="text" id="model" name="model" required placeholder="e.g., Camry, X5">
                        </div>
                        
                        <div class="form-group">
                            <label for="year">Year:</label>
                            <input type="number" id="year" name="year" required min="1900" max="<?php echo date('Y') + 1; ?>" placeholder="e.g., 2024">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price ($):</label>
                            <input type="number" id="price" name="price" step="0.01" required min="0" placeholder="e.g., 25000">
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Image:</label>
                            <input type="file" id="image" name="image" accept="image/*" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" required rows="4" placeholder="Enter car description..."></textarea>
                    </div>
                    
                    <button type="submit" name="add_car" class="submit-btn">Add Car</button>
                </form>
            </div>
            
            <div class="admin-section">
                <h2>Manage Cars</h2>
                <div class="car-grid">
                    <?php while ($car = $cars->fetch_assoc()): ?>
                        <div class="card">
                            <?php if ($car['image_url']): ?>
                                <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                            <?php endif; ?>
                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($car['year'] . ' ' . $car['make'] . ' ' . $car['model']); ?></h3>
                                <p><strong>Price:</strong> $<?php echo number_format($car['price'], 2); ?></p>
                                <p><?php echo htmlspecialchars($car['description']); ?></p>
                                
                                <?php
                                // Check if car has appointments
                                $stmt = $conn->prepare("SELECT COUNT(*) as appointment_count FROM appointments WHERE car_id = ?");
                                $stmt->bind_param("i", $car['id']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                $has_appointments = $row['appointment_count'] > 0;
                                ?>
                                
                                <div class="button-group">
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                        <button type="submit" name="delete_car" class="delete-btn" 
                                            <?php echo $has_appointments ? 'disabled' : ''; ?>
                                            onclick="return confirm('Are you sure you want to delete this car?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                    <a href="edit_car.php?id=<?php echo $car['id']; ?>" class="edit-btn">Edit</a>
                                </div>
                                
                                <?php if ($has_appointments): ?>
                                    <p class="warning-text">This car has existing appointments and cannot be deleted.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div id="warranties" class="tab-content">
            <div class="admin-section">
                <h2>Manage Warranties</h2>
                <div class="warranty-grid">
                    <?php while ($warranty = $warranties->fetch_assoc()): ?>
                        <div class="card">
                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($warranty['year'] . ' ' . $warranty['make'] . ' ' . $warranty['model']); ?></h3>
                                <p><strong>Customer:</strong> <?php echo htmlspecialchars($warranty['email']); ?></p>
                                <p><strong>Warranty Type:</strong> <?php echo ucfirst(htmlspecialchars($warranty['warranty_type'])); ?></p>
                                <p><strong>Start Date:</strong> <?php echo date('F j, Y', strtotime($warranty['start_date'])); ?></p>
                                <p><strong>End Date:</strong> <?php echo date('F j, Y', strtotime($warranty['end_date'])); ?></p>
                                <span class="status <?php echo strtotime($warranty['end_date']) > time() ? 'active' : 'expired'; ?>">
                                    <?php echo strtotime($warranty['end_date']) > time() ? 'Active' : 'Expired'; ?>
                                </span>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div id="parts" class="tab-content">
            <div class="admin-section">
                <h2>Manage Car Parts</h2>
                <a href="carparts.php" class="submit-btn" style="text-decoration: none; display: inline-block; margin-bottom: 20px;">
                    <i class="fas fa-cog"></i> Manage Parts
                </a>
                <div class="parts-grid">
                    <?php while ($part = $parts->fetch_assoc()): ?>
                        <div class="card">
                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($part['name']); ?></h3>
                                <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($part['type'])); ?></p>
                                <p><strong>Brand:</strong> <?php echo htmlspecialchars($part['brand']); ?></p>
                                <p><strong>Model:</strong> <?php echo htmlspecialchars($part['model']); ?></p>
                                <p><strong>Year:</strong> <?php echo htmlspecialchars($part['year']); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabId).classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
        }
    </script>
</body>
</html> 