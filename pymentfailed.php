<?php

require_once 'includes/db_connect.php';

$appointmentId =
    (int)($_GET['appointment_id'] ?? 0);

$pageTitle = "Payment Failed";
$basePath = "";

include 'includes/header.php';

?>

<div class="form-wrapper">

    <h2>Payment Failed ❌</h2>

    <p class="error-msg">
        Your payment could not be completed.
    </p>

    <div class="test-card">

        <h3>Test Card</h3>

        <p>
            Card:
            <strong>4242 4242 4242 4242</strong>
        </p>

        <p>
            Expiry:
            <strong>12/30</strong>
        </p>

        <p>
            CVV:
            <strong>123</strong>
        </p>

    </div>

    <p>

        <a
            href="payment.php?appointment_id=<?php
            echo $appointmentId;
            ?>"
        >
            &larr; Try Again
        </a>

    </p>

</div>

<?php include 'includes/footer.php'; ?>