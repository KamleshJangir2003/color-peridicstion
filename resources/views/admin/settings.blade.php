@extends('admin.layout')
@section('title', 'Settings')

@section('content')

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

    <!-- GAME SETTINGS -->
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;">🎮 Game Settings</div>
        <div class="form-group">
            <label class="form-label">Round Duration (seconds)</label>
            <input type="number" id="s-round_duration" class="form-control" value="30">
        </div>
        <div class="form-group">
            <label class="form-label">Minimum Bet Amount (₹)</label>
            <input type="number" id="s-min_bet" class="form-control" value="10">
        </div>
        <div class="form-group">
            <label class="form-label">Maximum Bet Amount (₹)</label>
            <input type="number" id="s-max_bet" class="form-control" value="10000">
        </div>
        <button class="btn btn-primary" onclick="saveSettings(['round_duration','min_bet','max_bet'])">
            💾 Save Game Settings
        </button>
    </div>

    <!-- WITHDRAWAL SETTINGS -->
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;">💸 Withdrawal Settings</div>
        <div class="form-group">
            <label class="form-label">Minimum Withdrawal (₹)</label>
            <input type="number" id="s-withdrawal_min" class="form-control" value="200">
        </div>
        <div class="form-group">
            <label class="form-label">Daily Withdrawal Limit (₹)</label>
            <input type="number" id="s-withdrawal_daily_limit" class="form-control" value="10000">
        </div>
        <div class="form-group">
            <label class="form-label">Minimum Deposit (₹)</label>
            <input type="number" id="s-deposit_min" class="form-control" value="100">
        </div>
        <button class="btn btn-primary" onclick="saveSettings(['withdrawal_min','withdrawal_daily_limit','deposit_min'])">
            💾 Save Withdrawal Settings
        </button>
    </div>

    <!-- REFERRAL SETTINGS -->
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;">👥 Referral Settings</div>
        <div class="form-group">
            <label class="form-label">Level 1 Commission (%)</label>
            <input type="number" id="s-referral_commission_l1" class="form-control" value="2" step="0.1">
        </div>
        <div class="form-group">
            <label class="form-label">Level 2 Commission (%)</label>
            <input type="number" id="s-referral_commission_l2" class="form-control" value="1" step="0.1">
        </div>
        <button class="btn btn-primary" onclick="saveSettings(['referral_commission_l1','referral_commission_l2'])">
            💾 Save Referral Settings
        </button>
    </div>

    <!-- BONUS SETTINGS -->
    <div class="card">
        <div style="font-size:14px;font-weight:700;margin-bottom:16px;">🎁 Bonus Settings</div>
        <div class="form-group">
            <label class="form-label">Daily Bonus Base Amount (₹)</label>
            <input type="number" id="s-daily_bonus_base" class="form-control" value="10">
        </div>
        <div class="form-group">
            <label class="form-label">UPI ID for Deposits</label>
            <input type="text" id="s-upi_id" class="form-control" value="colorwin@upi">
        </div>
        <div class="form-group">
            <label class="form-label">TRON Wallet Address</label>
            <input type="text" id="s-tron_address" class="form-control" placeholder="T...">
        </div>
        <button class="btn btn-primary" onclick="saveSettings(['daily_bonus_base','upi_id','tron_address'])">
            💾 Save Bonus Settings
        </button>
    </div>
</div>

<!-- CHANGE ADMIN PASSWORD -->
<div class="card" style="margin-top:16px;">
    <div style="font-size:14px;font-weight:700;margin-bottom:16px;">🔐 Change Admin Password</div>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;align-items:end;">
        <div class="form-group" style="margin:0;">
            <label class="form-label">Current Password</label>
            <input type="password" id="currentPwd" class="form-control" placeholder="Current password">
        </div>
        <div class="form-group" style="margin:0;">
            <label class="form-label">New Password</label>
            <input type="password" id="newPwd" class="form-control" placeholder="New password">
        </div>
        <button class="btn btn-primary" onclick="changePassword()">Update Password</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Save individual settings
async function saveSettings(keys) {
    const promises = keys.map(key => {
        const val = document.getElementById('s-' + key)?.value;
        if (val === undefined) return;
        return fetch('/api/admin/settings', {
            method: 'POST',
            headers: { 'Authorization': 'Bearer ' + ADMIN_TOKEN, 'Content-Type': 'application/json', 'Accept': 'application/json' },
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
        headers: { 'Authorization': 'Bearer ' + ADMIN_TOKEN, 'Content-Type': 'application/json', 'Accept': 'application/json' },
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
