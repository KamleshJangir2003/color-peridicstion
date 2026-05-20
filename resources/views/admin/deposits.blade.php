@extends('admin.layout')
@section('title', 'Deposits Management')

@section('content')

<!-- FILTER TABS -->
<div style="display:flex;gap:8px;margin-bottom:16px;">
    <button class="btn btn-primary" id="tab-pending"   onclick="filterDeposits('pending')">⏳ Pending</button>
    <button class="btn btn-outline" id="tab-approved"  onclick="filterDeposits('approved')">✅ Approved</button>
    <button class="btn btn-outline" id="tab-rejected"  onclick="filterDeposits('rejected')">❌ Rejected</button>
    <button class="btn btn-outline" id="tab-all"       onclick="filterDeposits('')">📋 All</button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>User</th><th>Amount</th><th>Method</th>
                    <th>Txn ID</th><th>Screenshot</th><th>Date</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody id="depositsTable">
                <tr><td colspan="9" style="text-align:center;color:var(--muted);padding:24px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="depPagination" style="display:flex;justify-content:center;gap:8px;margin-top:16px;"></div>
</div>

<!-- SCREENSHOT MODAL -->
<div class="modal-overlay" id="screenshotModal" onclick="closeModal('screenshotModal')">
    <div class="modal" onclick="event.stopPropagation()" style="max-width:360px;text-align:center;">
        <div class="modal-title">
            Payment Screenshot
            <button class="modal-close" onclick="closeModal('screenshotModal')">✕</button>
        </div>
        <img id="screenshotImg" src="" style="width:100%;border-radius:10px;max-height:400px;object-fit:contain;">
    </div>
</div>

@endsection

@push('scripts')
<script>
let depFilter = 'pending';
let depPage = 1;

async function loadDeposits(page = 1) {
    depPage = page;
    const url = `/deposits${depFilter ? '?status=' + depFilter : '?'}${depFilter ? '&' : ''}page=${page}`;
    const data = await AAPI(url);
    const deps = data.data || [];

    const methodIcon = { upi:'📱', qr:'📷', tron_usdt:'🔷' };
    const statusBadge = { pending:'badge-gold', approved:'badge-green', rejected:'badge-red' };

    document.getElementById('depositsTable').innerHTML = deps.length
        ? deps.map(d => `<tr>
            <td style="color:var(--muted);font-size:12px;">${d.id}</td>
            <td>
                <div style="font-weight:600;">${d.user?.name || '--'}</div>
                <div style="font-size:11px;color:var(--muted);">${d.user?.phone || ''}</div>
            </td>
            <td style="font-size:16px;font-weight:800;color:var(--gold);">₹${d.amount}</td>
            <td>${methodIcon[d.method] || ''} ${d.method?.toUpperCase()}</td>
            <td style="font-family:monospace;font-size:11px;color:var(--muted);">${d.transaction_id || '--'}</td>
            <td>
                ${d.screenshot
                    ? `<button class="btn btn-sm btn-outline" onclick="viewScreenshot('/storage/${d.screenshot}')">📷 View</button>`
                    : '<span style="color:var(--muted);font-size:12px;">None</span>'}
            </td>
            <td style="font-size:11px;color:var(--muted);">${new Date(d.created_at).toLocaleString('en-IN')}</td>
            <td><span class="badge ${statusBadge[d.status]}">${d.status}</span></td>
            <td>
                ${d.status === 'pending' ? `
                    <button class="btn btn-sm btn-green" onclick="approveDeposit(${d.id})">✓ Approve</button>
                    <button class="btn btn-sm btn-red" onclick="rejectDeposit(${d.id})" style="margin-left:4px;">✗ Reject</button>
                ` : `<span style="color:var(--muted);font-size:12px;">Processed</span>`}
            </td>
        </tr>`).join('')
        : '<tr><td colspan="9" style="text-align:center;color:var(--muted);padding:24px;">No deposits found</td></tr>';

    // Pagination
    if (data.last_page > 1) {
        let html = '';
        for (let i = Math.max(1, depPage-2); i <= Math.min(data.last_page, depPage+2); i++) {
            html += `<button class="btn ${i===depPage?'btn-primary':'btn-outline'}" style="padding:6px 12px;" onclick="loadDeposits(${i})">${i}</button>`;
        }
        document.getElementById('depPagination').innerHTML = html;
    }
}

function filterDeposits(status) {
    depFilter = status;
    ['pending','approved','rejected','all'].forEach(s => {
        const btn = document.getElementById(`tab-${s || 'all'}`);
        if (btn) btn.className = 'btn ' + (s === status ? 'btn-primary' : 'btn-outline');
    });
    loadDeposits(1);
}

function viewScreenshot(url) {
    document.getElementById('screenshotImg').src = url;
    document.getElementById('screenshotModal').classList.add('open');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('open');
}

async function approveDeposit(id) {
    if (!confirm('Approve this deposit?')) return;
    const data = await AAPI(`/deposits/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
    showToast('Deposit approved! Wallet credited.', 'success');
    loadDeposits(depPage);
}

async function rejectDeposit(id) {
    const note = prompt('Rejection reason (optional):') || '';
    const data = await AAPI(`/deposits/${id}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
    showToast('Deposit rejected', 'error');
    loadDeposits(depPage);
}

loadDeposits();
</script>
@endpush
