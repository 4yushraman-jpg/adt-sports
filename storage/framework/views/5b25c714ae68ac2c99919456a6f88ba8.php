<?php $__env->startSection('title','Media Library'); ?>
<?php $__env->startSection('content'); ?>

<div class="page-hd">
  <div><h1>Media Library</h1><div class="page-hd-sub"><?php echo e($media->total()); ?> files</div></div>
  <label for="uploadInput" class="btn btn-primary" style="cursor:pointer">📤 Upload Images</label>
  <input type="file" id="uploadInput" multiple accept="image/*" style="display:none" onchange="uploadFiles(this.files)">
</div>

<div class="drop-zone" id="dropZone">
  <div class="drop-icon">🖼️</div>
  <p style="color:var(--ink2);font-size:14px;margin-bottom:4px">Drag & drop images here, or click Upload above</p>
  <p style="color:var(--ink3);font-size:12px">PNG, JPG, GIF, WebP — max 10MB each</p>
</div>

<div id="uploadProgress" style="display:none;margin-bottom:16px">
  <div style="background:var(--card);border:1px solid var(--border);border-radius:8px;padding:12px 16px;font-size:13px;color:var(--ink2)">
    ⏳ Uploading files… please wait.
  </div>
</div>

<div class="media-grid" id="mediaGrid">
  <?php $__empty_1 = true; $__currentLoopData = $media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
  <div class="media-item" id="media-<?php echo e($m->id); ?>">
    <div class="media-thumb">
      <img src="<?php echo e($m->url); ?>" alt="<?php echo e($m->original_name); ?>" loading="lazy"
           onerror="this.style.display='none';this.parentNode.innerHTML='🖼️'">
    </div>
    <div class="media-info">
      <div class="media-name" title="<?php echo e($m->original_name); ?>"><?php echo e($m->original_name); ?></div>
      <div class="media-size"><?php echo e($m->formatted_size); ?></div>
      <div style="display:flex;gap:4px;margin-top:7px">
        <button class="btn btn-ghost btn-sm" style="font-size:10px;padding:3px 8px;flex:1"
          onclick="copyUrl('<?php echo e($m->url); ?>')">📋 Copy URL</button>
        <form action="<?php echo e(route('admin.media.destroy',$m)); ?>" method="POST" style="display:inline"
              onsubmit="return confirm('Delete this image?')">
          <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
          <button type="submit" class="btn btn-danger btn-sm" style="font-size:10px;padding:3px 8px">🗑️</button>
        </form>
      </div>
    </div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
  <div class="empty-state" style="grid-column:1/-1">
    <div style="font-size:44px;margin-bottom:12px">🖼️</div>
    <p>No images uploaded yet. Upload your first image above.</p>
  </div>
  <?php endif; ?>
</div>

<?php if($media->hasPages()): ?>
  <div style="padding:16px 0"><?php echo e($media->links()); ?></div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Drag & drop
const dz = document.getElementById('dropZone');
dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('over'); });
dz.addEventListener('dragleave', () => dz.classList.remove('over'));
dz.addEventListener('drop', e => {
  e.preventDefault(); dz.classList.remove('over');
  uploadFiles(e.dataTransfer.files);
});
dz.addEventListener('click', () => document.getElementById('uploadInput').click());

async function uploadFiles(files) {
  if (!files.length) return;
  document.getElementById('uploadProgress').style.display = 'block';
  for (const file of files) {
    const fd = new FormData();
    fd.append('file', file);
    fd.append('_token', '<?php echo e(csrf_token()); ?>');
    try {
      const r = await fetch('<?php echo e(route("admin.media.upload")); ?>', {method:'POST',body:fd});
      const d = await r.json();
      if (d.url) prependMedia(d);
    } catch(e) { console.error(e); }
  }
  document.getElementById('uploadProgress').style.display = 'none';
}

function prependMedia(d) {
  const grid = document.getElementById('mediaGrid');
  const div = document.createElement('div');
  div.className = 'media-item';
  div.innerHTML = `
    <div class="media-thumb"><img src="${d.url}" style="width:100%;height:100%;object-fit:cover"></div>
    <div class="media-info">
      <div class="media-name">${d.name}</div>
      <div class="media-size">Just uploaded</div>
      <div style="margin-top:7px">
        <button class="btn btn-ghost btn-sm" style="font-size:10px;padding:3px 8px;width:100%" onclick="copyUrl('${d.url}')">📋 Copy URL</button>
      </div>
    </div>`;
  grid.prepend(div);
}

function copyUrl(url) {
  const full = window.location.origin + url;
  navigator.clipboard.writeText(full)
    .then(() => { alert('URL copied: ' + full); })
    .catch(() => prompt('Copy this URL:', full));
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports-laravel\resources\views/admin/media/index.blade.php ENDPATH**/ ?>