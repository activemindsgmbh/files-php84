<?php
declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    // Only handle FPDF classes
    if ($class === 'FPDF' || str_starts_with($class, 'FPDF_')) {
        $file = __DIR__ . '/' . strtolower($class) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});