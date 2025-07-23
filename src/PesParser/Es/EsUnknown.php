<?php
namespace PhpBg\DvbPsi\PesParser\Es;

class EsUnknown extends EsAbstract
{
    protected $streamType;

    public function __toString()
    {
        return sprintf("Unknown: %s", $this->streamType);
    }
}
