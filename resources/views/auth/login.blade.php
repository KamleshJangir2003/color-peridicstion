<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ColorWin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --bg:#0F172A; --card:#1E293B; --border:#334155;
            --primary:#7C3AED; --green:#22C55E; --red:#EF4444;
            --gold:#F59E0B; --text:#E2E8F0; --muted:#94A3B8;
        }
        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-box {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px 24px;
            width: 100%;
            max-width: 400px;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-logo h1 {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #7C3AED, #F59E0B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .auth-logo p { color: var(--muted); font-size: 13px; margin-top: 4px; }
        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 13px; color: var(--muted); margin-bottom: 6px; display: block; }
        .input-wrap { position: relative; }
        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px 12px 40px;
            color: var(--text);
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-control:focus { border-color: var(--primary); }
        .btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 13px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--primary); color: #fff; margin-top: 8px; }
        .btn-primary:hover { background: #9D5CF6; }
        .btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }
        .auth-footer a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .error-msg {
            background: rgba(239,68,68,0.1);
            border: 1px solid var(--red);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            color: var(--red);
            margin-bottom: 16px;
            display: none;
        }
        .tabs {
            display: flex;
            background: var(--bg);
            border-radius: 10px;
            padding: 4px;
            margin-bottom: 24px;
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            color: var(--muted);
            transition: all 0.2s;
        }
        .tab.active { background: var(--primary); color: #fff; }
    </style>
</head>
<body>
<div class="auth-box">
    <div class="auth-logo">
        <img src="/colorlogo-removebg-preview.png" alt="ColorWin" style="height:60px;width:auto;">
        <p>India's #1 Color Prediction Game</p>
    </div>

    <div class="tabs">
        <div class="tab active" onclick="switchTab('login')">Login</div>
        <div class="tab" onclick="switchTab('register')">Register</div>
    </div>

    <div id="errorMsg" class="error-msg"></div>

    <!-- LOGIN FORM -->
    <div id="loginForm">
        <div class="form-group">
            <label class="form-label">Mobile Number</label>
            <div class="input-wrap">
                <i class="fas fa-phone"></i>
                <input type="tel" id="loginPhone" class="form-control" placeholder="Enter mobile number">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" id="loginPassword" class="form-control" placeholder="Enter password">
            </div>
        </div>
        <button class="btn btn-primary" id="loginBtn" onclick="doLogin()">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
        <div style="text-align:center; margin-top:12px;">
            <a href="{{ route('forgot-password') }}" style="color:var(--muted); font-size:13px;">Forgot Password?</a>
        </div>
    </div>

    <!-- REGISTER FORM -->
    <div id="registerForm" style="display:none;">
        <div class="form-group">
            <label class="form-label">Full Name</label>
            <div class="input-wrap">
                <i class="fas fa-user"></i>
                <input type="text" id="regName" class="form-control" placeholder="Enter your name">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Mobile Number</label>
            <div class="input-wrap">
                <i class="fas fa-phone"></i>
                <input type="tel" id="regPhone" class="form-control" placeholder="Enter mobile number">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" id="regEmail" class="form-control" placeholder="Enter your email">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">OTP</label>
            <div style="display:flex; gap:8px;">
                <div class="input-wrap" style="flex:1;">
                    <i class="fas fa-key"></i>
                    <input type="text" id="regOtp" class="form-control" placeholder="6-digit OTP" maxlength="6">
                </div>
                <button class="btn btn-primary" style="width:auto; padding:12px 16px; white-space:nowrap;" id="sendOtpBtn" onclick="sendOtp()">
                    Send OTP
                </button>
            </div>
            <div style="font-size:11px;color:#94A3B8;margin-top:4px;">📧 OTP aapki email pe bheja jayega</div>
        </div>
        <div class="form-group">
            <label class="form-label">Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" id="regPassword" class="form-control" placeholder="Min 6 characters">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Confirm Password</label>
            <div class="input-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" id="regPasswordConfirm" class="form-control" placeholder="Repeat password">
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Referral Code <span style="color:#EF4444;">*</span></label>
            <div class="input-wrap">
                <i class="fas fa-gift"></i>
                <input type="text" id="regReferral" class="form-control" placeholder="Referral code required"
                    value="{{ request('ref') }}" required>
            </div>
            <div style="font-size:11px;color:#94A3B8;margin-top:4px;">⚠️ Referral code ke bina account nahi banega</div>
        </div>
        <button class="btn btn-primary" id="registerBtn" onclick="doRegister()">
            <i class="fas fa-user-plus"></i> Create Account
        </button>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab').forEach((t,i) => t.classList.toggle('active', (i===0&&tab==='login')||(i===1&&tab==='register')));
    document.getElementById('loginForm').style.display    = tab === 'login'    ? 'block' : 'none';
    document.getElementById('registerForm').style.display = tab === 'register' ? 'block' : 'none';
    document.getElementById('errorMsg').style.display = 'none';
}

function showError(msg) {
    const el = document.getElementById('errorMsg');
    el.textContent = msg;
    el.style.display = 'block';
}

async function doLogin() {
    const btn = document.getElementById('loginBtn');
    btn.disabled = true;
    btn.textContent = 'Logging in...';
    try {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                phone: document.getElementById('loginPhone').value,
                password: document.getElementById('loginPassword').value,
            })
        });
        const data = await res.json();
        if (!res.ok) { showError(data.message || 'Login failed'); return; }
        // Token save karo
        localStorage.clear();
        localStorage.setItem('token', data.token);
        // Verify save hua
        if (!localStorage.getItem('token')) {
            showError('Token save failed. Try again.');
            return;
        }
        window.location.replace('/game');
    } catch(e) {
        showError('Network error. Try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login';
    }
}

let otpCooldown = 0;
async function sendOtp() {
    if (otpCooldown > 0) return;
    const email = document.getElementById('regEmail').value.trim();
    if (!email) { showError('Pehle email address enter karein'); return; }
    const btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    btn.textContent = 'Sending...';
    try {
        const res = await fetch('/api/otp/send', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ email, type: 'register' })
        });
        const data = await res.json();
        if (res.ok) {
            otpCooldown = 60;
            const interval = setInterval(() => {
                otpCooldown--;
                btn.textContent = otpCooldown > 0 ? `${otpCooldown}s` : 'Send OTP';
                btn.disabled = otpCooldown > 0;
                if (otpCooldown <= 0) clearInterval(interval);
            }, 1000);
        } else {
            showError(data.message || 'OTP send karne mein error');
            btn.disabled = false;
            btn.textContent = 'Send OTP';
        }
    } catch(e) {
        showError('Network error. Try again.');
        btn.disabled = false;
        btn.textContent = 'Send OTP';
    }
}

async function doRegister() {
    const btn = document.getElementById('registerBtn');
    const referral = document.getElementById('regReferral').value.trim();

    if (!referral) {
        showError('Referral code required hai. Bina referral ke account nahi banega.');
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Creating account...';
    try {
        const res = await fetch('/api/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                name:                  document.getElementById('regName').value,
                phone:                 document.getElementById('regPhone').value,
                email:                 document.getElementById('regEmail').value,
                otp:                   document.getElementById('regOtp').value,
                password:              document.getElementById('regPassword').value,
                password_confirmation: document.getElementById('regPasswordConfirm').value,
                referral_code:         document.getElementById('regReferral').value,
            })
        });
        const data = await res.json();
        if (!res.ok) {
            const errors = data.errors ? Object.values(data.errors).flat().join(', ') : data.message;
            showError(errors);
            return;
        }
        localStorage.setItem('token', data.token);
        window.location.href = '/game';
    } catch(e) {
        showError('Network error. Try again.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account';
    }
}

document.addEventListener('keydown', e => {
    if (e.key === 'Enter') {
        if (document.getElementById('loginForm').style.display !== 'none') doLogin();
        else doRegister();
    }
});

function quickLogin() {
    document.getElementById('loginPhone').value    = '8888888888';
    document.getElementById('loginPassword').value = 'user@123';
    doLogin();
}

// Auto-clear old token and check
localStorage.removeItem('token');
</script>
</body>
</html>
