<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>@yield('title', ($settings['site_name'] ?? 'ADT Sports'))</title>
<meta name="description" content="@yield('meta_desc', $settings['site_description'] ?? "India's #1 Kabaddi media platform.")">

{{-- ── SEO METADATA ───────────────────────────────────────── --}}
@php
    $seoSiteName  = $settings['site_name'] ?? 'ADT Sports';
    $seoTitle     = trim($__env->yieldContent('title', $seoSiteName));
    $seoDesc      = trim($__env->yieldContent('meta_desc', $settings['site_description'] ?? "India's #1 Kabaddi media platform."));
    // Each list view already sets a page-aware @section('canonical'); article
    // pages set their own. Fall back to the current URL otherwise.
    $seoCanonical = trim($__env->yieldContent('canonical')) ?: url()->current();
    $seoRobots    = trim($__env->yieldContent('robots', 'index, follow'));
    $seoType      = trim($__env->yieldContent('og_type', 'website'));
    $seoImage     = trim($__env->yieldContent('og_image')) ?: '/public/uploads/logo.png';
    if (! \Illuminate\Support\Str::startsWith($seoImage, ['http://', 'https://'])) {
        $seoImage = url($seoImage);
    }
    $seoSocials = array_values(array_filter([
        $settings['facebook_url']  ?? null,
        $settings['instagram_url'] ?? null,
        $settings['youtube_url']   ?? null,
        $settings['twitter_url']   ?? null,
    ]));
@endphp
<link rel="canonical" href="{{ $seoCanonical }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="alternate" type="application/rss+xml" title="{{ $seoSiteName }} RSS" href="{{ url('/feed.xml') }}">
@stack('head_links')

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:site_name" content="{{ $seoSiteName }}">
<meta property="og:title" content="{{ $seoTitle }}">
<meta property="og:description" content="{{ $seoDesc }}">
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:image" content="{{ $seoImage }}">
@stack('og_meta')

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoTitle }}">
<meta name="twitter:description" content="{{ $seoDesc }}">
<meta name="twitter:image" content="{{ $seoImage }}">
@if(!empty($settings['twitter_url']))
<meta name="twitter:site" content="{{ '@' . \Illuminate\Support\Str::afterLast(rtrim($settings['twitter_url'], '/'), '/') }}">
@endif

{{-- Organization + WebSite structured data (site-wide) --}}
<script type="application/ld+json">
{!! json_encode([
    '@context'    => 'https://schema.org',
    '@type'       => 'Organization',
    'name'        => $seoSiteName,
    'url'         => url('/'),
    'logo'        => url('/public/uploads/logo.png'),
    'description' => $settings['site_description'] ?? "India's #1 Kabaddi media platform.",
] + (count($seoSocials) ? ['sameAs' => $seoSocials] : []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context'        => 'https://schema.org',
    '@type'           => 'WebSite',
    'name'            => $seoSiteName,
    'url'             => url('/'),
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => url('/search') . '?q={search_term_string}',
        'query-input' => 'required name=search_term_string',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}
</script>
@stack('schema')

  <link rel="shortcut icon" href="/public/uploads/logo.png" data-inertia="favicon-shortcut">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;0,800;1,600&display=swap" rel="stylesheet">
{{-- Font Awesome is decorative-only — load it non-render-blocking to protect LCP --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" media="print" onload="this.media='all'" />
<noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" /></noscript>
@vite(['resources/css/app.css'])
@stack('styles')
</head>
<body>
<a href="#main" class="skip-link">Skip to content</a>
<div id="readBar"></div>

<div class="search-overlay" id="srchOverlay">
  <div class="search-box">
    <div class="search-row">
      <span class="s-icon">🔍</span>
      <form action="{{ route('search') }}" method="GET" style="flex:1;display:flex">
        <input type="text" name="q" placeholder="Search Kabaddi news, players, leagues…" autofocus
          value="{{ request('q') }}" style="flex:1">
      </form>
      <button class="s-close" onclick="document.getElementById('srchOverlay').classList.remove('open')">✕</button>
    </div>
    <p class="search-hint">Press ESC to close · Enter to search</p>
  </div>
</div>

<div class="mobile-overlay" id="mobileOverlay"></div>
<div class="mobile-nav" id="mobileNav">
  <a href="{{ route('home') }}">🏠 Home</a>
  @foreach($categories as $cat)
    <a href="{{ route('category', $cat->slug) }}">{{ $cat->name }}</a>
  @endforeach
  <a href="{{ route('search') }}">🔍 Search</a>
</div>

{{-- TICKER --}}
<div class="ticker-strip">
  <div class="ticker-container">
    <span class="ticker-label">Live</span>

    @php
      $ticker = $settings['breaking_ticker'] ?? 'ADT Sports — India\'s #1 Kabaddi Platform';
      $items = array_filter(array_map('trim', explode('|', $ticker)));
      $doubled = array_merge($items, $items);
    @endphp

    <div class="ticker-wrapper">
      <div class="ticker-inner">
        @foreach($doubled as $t)
          <span class="ticker-item">{{ $t }}</span>
          <span class="ticker-sep">◆</span>
        @endforeach
      </div>
    </div>
  </div>
</div>

{{-- NAV --}}
<nav id="mainNav" aria-label="Primary">
  <div class="nav-wrap">
    <a href="{{ route('home') }}" class="logo">
      <div class="logo-img"><img src="/public/uploads/logo.png" onerror="this.style.display='none'" alt="ADT"></div>
      <div class="logo-wordmark"><span class="brand">ADT</span> Sports</div>
    </a>
    <div class="nav-links">
      <a href="{{ route('home') }}" class="{{ request()->routeIs('home') && !request('category') ? 'active':'' }}">Home</a>
      @foreach($categories->take(5) as $cat)
        <a href="{{ route('category',$cat->slug) }}"
          class="{{ request()->route('slug')===$cat->slug ? 'active':'' }}">{{ $cat->name }}</a>
      @endforeach
      @if($categories->count() > 5)
      <div class="nav-drop">
        <a href="#">More <span style="font-size:9px;opacity:.5">▾</span></a>
        <div class="drop-menu">
          @foreach($categories->skip(5) as $cat)
            <a href="{{ route('category',$cat->slug) }}">{{ $cat->name }}</a>
          @endforeach
        </div>
      </div>
      @endif
    </div>
    <div class="nav-right">
      <button class="icon-btn" onclick="document.getElementById('srchOverlay').classList.add('open')"><i class="fa-solid fa-magnifying-glass"></i></button>
      <button class="icon-btn" id="themeBtn"><i class="fa-solid fa-moon"></i></button>
      <a href="#newsletter" class="btn-sub">Subscribe</a>
      <button class="hamburger" id="hamburger"><span></span><span></span><span></span></button>
    </div>
  </div>
</nav>

<main id="main">
@yield('content')
</main>

{{-- FOOTER --}}
<footer>
  <div class="footer-grid">
    <div>
      <div class="ft-logo">
        <div class="fl-img"><img src="/public/uploads/logo.png" onerror="this.style.display='none'" alt="ADT"></div>
        <span><em>ADT</em> Sports</span>
      </div>
      <p class="ft-desc">{{ $settings['site_description'] ?? "India's #1 Kabaddi-focused digital media brand." }}</p>
      <div class="ft-socials">
        @if(!empty($settings['facebook_url']))  <a href="{{ $settings['facebook_url'] }}"  target="_blank" class="ft-soc icon-facebook"><i class="fa-brands fa-facebook"></i></a> @endif
        @if(!empty($settings['instagram_url'])) <a href="{{ $settings['instagram_url'] }}" target="_blank" class="ft-soc icon-instagram"><i class="fa-brands fa-instagram"></i></a> @endif
        @if(!empty($settings['youtube_url']))   <a href="{{ $settings['youtube_url'] }}"   target="_blank" class="ft-soc icon-youtube"><i class="fa-brands fa-youtube"></i></a>  @endif
        @if(!empty($settings['twitter_url']))   <a href="{{ $settings['twitter_url'] }}"   target="_blank" class="ft-soc icon-twitter"><i class="fa-brands fa-twitter"></i></a> @endif
      </div>
    </div>
    <div class="ft-col">
      <h4>Coverage</h4>
      <ul>
        @foreach($categories->take(5) as $cat)
          <li><a href="{{ route('category',$cat->slug) }}">{{ $cat->name }}</a></li>
        @endforeach
      </ul>
    </div>
    <div class="ft-col">
      <h4>More</h4>
      <ul>
        @foreach($categories->skip(5)->take(5) as $cat)
          <li><a href="{{ route('category',$cat->slug) }}">{{ $cat->name }}</a></li>
        @endforeach
        <li><a href="{{ route('search') }}">Search</a></li>
      </ul>
    </div>
    <div class="ft-col">
      <h4>ADT Sports</h4>
      <ul>
        @if(!empty($settings['site_email'])) <li><a href="mailto:{{ $settings['site_email'] }}">Contact Us</a></li> @endif
        @if(!empty($settings['site_phone'])) <li><a href="tel:{{ $settings['site_phone'] }}">{{ $settings['site_phone'] }}</a></li> @endif
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© {{ date('Y') }} {{ $settings['site_name'] ?? 'ADT Sports' }}. All rights reserved.</span>
    <span class="footer-tagline">"{{ $settings['footer_tagline'] ?? 'ADT Sports is not covering Kabaddi. It is building its future.' }}"</span>
  </div>
</footer>

<script>
let dark = localStorage.getItem('adt-theme')==='dark';
const themeBtn = document.getElementById('themeBtn');
function setTheme(d){dark=d;document.documentElement.setAttribute('data-theme',d?'dark':'light');themeBtn.textContent=d?'☀️':'🌙';localStorage.setItem('adt-theme',d?'dark':'light')}
themeBtn.onclick=()=>setTheme(!dark);
if(dark) setTheme(true);
const hamburger=document.getElementById('hamburger');const mobileNav=document.getElementById('mobileNav');const mobileOverlay=document.getElementById('mobileOverlay');
hamburger.onclick=()=>{mobileNav.classList.toggle('open');mobileOverlay.classList.toggle('open')};
mobileOverlay.onclick=()=>{mobileNav.classList.remove('open');mobileOverlay.classList.remove('open')};
document.addEventListener('keydown',e=>{if(e.key==='Escape')document.getElementById('srchOverlay').classList.remove('open')});
window.addEventListener('scroll',()=>{
  document.getElementById('mainNav').classList.toggle('shadow',window.scrollY>30);
  const d=document.documentElement;document.getElementById('readBar').style.width=((window.scrollY/(d.scrollHeight-d.clientHeight))*100)+'%';
},{passive:true});
</script>
@stack('scripts')
</body>
</html>
