@extends('admin.layout')
@section('title', 'Game Control')

@section('content')

<div class="g2" style="margin-bottom:14px;">

    <!-- CURRENT ROUND -->
    <div class="card" style="text-align:center;">
        <div style="font-size:13px;font-weight:700;margin-bottom:14px;">🎮 Current Round</div>
        <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Round ID</div>
        <div style="font-size:20px;font-weight:800;font-family:monospace;color:var(--gold);" id="crRoundId">--</div>
        <div style="margin:10px 0;"><span class="badge badge-green" id="crStatus">Loading...</span></div>
        <div style="font-size:34px;font-weight:900;color:var(--text);" id="crTimer">--</div>
        <div style="font-size:12px;color:var(--muted);margin-top:6px;">Bets: <strong id="crBets">₹0</strong></div>
    </div>

    <!-- RESULT ENGINE -->
    <div class="card">
        <div style="font-size:13px;font-weight:700;margin-bottom:12px;">⚙️ Result Engine</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <button class="btn btn-outline" id="rt-smart" onclick="setResultType('smart')" style="text-align:left;padding:12px;">
                🧠 <strong>Smart Profit</strong><br>
                <span style="font-size:11px;color:var(--muted);margin-left:20px;">Auto safest result</span>
            </button>
            <button class="btn btn-outline" id="rt-auto" onclick="setResultType('auto')" style="text-align:left;padding:12px;">
                🎲 <strong>Auto Random</strong><br>
                <span style="font-size:11px;color:var(--muted);margin-left:20px;">Pure random</span>
            </button>
            <button class="btn btn-outline" id="rt-admin" onclick="setResultType('admin')" style="text-align:left;padding:12px;">
                👑 <strong>Admin Control</strong><br>
                <span style="font-size:11px;color:var(--muted);margin-left:20px;">You choose manually</span>
            </button>
        </div>
        <div style="margin-top:10px;font-size:12px;color:var(--muted);">
            Active: <strong id="currentResultType" style="color:var(--primary);">--</strong>
        </div>
    </div>
</div>

<!-- MANUAL RESULT -->
<div class="card" id="manualResultCard" style="margin-bottom:14px;display:none;">
    <div style="font-size:13px;font-weight:700;margin-bottom:12px;">👑 Set Manual Result</div>
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-bottom:14px;">
        @foreach([0=>'violet',1=>'green',2=>'red',3=>'green',4=>'red',5=>'violet',6=>'red',7=>'green',8=>'red',9=>'green'] as $num => $color)
        <button onclick="selectManualNum({{ $num }}, this)"
            style="border:2px solid var(--border);border-radius:10px;padding:12px 4px;font-size:17px;font-weight:800;cursor:pointer;background:rgba({{ $color=='green'?'34,197,94':($color=='red'?'239,68,68':'168,85,247') }},0.15);color:var(--{{ $color }});"
            id="mnum-{{ $num }}">{{ $num }}</button>
        @endforeach
    </div>
    <button class="btn btn-primary" style="width:100%;" id="setResultBtn" onclick="setManualResult()" disabled>
        Set Result for Current Round
    </button>
</div>

<!-- ROUND HISTORY -->
<div class="card">
    <div style="font-size:13px;font-weight:700;margin-bottom:12px;">📊 Round History</div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Round</th><th>Status</th><th>Result</th><th>Engine</th><th>Bets</th><th>Profit</th></tr>
            </thead>
            <tbody id="roundsTable">
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
let selectedManualNum = null;

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
    const data = await AAPI('/game/rounds?per_page=1');
    const type = (data.data || [])[0]?.result_type || 'smart';
    document.getElementById('currentResultType').textContent = type.toUpperCase();
    highlightResultType(type);
}

function highlightResultType(type) {
    ['smart','auto','admin'].forEach(t => {
        const btn = document.getElementById('rt-' + t);
        if (btn) btn.className = 'btn ' + (t === type ? 'btn-primary' : 'btn-outline');
        if (btn) btn.style.textAlign = 'left';
        if (btn) btn.style.padding = '12px';
    });
    document.getElementById('manualResultCard').style.display = type === 'admin' ? 'block' : 'none';
}

async function setResultType(type) {
    await AAPI('/game/result-type', { method:'POST', body:JSON.stringify({ type }) });
    showToast('Engine: ' + type.toUpperCase(), 'success');
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
    if (selectedManualNum === null) { showToast('Select a number first', 'error'); return; }
    const data = await AAPI('/game/rounds');
    const round = (data.data || []).find(r => r.status === 'closed');
    if (!round) { showToast('No active round', 'error'); return; }
    const res = await AAPI(`/game/rounds/${round.id}/result`, {
        method: 'POST',
        body: JSON.stringify({ number: selectedManualNum })
    });
    if (res.number !== undefined) {
        showToast(`✅ Result: ${selectedManualNum} (${res.color})`, 'success');
    } else {
        showToast(res.message || 'Failed', 'error');
    }
    loadRounds(); loadCurrentRound();
}

async function loadRounds() {
    const data = await AAPI('/game/rounds');
    const rounds = data.data || [];
    const colorDot = { green:'🟢', red:'🔴', violet:'🟣' };
    const engineBadge = { smart:'badge-purple', auto:'badge-muted', admin:'badge-gold' };

    document.getElementById('roundsTable').innerHTML = rounds.length
        ? rounds.map(r => `<tr>
            <td style="font-family:monospace;font-size:11px;">${r.round_id}</td>
            <td><span class="badge ${r.status==='resulted'?'badge-green':r.status==='open'?'badge-purple':'badge-muted'}">${r.status}</span></td>
            <td>${r.result_number !== null ? (colorDot[r.result_color]||'') + ' ' + r.result_number : '--'}</td>
            <td><span class="badge ${engineBadge[r.result_type]||'badge-muted'}">${r.result_type}</span></td>
            <td>₹${r.total_bet_amount}</td>
            <td style="font-weight:700;color:${parseFloat(r.result?.profit||0)>=0?'var(--green)':'var(--red)'};">
                ₹${parseFloat(r.result?.profit || 0).toFixed(0)}
            </td>
        </tr>`).join('')
        : '<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">No rounds yet</td></tr>';
}

loadCurrentRound();
loadResultType();
loadRounds();
setInterval(loadCurrentRound, 5000);
setInterval(loadRounds, 15000);
</script>
@endpush
