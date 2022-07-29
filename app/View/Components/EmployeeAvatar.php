<?php

namespace App\View\Components;

use Illuminate\View\Component;

class EmployeeAvatar extends Component
{
    public $photo;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($photo)
    {
        $this->photo = $photo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.employee-avatar');
    }
}
