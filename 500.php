<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Server Error | TravelBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#0f172a; min-height:100vh; display:flex; align-items:center; justify-content:center; text-align:center; padding:20px; }
        .error-code { font-size:clamp(6rem,20vw,12rem); font-weight:900; line-height:1; background:linear-gradient(135deg,#ef4444,#f97316); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .icon { font-size:4rem; animation:shake 1s ease-in-out infinite; display:inline-block; }
        @keyframes shake { 0%,100%{transform:rotate(-5deg);} 50%{transform:rotate(5deg);} }
        h2 { color:#fff; font-size:2rem; margin:16px 0 8px; }
        p { color:rgba(255,255,255,0.5); margin-bottom:32px; }
        .btn-home { background:linear-gradient(135deg,#ef4444,#f97316); color:#fff; padding:14px 36px; border-radius:50px; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:transform 0.3s; }
        .btn-home:hover { transform:translateY(-3px); color:#fff; }
    </style>
</head>
<body>
    <div>
        <div class="icon">⚠️</div>
        <div class="error-code">500</div>
        <h2>Houston, We Have a Problem!</h2>
<?php
if (!defined('SITE_URL')) {
    @require_once __DIR__ . '/config/constants.php';
}
$homeUrl = defined('SITE_URL') ? SITE_URL : '/travel_booking/';
?>
        <a href="<?php echo htmlspecialchars($homeUrl); ?>" class="btn-home"><i class="bi bi-house me-2"></i>Return to Safety</a>
    </div>
</body>
</html>
