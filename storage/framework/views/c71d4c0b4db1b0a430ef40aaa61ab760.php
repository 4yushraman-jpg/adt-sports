<?php $__env->startSection('title','Team Members'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-hd">
  <div><h1>Team Members</h1><div class="page-hd-sub"><?php echo e($users->count()); ?> members</div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

  <div class="table-wrap">
    <div class="table-hd"><h3>All Users</h3></div>
    <table>
      <thead><tr><th>Member</th><th>Email</th><th>Role</th><th>Articles</th><th>Last Login</th><th>Actions</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <div class="user-av" style="background:<?php echo e($u->id===1?'var(--brand)':'#4A4640'); ?>"><?php echo e($u->initials); ?></div>
              <span style="font-weight:500;color:var(--ink)"><?php echo e($u->name); ?></span>
            </div>
          </td>
          <td style="font-size:12px;color:var(--ink3)"><?php echo e($u->email); ?></td>
          <td><span class="badge badge-<?php echo e($u->role); ?>"><?php echo e($u->role); ?></span></td>
          <td style="font-weight:500"><?php echo e($u->articles_count); ?></td>
          <td style="font-size:11px;color:var(--ink3)">
            <?php echo e($u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never'); ?>

          </td>
          <td>
            <?php if($u->id !== auth()->id()): ?>
              <form action="<?php echo e(route('admin.users.destroy',$u)); ?>" method="POST" style="display:inline"
                    onsubmit="return confirm('Remove <?php echo e($u->name); ?> from the team?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger btn-sm">🗑️ Remove</button>
              </form>
            <?php else: ?>
              <span style="font-size:12px;color:var(--ink3)">You</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" style="text-align:center;padding:24px;color:var(--ink3)">No team members yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="panel-card">
    <h4>Add Team Member</h4>
    <form action="<?php echo e(route('admin.users.store')); ?>" method="POST">
      <?php echo csrf_field(); ?>
      <div class="field">
        <label>Full Name *</label>
        <input type="text" name="name" required placeholder="Full name" value="<?php echo e(old('name')); ?>">
      </div>
      <div class="field">
        <label>Email Address *</label>
        <input type="email" name="email" required placeholder="email@example.com" value="<?php echo e(old('email')); ?>">
      </div>
      <div class="field">
        <label>Password *</label>
        <input type="password" name="password" required placeholder="Min 8 characters">
      </div>
      <div class="field" style="margin-bottom:16px">
        <label>Role</label>
        <select name="role">
          <option value="editor">Editor — Write & manage own articles</option>
          <option value="admin">Admin — Full access to everything</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%">+ Add Member</button>
    </form>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports-laravel\resources\views/admin/users/index.blade.php ENDPATH**/ ?>