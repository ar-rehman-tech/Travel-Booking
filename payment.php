<?php
$pageTitle = 'Payment';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/session.php';

if (!isLoggedIn()) redirect(SITE_URL . '/auth/login.php');

$booking_id = (int)($_GET['booking_id'] ?? 0);
$uid = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("SELECT b.*, p.title, d.name as dest_name FROM bookings b JOIN packages p ON b.package_id = p.id JOIN destinations d ON p.destination_id = d.id WHERE b.id = ? AND b.user_id = ?");
$stmt->bind_param("ii", $booking_id, $uid);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) redirect(SITE_URL . '/user/my-bookings.php');

// Already paid - redirect to confirmation
if ($booking['status'] === 'confirmed') {
    redirect(SITE_URL . '/booking-confirmation.php?id=' . $booking_id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request. Please try again.');
        redirect(SITE_URL . '/payment.php?booking_id=' . $booking_id);
    }

    $method = sanitize($_POST['payment_method'] ?? 'credit_card');
    $card_raw = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
    $card_last = strlen($card_raw) >= 4 ? substr($card_raw, -4) : '';
    $txn_id = 'TXN' . strtoupper(substr(md5(uniqid()), 0, 12));

    // Use prepared statement - fixed SQL injection
    $ps = $conn->prepare("INSERT INTO payments (booking_id, user_id, amount, payment_method, transaction_id, card_last_four, status, paid_at) VALUES (?, ?, ?, ?, ?, ?, 'success', NOW())");
    $ps->bind_param("iidsss", $booking_id, $uid, $booking['final_amount'], $method, $txn_id, $card_last);
    $ps->execute();

    // Fixed SQL injection: use prepared statement
    $upd = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ?");
    $upd->bind_param("ii", $booking_id, $uid);
    $upd->execute();

    setFlash('success', 'Payment successful! Your booking is confirmed.');
    redirect(SITE_URL . '/booking-confirmation.php?id=' . $booking_id);
}

include 'includes/header.php';
?>

<div class="page-header">
    <h1>Secure Payment</h1>
    <p>Complete your booking payment</p>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-7" data-aos="fade-up">
                <!-- Demo Payment Card Preview -->
                <div class="payment-card mb-4">
                    <div class="d-flex justify-content-between">
                        <span class="small" style="opacity:0.7;">CREDIT CARD</span>
                        <i class="bi bi-credit-card-2-front fs-4"></i>
                    </div>
                    <div class="card-number" id="cardPreview">•••• •••• •••• ••••</div>
                    <div class="d-flex justify-content-between">
                        <div><span class="small" style="opacity:0.5;">CARDHOLDER</span><br><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                        <div><span class="small" style="opacity:0.5;">EXPIRES</span><br><span id="expiryPreview">MM/YY</span></div>
                    </div>
                </div>

                <form method="POST" class="bg-white p-4 rounded-4 shadow-sm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">
                    <h5 class="mb-4"><i class="bi bi-shield-lock me-2 text-success"></i>Payment Details</h5>

                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <div class="d-flex gap-3 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="credit_card" id="cc" checked>
                                <label class="form-check-label" for="cc"><i class="bi bi-credit-card me-1"></i>Credit Card</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="debit_card" id="dc">
                                <label class="form-check-label" for="dc"><i class="bi bi-credit-card-2-front me-1"></i>Debit Card</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="upi" id="upi">
                                <label class="form-check-label" for="upi"><i class="bi bi-phone me-1"></i>UPI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" value="net_banking" id="nb">
                                <label class="form-check-label" for="nb"><i class="bi bi-bank me-1"></i>Net Banking</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Card Number</label>
                        <input type="text" name="card_number" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19" required
                               oninput="this.value=this.value.replace(/[^\d\s]/g,'');document.getElementById('cardPreview').innerText=this.value||'•••• •••• •••• ••••'">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Expiry Date</label>
                            <input type="text" name="expiry" class="form-control" placeholder="MM/YY" maxlength="5" required
                                   oninput="document.getElementById('expiryPreview').innerText=this.value||'MM/YY'">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CVV</label>
                            <input type="password" name="cvv" class="form-control" placeholder="•••" maxlength="4" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Cardholder Name</label>
                        <input type="text" name="cardholder" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" required>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Package</span><strong><?php echo htmlspecialchars($booking['title']); ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span><span><?php echo CURRENCY . number_format($booking['total_amount'], 2); ?></span>
                        </div>
                        <?php if ($booking['discount_amount'] > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount</span><span>-<?php echo CURRENCY . number_format($booking['discount_amount'], 2); ?></span>
                        </div>
                        <?php endif; ?>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong class="text-primary fs-5"><?php echo CURRENCY . number_format($booking['final_amount'], 2); ?></strong>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-hero w-100">
                        <i class="bi bi-lock me-2"></i>Pay <?php echo CURRENCY . number_format($booking['final_amount'], 2); ?>
                    </button>
                    <p class="text-center text-muted small mt-3"><i class="bi bi-shield-check me-1"></i>This is a demo payment gateway. No real charges.</p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
