<?php

$paths = [
    __DIR__.'/../resources/views',
    __DIR__.'/../app',
    __DIR__.'/../routes',
    __DIR__.'/../bootstrap',
    __DIR__.'/../config',
];

$patterns = [
    'เน€',
    'เธ',
    'โ',
    'ย',
    'ย',
];

$extensions = ['php', 'blade.php', 'js', 'css', 'json', 'md'];
$errors = [];

foreach ($paths as $path) {
    if (! is_dir($path)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));

    foreach ($iterator as $file) {
        if (! $file->isFile()) {
            continue;
        }

        $name = $file->getFilename();
        $matchesExtension = false;

        foreach ($extensions as $extension) {
            if (str_ends_with($name, $extension)) {
                $matchesExtension = true;
                break;
            }
        }

        if (! $matchesExtension) {
            continue;
        }

        $content = file_get_contents($file->getPathname());

        foreach ($patterns as $pattern) {
            if (str_contains($content, $pattern)) {
                $errors[] = $file->getPathname();
                break;
            }
        }
    }
}

if ($errors !== []) {
    fwrite(STDERR, "Potential mojibake detected in:\n");
    foreach ($errors as $file) {
        fwrite(STDERR, ' - '.$file."\n");
    }
    exit(1);
}

echo "No mojibake detected in scanned files.\n";
