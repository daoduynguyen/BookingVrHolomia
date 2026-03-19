<?php

$dir = new RecursiveDirectoryIterator('resources/views/admin');
$iterator = new RecursiveIteratorIterator($dir);

// Order is important!
$replacements = [
    // Backgrounds
    'bg-dark' => 'bg-white',
    // bg-black used in nested cards, switch to bg-light
    'bg-black' => 'bg-light',
    
    // Text colors
    'text-white-50' => 'text-muted', // First to avoid replacing 'text-white' early
    'text-while-50' => 'text-muted',
    'text-white' => 'text-dark',
    'text-info' => 'text-primary',
    'text-warning' => 'text-primary',
    
    // Borders
    'border-secondary' => 'border-light',
    'border-info' => 'border-primary',
    'border-warning' => 'border-primary',
    
    // Buttons
    'btn-info' => 'btn-primary',
    'btn-outline-info' => 'btn-outline-primary',
    'btn-warning' => 'btn-primary',
    'btn-outline-warning' => 'btn-outline-primary',
    
    // Badges / Alerts / Background utilities
    'bg-info' => 'bg-primary',
    'bg-warning' => 'bg-primary',
    
    // Specific Hex Colors
    '#1a1d20' => '#ffffff',
    '#212529' => '#f8f9fa',
    '#0dcaf0' => '#0d6efd',
    '#ffc107' => '#0d6efd',
];

$count = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $path = $file->getPathname();
        $original = file_get_contents($path);
        $content = $original;
        
        // table-dark removal
        $content = str_replace('table-dark', '', $content);
        
        foreach ($replacements as $search => $replace) {
            $content = str_replace($search, $replace, $content);
        }
        
        // special exception for opacity classes which might look bad on light themes
        $content = str_replace('bg-opacity-25', 'bg-opacity-10', $content);
        
        if ($original !== $content) {
            file_put_contents($path, $content);
            echo "Updated: $path\n";
            $count++;
        }
    }
}
echo "Total files updated: $count\n";
