<?php
$pageTitle = 'Package Details';
include 'includes/header.php';

$id   = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT p.*, d.name as dest_name, d.country, d.description as dest_desc FROM packages p JOIN destinations d ON p.destination_id = d.id WHERE p.id = ? AND p.status = 'active'");
$stmt->bind_param('i', $id);
$stmt->execute();
$pkg = $stmt->get_result()->fetch_assoc();
if (!$pkg) { header('Location: ' . SITE_URL . '/packages.php'); exit; }

// Reviews
$revStmt = $conn->prepare("SELECT r.*, u.full_name, u.city FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.package_id = ? AND r.status = 'approved' ORDER BY r.created_at DESC LIMIT 10");
$revStmt->bind_param('i', $id);
$revStmt->execute();
$reviews = $revStmt->get_result();

// Avg rating
$rStmt = $conn->prepare("SELECT AVG(rating) as avg_r, COUNT(*) as cnt FROM reviews WHERE package_id = ? AND status = 'approved'");
$rStmt->bind_param('i', $id);
$rStmt->execute();
$rData   = $rStmt->get_result()->fetch_assoc();
$avgRating = round($rData['avg_r'] ?? 4.8, 1);
$rCount    = $rData['cnt'] ?? 0;

// Related packages
$relStmt = $conn->prepare("SELECT p.*, d.name as dest_name FROM packages p JOIN destinations d ON p.destination_id = d.id WHERE p.destination_id = ? AND p.id != ? AND p.status = 'active' LIMIT 3");
$relStmt->bind_param('ii', $pkg['destination_id'], $id);
$relStmt->execute();
$related = $relStmt->get_result();

$displayPrice = $pkg['discount_price'] ?: $pkg['price'];
$hasDiscount  = !empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price'];
$discountPct  = $hasDiscount ? round((1 - $pkg['discount_price']/$pkg['price'])*100) : 0;

$pkgImages = [
    1=>'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1200&h=600&fit=crop&q=80',
    2=>'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=1200&h=600&fit=crop&q=80',
    3=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=1200&h=600&fit=crop&q=80',
    4=>'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=1200&h=600&fit=crop&q=80',
    5=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=1200&h=600&fit=crop&q=80',
    6=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=1200&h=600&fit=crop&q=80',
];
$galleryImgs = [
    "https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=400&h=400&fit=crop&q=70",
    "https://images.unsplash.com/photo-1500259571355-332da5cb07aa?w=400&h=400&fit=crop&q=70",
    "https://images.unsplash.com/photo-1503220317375-aaad61436b1b?w=400&h=400&fit=crop&q=70",
    "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&h=400&fit=crop&q=70",
    "https://images.unsplash.com/photo-1488085061387-422e29b40080?w=400&h=400&fit=crop&q=70",
    "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=400&h=400&fit=crop&q=70",
];
$heroImg = $pkgImages[$id] ?? 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1200&h=600&fit=crop&q=80';
?>

<!-- Package Hero -->
<div class="pkg-hero">
    <img src="<?php echo $heroImg; ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>">
    <div class="pkg-hero-overlay text-white">
        <div class="w-100">
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb" style="--bs-breadcrumb-divider-color:rgba(255,255,255,0.5);">
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>" class="text-white opacity-75">Home</a></li>
                    <li class="breadcrumb-item"><a href="packages.php" class="text-white opacity-75">Packages</a></li>
                    <li class="breadcrumb-item active text-white"><?php echo htmlspecialchars($pkg['title']); ?></li>
                </ol>
            </nav>
            <div class="d-flex flex-wrap align-items-start gap-3">
                <div class="flex-grow-1">
                    <span class="badge rounded-pill mb-2" style="background:var(--gradient-warm);"><?php echo ucfirst($pkg['category']); ?></span>
                    <h1 class="h2 fw-bold mb-2"><?php echo htmlspecialchars($pkg['title']); ?></h1>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <span><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($pkg['dest_name']); ?>, <?php echo htmlspecialchars($pkg['country']); ?></span>
                        <span><i class="bi bi-clock me-1"></i><?php echo $pkg['duration_days']; ?> Days / <?php echo $pkg['duration_nights']; ?> Nights</span>
                        <span><i class="bi bi-people me-1"></i>Max <?php echo $pkg['max_persons']; ?> persons</span>
                        <div class="d-flex align-items-center gap-1">
                            <span style="color:#f59e0b;"><?php echo str_repeat('★', round($avgRating)); ?></span>
                            <span class="fw-bold"><?php echo $avgRating; ?></span>
                            <span class="opacity-75">(<?php echo $rCount ?: '124'; ?> reviews)</span>
                        </div>
                    </div>
                </div>
                <div class="text-md-end">
                    <div class="h3 fw-bold mb-0"><?php echo CURRENCY . number_format($displayPrice, 0); ?></div>
                    <?php if ($hasDiscount): ?><small class="opacity-75 text-decoration-line-through"><?php echo CURRENCY . number_format($pkg['price'], 0); ?></small><?php endif; ?>
                    <div class="small opacity-75">per person</div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">

                <!-- Tabs -->
                <ul class="nav pkg-tabs mb-0" id="pkgTabs">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#overview">Overview</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#itinerary">Itinerary</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#inclusions">Inclusions</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gallery-tab">Gallery</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews-tab">Reviews (<?php echo $rCount ?: '124'; ?>)</button></li>
                </ul>
                <div class="tab-content pkg-tabs-content">
                    <!-- Overview -->
                    <div class="tab-pane fade show active" id="overview">
                        <h5 class="fw-bold mb-3">About This Package</h5>
                        <p class="text-muted" style="line-height:1.8;"><?php echo nl2br(htmlspecialchars($pkg['description'])); ?></p>
                        <?php if ($pkg['dest_desc']): ?>
                        <h6 class="fw-bold mt-4 mb-2">About <?php echo htmlspecialchars($pkg['dest_name']); ?></h6>
                        <p class="text-muted" style="line-height:1.8;"><?php echo nl2br(htmlspecialchars($pkg['dest_desc'])); ?></p>
                        <?php endif; ?>
                        <div class="row g-3 mt-2">
                            <?php
                            $highlights = [
                                ['bi-airplane','Duration', $pkg['duration_days'].' Days / '.$pkg['duration_nights'].' Nights'],
                                ['bi-people',  'Group Size','Up to '.$pkg['max_persons'].' persons'],
                                ['bi-tag',     'Category',  ucfirst($pkg['category'])],
                                ['bi-calendar','Best Season',$pkg['start_date'] ? date('M Y', strtotime($pkg['start_date'])) : 'Year Round'],
                            ];
                            foreach ($highlights as [$icon, $label, $val]):
                            ?>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 rounded-3 bg-light">
                                    <i class="bi <?php echo $icon; ?> text-primary fs-4 d-block mb-1"></i>
                                    <div class="small text-muted"><?php echo $label; ?></div>
                                    <div class="fw-semibold small"><?php echo $val; ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Itinerary -->
                    <div class="tab-pane fade" id="itinerary">
                        <h5 class="fw-bold mb-4">Day-by-Day Itinerary</h5>
                        <?php
                        $itinerary = $pkg['itinerary'] ?? '';
                        $days = array_filter(explode("\n", $itinerary));
                        if (empty($days)) $days = ["Day 1: Arrival and check-in","Day 2: Explore local attractions","Day 3: Adventure activities","Day 4: Cultural experiences","Day 5: Leisure and shopping","Day 6: Departure"];
                        foreach ($days as $i => $day):
                            $parts = explode(':', $day, 2);
                            $dayLabel = trim($parts[0] ?? ('Day '.($i+1)));
                            $dayDesc  = trim($parts[1] ?? $day);
                        ?>
                        <div class="itinerary-day">
                            <div class="day-badge"><?php echo $i+1; ?></div>
                            <div>
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($dayLabel); ?></div>
                                <p class="text-muted mb-0 mt-1 small" style="line-height:1.7;"><?php echo htmlspecialchars($dayDesc); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Inclusions -->
                    <div class="tab-pane fade" id="inclusions">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-success mb-3"><i class="bi bi-check-circle me-2"></i>What's Included</h6>
                                <?php
                                $inc = $pkg['inclusions'] ? explode(',', $pkg['inclusions']) : ['Flights','Hotels','Breakfast','Tours','Transfers'];
                                foreach ($inc as $item): ?>
                                <div class="inclusion-item included">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span class="small"><?php echo htmlspecialchars(trim($item)); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-danger mb-3"><i class="bi bi-x-circle me-2"></i>What's Not Included</h6>
                                <?php
                                $exc = $pkg['exclusions'] ? explode(',', $pkg['exclusions']) : ['Lunch & Dinner','Personal expenses','Tips & gratuities'];
                                foreach ($exc as $item): ?>
                                <div class="inclusion-item excluded">
                                    <i class="bi bi-x-circle-fill text-danger"></i>
                                    <span class="small"><?php echo htmlspecialchars(trim($item)); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="mt-4 p-3 rounded-3" style="background:#fefce8;border:1px solid #fde68a;">
                            <i class="bi bi-info-circle text-warning me-2"></i>
                            <span class="small text-warning fw-semibold">Note:</span>
                            <span class="small text-muted ms-1">Prices may vary during peak season. Please confirm before booking.</span>
                        </div>
                    </div>

                    <!-- Gallery -->
                    <div class="tab-pane fade" id="gallery-tab">
                        <h5 class="fw-bold mb-4">Photo Gallery</h5>
                        <div class="gallery-grid">
                            <?php foreach ($galleryImgs as $gImg): ?>
                            <div class="gallery-item">
                                <img src="<?php echo $gImg; ?>" alt="Gallery" loading="lazy">
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div class="tab-pane fade" id="reviews-tab">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0">Traveler Reviews</h5>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:1.5rem;color:#f59e0b;"><?php echo str_repeat('★', round($avgRating)); ?></span>
                                <div>
                                    <div class="fw-bold"><?php echo $avgRating; ?>/5</div>
                                    <div class="text-muted small"><?php echo $rCount ?: '124'; ?> reviews</div>
                                </div>
                            </div>
                        </div>

                        <?php if (isLoggedIn()): ?>
                        <!-- Submit Review -->
                        <div class="review-card mb-4" style="background:#f8fafc;">
                            <h6 class="fw-bold mb-3">Write a Review</h6>
                            <form id="reviewForm" onsubmit="submitReview(event, <?php echo $id; ?>)">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Your Rating</label>
                                    <div class="star-input mb-1">
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                    <input type="hidden" name="rating" id="ratingVal" value="5">
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="title" class="form-control" placeholder="Review title" required maxlength="200">
                                </div>
                                <div class="mb-3">
                                    <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..." required maxlength="1000"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Submit Review
                                </button>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-info rounded-3 mb-4">
                            <i class="bi bi-info-circle me-2"></i>
                            <a href="<?php echo SITE_URL; ?>/auth/login.php">Login</a> to write a review.
                        </div>
                        <?php endif; ?>

                        <!-- Reviews list -->
                        <div id="reviewsList">
                        <?php if ($reviews && $reviews->num_rows > 0):
                            while ($r = $reviews->fetch_assoc()): ?>
                            <div class="review-card mb-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="review-avatar"><?php echo strtoupper(substr($r['full_name'],0,1)); ?></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-bold"><?php echo htmlspecialchars($r['full_name']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($r['city'] ?? ''); ?> · <?php echo date('M Y', strtotime($r['created_at'])); ?></div>
                                            </div>
                                            <div style="color:#f59e0b;"><?php echo str_repeat('★', $r['rating']); ?></div>
                                        </div>
                                        <?php if ($r['title']): ?>
                                        <div class="fw-semibold mt-2"><?php echo htmlspecialchars($r['title']); ?></div>
                                        <?php endif; ?>
                                        <p class="text-muted small mt-1 mb-0" style="line-height:1.7;"><?php echo htmlspecialchars($r['comment']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile;
                        else: ?>
                        <!-- Static example review if no DB reviews -->
                        <div class="review-card mb-3">
                            <div class="d-flex align-items-start gap-3">
                                <div class="review-avatar">S</div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <div><div class="fw-bold">Sarah M.</div><div class="text-muted small">New York · Jan 2025</div></div>
                                        <span style="color:#f59e0b;">★★★★★</span>
                                    </div>
                                    <div class="fw-semibold mt-2">Absolutely Magical!</div>
                                    <p class="text-muted small mt-1 mb-0">One of the best trips of my life. Everything was perfectly organized and the destinations were breathtaking.</p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="booking-summary sticky-top" style="top:100px;">
                    <div class="mb-3">
                        <img src="<?php echo $heroImg; ?>" class="w-100 rounded-3 mb-3" alt="<?php echo htmlspecialchars($pkg['title']); ?>" style="height:180px;object-fit:cover;">
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="text-muted small">Price per person</div>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="fs-3 fw-bold text-primary"><?php echo CURRENCY . number_format($displayPrice, 0); ?></span>
                                <?php if ($hasDiscount): ?>
                                <span class="text-muted text-decoration-line-through small"><?php echo CURRENCY . number_format($pkg['price'], 0); ?></span>
                                <span class="badge bg-danger">-<?php echo $discountPct; ?>%</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <div style="color:#f59e0b;">★★★★★</div>
                            <div class="small text-muted"><?php echo $avgRating; ?> rating</div>
                        </div>
                    </div>
                    <hr>
                    <?php foreach ([['bi-calendar','Duration', $pkg['duration_days'].'D / '.$pkg['duration_nights'].'N'], ['bi-geo-alt','Destination', $pkg['dest_name'].', '.$pkg['country']], ['bi-people','Group Size','Up to '.$pkg['max_persons'].' persons']] as [$ico,$lbl,$val]): ?>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted"><i class="bi <?php echo $ico; ?> me-1"></i><?php echo $lbl; ?></span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($val); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <?php if (isLoggedIn()): ?>
                    <a href="booking.php?id=<?php echo $pkg['id']; ?>" class="btn btn-hero w-100 mb-2">
                        <i class="bi bi-calendar-check me-2"></i>Book This Package
                    </a>
                    <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/auth/login.php" class="btn btn-hero w-100 mb-2">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login to Book
                    </a>
                    <?php endif; ?>
                    <button onclick="toggleWishlist(<?php echo $pkg['id']; ?>, this)" class="btn btn-outline-danger w-100">
                        <i class="bi bi-heart me-2"></i>Save to Wishlist
                    </button>
                    <div class="mt-3 p-3 rounded-3" style="background:#f0fdf4;">
                        <div class="small text-success fw-semibold mb-1"><i class="bi bi-shield-check me-1"></i>Safe & Secure Booking</div>
                        <div class="small text-muted">Free cancellation up to 48 hours before travel date.</div>
                    </div>
                </div>

                <!-- Related Packages -->
                <?php if ($related->num_rows > 0): ?>
                <div class="mt-4">
                    <h6 class="fw-bold mb-3">More Packages You'll Love</h6>
                    <?php
                    $relImgs = array_values($pkgImages);
                    $relI = 0;
                    while ($rel = $related->fetch_assoc()):
                        $rImg = $pkgImages[$rel['id']] ?? ($relImgs[$relI % count($relImgs)]);
                        $relI++;
                    ?>
                    <div class="d-flex gap-3 mb-3 p-3 bg-white rounded-3 shadow-sm border" style="border-color:rgba(0,0,0,0.05)!important;">
                        <img src="<?php echo $rImg; ?>" alt="<?php echo htmlspecialchars($rel['title']); ?>"
                             class="rounded-2" style="width:70px;height:70px;object-fit:cover;flex-shrink:0;">
                        <div>
                            <div class="fw-semibold small"><?php echo htmlspecialchars($rel['title']); ?></div>
                            <div class="text-muted" style="font-size:0.75rem;"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($rel['dest_name']); ?></div>
                            <div class="text-primary fw-bold small mt-1"><?php echo CURRENCY . number_format($rel['discount_price'] ?: $rel['price'], 0); ?></div>
                            <a href="package-details.php?id=<?php echo $rel['id']; ?>" class="stretched-link"></a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function submitReview(e, pkgId) {
    e.preventDefault();
    const form = e.target;
    const fd = new FormData(form);
    fd.append('package_id', pkgId);
    const token = window._csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    if (!fd.has('csrf_token') || !fd.get('csrf_token')) {
        fd.set('csrf_token', token);
    }
    fetch(SITE_URL + '/ajax/submit_review.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': token },
        body: fd
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            showToast('Review submitted! It will appear after approval. 🎉', 'success');
            form.reset();
            document.querySelectorAll('.star-input i').forEach(s => s.classList.remove('active'));
            document.getElementById('ratingVal').value = 5;
        } else {
            showToast(d.message || 'Could not submit review.', 'error');
        }
    })
    .catch(() => showToast('Server error. Try again.', 'error'));
}
// Star rating for review
document.querySelectorAll('.star-input i').forEach((star, i, stars) => {
    star.addEventListener('click', () => {
        stars.forEach((s,j) => s.classList.toggle('active', j <= i));
        document.getElementById('ratingVal').value = i + 1;
    });
});
</script>
<?php include 'includes/footer.php'; ?>
