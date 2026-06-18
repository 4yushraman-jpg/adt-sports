<?php $__env->startSection('title','All Articles'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-hd">
  <div><h1>All Articles</h1><div class="page-hd-sub"><?php echo e($articles->total()); ?> total</div></div>
  <a href="<?php echo e(route('admin.articles.create')); ?>" class="btn btn-primary">✍️ New Article</a>
</div>

<div class="table-wrap">
  <div class="table-hd">
    <h3>Articles</h3>
    <div class="table-filters">
      <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <input type="text" name="search" class="search-input" placeholder="Search title…" value="<?php echo e(request('search')); ?>">
        <select name="status" class="filter-select" onchange="this.form.submit()">
          <option value="">All Status</option>
          <option value="published" <?php echo e(request('status')=='published'?'selected':''); ?>>Published</option>
          <option value="draft"     <?php echo e(request('status')=='draft'?'selected':''); ?>>Drafts</option>
        </select>
        <select name="category" class="filter-select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($c->id); ?>" <?php echo e(request('category')==$c->id?'selected':''); ?>><?php echo e($c->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        <?php if(request()->hasAny(['search','status','category'])): ?>
          <a href="<?php echo e(route('admin.articles.index')); ?>" class="btn btn-ghost btn-sm">✕ Clear</a>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <table>
    <thead>
      <tr><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Views</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr>
        <td class="td-title">
          <a href="<?php echo e(route('admin.articles.edit',$a)); ?>" style="color:var(--ink)"><?php echo e(Str::limit($a->title,55)); ?></a>
          <small><?php echo e($a->slug); ?></small>
        </td>
        <td>
          <?php if($a->category): ?>
            <span style="color:<?php echo e($a->category->color); ?>;font-size:12px;font-weight:500">● <?php echo e($a->category->name); ?></span>
          <?php else: ?> <span style="color:var(--ink3)">—</span> <?php endif; ?>
        </td>
        <td style="font-size:12px;color:var(--ink3)"><?php echo e($a->author?->name ?? '—'); ?></td>
        <td>
          <span class="badge badge-<?php echo e($a->status); ?>"><?php echo e($a->status); ?></span>
          <?php if($a->featured): ?> <span style="font-size:12px" title="Featured">⭐</span> <?php endif; ?>
          <?php if($a->breaking): ?> <span style="font-size:12px" title="Breaking">🔴</span> <?php endif; ?>
        </td>
        <td style="font-weight:500"><?php echo e(number_format($a->views)); ?></td>
        <td style="font-size:11px;color:var(--ink3);white-space:nowrap"><?php echo e($a->created_at->format('d M Y')); ?></td>
        <td>
          <div class="actions">
            <a href="<?php echo e(route('admin.articles.edit',$a)); ?>" class="btn btn-ghost btn-sm">✏️ Edit</a>
            <?php if($a->status==='draft'): ?>
              <form action="<?php echo e(route('admin.articles.update',$a)); ?>" method="POST" style="display:inline">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <input type="hidden" name="title" value="<?php echo e($a->title); ?>">
                <input type="hidden" name="status" value="published">
                <button type="submit" class="btn btn-success btn-sm" title="Publish">🚀</button>
              </form>
            <?php else: ?>
              <form action="<?php echo e(route('admin.articles.update',$a)); ?>" method="POST" style="display:inline">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <input type="hidden" name="title" value="<?php echo e($a->title); ?>">
                <input type="hidden" name="status" value="draft">
                <button type="submit" class="btn btn-amber btn-sm" title="Unpublish">📝</button>
              </form>
            <?php endif; ?>
            <form action="<?php echo e(route('admin.articles.destroy',$a)); ?>" method="POST" style="display:inline"
                  onsubmit="return confirm('Delete this article? Cannot be undone.')">
              <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
            </form>
          </div>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--ink3)">
        No articles found. <a href="<?php echo e(route('admin.articles.create')); ?>" style="color:var(--brand)">Write one?</a>
      </td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <?php if($articles->hasPages()): ?>
    <div style="padding:12px 16px;border-top:1px solid var(--border)"><?php echo e($articles->links()); ?></div>
  <?php endif; ?>
  <div style="padding:8px 16px;font-size:11px;color:var(--ink3);border-top:1px solid var(--border2)">
    Showing <?php echo e($articles->firstItem()); ?>–<?php echo e($articles->lastItem()); ?> of <?php echo e($articles->total()); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports-laravel\resources\views/admin/articles/index.blade.php ENDPATH**/ ?>