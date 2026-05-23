<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>@yield('title', 'Admin') - ColorWin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        :root{
            --bg:#0F172A;--sidebar:#111827;--card:#1E293B;--border:#334155;
            --primary:#7C3AED;--green:#22C55E;--red:#EF4444;--gold:#F59E0B;
            --violet:#A855F7;--text:#E2E8F0;--muted:#94A3B8;
        }
        body{background:var(--bg);color:var(--text);font-family:'Segoe UI',sans-serif;display:flex;min-height:100vh;overflow-x:hidden;}

        /* ── SIDEBAR ── */
        .sidebar{width:240px;background:var(--sidebar);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:300;transition:transform .3s ease;}
        .sidebar-logo{padding:18px 16px;border-bottom:1px solid var(--border);flex-shrink:0;}
        .sidebar-logo h2{font-size:18px;font-weight:800;background:linear-gradient(135deg,var(--primary),var(--gold));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}
        .sidebar-logo p{font-size:11px;color:var(--muted);margin-top:2px;}
        .sidebar-nav{flex:1;padding:10px 0;overflow-y:auto;}
        .nav-section{padding:8px 16px 4px;font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;}
        .nav-item{display:flex;align-items:center;gap:10px;padding:11px 16px;color:var(--muted);text-decoration:none;font-size:13px;font-weight:500;transition:all .2s;border-left:3px solid transparent;}
        .nav-item:hover{color:var(--text);background:rgba(255,255,255,.04);}
        .nav-item.active{color:var(--primary);background:rgba(124,58,237,.1);border-left-color:var(--primary);}
        .nav-item i{width:18px;text-align:center;font-size:14px;}
        .sidebar-footer{padding:14px;border-top:1px solid var(--border);flex-shrink:0;}
        .admin-info{display:flex;align-items:center;gap:10px;margin-bottom:10px;}
        .admin-avatar{width:36px;height:36px;background:linear-gradient(135deg,var(--primary),var(--gold));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;flex-shrink:0;}
        .admin-name{font-size:13px;font-weight:600;}
        .admin-role{font-size:11px;color:var(--muted);}
        .logout-btn{width:100%;background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);border-radius:8px;padding:9px;color:var(--red);font-size:13px;cursor:pointer;transition:all .2s;}
        .logout-btn:hover{background:rgba(239,68,68,.2);}

        /* ── OVERLAY ── */
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:299;backdrop-filter:blur(2px);}
        .sidebar-overlay.open{display:block;}

        /* ── MAIN ── */
        .main{margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh;min-width:0;}
        .topbar{background:var(--card);border-bottom:1px solid var(--border);padding:0 20px;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;}
        .topbar-left{display:flex;align-items:center;gap:12px;}
        .topbar-title{font-size:15px;font-weight:700;}
        .topbar-right{display:flex;align-items:center;gap:10px;}
        .menu-toggle{display:none;background:none;border:none;color:var(--text);font-size:20px;cursor:pointer;padding:4px;}
        .page-content{padding:20px;flex:1;}

        /* ── STAT GRID ── */
        .stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;}
        .stat-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;position:relative;overflow:hidden;}
        .stat-card::after{content:'';position:absolute;top:-20px;right:-20px;width:80px;height:80px;border-radius:50%;opacity:.1;}
        .stat-card.purple::after{background:var(--primary);}
        .stat-card.green::after{background:var(--green);}
        .stat-card.red::after{background:var(--red);}
        .stat-card.gold::after{background:var(--gold);}
        .stat-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;margin-bottom:10px;}
        .stat-icon.purple{background:rgba(124,58,237,.15);}
        .stat-icon.green{background:rgba(34,197,94,.15);}
        .stat-icon.red{background:rgba(239,68,68,.15);}
        .stat-icon.gold{background:rgba(245,158,11,.15);}
        .stat-value{font-size:24px;font-weight:800;margin-bottom:3px;}
        .stat-label{font-size:12px;color:var(--muted);}

        /* ── CARD ── */
        .card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:18px;}

        /* ── TABLE ── */
        .table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
        table{width:100%;border-collapse:collapse;}
        th{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;padding:10px 12px;text-align:left;border-bottom:1px solid var(--border);white-space:nowrap;}
        td{padding:11px 12px;font-size:13px;border-bottom:1px solid rgba(51,65,85,.5);}
        tr:last-child td{border-bottom:none;}
        tr:hover td{background:rgba(255,255,255,.02);}

        /* ── BADGE ── */
        .badge{display:inline-block;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
        .badge-green{background:rgba(34,197,94,.15);color:var(--green);}
        .badge-red{background:rgba(239,68,68,.15);color:var(--red);}
        .badge-gold{background:rgba(245,158,11,.15);color:var(--gold);}
        .badge-purple{background:rgba(124,58,237,.15);color:var(--primary);}
        .badge-muted{background:rgba(148,163,184,.15);color:var(--muted);}

        /* ── BUTTONS ── */
        .btn{border:none;border-radius:8px;padding:8px 14px;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;}
        .btn-sm{padding:5px 10px;font-size:11px;}
        .btn-primary{background:var(--primary);color:#fff;}
        .btn-green{background:var(--green);color:#fff;}
        .btn-red{background:var(--red);color:#fff;}
        .btn-outline{background:transparent;border:1px solid var(--border);color:var(--text);}
        .btn:hover{opacity:.85;}
        .btn:disabled{opacity:.5;cursor:not-allowed;}

        /* ── MODAL ── */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:400;display:none;align-items:flex-end;justify-content:center;}
        .modal-overlay.open{display:flex;}
        .modal{background:var(--card);border:1px solid var(--border);border-radius:16px 16px 0 0;padding:20px;width:100%;max-width:520px;max-height:92vh;overflow-y:auto;}
        .modal-title{font-size:15px;font-weight:700;margin-bottom:14px;display:flex;justify-content:space-between;align-items:center;}
        .modal-close{background:none;border:none;color:var(--muted);font-size:20px;cursor:pointer;line-height:1;}
        .form-group{margin-bottom:13px;}
        .form-label{font-size:12px;color:var(--muted);margin-bottom:5px;display:block;}
        .form-control{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:10px 12px;color:var(--text);font-size:14px;outline:none;}
        .form-control:focus{border-color:var(--primary);}

        /* ── TOAST ── */
        .toast-container{position:fixed;top:64px;right:12px;z-index:999;display:flex;flex-direction:column;gap:8px;max-width:calc(100vw - 24px);}
        .toast{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:11px 14px;font-size:13px;min-width:200px;animation:slideIn .3s ease;}
        .toast.success{border-left:3px solid var(--green);}
        .toast.error{border-left:3px solid var(--red);}
        @keyframes slideIn{from{transform:translateX(110%);opacity:0}to{transform:translateX(0);opacity:1}}

        /* ── RESPONSIVE GRID HELPERS ── */
        .g2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .g3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}

        /* ── FILTER TABS ── */
        .filter-tabs{display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;}
        .filter-tabs .btn{flex:1;min-width:80px;}

        /* ════════════════════════════
           MOBILE  ≤ 768px
        ════════════════════════════ */
        @media(max-width:768px){
            .menu-toggle{display:block;}
            .sidebar{transform:translateX(-100%);}
            .sidebar.open{transform:translateX(0);}
            .main{margin-left:0;}

            /* topbar */
            .topbar{padding:0 12px;height:52px;}
            .topbar-title{font-size:14px;}
            #topbarTime{display:none;}

            /* page */
            .page-content{padding:10px;}

            /* stat grid → 2 col */
            .stat-grid{grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;}
            .stat-card{padding:12px;}
            .stat-icon{width:34px;height:34px;font-size:15px;margin-bottom:7px;}
            .stat-value{font-size:18px;}
            .stat-label{font-size:11px;}

            /* cards */
            .card{padding:12px;border-radius:12px;}

            /* grids → single col */
            .g2,.g3{grid-template-columns:1fr;}

            /* tables */
            table{font-size:12px;}
            th{padding:8px 8px;font-size:10px;}
            td{padding:9px 8px;}

            /* buttons */
            .btn-sm{padding:5px 8px;font-size:11px;}

            /* modal → bottom sheet */
            .modal{border-radius:16px 16px 0 0;padding:16px;}

            /* filter tabs */
            .filter-tabs .btn{font-size:11px;padding:7px 6px;}
        }

        /* ════════════════════════════
           SMALL PHONES  ≤ 420px
        ════════════════════════════ */
        @media(max-width:420px){
            .stat-grid{grid-template-columns:1fr 1fr;gap:8px;}
            .stat-value{font-size:16px;}
            .stat-card{padding:10px;}
            .page-content{padding:8px;}
            .card{padding:10px;}
            th{font-size:9px;padding:7px 5px;}
            td{font-size:11px;padding:8px 5px;}
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="toast-container" id="toastContainer"></div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="/colorlogo-removebg-preview.png" alt="ColorWin" style="height:48px;object-fit:contain;display:block;margin-bottom:4px;">
        <p style="font-size:11px;color:var(--muted);">Admin Panel</p>
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
        <div class="topbar-left">
            <button class="menu-toggle" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
        </div>
        <div class="topbar-right">
            <span style="font-size:12px;color:var(--muted);" id="topbarTime"></span>
            <img src="/colorlogo-removebg-preview.png" alt="ColorWin" style="height:32px;object-fit:contain;">
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
    headers: {'Authorization':'Bearer '+ADMIN_TOKEN,'Content-Type':'application/json','Accept':'application/json'},
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
    localStorage.removeItem('admin_token');
    localStorage.removeItem('admin_user');
    window.location.href = '/admin/login';
}

setInterval(() => {
    document.getElementById('topbarTime').textContent = new Date().toLocaleTimeString('en-IN');
}, 1000);

async function loadPendingCounts() {
    try {
        const [deps, wds] = await Promise.all([
            AAPI('/deposits?status=pending'),
            AAPI('/withdrawals?status=pending'),
        ]);
        const dc = deps.total || 0, wc = wds.total || 0;
        const db = document.getElementById('pendingDepositsBadge');
        const wb = document.getElementById('pendingWdBadge');
        if (dc > 0) { db.textContent = dc; db.style.display = 'inline'; }
        if (wc > 0) { wb.textContent = wc; wb.style.display = 'inline'; }
    } catch(e) {}
}
loadPendingCounts();

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
}
document.querySelectorAll('.nav-item').forEach(el => el.addEventListener('click', closeSidebar));
</script>
@stack('scripts')
</body>
</html>
