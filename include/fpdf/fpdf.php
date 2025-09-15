<?php
declare(strict_types=1);

/*******************************************************************************
* FPDF                                                                         *
*                                                                              *
* Version: 2.7-PHP8.4                                                          *
* Date:    2025-09-15                                                          *
* Author:  Olivier PLATHEY                                                     *
* PHP 8.4 Adaptation: ActiveMinds GmbH                                         *
*******************************************************************************/

define('FPDF_VERSION','2.7-PHP8.4');

class FPDF
{
    protected int $page;               // current page number
    protected int $n;                  // current object number
    protected array $offsets;          // array of object offsets
    protected string $buffer;          // buffer holding in-memory PDF
    protected array $pages;            // array containing pages
    protected int $state;              // current document state
    protected bool $compress;          // compression flag
    protected float $k;                // scale factor (number of points in user unit)
    protected string $DefOrientation;  // default orientation
    protected string $CurOrientation;  // current orientation
    protected array $StdPageSizes;     // standard page sizes
    protected array $DefPageSize;      // default page size
    protected array $CurPageSize;      // current page size
    protected array $PageSizes;        // used for pages with non default sizes or orientations
    protected float $wPt;              // width of current page in points
    protected float $hPt;              // height of current page in points
    protected float $w;                // width of current page in user unit
    protected float $h;                // height of current page in user unit
    protected float $lMargin;          // left margin
    protected float $tMargin;          // top margin
    protected float $rMargin;          // right margin
    protected float $bMargin;          // bottom margin
    protected float $cMargin;          // cell margin
    protected float $x;                // current x position
    protected float $y;                // current y position
    protected float $lasth;            // height of last printed cell
    protected int $LineWidth;          // line width in user unit
    protected array $fonts;            // array of used fonts
    protected array $FontFiles;        // array of font files
    protected array $diffs;            // array of encoding differences
    protected array $images;           // array of used images
    protected array $links;            // array of internal links
    protected string $FontFamily;      // current font family
    protected string $FontStyle;       // current font style
    protected int $FontSizePt;         // current font size in points
    protected float $FontSize;         // current font size in user unit
    protected array $underline;        // underlining flag
    protected string $DrawColor;       // commands for drawing color
    protected string $FillColor;       // commands for filling color
    protected string $TextColor;       // commands for text color
    protected bool $ColorFlag;         // indicates whether fill and text colors are different
    protected int|float $ws;           // word spacing
    protected bool $AutoPageBreak;     // automatic page breaking
    protected float $PageBreakTrigger; // threshold used to trigger page breaks
    protected bool $InHeader;          // flag set when processing header
    protected bool $InFooter;          // flag set when processing footer
    protected array $ZoomMode;         // zoom display mode
    protected string|array $LayoutMode;// layout display mode
    protected string $title;           // title
    protected string $subject;         // subject
    protected string $author;          // author
    protected string $keywords;        // keywords
    protected string $creator;         // creator
    protected int $AliasNbPages;       // alias for total number of pages
    protected string $PDFVersion;      // PDF version number

    /**
     * Constructor
     * @param string $orientation Page orientation (P=portrait, L=landscape)
     * @param string $unit User unit (pt=point, mm=millimeter, cm=centimeter, in=inch)
     * @param string|array $size Page size (A3, A4, A5, Letter, Legal or array(width,height))
     */
    public function __construct(string $orientation = 'P', string $unit = 'mm', string|array $size = 'A4')
    {
        // Initialize properties with type safety
        $this->page = 0;
        $this->n = 2;
        $this->buffer = '';
        $this->pages = [];
        $this->PageSizes = [];
        $this->state = 0;
        $this->fonts = [];
        $this->FontFiles = [];
        $this->images = [];
        $this->links = [];
        $this->InHeader = false;
        $this->InFooter = false;
        $this->lasth = 0;
        $this->FontFamily = '';
        $this->FontStyle = '';
        $this->FontSizePt = 12;
        $this->underline = [];
        $this->DrawColor = '0 G';
        $this->FillColor = '0 g';
        $this->TextColor = '0 g';
        $this->ColorFlag = false;
        $this->ws = 0;
        $this->offsets = [];
        $this->diffs = [];
        
        // More initialization code would follow...
        // The rest of the constructor implementation
    }

    // ... Rest of the class methods would follow with proper type hints and PHP 8.4 features
}