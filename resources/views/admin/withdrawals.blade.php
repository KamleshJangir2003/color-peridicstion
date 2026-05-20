@extends('admin.layout')
@section('title', 'Withdrawals Management')

@section('content')

<div style="display:flex;gap:8px;margin-bottom:16px;">
    <button class="btn btn-primary" id="tab-pending"  onclick="filterWd('pending')">⏳ Pending</button>
    <button class="btn btn-outline" id="tab-approved" onclick="filterWd('approved')">✅ Approved</button>
    <button class="btn btn-outline" id="tab-rejected" onclick="filterWd('rejected')">❌ Rejected</button>
    <button class="btn btn-outline" id="tab-"         onclick="filterWd('')">📋 All</button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>User</th><th>Amount</th><th>Method</th>
                    <th>Account Details</th><th>Date</th><th>Status</th><th>Actions</th>
                </tr>
            </thead>
            <tbody id="wdTable">
                <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="wdPagination" style="display:flex;justify-content:center;gap:8px;margin-top:16px;"></div>
</div>

<!-- ACCOUNT DETAILS MODAL -->
<div class="modal-overlay" id="accModal" onclick="closeModal('accModal')">
    <div class="modal" onclick="event.stopPropagation()" style="max-width:360px;">
        <div class="modal-title">
            Account Details
            <button class="modal-close" onclick="closeModal('accModal')">✕</button>
        </div>
        <div id="accDetails" style="font-size:13px;line-height:2;"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let wdFilter = 'pending';
let wdPage = 1;

async function loadWithdrawals(page = 1) {
    wdPage = page;
    const url = `/withdrawals${wdFilter ? '?status=' + wdFilter : '?'}${wdFilter ? '&' : ''}page=${page}`;
    const data = await AAPI(url);
    const wds = data.data || [];

    const methodIcon = { bank:'🏦', upi:'📱', tron:'🔷' };
    const statusBadge = { pending:'badge-gold', approved:'badge-green', rejected:'badge-red' };

    document.getElementById('wdTable').innerHTML = wds.length
        ? wds.map(w => `<tr>
            <td style="color:var(--muted);font-size:12px;">${w.id}</td>
            <td>
                <div style="font-weight:600;">${w.user?.name || '--'}</div>
                <div style="font-size:11px;color:var(--muted);">${w.user?.phone || ''}</div>
            </td>
            <td style="font-size:16px;font-weight:800;color:var(--red);">₹${w.amount}</td>
            <td>${methodIcon[w.method] || ''} ${w.method?.toUpperCase()}</td>
            <td>
                <button class="btn btn-sm btn-outline" onclick='viewAccDetails(${JSON.stringify(w.account_details)})'>
                    👁 View
                </button>
            </td>
            <td style="font-size:11px;color:var(--muted);">${new Date(w.created_at).toLocaleString('en-IN')}</td>
            <td><span class="badge ${statusBadge[w.status]}">${w.status}</span></td>
            <td>
                ${w.status === 'pending' ? `
                    <button class="btn btn-sm btn-green" onclick="approveWd(${w.id})">✓ Approve</button>
                    <button class="btn btn-sm btn-red" onclick="rejectWd(${w.id})" style="margin-left:4px;">✗ Reject</button>
                ` : `<span style="color:var(--muted);font-size:12px;">Processed</span>`}
            </td>
        </tr>`).join('')
        : '<tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px;">No withdrawals found</td></tr>';

    if (data.last_page > 1) {
        let html = '';
        for (let i = Math.max(1, wdPage-2); i <= Math.min(data.last_page, wdPage+2); i++) {
            html += `<button class="btn ${i===wdPage?'btn-primary':'btn-outline'}" style="padding:6px 12px;" onclick="loadWithdrawals(${i})">${i}</button>`;
        }
        document.getElementById('wdPagination').innerHTML = html;
    }
}

function filterWd(status) {
    wdFilter = status;
    document.querySelectorAll('[id^="tab-"]').forEach(b => b.className = 'btn btn-outline');
    const activeBtn = document.getElementById('tab-' + status);
    if (activeBtn) activeBtn.className = 'btn btn-primary';
    loadWithdrawals(1);
}

function viewAccDetails(details) {
    const html = Object.entries(details).map(([k,v]) =>
        `<div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border);padding:6px 0;">
            <span style="color:var(--muted);text-transform:capitalize;">${k.replace(/_/g,' ')}</span>
            <strong>${v}</strong>
        </div>`
    ).join('');
    document.getElementById('accDetails').innerHTML = html;
    document.getElementById('accModal').classList.add('open');
}

function closeModal(id) { document.getElementById(id).classList.remove('open'); }

async function approveWd(id) {
    if (!confirm('Approve this withdrawal? Make sure payment is sent.')) return;
    await AAPI(`/withdrawals/${id}/approve`, { method: 'POST', body: JSON.stringify({}) });
    showToast('Withdrawal approved!', 'success');
    loadWithdrawals(wdPage);
}

async function rejectWd(id) {
    const note = prompt('Rejection reason (amount will be refunded):') || '';
    await AAPI(`/withdrawals/${id}/reject`, { method: 'POST', body: JSON.stringify({ note }) });
    showToast('Withdrawal rejected. Amount refunded.', 'error');
    loadWithdrawals(wdPage);
}

loadWithdrawals();
</script>
@endpush
