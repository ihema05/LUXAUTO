<?php
require_once 'db_config.php';
session_start();

// Get all cars from database
$sql = "SELECT * FROM cars ORDER BY created_at DESC";
$result = $conn->query($sql);
$cars = $result->fetch_all(MYSQLI_ASSOC);

// Handle purchase request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_car'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please login or register to purchase a car.";
        header('Location: login.php');
        exit();
    }

    $car_id = $_POST['car_id'];
    $user_id = $_SESSION['user_id'];

    // Check if user already has credentials
    $stmt = $conn->prepare("SELECT id FROM user_credentials WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['error'] = "Please complete your profile before purchasing a car.";
        $_SESSION['redirect_after_credentials'] = 'CarDearlship.php';
        header('Location: user-credentials.php');
        exit();
    }

    // Create purchase record
    $stmt = $conn->prepare("INSERT INTO purchases (user_id, item_type, item_id) VALUES (?, 'car', ?)");
    $stmt->bind_param("ii", $user_id, $car_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Car purchased successfully! You can now view your warranty.";
        header('Location: warranty.php');
        exit();
    } else {
        $_SESSION['error'] = "Error processing purchase. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Auto Gallery - Collection</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="CarCss.css">
    <style>
        .vehicle-card {
            position: relative;
            overflow: hidden;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .vehicle-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .vehicle-image2 {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }
        .vehicle-info {
            padding: 20px;
        }
        .vehicle-info h3 {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-family: 'Playfair Display', serif;
        }
        .price {
            font-size: 1.3rem;
            color: var(--accent-color);
            font-weight: bold;
            margin: 10px 0;
        }
        .car-actions {
            margin-top: 20px;
        }
        .purchase-btn {
            display: block;
            width: 100%;
            background: var(--accent-color);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .purchase-btn:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
        }
        .success, .error {
            padding: 15px;
            margin: 20px auto;
            border-radius: 8px;
            text-align: center;
            max-width: 800px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .collection {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 40px;
            font-family: 'Playfair Display', serif;
        }
        .vehicles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px 0;
        }
        @media (max-width: 768px) {
            .vehicles-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            .section-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="collection">
        <h2 class="section-title">Our Luxury Collection</h2>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <div class="vehicles-grid">
            <?php foreach ($cars as $car): ?>
                <div class="vehicle-card">
                    <img src="<?php echo htmlspecialchars($car['image_url']); ?>" alt="<?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?>" class="vehicle-image2">
                    <div class="vehicle-info">
                        <h3><?php echo htmlspecialchars($car['make'] . ' ' . $car['model']); ?></h3>
                        <p class="price">$<?php echo number_format($car['price']); ?></p>
                        <p><?php echo htmlspecialchars($car['description']); ?></p>
                        <div class="car-actions">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="purchase_car.php?car_id=<?php echo $car['id']; ?>" class="purchase-btn">Purchase</a>
                            <?php else: ?>
                                <a href="login.php" class="purchase-btn">Login to Purchase</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 Luxury Auto Gallery. All rights reserved.</p>
    </footer>
</body>
</html> 