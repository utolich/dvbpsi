<?php
namespace PhpBg\DvbPsi\PesParser;

use PhpBg\DvbPsi\PesParser\EsParsers\EsParserUnknown;

class Parser {
    public function parse($data, $streamType = null)
    {
        if (strlen($data) < 4) {
            throw new \Exception('Wrong packet size');
        }

        $pes = new Pes();
        $pointer = 0;
        $tmp = unpack("N", substr($data, $pointer, 4))[1];
        $pointer += 4;

        if (($tmp >> 8) !== 0x000001) {
            throw new \Exception('Packet start code prefix wrong');
        }

        if (strlen($data) < $pointer + 3) {
            throw new \Exception('Wrong payload size');
        }

        // Marker bit
        $tmp = unpack("C", $data[$pointer + 2])[1];
        if (($tmp >> 6) !== 0x02) {
            throw new \Exception('Marker bit wrong');
        }

        // Audio streams (0xC0-0xDF), Video streams (0xE0-0xEF)
        $pes->streamId = $tmp & 0xff;

        // If the PES packet length is set to zero, the PES packet can be of any length.
        // A value of zero for the PES packet length can be used only when the PES packet payload is a video elementary stream
        $pesPacketLen = unpack("n", substr($data, $pointer, 2))[1];
        $pointer += 2;

        $tmp = unpack("n", substr($data, $pointer, 2))[1];
        $pointer += 2;

        $pes->scrabling = ($tmp >> 12) & 0x03;

        $pes->priority = ($tmp >> 11) & 0x01;

        $pes->alignmentIndicator = ($tmp >> 10) & 0x01;

        $pes->copyright = ($tmp >> 9) & 0x01;

        $pes->originalOrCopy = ($tmp >> 8) & 0x01;

        // 11 = both present, 01 is forbidden, 10 = only PTS, 00 = no PTS or DTS
        $pes->flagsPtsDts = ($tmp >> 6) & 0x03;

        $pes->flagEscr = ($tmp >> 5) & 0x01;

        $pes->flagEsRate = ($tmp >> 4) & 0x01;

        $pes->flagDsmTrickMode = ($tmp >> 3) & 0x01;

        $pes->flagAdditionalCopyInfo = ($tmp >> 2) & 0x01;

        $pes->flagCrc = ($tmp >> 1) & 0x01;

        $pes->flagExtension = $tmp & 0x01;

        // gives the length of the remainder of the PES header in bytes
        $pesHeaderLen = unpack("C", substr($data, $pointer, 1))[1];
        $pointer += 1;

        // Optional fields
        // https://dvd.sourceforge.net/dvdinfo/pes-hdr.html
        $tmp = substr($data, $pointer, $pesHeaderLen);
        $pointer += $pesHeaderLen;

        $pointerOpt = 0;

        if ($pes->flagsPtsDts >> 1) {
            $ptsArray = unpack("C1pts/n2pts", substr($tmp, $pointerOpt, 5));
            $pointerOpt += 5;
            $PTS_32_30 = ($ptsArray['pts'] >> 1) & 0x07;
            $PTS_32_15 = ($ptsArray['pts1'] >> 1);
            $PTS_14_0 = ($ptsArray['pts2'] >> 1);
            $pes->pts = ($PTS_32_30 << 30) + ($PTS_32_15 << 15) + $PTS_14_0;
        }

        if ($pes->flagsPtsDts & 0x01) {
            $dtsArray = unpack("C1dts/n2dts", substr($tmp, $pointerOpt, 5));
            $pointerOpt += 5;
            $DTS_32_30 = ($dtsArray['dts'] >> 1) & 0x07;
            $DTS_32_15 = ($dtsArray['dts1'] >> 1);
            $DTS_14_0 = ($dtsArray['dts2'] >> 1);
            $pes->dts = ($DTS_32_30 << 30) + ($DTS_32_15 << 15) + $DTS_14_0;
        }

        // other flags are not important yet

        //ES
        if (!is_null($streamType)) {
            $esData = substr($data, $pointer);
            $esParserName = "\PhpBg\DvbPsi\PesParser\EsParsers\EsParser{$streamType}";
            if (class_exists($esParserName)) {
                $esParser = new $esParserName($streamType);
            } else {
                $esParser = new EsParserUnknown($streamType);
            }
            $pes->es = $esParser->parse($esData);
        }
        return $pes;
    }
}

