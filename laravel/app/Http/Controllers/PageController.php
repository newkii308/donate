<?php

namespace App\Http\Controllers;

use App\Models\ContentBlock;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $page): View
    {
        abort_unless(array_key_exists($page, ContentBlock::PAGES), 404);

        $blocks = ContentBlock::forPage($page)->visible()->orderBy('sort_order')->get();

        return view('pages.show', [
            'page' => $page,
            'title' => ContentBlock::PAGES[$page],
            'blocks' => $blocks,
        ]);
    }
}
