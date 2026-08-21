<?php

require_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header('Location: index.php');
    exit;
}


$appointmentId =
    (int)($_POST['appointment_id'] ?? 0);

$cardName =
    trim($_POST['card_name'] ?? '');

$cardNumber =
    preg_replace(
        '/\D/',
        '',
        $_POST['card_number'] ?? ''
    );

$cardExpiry =
    trim($_POST['card_expiry'] ?? '');

$cardCvv =
    trim($_POST['card_cvv'] ?? '');

$errors = [];


/*
|--------------------------------------------------------------------------
| Validate appointment
|--------------------------------------------------------------------------
*/

if ($appointmentId <= 0) {

    $errors[] =
        "Invalid appointment.";

}


/*
|--------------------------------------------------------------------------
| Validate card
|--------------------------------------------------------------------------
*/

if ($cardName === '') {

    $errors[] =
        "Name on card is required.";

}


if (strlen($cardNumber) !== 16) {

    $errors[] =
        "Card number must be 16 digits.";

}


if (
    !preg_match(
        '/^(0[1-9]|1[0-2])\/\d{2}$/',
        $cardExpiry
    )
) {

    $errors[] =
        "Expiry must be in MM/YY format.";

}


if (!preg_match('/^[0-9]{3}$/', $cardCvv)) {

    $errors[] =
        "CVV must be 3 digits.";

}


/*
|--------------------------------------------------------------------------
| Get appointment
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM appointments
    WHERE appointment_id = ?
");

$stmt->execute([$appointmentId]);

$appointment =
    $stmt->fetch(PDO::FETCH_ASSOC);


if (!$appointment) {

    $errors[] =
        "Appointment not found.";

}


/*
|--------------------------------------------------------------------------
| Check payment status
|--------------------------------------------------------------------------
*/

if (
    $appointment &&
    $appointment['payment_status'] === 'Paid'
) {

    $errors[] =
        "This appointment is already paid.";

}


/*
|--------------------------------------------------------------------------
| Display validation errors
|--------------------------------------------------------------------------
*/

if (!empty($errors)) {

    $pageTitle = "Payment Failed";
    $basePath = "";

    include 'includes/header.php';

    ?>

    <div class="form-wrapper">

        <h2>Payment Failed ❌</h2>

        <?php foreach ($errors as $error): ?>

            <p class="error-msg">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endforeach; ?>

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

    <?php

    include 'includes/footer.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| MOCK PAYMENT GATEWAY
|--------------------------------------------------------------------------
|
| Test successful card:
|
| 4242 4242 4242 4242
|
|--------------------------------------------------------------------------
*/

$testCard =
    '4242424242424242';


/*
|--------------------------------------------------------------------------
| Payment declined
|--------------------------------------------------------------------------
*/

if ($cardNumber !== $testCard) {

    $pageTitle = "Payment Failed";
    $basePath = "";

    include 'includes/header.php';

    ?>

    <div class="form-wrapper">

        <h2>Payment Declined ❌</h2>

        <p class="error-msg">
            Your payment was declined.
        </p>

        <div class="test-card">

            <p>
                Use this test card:
            </p>

            <strong>
                4242 4242 4242 4242
            </strong>

            <p>
                Expiry: 12/30
            </p>

            <p>
                CVV: 123
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

    <?php

    include 'includes/footer.php';

    exit;
}


/*
|--------------------------------------------------------------------------
| Payment successful
|--------------------------------------------------------------------------
*/

// Store only last 4 digits
$cardLast4 =
    substr($cardNumber, -4);


/*
|--------------------------------------------------------------------------
| Update database
|--------------------------------------------------------------------------
*/

$update = $pdo->prepare("
    UPDATE appointments

    SET
        payment_status = 'Paid',
        card_last4 = ?

    WHERE appointment_id = ?
");

$update->execute([
    $cardLast4,
    $appointmentId
]);


/*
|--------------------------------------------------------------------------
| Send user to success page
|--------------------------------------------------------------------------
*/

header(
    'Location: payment_success.php?appointment_id='
    . $appointmentId
);

exit;