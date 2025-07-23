<?php
namespace PhpBg\DvbPsi\PesParser\EsParsers;

use PhpBg\DvbPsi\PesParser\Es;
use PhpBg\DvbPsi\PesParser\Pes;

class EsParser15 extends EsParserAbstract
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
//            var_dump(sprintf('%b', $header));
            $es = new Es\Es15($this->streamType);
            $es->id = ($header >> 19) & 0x1;
//            var_dump(sprintf('x%X', $es->id));
            $es->layer = 00;
//            var_dump(sprintf('x%X', $es->layer));
            $es->profile = ($header >> 14) & 0x3;
//            var_dump(sprintf('x%X', $es->profile));
            $es->samplingFrequency = ($header >> 10) & 0xF;
//            var_dump(sprintf('x%X', $es->samplingFrequency));
            $es->copyright = ($header >> 5) & 0x1;
//            var_dump(sprintf('x%X', $es->copyright));
            $es->original = ($header >> 4) & 0x1;
//            var_dump(sprintf('x%X', $es->original));
        }
        return $es ?? null;
    }
}

