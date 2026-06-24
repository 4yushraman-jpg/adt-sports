@extends('layouts.admin')
@section('title','Comments')
@section('content')

<div class="page-hd">
  <div><h1>Comments</h1><div class="page-hd-sub">{{ $pending->count() }} awaiting moderation</div></div>
</div>

{{-- Pending queue --}}
<div class="table-wrap" style="margin-bottom:24px">
  <div class="table-hd"><h3><i class="fa-solid fa-clock" style="color:var(--amber)"></i> Pending</h3></div>
  <table>
    <thead><tr><th>Author</th><th>Comment</th><th>Article</th><th>When</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($pending as $c)
      <tr>
        <td>
          <div style="font-weight:500;color:var(--ink)">{{ $c->author_name }}</div>
          <div style="font-size:11px;color:var(--ink3)">{{ $c->author_email }}</div>
        </td>
        <td style="font-size:13px;color:var(--ink2);max-width:340px">{!! $c->body !!}</td>
        <td style="font-size:12px">
          <a href="{{ route('article', $c->article->slug) }}" target="_blank" style="color:var(--brand)">{{ Str::limit($c->article->title, 40) }}</a>
        </td>
        <td style="font-size:11px;color:var(--ink3)">{{ $c->created_at->diffForHumans() }}</td>
        <td style="white-space:nowrap">
          <form action="{{ route('admin.comments.approve', $c) }}" method="POST" style="display:inline">
            @csrf @method('PUT')
            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-check"></i> Approve</button>
          </form>
          <form action="{{ route('admin.comments.destroy', $c) }}" method="POST" style="display:inline"
                onsubmit="return confirm('Delete this comment?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i></button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5"><div class="empty-state"><i class="fa-solid fa-champagne-glasses"></i>Nothing awaiting moderation</div></td></tr>
      @endforelse
    </tbody>
  </table>
</div>

{{-- Approved --}}
<div class="table-wrap">
  <div class="table-hd"><h3><i class="fa-solid fa-circle-check" style="color:var(--green)"></i> Approved</h3></div>
  <table>
    <thead><tr><th>Author</th><th>Comment</th><th>Article</th><th>When</th><th>Actions</th></tr></thead>
    <tbody>
      @forelse($approved as $c)
      <tr>
        <td>
          <div style="font-weight:500;color:var(--ink)">{{ $c->author_name }}</div>
          <div style="font-size:11px;color:var(--ink3)">{{ $c->author_email }}</div>
        </td>
        <td style="font-size:13px;color:var(--ink2);max-width:340px">{!! $c->body !!}</td>
        <td style="font-size:12px">
          <a href="{{ route('article', $c->article->slug) }}" target="_blank" style="color:var(--brand)">{{ Str::limit($c->article->title, 40) }}</a>
        </td>
        <td style="font-size:11px;color:var(--ink3)">{{ $c->created_at->diffForHumans() }}</td>
        <td>
          <form action="{{ route('admin.comments.destroy', $c) }}" method="POST" style="display:inline"
                onsubmit="return confirm('Delete this comment?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash-can"></i></button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5"><div class="empty-state"><i class="fa-regular fa-comment-dots"></i>No approved comments yet</div></td></tr>
      @endforelse
    </tbody>
  </table>
  @if($approved->hasPages())
    <div style="padding:14px">{{ $approved->links() }}</div>
  @endif
</div>

@endsection
