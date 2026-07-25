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
            @if ($post->exists && $post->status === 'published')
                <a class="btn secondary" href="{{ $post->publicUrl() }}" target="_blank">View Post</a>
            @endif
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

        <div class="field">
            <label for="slug">Slug</label>
            <div style="display: flex; gap: 8px;">
                <input id="slug" name="slug" value="{{ old('slug', $post->slug) }}" maxlength="255" placeholder="generated-from-title" {!! $post->exists ? 'readonly style="background: var(--bg);"' : '' !!}>
                @if ($post->exists)
                    <button id="btn-edit-slug" class="btn secondary" type="button" style="height: 38px; white-space: nowrap;">Edit Slug</button>
                @endif
            </div>
            <div class="muted">Leave empty to generate it from the title.</div>
            @error('slug') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="grid">
            <div class="field">
                <label for="author_id">Author</label>
                <select id="author_id" name="author_id" required>
                    @foreach ($authors as $author)
                        <option value="{{ $author->id }}" @selected((int) old('author_id', $post->author_id) === (int) $author->id)>{{ $author->name }} ({{ $author->email }})</option>
                    @endforeach
                </select>
                @error('author_id') <div class="error">{{ $message }}</div> @enderror
            </div>

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
            <div class="actions" style="margin-top:10px;">
                <input id="featured_image_file" type="file" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml" style="display:none;">
                <button id="featured_image_upload" class="btn secondary" type="button">Upload Featured Image</button>
                <span id="featured_image_upload_status" class="muted"></span>
            </div>
            <div id="featured_image_preview_wrap" style="margin-top:12px;{{ old('featured_image', $post->featured_image) ? '' : 'display:none;' }}">
                <img id="featured_image_preview" src="{{ old('featured_image', $post->featured_image) }}" alt="" style="display:block;width:min(360px,100%);aspect-ratio:16/9;object-fit:cover;border:1px solid #e5e7eb;border-radius:8px;">
            </div>
            @error('featured_image') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="category_id">Category</label>
            @if ($categories->isEmpty())
                <div><span class="muted">Create categories first to assign them here.</span></div>
            @else
                <select id="category_id" name="category_id" style="width: 100%;">
                    <option value="">No Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            @endif
            @error('category_id') <div class="error">{{ $message }}</div> @enderror
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

        <section class="panel" style="padding:16px;margin:22px 0 0;">
            <h2 style="margin-top:0;">SEO Options</h2>
            <div class="grid">
                <div class="field">
                    <label for="meta_title">Meta title</label>
                    <input id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" maxlength="70">
                    <div class="muted">Best around 50-60 characters.</div>
                    @error('meta_title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="canonical_url">Canonical URL</label>
                    <input id="canonical_url" name="canonical_url" type="url" value="{{ old('canonical_url', $post->canonical_url) }}">
                    @error('canonical_url') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field">
                <label for="meta_description">Meta description</label>
                <textarea id="meta_description" name="meta_description" maxlength="170">{{ old('meta_description', $post->meta_description) }}</textarea>
                <div class="muted">Best around 140-160 characters.</div>
                @error('meta_description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="meta_keywords">Meta keywords</label>
                <input id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}" maxlength="255">
                @error('meta_keywords') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="grid">
                <div class="field">
                    <label for="og_title">Open Graph title</label>
                    <input id="og_title" name="og_title" value="{{ old('og_title', $post->og_title) }}" maxlength="95">
                    @error('og_title') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="field">
                    <label for="og_image">Open Graph image URL</label>
                    <input id="og_image" name="og_image" type="url" value="{{ old('og_image', $post->og_image) }}">
                    @error('og_image') <div class="error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="field">
                <label for="og_description">Open Graph description</label>
                <textarea id="og_description" name="og_description" maxlength="200">{{ old('og_description', $post->og_description) }}</textarea>
                @error('og_description') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="robots">Robots</label>
                <select id="robots" name="robots">
                    @foreach (['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'] as $robotsOption)
                        <option value="{{ $robotsOption }}" @selected(old('robots', $post->robots ?: 'index,follow') === $robotsOption)>{{ $robotsOption }}</option>
                    @endforeach
                </select>
                @error('robots') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="schema_markup">Schema JSON</label>
                @php
                    $schemaMarkup = old('schema_markup', is_array($post->schema_markup) ? json_encode($post->schema_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $post->schema_markup);
                @endphp
                <textarea id="schema_markup" name="schema_markup" placeholder='{"@@context":"https://schema.org","@@type":"Article"}'>{{ $schemaMarkup }}</textarea>
                @error('schema_markup') <div class="error">{{ $message }}</div> @enderror
            </div>
        </section>

        <button class="btn" type="submit">Save Post</button>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script>
        const contentInput = document.getElementById('content');
        const form = document.getElementById('dulluhan-post-form');
        const btnEditSlug = document.getElementById('btn-edit-slug');
        const slugInput = document.getElementById('slug');

        if (btnEditSlug && slugInput) {
            btnEditSlug.addEventListener('click', () => {
                slugInput.removeAttribute('readonly');
                slugInput.style.background = '';
                slugInput.focus();
                btnEditSlug.style.display = 'none';
            });
        }
        const contentError = document.getElementById('content-client-error');
        const autosaveStatus = document.getElementById('autosave-status');
        const featuredImageInput = document.getElementById('featured_image');
        const featuredImageFile = document.getElementById('featured_image_file');
        const featuredImageUpload = document.getElementById('featured_image_upload');
        const featuredImageUploadStatus = document.getElementById('featured_image_upload_status');
        const featuredImagePreviewWrap = document.getElementById('featured_image_preview_wrap');
        const featuredImagePreview = document.getElementById('featured_image_preview');
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

        function uploadFeaturedImage(file) {
            if (!file) return;
            const formData = new FormData();
            formData.append('image', file);
            featuredImageUploadStatus.textContent = 'Uploading...';

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
                    featuredImageInput.value = url;
                    featuredImagePreview.src = url;
                    featuredImagePreviewWrap.style.display = 'block';
                    featuredImageUploadStatus.textContent = 'Uploaded';
                    markAutosavePending();
                })
                .catch(() => {
                    featuredImageUploadStatus.textContent = 'Upload failed';
                });
        }

        quill.root.addEventListener('drop', event => {
            const file = [...event.dataTransfer.files].find(item => item.type.startsWith('image/'));
            if (!file) return;
            event.preventDefault();
            uploadImage(file);
        });

        quill.on('text-change', syncContent);
        quill.on('text-change', markAutosavePending);

        featuredImageUpload.addEventListener('click', () => featuredImageFile.click());
        featuredImageFile.addEventListener('change', () => uploadFeaturedImage(featuredImageFile.files[0]));
        featuredImageInput.addEventListener('input', () => {
            featuredImagePreview.src = featuredImageInput.value;
            featuredImagePreviewWrap.style.display = featuredImageInput.value ? 'block' : 'none';
        });

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
