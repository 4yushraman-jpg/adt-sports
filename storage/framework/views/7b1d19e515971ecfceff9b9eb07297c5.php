<?php $__env->startSection('title','Categories'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-hd">
  <div><h1>Categories</h1><div class="page-hd-sub"><?php echo e($categories->count()); ?> categories</div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

  
  <div class="table-wrap">
    <div class="table-hd"><h3>All Categories</h3></div>
    <table>
      <thead><tr><th>Name</th><th>Slug</th><th>Color</th><th>Articles</th><th>Actions</th></tr></thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td style="font-weight:500;color:var(--ink)">
            <span style="color:<?php echo e($c->color); ?>">●</span> <?php echo e($c->name); ?>

          </td>
          <td style="font-size:12px;color:var(--ink3)"><?php echo e($c->slug); ?></td>
          <td>
            <div style="width:24px;height:24px;border-radius:50%;background:<?php echo e($c->color); ?>;border:2px solid var(--border);display:inline-block"></div>
          </td>
          <td style="font-weight:600"><?php echo e($c->article_count); ?></td>
          <td>
            <div class="actions">
              <button class="btn btn-ghost btn-sm"
                onclick="openEdit(<?php echo e($c->id); ?>,'<?php echo e(addslashes($c->name)); ?>','<?php echo e($c->color); ?>','<?php echo e(addslashes($c->description ?? '')); ?>')">
                ✏️ Edit
              </button>
              <form action="<?php echo e(route('admin.categories.destroy',$c)); ?>" method="POST" style="display:inline"
                    onsubmit="return confirm('Delete category \'<?php echo e($c->name); ?>\'?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="5" style="text-align:center;padding:24px;color:var(--ink3)">No categories yet</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  
  <div class="panel-card" id="catFormCard">
    <h4 id="catFormTitle">Add New Category</h4>
    <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST" id="catForm">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="_method" id="catMethod" value="POST">
      <input type="hidden" name="_cat_id" id="catEditId" value="">

      <div class="field">
        <label>Category Name *</label>
        <input type="text" name="name" id="catName" required placeholder="e.g. Match Updates">
      </div>
      <div class="field">
        <label>Accent Color</label>
        <div style="display:flex;align-items:center;gap:10px">
          <input type="color" name="color" id="catColor" value="#D4420A">
          <span style="font-size:12px;color:var(--ink3)">Choose a brand color for this category</span>
        </div>
      </div>
      <div class="field" style="margin-bottom:16px">
        <label>Description <span style="font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
        <textarea name="description" id="catDesc" rows="2" placeholder="Brief description…"></textarea>
      </div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn btn-primary" id="catSubmitBtn">+ Add Category</button>
        <button type="button" class="btn btn-ghost" onclick="resetCatForm()">Reset</button>
      </div>
    </form>
  </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openEdit(id, name, color, desc) {
  document.getElementById('catFormTitle').textContent = 'Edit Category';
  document.getElementById('catForm').action = '/admin/categories/' + id;
  document.getElementById('catMethod').value = 'PUT';
  document.getElementById('catEditId').value = id;
  document.getElementById('catName').value = name;
  document.getElementById('catColor').value = color;
  document.getElementById('catDesc').value = desc;
  document.getElementById('catSubmitBtn').textContent = '✓ Update Category';
  document.getElementById('catFormCard').scrollIntoView({behavior:'smooth'});
}
function resetCatForm() {
  document.getElementById('catFormTitle').textContent = 'Add New Category';
  document.getElementById('catForm').action = '<?php echo e(route("admin.categories.store")); ?>';
  document.getElementById('catMethod').value = 'POST';
  document.getElementById('catEditId').value = '';
  document.getElementById('catName').value = '';
  document.getElementById('catColor').value = '#D4420A';
  document.getElementById('catDesc').value = '';
  document.getElementById('catSubmitBtn').textContent = '+ Add Category';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports-laravel\resources\views/admin/categories/index.blade.php ENDPATH**/ ?>