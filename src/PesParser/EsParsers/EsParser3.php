<?php
namespace PhpBg\DvbPsi\PesParser\EsParsers;

use PhpBg\DvbPsi\PesParser\Es;

class EsParser3 extends EsParserAbstract
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
//            var_dump(sprintf('%b', ($header>> 10) & 0x11));
            $es = new Es\Es3($this->streamType);
            $es->id = ($header >> 19) & 0x1;
//            var_dump(sprintf('x%X', $es->id));
            $es->layer = ($header >> 17) & 0x3;
//            var_dump(sprintf('x%X', $es->layer));
            $es->bitRateIndex = ($header >> 12) & 0xF;
//            var_dump(sprintf('x%X', $es->bitRateIndex));
            $es->samplingFrequency = ($header >> 10) & 0x3;
//            var_dump(sprintf('x%X', $es->samplingFrequency));
            $es->mode = ($header >> 6) & 0x3;
//            var_dump(sprintf('x%X', $es->mode));
            $es->modeExtension = ($header >> 4) & 0x3;
//            var_dump(sprintf('x%X', $es->modeExtension));
            $es->copyright = ($header >> 3) & 0x1;
//            var_dump(sprintf('x%X', $es->copyright));
            $es->original = ($header >> 2) & 0x1;
//            var_dump(sprintf('x%X', $es->original));
            $es->emphasis = $header & 0x3;
//            var_dump(sprintf('x%X', $es->emphasis));
        }
        return $es ?? null;
    }
}

