@extends('dulluhan::admin.layout', ['title' => $post->exists ? 'Edit Post' : 'New Post'])

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="topbar">
        <h1>{{ $post->exists ? 'Edit Post' : 'New Post' }}</h1>
        <div class="actions">
            <span id="autosave-status" class="muted">{{ $post->autosaved_at ? 'Autosaved ' . $post->autosaved_at->diffForHumans() : 'Autosave ready' }}</span>
            <a class="btn secondary" href="{{ route('dulluhan.admin.posts.index') }}">All Posts</a>
        </div>
    </div>

    <form id="dulluhan-post-form" class="panel" method="post" action="{{ $action }}" novalidate>
        @csrf
        @if ($method !== 'POST')
            <input id="form-method" type="hidden" name="_method" value="{{ $method }}">
        @endif

        <div class="field">
            <label for="title">Title</label>
            <input id="title" name="title" value="{{ old('title', $post->title) }}" maxlength="255" required>
            @error('title') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="grid">
            <div class="field">
                <label for="post_type">Post type</label>
                <select id="post_type" name="post_type" required>
                    @foreach ($postTypes as $value => $label)
                        <option value="{{ $value }}" @selected(old('post_type', $post->post_type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('post_type') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="status">Status</label>
                <select id="status" name="status" required>
                    <option value="draft" @selected(old('status', $post->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $post->status) === 'published')>Published</option>
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="field">
            <label for="featured_image">Featured image URL</label>
            <input id="featured_image" name="featured_image" type="url" value="{{ old('featured_image', $post->featured_image) }}">
            @error('featured_image') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label>Categories</label>
            <div class="checkbox-grid">
                @php
                    $selectedCategories = collect(old('categories', $post->categories->pluck('id')->all()))->map(fn ($id) => (int) $id)->all();
                @endphp
                @forelse ($categories as $category)
                    <label>
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" @checked(in_array($category->id, $selectedCategories, true))>
                        {{ $category->name }}
                    </label>
                @empty
                    <span class="muted">Create categories first to assign them here.</span>
                @endforelse
            </div>
            @error('categories') <div class="error">{{ $message }}</div> @enderror
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

        <div class="field">
            <label for="published_at">Publish at</label>
            <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
            @error('published_at') <div class="error">{{ $message }}</div> @enderror
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
        const autosaveStatus = document.getElementById('autosave-status');
        const uploadUrl = @json(route('dulluhan.admin.uploads.images'));
        let autosaveUrl = @json($post->exists ? route('dulluhan.admin.posts.autosave.existing', $post) : route('dulluhan.admin.posts.autosave'));
        let postExists = @json($post->exists);
        const csrfToken = @json(csrf_token());
        const autosaveInterval = @json(config('dulluhan.autosave.interval_ms', 30000));
        let autosaveTimer = null;
        let autosavePending = false;

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

        function collectFormData() {
            syncContent();
            const formData = new FormData(form);
            formData.delete('_method');
            return formData;
        }

        function markAutosavePending() {
            autosavePending = true;
            autosaveStatus.textContent = 'Unsaved changes';
            clearTimeout(autosaveTimer);
            autosaveTimer = setTimeout(runAutosave, autosaveInterval);
        }

        function runAutosave() {
            if (!autosavePending) return;
            autosavePending = false;
            autosaveStatus.textContent = 'Autosaving...';

            fetch(autosaveUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: collectFormData()
            })
                .then(response => {
                    if (!response.ok) throw new Error('Autosave failed');
                    return response.json();
                })
                .then(data => {
                    autosaveStatus.textContent = 'Autosaved just now';

                    if (!postExists && data.edit_url) {
                        postExists = true;
                        autosaveUrl = data.autosave_url;
                        form.action = data.update_url;

                        if (!document.getElementById('form-method')) {
                            const methodInput = document.createElement('input');
                            methodInput.id = 'form-method';
                            methodInput.type = 'hidden';
                            methodInput.name = '_method';
                            methodInput.value = 'PUT';
                            form.appendChild(methodInput);
                        }

                        window.history.replaceState({}, '', data.edit_url);
                    }
                })
                .catch(() => {
                    autosaveStatus.textContent = 'Autosave failed';
                    autosavePending = true;
                });
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
                    markAutosavePending();
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
        quill.on('text-change', markAutosavePending);

        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.addEventListener('input', markAutosavePending);
            input.addEventListener('change', markAutosavePending);
        });

        form.addEventListener('submit', event => {
            autosavePending = false;
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
