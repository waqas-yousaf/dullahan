@extends('dullahan::admin.layout', ['title' => 'Dullahan Dashboard'])

@push('head')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .metric-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            transition: all 0.25s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
            border-color: var(--accent);
        }
        
        .metric-icon-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: rgba(79, 70, 229, 0.08);
            color: #4f46e5;
        }
        
        .card-total .metric-icon-wrap {
            background: rgba(79, 70, 229, 0.08);
            color: #4f46e5;
        }
        .card-published .metric-icon-wrap {
            background: rgba(16, 185, 129, 0.08);
            color: #10b981;
        }
        .card-drafts .metric-icon-wrap {
            background: rgba(245, 158, 11, 0.08);
            color: #f59e0b;
        }
        
        /* Dark theme icon backgrounds */
        html[data-theme="dark"] .card-total .metric-icon-wrap {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }
        html[data-theme="dark"] .card-published .metric-icon-wrap {
            background: rgba(52, 211, 153, 0.15);
            color: #34d399;
        }
        html[data-theme="dark"] .card-drafts .metric-icon-wrap {
            background: rgba(251, 191, 36, 0.15);
            color: #fbbf24;
        }

        .metric-data {
            display: flex;
            flex-direction: column;
        }
        
        .metric-label {
            font-size: 14px;
            font-weight: 500;
            color: var(--muted);
        }
        
        .metric-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.2;
            margin-top: 4px;
        }
        
        /* Recent posts styling */
        .recent-posts-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 20px;
        }
        
        .post-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        
        .post-row:hover {
            border-color: var(--accent);
            background: var(--bg);
        }
        
        .post-info-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .post-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
        }
        
        .post-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        
        .post-title:hover {
            color: var(--accent);
        }
        
        .post-meta-line {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
            margin-top: 4px;
        }
        
        .author-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: var(--line);
            color: var(--text);
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        
        .status-badge.published {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }
        
        .status-badge.draft {
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
        }
        
        .post-time-meta {
            font-size: 13px;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }
    </style>
@endpush

@section('content')
    <div class="topbar">
        <h1>Dashboard</h1>
        <a class="btn submit-primary" href="{{ route('dullahan.admin.posts.create') }}">New Post</a>
    </div>

    <section class="grid" style="margin-bottom: 28px;">
        <div class="metric-card card-total">
            <div class="metric-icon-wrap">
                <span class="material-icons">article</span>
            </div>
            <div class="metric-data">
                <span class="metric-label">Total posts</span>
                <strong class="metric-value">{{ $totalPosts }}</strong>
            </div>
        </div>
        
        <div class="metric-card card-published">
            <div class="metric-icon-wrap">
                <span class="material-icons">check_circle</span>
            </div>
            <div class="metric-data">
                <span class="metric-label">Published</span>
                <strong class="metric-value">{{ $publishedPosts }}</strong>
            </div>
        </div>

        <div class="metric-card card-drafts">
            <div class="metric-icon-wrap">
                <span class="material-icons">edit_document</span>
            </div>
            <div class="metric-data">
                <span class="metric-label">Drafts</span>
                <strong class="metric-value">{{ $draftPosts }}</strong>
            </div>
        </div>
    </section>

    <section class="panel" style="padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--line); padding-bottom: 14px; margin-bottom: 16px;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600;">Recent posts</h2>
            <span class="material-icons" style="color: var(--muted);">history</span>
        </div>
        
        <div class="recent-posts-list">
            @forelse ($recentPosts as $post)
                <div class="post-row">
                    <div class="post-info-main">
                        <div class="post-icon">
                            <span class="material-icons">article</span>
                        </div>
                        <div>
                            <a href="{{ route('dullahan.admin.posts.edit', $post) }}" class="post-title">{{ $post->title }}</a>
                            <div class="post-meta-line">
                                <span>by</span>
                                <span class="author-badge">
                                    <span class="material-icons" style="font-size: 12px; vertical-align: middle;">person</span>
                                    <span style="vertical-align: middle;">{{ $post->author?->name ?? 'Unknown' }}</span>
                                </span>
                                <span>•</span>
                                <span class="status-badge {{ $post->status }}">
                                    {{ $post->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="post-time-meta">
                        <span class="material-icons" style="font-size: 16px;">schedule</span>
                        <span>{{ $post->autosaved_at ? 'autosaved ' . $post->autosaved_at->diffForHumans() : 'saved ' . $post->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px 0;">
                    <span class="material-icons" style="font-size: 48px; color: var(--muted); margin-bottom: 12px;">description</span>
                    <p class="muted" style="margin: 0;">No posts yet. Get started by creating your first post!</p>
                </div>
            @endforelse
        </div>
    </section>
@endsection
