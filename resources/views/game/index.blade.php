@extends('layouts.app')
@section('title', 'Color Prediction Game')

@push('styles')
<style>
/* WALLET STRIP */
.wallet-strip{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-bottom:14px;}
.wallet-item{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:12px 10px;text-align:center;}
.wallet-item .lbl{font-size:10px;color:var(--muted);margin-bottom:3px;}
.wallet-item .amt{font-size:15px;font-weight:800;color:var(--gold);}

/* TIMER */
.timer-card{background:linear-gradient(135deg,#1E293B,#0F172A);border:1px solid var(--border);border-radius:16px;padding:18px 16px;text-align:center;margin-bottom:14px;position:relative;overflow:hidden;}
.timer-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--primary),var(--gold));}
.round-info{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
.round-id-txt{font-size:12px;color:var(--muted);}
.round-id-txt span{color:var(--gold);font-weight:700;font-family:monospace;}
.timer-lbl{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;}
.timer-num{font-size:56px;font-weight:900;line-height:1;margin-bottom:8px;font-variant-numeric:tabular-nums;}
.timer-num.safe{color:#22C55E;}
.timer-num.warn{color:#F59E0B;}
.timer-num.danger{color:#EF4444;animation:blink .5s infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.4}}
.timer-bar-bg{background:var(--border);border-radius:4px;height:5px;overflow:hidden;}
.timer-bar-fill{height:100%;border-radius:4px;background:linear-gradient(90deg,var(--primary),var(--gold));transition:width 1s linear;}

/* LAST RESULTS */
.results-row{display:flex;gap:6px;overflow-x:auto;padding-bottom:4px;scrollbar-width:none;}
.results-row::-webkit-scrollbar{display:none;}
.rdot{min-width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0;cursor:pointer;}
.rdot.green{background:#22C55E;color:#fff;}
.rdot.red{background:#EF4444;color:#fff;}
.rdot.violet{background:#A855F7;color:#fff;}

/* COLOR BUTTONS */
.color-btns{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:12px;}
.cbtn{border:none;border-radius:12px;padding:16px 8px;font-size:14px;font-weight:700;cursor:pointer;display:flex;flex-direction:column;align-items:center;gap:3px;transition:all .15s;}
.cbtn span{font-size:10px;font-weight:500;opacity:.85;}
.cbtn.green{background:#22C55E;color:#fff;}
.cbtn.red{background:#EF4444;color:#fff;}
.cbtn.violet{background:#A855F7;color:#fff;}
.cbtn:hover{transform:scale(1.04);filter:brightness(1.1);}
.cbtn.sel{box-shadow:0 0 0 3px #fff,0 0 0 5px rgba(255,255,255,.3);transform:scale(1.04);}
.cbtn:disabled{opacity:.4;cursor:not-allowed;transform:none;}

/* NUMBER BUTTONS */
.nbtn-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:12px;}
.nbtn{border:2px solid transparent;border-radius:10px;padding:13px 4px;font-size:16px;font-weight:800;cursor:pointer;text-align:center;transition:all .15s;}
.nbtn.green{background:rgba(34,197,94,.15);color:#22C55E;border-color:#22C55E;}
.nbtn.red{background:rgba(239,68,68,.15);color:#EF4444;border-color:#EF4444;}
.nbtn.violet{background:rgba(168,85,247,.15);color:#A855F7;border-color:#A855F7;}
.nbtn:hover{transform:scale(1.06);filter:brightness(1.2);}
.nbtn.sel{box-shadow:0 0 0 2px #fff;transform:scale(1.06);}
.nbtn:disabled{opacity:.4;cursor:not-allowed;transform:none;}

/* AMOUNT CHIPS */
.chips{display:flex;gap:7px;flex-wrap:wrap;margin-bottom:10px;}
.chip{background:var(--bg);border:1px solid var(--border);border-radius:20px;padding:6px 14px;font-size:13px;font-weight:600;cursor:pointer;color:var(--text);transition:all .15s;}
.chip:hover,.chip.sel{background:var(--primary);border-color:var(--primary);color:#fff;}

/* BET SUMMARY */
.bet-summary{background:var(--bg);border-radius:10px;padding:12px;margin-bottom:12px;}
.bet-summary-row{display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;}
.bet-summary-row:last-child{margin-bottom:0;}

/* PLACE BET BTN */
.place-btn{width:100%;border:none;border-radius:12px;padding:15px;font-size:16px;font-weight:800;cursor:pointer;background:linear-gradient(135deg,var(--primary),#9D5CF6);color:#fff;transition:all .2s;letter-spacing:.5px;}
.place-btn:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(124,58,237,.4);}
.place-btn:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none;}

/* BETTING CLOSED */
.bet-area{position:relative;}
.bet-closed{position:absolute;inset:0;background:rgba(15,23,42,.88);border-radius:16px;display:none;align-items:center;justify-content:center;flex-direction:column;gap:8px;z-index:10;font-size:15px;font-weight:700;color:var(--red);}

/* RESULT POPUP */
.result-overlay{position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:200;display:none;align-items:center;justify-content:center;}
.result-popup{background:var(--card);border-radius:24px;padding:32px 24px;text-align:center;width:290px;animation:popIn .4s cubic-bezier(.175,.885,.32,1.275);}
@keyframes popIn{from{transform:scale(.5);opacity:0}to{transform:scale(1);opacity:1}}
.result-circle{width:90px;height:90px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:900;margin:0 auto 16px;}

/* HISTORY TABLE */
.htable{width:100%;border-collapse:collapse;}
.htable th{font-size:11px;color:var(--muted);text-transform:uppercase;padding:8px 6px;text-align:left;border-bottom:1px solid var(--border);}
.htable td{padding:10px 6px;font-size:13px;border-bottom:1px solid rgba(51,65,85,.5);}
.htable tr:last-child td{border-bottom:none;}

/* SECTION TITLE */
.sec-title{font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;}
</style>
@endpush

@section('content')

<!-- WALLET STRIP -->
<div class="wallet-strip">
    <div class="wallet-item">
        <div class="lbl">💰 Main</div>
        <div class="amt" id="mainBal">₹0</div>
    </div>
    <div class="wallet-item">
        <div class="lbl">🏆 Winning</div>
        <div class="amt" id="winBal" style="color:#22C55E;">₹0</div>
    </div>
    <div class="wallet-item">
        <div class="lbl">🎁 Bonus</div>
        <div class="amt" id="bonusBal" style="color:#A855F7;">₹0</div>
    </div>
</div>

<!-- TIMER CARD -->
<div class="timer-card">
    <div class="round-info">
        <div class="round-id-txt">Round: <span id="roundId">Loading...</span></div>
        <div style="font-size:11px;" id="roundStatusTxt">
            <span style="color:#22C55E;">● Live</span>
        </div>
    </div>
    <div class="timer-lbl">Time Remaining</div>
    <div class="timer-num safe" id="timerNum">30</div>
    <div class="timer-bar-bg">
        <div class="timer-bar-fill" id="timerBar" style="width:100%;"></div>
    </div>
</div>

<!-- RECENT RESULTS -->
<div class="card" style="padding:12px 14px;margin-bottom:14px;">
    <div class="sec-title" style="margin-bottom:8px;">📊 Recent Results</div>
    <div class="results-row" id="resultsRow">
        <span style="color:var(--muted);font-size:13px;">No results yet</span>
    </div>
</div>

<!-- BET AREA -->
<div class="card bet-area" id="betArea">
    <div class="bet-closed" id="betClosed">
        <span>⏳</span>
        <span>Betting Closed</span>
        <span style="font-size:12px;color:var(--muted);">Wait for next round...</span>
    </div>

    <!-- COLOR BETS -->
    <div class="sec-title">Bet on Color</div>
    <div class="color-btns">
        <button class="cbtn green" onclick="selectBet('color','green',this)">
            🟢 Green <span>1,3,7,9 → 2x</span>
        </button>
        <button class="cbtn violet" onclick="selectBet('color','violet',this)">
            🟣 Violet <span>0,5 → 2x</span>
        </button>
        <button class="cbtn red" onclick="selectBet('color','red',this)">
            🔴 Red <span>2,4,6,8 → 2x</span>
        </button>
    </div>

    <!-- NUMBER BETS -->
    <div class="sec-title">Bet on Number (9x)</div>
    <div class="nbtn-grid">
        @foreach([0=>'violet',1=>'green',2=>'red',3=>'green',4=>'red',5=>'violet',6=>'red',7=>'green',8=>'red',9=>'green'] as $num=>$color)
        <button class="nbtn {{$color}}" onclick="selectBet('number','{{$num}}',this)">{{$num}}</button>
        @endforeach
    </div>

    <!-- AMOUNT -->
    <div class="sec-title">Select Amount</div>
    <div class="chips">
        @foreach([10,50,100,500,1000] as $a)
        <div class="chip" onclick="selectAmt({{$a}},this)">₹{{$a}}</div>
        @endforeach
    </div>
    <div style="margin-bottom:12px;">
        <input type="number" id="betAmt" class="form-control" placeholder="Custom amount" min="10" value="10" oninput="updateSummary()">
    </div>

    <!-- SUMMARY -->
    <div class="bet-summary" id="betSummary" style="display:none;">
        <div class="bet-summary-row">
            <span style="color:var(--muted);">Bet on:</span>
            <span id="sumType" style="font-weight:700;"></span>
        </div>
        <div class="bet-summary-row">
            <span style="color:var(--muted);">Amount:</span>
            <span id="sumAmt" style="font-weight:700;color:var(--gold);"></span>
        </div>
        <div class="bet-summary-row">
            <span style="color:var(--muted);">Potential Win:</span>
            <span id="sumWin" style="font-weight:700;color:#22C55E;"></span>
        </div>
    </div>

    <button class="place-btn" id="placeBtn" onclick="placeBet()" disabled>🎯 Place Bet</button>
</div>

<!-- ROUND HISTORY -->
<div class="card" style="margin-top:14px;">
    <div class="sec-title">📋 Round History</div>
    <table class="htable">
        <thead><tr><th>Round</th><th>Result</th><th>My Bet</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody id="myBetsBody">
            <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">Loading...</td></tr>
        </tbody>
    </table>
</div>

<!-- RESULT POPUP -->
<div class="result-overlay" id="resultOverlay" onclick="closeResult()">
    <div class="result-popup" onclick="event.stopPropagation()">
        <div class="result-circle" id="resultCircle">0</div>
        <h2 id="resultTitle" style="font-size:22px;margin-bottom:6px;"></h2>
        <p id="resultSub" style="color:var(--muted);font-size:13px;margin-bottom:16px;"></p>
        <div id="resultWin" style="font-size:28px;font-weight:800;color:var(--gold);margin-bottom:16px;display:none;"></div>
        <button class="place-btn" onclick="closeResult()">Continue Playing</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
if (!localStorage.getItem('token')) window.location.href = '/login';

const API = (path, opts={}) => fetch('/api' + path, {
    headers: {'Authorization':'Bearer '+localStorage.getItem('token'),'Content-Type':'application/json','Accept':'application/json'},
    ...opts
}).then(async r => {
    const d = await r.json();
    if (d.message === 'Unauthenticated.') { localStorage.removeItem('token'); window.location.href='/login'; }
    return d;
});

let currentRound = null;
let timerInterval = null;
let selType = null, selVal = null;
let ROUND_SECS = 30; // updated from server on each round load

// ── WALLET ────────────────────────────────────────────────────
async function loadWallet() {
    const d = await API('/wallet/balance');
    document.getElementById('mainBal').textContent  = '₹' + parseFloat(d.main||0).toFixed(2);
    document.getElementById('winBal').textContent   = '₹' + parseFloat(d.winning||0).toFixed(2);
    document.getElementById('bonusBal').textContent = '₹' + parseFloat(d.bonus||0).toFixed(2);
    const nav = document.getElementById('navBalanceAmt');
    if (nav) nav.textContent = '₹' + parseFloat(d.total||0).toFixed(2);
}

// ── ROUND ─────────────────────────────────────────────────────
async function loadRound() {
    try {
        const d = await API('/game/round');
        const isNewRound = !currentRound || currentRound.id !== d.id;
        currentRound = d;

        // Calculate exact seconds left from server ends_at (avoids client drift)
        const secsLeft = d.ends_at
            ? Math.max(0, Math.round((new Date(d.ends_at) - Date.now()) / 1000))
            : d.seconds_left;

        if (isNewRound) {
            document.getElementById('roundId').textContent = d.round_id;
            document.getElementById('roundStatusTxt').innerHTML = '<span style="color:#22C55E;">● Live</span>';
            ROUND_SECS = Math.max(secsLeft, 1);
        }

        startTimer(secsLeft);
        if (secsLeft > 5) enableBetting();
        else disableBetting();
    } catch(e) {
        document.getElementById('roundId').textContent = 'Error - Refresh';
    }
}

// ── TIMER ─────────────────────────────────────────────────────
function startTimer(secs) {
    clearInterval(timerInterval);
    let rem = Math.max(0, Math.round(secs));

    function tick() {
        const el  = document.getElementById('timerNum');
        const bar = document.getElementById('timerBar');
        el.textContent = rem;
        bar.style.width = ((rem / ROUND_SECS) * 100) + '%';

        if (rem <= 5) {
            el.className = 'timer-num danger';
            disableBetting();
        } else if (rem <= 10) {
            el.className = 'timer-num warn';
        } else {
            el.className = 'timer-num safe';
        }

        if (rem <= 0) {
            clearInterval(timerInterval);
            document.getElementById('roundStatusTxt').innerHTML = '<span style="color:var(--gold);">⏳ Processing...</span>';
            disableBetting();
            waitForResult(currentRound?.id);
            return;
        }
        rem--;
    }
    tick();
    timerInterval = setInterval(tick, 1000);
}

// ── ENABLE/DISABLE BETTING ────────────────────────────────────
function enableBetting() {
    document.getElementById('betClosed').style.display = 'none';
    document.querySelectorAll('.cbtn,.nbtn').forEach(b => b.disabled = false);
}
function disableBetting() {
    document.getElementById('betClosed').style.display = 'flex';
    document.querySelectorAll('.cbtn,.nbtn').forEach(b => b.disabled = true);
    document.getElementById('placeBtn').disabled = true;
}

// ── SELECT BET ────────────────────────────────────────────────
function selectBet(type, val, el) {
    document.querySelectorAll('.cbtn,.nbtn').forEach(b => b.classList.remove('sel'));
    el.classList.add('sel');
    selType = type; selVal = val;
    updateSummary();
}

function selectAmt(amt, el) {
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('sel'));
    el.classList.add('sel');
    document.getElementById('betAmt').value = amt;
    updateSummary();
}

function updateSummary() {
    const amt = parseFloat(document.getElementById('betAmt').value) || 0;
    if (selType && amt >= 10) {
        const mult = selType === 'number' ? 9 : 2;
        const labels = {green:'🟢 Green', red:'🔴 Red', violet:'🟣 Violet'};
        document.getElementById('sumType').textContent = selType === 'color' ? labels[selVal] : 'Number ' + selVal;
        document.getElementById('sumAmt').textContent  = '₹' + amt.toFixed(2);
        document.getElementById('sumWin').textContent  = '₹' + (amt * mult).toFixed(2);
        document.getElementById('betSummary').style.display = 'block';
        document.getElementById('placeBtn').disabled = false;
    } else {
        document.getElementById('betSummary').style.display = 'none';
        document.getElementById('placeBtn').disabled = true;
    }
}

// ── PLACE BET ─────────────────────────────────────────────────
async function placeBet() {
    if (!currentRound || !selType) return;
    const amt = parseFloat(document.getElementById('betAmt').value);
    if (!amt || amt < 10) { showToast('Minimum bet ₹10', 'error'); return; }

    const btn = document.getElementById('placeBtn');
    btn.disabled = true; btn.textContent = 'Placing...';

    try {
        const d = await API('/game/bet', {
            method: 'POST',
            body: JSON.stringify({
                round_id:  currentRound.id,
                bet_type:  selType,
                bet_value: selVal,
                amount:    amt,
            })
        });
        if (d.id) {
            showToast('✅ Bet placed! ₹' + amt + ' on ' + selVal, 'success');
            loadWallet(); loadMyBets();
            document.querySelectorAll('.cbtn,.nbtn').forEach(b => b.classList.remove('sel'));
            selType = null; selVal = null;
            document.getElementById('betSummary').style.display = 'none';
        } else {
            showToast(d.message || 'Bet failed', 'error');
        }
    } catch(e) { showToast('Network error', 'error'); }
    finally {
        btn.disabled = false; btn.textContent = '🎯 Place Bet';
    }
}

// ── HISTORY ───────────────────────────────────────────────────
async function loadHistory() {
    const d = await API('/game/history');
    const results = d.data || [];
    const row = document.getElementById('resultsRow');
    if (!results.length) {
        row.innerHTML = '<span style="color:var(--muted);font-size:13px;">No results yet</span>';
        return;
    }
    row.innerHTML = results.slice(0,20).map(r => {
        const color = r.color || 'violet';
        const num   = (r.number !== null && r.number !== undefined) ? r.number : '?';
        return `<div class="rdot ${color}">${num}</div>`;
    }).join('');
}

// ── ROUND HISTORY + MY BETS (combined) ───────────────────────
async function loadMyBets() {
    const [histData, betsData] = await Promise.all([
        API('/game/history'),
        API('/game/my-bets')
    ]);
    const results  = histData.data || [];
    const myBets   = betsData.data || [];
    const icons    = {green:'🟢', red:'🔴', violet:'🟣'};
    const colorHex = {green:'#22C55E', red:'#EF4444', violet:'#A855F7'};

    // map my bets by round_id
    const betMap = {};
    myBets.forEach(b => { betMap[b.round_id] = b; });

    document.getElementById('myBetsBody').innerHTML = results.length
        ? results.slice(0,20).map(r => {
            const dot = `<span style="display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:50%;background:${colorHex[r.color]||'#7C3AED'};color:#fff;font-weight:800;font-size:12px;">${r.number}</span>`;
            const myBet = betMap[r.round_id];
            let betLabel   = '<span style="color:var(--muted);font-size:11px;">No bet</span>';
            let amtLabel   = '--';
            let statusLabel = '--';
            if (myBet) {
                betLabel = myBet.bet_type === 'color'
                    ? (icons[myBet.bet_value]||'') + ' ' + myBet.bet_value
                    : '#' + myBet.bet_value;
                amtLabel = '₹' + myBet.amount;
                if (myBet.status === 'won')
                    statusLabel = `<span style="color:#22C55E;font-weight:700;">+₹${myBet.win_amount} 🎉</span>`;
                else if (myBet.status === 'lost')
                    statusLabel = `<span style="color:#EF4444;font-weight:700;">❌ Lost</span>`;
                else
                    statusLabel = `<span style="color:var(--gold);">⏳</span>`;
            }
            return `<tr>
                <td style="font-size:11px;color:var(--muted);font-family:monospace;">${String(r.round?.round_id||r.round_id||'--').slice(-4)}</td>
                <td>${dot}</td>
                <td>${betLabel}</td>
                <td style="font-size:12px;">${amtLabel}</td>
                <td>${statusLabel}</td>
            </tr>`;
        }).join('')
        : '<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">No rounds yet</td></tr>';
}

// ── WAIT FOR RESULT (poll until admin sets result) ───────────
async function waitForResult(roundId) {
    let attempts = 0;
    const poll = setInterval(async () => {
        attempts++;
        try {
            // Check history for result
            const hist = await API('/game/history');
            const histRound = (hist.data || []).find(r => r.round_id == roundId);
            if (histRound) {
                clearInterval(poll);
                await loadHistory();
                await loadMyBets();
                await loadWallet();
                await loadRound();
                // Check if user won/lost
                const bets = await API('/game/my-bets');
                const myBet = (bets.data || []).find(b => b.round_id == roundId);
                const winAmt = myBet?.status === 'won' ? parseFloat(myBet.win_amount || 0) : 0;
                showResult(histRound.number, histRound.color, winAmt);
                return;
            }
        } catch(e) {}
        if (attempts >= 30) { // 60s max wait
            clearInterval(poll);
            await loadRound();
        }
    }, 2000);
}

// ── RESULT POPUP ──────────────────────────────────────────────
function showResult(number, color, winAmt=0) {
    const hex = {green:'#22C55E', red:'#EF4444', violet:'#A855F7'};
    const c = document.getElementById('resultCircle');
    c.textContent = number;
    c.style.background = hex[color]||'#7C3AED';
    c.style.color = '#fff';
    document.getElementById('resultTitle').textContent = color.toUpperCase() + ' - ' + number;
    document.getElementById('resultSub').textContent = 'Round result declared!';
    const we = document.getElementById('resultWin');
    if (winAmt > 0) {
        we.textContent = '+₹' + winAmt + ' Won! 🎉';
        we.style.display = 'block';
        document.getElementById('resultTitle').textContent = '🎉 You Won!';
    } else { we.style.display = 'none'; }
    document.getElementById('resultOverlay').style.display = 'flex';
    setTimeout(closeResult, 4000);
}
function closeResult() {
    document.getElementById('resultOverlay').style.display = 'none';
}

// ── INIT ──────────────────────────────────────────────────────
loadWallet();
loadRound();
loadHistory();
loadMyBets();

// Wallet + bets refresh every 10s
setInterval(() => { loadWallet(); loadMyBets(); }, 10000);

// Re-sync round from server every 15s to prevent timer drift
setInterval(async () => {
    if (!timerInterval) return; // timer already stopped, loadRound will be called
    try {
        const d = await API('/game/round');
        if (!currentRound || currentRound.id !== d.id) {
            // New round started — full reload
            currentRound = d;
            ROUND_SECS = 30;
            document.getElementById('roundId').textContent = d.round_id;
            const secsLeft = d.ends_at ? Math.max(0, Math.round((new Date(d.ends_at) - Date.now()) / 1000)) : d.seconds_left;
            ROUND_SECS = secsLeft;
            startTimer(secsLeft);
            if (secsLeft > 5) enableBetting(); else disableBetting();
            loadHistory(); loadMyBets();
        }
    } catch(e) {}
}, 15000);
</script>
@endpush
