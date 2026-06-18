@extends('layouts.admin')
@section('title','Media Library')
@section('content')

<div class="page-hd">
  <div><h1>Media Library</h1><div class="page-hd-sub">{{ $media->total() }} files</div></div>
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
  @forelse($media as $m)
  <div class="media-item" id="media-{{ $m->id }}">
    <div class="media-thumb">
      <img src="{{ $m->url }}" alt="{{ $m->original_name }}" loading="lazy"
           onerror="this.style.display='none';this.parentNode.innerHTML='🖼️'">
    </div>
    <div class="media-info">
      <div class="media-name" title="{{ $m->original_name }}">{{ $m->original_name }}</div>
      <div class="media-size">{{ $m->formatted_size }}</div>
      <div style="display:flex;gap:4px;margin-top:7px">
        <button class="btn btn-ghost btn-sm" style="font-size:10px;padding:3px 8px;flex:1"
          onclick="copyUrl('{{ $m->url }}')">📋 Copy URL</button>
        <form action="{{ route('admin.media.destroy',$m) }}" method="POST" style="display:inline"
              onsubmit="return confirm('Delete this image?')">
          @csrf @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm" style="font-size:10px;padding:3px 8px">🗑️</button>
        </form>
      </div>
    </div>
  </div>
  @empty
  <div class="empty-state" style="grid-column:1/-1">
    <div style="font-size:44px;margin-bottom:12px">🖼️</div>
    <p>No images uploaded yet. Upload your first image above.</p>
  </div>
  @endforelse
</div>

@if($media->hasPages())
  <div style="padding:16px 0">{{ $media->links() }}</div>
@endif

@endsection

@push('scripts')
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
    fd.append('_token', '{{ csrf_token() }}');
    try {
      const r = await fetch('{{ route("admin.media.upload") }}', {method:'POST',body:fd});
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
@endpush
