<?php $__env->startSection('title', $q ? "Search: {$q}" : 'Search' . ' — ' . ($settings['site_name'] ?? 'ADT Sports')); ?>

<?php $__env->startSection('content'); ?>
<div class="wrap" style="padding-top:40px">

  
  <div style="max-width:600px;margin:0 auto 48px">
    <h1 style="font-family:var(--display);font-size:clamp(28px,4vw,40px);font-weight:800;line-height:1.2;color:var(--ink);margin-bottom:20px;text-align:center">
      Search Kabaddi Stories
    </h1>
    <form action="<?php echo e(route('search')); ?>" method="GET">
      <div style="display:flex;background:var(--surface);border:2px solid var(--rule);border-radius:50px;overflow:hidden;transition:border-color .2s;padding:4px 4px 4px 20px"
           onfocusin="this.style.borderColor='var(--brand)'" onfocusout="this.style.borderColor='var(--rule)'">
        <input type="text" name="q" value="<?php echo e($q); ?>" placeholder="Search articles, players, leagues…"
          autofocus
          style="flex:1;background:none;border:none;outline:none;font-size:15px;font-family:var(--sans);color:var(--ink);padding:8px 0">
        <button type="submit"
          style="background:var(--brand);color:#fff;border:none;border-radius:40px;padding:10px 24px;font-size:13px;font-weight:600;cursor:pointer;transition:background .15s"
          onmouseover="this.style.background='var(--brand-h)'" onmouseout="this.style.background='var(--brand)'">
          Search
        </button>
      </div>
    </form>
  </div>

  <?php if($q): ?>
  <div class="content-grid">
    <main>
      <?php if($articles->count()): ?>
      <div class="sec-hd">
        <div class="sec-hd-left">
          <div class="sec-hd-bar"></div>
          <span class="sec-hd-label"><?php echo e($articles->total()); ?> results for "<?php echo e($q); ?>"</span>
        </div>
      </div>

      <?php $__currentLoopData = $articles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route('article', $a->slug)); ?>" class="card-row" style="text-decoration:none;display:grid">
        <div>
          <span class="cr-cat" style="<?php echo e($a->category ? 'color:'.$a->category->color : ''); ?>">
            <?php echo e($a->category?->name ?? 'Article'); ?>

          </span>
          <div class="cr-title"><?php echo e($a->title); ?></div>
          <?php if($a->excerpt): ?><div class="cr-excerpt"><?php echo e($a->excerpt); ?></div><?php endif; ?>
          <div class="cr-meta">
            <span><?php echo e($a->author?->name ?? 'ADT Sports'); ?></span>
            <span class="sep"></span>
            <span><?php echo e($a->formatted_date); ?></span>
            <span class="sep"></span>
            <span><?php echo e($a->read_time); ?> read</span>
          </div>
        </div>
        <div class="cr-thumb" style="background:<?php echo e($a->cover_bg); ?>">
          <?php if($a->cover_image): ?><img src="<?php echo e($a->cover_image); ?>" style="width:100%;height:100%;object-fit:cover" alt="">
          <?php else: ?> <?php echo e($a->cover_emoji); ?> <?php endif; ?>
        </div>
      </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

      <?php if($articles->hasPages()): ?>
        <div class="pagination-wrap"><?php echo e($articles->links()); ?></div>
      <?php endif; ?>

      <?php else: ?>
      <div style="text-align:center;padding:64px 20px;color:var(--ink3)">
        <div style="font-size:44px;margin-bottom:14px">🔍</div>
        <p style="font-size:16px;margin-bottom:8px">No results for <strong style="color:var(--ink)">"<?php echo e($q); ?>"</strong></p>
        <p style="font-size:14px">Try different keywords or browse categories below.</p>
        <div class="tag-cloud" style="justify-content:center;margin-top:20px">
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('category', $cat->slug)); ?>" class="tag"><?php echo e($cat->name); ?></a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
      <?php endif; ?>
    </main>

    <aside class="sidebar-col">
      <div class="widget">
        <div class="sec-hd" style="margin-bottom:14px">
          <div class="sec-hd-left"><div class="sec-hd-bar"></div><span class="sec-hd-label">Trending</span></div>
        </div>
        <?php $__currentLoopData = $trending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('article', $t->slug)); ?>" class="card-num" style="text-decoration:none">
          <div class="cn-num">0<?php echo e($i + 1); ?></div>
          <div>
            <div class="cn-title"><?php echo e($t->title); ?></div>
            <div class="cn-meta"><?php echo e($t->category?->name); ?> · <?php echo e($t->formatted_date); ?></div>
          </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <div class="widget">
        <div class="sec-hd" style="margin-bottom:14px">
          <div class="sec-hd-left"><div class="sec-hd-bar"></div><span class="sec-hd-label">Browse Topics</span></div>
        </div>
        <div class="tag-cloud">
          <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('category', $cat->slug)); ?>" class="tag"><?php echo e($cat->name); ?></a>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </aside>
  </div>
  <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports-laravel\resources\views/frontend/search.blade.php ENDPATH**/ ?>