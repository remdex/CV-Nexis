<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Collection;
use App\Models\Candidate;

class LastComments extends Component
{
    public Collection $comments;
    public ?Candidate $candidate;

    /**
     * Create a new component instance.
     */
    public function __construct($comments = null, $candidate = null)
    {
        $this->comments = $comments ?? collect();
        $this->candidate = $candidate;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.last-comments');
    }
}
