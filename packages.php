<?php
$pageTitle = 'Travel Packages';
include 'includes/header.php';

$where  = "WHERE p.status = 'active'";
$params = [];
$types  = '';

if (!empty($_GET['search'])) {
    $s = '%' . trim($_GET['search']) . '%';
    $where .= " AND (p.title LIKE ? OR d.name LIKE ? OR d.country LIKE ?)";
    $params = [$s, $s, $s];
    $types .= 'sss';
}
if (!empty($_GET['category'])) {
    $where .= " AND p.category = ?";
    $params[] = trim($_GET['category']);
    $types .= 's';
}
if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
    $where .= " AND COALESCE(p.discount_price, p.price) >= ?";
    $params[] = (float)$_GET['min_price'];
    $types .= 'd';
}
if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
    $where .= " AND COALESCE(p.discount_price, p.price) <= ?";
    $params[] = (float)$_GET['max_price'];
    $types .= 'd';
}

$sort    = $_GET['sort'] ?? 'newest';
$orderBy = match($sort) {
    'price_low'  => 'COALESCE(p.discount_price,p.price) ASC',
    'price_high' => 'COALESCE(p.discount_price,p.price) DESC',
    'name'       => 'p.title ASC',
    'rating'     => 'p.is_featured DESC',
    default      => 'p.created_at DESC'
};

// Pagination
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 24; // showing more packages
$offset   = ($page - 1) * $perPage;

$countSql = "SELECT COUNT(*) as c FROM packages p JOIN destinations d ON p.destination_id = d.id $where";
$cStmt = $conn->prepare($countSql);
if ($types) $cStmt->bind_param($types, ...$params);
$cStmt->execute();
$total      = $cStmt->get_result()->fetch_assoc()['c'];
$totalPages = ceil($total / $perPage);

$sql  = "SELECT p.*, d.name as dest_name, d.country FROM packages p JOIN destinations d ON p.destination_id = d.id $where ORDER BY $orderBy LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$allParams = array_merge($params, [$perPage, $offset]);
$allTypes  = $types . 'ii';
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$packages = $stmt->get_result();

$pkgImages = [
    1=>'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&h=400&fit=crop&q=80',
    2=>'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&h=400&fit=crop&q=80',
    3=>'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=600&h=400&fit=crop&q=80',
    4=>'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=600&h=400&fit=crop&q=80',
    5=>'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&h=400&fit=crop&q=80',
    6=>'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=600&h=400&fit=crop&q=80',
];
?>

<div class="page-header position-relative overflow-hidden" style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 50%,#0ea5e9 100%);padding:140px 0 100px;">
    <div style="position:absolute;inset:0;background:url('https://images.unsplash.com/photo-1500835556837-99ac94a94552?w=1920&q=60') center/cover no-repeat;opacity:0.15;"></div>
    <div class="container text-center position-relative" style="z-index:2;">
        <span class="section-badge mb-3 d-inline-block" style="background:rgba(249,115,22,0.2);color:#fb923c;border:1px solid rgba(249,115,22,0.3);">
            <i class="bi bi-suitcase me-1"></i> All Packages
        </span>
        <h1 class="fw-bold text-white mb-2" style="font-size:3rem;">Travel Packages</h1>
        <p class="mt-2" style="opacity:0.75;color:#fff;font-size:1.05rem;">Find your perfect getaway — <strong><?php echo $total; ?></strong> packages available</p>
        <nav aria-label="breadcrumb" class="mt-3 d-flex justify-content-center">
            <ol class="breadcrumb mb-0" style="background:rgba(255,255,255,0.08);padding:8px 20px;border-radius:50px;">
                <li class="breadcrumb-item"><a href="/" class="text-white opacity-75 text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active text-white">Packages</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section-padding">
    <div class="container">

        <!-- Filter Bar -->
        <div class="filter-bar gsap-fade-up">
            <form method="GET" class="row g-3 align-items-end" id="filterForm">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Destination or package..."
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Category</label>
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        <?php foreach (['adventure'=>'🏔️ Adventure','luxury'=>'💎 Luxury','budget'=>'💰 Budget','family'=>'👨‍👩‍👧 Family','honeymoon'=>'💑 Honeymoon','solo'=>'🚶 Solo'] as $val=>$label): ?>
                        <option value="<?php echo $val; ?>" <?php echo ($_GET['category']??'')===$val?'selected':''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Min Price ($)</label>
                    <input type="number" name="min_price" class="form-control" placeholder="0"
                           value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Max Price ($)</label>
                    <input type="number" name="max_price" class="form-control" placeholder="5000"
                           value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>" min="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Sort By</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest"     <?php echo $sort==='newest'?'selected':''; ?>>Newest</option>
                        <option value="price_low"  <?php echo $sort==='price_low'?'selected':''; ?>>Price: Low → High</option>
                        <option value="price_high" <?php echo $sort==='price_high'?'selected':''; ?>>Price: High → Low</option>
                        <option value="name"       <?php echo $sort==='name'?'selected':''; ?>>Name A–Z</option>
                        <option value="rating"     <?php echo $sort==='rating'?'selected':''; ?>>Featured First</option>
                    </select>
                </div>
                <div class="col-md-12 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-search me-1"></i>Apply Filters
                    </button>
                    <a href="packages.php" class="btn btn-outline-secondary px-3">
                        <i class="bi bi-x-circle me-1"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div class="row g-4 cards-row">
            <?php if ($packages->num_rows === 0): ?>
                <div class="col-12 text-center py-5 gsap-fade-up">
                    <i class="bi bi-search" style="font-size:4rem;color:var(--gray);"></i>
                    <h4 class="mt-3 fw-bold">No packages found</h4>
                    <p class="text-muted">Try adjusting your filters or <a href="packages.php">clear all</a></p>
                </div>
            <?php else: ?>
                <?php while ($pkg = $packages->fetch_assoc()):
                    $imgUrl      = $pkgImages[$pkg['id']] ?? 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&h=400&fit=crop&q=80';
                    $displayPrice= $pkg['discount_price'] ?: $pkg['price'];
                    $hasDiscount = !empty($pkg['discount_price']) && $pkg['discount_price'] < $pkg['price'];
                    $discountPct = $hasDiscount ? round((1 - $pkg['discount_price']/$pkg['price'])*100) : 0;
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="package-card h-100 d-flex flex-column">
                        <div class="card-img-wrapper">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo htmlspecialchars($pkg['title']); ?>" loading="lazy">
                            <span class="card-badge"><?php echo ucfirst($pkg['category']); ?></span>
                            <?php if ($hasDiscount): ?>
                            <div class="discount-ribbon">-<?php echo $discountPct; ?>%</div>
                            <?php endif; ?>
                            <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $pkg['id']; ?>, this)" title="Wishlist">
                                <i class="bi bi-heart"></i>
                            </button>
                        </div>
                        <div class="card-body flex-grow-1">
                            <h5 class="card-title"><?php echo htmlspecialchars($pkg['title']); ?></h5>
                            <div class="card-meta">
                                <span><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($pkg['dest_name']); ?></span>
                                <span><i class="bi bi-clock"></i> <?php echo $pkg['duration_days']; ?>D/<?php echo $pkg['duration_nights']; ?>N</span>
                            </div>
                            <p class="text-muted small mb-3"><?php echo htmlspecialchars(substr($pkg['description'], 0, 90)); ?>...</p>
                            <div class="card-price">
                                <span class="price-current"><?php echo CURRENCY . number_format($displayPrice, 0); ?></span>
                                <?php if ($hasDiscount): ?>
                                <span class="price-original"><?php echo CURRENCY . number_format($pkg['price'], 0); ?></span>
                                <?php endif; ?>
                                <span class="text-muted small">/ person</span>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center gap-1">
                                <i class="bi bi-star-fill text-warning"></i>
                                <span class="small fw-semibold">4.8</span>
                                <span class="text-muted small">(<?php echo (($pkg['id'] * 37 + 113) % 121) + 80; ?>)</span>
                            </div>
                            <a href="package-details.php?id=<?php echo $pkg['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5 gsap-fade-up" aria-label="Packages pagination">
            <ul class="pagination justify-content-center gap-2">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link rounded-3" href="?<?php echo http_build_query(array_merge($_GET, ['page'=>$page-1])); ?>">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
                <?php endif; ?>
                <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
                <li class="page-item <?php echo $i===$page?'active':''; ?>">
                    <a class="page-link rounded-3" href="?<?php echo http_build_query(array_merge($_GET, ['page'=>$i])); ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link rounded-3" href="?<?php echo http_build_query(array_merge($_GET, ['page'=>$page+1])); ?>">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
