<?php
require_once 'config/database.php';

// Se già loggato, vai alla dashboard
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
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            background: #000;
            overflow: hidden;
            height: 100vh;
            font-family: 'Inter', sans-serif;
        }

        /* ===== SPLASH SCREEN STILE FERRARI ===== */
        .splash-screen {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #000;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            animation: splashFadeOut 1s ease 3.5s forwards;
        }

        .splash-logo {
            text-align: center;
            animation: splashPulse 2s ease infinite;
        }

        .splash-logo .shield {
            font-size: 8rem;
            color: #dc143c;
            filter: drop-shadow(0 0 30px rgba(220, 20, 60, 0.8));
            animation: shieldAppear 1.5s ease forwards;
            opacity: 0;
        }

        .splash-logo .brand-text {
            font-family: 'Orbitron', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: white;
            letter-spacing: 15px;
            margin-top: 20px;
            opacity: 0;
            animation: textAppear 1s ease 1s forwards;
            text-transform: uppercase;
        }

        .splash-logo .brand-sub {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.9rem;
            color: #dc143c;
            letter-spacing: 10px;
            margin-top: 10px;
            opacity: 0;
            animation: textAppear 1s ease 1.5s forwards;
            text-transform: uppercase;
        }

        .splash-line {
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #dc143c, transparent);
            margin: 20px auto;
            animation: lineExpand 1.5s ease 2s forwards;
        }

        @keyframes shieldAppear {
            0% { opacity: 0; transform: scale(0.3) rotateY(180deg); }
            60% { transform: scale(1.2) rotateY(0deg); }
            100% { opacity: 1; transform: scale(1) rotateY(0deg); }
        }

        @keyframes textAppear {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes lineExpand {
            0% { width: 0; }
            100% { width: 300px; }
        }

        @keyframes splashPulse {
            0%, 100% { filter: drop-shadow(0 0 20px rgba(220, 20, 60, 0.3)); }
            50% { filter: drop-shadow(0 0 40px rgba(220, 20, 60, 0.6)); }
        }

        @keyframes splashFadeOut {
            0% { opacity: 1; }
            100% { opacity: 0; pointer-events: none; }
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            position: relative;
            width: 100%;
            height: 100vh;
            opacity: 0;
            animation: mainAppear 1.5s ease 3.5s forwards;
        }

        @keyframes mainAppear {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* Background Video/Image */
        .hero-bg {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: 
                linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(220,20,60,0.3) 50%, rgba(0,0,0,0.9) 100%),
                url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1920&q=80') center/cover;
            filter: brightness(0.4);
        }

        /* Content Overlay */
        .hero-content {
            position: relative;
            z-index: 10;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .hero-logo-icon {
            font-size: 5rem;
            color: #dc143c;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 20px rgba(220, 20, 60, 0.6));
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .hero-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            letter-spacing: 10px;
            margin-bottom: 10px;
            text-transform: uppercase;
            background: linear-gradient(135deg, #fff, #dc143c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.7);
            max-width: 600px;
            margin-bottom: 50px;
            letter-spacing: 2px;
        }

        /* Sezione Features */
        .features-row {
            display: flex;
            gap: 40px;
            margin-bottom: 50px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .feature-item {
            text-align: center;
            opacity: 0;
            animation: featureAppear 0.6s ease forwards;
        }

        .feature-item:nth-child(1) { animation-delay: 4s; }
        .feature-item:nth-child(2) { animation-delay: 4.2s; }
        .feature-item:nth-child(3) { animation-delay: 4.4s; }
        .feature-item:nth-child(4) { animation-delay: 4.6s; }

        @keyframes featureAppear {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .feature-icon {
            width: 60px; height: 60px;
            border: 2px solid rgba(220,20,60,0.5);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.5rem;
            color: #dc143c;
            transition: all 0.3s ease;
        }

        .feature-item:hover .feature-icon {
            background: #dc143c;
            color: white;
            transform: scale(1.1) rotate(5deg);
        }

        .feature-item span {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            letter-spacing: 1px;
        }

        /* Buttons */
        .hero-buttons {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-hero {
            padding: 18px 50px;
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 3px;
            text-transform: uppercase;
            border-radius: 0;
            transition: all 0.4s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-hero:hover::before {
            left: 100%;
        }

        .btn-enter {
            background: #dc143c;
            color: white;
            border: 2px solid #dc143c;
        }

        .btn-enter:hover {
            background: transparent;
            color: #dc143c;
            box-shadow: 0 0 30px rgba(220,20,60,0.5);
        }

        .btn-register-hero {
            background: transparent;
            color: white;
            border: 2px solid rgba(255,255,255,0.5);
        }

        .btn-register-hero:hover {
            border-color: white;
            background: rgba(255,255,255,0.1);
            box-shadow: 0 0 30px rgba(255,255,255,0.2);
        }

        /* Particles */
        .particles {
            position: absolute;
            width: 100%; height: 100%;
            overflow: hidden;
            z-index: 5;
        }

        .particle {
            position: absolute;
            width: 3px; height: 3px;
            background: #dc143c;
            border-radius: 50%;
            animation: particleFloat linear infinite;
            opacity: 0.5;
        }

        @keyframes particleFloat {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 0.5; }
            90% { opacity: 0.5; }
            100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title { font-size: 2.5rem; letter-spacing: 5px; }
            .splash-logo .brand-text { font-size: 2rem; letter-spacing: 8px; }
            .splash-logo .shield { font-size: 5rem; }
            .btn-hero { padding: 15px 30px; font-size: 0.9rem; }
        }
    </style>
</head>
<body>

<!-- SPLASH SCREEN STILE FERRARI -->
<div class="splash-screen">
    <div class="splash-logo">
        <div class="shield">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="brand-text">MYBACKPACK</div>
        <div class="splash-line"></div>
        <div class="brand-sub">Digital School Backpack</div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="hero-bg"></div>
    
    <!-- Particles -->
    <div class="particles" id="particles"></div>
    
    <div class="hero-content">
        <div class="hero-logo-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <h1 class="hero-title">MyBackpack</h1>
        <p class="hero-subtitle">Il tuo zaino digitale. Organizza libri, giochi, multimedia e molto altro in un unico posto.</p>
        
        <!-- Features -->
        <div class="features-row">
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-book"></i></div>
                <span>Libri</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-gamepad"></i></div>
                <span>Giochi</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-photo-video"></i></div>
                <span>Multimedia</span>
            </div>
            <div class="feature-item">
                <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                <span>Calendario</span>
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="hero-buttons">
            <a href="login.php" class="btn-hero btn-enter">
                <i class="fas fa-sign-in-alt"></i> Accedi
            </a>
            <a href="register.php" class="btn-hero btn-register-hero">
                <i class="fas fa-user-plus"></i> Registrati
            </a>
        </div>
    </div>
</div>

<script>
// Genera particelle
const container = document.getElementById('particles');
for(let i = 0; i < 30; i++) {
    const particle = document.createElement('div');
    particle.classList.add('particle');
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