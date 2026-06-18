<?php $__env->startSection('title', $article ? 'Edit Article' : 'New Article'); ?>
<?php $__env->startSection('content'); ?>

<form action="<?php echo e($article ? route('admin.articles.update',$article) : route('admin.articles.store')); ?>"
      method="POST" id="artForm">
  <?php echo csrf_field(); ?>
  <?php if($article): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>
  
  <input type="hidden" name="body"        id="bodyInput">
  <input type="hidden" name="cover_bg"    id="coverBgInput"    value="<?php echo e(old('cover_bg',    $article?->cover_bg    ?? 'linear-gradient(145deg,#1A1410,#221808)')); ?>">
  <input type="hidden" name="cover_emoji" id="coverEmojiInput" value="<?php echo e(old('cover_emoji', $article?->cover_emoji ?? '📰')); ?>">
  <input type="hidden" name="cover_image" id="coverImgInput"   value="<?php echo e(old('cover_image', $article?->cover_image ?? '')); ?>">
  <input type="hidden" name="tags"        id="tagsInput"       value="<?php echo e(old('tags', $article?->tags_list ?? '')); ?>">

  <div class="page-hd">
    <div>
      <h1><?php echo e($article ? 'Edit Article' : 'New Article'); ?></h1>
      <div class="page-hd-sub">
        <?php if($article): ?> Last saved <?php echo e($article->updated_at->diffForHumans()); ?> <?php else: ?> Fill in the details below <?php endif; ?>
      </div>
    </div>
    <div style="display:flex;gap:8px">
      <button type="submit" name="status_override" value="draft"     class="btn btn-ghost">💾 Save Draft</button>
      <button type="submit" name="status_override" value="published" class="btn btn-success">🚀 Publish</button>
    </div>
  </div>

  <div class="editor-grid">

    
    <div>
      <div class="field">
        <label>Article Title *</label>
        <input type="text" name="title" id="titleInput" required
          value="<?php echo e(old('title', $article?->title)); ?>"
          placeholder="Enter a compelling headline…"
          style="font-size:18px;font-weight:600;padding:13px 16px">
      </div>

      <div class="field">
        <label>Excerpt / Summary</label>
        <textarea name="excerpt" rows="3"
          placeholder="A brief summary shown on article cards and in search results…"><?php echo e(old('excerpt', $article?->excerpt)); ?></textarea>
        <div class="field-hint">Keep under 200 characters for best display.</div>
      </div>

      <div class="field">
        <label>Article Body *</label>
        <div class="rte-wrap">
          <div class="rte-toolbar">
            <button type="button" class="rte-btn" onclick="fmt('bold')"              title="Bold"><b>B</b></button>
            <button type="button" class="rte-btn" onclick="fmt('italic')"            title="Italic"><i>I</i></button>
            <button type="button" class="rte-btn" onclick="fmt('underline')"         title="Underline"><u>U</u></button>
            <div class="rte-sep"></div>
            <button type="button" class="rte-btn" onclick="fmtBlock('h2')"           title="Heading 2" style="font-size:11px;width:auto;padding:0 8px">H2</button>
            <button type="button" class="rte-btn" onclick="fmtBlock('h3')"           title="Heading 3" style="font-size:11px;width:auto;padding:0 8px">H3</button>
            <button type="button" class="rte-btn" onclick="fmtBlock('p')"            title="Paragraph" style="font-size:11px;width:auto;padding:0 8px">¶</button>
            <div class="rte-sep"></div>
            <button type="button" class="rte-btn" onclick="fmt('insertUnorderedList')" title="Bullet list">• ≡</button>
            <button type="button" class="rte-btn" onclick="fmt('insertOrderedList')"   title="Numbered list">1.≡</button>
            <div class="rte-sep"></div>
            <button type="button" class="rte-btn" onclick="fmtBlock('blockquote')"   title="Blockquote">❝</button>
            <button type="button" class="rte-btn" onclick="insertCallout()"          title="Callout box" style="font-size:11px;width:auto;padding:0 7px">📦 Box</button>
            <div class="rte-sep"></div>
            <button type="button" class="rte-btn" onclick="insertLink()"             title="Insert link">🔗</button>
            <button type="button" class="rte-btn" onclick="fmt('removeFormat')"      title="Clear formatting" style="font-size:11px">✕ Fmt</button>
            <div style="margin-left:auto">
              <button type="button" class="rte-btn" id="htmlModeBtn" onclick="toggleHTML()"
                style="width:auto;padding:0 10px;font-size:11px;font-weight:700">HTML</button>
            </div>
          </div>
          <div id="rteEditor" contenteditable="true"><?php echo old('body', $article?->body); ?></div>
          <div class="rte-footer">
            <span id="wordCount">0 words</span>
            <span id="readTimeEst">~0 min read</span>
          </div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:4px">
        <div class="field">
          <label>SEO Title <span style="font-weight:400;text-transform:none;letter-spacing:0;font-size:10px">(optional)</span></label>
          <input type="text" name="meta_title" value="<?php echo e(old('meta_title',$article?->meta_title)); ?>" placeholder="Defaults to article title">
        </div>
        <div class="field">
          <label>SEO Description</label>
          <textarea name="meta_desc" rows="2" placeholder="160-char search description…"><?php echo e(old('meta_desc',$article?->meta_desc)); ?></textarea>
        </div>
      </div>
    </div>

    
    <div>

      
      <div class="panel-card">
        <h4>Publish Settings</h4>
        <div class="field" style="margin-bottom:12px">
          <label>Status</label>
          <select name="status" id="statusSelect">
            <option value="draft"     <?php echo e(old('status',$article?->status??'draft')=='draft'?'selected':''); ?>>📝 Draft</option>
            <option value="published" <?php echo e(old('status',$article?->status)=='published'?'selected':''); ?>>✅ Published</option>
          </select>
        </div>
        <div class="field" style="margin-bottom:14px">
          <label>Category</label>
          <select name="category_id">
            <option value="">— Select Category —</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($cat->id); ?>"
                <?php echo e(old('category_id',$article?->category_id)==$cat->id?'selected':''); ?>>
                <?php echo e($cat->name); ?>

              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="toggle-row">
          <span class="toggle-label">⭐ Featured Article</span>
          <input type="checkbox" name="featured" value="1" class="toggle-cb"
            <?php echo e(old('featured',$article?->featured)?'checked':''); ?>>
        </div>
        <div class="toggle-row">
          <span class="toggle-label">🔴 Breaking News</span>
          <input type="checkbox" name="breaking" value="1" class="toggle-cb"
            <?php echo e(old('breaking',$article?->breaking)?'checked':''); ?>>
        </div>
      </div>

      
      <div class="panel-card">
        <h4>Cover Image</h4>
        <div class="cover-preview" id="coverPreview" onclick="document.getElementById('coverFileInput').click()">
          <span id="coverEmojiShow" style="font-size:60px"><?php echo e($article?->cover_emoji ?? '📰'); ?></span>
          <img id="coverImgPreview" src="<?php echo e($article?->cover_image); ?>"
               style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;display:<?php echo e($article?->cover_image ? 'block' : 'none'); ?>">
          <div class="cover-overlay">📷 Change Cover</div>
        </div>
        <input type="file" id="coverFileInput" accept="image/*" style="display:none" onchange="uploadCover(this)">

        <div class="field" style="margin-bottom:10px">
          <label>Cover Emoji</label>
          <div class="emoji-grid" id="emojiGrid"></div>
        </div>
        <div class="field" style="margin-bottom:0">
          <label>Background Theme</label>
          <div class="bg-grid" id="bgGrid"></div>
        </div>
      </div>

      
      <div class="panel-card">
        <h4>Tags</h4>
        <div class="tag-wrap" id="tagWrap" onclick="document.getElementById('tagRealInput').focus()"></div>
        <input type="text" class="tag-inp" id="tagRealInput" placeholder="Type a tag, press Enter"
          onkeydown="handleTag(event)" style="background:var(--card);border:1px solid var(--border);border-radius:6px;padding:6px 10px;width:100%;margin-top:8px;color:var(--ink);outline:none;font-size:13px">
        <div class="field-hint" style="margin-top:5px">Separate with Enter or comma.</div>
      </div>












    </div>
  </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const EMOJIS=['📰','🤸','🏆','📊','🌐','🎯','🏟️','🌱','📡','📖','🔥','⚡','🎬','🏅','🌟','🔴','📣','🎪'];
const BGS=[
  'linear-gradient(145deg,#140E0A,#221808)',
  'linear-gradient(145deg,#0A1420,#101C2A)',
  'linear-gradient(145deg,#0A1A0E,#102016)',
  'linear-gradient(145deg,#1A100A,#221608)',
  'linear-gradient(145deg,#12082A,#1A1030)',
  'linear-gradient(145deg,#0A0A1A,#101020)',
  'linear-gradient(145deg,#141206,#1E1A0A)',
  'linear-gradient(145deg,#180A18,#221030)',
];

// ── Tags ─────────────────────────────────────────────
let tags = [];
const savedTags = document.getElementById('tagsInput').value;
if (savedTags) tags = savedTags.split(',').map(t=>t.trim()).filter(Boolean);
renderTags();

function renderTags() {
  const w = document.getElementById('tagWrap');
  w.innerHTML = tags.map(t =>
    `<span class="tag-chip">${t}<span class="rx" onclick="removeTag('${t.replace(/'/g,"\\'")}')">✕</span></span>`
  ).join('');
  document.getElementById('tagsInput').value = tags.join(', ');
}
function handleTag(e) {
  if (e.key==='Enter'||e.key===',') {
    e.preventDefault();
    const v = e.target.value.trim().replace(/,$/,'');
    if (v && !tags.includes(v)) { tags.push(v); renderTags(); }
    e.target.value='';
  }
}
function removeTag(t) { tags=tags.filter(x=>x!==t); renderTags(); }

// ── Emoji grid ───────────────────────────────────────
const curEmoji = document.getElementById('coverEmojiInput').value || '📰';
document.getElementById('emojiGrid').innerHTML = EMOJIS.map(e =>
  `<button type="button" class="eg-btn${e===curEmoji?' sel':''}" onclick="selEmoji('${e}',this)">${e}</button>`
).join('');
function selEmoji(e,el) {
  document.querySelectorAll('.eg-btn').forEach(b=>b.classList.remove('sel'));
  el.classList.add('sel');
  document.getElementById('coverEmojiInput').value=e;
  document.getElementById('coverEmojiShow').textContent=e;
}

// ── BG grid ──────────────────────────────────────────
const curBg = document.getElementById('coverBgInput').value;
document.getElementById('bgGrid').innerHTML = BGS.map((bg,i) =>
  `<div class="bg-swatch${bg===curBg||(!curBg&&i===0)?' sel':''}" style="background:${bg}" onclick="selBg(this,'${bg.replace(/'/g,"\\'")}')"></div>`
).join('');
document.getElementById('coverPreview').style.background = curBg || BGS[0];
function selBg(el, bg) {
  document.querySelectorAll('.bg-swatch').forEach(b=>b.classList.remove('sel'));
  el.classList.add('sel');
  document.getElementById('coverBgInput').value=bg;
  document.getElementById('coverPreview').style.background=bg;
}

// ── Cover upload ─────────────────────────────────────
async function uploadCover(input) {
  const fd = new FormData();
  fd.append('file', input.files[0]);
  fd.append('_token', '<?php echo e(csrf_token()); ?>');
  try {
    const r = await fetch('<?php echo e(route("admin.media.upload")); ?>', {method:'POST',body:fd});
    const d = await r.json();
    document.getElementById('coverImgInput').value = d.url;
    const img = document.getElementById('coverImgPreview');
    img.src = d.url; img.style.display = 'block';
    document.getElementById('coverEmojiShow').style.opacity = '0';
  } catch(e) { alert('Upload failed: ' + e.message); }
}

// ── RTE ──────────────────────────────────────────────
let htmlMode = false;
function fmt(cmd) { document.execCommand(cmd,false,null); document.getElementById('rteEditor').focus(); updateCount(); }
function fmtBlock(tag) { document.execCommand('formatBlock',false,tag); document.getElementById('rteEditor').focus(); updateCount(); }
function insertLink() { const u=prompt('Enter URL:'); if(u) document.execCommand('createLink',false,u); }
function insertCallout() {
  document.execCommand('insertHTML',false,
    '<div style="background:rgba(212,66,10,.1);border:1px solid rgba(212,66,10,.2);border-radius:6px;padding:14px 18px;margin:16px 0;">📌 Write your callout text here...</div>');
}
function toggleHTML() {
  const ed = document.getElementById('rteEditor');
  const btn = document.getElementById('htmlModeBtn');
  if (!htmlMode) {
    const ta = document.createElement('textarea');
    ta.id='htmlArea';
    ta.style.cssText='width:100%;min-height:380px;background:var(--bg);color:var(--ink);border:none;outline:none;padding:18px 20px;font-family:monospace;font-size:12px;resize:vertical;line-height:1.6;display:block';
    ta.value=ed.innerHTML;
    ed.style.display='none';
    ed.parentNode.insertBefore(ta, ed.nextSibling);
    btn.style.color='var(--brand)'; htmlMode=true;
  } else {
    const ta=document.getElementById('htmlArea');
    ed.innerHTML=ta.value; ta.remove();
    ed.style.display=''; btn.style.color=''; htmlMode=false;
  }
}
function updateCount() {
  const text = htmlMode
    ? (document.getElementById('htmlArea')?.value||'').replace(/<[^>]+>/g,'')
    : (document.getElementById('rteEditor').innerText||'');
  const w = text.trim().split(/\s+/).filter(Boolean).length;
  document.getElementById('wordCount').textContent = w.toLocaleString()+' words';
  document.getElementById('readTimeEst').textContent = '~'+Math.max(1,Math.round(w/200))+' min read';
}
document.getElementById('rteEditor').addEventListener('input',updateCount);
updateCount();

// ── Submit: sync hidden fields ────────────────────────
document.getElementById('artForm').addEventListener('submit', function(e) {
  const body = htmlMode
    ? (document.getElementById('htmlArea')||{value:''}).value
    : document.getElementById('rteEditor').innerHTML;
  document.getElementById('bodyInput').value = body;

  // Sync status from which button was clicked
  const btn = e.submitter;
  if (btn && btn.name==='status_override') {
    document.getElementById('statusSelect').value = btn.value;
  }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\adt-sports\resources\views/admin/articles/editor.blade.php ENDPATH**/ ?>