<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ColorWin - India's #1 Color Prediction Game</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        :root{
            --bg:#0F172A;--card:#1E293B;--border:#334155;
            --primary:#7C3AED;--green:#22C55E;--red:#EF4444;
            --gold:#F59E0B;--text:#E2E8F0;--muted:#94A3B8;
        }
        html{scroll-behavior:smooth;}
        body{background:var(--bg);color:var(--text);font-family:'Segoe UI',sans-serif;overflow-x:hidden;}

        /* NAVBAR */
        nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;background:rgba(15,23,42,.95);backdrop-filter:blur(12px);border-bottom:1px solid rgba(255,255,255,.05);}
        .logo{font-size:20px;font-weight:900;background:linear-gradient(135deg,var(--primary),var(--gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;white-space:nowrap;}
        .nav-btns{display:flex;gap:8px;align-items:center;}
        .btn-login{background:transparent;border:1px solid var(--primary);border-radius:8px;padding:7px 14px;color:var(--primary);font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;white-space:nowrap;}
        .btn-login:hover{background:rgba(124,58,237,.15);}
        .btn-signup{background:var(--primary);border:none;border-radius:8px;padding:7px 14px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;white-space:nowrap;}
        .btn-signup:hover{background:#9D5CF6;}
        .btn-download{background:linear-gradient(135deg,var(--green),#16A34A);border:none;border-radius:8px;padding:7px 14px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;white-space:nowrap;display:inline-flex;align-items:center;gap:5px;}
        .btn-download:hover{opacity:.85;transform:translateY(-1px);}
        .btn-download-mobile{display:none;background:linear-gradient(135deg,var(--green),#16A34A);border:none;border-radius:12px;padding:13px 22px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;align-items:center;gap:8px;justify-content:center;}
        @media(max-width:640px){
            .btn-download{display:none;}
            .btn-download-mobile{display:inline-flex;width:100%;}
        }
        @media(max-width:400px){
            .btn-login{padding:6px 10px;font-size:12px;}
            .btn-signup{padding:6px 10px;font-size:12px;}
            .logo{font-size:17px;}
        }

        /* HERO */
        .hero{min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:100px 24px 60px;position:relative;overflow:hidden;}
        .hero-bg{position:absolute;inset:0;background:radial-gradient(ellipse at 50% 0%,rgba(124,58,237,.25) 0%,transparent 70%);pointer-events:none;}
        .hero-circles{position:absolute;inset:0;pointer-events:none;overflow:hidden;}
        .circle{position:absolute;border-radius:50%;opacity:.06;animation:float 6s ease-in-out infinite;}
        .c1{width:400px;height:400px;background:var(--primary);top:-100px;left:-100px;animation-delay:0s;}
        .c2{width:300px;height:300px;background:var(--gold);bottom:-50px;right:-50px;animation-delay:2s;}
        .c3{width:200px;height:200px;background:var(--green);top:40%;right:10%;animation-delay:4s;}
        @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-20px)}}

        .hero-content{position:relative;z-index:1;max-width:700px;}
        .hero-badge{display:inline-flex;align-items:center;gap:6px;background:rgba(124,58,237,.15);border:1px solid rgba(124,58,237,.4);border-radius:20px;padding:6px 16px;font-size:12px;color:var(--primary);font-weight:600;margin-bottom:24px;}
        .hero-badge span{width:6px;height:6px;background:var(--green);border-radius:50%;animation:pulse 1s infinite;}
        @keyframes pulse{0%,100%{opacity:1}50%{opacity:.3}}
        .hero h1{font-size:clamp(36px,8vw,64px);font-weight:900;line-height:1.1;margin-bottom:20px;}
        .hero h1 .g{background:linear-gradient(135deg,var(--primary),var(--gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .hero p{font-size:16px;color:var(--muted);margin-bottom:36px;line-height:1.7;max-width:500px;margin-left:auto;margin-right:auto;}
        .hero-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-bottom:48px;}
        .btn-main{background:linear-gradient(135deg,var(--primary),#9D5CF6);border:none;border-radius:12px;padding:16px 36px;color:#fff;font-size:16px;font-weight:800;cursor:pointer;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:8px;}
        .btn-main:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(124,58,237,.4);}
        .btn-sec{background:transparent;border:1px solid var(--border);border-radius:12px;padding:16px 36px;color:var(--text);font-size:16px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:8px;}
        .btn-sec:hover{border-color:var(--primary);color:var(--primary);}

        /* LIVE GAME PREVIEW */
        .game-preview{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:20px;max-width:340px;margin:0 auto;text-align:left;}
        .gp-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;}
        .gp-round{font-size:11px;color:var(--muted);}
        .gp-round span{color:var(--gold);font-weight:700;}
        .gp-live{display:flex;align-items:center;gap:5px;font-size:11px;color:var(--green);}
        .gp-live span{width:6px;height:6px;background:var(--green);border-radius:50%;animation:pulse 1s infinite;}
        .gp-timer{text-align:center;margin-bottom:14px;}
        .gp-timer-num{font-size:48px;font-weight:900;color:var(--green);line-height:1;}
        .gp-timer-bar{background:var(--border);border-radius:4px;height:4px;margin-top:8px;overflow:hidden;}
        .gp-timer-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--gold));border-radius:4px;animation:timerAnim 30s linear infinite;}
        @keyframes timerAnim{from{width:100%}to{width:0%}}
        .gp-colors{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:12px;}
        .gp-cbtn{border:none;border-radius:10px;padding:12px 6px;font-size:12px;font-weight:700;color:#fff;text-align:center;cursor:pointer;}
        .gp-cbtn.g{background:var(--green);}
        .gp-cbtn.v{background:#A855F7;}
        .gp-cbtn.r{background:var(--red);}
        .gp-nums{display:grid;grid-template-columns:repeat(5,1fr);gap:6px;}
        .gp-num{border-radius:8px;padding:10px 4px;font-size:14px;font-weight:800;text-align:center;}
        .gp-num.g{background:rgba(34,197,94,.15);color:var(--green);border:1px solid var(--green);}
        .gp-num.r{background:rgba(239,68,68,.15);color:var(--red);border:1px solid var(--red);}
        .gp-num.v{background:rgba(168,85,247,.15);color:#A855F7;border:1px solid #A855F7;}

        /* STATS */
        .stats{padding:60px 24px;text-align:center;}
        .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;max-width:800px;margin:0 auto;}
        .stat-box{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px 16px;}
        .stat-num{font-size:32px;font-weight:900;margin-bottom:4px;}
        .stat-lbl{font-size:12px;color:var(--muted);}

        /* HOW IT WORKS */
        .how{padding:60px 24px;max-width:900px;margin:0 auto;}
        .section-title{text-align:center;font-size:28px;font-weight:800;margin-bottom:8px;}
        .section-sub{text-align:center;color:var(--muted);font-size:14px;margin-bottom:40px;}
        .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;}
        .step{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px;text-align:center;position:relative;}
        .step-num{width:40px;height:40px;background:linear-gradient(135deg,var(--primary),#9D5CF6);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;margin:0 auto 14px;}
        .step h3{font-size:15px;font-weight:700;margin-bottom:8px;}
        .step p{font-size:12px;color:var(--muted);line-height:1.6;}
        .step-arrow{position:absolute;right:-14px;top:50%;transform:translateY(-50%);color:var(--border);font-size:20px;z-index:1;}

        /* COLOR GUIDE */
        .colors-section{padding:60px 24px;max-width:900px;margin:0 auto;}
        .color-cards{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}
        .color-card{border-radius:16px;padding:24px;text-align:center;}
        .color-card.green{background:linear-gradient(135deg,rgba(34,197,94,.15),rgba(34,197,94,.05));border:1px solid rgba(34,197,94,.3);}
        .color-card.red{background:linear-gradient(135deg,rgba(239,68,68,.15),rgba(239,68,68,.05));border:1px solid rgba(239,68,68,.3);}
        .color-card.violet{background:linear-gradient(135deg,rgba(168,85,247,.15),rgba(168,85,247,.05));border:1px solid rgba(168,85,247,.3);}
        .color-dot{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:900;margin:0 auto 12px;color:#fff;}
        .color-dot.g{background:var(--green);}
        .color-dot.r{background:var(--red);}
        .color-dot.v{background:#A855F7;}
        .color-card h3{font-size:16px;font-weight:800;margin-bottom:6px;}
        .color-card p{font-size:12px;color:var(--muted);margin-bottom:8px;}
        .color-card .mult{font-size:20px;font-weight:900;color:var(--gold);}

        /* FEATURES */
        .features{padding:60px 24px;max-width:900px;margin:0 auto;}
        .feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
        .feat{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:20px;}
        .feat-icon{font-size:28px;margin-bottom:10px;}
        .feat h3{font-size:14px;font-weight:700;margin-bottom:6px;}
        .feat p{font-size:12px;color:var(--muted);line-height:1.6;}

        /* CTA */
        .cta{padding:80px 24px;text-align:center;position:relative;overflow:hidden;}
        .cta-bg{position:absolute;inset:0;background:radial-gradient(ellipse at 50% 50%,rgba(124,58,237,.2) 0%,transparent 70%);pointer-events:none;}
        .cta-box{background:linear-gradient(135deg,rgba(124,58,237,.2),rgba(79,70,229,.1));border:1px solid rgba(124,58,237,.3);border-radius:24px;padding:48px 24px;max-width:600px;margin:0 auto;position:relative;}
        .cta h2{font-size:32px;font-weight:900;margin-bottom:12px;}
        .cta p{color:var(--muted);font-size:14px;margin-bottom:28px;}
        .cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}

        /* FOOTER */
        footer{padding:32px 24px;text-align:center;border-top:1px solid var(--border);color:var(--muted);font-size:12px;}
        footer a{color:var(--muted);text-decoration:none;margin:0 8px;}
        footer a:hover{color:var(--primary);}

        /* RESULTS TICKER */
        .ticker{background:var(--card);border-top:1px solid var(--border);border-bottom:1px solid var(--border);padding:10px 0;overflow:hidden;margin-bottom:0;}
        .ticker-inner{display:flex;gap:10px;animation:ticker 20s linear infinite;width:max-content;}
        @keyframes ticker{from{transform:translateX(0)}to{transform:translateX(-50%)}}
        .tick-dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;flex-shrink:0;}
        .tick-dot.g{background:var(--green);}
        .tick-dot.r{background:var(--red);}
        .tick-dot.v{background:#A855F7;}

        @media(max-width:640px){
            .stats-grid{grid-template-columns:1fr 1fr;}
            .steps{grid-template-columns:1fr;}
            .step-arrow{display:none;}
            .color-cards{grid-template-columns:1fr 1fr;}
            .feat-grid{grid-template-columns:1fr 1fr;}
            .nav-btns .btn-sec-nav{display:none;}
            .hero h1{font-size:32px;}
            .hero p{font-size:14px;}
            .btn-main,.btn-sec{padding:13px 22px;font-size:14px;}
            .section-title{font-size:22px;}
            .game-preview{max-width:100%;}
            .cta h2{font-size:24px;}
            .cta-box{padding:32px 16px;}
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="logo"><img src="/colorlogo-removebg-preview.png" alt="ColorWin" style="height:40px;width:auto;"></div>
    <div class="nav-btns">
        <a href="/app/download" class="btn-download"><i class="fas fa-download"></i> Download App</a>
        <a href="/login" class="btn-login">Login</a>
        <a href="/login" class="btn-signup">Register Now</a>
    </div>
</nav>

<!-- RESULTS TICKER -->
<div style="margin-top:64px;">
    <div class="ticker">
        <div class="ticker-inner" id="ticker">
            @php
            $tickColors = ['g','r','v','g','r','g','v','r','g','r','v','g','r','g','r','v','g','r','g','v'];
            $tickNums   = [1,2,0,3,4,7,5,6,9,8,1,3,2,7,4,0,9,6,5,8];
            @endphp
            @foreach($tickNums as $i => $n)
            <div class="tick-dot {{ $tickColors[$i] }}">{{ $n }}</div>
            @endforeach
            @foreach($tickNums as $i => $n)
            <div class="tick-dot {{ $tickColors[$i] }}">{{ $n }}</div>
            @endforeach
        </div>
    </div>
</div>

<!-- HERO -->
<section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-circles">
        <div class="circle c1"></div>
        <div class="circle c2"></div>
        <div class="circle c3"></div>
    </div>
    <div class="hero-content">
        <div class="hero-badge">
            <span></span> Live Game Running • 1000+ Players Online
        </div>
        <h1>
            Predict the Color<br>
            <span class="g">Win Real Money</span>
        </h1>
        <p>India's most trusted color prediction platform. Simple gameplay, instant withdrawals, and real cash prizes every 30 seconds!</p>
        <div class="hero-btns">
            <a href="/login" class="btn-main">
                <i class="fas fa-gamepad"></i> Start Playing Now
            </a>
            <a href="#how" class="btn-sec">
                <i class="fas fa-play-circle"></i> How to Play
            </a>
            <a href="/app/download" class="btn-download-mobile">
                <i class="fas fa-download"></i> Download App
            </a>
        </div>

        <!-- GAME PREVIEW -->
        <div class="game-preview">
            <div class="gp-header">
                <div class="gp-round">Round: <span>20260520025</span></div>
                <div class="gp-live"><span></span> LIVE</div>
            </div>
            <div class="gp-timer">
                <div class="gp-timer-num" id="demoTimer">28</div>
                <div style="font-size:10px;color:var(--muted);margin-top:2px;">seconds remaining</div>
                <div class="gp-timer-bar"><div class="gp-timer-fill"></div></div>
            </div>
            <div class="gp-colors">
                <div class="gp-cbtn g">🟢 Green<br><span style="font-size:10px;">2x</span></div>
                <div class="gp-cbtn v">🟣 Violet<br><span style="font-size:10px;">2x</span></div>
                <div class="gp-cbtn r">🔴 Red<br><span style="font-size:10px;">2x</span></div>
            </div>
            <div class="gp-nums">
                <div class="gp-num v">0</div>
                <div class="gp-num g">1</div>
                <div class="gp-num r">2</div>
                <div class="gp-num g">3</div>
                <div class="gp-num r">4</div>
                <div class="gp-num v">5</div>
                <div class="gp-num r">6</div>
                <div class="gp-num g">7</div>
                <div class="gp-num r">8</div>
                <div class="gp-num g">9</div>
            </div>
        </div>
    </div>
</section>

<!-- STATS -->
<section class="stats">
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-num" style="color:var(--primary);">50K+</div>
            <div class="stat-lbl">Registered Players</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:var(--gold);">₹2Cr+</div>
            <div class="stat-lbl">Total Winnings Paid</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:var(--green);">30s</div>
            <div class="stat-lbl">Per Round</div>
        </div>
        <div class="stat-box">
            <div class="stat-num" style="color:var(--red);">24/7</div>
            <div class="stat-lbl">Live Games</div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="how" id="how">
    <div class="section-title">How to Play?</div>
    <div class="section-sub">3 simple steps to start winning</div>
    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <h3>Register & Deposit</h3>
            <p>Create account with referral code. Add money via UPI, QR or USDT. Minimum deposit ₹100.</p>
            <div class="step-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <h3>Place Your Bet</h3>
            <p>Choose a color (Green/Red/Violet) or a number (0-9). Enter amount and place bet before timer ends.</p>
            <div class="step-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <h3>Win & Withdraw</h3>
            <p>Result every 30 seconds. Win up to 9x your bet. Withdraw instantly to bank, UPI or USDT.</p>
        </div>
    </div>
</section>

<!-- COLOR GUIDE -->
<section class="colors-section">
    <div class="section-title">Color Guide</div>
    <div class="section-sub">Know your colors and payouts</div>
    <div class="color-cards">
        <div class="color-card green">
            <div class="color-dot g">G</div>
            <h3 style="color:#22C55E;">Green</h3>
            <p>Numbers: 1, 3, 7, 9</p>
            <div class="mult">2x Payout</div>
        </div>
        <div class="color-card red">
            <div class="color-dot r">R</div>
            <h3 style="color:#EF4444;">Red</h3>
            <p>Numbers: 2, 4, 6, 8</p>
            <div class="mult">2x Payout</div>
        </div>
        <div class="color-card violet">
            <div class="color-dot v">V</div>
            <h3 style="color:#A855F7;">Violet</h3>
            <p>Numbers: 0, 5</p>
            <div class="mult">2x Payout</div>
        </div>
    </div>
    <div style="text-align:center;margin-top:20px;background:var(--card);border:1px solid var(--border);border-radius:12px;padding:16px;">
        <span style="color:var(--gold);font-size:14px;font-weight:700;">🎯 Exact Number Bet → </span>
        <span style="font-size:20px;font-weight:900;color:var(--gold);">9x Payout!</span>
    </div>
</section>

<!-- FEATURES -->
<section class="features">
    <div class="section-title">Why ColorWin?</div>
    <div class="section-sub">Trusted by thousands of players</div>
    <div class="feat-grid">
        <div class="feat">
            <div class="feat-icon">⚡</div>
            <h3>Instant Withdrawal</h3>
            <p>Withdraw your winnings instantly to bank account, UPI or USDT wallet.</p>
        </div>
        <div class="feat">
            <div class="feat-icon">🔒</div>
            <h3>100% Secure</h3>
            <p>Bank-level security with OTP verification and withdrawal password protection.</p>
        </div>
        <div class="feat">
            <div class="feat-icon">🎁</div>
            <h3>Daily Bonus</h3>
            <p>Claim daily check-in bonus. More consecutive days = more bonus rewards!</p>
        </div>
        <div class="feat">
            <div class="feat-icon">👥</div>
            <h3>Refer & Earn</h3>
            <p>Invite friends and earn 2% commission on every bet they place. Multi-level rewards!</p>
        </div>
        <div class="feat">
            <div class="feat-icon">📱</div>
            <h3>Mobile Friendly</h3>
            <p>Perfectly optimized for mobile. Play anytime, anywhere on any device.</p>
        </div>
        <div class="feat">
            <div class="feat-icon">🏆</div>
            <h3>VIP Program</h3>
            <p>Level up to VIP for higher cashback, faster withdrawals and exclusive bonuses.</p>
        </div>
    </div>
</section>

<!-- LIVE BETS FEED -->
<section style="padding:40px 16px;max-width:900px;margin:0 auto;">
    <div class="section-title">🔴 Live Bets</div>
    <div class="section-sub">Real-time bets from all players</div>

    <!-- ONLINE COUNT -->
    <div style="display:flex;justify-content:center;gap:12px;margin-bottom:20px;">
        <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:12px 20px;text-align:center;flex:1;max-width:160px;">
            <div style="font-size:22px;font-weight:800;color:var(--green);" id="onlineCount">--</div>
            <div style="font-size:11px;color:var(--muted);">🟢 Online</div>
        </div>
        <div style="background:var(--card);border:1px solid var(--border);border-radius:12px;padding:12px 20px;text-align:center;flex:1;max-width:160px;">
            <div style="font-size:22px;font-weight:800;color:var(--gold);" id="todayBets">--</div>
            <div style="font-size:11px;color:var(--muted);">🎯 Bets Today</div>
        </div>
    </div>

    <!-- BETS CARDS (Mobile) -->
    <div style="background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;">
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;">
            <span style="font-size:13px;font-weight:700;">Recent Bets</span>
            <span style="display:flex;align-items:center;gap:5px;font-size:11px;color:var(--green);">
                <span style="width:6px;height:6px;background:var(--green);border-radius:50%;display:inline-block;animation:pulse 1s infinite;"></span>
                Live
            </span>
        </div>
        <div id="liveBetsBody">
            <div style="text-align:center;padding:30px;color:var(--muted);">Loading...</div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta">
    <div class="cta-bg"></div>
    <div class="cta-box">
        <div style="font-size:48px;margin-bottom:12px;">🎮</div>
        <h2>Ready to Win?</h2>
        <p>Join 50,000+ players already winning on ColorWin. Register now with a referral code and start playing!</p>
        <div class="cta-btns">
            <a href="/login" class="btn-main" style="font-size:15px;padding:14px 32px;">
                <i class="fas fa-user-plus"></i> Create Account
            </a>
            <a href="/login" class="btn-sec" style="font-size:15px;padding:14px 32px;">
                <i class="fas fa-sign-in-alt"></i> Login
            </a>
        </div>
        <div style="margin-top:20px;font-size:11px;color:var(--muted);">
            🔒 Secure • ⚡ Instant Withdrawal • 🎁 Daily Bonus
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div style="margin-bottom:12px;"><img src="/colorlogo-removebg-preview.png" alt="ColorWin" style="height:40px;width:auto;"></div>
    <div style="margin-bottom:12px;">
        <a href="/login">Play Now</a>
        <a href="#how">How to Play</a>
        <a href="/login">Register</a>
        <a href="/admin/login">Admin</a>
    </div>
    <div style="color:var(--muted);font-size:11px;">
        © 2026 ColorWin. All rights reserved. | Play Responsibly 18+
    </div>
</footer>

<script>
// Demo timer countdown
let t = 28;
setInterval(() => {
    t--;
    if (t < 0) t = 30;
    const el = document.getElementById('demoTimer');
    if (el) {
        el.textContent = t;
        el.style.color = t <= 5 ? '#EF4444' : t <= 10 ? '#F59E0B' : '#22C55E';
    }
}, 1000);

// Live bets feed
const colorMap = { green:'#22C55E', red:'#EF4444', violet:'#A855F7' };
const colorIcon = { green:'🟢', red:'🔴', violet:'🟣' };

async function loadLiveBets() {
    try {
        const res = await fetch('/api/public/stats');
        const data = await res.json();

        // Online count
        if (data.online_players) document.getElementById('onlineCount').textContent = data.online_players.toLocaleString();
        if (data.total_bets_today !== undefined) document.getElementById('todayBets').textContent = data.total_bets_today.toLocaleString();

        // Bets cards
        const bets = data.recent_bets || [];
        const tbody = document.getElementById('liveBetsBody');

        if (!bets.length) {
            tbody.innerHTML = '<div style="text-align:center;padding:30px;color:var(--muted);">No bets yet. Be the first to play!</div>';
            return;
        }

        tbody.innerHTML = bets.map((b, i) => {
            const color   = colorMap[b.bet_on] || '#94A3B8';
            const icon    = colorIcon[b.bet_on] || '🔵';
            const betLabel = b.bet_type === 'color'
                ? icon + ' ' + b.bet_on.charAt(0).toUpperCase() + b.bet_on.slice(1)
                : '🔢 No. ' + b.bet_on;

            const isWon  = b.status === 'won';
            const isLost = b.status === 'lost';

            const resultHtml = isWon
                ? `<span style="background:rgba(34,197,94,.15);color:#22C55E;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">+₹${parseFloat(b.win_amt).toFixed(0)} 🎉</span>`
                : isLost
                ? `<span style="background:rgba(239,68,68,.15);color:#EF4444;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">Lost</span>`
                : `<span style="background:rgba(148,163,184,.15);color:#94A3B8;border-radius:20px;padding:3px 10px;font-size:11px;font-weight:700;">⏳</span>`;

            return `
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-bottom:1px solid rgba(51,65,85,.4);gap:8px;">
                <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#7C3AED,#9D5CF6);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;">${b.name[0]}</div>
                    <div style="min-width:0;">
                        <div style="font-size:13px;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${b.name}</div>
                        <div style="font-size:11px;color:${color};font-weight:600;margin-top:1px;">${betLabel}</div>
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:14px;font-weight:800;color:var(--gold)">₹${parseFloat(b.amount).toFixed(0)}</div>
                    <div style="margin-top:3px;">${resultHtml}</div>
                </div>
            </div>`;
        }).join('');

        // Update results ticker
        const results = data.recent_results || [];
        if (results.length) {
            const classMap = { green:'g', red:'r', violet:'v' };
            const tickerHtml = [...results, ...results].map(r =>
                `<div class="tick-dot ${classMap[r.color] || 'v'}">${r.number}</div>`
            ).join('');
            document.getElementById('ticker').innerHTML = tickerHtml;
        }

    } catch(e) {}
}

loadLiveBets();
setInterval(loadLiveBets, 5000);
</script>
</body>
</html>
