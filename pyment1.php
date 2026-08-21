<?php

require_once 'includes/db_connect.php';

$pageTitle = "Payment";
$basePath = "";

$appointmentId = isset($_GET['appointment_id'])
    ? (int)$_GET['appointment_id']
    : 0;

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

$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';

if (!$appointment) {

    echo '
    <div class="form-wrapper">
        <p class="error-msg">
            Appointment not found.
        </p>

        <a href="index.php">
            &larr; Back to home
        </a>
    </div>
    ';

    include 'includes/footer.php';
    exit;
}

if ($appointment['payment_status'] === 'Paid') {

    echo '
    <div class="form-wrapper">
        <p class="success-msg">
            This appointment is already paid for.
        </p>

        <a href="index.php">
            &larr; Back to home
        </a>
    </div>
    ';

    include 'includes/footer.php';
    exit;
}

?>

<div class="form-wrapper">

    <h2>Secure Payment</h2>

    <p class="subtitle">
        Mock payment gateway for testing only.
        No real payment will be processed.
    </p>

    <div class="summary-box">

        <p>
            <strong>Appointment ID:</strong>
            #<?php echo $appointment['appointment_id']; ?>
        </p>

        <p>
            <strong>Doctor:</strong>
            <?php echo htmlspecialchars($appointment['doctor_name']); ?>
        </p>

        <p>
            <strong>Specialization:</strong>
            <?php echo htmlspecialchars($appointment['specialization']); ?>
        </p>

        <p>
            <strong>Patient:</strong>
            <?php echo htmlspecialchars($appointment['patient_name']); ?>
        </p>

        <p>
            <strong>Appointment Date:</strong>
            <?php echo htmlspecialchars($appointment['appointment_date']); ?>
        </p>

        <p>
            <strong>Amount Due:</strong>
            Rs.
            <?php echo number_format($appointment['amount_paid'], 2); ?>
        </p>

    </div>


    <form
        action="process_payment.php"
        method="POST"
        id="paymentForm"
    >

        <input
            type="hidden"
            name="appointment_id"
            value="<?php echo $appointment['appointment_id']; ?>"
        >


        <div class="form-group">

            <label for="card_name">
                Name on Card
            </label>

            <input
                type="text"
                id="card_name"
                name="card_name"
                placeholder="K. Perera"
                required
            >

        </div>


        <div class="form-group">

            <label for="card_number">
                Card Number
            </label>

            <input
                type="text"
                id="card_number"
                name="card_number"
                placeholder="4242 4242 4242 4242"
                maxlength="19"
                required
            >

        </div>


        <div class="card-row">

            <div class="form-group">

                <label for="card_expiry">
                    Expiry (MM/YY)
                </label>

                <input
                    type="text"
                    id="card_expiry"
                    name="card_expiry"
                    placeholder="12/30"
                    maxlength="5"
                    required
                >

            </div>


            <div class="form-group">

                <label for="card_cvv">
                    CVV
                </label>

                <input
                    type="password"
                    id="card_cvv"
                    name="card_cvv"
                    placeholder="123"
                    maxlength="3"
                    required
                >

            </div>

        </div>


        <button
            type="submit"
            class="btn-primary"
        >
            Pay Rs.
            <?php echo number_format($appointment['amount_paid'], 2); ?>
        </button>

    </form>


    <div class="test-card">

        <h3>Test Payment</h3>

        <p>
            Card Number:
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

</div>


<script>

const cardNumber =
    document.getElementById('card_number');

cardNumber.addEventListener('input', function () {

    let digits =
        this.value
        .replace(/\D/g, '')
        .slice(0, 16);

    this.value =
        digits
        .replace(/(.{4})/g, '$1 ')
        .trim();

});


const cardExpiry =
    document.getElementById('card_expiry');

cardExpiry.addEventListener('input', function () {

    let digits =
        this.value
        .replace(/\D/g, '')
        .slice(0, 4);

    if (digits.length >= 3) {

        this.value =
            digits.slice(0, 2) +
            '/' +
            digits.slice(2);

    } else {

        this.value = digits;

    }

});


document
.getElementById('paymentForm')
.addEventListener('submit', function (e) {

    const digits =
        cardNumber.value.replace(/\D/g, '');

    if (digits.length !== 16) {

        e.preventDefault();

        alert(
            'Please enter a valid 16-digit card number.'
        );
    }

});

</script>


<?php include 'includes/footer.php'; ?>