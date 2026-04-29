<?php
require_once 'config/database.php';

if(isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBackpack - Entra</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .splash-screen { z-index: 9999; animation: splashFadeOut 1s ease 3.5s forwards; }
        .splash-logo { animation: splashPulse 2s ease infinite; }
        .anim-shield { opacity: 0; animation: shieldAppear 1.5s ease forwards; }
        .anim-text-1 { opacity: 0; animation: textAppear 1s ease 1s forwards; }
        .anim-text-2 { opacity: 0; animation: textAppear 1s ease 1.5s forwards; }
        .anim-line { width: 0; height: 2px; background: linear-gradient(90deg, transparent, #dc143c, transparent); animation: lineExpand 1.5s ease 2s forwards; }
        .main-content { opacity: 0; animation: mainAppear 1.5s ease 3.5s forwards; }
        .hero-bg { filter: brightness(0.4); }
        .hero-float { filter: drop-shadow(0 0 20px rgba(220,20,60,.6)); animation: float 3s ease-in-out infinite; }
        .hero-title { background: linear-gradient(135deg, #fff, #dc143c); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .btn-hero { position: relative; overflow: hidden; border-radius: 0; transition: all .4s ease; }
        .btn-hero::before { content:''; position:absolute; top:0; left:-100%; width:100%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,.2),transparent); transition:left .5s ease; }
        .btn-hero:hover::before { left: 100%; }
        .btn-enter { border: 2px solid #dc143c; }
        .btn-enter:hover { background:transparent!important; color:#dc143c!important; box-shadow:0 0 30px rgba(220,20,60,.5); }
        .btn-register-hero { border: 2px solid rgba(255,255,255,.5); }
        .btn-register-hero:hover { border-color:#fff; background:rgba(255,255,255,.1)!important; box-shadow:0 0 30px rgba(255,255,255,.2); }
        .particle { background:#dc143c; border-radius:50%; opacity:.5; animation:particleFloat linear infinite; }

        @keyframes shieldAppear { 0%{opacity:0;transform:scale(.3) rotateY(180deg)} 60%{transform:scale(1.2) rotateY(0)} 100%{opacity:1;transform:scale(1) rotateY(0)} }
        @keyframes textAppear { 0%{opacity:0;transform:translateY(30px)} 100%{opacity:1;transform:translateY(0)} }
        @keyframes lineExpand { to { width: 300px; } }
        @keyframes splashPulse { 0%,100%{filter:drop-shadow(0 0 20px rgba(220,20,60,.3))} 50%{filter:drop-shadow(0 0 40px rgba(220,20,60,.6))} }
        @keyframes splashFadeOut { to { opacity:0; pointer-events:none; } }
        @keyframes mainAppear { to { opacity:1; } }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-15px)} }
        @keyframes particleFloat { 0%{transform:translateY(100vh) rotate(0);opacity:0} 10%{opacity:.5} 90%{opacity:.5} 100%{transform:translateY(-10vh) rotate(720deg);opacity:0} }

        @media(max-width:768px) {
            .anim-shield { font-size:5rem!important }
            .anim-text-1 { font-size:2rem!important; letter-spacing:8px!important }
            .hero-title { font-size:2.5rem!important; letter-spacing:5px!important }
            .btn-hero { padding:15px 30px!important; font-size:.9rem!important }
        }
    </style>
</head>
<body class="bg-black overflow-hidden" style="font-family:'Inter',sans-serif">

    <div class="splash-screen position-fixed top-0 start-0 w-100 h-100 bg-black d-flex align-items-center justify-content-center">
        <div class="splash-logo text-center">
            <div class="anim-shield" style="font-size:8rem; color:#dc143c">
                <i class="bi bi-backpack2"></i>
            </div>
            <div class="anim-text-1 text-white text-uppercase" style="font-family:'Orbitron',sans-serif; font-size:3rem; font-weight:900; letter-spacing:15px">MyBackpack</div>
            <div class="anim-line mx-auto my-4"></div>
            <div class="anim-text-2 text-uppercase" style="font-family:'Orbitron',sans-serif; font-size:.9rem; letter-spacing:10px; color:#dc143c">Digital Backpack</div>
        </div>
    </div>

    <div class="main-content position-relative w-100 vh-100">
        <div class="hero-bg position-absolute top-0 start-0 w-100 h-100" style="background:linear-gradient(135deg,rgba(0,0,0,.9) 0%,rgba(220,20,60,.3) 50%,rgba(0,0,0,.9) 100%), url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1920&q=80') center/cover"></div>

        <div class="position-absolute w-100 h-100 overflow-hidden" style="z-index:5" id="particles"></div>

        <div class="position-relative h-100 d-flex flex-column align-items-center justify-content-center text-white text-center p-4" style="z-index:10">
            <div class="hero-float mb-4" style="font-size:5rem; color:#dc143c">
                <i class="bi bi-backpack2"></i>
            </div>
            <h1 class="hero-title text-uppercase mb-2" style="font-family:'Orbitron',sans-serif; font-size:4rem; font-weight:900; letter-spacing:10px">MyBackpack</h1>
            <p class="text-white-50 mb-5 px-3" style="max-width:600px; letter-spacing:2px; font-size:1.2rem">
                Il tuo zaino digitale. Organizza libri, giochi, multimedia e molto altro in un unico posto.
            </p>

            <div class="d-flex gap-4 flex-wrap justify-content-center">
                <a href="login.php" class="btn-hero btn-enter text-uppercase text-decoration-none d-inline-flex align-items-center gap-2 fw-bold" style="background:#dc143c; color:#fff; padding:18px 50px; font-size:1.1rem; letter-spacing:3px">
                    <i class="fas fa-sign-in-alt"></i> Accedi
                </a>
                <a href="register.php" class="btn-hero btn-register-hero text-uppercase text-decoration-none d-inline-flex align-items-center gap-2 fw-bold" style="background:transparent; color:#fff; padding:18px 50px; font-size:1.1rem; letter-spacing:3px">
                    <i class="fas fa-user-plus"></i> Registrati
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        const container = document.getElementById('particles');
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle', 'position-absolute');
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDuration = (Math.random() * 10 + 5) + 's';
            particle.style.animationDelay = (Math.random() * 5) + 's';
            particle.style.width = (Math.random() * 4 + 1) + 'px';
            particle.style.height = particle.style.width;
            container.appendChild(particle);
        }
    </script>
</body>
</html>