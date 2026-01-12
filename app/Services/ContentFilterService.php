<?php

namespace App\Services;

class ContentFilterService
{
    protected $badWords = [
        'kasar',
        'anjing',
        'babi',
        'sialan',
        'bodoh',
        'tolol',
        'bangsat', // Add more as needed
        'judi',
        'slot',
        'gacor', // Gambling terms
        'porn',
        'sex',
        'bokep', // NFSW
    ];

    public function filter($content)
    {
        foreach ($this->badWords as $word) {
            // Case insensitive replacement
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $content = preg_replace($pattern, str_repeat('*', strlen($word)), $content);
        }

        return $content;
    }

    public function containsBadWords($content)
    {
        foreach ($this->badWords as $word) {
            if (stripos($content, $word) !== false) {
                return true;
            }
        }
        return false;
    }
}
