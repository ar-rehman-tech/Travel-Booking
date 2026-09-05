<?php
$pageTitle = 'Destinations';
include 'includes/header.php';

$destImages = [
    1=>'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&h=400&fit=crop&q=80',
    2=>'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&h=400&fit=crop&q=80',
    3=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=600&h=400&fit=crop&q=80',
    4=>'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=600&h=400&fit=crop&q=80',
    5=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=400&fit=crop&q=80',
    6=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=600&h=400&fit=crop&q=80',
];

// Single destination view
if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
    $did  = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->bind_param('i', $did);
    $stmt->execute();
    $dest = $stmt->get_result()->fetch_assoc();
    if (!$dest) { header('Location: destinations.php'); exit; }

    $pkgStmt = $conn->prepare("SELECT p.* FROM packages p WHERE p.destination_id = ? AND p.status = 'active'");
    $pkgStmt->bind_param('i', $did);
    $pkgStmt->execute();
    $destPkgs = $pkgStmt->get_result();
    $heroImg  = $destImages[$did] ?? 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1200&h=600&fit=crop';
?>
<div class="pkg-hero">
    <img src="<?php echo $heroImg; ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>">
    <div class="pkg-hero-overlay text-white">
        <div>
            <h1 class="fw-bold mb-1"><?php echo htmlspecialchars($dest['name']); ?></h1>
            <p class="mb-2 opacity-75"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($dest['country']); ?></p>
            <?php if ($dest['climate']): ?>
            <span class="badge rounded-pill" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-thermometer me-1"></i><?php echo htmlspecialchars($dest['climate']); ?> Climate
            </span>
            <?php endif; ?>
            <?php if ($dest['best_season']): ?>
            <span class="badge rounded-pill ms-2" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-calendar me-1"></i>Best: <?php echo htmlspecialchars($dest['best_season']); ?>
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="gsap-fade-up">
                    <h2 class="fw-bold mb-3">About <?php echo htmlspecialchars($dest['name']); ?></h2>
                    <p class="text-muted" style="line-height:1.8;"><?php echo nl2br(htmlspecialchars($dest['description'] ?? 'An amazing destination waiting to be explored.')); ?></p>
                </div>
                <h4 class="fw-bold mt-5 mb-4 gsap-fade-up">Available Packages</h4>
                <div class="row g-4 cards-row">
                    <?php
                    $pkgImgs = [
                        1=>'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&h=400&fit=crop&q=80',
                        2=>'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&h=400&fit=crop&q=80',
                        3=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=600&h=400&fit=crop&q=80',
                        4=>'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=600&h=400&fit=crop&q=80',
                        5=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=400&fit=crop&q=80',
                        6=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=600&h=400&fit=crop&q=80',
                    ];
                    while ($p = $destPkgs->fetch_assoc()):
                        $pImg = $pkgImgs[$p['id']] ?? $heroImg;
                        $pPrice = $p['discount_price'] ?: $p['price'];
                    ?>
                    <div class="col-md-6">
                        <div class="package-card h-100 d-flex flex-column">
                            <div class="card-img-wrapper">
                                <img src="<?php echo $pImg; ?>" alt="<?php echo htmlspecialchars($p['title']); ?>">
                                <span class="card-badge"><?php echo ucfirst($p['category']); ?></span>
                            </div>
                            <div class="card-body flex-grow-1">
                                <h5 class="card-title"><?php echo htmlspecialchars($p['title']); ?></h5>
                                <div class="card-meta">
                                    <span><i class="bi bi-clock"></i> <?php echo $p['duration_days']; ?>D/<?php echo $p['duration_nights']; ?>N</span>
                                    <span><i class="bi bi-people"></i> Up to <?php echo $p['max_persons']; ?></span>
                                </div>
                                <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($p['description'], 0, 90)); ?>...</p>
                                <div class="card-price">
                                    <span class="price-current"><?php echo CURRENCY . number_format($pPrice, 0); ?></span>
                                    <span class="text-muted small">/ person</span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <span style="color:#f59e0b;">★★★★★</span>
                                <a href="package-details.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="booking-summary gsap-fade-left">
                    <h6 class="fw-bold mb-3">Destination Info</h6>
                    <?php foreach ([['bi-geo-alt','Country', $dest['country']], ['bi-thermometer','Climate', $dest['climate'] ?? 'Varies'], ['bi-calendar3','Best Season', $dest['best_season'] ?? 'Year Round'], ['bi-geo','Coordinates', round($dest['latitude'] ?? 0, 2) . ', ' . round($dest['longitude'] ?? 0, 2)]] as [$ico,$lbl,$val]): ?>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted"><i class="bi <?php echo $ico; ?> me-1"></i><?php echo $lbl; ?></span>
                        <span class="fw-semibold"><?php echo htmlspecialchars($val); ?></span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <a href="packages.php?search=<?php echo urlencode($dest['name']); ?>" class="btn btn-hero w-100">
                        <i class="bi bi-suitcase me-2"></i>View All Packages
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; exit; } // end single destination ?>

<!-- ===== DESTINATIONS LISTING ===== -->
<div class="page-header position-relative overflow-hidden" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0ea5e9 100%);padding:140px 0 100px;">
    <div style="position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1488646953014-85cb44e25828?w=1920&q=60') center/cover no-repeat;opacity:0.15;"></div>
    <div class="container text-center position-relative" style="z-index:2;">
        <span class="section-badge mb-3 d-inline-block" style="background:rgba(14,165,233,0.2);color:#38bdf8;border:1px solid rgba(14,165,233,0.3);">
            <i class="bi bi-globe-americas me-1"></i> Worldwide
        </span>
        <h1 class="fw-bold text-white mb-2" style="font-size:3rem;">Explore Destinations</h1>
        <p class="mt-2" style="opacity:0.75;color:#fff;font-size:1.05rem;">Discover amazing places around the world</p>
        <nav aria-label="breadcrumb" class="mt-3 d-flex justify-content-center">
            <ol class="breadcrumb mb-0" style="background:rgba(255,255,255,0.08);padding:8px 20px;border-radius:50px;">
                <li class="breadcrumb-item"><a href="/" class="text-white opacity-75 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white">Destinations</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding">
    <div class="container">
        <?php
        $allDests = $conn->query("SELECT * FROM destinations ORDER BY name ASC");
        ?>
        <div class="row g-4 cards-row">
            <?php while ($d = $allDests->fetch_assoc()):
                $dImg = $destImages[$d['id']] ?? 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&h=400&fit=crop&q=80';
            ?>
            <div class="col-lg-4 col-md-6">
                <a href="destinations.php?id=<?php echo $d['id']; ?>" class="text-decoration-none h-100 d-block">
                    <div class="dest-card-large h-100">
                        <img src="<?php echo $dImg; ?>" alt="<?php echo htmlspecialchars($d['name']); ?>" loading="lazy">
                        <div class="dest-overlay">
                            <div>
                                <h5 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($d['name']); ?></h5>
                                <p class="text-white mb-2 small" style="opacity:0.8;"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($d['country']); ?></p>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php if ($d['climate']): ?><span class="dest-package-count"><?php echo htmlspecialchars($d['climate']); ?></span><?php endif; ?>
                                    <?php if ($d['best_season']): ?><span class="dest-package-count">Best: <?php echo htmlspecialchars($d['best_season']); ?></span><?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
