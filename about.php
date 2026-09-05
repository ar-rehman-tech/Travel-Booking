<?php
$pageTitle = 'About Us';
include 'includes/header.php';
?>

<div class="page-header">
    <h1>About TravelBook</h1>
    <p class="mt-2" style="opacity:0.7;">Your trusted travel partner since 2019</p>
</div>

<!-- Story -->
<section class="section-padding">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6 gsap-fade-right">
                <span class="section-badge">Our Story</span>
                <h2 class="section-title mt-2">We Are Passionate About Travel</h2>
                <p class="text-muted" style="line-height:1.8;">TravelBook was born from a simple belief: everyone deserves to experience the world's wonders. Founded in 2019, we've helped over 500 travelers explore 120+ destinations across 60 countries.</p>
                <p class="text-muted" style="line-height:1.8;">Our team of travel experts handcraft every itinerary with meticulous attention to detail — ensuring you get the perfect balance of adventure, culture, relaxation, and value.</p>
                <div class="d-flex gap-4 mt-4">
                    <div><div class="fw-bold fs-3 text-primary">500+</div><div class="text-muted small">Happy Travelers</div></div>
                    <div><div class="fw-bold fs-3 text-primary">120+</div><div class="text-muted small">Destinations</div></div>
                    <div><div class="fw-bold fs-3 text-primary">5★</div><div class="text-muted small">Avg Rating</div></div>
                </div>
            </div>
            <div class="col-lg-6 gsap-fade-left">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1488085061387-422e29b40080?w=600&h=450&fit=crop&q=80"
                         class="img-fluid rounded-4 w-100" alt="About us" style="object-fit:cover;max-height:400px;">
                    <div class="position-absolute bg-white rounded-3 p-3 shadow-lg"
                         style="bottom:20px;right:-16px;min-width:200px;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.2rem;">
                                <i class="bi bi-award"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">#1 Travel Agency</div>
                                <div class="text-muted" style="font-size:0.75rem;">Asia Pacific 2024</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Values -->
<section class="section-padding bg-light">
    <div class="container">
        <div class="text-center gsap-fade-up">
            <span class="section-badge">Our Values</span>
            <h2 class="section-title mt-2">What We Stand For</h2>
        </div>
        <div class="row g-4 mt-2">
            <?php
            $values = [
                ['bi-heart-fill','#ef4444','Passion for Travel','We live and breathe travel. Every itinerary is crafted with genuine love for exploration and discovery.'],
                ['bi-shield-fill','#0ea5e9','Trust & Safety','Your safety is our top priority. All our packages include travel insurance and 24/7 emergency support.'],
                ['bi-people-fill','#10b981','Customer First','Every decision we make is with our travelers in mind. Your satisfaction is our success.'],
                ['bi-globe-americas','#8b5cf6','Sustainable Travel','We promote responsible tourism that respects local cultures and protects natural environments.'],
            ];
            foreach ($values as [$icon,$color,$title,$desc]):
            ?>
            <div class="col-lg-3 col-md-6 gsap-scale">
                <div class="text-center p-4 bg-white rounded-4 shadow-sm h-100">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3"
                         style="width:72px;height:72px;background:<?php echo $color; ?>15;font-size:1.8rem;color:<?php echo $color; ?>;">
                        <i class="bi <?php echo $icon; ?>"></i>
                    </div>
                    <h5 class="fw-bold"><?php echo $title; ?></h5>
                    <p class="text-muted small"><?php echo $desc; ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<!-- Stats Banner -->
<section class="stats-section section-padding">
    <div class="container">
        <div class="row g-4 text-center">
            <?php foreach ([['5','Years Experience'],['500+','Happy Travelers'],['120+','Destinations'],['98%','Satisfaction']] as [$num,$lbl]): ?>
            <div class="col-md-3 col-6 gsap-scale">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $num; ?></div>
                    <div class="stat-label"><?php echo $lbl; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-banner">
    <div class="container text-center text-white gsap-fade-up">
        <h2 class="section-title text-white">Ready to Start Your Adventure?</h2>
        <p class="mb-4" style="color:rgba(255,255,255,0.75);">Browse our curated collection of travel packages and find your perfect trip.</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="packages.php" class="btn btn-hero btn-lg"><i class="bi bi-compass me-2"></i>Explore Packages</a>
            <a href="contact.php" class="btn btn-outline-light btn-lg rounded-pill px-4"><i class="bi bi-chat me-2"></i>Get in Touch</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
