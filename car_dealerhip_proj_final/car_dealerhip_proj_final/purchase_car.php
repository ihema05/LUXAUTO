<?php
session_start();
require_once 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_car'])) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please login or register to purchase a car.";
        header('Location: login.php');
        exit();
    }

    $car_id = $_POST['car_id'];
    $user_id = $_SESSION['user_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $purchase_date = date('Y-m-d H:i:s');

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

    // Create appointment record
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, car_id, appointment_date, appointment_time, purchase_date, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("iisss", $user_id, $car_id, $appointment_date, $appointment_time, $purchase_date);
    
    if ($stmt->execute()) {
        $appointment_id = $conn->insert_id;
        $_SESSION['success'] = "Car purchase appointment scheduled successfully! Your appointment is on " . 
            date('F j, Y', strtotime($appointment_date)) . " at " . 
            date('g:i A', strtotime($appointment_time)) . ". You can view and print your appointment details in the warranty section.";
        header('Location: warranty.php');
        exit();
    } else {
        $_SESSION['error'] = "Error scheduling appointment. Please try again.";
    }
}

// Get available appointment times (business hours: 9 AM to 5 PM)
$available_times = [];
$start_time = strtotime('09:00:00');
$end_time = strtotime('17:00:00');
$interval = 60 * 60; // 1 hour intervals

for ($time = $start_time; $time <= $end_time; $time += $interval) {
    $available_times[] = date('H:i:s', $time);
}

// Get next 7 days for appointment dates
$available_dates = [];
for ($i = 1; $i <= 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    if (date('N', strtotime($date)) <= 5) { // Only weekdays
        $available_dates[] = $date;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Car Purchase - Luxury Auto Gallery</title>
    <link rel="stylesheet" href="CarCss.css">
    <style>
        .appointment-form {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--background-color);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: bold;
        }

        .form-group select,
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--secondary-color);
            border-radius: 5px;
            font-size: 1rem;
            font-family: 'Playfair Display', serif;
        }

        .submit-btn {
            background: var(--accent-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1.1rem;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: var(--primary-color);
        }

        .car-details {
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(197, 164, 126, 0.1);
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="appointment-section">
        <div class="container">
            <h1>Schedule Car Purchase Appointment</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <div class="appointment-form">
                <div class="car-details">
                    <h2>Selected Car Details</h2>
                    <?php
                    if (isset($_GET['car_id'])) {
                        $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
                        $stmt->bind_param("i", $_GET['car_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $car = $result->fetch_assoc();
                        
                        if ($car) {
                            echo "<p><strong>Make:</strong> " . htmlspecialchars($car['make']) . "</p>";
                            echo "<p><strong>Model:</strong> " . htmlspecialchars($car['model']) . "</p>";
                            echo "<p><strong>Year:</strong> " . htmlspecialchars($car['year']) . "</p>";
                            echo "<p><strong>Price:</strong> $" . number_format($car['price'], 2) . "</p>";
                        }
                    }
                    ?>
                </div>

                <form method="POST">
                    <input type="hidden" name="car_id" value="<?php echo isset($_GET['car_id']) ? $_GET['car_id'] : ''; ?>">
                    
                    <div class="form-group">
                        <label for="appointment_date">Select Date:</label>
                        <select name="appointment_date" id="appointment_date" required>
                            <option value="">Choose a date</option>
                            <?php foreach ($available_dates as $date): ?>
                                <option value="<?php echo $date; ?>">
                                    <?php echo date('F j, Y', strtotime($date)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="appointment_time">Select Time:</label>
                        <select name="appointment_time" id="appointment_time" required>
                            <option value="">Choose a time</option>
                            <?php foreach ($available_times as $time): ?>
                                <option value="<?php echo $time; ?>">
                                    <?php echo date('g:i A', strtotime($time)); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="purchase_car" class="submit-btn">Schedule Appointment</button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html> 