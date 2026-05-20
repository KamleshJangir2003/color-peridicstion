@extends('layouts.app')
@section('title', 'Deposit')

@push('styles')
<style>
.method-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 20px; }
.method-card {
    background: var(--bg);
    border: 2px solid var(--border);
    border-radius: 14px;
    padding: 16px 8px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}
.method-card:hover, .method-card.selected {
    border-color: var(--primary);
    background: rgba(124,58,237,0.1);
}
.method-card .icon { font-size: 28px; margin-bottom: 6px; }
.method-card .name { font-size: 12px; font-weight: 600; }

.amount-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px; margin-bottom: 14px; }
.amount-card {
    background: var(--bg);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    font-size: 14px;
    font-weight: 700;
    transition: all 0.2s;
}
.amount-card:hover, .amount-card.selected {
    border-color: var(--primary);
    color: var(--primary);
    background: rgba(124,58,237,0.1);
}

.deposit-history-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.deposit-history-item:last-child { border-bottom: none; }
</style>
@endpush

@section('content')

<div class="card">
    <div style="font-size:16px; font-weight:700; margin-bottom:16px;">💰 Add Money</div>

    <!-- METHOD -->
    <div style="font-size:13px; color:var(--muted); margin-bottom:10px;">Select Payment Method</div>
    <div class="method-grid">
        <div class="method-card selected" onclick="selectMethod('upi', this)">
            <div class="icon">📱</div>
            <div class="name">UPI</div>
        </div>
        <div class="method-card" onclick="selectMethod('qr', this)">
            <div class="icon">📷</div>
            <div class="name">QR Code</div>
        </div>
        <div class="method-card" onclick="selectMethod('tron_usdt', this)">
            <div class="icon">🔷</div>
            <div class="name">USDT</div>
        </div>
    </div>

    <!-- UPI DETAILS (shown when UPI selected) -->
    <div id="upiDetails" style="background:var(--bg); border-radius:12px; padding:14px; margin-bottom:16px; text-align:center;">
        <div style="font-size:12px; color:var(--muted); margin-bottom:6px;">Pay to UPI ID</div>
        <div style="font-size:18px; font-weight:800; color:var(--gold); margin-bottom:8px;" id="upiId">colorwin@upi</div>
        <button onclick="copyUpi()" style="background:rgba(124,58,237,0.2); border:1px solid var(--primary); border-radius:8px; padding:6px 16px; color:var(--primary); font-size:12px; cursor:pointer;">
            📋 Copy UPI ID
        </button>
    </div>

    <!-- AMOUNT -->
    <div style="font-size:13px; color:var(--muted); margin-bottom:10px;">Select Amount</div>
    <div class="amount-grid">
        @foreach([100, 200, 500, 1000, 2000, 5000] as $amt)
        <div class="amount-card" onclick="selectDepositAmount({{ $amt }}, this)">₹{{ $amt }}</div>
        @endforeach
    </div>
    <div class="form-group">
        <input type="number" id="depositAmount" class="form-control" placeholder="Or enter custom amount" min="100">
    </div>

    <!-- TRANSACTION ID -->
    <div class="form-group">
        <label class="form-label">Transaction ID / UTR Number</label>
        <input type="text" id="txnId" class="form-control" placeholder="Enter transaction ID after payment">
    </div>

    <!-- SCREENSHOT -->
    <div class="form-group">
        <label class="form-label">Upload Screenshot (Optional)</label>
        <input type="file" id="screenshot" accept="image/*" class="form-control" style="padding:10px;">
    </div>

    <button class="btn btn-primary" id="depositBtn" onclick="submitDeposit()">
        <i class="fas fa-paper-plane"></i> Submit Deposit Request
    </button>
</div>

<!-- DEPOSIT HISTORY -->
<div class="card">
    <div style="font-size:14px; font-weight:700; margin-bottom:12px;">Recent Deposits</div>
    <div id="depositHistory">
        <div style="text-align:center; color:var(--muted); padding:16px;">Loading...</div>
    </div>
</div>

@endsection

@push('scripts')
<script>
if (!localStorage.getItem('token')) window.location.href = '/login';
const API = (path, opts={}) => fetch('/api' + path, {
    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token'), 'Accept': 'application/json' },
    ...opts
}).then(r => r.json());

let selectedMethod = 'upi';

function selectMethod(method, el) {
    selectedMethod = method;
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('upiDetails').style.display = method === 'upi' || method === 'qr' ? 'block' : 'none';
    if (method === 'tron_usdt') {
        document.getElementById('upiId').textContent = 'TRX: TColorWin123456789USDT';
    } else {
        document.getElementById('upiId').textContent = 'colorwin@upi';
    }
}

function selectDepositAmount(amt, el) {
    document.querySelectorAll('.amount-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('depositAmount').value = amt;
}

function copyUpi() {
    navigator.clipboard.writeText(document.getElementById('upiId').textContent);
    showToast('UPI ID copied!', 'success');
}

async function submitDeposit() {
    const amount = document.getElementById('depositAmount').value;
    const txnId  = document.getElementById('txnId').value;
    const file   = document.getElementById('screenshot').files[0];

    if (!amount || amount < 100) { showToast('Minimum deposit ₹100', 'error'); return; }
    if (!txnId) { showToast('Enter transaction ID', 'error'); return; }

    const btn = document.getElementById('depositBtn');
    btn.disabled = true;
    btn.textContent = 'Submitting...';

    const formData = new FormData();
    formData.append('amount', amount);
    formData.append('method', selectedMethod);
    formData.append('transaction_id', txnId);
    if (file) formData.append('screenshot', file);

    try {
        const res = await fetch('/api/deposits', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token'), 'Accept': 'application/json' },
            body: formData
        });
        const data = await res.json();
        if (res.ok) {
            showToast('Deposit request submitted! Pending approval.', 'success');
            document.getElementById('txnId').value = '';
            document.getElementById('depositAmount').value = '';
            loadDepositHistory();
        } else {
            showToast(data.message || 'Failed', 'error');
        }
    } catch(e) {
        showToast('Network error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Deposit Request';
    }
}

async function loadDepositHistory() {
    const data = await API('/deposits');
    const deposits = data.data || [];
    const statusBadge = { pending: 'badge-gold', approved: 'badge-green', rejected: 'badge-red' };
    document.getElementById('depositHistory').innerHTML = deposits.length
        ? deposits.map(d => `
            <div class="deposit-history-item">
                <div>
                    <div style="font-size:13px; font-weight:600;">₹${d.amount} via ${d.method.toUpperCase()}</div>
                    <div style="font-size:11px; color:var(--muted);">${new Date(d.created_at).toLocaleString('en-IN')}</div>
                </div>
                <span class="badge ${statusBadge[d.status]}">${d.status}</span>
            </div>`).join('')
        : '<div style="text-align:center;color:var(--muted);padding:16px;">No deposits yet</div>';
}

loadDepositHistory();
</script>
@endpush
