<?php

namespace App\Livewire\Frontend;

use Firefly\FilamentBlog\Models\Category;
use Firefly\FilamentBlog\Models\Post;
use Firefly\FilamentBlog\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;

class Blog extends Component
{
    use WithPagination;

    public $selectedCategory = null;

    public $selectedTag = null;

    public $searchTerm = '';

    public $postsPerPage = 6;

    public function mount()
    {
        // Initialization
    }

    // Filter posts by category
    public function filterByCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage(); // Reset pagination when filtering
    }

    // Filter posts by tag
    public function filterByTag($tagId)
    {
        $this->selectedTag = $tagId;
        $this->resetPage();
    }

    // Clear the filters
    public function clearFilters()
    {
        $this->selectedCategory = null;
        $this->selectedTag = null;
        $this->resetPage();
    }

    // Search through posts
    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Base query for posts
        $query = Post::query()->with(['tags', 'comments']);
        // Filter by category if selected
        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        // Filter by tag if selected
        if ($this->selectedTag) {
            $query->whereHas('tags', function ($q) {
                $q->where('id', $this->selectedTag);
            });
        }

        // Apply search term if set
        if ($this->searchTerm) {
            $query->where('title', 'like', '%'.$this->searchTerm.'%')
                ->orWhere('content', 'like', '%'.$this->searchTerm.'%');
        }

        // Paginate posts
        $posts = $query->paginate($this->postsPerPage);

        // Get categories and tags for the filters
        $categories = Category::all();
        $tags = Tag::all();

        return view('livewire.frontend.blog', [
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}
