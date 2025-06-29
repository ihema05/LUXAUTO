<?php
require_once 'db_config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* Handle car addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_car'])) {
    $make = filter_var($_POST['make'], FILTER_SANITIZE_STRING);
    $model = filter_var($_POST['model'], FILTER_SANITIZE_STRING);
    $year = filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    
    // Handle image upload
    $image_url = '';
    if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . time() . '_' . basename($_FILES["car_image"]["name"]);
        if (move_uploaded_file($_FILES["car_image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO cars (make, model, year, price, description, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidss", $make, $model, $year, $price, $description, $image_url);
    
    if ($stmt->execute()) {
        $success = "Car added successfully!";
    } else {
        $error = "Error adding car: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all cars
$cars = $conn->query("SELECT * FROM cars ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Car Dealership</title>
    <link rel="stylesheet" href="CarCss.css">
</head>
<body>
    <div class="container">
        <header>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?></h2>
            <a href="logout.php" class="logout-btn">Logout</a>
        </header>

        <?php 
        if (isset($success)) echo "<p class='success'>$success</p>";
        if (isset($error)) echo "<p class='error'>$error</p>";
        ?>

        <section class="add-car">
            <h3>Add New Car</h3>
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="make">Make:</label>
                    <input type="text" id="make" name="make" required>
                </div>

                <div class="form-group">
                    <label for="model">Model:</label>
                    <input type="text" id="model" name="model" required>
                </div>

                <div class="form-group">
                    <label for="year">Year:</label>
                    <input type="number" id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="price">Price:</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="car_image">Car Image:</label>
                    <input type="file" id="car_image" name="car_image" accept="image/*">
                </div>

                <button type="submit" name="add_car">Add Car</button>
            </form>
  */      </section>

        <section class="car-list">
            <h3>Available Cars</h3>
            <div class="cars-grid">
                <?php while ($car = $cars->fetch_assoc()): ?>
                    <div class="car-card">
                        <?php if ($car['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>">
                        <?php endif; ?>
                        <h4><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h4>
                        <p>Year: <?php echo htmlspecialchars($car['year']); ?></p>
                        <p>Price: $<?php echo number_format($car['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($car['description']); ?></p>
                        <a href="warranty.php?car_id=<?php echo $car['id']; ?>" class="purchase-btn">Purchase & Get Warranty</a>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>
</body>
</html> 