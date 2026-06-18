<?php $__env->startSection('title', $article->meta_title ?: $article->title . ' — ' . ($settings['site_name'] ?? 'ADT Sports')); ?>
<?php $__env->startSection('meta_desc', $article->meta_desc ?: $article->excerpt); ?>

<?php $__env->startSection('content'); ?>
<div class="article-wrap">

  
  <article class="article-main">
    <a href="<?php echo e(route('home')); ?>" class="back-btn">← Back to Home</a>

    <div class="art-hero-img" style="background:<?php echo e($article->cover_bg); ?>">
      <?php if($article->cover_image): ?>
        <img src="<?php echo e($article->cover_image); ?>" alt="<?php echo e($article->title); ?>">
      <?php else: ?>
        <span style="position:relative;z-index:1"><?php echo e($article->cover_emoji); ?></span>
      <?php endif; ?>
    </div>

    <?php if($article->category): ?>
      <a href="<?php echo e(route('category', $article->category->slug)); ?>" class="art-cat"
         style="background:<?php echo e($article->category->color); ?>">
        <?php echo e($article->category->name); ?>

      </a>
    <?php endif; ?>

    <h1 class="art-title"><?php echo e($article->title); ?></h1>

    <?php if($article->excerpt): ?>
      <p class="art-deck"><?php echo e($article->excerpt); ?></p>
    <?php endif; ?>

    <div class="art-byline">
      <div class="byline-av">✍️</div>
      <div>
        <div class="byline-name"><?php echo e($article->author?->name ?? 'ADT Sports Desk'); ?></div>
        <div class="byline-info"><?php echo e($article->formatted_date); ?> · <?php echo e($article->read_time); ?> read · <?php echo e(number_format($article->views)); ?> views</div>
      </div>
      <div class="byline-actions">
        <button class="action-btn" onclick="shareArticle()" title="Share">📤</button>
        <button class="action-btn" onclick="cycleFontSize()" title="Adjust font size">Aa</button>
      </div>
    </div>

    
    <?php if($article->tags && count($article->tags)): ?>
    <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:28px">
      <?php $__currentLoopData = $article->tags; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('search', ['q' => $tag])); ?>" class="tag" style="font-size:11px"><?php echo e($tag); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    
    <div class="art-body" id="artBody">
      <?php echo $article->body; ?>

    </div>

    
    <?php if($related->count()): ?>
    <div class="related-section">
      <div class="sec-hd">
        <div class="sec-hd-left">
          <div class="sec-hd-bar"></div>
          <span class="sec-hd-label">More Stories</span>
        </div>
      </div>
      <div class="cards-grid" style="margin-top:18px">
        <?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('article', $r->slug)); ?>" class="card-box" style="text-decoration:none">
          <div class="cb-thumb" style="background:<?php echo e($r->cover_bg); ?>">
            <?php if($r->cover_image): ?>
              <img src="<?php echo e($r->cover_image); ?>" style="width:100%;height:100%;object-fit:cover" alt="">
            <?php else: ?>
              <?php echo e($r->cover_emoji); ?>

            <?php endif; ?>
          </div>
          <?php if($r->category): ?>
            <span class="cb-cat" style="color:<?php echo e($r->category->color); ?>"><?php echo e($r->category->name); ?></span>
          <?php endif; ?>
          <div class="cb-title"><?php echo e($r->title); ?></div>
          <div class="cb-meta"><?php echo e($r->formatted_date); ?></div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
    </div>
    <?php endif; ?>
  </article>

  
  <aside class="art-sidebar">
    <div class="art-sidebar-sticky">

      
      <div class="widget widget-nl" style="margin-bottom:22px">
        <div class="sec-hd" style="border-bottom-color:rgba(255,255,255,.1);margin-bottom:12px">
          <div class="sec-hd-left">
            <div class="sec-hd-bar"></div>
            <span class="sec-hd-label" style="color:#F0EBE5">Daily Digest</span>
          </div>
        </div>
        <p class="nl-desc" style="font-size:13px">Top Kabaddi stories straight to your inbox — free.</p>
        <input type="email" class="nl-input" placeholder="your@email.com" id="sideNlEmail">
        <button class="nl-btn" onclick="subscribeSide()">Subscribe →</button>
      </div>

      
      <?php if($trending->count()): ?>
      <div class="widget">
        <div class="sec-hd" style="margin-bottom:14px">
          <div class="sec-hd-left">
            <div class="sec-hd-bar"></div>
            <span class="sec-hd-label">More Stories</span>
          </div>
        </div>
        <?php $__currentLoopData = $trending->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('article', $t->slug)); ?>" class="card-num" style="text-decoration:none">
          <div style="width:52px;height:52px;border-radius:6px;background:<?php echo e($t->cover_bg); ?>;display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;overflow:hidden">
            <?php if($t->cover_image): ?>
              <img src="<?php echo e($t->cover_image); ?>" style="width:100%;height:100%;object-fit:cover" alt="">
            <?php else: ?>
              <?php echo e($t->cover_emoji); ?>

            <?php endif; ?>
          </div>
          <div>
            <div class="cn-title"><?php echo e($t->title); ?></div>
            <div class="cn-meta"><?php echo e($t->formatted_date); ?></div>
          </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php endif; ?>

    </div>
  </aside>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const fontSizes = ['16px','18px','20px'];
let fsIdx = 1;
function cycleFontSize() {
  fsIdx = (fsIdx + 1) % fontSizes.length;
  document.getElementById('artBody').style.fontSize = fontSizes[fsIdx];
}
function shareArticle() {
  if (navigator.share) {
    navigator.share({ title: '<?php echo e(addslashes($article->title)); ?>', url: window.location.href });
  } else {
    navigator.clipboard.writeText(window.location.href).then(() => alert('Link copied to clipboard!'));
  }
}
function subscribeSide() {
  const e = document.getElementById('sideNlEmail').value;
  if (!e || !e.includes('@')) { alert('Please enter a valid email address.'); return; }
  alert('✅ Subscribed! You\'ll receive the Daily Digest soon.');
  document.getElementById('sideNlEmail').value = '';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports-laravel\resources\views/frontend/article.blade.php ENDPATH**/ ?>