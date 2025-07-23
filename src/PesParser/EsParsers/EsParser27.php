<?php
namespace PhpBg\DvbPsi\PesParser\EsParsers;

use PhpBg\DvbPsi\PesParser\Es;
use PhpBg\DvbPsi\PesParser\Golomb;
use PhpBg\DvbPsi\PesParser\Pes;

class EsParser27 extends EsParserAbstract
{
    public function parse($data)
    {
        $esPointer = 0;
        while ($esPointer < strlen($data)) {
            if (false !== $startPointer = strpos($data, pack('H*', '00000001'), $esPointer)) {
                $esPointer = $startPointer + 4;
                $arrNalHeader = unpack("C", substr($data, $esPointer, 1));
                if (!$arrNalHeader) {
                    break;
                }
                $nalHeader = $arrNalHeader[1];
                // zero bit
                if ($nalHeader >> 7) {
                    continue;
                }
                $nalUnitType = $nalHeader & 0x1F;
                // for SPS nal_unit_type = 7
                if (7 === $nalUnitType) {
                    $esPointer++;
                    $stopPointer = strpos($data, pack('H*', '00000001'), $esPointer);
                    $nal = substr($data, $esPointer, $stopPointer - $esPointer);
                    break;
                }
            } else {
                break;
            }
        }

        if (!empty($nal)) {
            $es = new Es\Es27($this->streamType);
            $esPointer = 0;
            $es->profileIdc = unpack("C", $nal[$esPointer])[1];
            $esPointer += 1;
            $constaintFlags = unpack("C", $nal[$esPointer])[1];
            $esPointer += 1;
            $es->levelIdc = unpack("C", $nal[$esPointer])[1];
            $esPointer += 1;
            $tmp = substr($nal, $esPointer);
            list ($offset, $seqParameterSetId) = Golomb::decode($tmp);
            if (in_array($es->profileIdc, [100, 110, 122, 244, 44, 83, 86, 118, 128, 138])) {
                list ($offset, $chromaFormatIdc) = Golomb::decode($tmp, $offset);
                switch ($chromaFormatIdc) {
                    case 0;
                        $subWidthC = 0;
                        $subHeightC = 0;
                        break;
                    case 1;
                        $subWidthC = 2;
                        $subHeightC = 2;
                        break;
                    case 2;
                        $subWidthC = 2;
                        $subHeightC = 1;
                        break;
                    case 3;
                        $subWidthC = 1;
                        $subHeightC = 1;
                        break;
                }
                $chromaArrayType = $chromaFormatIdc;
                if ($chromaFormatIdc == 3) {
                    if ($separateColourPlaneFlag = Golomb::read_bits($tmp, $offset, 1)) {
                        $subWidthC = 0;
                        $subHeightC = 0;
                    } else {
                        $chromaArrayType = 0;
                    }
                    $offset++;
                }
                list ($offset, $bitDepthLumaMinus8) = Golomb::decode($tmp, $offset);
                list ($offset, $bitDepthChromaMinus8) = Golomb::decode($tmp, $offset);
                $qpprimeYZeroTransformBypassFlag = Golomb::read_bits($tmp, $offset, 1);
                $offset++;
                $seqScalingMatrixPresentFlag = Golomb::read_bits($tmp, $offset, 1);
                $offset++;
                if ($seqScalingMatrixPresentFlag) {
                    for ($i = 0; $i < (($chromaFormatIdc != 3) ? 8 : 12); $i++) {
                        $seqScalingListPresentFlag[$i] = Golomb::read_bits($tmp, $offset, 1);
                        $offset++;
                        if ($seqScalingListPresentFlag[$i]) {
                            throw new \Exception('Scaling list not implemented');
                        }
                    }
                }
                list ($offset, $log2MaxFrameNumMinus4) = Golomb::decode($tmp, $offset);
                list ($offset, $picOrderCntType) = Golomb::decode($tmp, $offset);
                if ($picOrderCntType == 0) {
                    list ($offset, $log2MaxPicOrderCntLsbMinus4) = Golomb::decode($tmp, $offset);
                } else if ($picOrderCntType == 1) {
                    $deltaPicOrderAlwaysZeroFlag = Golomb::read_bits($tmp, $offset, 1);
                    $offset++;
                    list ($offset, $offsetForNonRefPic) = Golomb::decode($tmp, $offset, true);
                    list ($offset, $offsetForTopToBottomField) = Golomb::decode($tmp, $offset, true);
                    list ($offset, $numRefFramesInPicOrderCntCycle) = Golomb::decode($tmp, $offset);
                    for ($i = 0; $i < $numRefFramesInPicOrderCntCycle; $i++) {
                        list ($offset, $offsetForRefFrame[$i]) = Golomb::decode($tmp, $offset, true);
                    }
                }
                list ($offset, $maxNumRefFrames) = Golomb::decode($tmp, $offset);
                $gapsInFrameNumValueAllowedFlag = Golomb::read_bits($tmp, $offset, 1);
                $offset++;
                list ($offset, $picWidthInMbsMinus1) = Golomb::decode($tmp, $offset);
                list ($offset, $picHeightInMapUnitsMinus1) = Golomb::decode($tmp, $offset);
                $frameMbsOnlyFlag = Golomb::read_bits($tmp, $offset, 1);
                $offset++;
                if (!$frameMbsOnlyFlag) {
                    $mbAdaptiveFrameFieldFlag = Golomb::read_bits($tmp, $offset, 1);
                    $offset++;
                }
                $direct8x8InferenceFlag = Golomb::read_bits($tmp, $offset, 1);
                $offset++;
                $frameCroppingFlag = Golomb::read_bits($tmp, $offset, 1);
                $offset++;
                if ($frameCroppingFlag) {
                    list ($offset, $frameCropLeftOffset) = Golomb::decode($tmp, $offset);
                    list ($offset, $frameCropRightOffset) = Golomb::decode($tmp, $offset);
                    list ($offset, $frameCropTopOffset) = Golomb::decode($tmp, $offset);
                    list ($offset, $frameCropBottomOffset) = Golomb::decode($tmp, $offset);
                }
                if (empty($chromaArrayType)) {
                    $cropUnitX = 1;
                    $cropUnitY = 2 - $frameMbsOnlyFlag;
                } else {
                    $cropUnitX = $subWidthC ?? 0;
                    $cropUnitY = ($subHeightC ?? 0) * (2 - $frameMbsOnlyFlag);
                }
                $es->horizontalSize = ($picWidthInMbsMinus1 + 1) * 16 - $cropUnitX * ($frameCropRightOffset ?? 0);
                $es->verticalSize = (2 - $frameMbsOnlyFlag) * ($picHeightInMapUnitsMinus1 + 1) * 16 - $cropUnitY * ($frameCropBottomOffset ?? 0);
            }
        }
        return $es ?? null;
    }
}

