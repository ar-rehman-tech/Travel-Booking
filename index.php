<?php
$pageTitle = 'Home';
include 'includes/header.php';

// Featured packages
$packages = $conn->query("SELECT p.*, d.name as dest_name, d.country FROM packages p JOIN destinations d ON p.destination_id = d.id WHERE p.status = 'active' ORDER BY p.is_featured DESC LIMIT 9");

// Destinations
$destinations = $conn->query("SELECT * FROM destinations LIMIT 6");

// Approved testimonials / reviews
$testimonials = $conn->query("SELECT r.*, u.full_name, u.city, u.country, p.title as pkg_title FROM reviews r JOIN users u ON r.user_id = u.id JOIN packages p ON r.package_id = p.id WHERE r.status = 'approved' ORDER BY r.created_at DESC LIMIT 6");
?>

<!-- ===== HERO SECTION ===== -->
<section class="hero-section" id="hero">
    <div class="floating-shape shape-1"></div>
    <div class="floating-shape shape-2"></div>
    <div class="floating-shape shape-3"></div>
    
    <div class="hero-particles"></div>

    <div class="container">
        <div class="row align-items-center hero-content-row">
            
            <div class="col-lg-6 col-xl-5 hero-text-col">
                <div class="gsap-hero-title">
                    <div class="hero-badge-pill mb-4">
                        <span class="hero-badge-dot"></span>
                        <span><i class="bi bi-stars me-1"></i>Trusted by 500,000+ Travelers</span>
                    </div>
                    <h1 class="hero-title">
                        Explore the World<br>
                        <span class="hero-title-gradient">Your Way</span>
                    </h1>
                </div>
                <p class="hero-subtitle gsap-hero-sub mt-3">
                    Discover handpicked travel packages for every dream — luxury escapes, thrilling adventures, romantic getaways &amp; family journeys.
                </p>

                <!-- CTA Buttons -->
                <div class="d-flex gap-3 flex-wrap mt-4 gsap-hero-btn">
                    <a href="packages.php" class="btn btn-hero btn-lg">
                        <i class="bi bi-compass me-2"></i>Explore Packages
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="gsap-hero-btn mt-5">
                    <div class="hero-trust-row" style="transform: translate(0px, -20px);">
                        <div class="hero-stat">
                            <div class="hero-stat-num">500+</div>
                            <div class="hero-stat-lbl">Happy Travelers</div>
                        </div>
                        <div class="hero-stat-sep"></div>
                        <div class="hero-stat">
                            <div class="hero-stat-num">120+</div>
                            <div class="hero-stat-lbl">Destinations</div>
                        </div>
                        <div class="hero-stat-sep"></div>
                        <div class="hero-stat">
                            <div class="hero-stat-num">4.9★</div>
                            <div class="hero-stat-lbl">Avg Rating</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Image Grid -->
            <div class="col-lg-6 col-xl-7 d-none d-lg-block gsap-hero-img">
                <div class="hero-image-grid">
                    <div class="hero-img-main">
                        <img src="https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=700&h=500&fit=crop&q=85"
                             alt="Mountain Adventure" loading="eager">
                        <!-- Floating cards -->
                        <div class="hero-float-card hero-float-card--bl">
                            <div class="hfc-icon"><i class="bi bi-shield-check"></i></div>
                            <div>
                                <div class="hfc-title">Best Price</div>
                                <div class="hfc-sub">Guaranteed</div>
                            </div>
                        </div>
                        <div class="hero-float-card hero-float-card--tr">
                            <div class="hfc-stars">★★★★★</div>
                            <div class="hfc-title">4.9 / 5.0</div>
                            <div class="hfc-sub">2,400+ Reviews</div>
                        </div>
                    </div>
                    <div class="hero-img-secondary">
                        <img src="https://images.unsplash.com/photo-1573843981267-be1999ff37cd?w=320&h=220&fit=crop&q=80"
                             alt="Beach Resort" loading="eager">
                        <div class="hero-img-label">
                            <i class="bi bi-geo-alt-fill me-1"></i>Maldives Luxury
                        </div>
                    </div>
                    <div class="hero-img-tertiary">
                        <img src="https://images.unsplash.com/photo-1531219572328-a0171b4448a3?w=320&h=180&fit=crop&q=80"
                             alt="City Travel" loading="eager">
                        <div class="hero-img-label">
                            <i class="bi bi-geo-alt-fill me-1"></i>Swiss Alps
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Search Box ───────────────────────────────────────────────── -->
        <div class="search-box gsap-fade-up">
            <form action="<?php echo SITE_URL; ?>/packages.php" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="search-label"><i class="bi bi-geo-alt me-1"></i>Where To?</label>
                        <input type="text" name="search" class="form-control search-input"
                               placeholder="Destination or package...">
                    </div>
                    <div class="col-md-3">
                        <label class="search-label"><i class="bi bi-tag me-1"></i>Category</label>
                        <select name="category" class="form-select search-input">
                            <option value="">All Categories</option>
                            <option value="adventure">🏔️ Adventure</option>
                            <option value="luxury">💎 Luxury</option>
                            <option value="budget">💰 Budget</option>
                            <option value="family">👨‍👩‍👧 Family</option>
                            <option value="honeymoon">💑 Honeymoon</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="search-label"><i class="bi bi-calendar me-1"></i>Travel Date</label>
                        <input type="date" name="date" class="form-control search-input"
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-hero w-100 py-3">
                            <i class="bi bi-search me-2"></i>Search Trips
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


<!-- ===== POPULAR PACKAGES (Static Showcase) ===== -->
<section class="section-padding" style="background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 100%);">
    <div class="container">
        <div class="text-center gsap-fade-up mb-5">
            <span class="section-badge" style="background:rgba(14,165,233,0.12);color:var(--primary);">🌟 Most Booked</span>
            <h2 class="section-title mt-2">Popular Packages</h2>
            <p class="section-subtitle">Trending picks loved by thousands of travelers</p>
        </div>
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-lg-4 col-md-6 gsap-fade-up">
                <div class="popular-pkg-card">
                    <div class="popular-pkg-img" style="background-image:url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600&h=360&fit=crop&q=80');">
                        <span class="pkg-cat-pill"><i class="bi bi-mountain me-1"></i>Adventure</span>
                        <span class="pkg-price-pill">From $899</span>
                    </div>
                    <div class="popular-pkg-body">
                        <h5 class="fw-bold mb-1">Swiss Alps Explorer</h5>
                        <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1 text-primary"></i>Switzerland, Europe &bull; 7D/6N</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1">
                                <span style="color:#f59e0b;">★★★★★</span>
                                <span class="small fw-semibold ms-1">4.9</span>
                                <span class="text-muted small">(318)</span>
                            </div>
                            <a href="package-details.php?id=1" class="btn btn-sm btn-primary rounded-pill px-3">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="col-lg-4 col-md-6 gsap-fade-up">
                <div class="popular-pkg-card">
                    <div class="popular-pkg-img" style="background-image:url('https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=600&h=360&fit=crop&q=80');">
                        <span class="pkg-cat-pill" style="background:linear-gradient(135deg,#ec4899,#a855f7);"><i class="bi bi-heart me-1"></i>Honeymoon</span>
                        <span class="pkg-price-pill">From $1,299</span>
                    </div>
                    <div class="popular-pkg-body">
                        <h5 class="fw-bold mb-1">Maldives Luxury Escape</h5>
                        <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1 text-primary"></i>Maldives, Asia &bull; 6D/5N</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1">
                                <span style="color:#f59e0b;">★★★★★</span>
                                <span class="small fw-semibold ms-1">5.0</span>
                                <span class="text-muted small">(214)</span>
                            </div>
                            <a href="package-details.php?id=2" class="btn btn-sm btn-primary rounded-pill px-3">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="col-lg-4 col-md-6 gsap-fade-up">
                <div class="popular-pkg-card">
                    <div class="popular-pkg-img" style="background-image:url('https://images.unsplash.com/photo-1533105079780-92b9be482077?w=600&h=360&fit=crop&q=80');">
                        <span class="pkg-cat-pill" style="background:linear-gradient(135deg,#10b981,#0ea5e9);"><i class="bi bi-people me-1"></i>Family</span>
                        <span class="pkg-price-pill">From $649</span>
                    </div>
                    <div class="popular-pkg-body">
                        <h5 class="fw-bold mb-1">Bali Family Adventure</h5>
                        <p class="text-muted small mb-3"><i class="bi bi-geo-alt me-1 text-primary"></i>Bali, Indonesia &bull; 8D/7N</p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-1">
                                <span style="color:#f59e0b;">★★★★☆</span>
                                <span class="small fw-semibold ms-1">4.7</span>
                                <span class="text-muted small">(187)</span>
                            </div>
                            <a href="package-details.php?id=3" class="btn btn-sm btn-primary rounded-pill px-3">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5 gsap-fade-up">
            <a href="packages.php" class="btn btn-primary btn-lg px-5 rounded-pill fw-bold">
                See All Packages <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ===== CTA BANNER ===== -->
<section class="cta-banner">
    <div class="container text-center text-white">
        <div class="gsap-fade-up">
            <span class="section-badge" style="background:rgba(255,255,255,0.15);color:#fff;">Limited Time</span>
            <h2 class="section-title text-white mt-2">Ready for Your Dream Vacation?</h2>
            <p class="mb-4" style="color:rgba(255,255,255,0.75);font-size:1.1rem;max-width:500px;margin:0 auto;">
                Use code <strong class="text-warning">WELCOME10</strong> for 10% off your first booking.
            </p>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="packages.php" class="btn btn-hero btn-lg">
                    <i class="bi bi-compass me-2"></i>Browse Packages
                </a>
                <a href="contact.php" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                    <i class="bi bi-headset me-2"></i>Talk to Expert
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ===== DESTINATIONS ===== -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center gsap-fade-up">
            <span class="section-badge">🌍 Trending</span>
            <h2 class="section-title">Top Destinations</h2>
            <p class="section-subtitle">Explore the most sought-after travel destinations</p>
        </div>
        <div class="row g-4 cards-row">
            <?php
            $destImages = [
                1 => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=500&h=350&fit=crop&q=80',
                2 => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=500&h=350&fit=crop&q=80',
                3 => 'https://images.unsplash.com/photo-1514282401047-d79a71a590e8?w=500&h=350&fit=crop&q=80',
                4 => 'https://images.unsplash.com/photo-1531366936337-7c912a4589a7?w=500&h=350&fit=crop&q=80',
                5 => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=500&h=350&fit=crop&q=80',
                6 => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=500&h=350&fit=crop&q=80',
            ];
            while ($dest = $destinations->fetch_assoc()):
                $dImg = $destImages[$dest['id']] ?? 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=500&h=350&fit=crop&q=80';
            ?>
            <div class="col-lg-4 col-md-6">
                <a href="destinations.php?id=<?php echo $dest['id']; ?>" class="text-decoration-none">
                    <div class="dest-card-large">
                        <img src="<?php echo $dImg; ?>" alt="<?php echo htmlspecialchars($dest['name']); ?>" loading="lazy">
                        <div class="dest-overlay">
                            <div>
                                <h5 class="text-white fw-bold mb-1"><?php echo htmlspecialchars($dest['name']); ?></h5>
                                <p class="text-white mb-2 small opacity-75"><i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($dest['country']); ?></p>
                                <span class="dest-package-count"><i class="bi bi-suitcase me-1"></i>View Packages</span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-5 pt-3 gsap-fade-up">
            <a href="destinations.php" class="btn btn-outline-primary btn-lg px-5 rounded-pill fw-bold">
                All Destinations <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- ===== STATS ===== -->
<section class="stats-section section-padding">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3 col-6 gsap-scale">
                <div class="stat-item">
                    <div class="stat-number" data-count="500" data-suffix="+">0</div>
                    <div class="stat-label">Happy Travelers</div>
                </div>
            </div>
            <div class="col-md-3 col-6 gsap-scale">
                <div class="stat-item">
                    <div class="stat-number" data-count="120" data-suffix="+">0</div>
                    <div class="stat-label">Destinations</div>
                </div>
            </div>
            <div class="col-md-3 col-6 gsap-scale">
                <div class="stat-item">
                    <div class="stat-number" data-count="50" data-suffix="+">0</div>
                    <div class="stat-label">Tour Packages</div>
                </div>
            </div>
            <div class="col-md-3 col-6 gsap-scale">
                <div class="stat-item">
                    <div class="stat-number" data-count="98" data-suffix="%">0</div>
                    <div class="stat-label">Satisfaction Rate</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== WHY CHOOSE US ===== -->
<section class="section-padding">
    <div class="container">
        <div class="text-center gsap-fade-up">
            <span class="section-badge">⭐ Why Us</span>
            <h2 class="section-title">Why Choose TravelBook?</h2>
            <p class="section-subtitle">We make travel planning effortless and enjoyable</p>
        </div>
        <div class="row g-4">
            <?php
            $features = [
                ['bi-shield-check',  'var(--primary)',   'Best Price Guarantee', 'We offer competitive prices with no hidden fees or surprises.'],
                ['bi-headset',       'var(--secondary)', '24/7 Support',         'Round-the-clock customer support wherever you are in the world.'],
                ['bi-calendar-check','var(--success)',   'Easy Booking',         'Simple and fast booking process in just a few clicks.'],
                ['bi-award',         '#8b5cf6',          'Curated Experiences',  'Handpicked destinations and carefully crafted itineraries.'],
            ];
            foreach ($features as [$icon, $color, $title, $desc]):
            ?>
            <div class="col-lg-3 col-md-6 gsap-fade-up">
                <div class="text-center p-4 h-100 bg-white rounded-4 shadow-sm border" style="border-color:rgba(0,0,0,0.05)!important;transition:transform 0.3s ease;" onmouseenter="this.style.transform='translateY(-6px)'" onmouseleave="this.style.transform=''">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-3"
                         style="width:72px;height:72px;background:<?php echo $color; ?>20;font-size:2rem;color:<?php echo $color; ?>;">
                        <i class="bi <?php echo $icon; ?>"></i>
                    </div>
                    <h5 class="fw-bold mb-2"><?php echo $title; ?></h5>
                    <p class="text-muted small mb-0"><?php echo $desc; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<?php include 'includes/footer.php'; ?>
