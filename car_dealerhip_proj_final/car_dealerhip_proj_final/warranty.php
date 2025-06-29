<?php
require_once 'db_config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get car details if provided
$car_id = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$car = null;

if ($car_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM cars WHERE id = ?");
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $car = $result->fetch_assoc();
    $stmt->close();
}

// Generate warranty number
$warranty_number = 'W' . date('Ymd') . str_pad($car_id, 4, '0', STR_PAD_LEFT);

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Get user credentials
$stmt = $conn->prepare("SELECT uc.*, u.email FROM user_credentials uc JOIN users u ON uc.user_id = u.id WHERE uc.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$credentials = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_warranty'])) {
    // Check if required fields are present
    if (!isset($_POST['car_id']) || !isset($_POST['warranty_type'])) {
        $error_message = "Missing required warranty information.";
    } else {
        $car_id = (int)$_POST['car_id'];
        $warranty_type = $_POST['warranty_type'];
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+3 years')); // 3-year warranty by default

        // Check if user has provided their credentials
        if (!$credentials) {
            $error_message = "Please complete your profile information first.";
        } else {
            // Insert warranty record
            $stmt = $conn->prepare("INSERT INTO warranties (user_id, car_id, warranty_type, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iisss", $user_id, $car_id, $warranty_type, $start_date, $end_date);

            if ($stmt->execute()) {
                $success_message = "Warranty registration successful!";
            } else {
                $error_message = "Error registering warranty. Please try again.";
            }
        }
    }
}

// Get user's warranties
$stmt = $conn->prepare("
    SELECT w.*, c.make, c.model, c.year 
    FROM warranties w 
    JOIN cars c ON w.car_id = c.id 
    WHERE w.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$warranties = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $user_id = $_SESSION['user_id'];

    // First, get the full appointment details for debugging
    $stmt = $conn->prepare("
        SELECT a.*, p.name as part_name 
        FROM appointments a 
        LEFT JOIN car_parts p ON a.part_id = p.id 
        WHERE a.id = ? AND a.user_id = ?
    ");
    $stmt->bind_param("ii", $appointment_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();

    if ($appointment) {
        // Check if it's a part order (part_id is not null)
        if ($appointment['part_id'] !== null) {
            // Delete the part order
            $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ? AND user_id = ? AND part_id IS NOT NULL");
            $stmt->bind_param("ii", $appointment_id, $user_id);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Part order for " . htmlspecialchars($appointment['part_name']) . " removed successfully.";
            } else {
                $_SESSION['error'] = "Error removing part order. Please try again.";
            }
        } else {
            $_SESSION['error'] = "Car appointments cannot be removed.";
        }
    } else {
        $_SESSION['error'] = "Invalid order or you don't have permission to remove it.";
    }
    
    header('Location: warranty.php');
    exit();
}

// Get user's appointments
$stmt = $conn->prepare("
    SELECT a.*, 
           c.make as car_make, c.model as car_model, c.year as car_year,
           p.name as part_name, p.type as part_type, p.brand as part_brand
    FROM appointments a
    LEFT JOIN cars c ON a.car_id = c.id
    LEFT JOIN car_parts p ON a.part_id = p.id
    WHERE a.user_id = ?
    ORDER BY a.appointment_date ASC, a.appointment_time ASC
");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments & Orders - Luxury Auto Gallery</title>
    <link rel="stylesheet" href="CarCss.css">
    <style>
        .appointments-section {
            padding: 2rem 0;
        }

        .appointment-card {
            background: var(--background-color);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(197, 164, 126, 0.2);
        }

        .appointment-title {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin: 0;
        }

        .appointment-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-completed {
            background: #cce5ff;
            color: #004085;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .appointment-details {
            margin-bottom: 1rem;
        }

        .appointment-details p {
            margin: 0.5rem 0;
            color: var(--text-color);
        }

        .appointment-details strong {
            color: var(--accent-color);
        }

        .button-group {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .print-btn, .remove-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background 0.3s ease;
            text-align: center;
            flex: 1;
        }

        .print-btn {
            background: var(--accent-color);
            color: white;
        }

        .print-btn:hover {
            background: var(--primary-color);
        }

        .remove-btn {
            background: #dc3545;
            color: white;
        }

        .remove-btn:hover {
            background: #c82333;
        }

        @media print {
            .no-print {
                display: none;
            }
            .appointment-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        .warranty-document {
            display: none;
            padding: 2rem;
            background: white;
            max-width: 800px;
            margin: 0 auto;
        }

        @media print {
            body * {
                visibility: hidden;
            }
            .warranty-document, .warranty-document * {
                visibility: visible;
            }
            .warranty-document {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }

        .warranty-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--accent-color);
        }

        .warranty-content {
            margin-bottom: 2rem;
        }

        .warranty-footer {
            margin-top: 3rem;
            padding-top: 1rem;
            border-top: 1px solid #ddd;
        }

        .warranty-signature {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="warranty-document">
        <div class="warranty-header">
            <h1>Luxury Auto Gallery</h1>
            <h2>Warranty Certificate</h2>
            <p>Warranty Number: <?php echo $warranty_number; ?></p>
        </div>

        <div class="warranty-content">
            <h3>Warranty Details</h3>
            <?php if ($car): ?>
                <p><strong>Vehicle:</strong> <?php echo htmlspecialchars($car['make'] . ' ' . $car['model'] . ' (' . $car['year'] . ')'); ?></p>
                <p><strong>VIN:</strong> <?php echo htmlspecialchars($car['vin']); ?></p>
            <?php endif; ?>
            
            <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($credentials['full_name'] ?? ''); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($credentials['email'] ?? ''); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($credentials['phone'] ?? ''); ?></p>
            
            <h3>Warranty Coverage</h3>
            <p>This warranty covers:</p>
            <ul>
                <li>Manufacturing defects in materials and workmanship</li>
                <li>Engine and transmission components</li>
                <li>Electrical systems</li>
                <li>Air conditioning and heating systems</li>
                <li>Suspension and steering components</li>
            </ul>
            
            <p><strong>Warranty Period:</strong> 3 years from date of purchase</p>
            <p><strong>Start Date:</strong> <?php echo date('F j, Y'); ?></p>
            <p><strong>End Date:</strong> <?php echo date('F j, Y', strtotime('+3 years')); ?></p>
        </div>

        <div class="warranty-footer">
            <p>This warranty is non-transferable and valid only for the original purchaser.</p>
            <p>For warranty claims, please contact our service department with your warranty number.</p>
            
            <div class="warranty-signature">
                <div>
                    <div class="signature-line">Customer Signature</div>
                </div>
                <div>
                    <div class="signature-line">Authorized Dealer</div>
                </div>
            </div>
        </div>
    </div>

    <section class="appointments-section">
        <div class="container">
            <h1>My Appointments & Orders</h1>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if (empty($appointments)): ?>
                <p>You have no appointments or orders.</p>
            <?php else: ?>
                <?php foreach ($appointments as $appointment): ?>
                    <div class="appointment-card">
                        <div class="appointment-header">
                            <h2 class="appointment-title">
                                <?php if ($appointment['car_id']): ?>
                                    Car Purchase Appointment
                                <?php else: ?>
                                    Part Order
                                <?php endif; ?>
                            </h2>
                            <span class="appointment-status status-<?php echo strtolower($appointment['status']); ?>">
                                <?php echo ucfirst($appointment['status']); ?>
                            </span>
                        </div>

                        <div class="appointment-details">
                            <?php if ($appointment['car_id']): ?>
                                <p><strong>Car:</strong> <?php echo htmlspecialchars($appointment['car_make'] . ' ' . $appointment['car_model'] . ' (' . $appointment['car_year'] . ')'); ?></p>
                                <p><strong>Appointment Date:</strong> <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?></p>
                                <p><strong>Appointment Time:</strong> <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></p>
                            <?php else: ?>
                                <p><strong>Part:</strong> <?php echo htmlspecialchars($appointment['part_name']); ?></p>
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($appointment['part_type']); ?></p>
                                <p><strong>Brand:</strong> <?php echo htmlspecialchars($appointment['part_brand']); ?></p>
                                <p><strong>Delivery Date:</strong> <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?></p>
                            <?php endif; ?>
                            <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($appointment['purchase_date'])); ?></p>
                        </div>

                        <div class="button-group">
                            <?php if (!$appointment['car_id']): // Only show print button for parts ?>
                            <button class="print-btn no-print" onclick="redirectToWarranty('<?php echo htmlspecialchars($appointment['part_name']); ?>', '<?php echo htmlspecialchars($appointment['part_type']); ?>', '<?php echo htmlspecialchars($appointment['part_brand']); ?>')">Print Warranty</button>
                            <?php endif; ?>
                            <?php if (!$appointment['car_id']): // Only show remove button for part orders ?>
                            <form method="POST" style="margin: 0; flex: 1;">
                                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                <button type="submit" name="delete_appointment" class="remove-btn no-print" onclick="return confirm('Are you sure you want to remove this part order?')">Remove</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php include 'footer.php'; ?>

    <script>
    function redirectToWarranty(partName, partType, partBrand) {
        const params = new URLSearchParams({
            name: '<?php echo htmlspecialchars($credentials['full_name'] ?? ''); ?>',
            email: '<?php echo htmlspecialchars($credentials['email'] ?? ''); ?>',
            phone: '<?php echo htmlspecialchars($credentials['phone'] ?? ''); ?>',
            part_name: partName,
            part_type: partType,
            part_brand: partBrand,
            start_date: '<?php echo date('F j, Y'); ?>',
            end_date: '<?php echo date('F j, Y', strtotime('+3 years')); ?>'
        });
        
        window.open('printwarranty.html?' + params.toString(), '_blank');
    }
    </script>
</body>
</html> 