@extends('admin.layout')
@section('title', 'Dashboard')

@section('content')

<!-- STAT CARDS -->
<div class="stat-grid">
    <div class="stat-card purple">
        <div class="stat-icon purple">👥</div>
        <div class="stat-value" id="statUsers">--</div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green">💰</div>
        <div class="stat-value" id="statRevenue">--</div>
        <div class="stat-label">Revenue</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon gold">⏳</div>
        <div class="stat-value" id="statPendingDep">--</div>
        <div class="stat-label">Pending Dep.</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon red">📤</div>
        <div class="stat-value" id="statPendingWd">--</div>
        <div class="stat-label">Pending Wd.</div>
    </div>
</div>

<!-- DEPOSITS + WITHDRAWALS -->
<div class="g2" style="margin-bottom:14px;">

    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <span style="font-size:13px;font-weight:700;">⏳ Pending Deposits</span>
            <a href="/admin/deposits" style="font-size:11px;color:var(--primary);text-decoration:none;">All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Amt</th><th>Act</th></tr></thead>
                <tbody id="recentDeposits">
                    <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:16px;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <span style="font-size:13px;font-weight:700;">⏳ Pending Withdrawals</span>
            <a href="/admin/withdrawals" style="font-size:11px;color:var(--primary);text-decoration:none;">All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Amt</th><th>Act</th></tr></thead>
                <tbody id="recentWithdrawals">
                    <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:16px;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- RECENT GAME ROUNDS -->
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <span style="font-size:13px;font-weight:700;">🎮 Recent Rounds</span>
        <a href="/admin/game" style="font-size:11px;color:var(--primary);text-decoration:none;">Control →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Round</th><th>Status</th><th>Result</th><th>Bets</th><th>Profit</th></tr></thead>
            <tbody id="recentRounds">
                <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:16px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
async function loadDashboard() {
    const data = await AAPI('/dashboard');
    document.getElementById('statUsers').textContent    = data.total_users || 0;
    const totalRev = parseFloat(data.total_revenue || 0) + parseFloat(data.withdrawal_charges || 0);
    document.getElementById('statRevenue').textContent  = '₹' + totalRev.toFixed(0);
    document.getElementById('statPendingDep').textContent = data.pending_deposits || 0;
    document.getElementById('statPendingWd').textContent  = data.pending_withdrawals || 0;
}

async function loadRecentDeposits() {
    const data = await AAPI('/deposits?status=pending');
    const rows = (data.data || []).slice(0, 5);
    document.getElementById('recentDeposits').innerHTML = rows.length
        ? rows.map(d => `<tr>
            <td><div style="font-weight:600;font-size:12px;">${d.user?.name || '--'}</div><div style="font-size:10px;color:var(--muted);">${d.user?.phone || ''}</div></td>
            <td style="font-weight:700;color:var(--gold);">₹${d.amount}</td>
            <td style="white-space:nowrap;">
                <button class="btn btn-sm btn-green" onclick="approveDeposit(${d.id})">✓</button>
                <button class="btn btn-sm btn-red" onclick="rejectDeposit(${d.id})" style="margin-left:3px;">✗</button>
            </td>
        </tr>`).join('')
        : '<tr><td colspan="3" style="text-align:center;color:var(--muted);padding:14px;font-size:12px;">No pending</td></tr>';
}

async function loadRecentWithdrawals() {
    const data = await AAPI('/withdrawals?status=pending');
    const rows = (data.data || []).slice(0, 5);
    document.getElementById('recentWithdrawals').innerHTML = rows.length
        ? rows.map(w => `<tr>
            <td><div style="font-weight:600;font-size:12px;">${w.user?.name || '--'}</div><div style="font-size:10px;color:var(--muted);">${w.user?.phone || ''}</div></td>
            <td style="font-weight:700;color:var(--red);">₹${w.amount}</td>
            <td style="white-space:nowrap;">
                <button class="btn btn-sm btn-green" onclick="approveWd(${w.id})">✓</button>
                <button class="btn btn-sm btn-red" onclick="rejectWd(${w.id})" style="margin-left:3px;">✗</button>
            </td>
        </tr>`).join('')
        : '<tr><td colspan="3" style="text-align:center;color:var(--muted);padding:14px;font-size:12px;">No pending</td></tr>';
}

async function loadRecentRounds() {
    const data = await AAPI('/game/rounds');
    const rows = (data.data || []).slice(0, 8);
    const colorDot = { green:'🟢', red:'🔴', violet:'🟣' };
    document.getElementById('recentRounds').innerHTML = rows.length
        ? rows.map(r => `<tr>
            <td style="font-family:monospace;font-size:11px;">${r.round_id}</td>
            <td><span class="badge ${r.status==='resulted'?'badge-green':r.status==='open'?'badge-purple':'badge-muted'}">${r.status}</span></td>
            <td>${r.result_number !== null ? (colorDot[r.result_color]||'') + ' ' + r.result_number : '--'}</td>
            <td>₹${r.total_bet_amount}</td>
            <td style="font-weight:700;color:${parseFloat(r.result?.profit||0)>=0?'var(--green)':'var(--red)'};">
                ₹${parseFloat(r.result?.profit || 0).toFixed(0)}
            </td>
        </tr>`).join('')
        : '<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:14px;">No rounds yet</td></tr>';
}

async function approveDeposit(id) {
    try {
        const res = await AAPI(`/deposits/${id}/approve`, { method:'POST', body:JSON.stringify({}) });
        if (res.message && !res.error) {
            showToast(res.message, 'success');
            loadRecentDeposits(); loadDashboard();
        } else {
            showToast(res.message || 'Failed to approve', 'error');
        }
    } catch(e) { showToast('Error approving deposit', 'error'); }
}
async function rejectDeposit(id) {
    if (!confirm('Reject this deposit?')) return;
    try {
        const res = await AAPI(`/deposits/${id}/reject`, { method:'POST', body:JSON.stringify({}) });
        showToast(res.message || 'Deposit rejected', res.error ? 'error' : 'success');
        loadRecentDeposits(); loadDashboard();
    } catch(e) { showToast('Error', 'error'); }
}
async function approveWd(id) {
    try {
        const res = await AAPI(`/withdrawals/${id}/approve`, { method:'POST', body:JSON.stringify({}) });
        if (res.message && !res.error) {
            showToast(res.message, 'success');
            loadRecentWithdrawals(); loadDashboard();
        } else {
            showToast(res.message || 'Failed to approve', 'error');
        }
    } catch(e) { showToast('Error approving withdrawal', 'error'); }
}
async function rejectWd(id) {
    if (!confirm('Reject this withdrawal?')) return;
    try {
        const res = await AAPI(`/withdrawals/${id}/reject`, { method:'POST', body:JSON.stringify({}) });
        showToast(res.message || 'Withdrawal rejected', res.error ? 'error' : 'success');
        loadRecentWithdrawals(); loadDashboard();
    } catch(e) { showToast('Error', 'error'); }
}

loadDashboard();
loadRecentDeposits();
loadRecentWithdrawals();
loadRecentRounds();
setInterval(() => { loadDashboard(); loadRecentDeposits(); loadRecentWithdrawals(); }, 30000);
</script>
@endpush
