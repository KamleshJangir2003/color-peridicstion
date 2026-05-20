@extends('admin.layout')
@section('title', 'Game Control')

@section('content')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">

    <!-- CURRENT ROUND -->
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;">🎮 Current Round</div>
        <div id="currentRoundInfo" style="text-align:center;padding:16px;">
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">Round ID</div>
            <div style="font-size:22px;font-weight:800;font-family:monospace;color:var(--gold);" id="crRoundId">--</div>
            <div style="margin-top:12px;">
                <span class="badge badge-green" id="crStatus">Loading...</span>
            </div>
            <div style="font-size:36px;font-weight:900;margin:12px 0;" id="crTimer">--</div>
            <div style="font-size:12px;color:var(--muted);">Total Bets: <strong id="crBets">₹0</strong></div>
        </div>
    </div>

    <!-- RESULT TYPE CONTROL -->
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;">⚙️ Result Engine</div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <button class="btn btn-outline" id="rt-smart"  onclick="setResultType('smart')"  style="text-align:left;padding:14px;">
                🧠 <strong>Smart Profit Engine</strong><br>
                <span style="font-size:11px;color:var(--muted);margin-left:24px;">Auto picks safest result for house</span>
            </button>
            <button class="btn btn-outline" id="rt-auto"   onclick="setResultType('auto')"   style="text-align:left;padding:14px;">
                🎲 <strong>Auto Random</strong><br>
                <span style="font-size:11px;color:var(--muted);margin-left:24px;">Pure random result</span>
            </button>
            <button class="btn btn-outline" id="rt-admin"  onclick="setResultType('admin')"  style="text-align:left;padding:14px;">
                👑 <strong>Admin Controlled</strong><br>
                <span style="font-size:11px;color:var(--muted);margin-left:24px;">You choose the result manually</span>
            </button>
        </div>
        <div style="margin-top:12px;font-size:12px;color:var(--muted);">
            Current: <strong id="currentResultType" style="color:var(--primary);">--</strong>
        </div>
    </div>
</div>

<!-- MANUAL RESULT (shown when admin mode) -->
<div class="card" id="manualResultCard" style="margin-bottom:24px;display:none;">
    <div style="font-size:14px;font-weight:700;margin-bottom:16px;">👑 Set Manual Result</div>
    <div style="font-size:13px;color:var(--muted);margin-bottom:12px;">Select number for current closed round:</div>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:16px;">
        @foreach([0=>'violet',1=>'green',2=>'red',3=>'green',4=>'red',5=>'violet',6=>'red',7=>'green',8=>'red',9=>'green'] as $num => $color)
        <button onclick="selectManualNum({{ $num }}, this)"
            style="border:2px solid var(--border);border-radius:10px;padding:14px;font-size:18px;font-weight:800;cursor:pointer;background:rgba({{ $color=='green'?'34,197,94':($color=='red'?'239,68,68':'168,85,247') }},0.15);color:var(--{{ $color }});"
            id="mnum-{{ $num }}">{{ $num }}</button>
        @endforeach
    </div>
    <button class="btn btn-primary" id="setResultBtn" onclick="setManualResult()" disabled>
        Set Result for Current Round
    </button>
</div>

<!-- ROUND HISTORY -->
<div class="card">
    <div style="font-size:14px;font-weight:700;margin-bottom:16px;">📊 Round History</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Round ID</th><th>Status</th><th>Result</th><th>Engine</th><th>Total Bets</th><th>Payout</th><th>Profit</th><th>Time</th></tr>
            </thead>
            <tbody id="roundsTable">
                <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedManualNum = null;
let closedRoundId = null;

async function loadCurrentRound() {
    try {
        const res = await fetch('/api/game/round', {
            headers: { 'Authorization': 'Bearer ' + ADMIN_TOKEN, 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (data.round_id) {
            document.getElementById('crRoundId').textContent = data.round_id;
            document.getElementById('crStatus').textContent  = '🟢 Open';
            document.getElementById('crTimer').textContent   = data.seconds_left + 's';
        } else {
            document.getElementById('crRoundId').textContent = 'No active round';
            document.getElementById('crStatus').textContent  = 'Waiting';
            document.getElementById('crTimer').textContent   = '--';
        }
    } catch(e) {}
}

async function loadResultType() {
    // Get from settings
    const data = await AAPI('/game/rounds?per_page=1');
    const lastRound = (data.data || [])[0];
    const type = lastRound?.result_type || 'smart';
    document.getElementById('currentResultType').textContent = type.toUpperCase();
    highlightResultType(type);
}

function highlightResultType(type) {
    ['smart','auto','admin'].forEach(t => {
        const btn = document.getElementById('rt-' + t);
        if (btn) btn.className = 'btn ' + (t === type ? 'btn-primary' : 'btn-outline');
    });
    document.getElementById('manualResultCard').style.display = type === 'admin' ? 'block' : 'none';
}

async function setResultType(type) {
    await AAPI('/game/result-type', { method: 'POST', body: JSON.stringify({ type }) });
    showToast('Result engine set to: ' + type.toUpperCase(), 'success');
    document.getElementById('currentResultType').textContent = type.toUpperCase();
    highlightResultType(type);
}

function selectManualNum(num, el) {
    selectedManualNum = num;
    document.querySelectorAll('[id^="mnum-"]').forEach(b => b.style.boxShadow = 'none');
    el.style.boxShadow = '0 0 0 3px white';
    document.getElementById('setResultBtn').disabled = false;
}

async function setManualResult() {
    if (selectedManualNum === null) return;
    // Get latest closed round
    const data = await AAPI('/game/rounds');
    const closedRound = (data.data || []).find(r => r.status === 'closed');
    if (!closedRound) { showToast('No closed round found. Wait for round to close.', 'error'); return; }

    const res = await AAPI(`/game/rounds/${closedRound.id}/result`, {
        method: 'POST',
        body: JSON.stringify({ number: selectedManualNum })
    });
    showToast(`Result set: ${selectedManualNum}`, 'success');
    loadRounds();
    loadCurrentRound();
}

async function loadRounds() {
    const data = await AAPI('/game/rounds');
    const rounds = data.data || [];
    const colorDot = { green:'🟢', red:'🔴', violet:'🟣' };
    const engineBadge = { smart:'badge-purple', auto:'badge-muted', admin:'badge-gold' };

    document.getElementById('roundsTable').innerHTML = rounds.length
        ? rounds.map(r => `<tr>
            <td style="font-family:monospace;font-size:12px;">${r.round_id}</td>
            <td><span class="badge ${r.status==='resulted'?'badge-green':r.status==='open'?'badge-purple':'badge-muted'}">${r.status}</span></td>
            <td style="font-size:16px;">${r.result_number !== null ? (colorDot[r.result_color]||'') + ' ' + r.result_number : '--'}</td>
            <td><span class="badge ${engineBadge[r.result_type]||'badge-muted'}">${r.result_type}</span></td>
            <td>₹${r.total_bet_amount}</td>
            <td>₹${r.result?.total_payout || 0}</td>
            <td style="font-weight:700;color:${parseFloat(r.result?.profit||0)>=0?'var(--green)':'var(--red)'};">
                ₹${parseFloat(r.result?.profit || 0).toFixed(2)}
            </td>
            <td style="font-size:11px;color:var(--muted);">${new Date(r.created_at).toLocaleString('en-IN')}</td>
        </tr>`).join('')
        : '<tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px;">No rounds yet</td></tr>';
}

loadCurrentRound();
loadResultType();
loadRounds();
setInterval(loadCurrentRound, 5000);
setInterval(loadRounds, 15000);
</script>
@endpush
