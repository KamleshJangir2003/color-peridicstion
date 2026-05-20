@extends('layouts.app')
@section('title', 'Profile')

@push('styles')
<style>
.profile-header{background:linear-gradient(135deg,#1E293B,#0F172A);border:1px solid var(--border);border-radius:18px;padding:22px 16px;margin-bottom:14px;text-align:center;}
.avatar{width:72px;height:72px;background:linear-gradient(135deg,var(--primary),var(--gold));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;margin:0 auto 12px;color:#fff;}
.profile-name{font-size:20px;font-weight:800;}
.profile-phone{font-size:13px;color:var(--muted);margin-top:3px;}
.vip-badge{display:inline-block;background:linear-gradient(135deg,var(--gold),#F97316);color:#000;border-radius:20px;padding:4px 14px;font-size:12px;font-weight:800;margin-top:8px;}

.stats-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:14px;}
.stat-box{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:12px;text-align:center;}
.stat-box .sv{font-size:20px;font-weight:800;margin-bottom:2px;}
.stat-box .sl{font-size:10px;color:var(--muted);}

.bonus-card{background:linear-gradient(135deg,rgba(124,58,237,.2),rgba(168,85,247,.1));border:1px solid rgba(124,58,237,.4);border-radius:14px;padding:16px;margin-bottom:14px;text-align:center;}
.bonus-days{display:flex;justify-content:center;gap:6px;margin:12px 0;}
.day-dot{width:28px;height:28px;border-radius:50%;border:2px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:var(--muted);}
.day-dot.done{background:var(--primary);border-color:var(--primary);color:#fff;}
.day-dot.today{background:var(--gold);border-color:var(--gold);color:#000;animation:pulse .8s infinite;}
@keyframes pulse{0%,100%{transform:scale(1)}50%{transform:scale(1.1)}}

.ref-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:16px;margin-bottom:14px;}
.ref-code-box{background:var(--bg);border-radius:10px;padding:14px;text-align:center;margin:10px 0;}
.ref-code{font-size:26px;font-weight:900;color:var(--gold);letter-spacing:4px;font-family:monospace;}
.ref-btns{display:grid;grid-template-columns:1fr 1fr;gap:8px;}

.menu-list{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden;margin-bottom:14px;}
.menu-item{display:flex;align-items:center;gap:12px;padding:15px 16px;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text);transition:background .2s;}
.menu-item:last-child{border-bottom:none;}
.menu-item:hover{background:rgba(255,255,255,.03);}
.menu-item .icon{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.menu-item .label{font-size:14px;font-weight:600;flex:1;}
.menu-item .arrow{color:var(--muted);font-size:12px;}
</style>
@endpush

@section('content')

<!-- PROFILE HEADER -->
<div class="profile-header">
    <div class="avatar" id="avatarEl">?</div>
    <div class="profile-name" id="profileName">Loading...</div>
    <div class="profile-phone" id="profilePhone">--</div>
    <div class="vip-badge" id="vipBadge">⭐ VIP 0</div>
</div>

<!-- STATS -->
<div class="stats-grid">
    <div class="stat-box">
        <div class="sv" id="statTotalBets" style="color:var(--primary);">0</div>
        <div class="sl">Total Bets</div>
    </div>
    <div class="stat-box">
        <div class="sv" id="statWonBets" style="color:var(--green);">0</div>
        <div class="sl">Bets Won</div>
    </div>
    <div class="stat-box">
        <div class="sv" id="statCommission" style="color:var(--gold);">₹0</div>
        <div class="sl">Commission</div>
    </div>
</div>

<!-- WALLET SUMMARY -->
<div class="card" style="margin-bottom:14px;">
    <div style="font-size:13px;font-weight:700;margin-bottom:12px;">💰 Wallet Balance</div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
        <div style="text-align:center;background:var(--bg);border-radius:10px;padding:12px;">
            <div style="font-size:10px;color:var(--muted);margin-bottom:4px;">Main</div>
            <div style="font-size:16px;font-weight:800;color:var(--gold);" id="wMain">₹0</div>
        </div>
        <div style="text-align:center;background:var(--bg);border-radius:10px;padding:12px;">
            <div style="font-size:10px;color:var(--muted);margin-bottom:4px;">Winning</div>
            <div style="font-size:16px;font-weight:800;color:var(--green);" id="wWin">₹0</div>
        </div>
        <div style="text-align:center;background:var(--bg);border-radius:10px;padding:12px;">
            <div style="font-size:10px;color:var(--muted);margin-bottom:4px;">Bonus</div>
            <div style="font-size:16px;font-weight:800;color:var(--violet);" id="wBonus">₹0</div>
        </div>
    </div>
</div>

<!-- DAILY BONUS -->
<div class="bonus-card">
    <div style="font-size:15px;font-weight:700;margin-bottom:4px;">🎁 Daily Check-in Bonus</div>
    <div style="font-size:12px;color:var(--muted);">Claim every day. More consecutive days = more bonus!</div>
    <div class="bonus-days" id="bonusDays">
        @for($i=1;$i<=7;$i++)
        <div class="day-dot" id="day{{$i}}">{{$i}}</div>
        @endfor
    </div>
    <div style="font-size:13px;color:var(--muted);margin-bottom:12px;">
        Streak: <strong id="streakDays" style="color:var(--gold);">0</strong> days |
        Today's Bonus: <strong id="todayBonus" style="color:var(--green);">₹10</strong>
    </div>
    <button class="btn btn-primary" id="claimBtn" onclick="claimBonus()" style="border-radius:10px;">
        🎁 Claim Today's Bonus
    </button>
</div>

<!-- REFERRAL -->
<div class="ref-card">
    <div style="font-size:14px;font-weight:700;margin-bottom:4px;">👥 Refer & Earn</div>
    <div style="font-size:12px;color:var(--muted);margin-bottom:2px;">
        Earn <strong style="color:var(--green);">2%</strong> on L1 bets &
        <strong style="color:var(--gold);">1%</strong> on L2 bets
    </div>
    <div class="ref-code-box">
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Your Referral Code</div>
        <div class="ref-code" id="refCode">------</div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:12px;">
        <div style="background:var(--bg);border-radius:10px;padding:10px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:var(--primary);" id="refCount">0</div>
            <div style="font-size:10px;color:var(--muted);">Referrals</div>
        </div>
        <div style="background:var(--bg);border-radius:10px;padding:10px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:var(--green);" id="refEarned">₹0</div>
            <div style="font-size:10px;color:var(--muted);">Earned</div>
        </div>
        <div style="background:var(--bg);border-radius:10px;padding:10px;text-align:center;">
            <div style="font-size:18px;font-weight:800;color:var(--gold);">2%</div>
            <div style="font-size:10px;color:var(--muted);">Commission</div>
        </div>
    </div>
    <div class="ref-btns">
        <button class="btn btn-outline" onclick="copyRef()" style="padding:11px;">📋 Copy Code</button>
        <button class="btn btn-primary" onclick="shareRef()" style="padding:11px;">📤 Share Link</button>
    </div>
</div>

<!-- MENU -->
<div class="menu-list">
    <a href="/deposit" class="menu-item">
        <div class="icon" style="background:rgba(34,197,94,.15);">💰</div>
        <span class="label">Deposit Money</span>
        <i class="fas fa-chevron-right arrow"></i>
    </a>
    <a href="/withdraw" class="menu-item">
        <div class="icon" style="background:rgba(239,68,68,.15);">💸</div>
        <span class="label">Withdraw Money</span>
        <i class="fas fa-chevron-right arrow"></i>
    </a>
    <a href="/wallet" class="menu-item">
        <div class="icon" style="background:rgba(124,58,237,.15);">📋</div>
        <span class="label">Transaction History</span>
        <i class="fas fa-chevron-right arrow"></i>
    </a>
    <a href="#" class="menu-item" onclick="changeWithdrawalPwd()">
        <div class="icon" style="background:rgba(245,158,11,.15);">🔐</div>
        <span class="label">Withdrawal Password</span>
        <i class="fas fa-chevron-right arrow"></i>
    </a>
    <a href="#" class="menu-item" onclick="doLogout()" style="color:var(--red);">
        <div class="icon" style="background:rgba(239,68,68,.15);">🚪</div>
        <span class="label" style="color:var(--red);">Logout</span>
        <i class="fas fa-chevron-right arrow"></i>
    </a>
</div>

<!-- VERSION -->
<div style="text-align:center;color:var(--muted);font-size:11px;padding-bottom:10px;">
    ColorWin v1.0 | 🎮 Play Responsibly
</div>

@endsection

@push('scripts')
<script>
if (!localStorage.getItem('token')) window.location.href = '/login';
const API = (path, opts={}) => fetch('/api' + path, {
    headers: {'Authorization':'Bearer '+localStorage.getItem('token'),'Content-Type':'application/json','Accept':'application/json'},
    ...opts
}).then(r => r.json());

async function loadProfile() {
    const d = await API('/profile');
    if (!d.id) return;

    // Header
    const name = d.name || 'User';
    document.getElementById('avatarEl').textContent    = name[0].toUpperCase();
    document.getElementById('profileName').textContent = name;
    document.getElementById('profilePhone').textContent= d.phone || d.email || '--';
    document.getElementById('vipBadge').textContent    = '⭐ VIP ' + (d.vip_level || 0);

    // Stats
    document.getElementById('statTotalBets').textContent  = d.bets_count || 0;
    document.getElementById('statWonBets').textContent    = d.bets_won || 0;
    document.getElementById('statCommission').textContent = '₹' + parseFloat(d.commission_total || 0).toFixed(0);

    // Referral
    document.getElementById('refCode').textContent  = d.referral_code || '------';
    document.getElementById('refCount').textContent = d.referrals_count || 0;
    document.getElementById('refEarned').textContent= '₹' + parseFloat(d.commission_total || 0).toFixed(0);
}

async function loadWallet() {
    const d = await API('/wallet/balance');
    document.getElementById('wMain').textContent  = '₹' + parseFloat(d.main||0).toFixed(2);
    document.getElementById('wWin').textContent   = '₹' + parseFloat(d.winning||0).toFixed(2);
    document.getElementById('wBonus').textContent = '₹' + parseFloat(d.bonus||0).toFixed(2);
}

async function loadBonusStreak() {
    const d = await API('/bonus/history');
    const bonuses = d.data || [];
    const today = new Date().toISOString().split('T')[0];
    const todayBonus = bonuses.find(b => b.bonus_date === today);
    const streak = bonuses.length > 0 ? bonuses[0].consecutive_days : 0;

    document.getElementById('streakDays').textContent = streak;
    const baseBonus = 10;
    document.getElementById('todayBonus').textContent = '₹' + (baseBonus * Math.min(streak + 1, 7));

    // Highlight days
    for (let i = 1; i <= 7; i++) {
        const el = document.getElementById('day' + i);
        if (i < streak) el.className = 'day-dot done';
        else if (i === streak + 1 && !todayBonus) el.className = 'day-dot today';
    }

    if (todayBonus) {
        const btn = document.getElementById('claimBtn');
        btn.textContent = '✅ Claimed Today (₹' + todayBonus.amount + ')';
        btn.disabled = true;
        btn.style.background = 'rgba(34,197,94,.2)';
        btn.style.color = 'var(--green)';
        btn.style.border = '1px solid var(--green)';
    }
}

async function claimBonus() {
    const btn = document.getElementById('claimBtn');
    btn.disabled = true; btn.textContent = 'Claiming...';
    const d = await API('/bonus/daily', { method: 'POST' });
    if (d.success) {
        showToast('🎁 Bonus claimed! +₹' + d.amount + ' (Day ' + d.consecutive_days + ')', 'success');
        loadWallet(); loadBonusStreak();
    } else {
        showToast(d.message || 'Already claimed today', 'error');
        btn.disabled = false;
        btn.textContent = '🎁 Claim Today\'s Bonus';
    }
}

function copyRef() {
    const code = document.getElementById('refCode').textContent;
    navigator.clipboard.writeText(code);
    showToast('Referral code copied!', 'success');
}

function shareRef() {
    const code = document.getElementById('refCode').textContent;
    const url  = window.location.origin + '/login?ref=' + code;
    if (navigator.share) {
        navigator.share({ title: 'Join ColorWin!', text: 'Use my code ' + code + ' and win big!', url });
    } else {
        navigator.clipboard.writeText(url);
        showToast('Referral link copied!', 'success');
    }
}

function changeWithdrawalPwd() {
    const pwd = prompt('Enter new withdrawal password (4-6 digits):');
    if (!pwd) return;
    showToast('Feature coming soon!', 'error');
}

function doLogout() {
    fetch('/api/logout', {
        method: 'POST',
        headers: {'Authorization':'Bearer '+localStorage.getItem('token'),'Accept':'application/json'}
    }).finally(() => {
        localStorage.removeItem('token');
        window.location.href = '/login';
    });
}

loadProfile();
loadWallet();
loadBonusStreak();
</script>
@endpush
