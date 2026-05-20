<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - ColorWin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root { --bg:#0F172A; --card:#1E293B; --border:#334155; --primary:#7C3AED; --green:#22C55E; --red:#EF4444; --gold:#F59E0B; --text:#E2E8F0; --muted:#94A3B8; }
        body { background:var(--bg); color:var(--text); font-family:'Segoe UI',sans-serif; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
        .box { background:var(--card); border:1px solid var(--border); border-radius:20px; padding:32px 24px; width:100%; max-width:400px; }
        .logo { text-align:center; margin-bottom:24px; }
        .logo h1 { font-size:24px; font-weight:800; background:linear-gradient(135deg,#7C3AED,#F59E0B); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }
        .logo p { color:var(--muted); font-size:13px; margin-top:4px; }
        .form-group { margin-bottom:14px; }
        .form-label { font-size:13px; color:var(--muted); margin-bottom:6px; display:block; }
        .form-control { width:100%; background:var(--bg); border:1px solid var(--border); border-radius:10px; padding:12px 14px; color:var(--text); font-size:14px; outline:none; }
        .form-control:focus { border-color:var(--primary); }
        .btn { width:100%; border:none; border-radius:10px; padding:13px; font-size:15px; font-weight:700; cursor:pointer; background:var(--primary); color:#fff; margin-top:8px; }
        .btn:disabled { opacity:0.6; cursor:not-allowed; }
        .back { text-align:center; margin-top:16px; font-size:13px; color:var(--muted); }
        .back a { color:var(--primary); text-decoration:none; }
        .error { background:rgba(239,68,68,0.1); border:1px solid var(--red); border-radius:8px; padding:10px 14px; font-size:13px; color:var(--red); margin-bottom:14px; display:none; }
        .success { background:rgba(34,197,94,0.1); border:1px solid var(--green); border-radius:8px; padding:10px 14px; font-size:13px; color:var(--green); margin-bottom:14px; display:none; }
    </style>
</head>
<body>
<div class="box">
    <div class="logo">
        <h1>🔐 Reset Password</h1>
        <p>Enter your mobile number to reset</p>
    </div>
    <div id="errMsg" class="error"></div>
    <div id="sucMsg" class="success"></div>
    <div class="form-group">
        <label class="form-label">Mobile Number</label>
        <input type="tel" id="phone" class="form-control" placeholder="Enter registered mobile">
    </div>
    <div class="form-group">
        <label class="form-label">OTP</label>
        <div style="display:flex;gap:8px;">
            <input type="text" id="otp" class="form-control" placeholder="6-digit OTP" maxlength="6" style="flex:1;">
            <button onclick="sendOtp()" id="otpBtn" style="background:rgba(124,58,237,0.2);border:1px solid var(--primary);border-radius:10px;padding:12px 14px;color:var(--primary);font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">Send OTP</button>
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">New Password</label>
        <input type="password" id="password" class="form-control" placeholder="Min 6 characters">
    </div>
    <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" id="passwordConfirm" class="form-control" placeholder="Repeat password">
    </div>
    <button class="btn" id="resetBtn" onclick="resetPassword()">Reset Password</button>
    <div class="back"><a href="/login">← Back to Login</a></div>
</div>
<script>
let cooldown = 0;
async function sendOtp() {
    if (cooldown > 0) return;
    const phone = document.getElementById('phone').value;
    if (!phone) { document.getElementById('errMsg').textContent='Enter phone number'; document.getElementById('errMsg').style.display='block'; return; }
    const res = await fetch('/api/otp/send', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body:JSON.stringify({phone, type:'forgot_password'}) });
    const data = await res.json();
    if (data.otp) document.getElementById('otp').value = data.otp;
    cooldown = 60;
    const btn = document.getElementById('otpBtn');
    const iv = setInterval(() => { cooldown--; btn.textContent = cooldown > 0 ? `${cooldown}s` : 'Send OTP'; if(cooldown<=0) clearInterval(iv); }, 1000);
}
async function resetPassword() {
    const btn = document.getElementById('resetBtn');
    btn.disabled = true; btn.textContent = 'Resetting...';
    const res = await fetch('/api/forgot-password', { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'}, body:JSON.stringify({ phone:document.getElementById('phone').value, otp:document.getElementById('otp').value, password:document.getElementById('password').value, password_confirmation:document.getElementById('passwordConfirm').value }) });
    const data = await res.json();
    if (res.ok) {
        document.getElementById('sucMsg').textContent = 'Password reset! Redirecting...';
        document.getElementById('sucMsg').style.display = 'block';
        setTimeout(() => window.location.href = '/login', 2000);
    } else {
        document.getElementById('errMsg').textContent = data.message || 'Failed';
        document.getElementById('errMsg').style.display = 'block';
        btn.disabled = false; btn.textContent = 'Reset Password';
    }
}
</script>
</body>
</html>
