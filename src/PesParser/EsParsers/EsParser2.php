<?php
namespace PhpBg\DvbPsi\PesParser\EsParsers;

use PhpBg\DvbPsi\PesParser\Es;
class EsParser2 extends EsParserAbstract
{
    public function parse($data)
    {
        $esPointer = 0;
        while ($esPointer < strlen($data)) {
            if (false !== $esPointer = strpos($data, pack('H*', '000001B3'), $esPointer)) {
                $data = substr($data, $esPointer);
                break;
            } else {
                break;
            }
        }

        $code = unpack("N", substr($data, $esPointer, 4))[1];
        $esPointer += 4;
        if ($code === 0x000001B3) {
            $es = new Es\Es2($this->streamType);
            $tmp = unpack("N", substr($data, $esPointer, 4))[1];
            $esPointer += 4;
            $es->horizontalSize = ($tmp >> 20);
            $es->verticalSize = ($tmp >> 8) & 0xFFF;
            $es->aspectRatio = ($tmp >> 4) & 0xF;
            $es->frameRate = $tmp & 0xF;
            $tmp = unpack("N", substr($data, $esPointer, 4))[1];
            $es->bitrate = $tmp >> 14;
        }
        return $es ?? null;
    }
}

