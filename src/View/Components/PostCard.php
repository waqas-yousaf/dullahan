<?php

namespace WaqasYousaf\Dullahan\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use WaqasYousaf\Dullahan\Models\Post;

class PostCard extends Component
{
    public function __construct(public Post $post)
    {
    }

    public function render(): View
    {
        return view('dullahan::components.post-card');
    }
}
