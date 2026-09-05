<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page Not Found | TravelBook</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:'Poppins',sans-serif; background:#0f172a; min-height:100vh; display:flex; align-items:center; justify-content:center; text-align:center; padding:20px; overflow:hidden; }
        .error-code { font-size:clamp(6rem,20vw,12rem); font-weight:900; line-height:1; background:linear-gradient(135deg,#0ea5e9,#f97316); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
        .plane { font-size:4rem; animation:fly 3s ease-in-out infinite; display:inline-block; }
        @keyframes fly { 0%,100%{transform:translateY(0) rotate(-10deg);} 50%{transform:translateY(-20px) rotate(5deg);} }
        .cloud { position:absolute; border-radius:50px; background:rgba(255,255,255,0.03); animation:drift 8s ease-in-out infinite; }
        @keyframes drift { 0%,100%{transform:translateX(0);} 50%{transform:translateX(30px);} }
        h2 { color:#fff; font-size:2rem; margin:16px 0 8px; }
        p { color:rgba(255,255,255,0.5); margin-bottom:32px; }
        .btn-home { background:linear-gradient(135deg,#0ea5e9,#06b6d4); color:#fff; padding:14px 36px; border-radius:50px; text-decoration:none; font-weight:700; display:inline-flex; align-items:center; gap:8px; transition:transform 0.3s,box-shadow 0.3s; }
        .btn-home:hover { transform:translateY(-3px); box-shadow:0 15px 40px rgba(14,165,233,0.4); color:#fff; }
        .btn-back { background:rgba(255,255,255,0.08); color:rgba(255,255,255,0.7); padding:14px 28px; border-radius:50px; text-decoration:none; font-weight:600; display:inline-flex; align-items:center; gap:8px; transition:all 0.3s; }
        .btn-back:hover { background:rgba(255,255,255,0.15); color:#fff; }
    </style>
</head>
<body>
    <div class="cloud" style="width:200px;height:60px;top:10%;left:5%;animation-delay:0s;"></div>
    <div class="cloud" style="width:150px;height:45px;top:25%;right:8%;animation-delay:2s;"></div>
    <div class="cloud" style="width:100px;height:30px;bottom:20%;left:15%;animation-delay:4s;"></div>
    <div>
        <div class="plane">✈️</div>
        <div class="error-code">404</div>
        <h2>Lost in the Clouds!</h2>
        <p>The page you're looking for seems to have taken a detour.<br>Let's get you back on track.</p>
<?php
if (!defined('SITE_URL')) {
    @require_once __DIR__ . '/config/constants.php';
}
$homeUrl = defined('SITE_URL') ? SITE_URL : '/travel_booking/';
?>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?php echo htmlspecialchars($homeUrl); ?>" class="btn-home"><i class="bi bi-house"></i> Go Home</a>
            <a href="javascript:history.back()" class="btn-back"><i class="bi bi-arrow-left"></i> Go Back</a>
        </div>
        <p class="mt-4" style="color:rgba(255,255,255,0.25);font-size:0.8rem;">TravelBook &mdash; Your Journey Continues</p>
    </div>
</body>
</html>
