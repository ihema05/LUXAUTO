<?php
require_once 'db_config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Get item details based on type and id
$item_id = $_GET['item_id'] ?? null;
$item_type = $_GET['type'] ?? null;

if (!$item_id || !$item_type) {
    header("Location: user_carparts.php");
    exit();
}

// Get item details
if ($item_type === 'part') {
    $stmt = $conn->prepare("SELECT * FROM car_parts WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $item = $stmt->get_result()->fetch_assoc();
} else {
    header("Location: user_carparts.php");
    exit();
}

// Get user's bank information
$stmt = $conn->prepare("SELECT * FROM user_credentials WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_info = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required_fields = ['card_number', 'card_holder', 'expiry_date', 'cvv'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        $error_message = "Please fill in all required fields: " . implode(", ", $missing_fields);
    } else {
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert payment information
            $stmt = $conn->prepare("
                INSERT INTO payment_info 
                (user_id, card_number, card_holder, expiry_date, cvv) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("issss", 
                $user_id,
                $_POST['card_number'],
                $_POST['card_holder'],
                $_POST['expiry_date'],
                $_POST['cvv']
            );
            
            if ($stmt->execute()) {
                // Create appointment record for the part
                $delivery_date = date('Y-m-d', strtotime('+3 days'));
                $purchase_date = date('Y-m-d H:i:s');
                $appointment_time = '10:00:00';

                $stmt = $conn->prepare("
                    INSERT INTO appointments 
                    (user_id, part_id, appointment_date, appointment_time, purchase_date, status) 
                    VALUES (?, ?, ?, ?, ?, 'pending')
                ");
                
                $stmt->bind_param("iisss", 
                    $user_id,
                    $item_id,
                    $delivery_date,
                    $appointment_time,
                    $purchase_date
                );

                if ($stmt->execute()) {
                    $conn->commit();
                    $_SESSION['success'] = "Purchase completed successfully! Your part will be delivered on " . 
                        date('F j, Y', strtotime($delivery_date)) . ". You can view your warranty details in the warranty section.";
                    header("Location: warranty.php");
                    exit();
                } else {
                    throw new Exception("Error creating appointment");
                }
            } else {
                throw new Exception("Error saving payment information");
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error processing purchase. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Purchase - Luxury Auto Gallery</title>
    <link rel="stylesheet" href="CarCss.css">
    <style>
        .purchase-form {
            max-width: 600px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--background-color);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .item-details {
            margin-bottom: 2rem;
            padding: 1rem;
            background: rgba(197, 164, 126, 0.1);
            border-radius: 5px;
        }

        .item-details h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .item-details p {
            margin: 0.5rem 0;
        }

        .item-details strong {
            color: var(--accent-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-color);
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .form-group input:focus {
            border-color: var(--accent-color);
            outline: none;
        }

        .submit-btn {
            background: var(--accent-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            transition: background 0.3s ease;
        }

        .submit-btn:hover {
            background: var(--primary-color);
        }

        .error {
            color: #dc3545;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            background: #f8d7da;
        }

        .success {
            color: #28a745;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            background: #d4edda;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <section class="purchase-section">
        <div class="container">
            <h1>Complete Your Purchase</h1>
            
            <?php if ($error_message): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <div class="purchase-form">
                <div class="item-details">
                    <h3>Item Details</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($item['name']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($item['type']); ?></p>
                    <p><strong>Brand:</strong> <?php echo htmlspecialchars($item['brand']); ?></p>
                    <p><strong>Model:</strong> <?php echo htmlspecialchars($item['model']); ?></p>
                    <p><strong>Price:</strong> $<?php echo number_format($item['price'], 2); ?></p>
                </div>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" 
                               placeholder="Enter your card number" required
                               pattern="[0-9]{16}" maxlength="16"
                               value="<?php echo htmlspecialchars($user_info['card_number'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="card_holder">Card Holder Name</label>
                        <input type="text" id="card_holder" name="card_holder" 
                               placeholder="Enter card holder name" required
                               value="<?php echo htmlspecialchars($user_info['card_holder'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="expiry_date">Expiry Date</label>
                        <input type="text" id="expiry_date" name="expiry_date" 
                               placeholder="MM/YY" required
                               pattern="(0[1-9]|1[0-2])\/([0-9]{2})" maxlength="5"
                               value="<?php echo htmlspecialchars($user_info['expiry_date'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" 
                               placeholder="Enter CVV" required
                               pattern="[0-9]{3,4}" maxlength="4"
                               value="<?php echo htmlspecialchars($user_info['cvv'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="submit-btn">Complete Purchase</button>
                </form>
            </div>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
        // Format card number with spaces
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });

        // Format expiry date
        document.getElementById('expiry_date').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0,2) + '/' + value.slice(2);
            }
            e.target.value = value;
        });

        // Format CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value;
        });
    </script>
</body>
</html> 