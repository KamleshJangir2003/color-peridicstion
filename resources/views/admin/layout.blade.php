<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - ColorWin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        :root{
            --bg:#0F172A;--sidebar:#111827;--card:#1E293B;--border:#334155;
            --primary:#7C3AED;--green:#22C55E;--red:#EF4444;--gold:#F59E0B;
            --violet:#A855F7;--text:#E2E8F0;--muted:#94A3B8;
        }
        body{background:var(--bg);color:var(--text);font-family:'Segoe UI',sans-serif;display:flex;min-height:100vh;}

        /* SIDEBAR */
        .sidebar{width:240px;background:var(--sidebar);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;transition:transform 0.3s;}
        .sidebar-logo{padding:20px 16px;border-bottom:1px solid var(--border);}
        .sidebar-logo h2{font-size:18px;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .sidebar-logo p{font-size:11px;color:var(--muted);margin-top:2px;}
        .sidebar-nav{flex:1;padding:12px 0;overflow-y:auto;}
        .nav-section{padding:8px 16px 4px;font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;}
        .nav-item{display:flex;align-items:center;gap:10px;padding:10px 16px;color:var(--muted);text-decoration:none;font-size:13px;font-weight:500;transition:all 0.2s;border-left:3px solid transparent;}
        .nav-item:hover{color:var(--text);background:rgba(255,255,255,0.04);}
        .nav-item.active{color:var(--primary);background:rgba(124,58,237,0.1);border-left-color:var(--primary);}
        .nav-item i{width:18px;text-align:center;font-size:14px;}
        .sidebar-footer{padding:16px;border-top:1px solid var(--border);}
        .admin-info{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
        .admin-avatar{width:36px;height:36px;background:linear-gradient(135deg,var(--primary),var(--gold));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;}
        .admin-name{font-size:13px;font-weight:600;}
        .admin-role{font-size:11px;color:var(--muted);}
        .logout-btn{width:100%;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.3);border-radius:8px;padding:8px;color:var(--red);font-size:13px;cursor:pointer;transition:all 0.2s;}
        .logout-btn:hover{background:rgba(239,68,68,0.2);}

        /* MAIN */
        .main{margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh;}
        .topbar{background:var(--card);border-bottom:1px solid var(--border);padding:0 24px;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
        .topbar-title{font-size:16px;font-weight:700;}
        .topbar-right{display:flex;align-items:center;gap:12px;}
        .page-content{padding:24px;flex:1;}

        /* CARDS */
        .card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;}
        .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:20px;position:relative;overflow:hidden;}
        .stat-card::after{content:'';position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;opacity:0.1;}
        .stat-card.purple::after{background:var(--primary);}
        .stat-card.green::after{background:var(--green);}
        .stat-card.red::after{background:var(--red);}
        .stat-card.gold::after{background:var(--gold);}
        .stat-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px;}
        .stat-icon.purple{background:rgba(124,58,237,0.15);}
        .stat-icon.green{background:rgba(34,197,94,0.15);}
        .stat-icon.red{background:rgba(239,68,68,0.15);}
        .stat-icon.gold{background:rgba(245,158,11,0.15);}
        .stat-value{font-size:26px;font-weight:800;margin-bottom:4px;}
        .stat-label{font-size:12px;color:var(--muted);}

        /* TABLE */
        .table-wrap{overflow-x:auto;}
        table{width:100%;border-collapse:collapse;}
        th{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;padding:10px 12px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap;}
        td{padding:12px;font-size:13px;border-bottom:1px solid rgba(51,65,85,0.5);}
        tr:last-child td{border-bottom:none;}
        tr:hover td{background:rgba(255,255,255,0.02);}

        /* BADGE */
        .badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
        .badge-green{background:rgba(34,197,94,0.15);color:var(--green);}
        .badge-red{background:rgba(239,68,68,0.15);color:var(--red);}
        .badge-gold{background:rgba(245,158,11,0.15);color:var(--gold);}
        .badge-purple{background:rgba(124,58,237,0.15);color:var(--primary);}
        .badge-muted{background:rgba(148,163,184,0.15);color:var(--muted);}

        /* BUTTONS */
        .btn{border:none;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;}
        .btn-sm{padding:5px 10px;font-size:11px;}
        .btn-primary{background:var(--primary);color:#fff;}
        .btn-green{background:var(--green);color:#fff;}
        .btn-red{background:var(--red);color:#fff;}
        .btn-outline{background:transparent;border:1px solid var(--border);color:var(--text);}
        .btn:hover{opacity:0.85;}
        .btn:disabled{opacity:0.5;cursor:not-allowed;}

        /* MODAL */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:200;display:none;align-items:center;justify-content:center;}
        .modal-overlay.open{display:flex;}
        .modal{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;}
        .modal-title{font-size:16px;font-weight:700;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;}
        .modal-close{background:none;border:none;color:var(--muted);font-size:18px;cursor:pointer;}
        .form-group{margin-bottom:14px;}
        .form-label{font-size:12px;color:var(--muted);margin-bottom:5px;display:block;}
        .form-control{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:var(--text);font-size:13px;outline:none;}
        .form-control:focus{border-color:var(--primary);}

        /* TOAST */
        .toast-container{position:fixed;top:70px;right:20px;z-index:999;display:flex;flex-direction:column;gap:8px;}
        .toast{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:12px 16px;font-size:13px;min-width:220px;animation:slideIn 0.3s ease;}
        .toast.success{border-left:3px solid var(--green);}
        .toast.error{border-left:3px solid var(--red);}
        @keyframes slideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}

        /* MOBILE */
        .menu-toggle{display:none;background:none;border:none;color:var(--text);font-size:20px;cursor:pointer;}
        @media(max-width:768px){
            .sidebar{transform:translateX(-100%);}
            .sidebar.open{transform:translateX(0);}
            .main{margin-left:0;}
            .stat-grid{grid-template-columns:1fr 1fr;}
            .menu-toggle{display:block;}
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <h2>🛡️ ColorWin</h2>
        <p>Admin Panel</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section">Main</div>
        <a href="/admin/dashboard" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i> Dashboard
        </a>

        <div class="nav-section">Users</div>
        <a href="/admin/users" class="nav-item {{ request()->is('admin/users') ? 'active' : '' }}">
            <i class="fas fa-users"></i> All Users
        </a>

        <div class="nav-section">Finance</div>
        <a href="/admin/deposits" class="nav-item {{ request()->is('admin/deposits') ? 'active' : '' }}">
            <i class="fas fa-arrow-down-to-line"></i> Deposits
            <span id="pendingDepositsBadge" style="margin-left:auto;background:var(--red);color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;display:none;"></span>
        </a>
        <a href="/admin/withdrawals" class="nav-item {{ request()->is('admin/withdrawals') ? 'active' : '' }}">
            <i class="fas fa-arrow-up-from-bracket"></i> Withdrawals
            <span id="pendingWdBadge" style="margin-left:auto;background:var(--red);color:#fff;border-radius:10px;padding:1px 7px;font-size:10px;display:none;"></span>
        </a>

        <div class="nav-section">Game</div>
        <a href="/admin/game" class="nav-item {{ request()->is('admin/game') ? 'active' : '' }}">
            <i class="fas fa-gamepad"></i> Game Control
        </a>

        <div class="nav-section">System</div>
        <a href="/admin/settings" class="nav-item {{ request()->is('admin/settings') ? 'active' : '' }}">
            <i class="fas fa-gear"></i> Settings
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar" id="adminAvatar">A</div>
            <div>
                <div class="admin-name" id="adminName">Admin</div>
                <div class="admin-role">Super Admin</div>
            </div>
        </div>
        <button class="logout-btn" onclick="adminLogout()">
            <i class="fas fa-sign-out-alt"></i> Logout
        </button>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <div class="topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">
                <i class="fas fa-bars"></i>
            </button>
            <span class="topbar-title">@yield('title', 'Dashboard')</span>
        </div>
        <div class="topbar-right">
            <span style="font-size:12px;color:var(--muted);" id="topbarTime"></span>
            <a href="/game" target="_blank" style="color:var(--muted);font-size:13px;text-decoration:none;">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </div>
    </div>
    <div class="page-content">
        @yield('content')
    </div>
</div>

<script>
const ADMIN_TOKEN = localStorage.getItem('admin_token');
if (!ADMIN_TOKEN) window.location.href = '/admin/login';

const adminUser = JSON.parse(localStorage.getItem('admin_user') || '{}');
if (adminUser.name) {
    document.getElementById('adminName').textContent = adminUser.name;
    document.getElementById('adminAvatar').textContent = adminUser.name[0].toUpperCase();
}

const AAPI = (path, opts={}) => fetch('/api/admin' + path, {
    headers: { 'Authorization': 'Bearer ' + ADMIN_TOKEN, 'Content-Type': 'application/json', 'Accept': 'application/json' },
    ...opts
}).then(r => r.json());

function showToast(msg, type='success') {
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.textContent = msg;
    document.getElementById('toastContainer').appendChild(t);
    setTimeout(() => t.remove(), 3500);
}

function adminLogout() {
    if (!confirm('Logout from admin panel?')) return;
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    window.location.href = '/admin/login';
}

// Live clock
setInterval(() => {
    document.getElementById('topbarTime').textContent = new Date().toLocaleTimeString('en-IN');
}, 1000);

// Load pending counts for sidebar badges
async function loadPendingCounts() {
    try {
        const [deps, wds] = await Promise.all([
            AAPI('/deposits?status=pending'),
            AAPI('/withdrawals?status=pending'),
        ]);
        const dc = deps.total || 0;
        const wc = wds.total || 0;
        const db = document.getElementById('pendingDepositsBadge');
        const wb = document.getElementById('pendingWdBadge');
        if (dc > 0) { db.textContent = dc; db.style.display = 'inline'; }
        if (wc > 0) { wb.textContent = wc; wb.style.display = 'inline'; }
    } catch(e) {}
}
loadPendingCounts();
</script>
@stack('scripts')
</body>
</html>
