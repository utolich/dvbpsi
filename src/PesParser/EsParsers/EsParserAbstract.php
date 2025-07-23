<?php
namespace PhpBg\DvbPsi\PesParser\EsParsers;

use PhpBg\DvbPsi\PesParser\Es;
use PhpBg\DvbPsi\PesParser\Pes;

abstract class EsParserAbstract
{
    protected int $streamType;

    public function __construct($streamType) {
        $this->streamType = $streamType;
    }

    abstract public function parse($data);

}

