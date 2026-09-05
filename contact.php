<?php
$pageTitle = 'Contact Us';
include 'includes/header.php';

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRF($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please refresh and try again.';
    } else {
        $name    = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')));
        $email   = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $subject = htmlspecialchars(strip_tags(trim($_POST['subject'] ?? '')));
        $message = htmlspecialchars(strip_tags(trim($_POST['message'] ?? '')));

        if ($name && $email && $message && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $name, $email, $subject, $message);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $error = 'Failed to send your message. Please try again.';
            }
            $stmt->close();
        } else {
            $error = 'Please fill in all required fields with valid data.';
        }
    }
}
?>

<div class="page-header">
    <h1>Contact Us</h1>
    <p class="mt-2" style="opacity:0.7;">We're here to help plan your perfect trip</p>
</div>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-4 gsap-fade-right">
                <div class="contact-info-card">
                    <h4 class="fw-bold mb-1 text-white">Get in Touch</h4>
                    <p class="mb-4" style="color:rgba(255,255,255,0.65);">Have questions about a package? Need travel advice? We'd love to hear from you.</p>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Visit Us</div>
                            <div style="color:rgba(255,255,255,0.65);font-size:0.9rem;">123 Travel Street, Suite 400<br>New York, NY 10001</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Call Us</div>
                            <div style="color:rgba(255,255,255,0.65);font-size:0.9rem;">+1 (800) TRAVEL-1<br>Mon–Fri, 9am–6pm</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Email Us</div>
                            <div style="color:rgba(255,255,255,0.65);font-size:0.9rem;">info@travelbook.com<br>support@travelbook.com</div>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon"><i class="bi bi-clock-fill"></i></div>
                        <div>
                            <div class="fw-semibold">Working Hours</div>
                            <div style="color:rgba(255,255,255,0.65);font-size:0.9rem;">Mon–Sat: 9:00AM – 8:00PM<br>Sunday: 10:00AM – 5:00PM</div>
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-2">
                        <?php foreach (['facebook','instagram','twitter-x','linkedin'] as $s): ?>
                        <a href="#" class="text-white opacity-50" style="transition:opacity 0.2s;" onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=0.5">
                            <i class="bi bi-<?php echo $s; ?> fs-5"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="col-lg-8 gsap-fade-left">
                <?php if ($success): ?>
                <div class="text-center py-5">
                    <div class="mb-3" style="font-size:4rem;">🎉</div>
                    <h3 class="fw-bold">Message Sent!</h3>
                    <p class="text-muted">Thank you for reaching out. Our team will get back to you within 24 hours.</p>
                    <a href="contact.php" class="btn btn-primary mt-2">Send Another Message</a>
                </div>
                <?php else: ?>
                <div class="bg-white rounded-4 shadow-sm p-5 border" style="border-color:rgba(0,0,0,0.05)!important;">
                    <h4 class="fw-bold mb-4">Send Us a Message</h4>
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger rounded-3"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST" id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRF(); ?>">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Full Name *</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required maxlength="100"
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? (isLoggedIn() ? $_SESSION['user_name'] : '')); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address *</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? (isLoggedIn() ? $_SESSION['user_email'] : '')); ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Subject</label>
                                <select name="subject" class="form-select">
                                    <option value="General Inquiry">General Inquiry</option>
                                    <option value="Package Information">Package Information</option>
                                    <option value="Booking Support">Booking Support</option>
                                    <option value="Cancellation">Cancellation Request</option>
                                    <option value="Partnership">Partnership Opportunity</option>
                                    <option value="Complaint">Complaint</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Message *</label>
                                <textarea name="message" class="form-control" rows="5" required maxlength="2000"
                                          placeholder="Tell us how we can help you..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-hero px-5">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- Map embed -->
                <div class="mt-4 rounded-4 overflow-hidden" style="height:250px;border:1px solid rgba(0,0,0,0.08);">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.30591910525!2d-74.25986548248684!3d40.69714941932609!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY!5e0!3m2!1sen!2sus!4v1701368416399!5m2!1sen!2sus"
                        width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
