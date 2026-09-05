<?php
$pageTitle = 'Booking Confirmed';
include 'includes/header.php';

if (!isLoggedIn() && !isAdmin()) { redirect(SITE_URL . '/auth/login.php'); }

$bid  = (int)($_GET['id'] ?? 0);

if (isAdmin()) {
    $stmt = $conn->prepare("SELECT b.*, p.title, p.duration_days, p.duration_nights, p.inclusions, d.name as dest_name, d.country, pay.payment_method, pay.transaction_id, pay.paid_at FROM bookings b JOIN packages p ON b.package_id = p.id JOIN destinations d ON p.destination_id = d.id LEFT JOIN payments pay ON pay.booking_id = b.id WHERE b.id = ? ORDER BY pay.id DESC LIMIT 1");
    $stmt->bind_param('i', $bid);
} else {
    $uid  = (int)$_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT b.*, p.title, p.duration_days, p.duration_nights, p.inclusions, d.name as dest_name, d.country, pay.payment_method, pay.transaction_id, pay.paid_at FROM bookings b JOIN packages p ON b.package_id = p.id JOIN destinations d ON p.destination_id = d.id LEFT JOIN payments pay ON pay.booking_id = b.id WHERE b.id = ? AND b.user_id = ? ORDER BY pay.id DESC LIMIT 1");
    $stmt->bind_param('ii', $bid, $uid);
}
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$booking) {
    if (isAdmin()) {
        redirect(SITE_URL . '/admin/bookings.php');
    } else {
        redirect(SITE_URL . '/user/my-bookings.php');
    }
}

// Travelers
$tStmt = $conn->prepare("SELECT * FROM travelers WHERE booking_id = ?");
$tStmt->bind_param('i', $bid);
$tStmt->execute();
$travelers = $tStmt->get_result();
?>

<div style="padding-top:100px;"></div>
<section class="section-padding">
    <div class="container">
        <!-- Success Banner -->
        <div class="text-center mb-5 gsap-fade-up">
            <div class="mb-3" style="font-size:5rem;">✅</div>
            <h2 class="fw-bold">Booking Confirmed!</h2>
            <p class="text-muted">Your adventure is booked. Get ready for an amazing experience!</p>
            <span class="badge fs-6 py-2 px-4 rounded-pill" style="background:linear-gradient(135deg,#10b981,#059669);">
                <i class="bi bi-check-circle me-2"></i>Ref: <?php echo $booking['booking_ref']; ?>
            </span>
        </div>

        <!-- Invoice -->
        <div class="invoice-box gsap-fade-up" id="invoicePrint">
            <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="fw-bold mb-0"><i class="bi bi-globe-americas me-2"></i>TravelBook</h4>
                        <div class="small opacity-75 mt-1">Your Premium Travel Partner</div>
                    </div>
                    <div class="col-auto text-end">
                        <div class="invoice-status">✓ Confirmed</div>
                        <div class="small opacity-75 mt-2">Ref: <?php echo $booking['booking_ref']; ?></div>
                    </div>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase fw-semibold mb-2">Package Details</h6>
                    <div class="fw-bold fs-5"><?php echo htmlspecialchars($booking['title']); ?></div>
                    <div class="text-muted"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($booking['dest_name']); ?>, <?php echo htmlspecialchars($booking['country']); ?></div>
                    <div class="text-muted mt-1"><i class="bi bi-clock me-1"></i><?php echo $booking['duration_days']; ?> Days / <?php echo $booking['duration_nights']; ?> Nights</div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted small text-uppercase fw-semibold mb-2">Travel Info</h6>
                    <div><strong>Departure:</strong> <?php echo date('D, d M Y', strtotime($booking['travel_date'])); ?></div>
                    <div><strong>Return:</strong> <?php echo date('D, d M Y', strtotime($booking['return_date'])); ?></div>
                    <div><strong>Travelers:</strong> <?php echo $booking['adults']; ?> Adults<?php echo $booking['children'] > 0 ? ', ' . $booking['children'] . ' Children' : ''; ?></div>
                </div>
            </div>

            <!-- Traveler List -->
            <?php if ($travelers->num_rows > 0): ?>
            <h6 class="text-muted small text-uppercase fw-semibold mb-2">Traveler Details</h6>
            <table class="table table-sm table-bordered mb-4">
                <thead class="table-light"><tr><th>#</th><th>Name</th><th>Age</th><th>Gender</th></tr></thead>
                <tbody>
                    <?php $ti = 1; while ($t = $travelers->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $ti++; ?></td>
                        <td><?php echo htmlspecialchars($t['full_name']); ?></td>
                        <td><?php echo $t['age']; ?></td>
                        <td><?php echo ucfirst($t['gender'] ?? '—'); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <!-- Amount -->
            <div class="p-3 rounded-3 mb-4" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span><?php echo CURRENCY . number_format($booking['total_amount'], 2); ?></span>
                </div>
                <?php if ($booking['discount_amount'] > 0): ?>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Discount</span>
                    <span>-<?php echo CURRENCY . number_format($booking['discount_amount'], 2); ?></span>
                </div>
                <?php endif; ?>
                <hr class="my-2">
                <div class="d-flex justify-content-between fw-bold fs-5">
                    <span>Total Paid</span>
                    <span class="text-primary"><?php echo CURRENCY . number_format($booking['final_amount'], 2); ?></span>
                </div>
            </div>

            <!-- Payment Info -->
            <?php if ($booking['transaction_id']): ?>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="p-3 rounded-3 text-center" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <div class="small text-muted">Payment Method</div>
                        <div class="fw-semibold text-success"><?php echo ucwords(str_replace('_', ' ', $booking['payment_method'] ?? 'Card')); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3 text-center" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <div class="small text-muted">Transaction ID</div>
                        <div class="fw-semibold text-success" style="font-size:0.85rem;"><?php echo $booking['transaction_id']; ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3 text-center" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <div class="small text-muted">Paid On</div>
                        <div class="fw-semibold text-success"><?php echo $booking['paid_at'] ? date('d M Y', strtotime($booking['paid_at'])) : date('d M Y'); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($booking['special_requests']): ?>
            <div class="p-3 rounded-3 mb-4" style="background:#fefce8;border:1px solid #fde68a;">
                <div class="fw-semibold small mb-1"><i class="bi bi-chat-text me-1"></i>Special Requests</div>
                <p class="small text-muted mb-0"><?php echo htmlspecialchars($booking['special_requests']); ?></p>
            </div>
            <?php endif; ?>

            <div class="text-muted small text-center">
                Booked on <?php echo date('d M Y, h:i A', strtotime($booking['created_at'])); ?> · TravelBook © <?php echo date('Y'); ?>
            </div>
        </div>

        <!-- Actions -->
        <div class="d-flex flex-wrap justify-content-center gap-3 mt-4 no-print gsap-fade-up">
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="bi bi-printer me-2"></i>Print Invoice
            </button>
            <a href="<?php echo 'https://wa.me/?text=' . urlencode('I just booked ' . $booking['title'] . ' via TravelBook! Ref: ' . $booking['booking_ref']); ?>"
               target="_blank" class="btn btn-success btn-lg">
                <i class="bi bi-whatsapp me-2"></i>Share on WhatsApp
            </a>
            <a href="user/my-bookings.php" class="btn btn-outline-primary btn-lg">
                <i class="bi bi-journal-text me-2"></i>My Bookings
            </a>
            <a href="packages.php" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-compass me-2"></i>Book Another Trip
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
