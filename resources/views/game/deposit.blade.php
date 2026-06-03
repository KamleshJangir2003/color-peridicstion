@extends('layouts.app')
@section('title', 'Deposit')

@push('styles')
<style>
.method-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; }
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
.info-box {
    background: rgba(124,58,237,0.08);
    border: 1px solid rgba(124,58,237,0.3);
    border-radius: 10px;
    padding: 12px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 16px;
}
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
        <div class="method-card" onclick="selectMethod('bank', this)">
            <div class="icon">🏦</div>
            <div class="name">Bank Transfer</div>
        </div>
    </div>

    <!-- INFO -->
    <div class="info-box">
        <i class="fas fa-info-circle" style="color:var(--primary);"></i>
        After clicking <b>Pay Now</b>, you will be redirected to the secure payment gateway. Amount will be credited automatically after successful payment.
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

    <button class="btn btn-primary" id="depositBtn" onclick="submitDeposit()">
        <i class="fas fa-lock"></i> Pay Now via MvPay
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
    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token'), 'Accept': 'application/json', 'Content-Type': 'application/json' },
    ...opts
}).then(r => r.json());

let selectedMethod = 'upi';

function selectMethod(method, el) {
    selectedMethod = method;
    document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
}

function selectDepositAmount(amt, el) {
    document.querySelectorAll('.amount-card').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    document.getElementById('depositAmount').value = amt;
}

async function submitDeposit() {
    const amount = document.getElementById('depositAmount').value;

    if (!amount || amount < 100) { showToast('Minimum deposit ₹100', 'error'); return; }

    const btn = document.getElementById('depositBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    try {
        const data = await API('/deposits', {
            method: 'POST',
            body: JSON.stringify({ amount: parseFloat(amount), method: selectedMethod })
        });

        if (data.payment_url) {
            showToast('Redirecting to payment gateway...', 'success');
            setTimeout(() => { window.location.href = data.payment_url; }, 1000);
        } else if (data.deposit) {
            showToast('Deposit request created! Awaiting payment confirmation.', 'success');
            loadDepositHistory();
        } else {
            showToast(data.message || (data.errors ? Object.values(data.errors).flat().join(' | ') : 'Failed'), 'error');
        }
    } catch(e) {
        showToast('Network error', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock"></i> Pay Now via MvPay';
    }
}

async function loadDepositHistory() {
    const data = await API('/deposits');
    const deposits = data.data || [];
    const statusBadge = { pending: 'badge-gold', approved: 'badge-green', rejected: 'badge-red' };
    const methodIcon  = { upi: '📱', bank: '🏦', qr: '📷' };
    document.getElementById('depositHistory').innerHTML = deposits.length
        ? deposits.map(d => `
            <div class="deposit-history-item">
                <div>
                    <div style="font-size:13px; font-weight:600;">${methodIcon[d.method]||''} ₹${d.amount} via ${d.method.toUpperCase()}</div>
                    <div style="font-size:11px; color:var(--muted);">${new Date(d.created_at).toLocaleString('en-IN')}</div>
                </div>
                <span class="badge ${statusBadge[d.status]}">${d.status}</span>
            </div>`).join('')
        : '<div style="text-align:center;color:var(--muted);padding:16px;">No deposits yet</div>';
}

loadDepositHistory();
setInterval(loadDepositHistory, 15000);
</script>
@endpush
