<?php

namespace YourVendor\Dulluhan\View\Components;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class PostList extends Component
{
    public function __construct(public Collection|Paginator $posts)
    {
    }

    public function render(): View
    {
        return view('dulluhan::components.post-list');
    }
}
