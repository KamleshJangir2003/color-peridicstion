@extends('admin.layout')
@section('title', 'Live Bets')

@push('styles')
<style>
    .live-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px;}
    .live-badge{display:flex;align-items:center;gap:6px;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);border-radius:20px;padding:5px 12px;font-size:12px;color:var(--red);font-weight:600;}
    .live-dot{width:8px;height:8px;background:var(--red);border-radius:50%;animation:blink 1s infinite;}
    @keyframes blink{0%,100%{opacity:1}50%{opacity:.2}}
    .round-info{font-size:13px;color:var(--muted);}
    .round-info span{color:var(--text);font-weight:700;}
    .refresh-bar{height:3px;background:var(--border);border-radius:2px;overflow:hidden;margin-bottom:14px;}
    .refresh-progress{height:3px;background:var(--primary);border-radius:2px;width:100%;}

    /* Summary chips */
    .summary-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px;}
    .chip{border-radius:10px;padding:8px 14px;font-size:12px;font-weight:700;border:1px solid transparent;cursor:pointer;transition:all .2s;text-align:center;min-width:80px;}
    .chip.active{box-shadow:0 0 0 2px #fff;}
    .chip-red{background:rgba(239,68,68,.15);color:var(--red);border-color:rgba(239,68,68,.3);}
    .chip-green{background:rgba(34,197,94,.15);color:var(--green);border-color:rgba(34,197,94,.3);}
    .chip-violet{background:rgba(168,85,247,.15);color:var(--violet);border-color:rgba(168,85,247,.3);}
    .chip-num{background:rgba(148,163,184,.1);color:var(--text);border-color:var(--border);}
    .chip-all{background:rgba(124,58,237,.15);color:var(--primary);border-color:rgba(124,58,237,.3);}
    .chip-val{font-size:16px;display:block;}
    .chip-sub{font-size:10px;color:inherit;opacity:.8;}

    /* Stats bar */
    .stats-bar{display:flex;gap:14px;flex-wrap:wrap;background:var(--card);border:1px solid var(--border);border-radius:10px;padding:12px 16px;margin-bottom:14px;}
    .stat-item{font-size:11px;color:var(--muted);}
    .stat-item b{display:block;font-size:15px;color:var(--text);font-weight:700;}

    /* Table */
    .bet-value-pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;}
    .pill-red{background:rgba(239,68,68,.15);color:var(--red);}
    .pill-green{background:rgba(34,197,94,.15);color:var(--green);}
    .pill-violet{background:rgba(168,85,247,.15);color:var(--violet);}
    .pill-num{background:rgba(148,163,184,.12);color:var(--text);}
    .user-name{font-weight:600;font-size:13px;}
    .user-phone{font-size:11px;color:var(--muted);}
    .no-round{text-align:center;padding:50px 20px;color:var(--muted);}
    .no-round i{font-size:36px;margin-bottom:10px;display:block;}
    .search-box{background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:8px 12px;color:var(--text);font-size:13px;outline:none;width:100%;max-width:260px;}
    .search-box:focus{border-color:var(--primary);}
    .table-header{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;margin-bottom:10px;}
</style>
@endpush

@section('content')

<div class="live-header">
    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
        <div class="live-badge"><div class="live-dot"></div> LIVE</div>
        <div class="round-info">Round: <span id="roundId">—</span></div>
        <div class="round-info">Ends: <span id="endsAt">—</span></div>
    </div>
    <button class="btn btn-outline btn-sm" onclick="startRefreshCycle()">
        <i class="fas fa-rotate-right"></i> Refresh
    </button>
</div>

<div class="refresh-bar"><div class="refresh-progress" id="refreshBar"></div></div>

<!-- Stats -->
<div class="stats-bar" id="statsBar" style="display:none;">
    <div class="stat-item">Total Users <b id="statUsers">0</b></div>
    <div class="stat-item">Total Bets <b id="statBets">0</b></div>
    <div class="stat-item">Total Amount <b id="statAmount">₹0</b></div>
    <div class="stat-item">Highest On <b id="statHighest">—</b></div>
</div>

<!-- Summary chips (filter) -->
<div class="summary-row" id="summaryRow"></div>

<!-- Bets Table -->
<div class="card">
    <div class="table-header">
        <div style="font-size:13px;font-weight:700;">
            <i class="fas fa-list" style="color:var(--primary);margin-right:6px;"></i>
            All Bets <span id="betCountLabel" style="color:var(--muted);font-weight:400;font-size:12px;"></span>
        </div>
        <input class="search-box" id="searchBox" placeholder="Search user name / phone..." oninput="renderTable()">
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Bet On</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="betsBody">
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
let allBets = [];
let activeFilter = 'all';

const BET_COLORS = {
    red: 'pill-red', green: 'pill-green', violet: 'pill-violet',
};
const COLOR_MAP = {0:'violet',1:'green',2:'red',3:'green',4:'red',5:'violet',6:'red',7:'green',8:'red',9:'green'};

function pillClass(v) { return BET_COLORS[v] || 'pill-num'; }
function formatAmt(n) { return '₹' + Number(n).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2}); }
function chipClass(v) {
    if (v === 'red') return 'chip-red';
    if (v === 'green') return 'chip-green';
    if (v === 'violet') return 'chip-violet';
    return 'chip-num';
}

function renderTable() {
    const q = document.getElementById('searchBox').value.toLowerCase();
    const filtered = allBets.filter(b => {
        const matchFilter = activeFilter === 'all' || b.bet_value === activeFilter;
        const matchSearch = !q || b.user.toLowerCase().includes(q) || b.phone.includes(q);
        return matchFilter && matchSearch;
    });

    document.getElementById('betCountLabel').textContent = `(${filtered.length})`;

    if (!filtered.length) {
        document.getElementById('betsBody').innerHTML =
            `<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px;">Koi bet nahi mili.</td></tr>`;
        return;
    }

    document.getElementById('betsBody').innerHTML = filtered.map((b, i) => `
        <tr>
            <td style="color:var(--muted);font-size:12px;">${i + 1}</td>
            <td>
                <div class="user-name">${b.user}</div>
                <div class="user-phone">${b.phone}</div>
            </td>
            <td>
                <span class="bet-value-pill ${pillClass(b.bet_value)}">${b.bet_value.toUpperCase()}</span>
                ${b.bet_type === 'number' ? `<span class="bet-value-pill pill-${COLOR_MAP[+b.bet_value]}" style="margin-left:4px;">${COLOR_MAP[+b.bet_value].toUpperCase()}</span>` : ''}
            </td>
            <td><span style="font-size:11px;color:var(--muted);">${b.bet_type === 'color' ? '🎨 Color' : '🔢 Number'}</span></td>
            <td style="font-weight:700;color:var(--gold);">${formatAmt(b.amount)}</td>
            <td style="color:var(--muted);font-size:12px;">${b.placed_at}</td>
        </tr>
    `).join('');
}

function setFilter(val) {
    activeFilter = val;
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('active'));
    document.getElementById('chip-' + val)?.classList.add('active');
    renderTable();
}

async function loadBets() {
    try {
        const data = await AAPI('/game/live-bets');

        if (!data.round) {
            document.getElementById('roundId').textContent = '—';
            document.getElementById('endsAt').textContent = '—';
            document.getElementById('statsBar').style.display = 'none';
            document.getElementById('summaryRow').innerHTML = '';
            allBets = [];
            document.getElementById('betsBody').innerHTML =
                `<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:40px;"><i class="fas fa-moon" style="font-size:28px;display:block;margin-bottom:8px;"></i>Koi active round nahi hai abhi.</td></tr>`;
            return;
        }

        document.getElementById('roundId').textContent = '#' + data.round;
        document.getElementById('endsAt').textContent = data.ends_at
            ? new Date(data.ends_at).toLocaleTimeString('en-IN') : '—';

        allBets = data.bets || [];
        const summary = data.summary || [];
        const maxAmt = summary.length ? Math.max(...summary.map(s => +s.total_amount)) : 1;

        // Stats bar
        document.getElementById('statsBar').style.display = 'flex';
        document.getElementById('statUsers').textContent = allBets.length;
        document.getElementById('statBets').textContent = allBets.length;
        document.getElementById('statAmount').textContent = formatAmt(data.total_amount || 0);
        document.getElementById('statHighest').textContent = summary.length ? summary[0].bet_value.toUpperCase() : '—';

        // Summary chips
        const chips = [`<div class="chip chip-all ${activeFilter==='all'?'active':''}" id="chip-all" onclick="setFilter('all')">
            <span class="chip-val">ALL</span>
            <span class="chip-sub">${allBets.length} bets</span>
        </div>`];

        summary.forEach(s => {
            const isTop = +s.total_amount === maxAmt;
            chips.push(`<div class="chip ${chipClass(s.bet_value)} ${activeFilter===s.bet_value?'active':''}" id="chip-${s.bet_value}" onclick="setFilter('${s.bet_value}')">
                ${isTop ? '👑 ' : ''}<span class="chip-val">${s.bet_value.toUpperCase()}</span>
                <span class="chip-sub">${s.bet_count} users · ${formatAmt(s.total_amount)}</span>
            </div>`);
        });

        document.getElementById('summaryRow').innerHTML = chips.join('');
        renderTable();

    } catch(e) {
        document.getElementById('betsBody').innerHTML =
            `<tr><td colspan="5" style="text-align:center;color:var(--red);padding:30px;">Load karne mein error aaya.</td></tr>`;
    }
}

function startRefreshCycle() {
    loadBets();
    const bar = document.getElementById('refreshBar');
    bar.style.transition = 'none';
    bar.style.width = '100%';
    setTimeout(() => {
        bar.style.transition = 'width 30s linear';
        bar.style.width = '0%';
    }, 50);
}

startRefreshCycle();
setInterval(startRefreshCycle, 30000);
</script>
@endpush
