<?php

$pdfFile = 'C:/Users/ok/.gemini/antigravity/brain/86354b85-696b-4478-b9e4-224d26716c35/.user_uploaded/media_1790848755946.pdf';
$content = file_get_contents($pdfFile);

// Extract printable text chunks
preg_match_all('/[\x20-\x7E\s]{4,}/', $content, $matches);

foreach ($matches[0] as $text) {
    if (preg_match('/(recharge|operator|hlr|fetch|find|circle|api|url)/i', $text)) {
        echo trim($text)."\n";
    }
}
