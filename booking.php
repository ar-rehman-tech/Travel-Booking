<?php
$pageTitle = 'Book Package';

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/session.php';
if (!isLoggedIn()) {
    setFlash('error', 'Please login to book a package.');
    redirect(SITE_URL . '/auth/login.php');
}

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request. Please try again.');
        redirect(SITE_URL . '/booking.php?id=' . $id);
    }

    $travel_date = sanitize($_POST['travel_date']);
    $adults      = max(1, (int)$_POST['adults']);
    $children    = max(0, (int)$_POST['children']);
    $special     = sanitize($_POST['special_requests'] ?? '');
    $coupon_code = sanitize($_POST['coupon_code'] ?? '');

    // Validate travel date is in the future
    if (strtotime($travel_date) <= strtotime('today')) {
        setFlash('error', 'Travel date must be in the future.');
        redirect(SITE_URL . '/booking.php?id=' . $id);
    }

    $stmt = $conn->prepare("SELECT p.*, d.name as dest_name FROM packages p JOIN destinations d ON p.destination_id = d.id WHERE p.id = ? AND p.status = 'active'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $pkg = $stmt->get_result()->fetch_assoc();

    if (!$pkg) { redirect(SITE_URL . '/packages.php'); }

    $price    = $pkg['discount_price'] ?: $pkg['price'];
    $total    = $price * ($adults + ($children * 0.5));
    $discount = 0;
    $coupon   = null;

    // Validate coupon but DO NOT increment yet
    if ($coupon_code) {
        $cs = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND status = 'active' AND valid_until >= CURDATE() AND used_count < usage_limit");
        $cs->bind_param("s", $coupon_code);
        $cs->execute();
        $coupon = $cs->get_result()->fetch_assoc();
        if ($coupon) {
            if ($coupon['discount_type'] === 'percentage') {
                $discount = min($total * $coupon['discount_value'] / 100, $coupon['max_discount'] ?? $total);
            } else {
                $discount = min((float)$coupon['discount_value'], $total);
            }
        }
    }

    $final       = max(0, $total - $discount);
    $ref         = generateBookingRef();
    $return_date = date('Y-m-d', strtotime($travel_date . ' + ' . $pkg['duration_days'] . ' days'));

    $bs = $conn->prepare("INSERT INTO bookings (booking_ref, user_id, package_id, travel_date, return_date, adults, children, total_amount, discount_amount, final_amount, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $uid = (int)$_SESSION['user_id'];
    $bs->bind_param("siissiiddds", $ref, $uid, $id, $travel_date, $return_date, $adults, $children, $total, $discount, $final, $special);

    if ($bs->execute()) {
        $booking_id = $conn->insert_id;

        // Fix 11: Only increment coupon AFTER booking succeeds
        if ($coupon) {
            $cu = $conn->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
            $cu->bind_param("i", $coupon['id']);
            $cu->execute();
        }

        // Save traveler details
        for ($i = 1; $i <= $adults + $children; $i++) {
            $tname   = sanitize($_POST["traveler_name_$i"] ?? '');
            $tage    = max(0, (int)($_POST["traveler_age_$i"] ?? 0));
            $tgender = sanitize($_POST["traveler_gender_$i"] ?? 'male');
            if ($tname) {
                $ts = $conn->prepare("INSERT INTO travelers (booking_id, full_name, age, gender) VALUES (?, ?, ?, ?)");
                $ts->bind_param("isis", $booking_id, $tname, $tage, $tgender);
                $ts->execute();
            }
        }

        redirect(SITE_URL . '/payment.php?booking_id=' . $booking_id);
    } else {
        setFlash('error', 'Booking failed. Please try again.');
        redirect(SITE_URL . '/booking.php?id=' . $id);
    }
}

$stmt = $conn->prepare("SELECT p.*, d.name as dest_name FROM packages p JOIN destinations d ON p.destination_id = d.id WHERE p.id = ? AND p.status = 'active'");
$stmt->bind_param("i", $id);
$stmt->execute();
$pkg = $stmt->get_result()->fetch_assoc();

if (!$pkg) { redirect(SITE_URL . '/packages.php'); }

include 'includes/header.php';
?>

<div class="page-header">
    <h1>Book Your Trip</h1>
    <p><?php echo htmlspecialchars($pkg['title']); ?></p>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8" data-aos="fade-up">
                <form method="POST" id="bookingForm">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">

                    <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                        <h5><i class="bi bi-calendar3 me-2 text-primary"></i>Travel Details</h5>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label">Travel Date *</label>
                                <input type="date" name="travel_date" class="form-control" required
                                       min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Adults *</label>
                                <select name="adults" id="adults" class="form-select" onchange="updateTravelers()">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Children</label>
                                <select name="children" id="children" class="form-select" onchange="updateTravelers()">
                                    <?php for ($i = 0; $i <= 5; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-4 shadow-sm mb-4" id="travelersSection">
                        <h5><i class="bi bi-people me-2 text-primary"></i>Traveler Details</h5>
                        <div id="travelersContainer"></div>
                    </div>

                    <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                        <h5><i class="bi bi-chat-text me-2 text-primary"></i>Special Requests</h5>
                        <textarea name="special_requests" class="form-control" rows="3"
                                  placeholder="Any dietary requirements, accessibility needs, etc."></textarea>
                    </div>

                    <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                        <h5><i class="bi bi-ticket-perforated me-2 text-primary"></i>Coupon Code</h5>
                        <div class="input-group">
                            <input type="text" name="coupon_code" id="couponInput" class="form-control" placeholder="Enter coupon code" maxlength="50">
                            <button type="button" class="btn btn-outline-primary" onclick="applyCoupon()">Apply</button>
                        </div>
                        <div id="couponMsg" class="mt-2 small"></div>
                    </div>

                    <button type="submit" class="btn btn-hero w-100">
                        <i class="bi bi-credit-card me-2"></i>Proceed to Payment
                    </button>
                </form>
            </div>

            <div class="col-lg-4" data-aos="fade-left">
                <div class="booking-summary">
                    <h5>Booking Summary</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Package</span>
                        <strong><?php echo htmlspecialchars($pkg['title']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Destination</span>
                        <span><?php echo htmlspecialchars($pkg['dest_name']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Duration</span>
                        <span><?php echo $pkg['duration_days']; ?>D/<?php echo $pkg['duration_nights']; ?>N</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Price per person</span>
                        <strong class="text-primary"><?php echo CURRENCY . number_format($pkg['discount_price'] ?: $pkg['price'], 0); ?></strong>
                    </div>
                    <hr>
                    <?php
                    $pkgImages = [
                        1=>'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&h=400&fit=crop&q=80',
                        2=>'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&h=400&fit=crop&q=80',
                        3=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=600&h=400&fit=crop&q=80',
                        4=>'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=600&h=400&fit=crop&q=80',
                        5=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=400&fit=crop&q=80',
                        6=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=600&h=400&fit=crop&q=80',
                    ];
                    $imgUrl = $pkgImages[$pkg['id']] ?? 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&h=400&fit=crop&q=80';
                    ?>
                    <img src="<?php echo $imgUrl; ?>"
                         class="w-100 rounded-3" alt="<?php echo htmlspecialchars($pkg['dest_name']); ?>">
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function updateTravelers() {
    const adults = parseInt(document.getElementById('adults').value);
    const children = parseInt(document.getElementById('children').value);
    const container = document.getElementById('travelersContainer');
    container.innerHTML = '';
    for (let i = 1; i <= adults + children; i++) {
        const type = i <= adults ? 'Adult' : 'Child';
        const num  = i <= adults ? i : i - adults;
        container.innerHTML += `
        <div class="row g-3 mb-3 p-3 bg-light rounded-3">
            <div class="col-12"><strong>${type} ${num}</strong></div>
            <div class="col-md-4">
                <input type="text" name="traveler_name_${i}" class="form-control" placeholder="Full Name" required maxlength="100">
            </div>
            <div class="col-md-4">
                <input type="number" name="traveler_age_${i}" class="form-control" placeholder="Age" required min="0" max="120">
            </div>
            <div class="col-md-4">
                <select name="traveler_gender_${i}" class="form-select">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </div>`;
    }
}
updateTravelers();

function applyCoupon() {
    const code = document.getElementById('couponInput').value.trim();
    const msg  = document.getElementById('couponMsg');
    if (!code) { msg.innerHTML = '<span class="text-danger">Please enter a coupon code.</span>'; return; }
    fetch(SITE_URL + '/ajax/check_coupon.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'coupon_code=' + encodeURIComponent(code)
    })
    .then(r => r.json())
    .then(data => {
        if (data.valid) {
            msg.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>' + data.message + '</span>';
        } else {
            msg.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>' + data.message + '</span>';
        }
    })
    .catch(() => { msg.innerHTML = '<span class="text-danger">Could not validate coupon.</span>'; });
}
</script>

<?php include 'includes/footer.php'; ?>
