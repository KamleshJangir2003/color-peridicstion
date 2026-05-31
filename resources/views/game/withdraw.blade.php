@extends('layouts.app')
@section('title', 'Withdraw')

@section('content')

<style>
.bal-card{background:linear-gradient(135deg,rgba(34,197,94,.15),rgba(34,197,94,.05));border:1px solid #22C55E;border-radius:14px;padding:18px;margin-bottom:14px;text-align:center;}
.m-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px;}
.m-btn{background:#1E293B;border:2px solid #334155;border-radius:12px;padding:16px 8px;text-align:center;cursor:pointer;transition:all .2s;}
.m-btn.on{border-color:#7C3AED;background:rgba(124,58,237,.15);}
.m-btn .ic{font-size:28px;display:block;margin-bottom:6px;}
.m-btn .nm{font-size:12px;font-weight:700;color:#E2E8F0;}
.chip-row{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;}
.chip{background:#0F172A;border:1px solid #334155;border-radius:16px;padding:6px 14px;font-size:12px;font-weight:600;cursor:pointer;color:#E2E8F0;}
.chip.on{background:#7C3AED;border-color:#7C3AED;color:#fff;}
.otp-wrap{display:flex;gap:8px;}
.otp-wrap input{flex:1;}
.send-otp{background:rgba(124,58,237,.2);border:1px solid #7C3AED;border-radius:10px;padding:11px 14px;color:#7C3AED;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;}
.send-otp:disabled{opacity:.5;}
.warn{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.4);border-radius:8px;padding:10px;font-size:12px;color:#EF4444;margin-top:8px;}
.hi{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid #334155;}
.hi:last-child{border-bottom:none;}
</style>

<!-- BALANCE -->
<div class="bal-card">
    <div style="font-size:12px;color:#94A3B8;margin-bottom:4px;">Winning Balance (Withdrawable)</div>
    <div style="font-size:34px;font-weight:900;color:#22C55E;" id="winBal">₹0.00</div>
    <div style="font-size:11px;color:#94A3B8;margin-top:4px;">Min ₹200 | Daily limit ₹10,000</div>
</div>

<div class="card">
    <div style="font-size:15px;font-weight:700;margin-bottom:16px;">💸 Withdraw Money</div>

    <!-- STEP 1: METHOD -->
    <div style="font-size:12px;color:#94A3B8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Step 1 — Select Method</div>
    <div class="m-grid">
        <div class="m-btn on" id="btn-bank" onclick="pickMethod('bank')">
            <span class="ic">🏦</span>
            <span class="nm">Bank Transfer</span>
        </div>
        <div class="m-btn" id="btn-upi" onclick="pickMethod('upi')">
            <span class="ic">📱</span>
            <span class="nm">UPI</span>
        </div>
        <div class="m-btn" id="btn-tron" onclick="pickMethod('tron')">
            <span class="ic">🔷</span>
            <span class="nm">USDT TRC20</span>
        </div>
    </div>

    <!-- BANK -->
    <div id="sec-bank">
        <div style="font-size:12px;color:#94A3B8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Bank Details</div>
        <div style="margin-bottom:10px;">
            <label style="font-size:12px;color:#94A3B8;display:block;margin-bottom:5px;">Account Holder Name</label>
            <input type="text" id="b-name" class="form-control" placeholder="As per bank records">
        </div>
        <div style="margin-bottom:10px;">
            <label style="font-size:12px;color:#94A3B8;display:block;margin-bottom:5px;">Account Number</label>
            <input type="text" id="b-acc" class="form-control" placeholder="Enter account number">
        </div>
        <div style="margin-bottom:10px;">
            <label style="font-size:12px;color:#94A3B8;display:block;margin-bottom:5px;">IFSC Code</label>
            <input type="text" id="b-ifsc" class="form-control" placeholder="e.g. SBIN0001234">
        </div>
        <div style="margin-bottom:14px;">
            <label style="font-size:12px;color:#94A3B8;display:block;margin-bottom:5px;">Bank Name</label>
            <input type="text" id="b-bank" class="form-control" placeholder="e.g. State Bank of India">
        </div>
    </div>

    <!-- UPI -->
    <div id="sec-upi" style="display:none;">
        <div style="font-size:12px;color:#94A3B8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">UPI Details</div>
        <div style="margin-bottom:10px;">
            <label style="font-size:12px;color:#94A3B8;display:block;margin-bottom:5px;">UPI ID</label>
            <input type="text" id="u-id" class="form-control" placeholder="yourname@paytm or @gpay or @ybl">
        </div>
        <div style="margin-bottom:14px;">
            <label style="font-size:12px;color:#94A3B8;display:block;margin-bottom:5px;">Account Holder Name</label>
            <input type="text" id="u-name" class="form-control" placeholder="Your full name">
        </div>
    </div>

    <!-- TRON -->
    <div id="sec-tron" style="display:none;">
        <div style="font-size:12px;color:#94A3B8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">USDT TRC20 Details</div>
        <div style="margin-bottom:10px;">
            <label style="font-size:12px;color:#94A3B8;display:block;margin-bottom:5px;">TRON Wallet Address</label>
            <input type="text" id="t-addr" class="form-control" placeholder="Enter TRC20 address (starts with T)">
        </div>
        <div class="warn" style="margin-bottom:14px;">
            ⚠️ Only TRC20 USDT address. Wrong address = funds lost permanently!
        </div>
    </div>

    <!-- STEP 2: AMOUNT -->
    <div style="font-size:12px;color:#94A3B8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Step 2 — Enter Amount</div>
    <input type="number" id="wdAmt" class="form-control" placeholder="Enter amount (Min ₹200)" min="200" style="margin-bottom:8px;">
    <div class="chip-row" id="chipRow">
        <span class="chip" onclick="setAmt(200,this)">₹200</span>
        <span class="chip" onclick="setAmt(500,this)">₹500</span>
        <span class="chip" onclick="setAmt(1000,this)">₹1000</span>
        <span class="chip" onclick="setAmt(2000,this)">₹2000</span>
        <span class="chip" onclick="setAmt(5000,this)">₹5000</span>
    </div>

    <!-- STEP 3: SECURITY -->
    <div style="font-size:12px;color:#94A3B8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;">Step 3 — OTP Verification</div>
    <div style="margin-bottom:16px;">
        <div class="otp-wrap">
            <input type="text" id="wdOtp" class="form-control" placeholder="6-digit OTP" maxlength="6">
            <button class="send-otp" id="otpBtn" onclick="sendOtp()">Send OTP</button>
        </div>
        <div style="font-size:11px;color:#94A3B8;margin-top:4px;" id="otpNote"></div>
    </div>

    <div style="background:rgba(124,58,237,0.08);border:1px solid rgba(124,58,237,0.3);border-radius:10px;padding:12px;font-size:12px;color:#94A3B8;margin-bottom:14px;">
        <i class="fas fa-info-circle" style="color:#7C3AED;"></i>
        Withdrawal will be processed automatically via MvPay. Amount will be transferred to your account within 24 hours.
    </div>
    <button onclick="doWithdraw()" id="wdBtn" style="width:100%;background:linear-gradient(135deg,#7C3AED,#9D5CF6);border:none;border-radius:12px;padding:15px;font-size:15px;font-weight:800;color:#fff;cursor:pointer;">
        <i class="fas fa-arrow-up"></i> Withdraw via MvPay
    </button>
</div>

<!-- HISTORY -->
<div class="card">
    <div style="font-size:14px;font-weight:700;margin-bottom:12px;">📋 Withdrawal History</div>
    <div id="wdHist"><div style="text-align:center;color:#94A3B8;padding:20px;">Loading...</div></div>
</div>

<script>
if (!localStorage.getItem('token')) window.location.href = '/login';

const API = (p, o={}) => fetch('/api'+p, {
    headers:{'Authorization':'Bearer '+localStorage.getItem('token'),'Content-Type':'application/json','Accept':'application/json'}, ...o
}).then(r=>r.json());

let method = 'bank';
let userEmail = '';

// Load profile
API('/profile').then(d => {
    userEmail = d.email || '';
    phone = d.phone || '';
    if (phone) document.getElementById('otpNote').textContent =
        'OTP will be sent to your registered email';
});

// Load balance
API('/wallet/balance').then(d => {
    document.getElementById('winBal').textContent = '₹' + parseFloat(d.winning||0).toFixed(2);
});

// Pick method
function pickMethod(m) {
    method = m;

    // Cards
    ['bank','upi','tron'].forEach(x => {
        document.getElementById('btn-'+x).className = 'm-btn' + (x===m ? ' on' : '');
    });

    // Sections
    document.getElementById('sec-bank').style.display  = m==='bank'  ? 'block' : 'none';
    document.getElementById('sec-upi').style.display   = m==='upi'   ? 'block' : 'none';
    document.getElementById('sec-tron').style.display  = m==='tron'  ? 'block' : 'none';
}

// Amount chips
function setAmt(a, el) {
    document.getElementById('wdAmt').value = a;
    document.querySelectorAll('.chip').forEach(c => c.classList.remove('on'));
    el.classList.add('on');
}

// OTP
let cd = 0;
async function sendOtp() {
    if (cd > 0) return;
    if (!userEmail) {
        showToast('Profile load nahi hua, dobara try karo', 'error');
        return;
    }
    const btn = document.getElementById('otpBtn');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    try {
        const r = await fetch('/api/otp/send', {
            method:'POST',
            headers:{'Authorization':'Bearer '+localStorage.getItem('token'),'Content-Type':'application/json','Accept':'application/json'},
            body: JSON.stringify({email: userEmail, type:'withdrawal'})
        });
        const d = await r.json();
        if (r.ok) {
            showToast('OTP sent to ' + userEmail, 'success');
        } else {
            showToast(d.message || 'OTP send failed', 'error');
            btn.disabled = false;
            btn.textContent = 'Send OTP';
            return;
        }
        cd = 60;
        const iv = setInterval(()=>{
            cd--;
            btn.textContent = cd > 0 ? cd+'s' : 'Send OTP';
            btn.disabled    = cd > 0;
            if (cd<=0) clearInterval(iv);
        }, 1000);
    } catch(e) {
        showToast('OTP send failed', 'error');
        btn.disabled = false;
        btn.textContent = 'Send OTP';
    }
}

// Get account details
function getAcc() {
    if (method === 'bank') return {
        name:           document.getElementById('b-name').value,
        account_number: document.getElementById('b-acc').value,
        ifsc:           document.getElementById('b-ifsc').value.toUpperCase(),
        bank_name:      document.getElementById('b-bank').value,
    };
    if (method === 'upi') return {
        upi_id: document.getElementById('u-id').value,
        name:   document.getElementById('u-name').value,
    };
    return { tron_address: document.getElementById('t-addr').value };
}

// Submit
async function doWithdraw() {
    const amt = parseFloat(document.getElementById('wdAmt').value);
    const otp = document.getElementById('wdOtp').value.trim();

    if (!amt || amt < 200)        { showToast('Minimum ₹200', 'error'); return; }
    if (otp.length !== 6)          { showToast('Enter 6-digit OTP', 'error'); return; }

    const acc = getAcc();
    if (method==='upi'  && !acc.upi_id)        { showToast('Enter UPI ID', 'error'); return; }
    if (method==='tron' && !acc.tron_address)   { showToast('Enter TRON address', 'error'); return; }
    if (method==='bank' && !acc.account_number) { showToast('Enter account number', 'error'); return; }

    const btn = document.getElementById('wdBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    try {
        const d = await API('/withdrawals', {
            method:'POST',
            body: JSON.stringify({ amount: amt, method, account_details: acc, otp })
        });
        if (d.withdrawal) {
            showToast('✅ Withdrawal submitted! Processing via MvPay.', 'success');
            document.getElementById('wdAmt').value = '';
            document.getElementById('wdOtp').value = '';
            document.querySelectorAll('.chip').forEach(c=>c.classList.remove('on'));
            API('/wallet/balance').then(b=>{ document.getElementById('winBal').textContent='₹'+parseFloat(b.winning||0).toFixed(2); });
            loadHist();
        } else {
            showToast(d.message || (d.errors ? Object.values(d.errors).flat().join(' | ') : 'Failed'), 'error');
        }
    } catch(e) { showToast('Network error', 'error'); }
    finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-arrow-up"></i> Submit Withdrawal Request';
    }
}

// History
async function loadHist() {
    const d = await API('/withdrawals');
    const w = d.data || [];
    const sb = {pending:'badge-gold', approved:'badge-green', rejected:'badge-red'};
    const ic = {bank:'🏦', upi:'📱', tron:'🔷'};
    document.getElementById('wdHist').innerHTML = w.length
        ? w.map(x=>`<div class="hi">
            <div>
                <div style="font-size:14px;font-weight:700;">${ic[x.method]||''} ₹${x.amount}</div>
                <div style="font-size:11px;color:#94A3B8;margin-top:2px;">${x.method?.toUpperCase()} &bull; ${new Date(x.created_at).toLocaleString('en-IN')}</div>
                ${x.admin_note?`<div style="font-size:11px;color:#EF4444;margin-top:2px;">Note: ${x.admin_note}</div>`:''}
            </div>
            <span class="badge ${sb[x.status]}">${x.status}</span>
          </div>`).join('')
        : '<div style="text-align:center;color:#94A3B8;padding:20px;">No withdrawals yet</div>';
}

loadHist();
</script>

@endsection
