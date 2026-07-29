<?php

namespace App\View\Components;

use App\Models\Lesson;
use Illuminate\View\Component;
use Illuminate\View\View;

class VideoPlayer extends Component
{
    public function __construct(public Lesson $lesson)
    {
    }

    public function render(): View
    {
        return view('components.video-player');
    }
}
