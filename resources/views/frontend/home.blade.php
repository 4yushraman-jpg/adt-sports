@extends('layouts.frontend')
@include('partials.pagination_seo', ['paginator' => $articles])
@section('title', ($settings['site_name'] ?? 'ADT Sports'))
{{-- When filtered via ?category=, consolidate to the canonical category page to avoid duplicate content --}}
@if($catSlug && $categories->firstWhere('slug', $catSlug))
  @section('canonical', route('category', $catSlug))
@else
  {{-- Self-reference paginated pages (incl. ?page=N) so deeper pages stay indexable --}}
  @section('canonical', $articles->currentPage() > 1 ? $articles->url($articles->currentPage()) : route('home'))
  @push('head_links')
    @if($articles->previousPageUrl())<link rel="prev" href="{{ $articles->previousPageUrl() }}">@endif
    @if($articles->nextPageUrl())<link rel="next" href="{{ $articles->nextPageUrl() }}">@endif
  @endpush
@endif

@section('content')
<div class="wrap">

  <h1 class="sr-only">{{ $settings['site_name'] ?? 'ADT Sports' }} — {{ $settings['site_tagline'] ?? "India's #1 Kabaddi Media Platform" }}</h1>

  {{-- ── HERO ─────────────────────────────────────────────── --}}
  @if($heroLead)
  <div class="home-hero">
    <a href="{{ route('article', $heroLead->slug) }}" class="hero-lead">
      <div class="hero-lead-art" style="background:{{ $heroLead->cover_bg }}">
        @if($heroLead->cover_image)
          <img src="{{ $heroLead->cover_image }}" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover" alt="{{ $heroLead->title }}" fetchpriority="high" decoding="async">
        @else
          <x-cover-placeholder :article="$heroLead" />
        @endif
      </div>
      <div class="hero-lead-veil"></div>
      <div class="hero-lead-body">
        @if($heroLead->category)
          <span class="cat-pill">{{ $heroLead->category->name }}</span>
        @endif
        <h2 class="hero-lead-title">{{ $heroLead->title }}</h2>
        <div class="hero-lead-meta">
          <span>{{ $heroLead->author?->name ?? 'ADT Sports' }}</span>
          <span class="sep"></span>
          <span>{{ $heroLead->formatted_date }}</span>
          <span class="sep"></span>
          <span>{{ $heroLead->read_time }} read</span>
        </div>
      </div>
    </a>

    <div class="hero-stack">
      @foreach($heroStack as $a)
      <a href="{{ route('article', $a->slug) }}" class="hero-stack-item">
        <div class="stack-thumb" style="background:{{ $a->cover_bg }}">
          @if($a->cover_image)
            <img src="{{ $a->cover_image }}" style="width:100%;height:100%;object-fit:cover" alt="{{ $a->title }}" loading="lazy" decoding="async">
          @else
            <x-cover-placeholder :article="$a" />
          @endif
        </div>
        <div>
          @if($a->category)<div class="stack-cat">{{ $a->category->name }}</div>@endif
          <h3 class="stack-title">{{ $a->title }}</h3>
          <div class="stack-meta">{{ $a->formatted_date }} · {{ $a->read_time }} read</div>
        </div>
      </a>
      @endforeach
    </div>
  </div>
  @endif

  {{-- ── MAIN CONTENT + SIDEBAR ──────────────────────────── --}}
  <div class="content-grid">
    <main>
      <div class="sec-hd">
        <div class="sec-hd-left">
          <div class="sec-hd-bar"></div>
          <span class="sec-hd-label">
            {{ $catSlug ? ($categories->firstWhere('slug',$catSlug)?->name ?? 'Articles') : 'Latest Stories' }}
          </span>
        </div>
        @if($catSlug)
          <a href="{{ route('category', $catSlug) }}" class="sec-hd-more">All {{ $categories->firstWhere('slug',$catSlug)?->name }} →</a>
        @endif
      </div>

      {{-- Article feed --}}
      @forelse($articles->take(5) as $a)
      <a href="{{ route('article', $a->slug) }}" class="card-row" style="text-decoration:none;display:grid">
        <div>
          <span class="cr-cat" style="{{ $a->category ? 'color:'.$a->category->color : '' }}">
            {{ $a->category?->name ?? 'Article' }}
          </span>
          <h2 class="cr-title">{{ $a->title }}</h2>
          @if($a->excerpt)
            <div class="cr-excerpt">{{ $a->excerpt }}</div>
          @endif
          <div class="cr-meta">
            <span>{{ $a->author?->name ?? 'ADT Sports' }}</span>
            <span class="sep"></span>
            <span>{{ $a->formatted_date }}</span>
            <span class="sep"></span>
            <span>{{ $a->read_time }} read</span>
          </div>
        </div>
        <div class="cr-thumb" style="background:{{ $a->cover_bg }}">
          @if($a->cover_image)
            <img src="{{ $a->cover_image }}" style="width:100%;height:100%;object-fit:cover" alt="{{ $a->title }}" loading="lazy" decoding="async">
          @else
            <x-cover-placeholder :article="$a" />
          @endif
        </div>
      </a>
      @empty
      <div style="text-align:center;padding:64px 20px;color:var(--ink3)">
        <div style="font-size:44px;margin-bottom:14px">📭</div>
        <p style="font-size:15px">No articles found{{ $catSlug ? ' in this category' : '' }}.</p>
        @if($catSlug)
          <a href="{{ route('home') }}" style="color:var(--brand);font-size:14px;margin-top:10px;display:inline-block">← Back to all articles</a>
        @endif
      </div>
      @endforelse

      {{-- Feature strip (articles 6-8) — only when it can show a full row of 3,
           otherwise a lone card looks stray. --}}
      @if($articles->count() >= 8)
      <div class="feature-strip">
        @foreach($articles->slice(5, 3) as $a)
        <a href="{{ route('article', $a->slug) }}" class="fs-item" style="text-decoration:none">
          <div class="fs-cat">{{ $a->breaking ? '🔴 Breaking' : ($a->category?->name ?? 'Article') }}</div>
          <h3 class="fs-title">{{ $a->title }}</h3>
          <div class="fs-meta">{{ $a->read_time }} read · {{ $a->formatted_date }}</div>
        </a>
        @endforeach
      </div>
      @endif

      {{-- Must Read grid (articles 9-11) — only when it can fill all 3 cells. --}}
      @if($articles->count() >= 11)
      <div class="sec-hd">
        <div class="sec-hd-left">
          <div class="sec-hd-bar"></div>
          <span class="sec-hd-label">Must Read</span>
        </div>
      </div>
      <div class="cards-grid">
        @foreach($articles->slice(8, 3) as $a)
        <a href="{{ route('article', $a->slug) }}" class="card-box" style="text-decoration:none">
          <div class="cb-thumb" style="background:{{ $a->cover_bg }}">
            @if($a->cover_image)
              <img src="{{ $a->cover_image }}" style="width:100%;height:100%;object-fit:cover" alt="{{ $a->title }}" loading="lazy" decoding="async">
            @else
              <x-cover-placeholder :article="$a" />
            @endif
          </div>
          <span class="cb-cat" style="{{ $a->category ? 'color:'.$a->category->color : '' }}">
            {{ $a->category?->name ?? '' }}
          </span>
          <h3 class="cb-title">{{ $a->title }}</h3>
          @if($a->excerpt)<div class="cb-excerpt">{{ $a->excerpt }}</div>@endif
          <div class="cb-meta">{{ $a->formatted_date }} · {{ $a->read_time }} read</div>
        </a>
        @endforeach
      </div>
      @endif

      {{-- Pagination --}}
      @if($articles->hasPages())
      <div class="pagination-wrap">
        {{ $articles->links() }}
      </div>
      @endif

    </main>

    {{-- ── SIDEBAR ─────────────────────────────────────────── --}}
    <aside class="sidebar-col">

      {{-- Trending --}}
      <div class="widget">
        <div style="display:inline-flex;align-items:center;gap:6px;background:var(--brand-soft);color:var(--brand);font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:3px 10px;border-radius:20px;margin-bottom:14px">
          <i class="fa-solid fa-arrow-trend-up"></i> Trending Now
        </div>
        @forelse($trending as $i => $a)
        <a href="{{ route('article', $a->slug) }}" class="card-num" style="text-decoration:none">
          <div class="cn-num">0{{ $i + 1 }}</div>
          <div>
            <div class="cn-title">{{ $a->title }}</div>
            <div class="cn-meta">{{ $a->category?->name ?? '' }} · {{ $a->formatted_date }}</div>
          </div>
        </a>
        @empty
        <p style="color:var(--ink3);font-size:13px">No trending articles yet.</p>
        @endforelse
      </div>


      {{-- About --}}
      <div class="widget">
        <div class="about-mini-logo">
          <div class="am-img"><img src="/uploads/logo.png" onerror="this.style.display='none'" alt="ADT"></div>
          <div class="am-name"><span>ADT</span> Sports</div>
        </div>
        <p class="about-mini-desc">India's #1 Kabaddi media platform — covering every raid, every story, every league.</p>
        <div class="socials-row">
          @if(!empty($settings['facebook_url']))  <a href="{{ $settings['facebook_url'] }}"  target="_blank" class="soc-btn"><i class="fa-brands fa-facebook"></i> Follow</a> @endif
          @if(!empty($settings['instagram_url'])) <a href="{{ $settings['instagram_url'] }}" target="_blank" class="soc-btn"><i class="fa-brands fa-instagram"></i> Follow</a> @endif
          @if(!empty($settings['youtube_url']))   <a href="{{ $settings['youtube_url'] }}"   target="_blank" class="soc-btn"><i class="fa-brands fa-youtube"></i> Watch</a>  @endif
          @if(empty($settings['facebook_url']) && empty($settings['instagram_url']) && empty($settings['youtube_url']))
            <span class="soc-btn"><i class="fa-brands fa-facebook"></i> Follow</span>
            <span class="soc-btn"><i class="fa-brands fa-instagram"></i> Follow</span>
            <span class="soc-btn"><i class="fa-brands fa-youtube"></i> Watch</span>
          @endif
        </div>
      </div>

    </aside>
  </div>
</div>
@endsection

