<?php

namespace App\Service\SOLID;

/**
 *
 */
class SOLIDService implements SOLIDInterface
{

    private $SOLIDPrivateVar;

    public function __construct(){

        $this->SOLIDPrivateVar = 'SOLID test succesfull completed!';

    }

    public function TestFunction($null) {

        return $this->SOLIDPrivateVar;

    }

    public function TestOutputFormat(){

        return 'From SOLIDService: '.$this->SOLIDPrivateVar;

    }

    public function NotExistInInterfaceFunction() {

        return 'Кек';

    }

}
