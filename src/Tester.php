<?php

namespace RedirectTester;
use Exception;

class Tester
{
    private $warnings = [];
    private $domain = '';
    private $skipHeader = false;
    private $testedFile = '';
    private $redirects = [];
    private $appendToURLs = '';

    public function __construct($domain)
    {
        $this->domain = $domain;
    }


    private function extractRedirects($filename, $limit, $start = 1)
    {
        $content = file_get_contents($filename);
        $lines = explode("\n", $content);
        $lineNumber = 1;

        if ($this->skipHeader) {
            array_shift($lines);
            $lineNumber = $start + 1;
        }
        if (!is_array($lines)) {
            throw new Exception("Expected an array for \$lines");
        }

        if (!is_int($start)) {
            throw new Exception("\$start must be integers");
        }
        // Ensure $start is within the array bounds
        $start = max(0, min($start, count($lines) - 1));

        // Ensure $limit is positive
        $limit = max(0, $limit);

        // Slice the array to get the desired portion
        array_slice($lines, $start, $limit);

        foreach ($lines as $line) {
            $redirect = Helpers::parseRedirectLine($line, $this->domain);
            if ($redirect !== null) {
                $this->redirects[] = new Redirect($redirect[0], $redirect[1]);
            } else {
                $this->warnings[] = "Warning: Invalid redirect format on line {$lineNumber} - {$line}";
            }
            $lineNumber++;
        }
        return $this->redirects;
    }

    public function appendToURLs($append)
    {
        $this->appendToURLs = $append;
    }

    public static function testRedirect($source, $destination, &$resultURL)
    {
        $result = Helpers::fetchEffectiveUrl($source);
        $resultURL = $result;
        return $result === $destination;
    }


    public function testFile($filename, $skipHeader = false, $limit = null, $start = 1)
    {
        $this->skipHeader = $skipHeader;
        $this->testedFile = $filename;
        $this->extractRedirects($filename, $limit , $start);
        foreach ($this->redirects as $redirect) {
            if ($this->appendToURLs !== '') {
                $redirect->source .= $this->appendToURLs;
                $redirect->destination .= $this->appendToURLs;
            }
    
            $redirect->works =  self::testRedirect($redirect->source, $redirect->destination, $redirect->destinationResult);
        }
    }
    
    public function reportResults(){
        if (empty($this->redirects)) {
            echo "No redirects to test yet." . PHP_EOL;
            return;
        }
        if (empty($this->testedFile)) {
            echo "No file has been tested yet." . PHP_EOL;
            return;
        }
        echo "Testing Redirects for: $this->domain" . PHP_EOL;
        echo "Testing File: $this->testedFile :" . PHP_EOL . PHP_EOL;
        foreach ($this->redirects as $redirect) {
            echo $redirect->works ? "✅" : "❌";
            echo " $redirect->source redirects to $redirect->destinationResult " . ($redirect->works ? "" : "instead of $redirect->destination") . PHP_EOL;
        }
        if (!empty($this->warnings)) {
            echo PHP_EOL . "Warnings:" . PHP_EOL . implode(PHP_EOL, $this->warnings);
        }
    }


}

