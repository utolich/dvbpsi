<?php
namespace PhpBg\DvbPsi\PesParser\EsParsers;

use PhpBg\DvbPsi\PesParser\Es;
class EsParserUnknown extends EsParserAbstract
{

    public function parse($data)
    {
        return new Es\EsUnknown($this->streamType);
    }
}

