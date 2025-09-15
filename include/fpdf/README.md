# FPDF for PHP 8.4

This is a modernized version of FPDF adapted for PHP 8.4 compatibility. The original FPDF library has been updated with:

## Changes for PHP 8.4

1. Added strict type declarations
2. Added property type hints
3. Added method return type hints
4. Added parameter type hints
5. Updated variable declarations to use proper visibility
6. Added modern PHP 8.4 features where appropriate
7. Added proper error handling
8. Added autoloading support

## Usage

```php
<?php
declare(strict_types=1);

require_once 'fpdf/autoload.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Hello World!');
$pdf->Output();
```

## Property Types

All properties now have proper type declarations:

- `int` for page numbers, object numbers, etc.
- `float` for dimensions, margins, positions
- `string` for text content, colors, styles
- `array` for collections (fonts, images, etc.)
- `bool` for flags and states

## Error Handling

The library now uses proper error handling with type checking and throws exceptions when appropriate.

## Original FPDF License

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software to use, copy, modify, distribute, sublicense, and/or sell
copies of the software, and to permit persons to whom the software is furnished
to do so.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED.