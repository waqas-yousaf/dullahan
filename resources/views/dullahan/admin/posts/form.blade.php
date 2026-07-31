@extends('dullahan::admin.layout', ['title' => $post->exists ? 'Edit Post' : 'New Post'])

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@enzedonline/quill-blot-formatter2@3.2/dist/css/quill-blot-formatter2.min.css" rel="stylesheet">
    <style>
        :root {
            --btn-submit: #4f46e5;
            --btn-submit-hover: #4338ca;
            --seo-sub-border: #f1f5f9;
        }
        html[data-theme="dark"] {
            --btn-submit: #6366f1;
            --btn-submit-hover: #4f46e5;
            --seo-sub-border: #1e293b;
        }
        .btn.submit-primary {
            background: var(--btn-submit);
            color: #ffffff;
        }
        .btn.submit-primary:hover {
            background: var(--btn-submit-hover);
        }
        .seo-panel[open] {
            border-color: var(--accent) !important;
        }
        .seo-panel summary::-webkit-details-marker {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div class="topbar">
        <h1>{{ $post->exists ? 'Edit Post' : 'New Post' }}</h1>
        <div class="actions">
            <span id="autosave-status" class="muted">{{ $post->autosaved_at ? 'Autosaved ' . $post->autosaved_at->diffForHumans() : 'Autosave ready' }}</span>
            @if ($post->exists && $post->blogViewUrl())
                <a class="btn success" href="{{ $post->blogViewUrl() }}" target="_blank" rel="noopener">View in browser</a>
            @endif
            <button type="submit" form="dullahan-post-form" class="btn submit-primary">
                {{ $post->exists ? 'Update Post' : 'Save Post' }}
            </button>
            <a class="btn info" href="{{ route('dullahan.admin.posts.index') }}">All Posts</a>
        </div>
    </div>

    <form id="dullahan-post-form" class="panel" method="post" action="{{ $action }}" novalidate>
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
        </div>

        <div style="display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap; margin-bottom: 16px;">
            <div class="field" style="flex: 2; min-width: 200px; margin-bottom: 0;">
                <label for="featured_image">Featured image (URL)</label>
                <input id="featured_image" name="featured_image" type="url" value="{{ old('featured_image', $post->featured_image) }}">
                @error('featured_image') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom: 0; display: flex; align-items: center; gap: 8px; position: relative;">
                <input id="featured_image_file" type="file" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml" style="display:none;">
                <button id="featured_image_upload" class="btn secondary" type="button" style="height: 42px; white-space: nowrap;">Upload Image</button>
                <a id="featured_image_view" class="btn secondary" href="{{ old('featured_image', $post->featured_image) }}" target="_blank" rel="noopener" style="height: 42px; white-space: nowrap; {{ old('featured_image', $post->featured_image) ? '' : 'display: none;' }}">View Image</a>
                <span id="featured_image_upload_status" class="muted" style="position: absolute; bottom: -20px; left: 0; font-size: 12px; white-space: nowrap;"></span>
            </div>

            <div class="field" style="flex: 1.5; min-width: 200px; margin-bottom: 0;">
                <label for="featured_image_alt">Featured image alt text</label>
                <input id="featured_image_alt" name="featured_image_alt" type="text" value="{{ old('featured_image_alt', $post->featured_image_alt) }}">
                @error('featured_image_alt') <div class="error">{{ $message }}</div> @enderror
            </div>
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

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
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
                <label for="published_at">Publish at</label>
                <input id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                @error('published_at') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="field">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt" placeholder="Brief summary of the post...">{{ old('excerpt', $post->excerpt) }}</textarea>
            @error('excerpt') <div class="error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 7px;">
                <label for="content" style="margin: 0;">Content</label>
                <button type="button" id="btn-toggle-editor-mode" class="btn secondary" style="padding: 4px 8px; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                    <span class="material-icons" style="font-size: 16px;">code</span>
                    <span id="editor-mode-text">View Code</span>
                </button>
            </div>
            <input id="content" name="content" type="hidden" value="{{ old('content', $post->content) }}" required>
            <div id="editor-container-wrapper" style="position: relative;">
                <div id="dullahan-editor">{!! old('content', $post->content) !!}</div>
                <textarea id="dullahan-html-editor" style="display: none; font-family: monospace; font-size: 14px; min-height: 640px; resize: vertical; width: 100%; border: 1px solid var(--line); border-radius: 0 0 6px 6px; padding: 12px; background: var(--panel); color: var(--text);"></textarea>
            </div>
            <div id="content-client-error" class="error" hidden>Content must be at least 10 characters.</div>
            @error('content') <div class="error">{{ $message }}</div> @enderror
        </div>

        <details class="panel seo-panel" open style="padding: 24px; margin: 28px 0 0; background: var(--panel);">
            <summary style="cursor: pointer; list-style: none; display: flex; align-items: center; justify-content: space-between; outline: none; user-select: none;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span class="material-icons" style="color: var(--accent); font-size: 24px; vertical-align: middle;">travel_explore</span>
                    <h2 style="margin: 0; font-size: 18px; font-weight: 600; vertical-align: middle;">SEO Options</h2>
                </div>
                <span class="material-icons seo-toggle-icon" style="color: var(--muted); font-size: 24px; transition: transform 0.2s;">expand_more</span>
            </summary>

            <div style="margin-top: 24px; display: flex; flex-direction: column; gap: 24px;">
                <!-- Section 1: Standard Meta Tags -->
                <div style="border-bottom: 1px solid var(--seo-sub-border); padding-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                        <span class="material-icons" style="color: var(--muted); font-size: 18px; vertical-align: middle;">search</span>
                        <h3 style="margin: 0; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); vertical-align: middle;">Standard Meta Tags</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                        <div class="field" style="margin-bottom: 0;">
                            <label for="meta_title" style="font-size: 14px;">Meta Title</label>
                            <input id="meta_title" name="meta_title" value="{{ old('meta_title', $post->meta_title) }}" minlength="50" maxlength="70" placeholder="Enter meta title...">
                            <div class="muted" style="font-size: 12px; margin-top: 4px;">Must be between 50 and 70 characters.</div>
                            @error('meta_title') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field" style="margin-bottom: 0;">
                            <label for="canonical_url" style="font-size: 14px;">Canonical URL</label>
                            <input id="canonical_url" name="canonical_url" type="url" value="{{ old('canonical_url', $post->canonical_url) }}" placeholder="https://example.com/canonical-url">
                            @error('canonical_url') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="field" style="margin-top: 16px; margin-bottom: 0;">
                        <label for="meta_description" style="font-size: 14px;">Meta Description</label>
                        <textarea id="meta_description" name="meta_description" maxlength="170" placeholder="Brief summary of the page for search engines..." style="min-height: 80px;">{{ old('meta_description', $post->meta_description) }}</textarea>
                        <div class="muted" style="font-size: 12px; margin-top: 4px;">Best around 140-160 characters.</div>
                        @error('meta_description') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-top: 16px;">
                        <div class="field" style="margin-bottom: 0;">
                            <label for="meta_keywords" style="font-size: 14px;">Meta Keywords</label>
                            <input id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $post->meta_keywords) }}" maxlength="255" placeholder="keywords, separated, by, commas">
                            @error('meta_keywords') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field" style="margin-bottom: 0;">
                            <label for="robots" style="font-size: 14px;">Robots</label>
                            <select id="robots" name="robots">
                                @foreach (['index,follow', 'index,nofollow', 'noindex,follow', 'noindex,nofollow'] as $robotsOption)
                                    <option value="{{ $robotsOption }}" @selected(old('robots', $post->robots ?: 'index,follow') === $robotsOption)>{{ $robotsOption }}</option>
                                @endforeach
                            </select>
                            @error('robots') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Open Graph (Social Sharing) -->
                <div style="border-bottom: 1px solid var(--seo-sub-border); padding-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                        <span class="material-icons" style="color: var(--muted); font-size: 18px; vertical-align: middle;">share</span>
                        <h3 style="margin: 0; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); vertical-align: middle;">Social Sharing (Open Graph)</h3>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
                        <div class="field" style="margin-bottom: 0;">
                            <label for="og_title" style="font-size: 14px;">Open Graph Title</label>
                            <input id="og_title" name="og_title" value="{{ old('og_title', $post->og_title) }}" maxlength="95" placeholder="Social media post title...">
                            @error('og_title') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="field" style="margin-bottom: 0;">
                            <label for="og_image" style="font-size: 14px;">Open Graph Image URL</label>
                            <input id="og_image" name="og_image" type="url" value="{{ old('og_image', $post->og_image) }}" placeholder="https://example.com/share-image.jpg">
                            @error('og_image') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="field" style="margin-top: 16px; margin-bottom: 0;">
                        <label for="og_description" style="font-size: 14px;">Open Graph Description</label>
                        <textarea id="og_description" name="og_description" maxlength="200" placeholder="Social media post description..." style="min-height: 80px;">{{ old('og_description', $post->og_description) }}</textarea>
                        @error('og_description') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Section 3: Schema Markup / JSON-LD -->
                <div>
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                        <span class="material-icons" style="color: var(--muted); font-size: 18px; vertical-align: middle;">code</span>
                        <h3 style="margin: 0; font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: var(--muted); vertical-align: middle;">Schema JSON-LD</h3>
                    </div>

                    <div class="field" style="margin-bottom: 0;">
                        <label for="schema_markup" style="font-size: 14px;">Structured Data Schema</label>
                        @php
                            $schemaMarkup = old('schema_markup', is_array($post->schema_markup) ? json_encode($post->schema_markup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $post->schema_markup);
                        @endphp
                        <textarea id="schema_markup" name="schema_markup" placeholder='{"@context":"https://schema.org","@type":"Article"}' style="font-family: monospace; font-size: 13px; min-height: 120px;">{{ $schemaMarkup }}</textarea>
                        <div class="muted" style="font-size: 12px; margin-top: 4px;">Valid JSON-LD schema markup block.</div>
                        @error('schema_markup') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </details>

        <button class="btn submit-primary" type="submit">{{ $post->exists ? 'Update Post' : 'Save Post' }}</button>
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/katex@0.16.11/dist/katex.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@enzedonline/quill-blot-formatter2@3.2/dist/index.min.js"></script>
    <script>
        const contentInput = document.getElementById('content');
        const form = document.getElementById('dullahan-post-form');
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
        const featuredImageAltInput = document.getElementById('featured_image_alt');
        const featuredImageFile = document.getElementById('featured_image_file');
        const featuredImageUpload = document.getElementById('featured_image_upload');
        const featuredImageUploadStatus = document.getElementById('featured_image_upload_status');
        const featuredImageView = document.getElementById('featured_image_view');
        const uploadUrl = @json(route('dullahan.admin.uploads.images'));
        let autosaveUrl = @json($post->exists ? route('dullahan.admin.posts.autosave.existing', $post) : route('dullahan.admin.posts.autosave'));
        let postExists = @json($post->exists);
        const csrfToken = @json(csrf_token());
        const autosaveInterval = @json(config('dullahan.autosave.interval_ms', 30000));
        let autosaveTimer = null;
        let autosavePending = false;

        if (typeof QuillBlotFormatter2 !== 'undefined') {
            QuillBlotFormatter2.default.registerFormats(Quill);
            Quill.register('modules/blotFormatter2', QuillBlotFormatter2.default);
        }

        const quill = new Quill('#dullahan-editor', {
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
                },
                blotFormatter2: {
                    image: {
                        allowAltTitleEdit: true,
                        registerImageTitleBlot: true
                    }
                }
            }
        });

        let htmlMode = false;
        const btnToggleMode = document.getElementById('btn-toggle-editor-mode');
        const editorModeText = document.getElementById('editor-mode-text');
        const htmlEditor = document.getElementById('dullahan-html-editor');
        const editorContainer = document.getElementById('dullahan-editor');

        function syncContent() {
            if (htmlMode) {
                contentInput.value = htmlEditor.value;
            } else {
                contentInput.value = quill.root.innerHTML;
            }
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

            const titleInput = document.getElementById('title');
            const metaTitleInput = document.getElementById('meta_title');
            let seoTitle = '';
            if (metaTitleInput && metaTitleInput.value.trim() !== '') {
                seoTitle = metaTitleInput.value.trim();
            } else if (titleInput && titleInput.value.trim() !== '') {
                seoTitle = titleInput.value.trim();
            }
            if (seoTitle !== '') {
                formData.append('title', seoTitle);
            }

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
                    if (featuredImageView) {
                        featuredImageView.href = url;
                        featuredImageView.style.display = 'inline-flex';
                    }
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
            if (featuredImageView) {
                featuredImageView.href = featuredImageInput.value;
                featuredImageView.style.display = featuredImageInput.value ? 'inline-flex' : 'none';
            }
        });

        form.querySelectorAll('input, select, textarea').forEach(input => {
            if (input !== htmlEditor) {
                input.addEventListener('input', markAutosavePending);
                input.addEventListener('change', markAutosavePending);
            }
        });

        if (btnToggleMode && htmlEditor) {
            btnToggleMode.addEventListener('click', function () {
                const toolbar = form.querySelector('.ql-toolbar');
                htmlMode = !htmlMode;

                if (htmlMode) {
                    htmlEditor.value = quill.root.innerHTML;
                    editorContainer.style.display = 'none';
                    htmlEditor.style.display = 'block';
                    if (toolbar) toolbar.style.display = 'none';
                    editorModeText.textContent = 'Rich Text';
                    btnToggleMode.querySelector('.material-icons').textContent = 'edit';
                } else {
                    quill.root.innerHTML = htmlEditor.value;
                    syncContent();
                    htmlEditor.style.display = 'none';
                    editorContainer.style.display = 'block';
                    if (toolbar) toolbar.style.display = 'block';
                    editorModeText.textContent = 'View Code';
                    btnToggleMode.querySelector('.material-icons').textContent = 'code';
                }
            });

            htmlEditor.addEventListener('input', function () {
                contentInput.value = htmlEditor.value;
                markAutosavePending();
            });
        }

        form.addEventListener('submit', event => {
            autosavePending = false;
            if (htmlMode) {
                quill.root.innerHTML = htmlEditor.value;
            }
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
