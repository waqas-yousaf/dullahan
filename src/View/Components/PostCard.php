<?php

namespace YourVendor\Dulluhan\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use YourVendor\Dulluhan\Models\Post;

class PostCard extends Component
{
    public function __construct(public Post $post)
    {
    }

    public function render(): View
    {
        return view('dulluhan::components.post-card');
    }
}
