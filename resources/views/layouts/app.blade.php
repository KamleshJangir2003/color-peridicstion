<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', 'ColorWin') - ColorWin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        :root{
            --bg:#0F172A;--card:#1E293B;--border:#334155;
            --primary:#7C3AED;--primary-light:#9D5CF6;
            --green:#22C55E;--red:#EF4444;--violet:#A855F7;--gold:#F59E0B;
            --text:#E2E8F0;--muted:#94A3B8;
        }
        body{background:var(--bg);color:var(--text);font-family:'Segoe UI',sans-serif;min-height:100vh;}
        .navbar{background:var(--card);border-bottom:1px solid var(--border);padding:0 16px;height:54px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
        .navbar-brand{font-size:19px;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .wallet-chip{background:rgba(124,58,237,.2);border:1px solid var(--primary);border-radius:20px;padding:5px 12px;font-size:13px;font-weight:700;color:var(--gold);display:flex;align-items:center;gap:6px;}
        .logout-btn{background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:7px 11px;color:#EF4444;font-size:13px;cursor:pointer;}
        .logout-btn:hover{background:rgba(239,68,68,.3);}
        .bottom-nav{position:fixed;bottom:0;left:0;right:0;background:var(--card);border-top:1px solid var(--border);display:flex;z-index:100;}
        .bottom-nav a{flex:1;display:flex;flex-direction:column;align-items:center;padding:8px 0 6px;color:var(--muted);text-decoration:none;font-size:10px;gap:3px;transition:color .2s;}
        .bottom-nav a.active,.bottom-nav a:hover{color:var(--primary);}
        .bottom-nav a i{font-size:19px;}
        .main-content{padding:14px 14px 80px;max-width:480px;margin:0 auto;}
        .card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:14px;margin-bottom:14px;}
        .form-control{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:11px 13px;color:var(--text);font-size:14px;outline:none;transition:border-color .2s;}
        .form-control:focus{border-color:var(--primary);}
        .form-label{font-size:12px;color:var(--muted);margin-bottom:5px;display:block;}
        .form-group{margin-bottom:13px;}
        .badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
        .badge-green{background:rgba(34,197,94,.15);color:var(--green);}
        .badge-red{background:rgba(239,68,68,.15);color:var(--red);}
        .badge-gold{background:rgba(245,158,11,.15);color:var(--gold);}
        .badge-muted{background:rgba(148,163,184,.15);color:var(--muted);}
        .badge-purple{background:rgba(124,58,237,.15);color:var(--primary);}
        .btn{border:none;border-radius:10px;padding:12px 18px;font-size:14px;font-weight:600;cursor:pointer;transition:all .2s;width:100%;}
        .btn-primary{background:var(--primary);color:#fff;}
        .btn-primary:hover{background:var(--primary-light);}
        .btn-outline{background:transparent;border:1px solid var(--border);color:var(--text);}
        .btn:disabled{opacity:.5;cursor:not-allowed;}
        .toast-wrap{position:fixed;top:62px;right:14px;z-index:999;display:flex;flex-direction:column;gap:7px;}
        .toast{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:11px 15px;font-size:13px;min-width:200px;animation:slideIn .3s ease;}
        .toast.success{border-left:3px solid var(--green);}
        .toast.error{border-left:3px solid var(--red);}
        @keyframes slideIn{from{transform:translateX(110%);opacity:0}to{transform:translateX(0);opacity:1}}
    </style>
    @stack('styles')
</head>
<body>

<div class="toast-wrap" id="toastWrap"></div>

<!-- NAVBAR -->
<nav class="navbar">
    <span class="navbar-brand">🎮 ColorWin</span>
    <div style="display:flex;align-items:center;gap:8px;">
        <div class="wallet-chip">
            <i class="fas fa-wallet" style="font-size:12px;"></i>
            <span id="navBalanceAmt">₹0</span>
        </div>
        <button class="logout-btn" onclick="doLogout()">
            <i class="fas fa-sign-out-alt"></i>
        </button>
    </div>
</nav>

<!-- CONTENT -->
<div class="main-content">
    @yield('content')
</div>

<!-- BOTTOM NAV -->
<nav class="bottom-nav">
    <a href="/game"     class="{{ request()->is('game')     ? 'active' : '' }}">
        <i class="fas fa-gamepad"></i>Game
    </a>
    <a href="/wallet"   class="{{ request()->is('wallet')   ? 'active' : '' }}">
        <i class="fas fa-wallet"></i>Wallet
    </a>
    <a href="/deposit"  class="{{ request()->is('deposit')  ? 'active' : '' }}">
        <i class="fas fa-plus-circle"></i>Deposit
    </a>
    <a href="/withdraw" class="{{ request()->is('withdraw') ? 'active' : '' }}">
        <i class="fas fa-arrow-up"></i>Withdraw
    </a>
    <a href="/profile"  class="{{ request()->is('profile')  ? 'active' : '' }}">
        <i class="fas fa-user"></i>Profile
    </a>
</nav>

<script>
const TOKEN = localStorage.getItem('token');

// Auth check
if (!TOKEN && !window.location.pathname.includes('/login') && !window.location.pathname.includes('/forgot')) {
    window.location.href = '/login';
}

// Toast
function showToast(msg, type='success') {
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    document.getElementById('toastWrap').appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

// Logout
function doLogout() {
    fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + (localStorage.getItem('token') || ''),
            'Accept': 'application/json'
        }
    }).finally(() => {
        localStorage.removeItem('token');
        window.location.href = '/login';
    });
}

// Nav balance
async function loadNavBalance() {
    if (!TOKEN) return;
    try {
        const r = await fetch('/api/wallet/balance', {
            headers: {'Authorization':'Bearer '+TOKEN, 'Accept':'application/json'}
        });
        const d = await r.json();
        if (d.message === 'Unauthenticated.') {
            localStorage.removeItem('token');
            window.location.href = '/login';
            return;
        }
        if (d.total !== undefined) {
            document.getElementById('navBalanceAmt').textContent = '₹' + parseFloat(d.total).toFixed(2);
        }
    } catch(e) {}
}

loadNavBalance();
setInterval(loadNavBalance, 15000);
</script>

@stack('scripts')
</body>
</html>
