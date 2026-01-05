<?php

namespace App\Livewire\Frontend;

use Firefly\FilamentBlog\Models\Post;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class BlogSingle extends Component
{
    public $post;

    public function mount($slug)
    {
        $this->post = Post::where('slug', $slug)->first();
    }

    public function render()
    {
        return view('livewire.frontend.blog-single');
    }
}
