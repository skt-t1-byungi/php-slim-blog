<?php

namespace App\Controller;

class About
{
    const MARKDOWN_PATH = ROOT_DIR . '/about.md';

    function __invoke()
    {
        $content = markdown(file_get_contents(self::MARKDOWN_PATH));
        
        return view('about', ['content' => $content]);
    }
}