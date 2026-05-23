@extends('admin.layout')
@section('title', 'Settings')

@section('content')

<div class="g2" style="margin-bottom:14px;">

    <!-- GAME SETTINGS -->
    <div class="card">
        <div style="font-size:13px;font-weight:700;margin-bottom:14px;">🎮 Game Settings</div>
        <div class="form-group">
            <label class="form-label">Round Duration (seconds)</label>
            <input type="number" id="s-round_duration" class="form-control" value="30">
        </div>
        <div class="form-group">
            <label class="form-label">Min Bet (₹)</label>
            <input type="number" id="s-min_bet" class="form-control" value="10">
        </div>
        <div class="form-group">
            <label class="form-label">Max Bet (₹)</label>
            <input type="number" id="s-max_bet" class="form-control" value="10000">
        </div>
        <button class="btn btn-primary" style="width:100%;" onclick="saveSettings(['round_duration','min_bet','max_bet'])">
            💾 Save Game Settings
        </button>
    </div>

    <!-- WITHDRAWAL SETTINGS -->
    <div class="card">
        <div style="font-size:13px;font-weight:700;margin-bottom:14px;">💸 Withdrawal Settings</div>
        <div class="form-group">
            <label class="form-label">Min Withdrawal (₹)</label>
            <input type="number" id="s-withdrawal_min" class="form-control" value="200">
        </div>
        <div class="form-group">
            <label class="form-label">Daily Limit (₹)</label>
            <input type="number" id="s-withdrawal_daily_limit" class="form-control" value="10000">
        </div>
        <div class="form-group">
            <label class="form-label">Min Deposit (₹)</label>
            <input type="number" id="s-deposit_min" class="form-control" value="100">
        </div>
        <button class="btn btn-primary" style="width:100%;" onclick="saveSettings(['withdrawal_min','withdrawal_daily_limit','deposit_min'])">
            💾 Save Withdrawal Settings
        </button>
    </div>

    <!-- REFERRAL SETTINGS -->
    <div class="card">
        <div style="font-size:13px;font-weight:700;margin-bottom:14px;">👥 Referral Settings</div>
        <div class="form-group">
            <label class="form-label">Level 1 Commission (%)</label>
            <input type="number" id="s-referral_commission_l1" class="form-control" value="2" step="0.1">
        </div>
        <div class="form-group">
            <label class="form-label">Level 2 Commission (%)</label>
            <input type="number" id="s-referral_commission_l2" class="form-control" value="1" step="0.1">
        </div>
        <button class="btn btn-primary" style="width:100%;" onclick="saveSettings(['referral_commission_l1','referral_commission_l2'])">
            💾 Save Referral Settings
        </button>
    </div>

    <!-- BONUS SETTINGS -->
    <div class="card">
        <div style="font-size:13px;font-weight:700;margin-bottom:14px;">🎁 Bonus & Payment</div>
        <div class="form-group">
            <label class="form-label">Daily Bonus Base (₹)</label>
            <input type="number" id="s-daily_bonus_base" class="form-control" value="10">
        </div>
        <div class="form-group">
            <label class="form-label">UPI ID</label>
            <input type="text" id="s-upi_id" class="form-control" value="colorwin@upi">
        </div>
        <div class="form-group">
            <label class="form-label">TRON Wallet</label>
            <input type="text" id="s-tron_address" class="form-control" placeholder="T...">
        </div>
        <button class="btn btn-primary" style="width:100%;" onclick="saveSettings(['daily_bonus_base','upi_id','tron_address'])">
            💾 Save Bonus Settings
        </button>
    </div>
</div>

<!-- CHANGE PASSWORD -->
<div class="card">
    <div style="font-size:13px;font-weight:700;margin-bottom:14px;">🔐 Change Admin Password</div>
    <div class="g2" style="align-items:end;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Current Password</label>
            <input type="password" id="currentPwd" class="form-control" placeholder="Current password">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">New Password</label>
            <input type="password" id="newPwd" class="form-control" placeholder="New password">
        </div>
    </div>
    <button class="btn btn-primary" style="margin-top:12px;width:100%;" onclick="changePassword()">Update Password</button>
</div>

@endsection

@push('scripts')
<script>
async function saveSettings(keys) {
    const promises = keys.map(key => {
        const val = document.getElementById('s-' + key)?.value;
        if (val === undefined) return;
        return fetch('/api/admin/settings', {
            method: 'POST',
            headers: {'Authorization':'Bearer '+ADMIN_TOKEN,'Content-Type':'application/json','Accept':'application/json'},
            body: JSON.stringify({ key, value: val })
        });
    });
    await Promise.all(promises);
    showToast('Settings saved!', 'success');
}

async function changePassword() {
    const current = document.getElementById('currentPwd').value;
    const newPwd  = document.getElementById('newPwd').value;
    if (!current || !newPwd) { showToast('Fill both fields', 'error'); return; }
    const res = await fetch('/api/admin/change-password', {
        method: 'POST',
        headers: {'Authorization':'Bearer '+ADMIN_TOKEN,'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify({ current_password: current, password: newPwd, password_confirmation: newPwd })
    });
    const data = await res.json();
    if (res.ok) {
        showToast('Password changed!', 'success');
        document.getElementById('currentPwd').value = '';
        document.getElementById('newPwd').value = '';
    } else {
        showToast(data.message || 'Failed', 'error');
    }
}
</script>
@endpush
