@extends('layouts.admin')
@section('title','All Articles')
@section('content')

<div class="page-hd">
  <div><h1>All Articles</h1><div class="page-hd-sub">{{ $articles->total() }} total</div></div>
  <div style="display:flex;gap:8px;align-items:center">
    <a href="{{ route('admin.articles.trash') }}" class="btn btn-ghost">🗑️ Trash</a>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">✍️ New Article</a>
  </div>
</div>

<div class="table-wrap">
  <div class="table-hd">
    <h3>Articles</h3>
    <div class="table-filters">
      <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        <input type="text" name="search" class="search-input" placeholder="Search title…" value="{{ request('search') }}">
        <select name="status" class="filter-select" onchange="this.form.submit()">
          <option value="">All Status</option>
          <option value="published" {{ request('status')=='published'?'selected':'' }}>Published</option>
          <option value="draft"     {{ request('status')=='draft'?'selected':'' }}>Drafts</option>
        </select>
        <select name="category" class="filter-select" onchange="this.form.submit()">
          <option value="">All Categories</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ request('category')==$c->id?'selected':'' }}>{{ $c->name }}</option>
          @endforeach
        </select>
        <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        @if(request()->hasAny(['search','status','category']))
          <a href="{{ route('admin.articles.index') }}" class="btn btn-ghost btn-sm">✕ Clear</a>
        @endif
      </form>
    </div>
  </div>

  <table>
    <thead>
      <tr><th>Title</th><th>Category</th><th>Author</th><th>Status</th><th>Views</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
      @forelse($articles as $a)
      <tr>
        <td class="td-title">
          <a href="{{ route('admin.articles.edit',$a) }}" style="color:var(--ink)">{{ Str::limit($a->title,55) }}</a>
          <small>{{ $a->slug }}</small>
        </td>
        <td>
          @if($a->category)
            <span style="color:{{ $a->category->color }};font-size:12px;font-weight:500">● {{ $a->category->name }}</span>
          @else <span style="color:var(--ink3)">—</span> @endif
        </td>
        <td style="font-size:12px;color:var(--ink3)">{{ $a->author?->name ?? '—' }}</td>
        <td>
          @if($a->isScheduled())
            <span class="badge badge-published" style="background:#7C3AED1a;color:#7C3AED;border-color:#7C3AED55"
                  title="Goes live {{ $a->published_at->format('d M Y, H:i') }}">🕒 scheduled</span>
            <div style="font-size:10px;color:var(--ink3);margin-top:3px">{{ $a->published_at->format('d M Y, H:i') }}</div>
          @else
            <span class="badge badge-{{ $a->status }}">{{ $a->status }}</span>
          @endif
          @if($a->featured) <span style="font-size:12px" title="Featured">⭐</span> @endif
          @if($a->breaking) <span style="font-size:12px" title="Breaking">🔴</span> @endif
        </td>
        <td style="font-weight:500">{{ number_format($a->views) }}</td>
        <td style="font-size:11px;color:var(--ink3);white-space:nowrap">{{ $a->created_at->format('d M Y') }}</td>
        <td>
          <div class="actions">
            <a href="{{ route('admin.articles.edit',$a) }}" class="btn btn-ghost btn-sm">✏️ Edit</a>
            @if($a->status==='draft')
              <form action="{{ route('admin.articles.update',$a) }}" method="POST" style="display:inline">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $a->title }}">
                <input type="hidden" name="status" value="published">
                <button type="submit" class="btn btn-success btn-sm" title="Publish">🚀</button>
              </form>
            @else
              <form action="{{ route('admin.articles.update',$a) }}" method="POST" style="display:inline">
                @csrf @method('PUT')
                <input type="hidden" name="title" value="{{ $a->title }}">
                <input type="hidden" name="status" value="draft">
                <button type="submit" class="btn btn-amber btn-sm" title="Unpublish">📝</button>
              </form>
            @endif
            <form action="{{ route('admin.articles.destroy',$a) }}" method="POST" style="display:inline"
                  onsubmit="return confirm('Delete this article? Cannot be undone.')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr><td colspan="7" style="text-align:center;padding:32px;color:var(--ink3)">
        No articles found. <a href="{{ route('admin.articles.create') }}" style="color:var(--brand)">Write one?</a>
      </td></tr>
      @endforelse
    </tbody>
  </table>

  @if($articles->hasPages())
    <div style="padding:12px 16px;border-top:1px solid var(--border)">{{ $articles->links() }}</div>
  @endif
  <div style="padding:8px 16px;font-size:11px;color:var(--ink3);border-top:1px solid var(--border2)">
    Showing {{ $articles->firstItem() }}–{{ $articles->lastItem() }} of {{ $articles->total() }}
  </div>
</div>
@endsection
