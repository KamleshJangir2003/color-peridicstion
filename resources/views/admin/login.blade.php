<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ColorWin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        :root{--bg:#0F172A;--card:#1E293B;--border:#334155;--primary:#7C3AED;--red:#EF4444;--gold:#F59E0B;--text:#E2E8F0;--muted:#94A3B8;}
        body{background:var(--bg);color:var(--text);font-family:'Segoe UI',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;}
        .box{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:36px 28px;width:100%;max-width:420px;}
        .logo{text-align:center;margin-bottom:28px;}
        .logo .shield{width:64px;height:64px;background:linear-gradient(135deg,var(--primary),#4F46E5);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 12px;}
        .logo h1{font-size:22px;font-weight:800;}
        .logo p{color:var(--muted);font-size:13px;margin-top:4px;}
        .form-group{margin-bottom:16px;}
        .form-label{font-size:13px;color:var(--muted);margin-bottom:6px;display:block;}
        .input-wrap{position:relative;}
        .input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);font-size:14px;}
        .form-control{width:100%;background:var(--bg);border:1px solid var(--border);border-radius:10px;padding:13px 14px 13px 42px;color:var(--text);font-size:14px;outline:none;transition:border-color 0.2s;}
        .form-control:focus{border-color:var(--primary);}
        .btn{width:100%;border:none;border-radius:10px;padding:14px;font-size:15px;font-weight:700;cursor:pointer;background:linear-gradient(135deg,var(--primary),#4F46E5);color:#fff;margin-top:8px;transition:all 0.2s;}
        .btn:hover{opacity:0.9;transform:translateY(-1px);}
        .btn:disabled{opacity:0.6;cursor:not-allowed;transform:none;}
        .error{background:rgba(239,68,68,0.1);border:1px solid var(--red);border-radius:8px;padding:10px 14px;font-size:13px;color:var(--red);margin-bottom:16px;display:none;}
        .back{text-align:center;margin-top:16px;font-size:13px;color:var(--muted);}
        .back a{color:var(--primary);text-decoration:none;}
        .credentials-hint{background:rgba(124,58,237,0.1);border:1px solid rgba(124,58,237,0.3);border-radius:10px;padding:12px 14px;margin-bottom:20px;font-size:12px;color:var(--muted);}
        .credentials-hint strong{color:var(--gold);}
    </style>
</head>
<body>
<div class="box">
    <div class="logo">
        <div class="shield">🛡️</div>
        <h1>Admin Panel</h1>
        <p>ColorWin Management System</p>
    </div>

    <div id="errMsg" class="error"></div>

    <div class="form-group">
        <label class="form-label">Admin Phone Number</label>
        <div class="input-wrap">
            <i class="fas fa-phone"></i>
            <input type="tel" id="phone" class="form-control" placeholder="Enter admin phone">
        </div>
    </div>
    <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" class="form-control" placeholder="Enter password">
        </div>
    </div>

    <button class="btn" id="loginBtn" onclick="adminLogin()">
        <i class="fas fa-sign-in-alt"></i> Login to Admin Panel
    </button>

    <div class="back"><a href="/login">← User Login</a></div>
</div>

<script>
async function adminLogin() {
    const btn = document.getElementById('loginBtn');
    const err = document.getElementById('errMsg');
    err.style.display = 'none';
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';

    try {
        const res = await fetch('/api/admin/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({
                phone:    document.getElementById('phone').value,
                password: document.getElementById('password').value,
            })
        });
        const data = await res.json();
        if (!res.ok) {
            err.textContent = data.message || 'Invalid credentials';
            err.style.display = 'block';
            return;
        }
        localStorage.setItem('admin_token', data.token);
        localStorage.setItem('admin_user', JSON.stringify(data.user));
        window.location.href = '/admin/dashboard';
    } catch(e) {
        err.textContent = 'Network error. Try again.';
        err.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Login to Admin Panel';
    }
}
document.addEventListener('keydown', e => { if(e.key==='Enter') adminLogin(); });
</script>
</body>
</html>
