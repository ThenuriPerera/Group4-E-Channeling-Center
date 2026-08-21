<?php
session_start(); // Start session at the top
require_once 'includes/db_connect.php';

// --- Check if user is logged in ---
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// --- Collect & sanitize input ---
$doctorId = (int)($_POST['doctor_id'] ?? 0);
$patientName = trim($_POST['patient_name'] ?? '');
$patientAge = (int)($_POST['patient_age'] ?? 0);
$patientContact = trim($_POST['patient_contact'] ?? '');
$appointmentDate = trim($_POST['appointment_date'] ?? '');
$userId = $_SESSION['user_id']; // Get the logged-in user ID

$errors = [];

// --- Server-side validation ---
if ($patientName === '' || strlen($patientName) < 2) {
    $errors[] = "Please enter a valid name.";
}
if ($patientAge <= 0 || $patientAge > 120) {
    $errors[] = "Please enter a valid age.";
}
if (!preg_match('/^[0-9]{10}$/', $patientContact)) {
    $errors[] = "Please enter a valid 10-digit contact number.";
}
if ($appointmentDate === '' || strtotime($appointmentDate) < strtotime(date('Y-m-d'))) {
    $errors[] = "Please choose a valid future appointment date.";
}

// --- Confirm the doctor exists and get the fee ---
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE doctor_id = ?");
$stmt->execute([$doctorId]);
$doctor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$doctor) {
    $errors[] = "Selected doctor does not exist.";
}

if (!empty($errors)) {
    $pageTitle = "Booking Error";
    $basePath = "";
    include 'includes/header.php';
    echo '<div class="form-wrapper"><h2>Booking Error</h2>';
    foreach ($errors as $err) {
        echo '<p class="error-msg">' . htmlspecialchars($err) . '</p>';
    }
    echo '<p><a href="booking.php?doctor_id=' . $doctorId . '">&larr; Go back and try again</a></p></div>';
    include 'includes/footer.php';
    exit;
}

// --- Insert appointment with user_id and 'Pending' status ---
$insert = $pdo->prepare("
    INSERT INTO appointments (doctor_id, user_id, patient_name, patient_age, patient_contact, appointment_date, amount_paid, payment_status)
    VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')
");
$insert->execute([
    $doctorId,
    $userId,
    $patientName,
    $patientAge,
    $patientContact,
    $appointmentDate,
    $doctor['channeling_fee']
]);

$appointmentId = $pdo->lastInsertId();

// --- Move on to the mock payment gateway ---
header("Location: payment.php?appointment_id=" . $appointmentId);
exit;