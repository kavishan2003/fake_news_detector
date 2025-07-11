<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\FakenessCheck;

class FakenessDetail extends Component
{
    public $slug;
    public $article;

    // Runs when the component is mounted
    public function mount($slug)
    {
        $this->slug = $slug;
        $this->article = FakenessCheck::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.fakeness-detail', [
            'article' => $this->article,
        ]);
    }
}
