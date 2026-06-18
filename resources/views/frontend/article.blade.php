@extends('layouts.frontend')
@section('title', $article->meta_title ?: $article->title . ' — ' . ($settings['site_name'] ?? 'ADT Sports'))
@section('meta_desc', $article->meta_desc ?: $article->excerpt)

@section('content')
<div class="article-wrap">

  {{-- ── MAIN ARTICLE ─────────────────────────────────────── --}}
  <article class="article-main">
    <a href="{{ route('home') }}" class="back-btn">← Back to Home</a>

    <div class="art-hero-img" style="background:{{ $article->cover_bg }}">
      @if($article->cover_image)
        <img src="{{ $article->cover_image }}" alt="{{ $article->title }}">
      @else
        <span style="position:relative;z-index:1">{{ $article->cover_emoji }}</span>
      @endif
    </div>

    @if($article->category)
      <a href="{{ route('category', $article->category->slug) }}" class="art-cat"
         style="background:{{ $article->category->color }}">
        {{ $article->category->name }}
      </a>
    @endif

    <h1 class="art-title">{{ $article->title }}</h1>

    @if($article->excerpt)
      <p class="art-deck">{{ $article->excerpt }}</p>
    @endif

    <div class="art-byline">
      <div class="byline-av">✍️</div>
      <div>
        <div class="byline-name">{{ $article->author?->name ?? 'ADT Sports Desk' }}</div>
        <div class="byline-info">{{ $article->formatted_date }} · {{ $article->read_time }} read · {{ number_format($article->views) }} views</div>
      </div>
      <div class="byline-actions">
        <button class="action-btn" onclick="shareArticle()" title="Share">📤</button>
        <button class="action-btn" onclick="cycleFontSize()" title="Adjust font size">Aa</button>
      </div>
    </div>

    {{-- Tags --}}
    @if($article->tags && count($article->tags))
    <div style="display:flex;flex-wrap:wrap;gap:7px;margin-bottom:28px">
      @foreach($article->tags as $tag)
        <a href="{{ route('search', ['q' => $tag]) }}" class="tag" style="font-size:11px">{{ $tag }}</a>
      @endforeach
    </div>
    @endif

    {{-- Article body --}}
    <div class="art-body" id="artBody">
      {!! $article->body !!}
    </div>

    {{-- Related articles --}}
    @if($related->count())
    <div class="related-section">
      <div class="sec-hd">
        <div class="sec-hd-left">
          <div class="sec-hd-bar"></div>
          <span class="sec-hd-label">More Stories</span>
        </div>
      </div>
      <div class="cards-grid" style="margin-top:18px">
        @foreach($related as $r)
        <a href="{{ route('article', $r->slug) }}" class="card-box" style="text-decoration:none">
          <div class="cb-thumb" style="background:{{ $r->cover_bg }}">
            @if($r->cover_image)
              <img src="{{ $r->cover_image }}" style="width:100%;height:100%;object-fit:cover" alt="">
            @else
              {{ $r->cover_emoji }}
            @endif
          </div>
          @if($r->category)
            <span class="cb-cat" style="color:{{ $r->category->color }}">{{ $r->category->name }}</span>
          @endif
          <div class="cb-title">{{ $r->title }}</div>
          <div class="cb-meta">{{ $r->formatted_date }}</div>
        </a>
        @endforeach
      </div>
    </div>
    @endif
  </article>

  {{-- ── ARTICLE SIDEBAR ─────────────────────────────────── --}}
  <aside class="art-sidebar">
    <div class="art-sidebar-sticky">

      {{-- Newsletter --}}
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

      {{-- More Stories --}}
      @if($trending->count())
      <div class="widget">
        <div class="sec-hd" style="margin-bottom:14px">
          <div class="sec-hd-left">
            <div class="sec-hd-bar"></div>
            <span class="sec-hd-label">More Stories</span>
          </div>
        </div>
        @foreach($trending->take(5) as $t)
        <a href="{{ route('article', $t->slug) }}" class="card-num" style="text-decoration:none">
          <div style="width:52px;height:52px;border-radius:6px;background:{{ $t->cover_bg }};display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;overflow:hidden">
            @if($t->cover_image)
              <img src="{{ $t->cover_image }}" style="width:100%;height:100%;object-fit:cover" alt="">
            @else
              {{ $t->cover_emoji }}
            @endif
          </div>
          <div>
            <div class="cn-title">{{ $t->title }}</div>
            <div class="cn-meta">{{ $t->formatted_date }}</div>
          </div>
        </a>
        @endforeach
      </div>
      @endif

    </div>
  </aside>

</div>
@endsection

@push('scripts')
<script>
const fontSizes = ['16px','18px','20px'];
let fsIdx = 1;
function cycleFontSize() {
  fsIdx = (fsIdx + 1) % fontSizes.length;
  document.getElementById('artBody').style.fontSize = fontSizes[fsIdx];
}
function shareArticle() {
  if (navigator.share) {
    navigator.share({ title: '{{ addslashes($article->title) }}', url: window.location.href });
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
@endpush
