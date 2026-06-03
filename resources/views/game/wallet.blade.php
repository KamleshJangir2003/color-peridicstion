@extends('layouts.app')
@section('title', 'My Wallet')

@push('styles')
<style>
.balance-card {
    background: linear-gradient(135deg, #7C3AED, #4F46E5);
    border-radius: 20px;
    padding: 24px;
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
}
.balance-card::after {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 150px; height: 150px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
}
.balance-label { font-size: 12px; opacity: 0.8; margin-bottom: 6px; }
.balance-total { font-size: 36px; font-weight: 900; color: #fff; margin-bottom: 16px; }
.balance-row { display: flex; gap: 12px; }
.balance-sub { flex: 1; background: rgba(255,255,255,0.1); border-radius: 10px; padding: 10px; }
.balance-sub .lbl { font-size: 10px; opacity: 0.7; margin-bottom: 2px; }
.balance-sub .val { font-size: 15px; font-weight: 700; color: #fff; }

.txn-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
}
.txn-item:last-child { border-bottom: none; }
.txn-icon {
    width: 40px; height: 40px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px; flex-shrink: 0;
}
.txn-icon.credit { background: rgba(34,197,94,0.15); }
.txn-icon.debit  { background: rgba(239,68,68,0.15); }
.txn-info { flex: 1; }
.txn-desc { font-size: 13px; font-weight: 600; }
.txn-date { font-size: 11px; color: var(--muted); margin-top: 2px; }
.txn-amount { font-size: 15px; font-weight: 700; }
.txn-amount.credit { color: var(--green); }
.txn-amount.debit  { color: var(--red); }
</style>
@endpush

@section('content')

<div class="balance-card">
    <div class="balance-label">Total Balance</div>
    <div class="balance-total" id="totalBal">₹0.00</div>
    <div class="balance-row">
        <div class="balance-sub">
            <div class="lbl">Main</div>
            <div class="val" id="wMainBal">₹0</div>
        </div>
        <div class="balance-sub">
            <div class="lbl">Winning</div>
            <div class="val" id="wWinBal">₹0</div>
        </div>
        <div class="balance-sub">
            <div class="lbl">Bonus</div>
            <div class="val" id="wBonusBal">₹0</div>
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
    <a href="{{ route('deposit') }}" style="text-decoration:none;">
        <button class="btn btn-primary" style="border-radius:12px; padding:14px;">
            <i class="fas fa-plus"></i> Deposit
        </button>
    </a>
    <a href="{{ route('withdraw') }}" style="text-decoration:none;">
        <button class="btn btn-outline" style="border-radius:12px; padding:14px;">
            <i class="fas fa-arrow-up"></i> Withdraw
        </button>
    </a>
</div>

<div class="card">
    <div class="section-title" style="font-size:13px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:12px;">
        Transaction History
    </div>
    <div id="txnList">
        <div style="text-align:center; color:var(--muted); padding:20px;">Loading...</div>
    </div>
    <div id="txnPagination" style="text-align:center; margin-top:12px;"></div>
</div>

@endsection

@push('scripts')
<script>
if (!localStorage.getItem('token')) window.location.href = '/login';
const API = (path, opts={}) => fetch('/api' + path, {
    headers: { 'Authorization': 'Bearer ' + localStorage.getItem('token'), 'Content-Type': 'application/json', 'Accept': 'application/json' },
    ...opts
}).then(r => r.json());

async function loadWallet() {
    const data = await API('/wallet/balance');
    document.getElementById('totalBal').textContent  = '₹' + parseFloat(data.total||0).toFixed(2);
    document.getElementById('wMainBal').textContent  = '₹' + parseFloat(data.main||0).toFixed(2);
    document.getElementById('wWinBal').textContent   = '₹' + parseFloat(data.winning||0).toFixed(2);
    document.getElementById('wBonusBal').textContent = '₹' + parseFloat(data.bonus||0).toFixed(2);
}

async function loadTransactions(page = 1) {
    const data = await API(`/wallet/transactions?page=${page}`);
    const txns = data.data || [];
    const icons = { credit: '⬇️', debit: '⬆️' };
    const walletColors = { main: '#7C3AED', winning: '#22C55E', bonus: '#F59E0B' };

    document.getElementById('txnList').innerHTML = txns.length
        ? txns.map(t => `
            <div class="txn-item">
                <div class="txn-icon ${t.type}">
                    ${t.type === 'credit' ? '⬇️' : '⬆️'}
                </div>
                <div class="txn-info">
                    <div class="txn-desc">${t.description || t.wallet_type + ' wallet'}</div>
                    <div class="txn-date">${new Date(t.created_at).toLocaleString('en-IN')}</div>
                </div>
                <div class="txn-amount ${t.type}">
                    ${t.type === 'credit' ? '+' : '-'}₹${parseFloat(t.amount).toFixed(2)}
                </div>
            </div>`).join('')
        : '<div style="text-align:center;color:var(--muted);padding:20px;">No transactions yet</div>';

    // Pagination
    if (data.last_page > 1) {
        let btns = '';
        if (data.current_page > 1) btns += `<button class="btn btn-outline" style="width:auto;padding:8px 16px;margin:4px;" onclick="loadTransactions(${data.current_page-1})">← Prev</button>`;
        btns += `<span style="color:var(--muted);font-size:13px;padding:8px;">${data.current_page}/${data.last_page}</span>`;
        if (data.current_page < data.last_page) btns += `<button class="btn btn-outline" style="width:auto;padding:8px 16px;margin:4px;" onclick="loadTransactions(${data.current_page+1})">Next →</button>`;
        document.getElementById('txnPagination').innerHTML = btns;
    }
}

loadWallet();
loadTransactions();
setInterval(() => { loadWallet(); loadTransactions(); }, 15000);
</script>
@endpush
