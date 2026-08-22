<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public bool $reverse;

    public function __construct(bool $reverse = false)
    {
        $this->reverse = $reverse || request()->routeIs('login');
    }

    public function render(): View
    {
        return view('layouts.guest');
    }
}
