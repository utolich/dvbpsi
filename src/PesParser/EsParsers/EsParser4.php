<?php
namespace PhpBg\DvbPsi\PesParser\EsParsers;

use PhpBg\DvbPsi\PesParser\Es;
use PhpBg\DvbPsi\PesParser\Pes;

class EsParser4 extends EsParserAbstract
{
    public function parse($data)
    {
        $esPointer = 0;
        while ($esPointer < strlen($data)) {
            if (false !== $esPointer = strpos($data, pack('H*', 'FF'), $esPointer)) {
                $tmp = unpack("N", substr($data, $esPointer, 4))[1];
                if (($tmp >> 20) === 0xFFF) {
                    $header = $tmp;
                    break;
                }
                $esPointer += 2;
            } else {
                break;
            }
        }
        if (!empty($header)) {
            $es = new Es\Es4($this->streamType);
            $es->id = ($header >> 19) & 0x1;
            $es->layer = ($header >> 17) & 0x3;
            $es->bitRateIndex = ($header >> 12) & 0xF;
            $es->samplingFrequency = ($header >> 10) & 0x3;
            $es->mode = ($header >> 6) & 0x3;
            $es->modeExtension = ($header >> 4) & 0x3;
            $es->copyright = ($header >> 3) & 0x1;
            $es->original = ($header >> 2) & 0x1;
            $es->emphasis = $header & 0x3;
        }
        return $es ?? null;
    }
}

