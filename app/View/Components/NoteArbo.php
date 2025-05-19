<?php

namespace App\View\Components;

use Closure;
use App\Models\Note;
use App\Models\Folder;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;

class NoteArbo extends Component
{
    public $folders;

    /**
     * @var Note[] An array of Folder objects
     */
    public $notes;

    public function __construct($folders, $notes)
    {
        $this->folders = $folders;
        $this->notes = $notes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.note-arbo');
    }
}
