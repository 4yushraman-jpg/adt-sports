<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-hd">
  <div>
    <h1>Dashboard</h1>
    <div class="page-hd-sub">
      <?php $h = now()->hour; $g = $h<12?'Good morning':($h<17?'Good afternoon':'Good evening'); ?>
      <?php echo e($g); ?>, <?php echo e(explode(' ', auth()->user()->name)[0]); ?>! 👋
    </div>
  </div>
  <a href="<?php echo e(route('admin.articles.create')); ?>" class="btn btn-primary">✍️ Write Article</a>
</div>

<div class="stat-grid">
  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Total Articles</span>
      <div class="stat-icon" style="background:rgba(212,66,10,.15)">📰</div>
    </div>
    <div class="stat-value"><?php echo e(number_format($stats['total'])); ?></div>
    <div class="stat-sub">All time</div>
  </div>
  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Published</span>
      <div class="stat-icon" style="background:rgba(22,163,74,.15)">✅</div>
    </div>
    <div class="stat-value"><?php echo e(number_format($stats['published'])); ?></div>
    <div class="stat-sub">Live on site</div>
  </div>
  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Drafts</span>
      <div class="stat-icon" style="background:rgba(217,119,6,.15)">📝</div>
    </div>
    <div class="stat-value"><?php echo e(number_format($stats['drafts'])); ?></div>
    <div class="stat-sub">Unpublished</div>
  </div>
  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Total Views</span>
      <div class="stat-icon" style="background:rgba(37,99,235,.15)">👁️</div>
    </div>
    <div class="stat-value"><?php echo e(number_format($stats['total_views'])); ?></div>
    <div class="stat-sub">Across all articles</div>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
  <div class="table-wrap">
    <div class="table-hd"><h3>Recent Articles</h3><a href="<?php echo e(route('admin.articles.index')); ?>" class="btn btn-ghost btn-sm">View All</a></div>
    <table>
      <thead><tr><th>Title</th><th>Status</th><th>Date</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="td-title">
            <a href="<?php echo e(route('admin.articles.edit',$a)); ?>" style="color:var(--ink)"><?php echo e(Str::limit($a->title,44)); ?></a>
            <small><?php echo e($a->category?->name ?? '—'); ?></small>
          </td>
          <td><span class="badge badge-<?php echo e($a->status); ?>"><?php echo e($a->status); ?></span></td>
          <td style="font-size:11px;color:var(--ink3)"><?php echo e($a->created_at->format('d M Y')); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink3)">No articles yet. <a href="<?php echo e(route('admin.articles.create')); ?>" style="color:var(--brand)">Write one?</a></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="table-wrap">
    <div class="table-hd"><h3>Most Viewed</h3></div>
    <table>
      <thead><tr><th>Title</th><th>Category</th><th>Views</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $topViewed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td class="td-title"><a href="<?php echo e(route('admin.articles.edit',$a)); ?>" style="color:var(--ink)"><?php echo e(Str::limit($a->title,38)); ?></a></td>
          <td style="font-size:12px;color:var(--ink3)"><?php echo e($a->category?->name ?? '—'); ?></td>
          <td style="font-weight:600;color:var(--brand)"><?php echo e(number_format($a->views)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="3" style="text-align:center;padding:24px;color:var(--ink3)">No views yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>