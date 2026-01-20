<?php

namespace App\View\Components;

use Illuminate\View\Component;

class DocumentView extends Component
{
    public ?string $url;
    public ?string $name;

    /**
     * Create a new component instance.
     */
    public function __construct(?string $url = null, ?string $name = null)
    {
        $this->url = $url;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.document-view');
    }
}
