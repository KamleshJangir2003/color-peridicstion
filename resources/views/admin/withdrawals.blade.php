@extends('admin.layout')
@section('title', 'Withdrawals')

@section('content')

<div class="filter-tabs">
    <button class="btn btn-primary" id="tab-pending"  onclick="filterWd('pending')">⏳ Pending</button>
    <button class="btn btn-outline" id="tab-approved" onclick="filterWd('approved')">✅ Approved</button>
    <button class="btn btn-outline" id="tab-rejected" onclick="filterWd('rejected')">❌ Rejected</button>
    <button class="btn btn-outline" id="tab-"         onclick="filterWd('')">📋 All</button>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>#</th><th>User</th><th>Amount</th><th>Method</th><th>Account</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody id="wdTable">
                <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px;">Loading...</td></tr>
            </tbody>
        </table>
    </div>
    <div id="wdPagination" style="display:flex;justify-content:center;gap:6px;margin-top:14px;flex-wrap:wrap;"></div>
</div>

<!-- ACCOUNT DETAILS MODAL -->
<div class="modal-overlay" id="accModal" onclick="closeModal('accModal')">
    <div class="modal" onclick="event.stopPropagation()" style="max-width:400px;">
        <div class="modal-title">
            🏦 Account Details
            <button class="modal-close" onclick="closeModal('accModal')">✕</button>
        </div>
        <div id="accDetails" style="font-size:13px;line-height:2;"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let wdFilter = 'pending', wdPage = 1;

async function loadWithdrawals(page = 1) {
    wdPage = page;
    const url = `/withdrawals${wdFilter ? '?status=' + wdFilter : '?'}${wdFilter ? '&' : ''}page=${page}`;
    const data = await AAPI(url);
    const wds = data.data || [];
    const methodIcon = { bank:'🏦', upi:'📱', tron:'🔷' };
    const statusBadge = { pending:'badge-gold', approved:'badge-green', rejected:'badge-red' };

    document.getElementById('wdTable').innerHTML = wds.length
        ? wds.map(w => `<tr>
            <td style="color:var(--muted);font-size:11px;">${w.id}</td>
            <td><div style="font-weight:600;font-size:12px;">${w.user?.name || '--'}</div><div style="font-size:10px;color:var(--muted);">${w.user?.phone || ''}</div></td>
            <td style="font-weight:800;color:var(--red);">₹${w.amount}</td>
            <td style="font-size:12px;">${methodIcon[w.method] || ''} ${(w.method||'').toUpperCase()}</td>
            <td><button class="btn btn-sm btn-outline" onclick='viewAccDetails(${JSON.stringify(w.account_details)})'>👁</button></td>
            <td style="font-size:10px;color:var(--muted);">${new Date(w.created_at).toLocaleDateString('en-IN')}</td>
            <td><span class="badge ${statusBadge[w.status]}">${w.status}</span></td>
            <td style="white-space:nowrap;">
                ${w.status === 'pending'
                    ? `<button class="btn btn-sm btn-green" onclick="approveWd(${w.id})">✓</button>
                       <button class="btn btn-sm btn-red" onclick="rejectWd(${w.id})" style="margin-left:3px;">✗</button>`
                    : `<span style="color:var(--muted);font-size:11px;">Done</span>`}
            </td>
        </tr>`).join('')
        : '<tr><td colspan="8" style="text-align:center;color:var(--muted);padding:24px;">No withdrawals found</td></tr>';

    if (data.last_page > 1) {
        let html = '';
        for (let i = Math.max(1, wdPage-2); i <= Math.min(data.last_page, wdPage+2); i++) {
            html += `<button class="btn ${i===wdPage?'btn-primary':'btn-outline'}" style="padding:5px 11px;" onclick="loadWithdrawals(${i})">${i}</button>`;
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
    const html = Object.entries(details || {}).map(([k,v]) =>
        `<div style="display:flex;justify-content:space-between;border-bottom:1px solid var(--border);padding:6px 0;">
            <span style="color:var(--muted);text-transform:capitalize;">${k.replace(/_/g,' ')}</span>
            <strong>${v}</strong>
        </div>`
    ).join('');
    document.getElementById('accDetails').innerHTML = html || '<p style="color:var(--muted);">No details</p>';
    document.getElementById('accModal').classList.add('open');
}

function closeModal(id) { document.getElementById(id).classList.remove('open'); }

async function approveWd(id) {
    if (!confirm('Approve? Gateway payout will be sent now.')) return;
    try {
        const res = await AAPI(`/withdrawals/${id}/approve`, { method:'POST', body:JSON.stringify({}) });
        if (res.message && res.message.toLowerCase().includes('failed')) {
            showToast('❌ ' + res.message, 'error');
        } else if (res.gateway) {
            showToast('✅ Payout sent! ' + (res.gateway.message || ''), 'success');
        } else {
            showToast(res.message || '✅ Approved!', 'success');
        }
    } catch(e) {
        showToast('❌ Request failed: ' + e.message, 'error');
    }
    loadWithdrawals(wdPage);
}
async function rejectWd(id) {
    const note = prompt('Rejection reason (amount will be refunded):') || '';
    await AAPI(`/withdrawals/${id}/reject`, { method:'POST', body:JSON.stringify({ note }) });
    showToast('Withdrawal rejected. Amount refunded.', 'error');
    loadWithdrawals(wdPage);
}

loadWithdrawals();
</script>
@endpush
