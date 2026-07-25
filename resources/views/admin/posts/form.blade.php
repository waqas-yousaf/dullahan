@extends('dulluhan::admin.layout', ['title' => $post->exists ? 'Edit Post' : 'New Post'])

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="topbar">
        <h1>{{ $post->exists ? 'Edit Post' : 'New Post' }}</h1>
        <a class="btn secondary" href="{{ route('dulluhan.admin.posts.index') }}">All Posts</a>
    </div>

    <form id="dulluhan-post-form" class="panel" method="post" action="{{ $action }}" novalidate>
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div class="field">
            <label for="title">Title</label>
            <input id="title" name="title" value="{{ old('title', $post->title) }}" maxlength="255" required>
            @error('title') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="featured_image">Featured image URL</label>
            <input id="featured_image" name="featured_image" type="url" value="{{ old('featured_image', $post->featured_image) }}">
            @error('featured_image') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt">{{ old('excerpt', $post->excerpt) }}</textarea>
        </div>

        <div class="field">
            <label for="content">Content</label>
            <input id="content" name="content" type="hidden" value="{{ old('content', $post->content) }}" required>
            <div id="dulluhan-editor">{!! old('content', $post->content) !!}</div>
            <div id="content-client-error" class="error" hidden>Content must be at least 10 characters.</div>
            @error('content') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="grid">
            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="published_at">Publish at</label>
                <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                @error('published_at') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <button class="btn" type="submit">Save Post</button>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script>
        const contentInput = document.getElementById('content');
        const form = document.getElementById('dulluhan-post-form');
        const contentError = document.getElementById('content-client-error');
        const uploadUrl = @json(route('dulluhan.admin.uploads.images'));
        const csrfToken = @json(csrf_token());

        const quill = new Quill('#dulluhan-editor', {
            theme: 'snow',
            modules: {
                toolbar: {
                    container: [
                        [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ color: [] }, { background: [] }],
                        [{ script: 'sub' }, { script: 'super' }],
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],
                        [{ align: [] }, { indent: '-1' }, { indent: '+1' }],
                        [{ list: 'ordered' }, { list: 'bullet' }, { direction: 'rtl' }],
                        ['blockquote', 'code-block'],
                        ['link', 'image', 'video', 'formula'],
                        ['clean']
                    ],
                    handlers: {
                        image: imageHandler
                    }
                }
            }
        });

        function syncContent() {
            contentInput.value = quill.root.innerHTML;
        }

        function imageHandler() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/jpg,image/webp,image/svg+xml';
            input.click();
            input.onchange = () => uploadImage(input.files[0]);
        }

        function uploadImage(file) {
            if (!file) return;
            const formData = new FormData();
            formData.append('image', file);

            fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: formData
            })
                .then(response => {
                    if (!response.ok) throw new Error('Upload failed');
                    return response.json();
                })
                .then(({ url }) => {
                    const range = quill.getSelection(true);
                    quill.insertEmbed(range.index, 'image', url);
                    quill.setSelection(range.index + 1);
                    syncContent();
                })
                .catch(() => alert('Image upload failed.'));
        }

        quill.root.addEventListener('drop', event => {
            const file = [...event.dataTransfer.files].find(item => item.type.startsWith('image/'));
            if (!file) return;
            event.preventDefault();
            uploadImage(file);
        });

        quill.on('text-change', syncContent);

        form.addEventListener('submit', event => {
            syncContent();
            const text = quill.getText().trim();
            contentError.hidden = text.length >= 10;

            if (!form.checkValidity() || text.length < 10) {
                event.preventDefault();
                form.reportValidity();
            }
        });
    </script>
@endpush
