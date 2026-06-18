<?php $__env->startSection('title','Site Settings'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-hd">
  <div><h1>Settings</h1><div class="page-hd-sub">Configure your site</div></div>
</div>

<div class="tabs">
  <button class="tab-btn active" onclick="switchTab('general',this)">🌐 General</button>
  <button class="tab-btn" onclick="switchTab('social',this)">📱 Social Media</button>
  <button class="tab-btn" onclick="switchTab('profile',this)" id="profileTabBtn">👤 My Profile</button>
</div>


<div id="tab-general" class="tab-pane active">
  <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="settings-section">
      <div class="ss-hd">🌐 Site Information</div>
      <div class="ss-body">
        <div class="settings-row">
          <div><div class="sr-label">Site Name</div><div class="sr-desc">Shown in browser tab and navbar</div></div>
          <input type="text" name="site_name" class="field" style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e($settings['site_name'] ?? 'ADT Sports'); ?>" placeholder="ADT Sports">
        </div>
        <div class="settings-row">
          <div><div class="sr-label">Tagline</div><div class="sr-desc">Subtitle shown on the site</div></div>
          <input type="text" name="site_tagline"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e($settings['site_tagline'] ?? ''); ?>" placeholder="India's #1 Kabaddi Media Platform">
        </div>
        <div class="settings-row">
          <div><div class="sr-label">Contact Email</div></div>
          <input type="email" name="site_email"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e($settings['site_email'] ?? ''); ?>" placeholder="admin@adtsports.com">
        </div>
        <div class="settings-row">
          <div><div class="sr-label">Phone Number</div></div>
          <input type="text" name="site_phone"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e($settings['site_phone'] ?? ''); ?>" placeholder="+91 9979269732">
        </div>
        <div class="settings-row">
          <div><div class="sr-label">Footer Tagline</div><div class="sr-desc">Italic line shown in footer</div></div>
          <input type="text" name="footer_tagline"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e($settings['footer_tagline'] ?? ''); ?>">
        </div>
      </div>
    </div>

    <div class="settings-section">
      <div class="ss-hd">📢 Breaking News Ticker</div>
      <div class="ss-body">
        <div class="settings-row">
          <div>
            <div class="sr-label">Ticker Text</div>
            <div class="sr-desc">Scrolls across the top. Separate stories with " | "</div>
          </div>
          <textarea name="breaking_ticker" rows="3"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:13px;color:var(--ink);outline:none;resize:vertical;font-family:'Inter',sans-serif;line-height:1.6"
            placeholder="Breaking story one | Breaking story two | Another update"><?php echo e($settings['breaking_ticker'] ?? ''); ?></textarea>
        </div>
      </div>
    </div>

    <div class="settings-section">
      <div class="ss-hd">📄 Article Display</div>
      <div class="ss-body">
        <div class="settings-row">
          <div><div class="sr-label">Articles Per Page</div><div class="sr-desc">Homepage pagination count</div></div>
          <input type="number" name="articles_per_page" min="5" max="50"
            style="width:100px;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e($settings['articles_per_page'] ?? 10); ?>">
        </div>
      </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg">💾 Save Settings</button>
  </form>
</div>


<div id="tab-social" class="tab-pane">
  <form action="<?php echo e(route('admin.settings.update')); ?>" method="POST">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="settings-section">
      <div class="ss-hd">📱 Social Media Links</div>
      <div class="ss-body">
        <?php $__currentLoopData = ['facebook_url'=>'📘 Facebook URL','instagram_url'=>'📸 Instagram URL','youtube_url'=>'▶️ YouTube URL','twitter_url'=>'🐦 Twitter/X URL']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="settings-row">
          <div><div class="sr-label"><?php echo e($label); ?></div></div>
          <input type="url" name="<?php echo e($key); ?>"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e($settings[$key] ?? ''); ?>" placeholder="https://...">
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg">💾 Save Social Links</button>
  </form>
</div>


<div id="tab-profile" class="tab-pane">
  <form action="<?php echo e(route('admin.profile.update')); ?>" method="POST">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
    <div class="settings-section">
      <div class="ss-hd">👤 My Account — <?php echo e(auth()->user()->name); ?></div>
      <div class="ss-body">
        <div class="settings-row">
          <div><div class="sr-label">Display Name</div></div>
          <input type="text" name="name"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e(auth()->user()->name); ?>" required>
        </div>
        <div class="settings-row">
          <div><div class="sr-label">Email Address</div></div>
          <input type="email" name="email"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            value="<?php echo e(auth()->user()->email); ?>" required>
        </div>
        <div class="settings-row">
          <div><div class="sr-label">New Password</div><div class="sr-desc">Leave blank to keep current password</div></div>
          <input type="password" name="password"
            style="width:100%;background:var(--card);border:1px solid var(--border);border-radius:8px;padding:10px 13px;font-size:14px;color:var(--ink);outline:none"
            placeholder="Min 8 characters…">
        </div>
        <div class="settings-row">
          <div><div class="sr-label">Role</div></div>
          <div style="padding-top:10px">
            <span class="badge badge-<?php echo e(auth()->user()->role); ?>"><?php echo e(auth()->user()->role); ?></span>
          </div>
        </div>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-lg">✓ Update Profile</button>
  </form>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Open profile tab directly if #profile hash
if(window.location.hash==='#profile'){
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  document.getElementById('profileTabBtn').classList.add('active');
  document.querySelectorAll('.tab-pane').forEach(p=>p.classList.remove('active'));
  document.getElementById('tab-profile').classList.add('active');
}
function switchTab(id,btn){
  document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.tab-pane').forEach(p=>p.classList.remove('active'));
  document.getElementById('tab-'+id).classList.add('active');
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>