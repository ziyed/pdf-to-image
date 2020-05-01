<?php

namespace App\Libraries;

use Imagick;
use Exception;
use Org_Heigl\Ghostscript\Ghostscript;

class InvalidFormat extends Exception {}
class PdfDoesNotExist extends Exception {}
class PageDoesNotExist extends Exception {}

class PdfToImage
{   
    protected $ghostscript;   
    protected $outputFormat = '';
    protected $page = 1;
    protected $validOutputFormats = ['jpg', 'jpeg', 'png'];
    public $imagick;
    protected $numberOfPages;

    public function __construct(string $pdfFile)
    {
        if (! filter_var($pdfFile, FILTER_VALIDATE_URL) && ! file_exists($pdfFile)) {
            throw new PdfDoesNotExist();
        }

        $this->ghostscript = (new Ghostscript())
            ->setInputFile($pdfFile)
            ->setResolution(144);
        
        $this->imagick = new Imagick();

        $this->imagick->pingImage($pdfFile);

        $this->numberOfPages = $this->imagick->getNumberImages();   

    }

    public function setResolution(int $resolution): self
    {
        $this->ghostscript->setResolution($resolution);

        return $this;
    }

    public function setOutputFormat(string $outputFormat): self
    {
        if (! $this->isValidOutputFormat($outputFormat)) {
            throw new InvalidFormat("Format {$outputFormat} is not supported");
        }

        $this->outputFormat = $outputFormat;

        return $this;
    }

    public function setPage(int $pageNumber): self
    {
        $this->ghostscript->setPages($pageNumber);

        if ($pageNumber > $this->getNumberOfPages()) {
            throw new PageDoesNotExist("Page {$pageNumber} does not exist");
        }

        return $this;
    }

    public function getNumberOfPages(): int
    {
        return $this->numberOfPages;
    }

    public function getGhostscript(): Ghostscript
    {
        return $this->ghostscript;
    }

    public function saveImage($pathToImage)
    {
        if ($this->outputFormat === '') {
            $this->outputFormat = $this->determineOutputFormat($pathToImage);
        }

        $this->ghostscript->setDevice($this->outputFormat);

        $this->ghostscript->setOutputFile($pathToImage);

        $this->ghostscript->render();
    }

    protected function isValidOutputFormat(string $outputFormat): bool
    {
        return in_array($outputFormat, $this->validOutputFormats);
    }

    protected function determineOutputFormat(string $pathToImage): string
    {
        $outputFormat = pathinfo($pathToImage, PATHINFO_EXTENSION);

        $outputFormat = strtolower($outputFormat);

        if (! $this->isValidOutputFormat($outputFormat)) {
            $outputFormat = 'jpeg';
        }

        if ($outputFormat == 'jpg') {
            $outputFormat = 'jpeg';
        }

        return $outputFormat;
    }
}
