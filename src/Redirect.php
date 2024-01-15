<?php

namespace RedirectTester;

class Redirect {

    public $source = '';
    public $destination = '';
    public $works =  null;
    public $destinationResult = '';

    public function __construct($source, $destination){
        $this->source = $source;
        $this->destination = $destination;
    }
}
