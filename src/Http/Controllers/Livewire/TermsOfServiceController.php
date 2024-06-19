<?php

namespace Ibnisnar\Authentication\Http\Controllers\Livewire;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Ibnisnar\Authentication\Authentication;

class TermsOfServiceController extends Controller
{
    /**
     * Show the terms of service for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $termsFile = Authentication::localizedMarkdownPath('terms.md');

        return view('terms', [
            'terms' => Str::markdown(file_get_contents($termsFile)),
        ]);
    }
}
