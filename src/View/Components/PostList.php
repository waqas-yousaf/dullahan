<?php

namespace WaqasYousaf\Dulluhan\View\Components;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class PostList extends Component
{
    public function __construct(
        public Collection|Paginator|Builder|array $posts,
        public ?string $search = null,
        public string|array|null $postType = null,
        public string|array|null $category = null,
        public ?string $status = null,
        public ?int $limit = null,
        public bool $paginate = false,
        public ?int $perPage = null,
    ) {
    }

    public function render(): View
    {
        return view('dulluhan::components.post-list', [
            'filteredPosts' => $this->filteredPosts(),
        ]);
    }

    public function filteredPosts(): Collection|Paginator
    {
        if ($this->posts instanceof Builder) {
            return $this->filterQuery(clone $this->posts);
        }

        if ($this->posts instanceof Paginator && method_exists($this->posts, 'getCollection')) {
            $this->posts->setCollection($this->filterCollection($this->posts->getCollection()));

            return $this->posts;
        }

        return $this->filterCollection($this->posts instanceof Collection ? $this->posts : collect($this->posts));
    }

    private function filterQuery(Builder $query): Collection|Paginator
    {
        $query
            ->when($this->search, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($this->postType, fn (Builder $query) => $query->whereIn('post_type', $this->values($this->postType)))
            ->when($this->status, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($this->category, function (Builder $query): void {
                $query->whereHas('category', function (Builder $query): void {
                    $values = $this->values($this->category);

                    $query->whereIn('id', array_filter($values, 'is_numeric'))
                        ->orWhereIn('slug', $values)
                        ->orWhereIn('name', $values);
                });
            })
            ->when($this->limit, fn (Builder $query, int $limit) => $query->limit($limit));

        if ($this->paginate) {
            return $query->paginate($this->perPage ?? config('dulluhan.pagination.posts_per_page', 12));
        }

        return $query->get();
    }

    private function filterCollection(Collection $posts): Collection
    {
        $filtered = $posts
            ->when($this->search, function (Collection $posts, string $search): Collection {
                $needle = mb_strtolower($search);

                return $posts->filter(function ($post) use ($needle): bool {
                    return str_contains(mb_strtolower((string) $post->title), $needle)
                        || str_contains(mb_strtolower((string) $post->content), $needle);
                });
            })
            ->when($this->postType, fn (Collection $posts) => $posts->whereIn('post_type', $this->values($this->postType)))
            ->when($this->status, fn (Collection $posts, string $status) => $posts->where('status', $status))
            ->when($this->category, function (Collection $posts): Collection {
                $values = $this->values($this->category);

                return $posts->filter(function ($post) use ($values): bool {
                    return $post->category && (
                        in_array((string) $post->category->id, $values, true)
                        || in_array($post->category->slug, $values, true)
                        || in_array($post->category->name, $values, true)
                    );
                });
            })
            ->values();

        return $this->limit ? $filtered->take($this->limit) : $filtered;
    }

    private function values(string|array|null $value): array
    {
        return collect(is_array($value) ? $value : explode(',', (string) $value))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
