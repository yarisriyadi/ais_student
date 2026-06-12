<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ais Student - Welcome</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-splash: #0f172a;
            --accent-blue: #3498db;
        }

        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: var(--bg-splash);
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
        }

        .splash-wrapper {
            text-align: center;
            position: relative;
            width: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .splash-logo {
            width: 80%; 
            max-width: 450px; 
            height: auto;
            margin-bottom: 50px;
            filter: drop-shadow(0 0 30px rgba(52, 152, 219, 0.4));
            animation: logoEntrance 1.5s ease-out, logoPulse 3s infinite 1.5s;
        }

        .loader-container {
            width: 60%; 
            max-width: 400px;
            height: 6px; 
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            margin: 0 auto;
            overflow: hidden;
            position: relative;
        }

        .loader-bar {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #3498db, #2ecc71, #3498db);
            background-size: 200% 100%;
            box-shadow: 0 0 15px rgba(52, 152, 219, 0.8);
            animation: fillProgress 5s linear forwards, aurora 2s linear infinite;
        }

        .status-text {
            margin-top: 25px;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            opacity: 0;
            animation: textFadeIn 0.8s ease-out forwards 0.5s;
        }

        /* --- ANIMASI --- */
        @keyframes logoEntrance {
            from { opacity: 0; transform: scale(0.5) translateY(30px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes logoPulse {
            0% { transform: scale(1); filter: drop-shadow(0 0 30px rgba(52, 152, 219, 0.4)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 0 50px rgba(52, 152, 219, 0.6)); }
            100% { transform: scale(1); filter: drop-shadow(0 0 30px rgba(52, 152, 219, 0.4)); }
        }

        @keyframes fillProgress {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        @keyframes aurora {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        @keyframes textFadeIn {
            to { opacity: 1; }
        }

        /* --- PENYESUAIAN MOBILE --- */
        @media (max-width: 600px) {
            .splash-logo { 
                max-width: 280px; /* Ukuran pas untuk layar HP */
                margin-bottom: 40px;
            }
            .loader-container { 
                width: 75%; 
                height: 5px;
            }
            .status-text {
                font-size: 11px;
                letter-spacing: 2px;
            }
        }
    </style>
</head>
<body>

<div class="splash-wrapper">
    <img src="bahan_gambar/ais_icon3.png" alt="Logo" class="splash-logo">
    <div class="loader-container">
        <div class="loader-bar"></div>
    </div>
    <div class="status-text">Memuat Sistem...</div>
</div>

<script>
    setTimeout(function() {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.6s ease';
        
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 600);
    }, 4400); 
</script>

</body>
</html>