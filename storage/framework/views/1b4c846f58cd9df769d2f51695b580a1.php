<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login — ADT Sports</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:#0F0E0D;color:#F5F0EB;height:100vh;display:flex;align-items:center;justify-content:center;-webkit-font-smoothing:antialiased}
.box{width:100%;max-width:380px;padding:0 24px}
.logo{display:flex;align-items:center;gap:10px;justify-content:center;margin-bottom:36px}
.logo-dot{width:44px;height:44px;border-radius:50%;background:#D4420A;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;color:#fff}
.logo-name{font-size:20px;font-weight:700}.logo-name em{font-style:normal;color:#D4420A}
h2{font-size:22px;font-weight:700;text-align:center;margin-bottom:6px}
.sub{font-size:13px;color:#78716C;text-align:center;margin-bottom:28px}
.field{margin-bottom:14px}
.field label{display:block;font-size:11px;font-weight:600;letter-spacing:.8px;text-transform:uppercase;color:#6B6560;margin-bottom:7px}
.field input{width:100%;background:#1C1917;border:1px solid rgba(255,255,255,.07);border-radius:8px;padding:11px 14px;font-size:14px;color:#F5F0EB;outline:none;transition:border-color .2s;font-family:'Inter',sans-serif}
.field input:focus{border-color:#D4420A}
.field input::placeholder{color:#44403C}
.err{background:rgba(220,38,38,.12);border:1px solid rgba(220,38,38,.25);color:#FCA5A5;font-size:12px;padding:10px 14px;border-radius:6px;margin-bottom:14px}
.btn-login{width:100%;background:#D4420A;color:#fff;padding:12px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;border:none;transition:background .15s;margin-top:4px;font-family:'Inter',sans-serif}
.btn-login:hover{background:#B83808}
.hint{margin-top:20px;background:#1C1917;border:1px solid rgba(255,255,255,.06);border-radius:8px;padding:12px 16px;font-size:12px;color:#6B6560;text-align:center;line-height:1.6}
.hint strong{color:#A8A09A}
</style>
</head>
<body>
<div class="box">
  <div class="logo">
    <img src="/uploads/logo.png" width="45" height="45" alt="ADT">
    <span class="logo-name"><em>ADT</em> Sports Admin</span>
  </div>
  <h2>Welcome back</h2>
  <p class="sub">Sign in to manage your Kabaddi media platform</p>

  <?php if($errors->any()): ?>
    <div class="err"><?php echo e($errors->first()); ?></div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="err"><?php echo e(session('error')); ?></div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('admin.login.post')); ?>">
    <?php echo csrf_field(); ?>
    <div class="field">
      <label>Email Address</label>
      <input type="email" name="email" value="<?php echo e(old('email','admin@adtsports.com')); ?>" required autofocus placeholder="admin@adtsports.com">
    </div>
    <div class="field">
      <label>Password</label>
      <input type="password" name="password" required placeholder="••••••••••">
    </div>
    <button type="submit" class="btn-login">Sign In →</button>
  </form>
</div>
</body>
</html>
<?php /**PATH C:\laragon\www\adt-sports\resources\views/auth/login.blade.php ENDPATH**/ ?>