<?php

require_once 'includes/db_connect.php';

$appointmentId =
    (int)($_GET['appointment_id'] ?? 0);


$stmt = $pdo->prepare("
    SELECT
        a.*,
        d.name AS doctor_name,
        d.specialization

    FROM appointments a

    JOIN doctors d
        ON a.doctor_id = d.doctor_id

    WHERE a.appointment_id = ?
");

$stmt->execute([$appointmentId]);

$appointment =
    $stmt->fetch(PDO::FETCH_ASSOC);


$pageTitle = "Payment Successful";
$basePath = "";

include 'includes/header.php';

?>

<div class="form-wrapper">

<?php if ($appointment): ?>

    <h2>Payment Successful ✅</h2>

    <p class="success-msg">
        Your payment has been completed successfully.
    </p>


    <div class="summary-box">

        <p>
            <strong>Appointment ID:</strong>
            #<?php
            echo $appointment['appointment_id'];
            ?>
        </p>

        <p>
            <strong>Doctor:</strong>
            <?php
            echo htmlspecialchars(
                $appointment['doctor_name']
            );
            ?>
        </p>

        <p>
            <strong>Patient:</strong>
            <?php
            echo htmlspecialchars(
                $appointment['patient_name']
            );
            ?>
        </p>

        <p>
            <strong>Appointment Date:</strong>
            <?php
            echo htmlspecialchars(
                $appointment['appointment_date']
            );
            ?>
        </p>

        <p>
            <strong>Amount Paid:</strong>
            Rs.
            <?php
            echo number_format(
                $appointment['amount_paid'],
                2
            );
            ?>
        </p>

        <p>
            <strong>Payment Status:</strong>
            Paid
        </p>

        <p>
            <strong>Card:</strong>
            **** **** ****
            <?php
            echo htmlspecialchars(
                $appointment['card_last4']
            );
            ?>
        </p>

    </div>


    <p class="success-msg">
        Your appointment has been confirmed.
        Please arrive 15 minutes early.
    </p>


    <p>
        <a href="index.php">
            &larr; Back to Home
        </a>
    </p>


<?php else: ?>

    <h2>Appointment Not Found</h2>

    <a href="index.php">
        &larr; Back to Home
    </a>

<?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>