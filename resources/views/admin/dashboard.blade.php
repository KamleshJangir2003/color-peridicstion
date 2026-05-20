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
        <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card gold">
        <div class="stat-icon gold">⏳</div>
        <div class="stat-value" id="statPendingDep">--</div>
        <div class="stat-label">Pending Deposits</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon red">📤</div>
        <div class="stat-value" id="statPendingWd">--</div>
        <div class="stat-label">Pending Withdrawals</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">

    <!-- RECENT DEPOSITS -->
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <span style="font-size:14px;font-weight:700;">Recent Deposits</span>
            <a href="/admin/deposits" style="font-size:12px;color:var(--primary);text-decoration:none;">View All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="recentDeposits">
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:20px;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RECENT WITHDRAWALS -->
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <span style="font-size:14px;font-weight:700;">Recent Withdrawals</span>
            <a href="/admin/withdrawals" style="font-size:12px;color:var(--primary);text-decoration:none;">View All →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>User</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
                <tbody id="recentWithdrawals">
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:20px;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- RECENT GAME ROUNDS -->
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <span style="font-size:14px;font-weight:700;">Recent Game Rounds</span>
        <a href="/admin/game" style="font-size:12px;color:var(--primary);text-decoration:none;">Game Control →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Round ID</th><th>Status</th><th>Result</th><th>Total Bets</th><th>Payout</th><th>Profit</th></tr></thead>
            <tbody id="recentRounds">
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:20px;">Loading...</td></tr>
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
    document.getElementById('statRevenue').textContent  = '₹' + parseFloat(data.total_revenue || 0).toFixed(0);
    document.getElementById('statPendingDep').textContent = data.pending_deposits || 0;
    document.getElementById('statPendingWd').textContent  = data.pending_withdrawals || 0;
}

async function loadRecentDeposits() {
    const data = await AAPI('/deposits?status=pending');
    const rows = (data.data || []).slice(0, 5);
    document.getElementById('recentDeposits').innerHTML = rows.length
        ? rows.map(d => `<tr>
            <td>${d.user?.name || '--'}<br><span style="font-size:11px;color:var(--muted);">${d.user?.phone || ''}</span></td>
            <td style="font-weight:700;">₹${d.amount}</td>
            <td><span class="badge badge-gold">pending</span></td>
            <td>
                <button class="btn btn-sm btn-green" onclick="approveDeposit(${d.id})">✓</button>
                <button class="btn btn-sm btn-red" onclick="rejectDeposit(${d.id})" style="margin-left:4px;">✗</button>
            </td>
        </tr>`).join('')
        : '<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:16px;">No pending deposits</td></tr>';
}

async function loadRecentWithdrawals() {
    const data = await AAPI('/withdrawals?status=pending');
    const rows = (data.data || []).slice(0, 5);
    document.getElementById('recentWithdrawals').innerHTML = rows.length
        ? rows.map(w => `<tr>
            <td>${w.user?.name || '--'}<br><span style="font-size:11px;color:var(--muted);">${w.user?.phone || ''}</span></td>
            <td style="font-weight:700;">₹${w.amount}</td>
            <td><span class="badge badge-gold">pending</span></td>
            <td>
                <button class="btn btn-sm btn-green" onclick="approveWd(${w.id})">✓</button>
                <button class="btn btn-sm btn-red" onclick="rejectWd(${w.id})" style="margin-left:4px;">✗</button>
            </td>
        </tr>`).join('')
        : '<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:16px;">No pending withdrawals</td></tr>';
}

async function loadRecentRounds() {
    const data = await AAPI('/game/rounds');
    const rows = (data.data || []).slice(0, 8);
    const colorDot = { green:'🟢', red:'🔴', violet:'🟣' };
    document.getElementById('recentRounds').innerHTML = rows.length
        ? rows.map(r => `<tr>
            <td style="font-family:monospace;font-size:12px;">${r.round_id}</td>
            <td><span class="badge ${r.status==='resulted'?'badge-green':r.status==='open'?'badge-purple':'badge-muted'}">${r.status}</span></td>
            <td>${r.result_number !== null ? (colorDot[r.result_color]||'') + ' ' + r.result_number : '--'}</td>
            <td>₹${r.total_bet_amount}</td>
            <td>₹${r.result?.total_payout || 0}</td>
            <td style="color:${parseFloat(r.result?.profit||0)>=0?'var(--green)':'var(--red)'};">
                ₹${parseFloat(r.result?.profit || 0).toFixed(2)}
            </td>
        </tr>`).join('')
        : '<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:16px;">No rounds yet</td></tr>';
}

async function approveDeposit(id) {
    await AAPI(`/deposits/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
    showToast('Deposit approved!', 'success');
    loadRecentDeposits(); loadDashboard();
}
async function rejectDeposit(id) {
    if (!confirm('Reject this deposit?')) return;
    await AAPI(`/deposits/${id}/reject`, { method: 'POST', body: JSON.stringify({}) });
    showToast('Deposit rejected', 'error');
    loadRecentDeposits(); loadDashboard();
}
async function approveWd(id) {
    await AAPI(`/withdrawals/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
    showToast('Withdrawal approved!', 'success');
    loadRecentWithdrawals(); loadDashboard();
}
async function rejectWd(id) {
    if (!confirm('Reject this withdrawal?')) return;
    await AAPI(`/withdrawals/${id}/reject`, { method: 'POST', body: JSON.stringify({}) });
    showToast('Withdrawal rejected', 'error');
    loadRecentWithdrawals(); loadDashboard();
}

loadDashboard();
loadRecentDeposits();
loadRecentWithdrawals();
loadRecentRounds();
setInterval(() => { loadDashboard(); loadRecentDeposits(); loadRecentWithdrawals(); }, 30000);
</script>
@endpush
