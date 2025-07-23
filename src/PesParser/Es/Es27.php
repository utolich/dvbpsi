<?php
namespace PhpBg\DvbPsi\PesParser\Es;

class Es27 extends EsAbstract
{
    // ITU-T Rec. H.264 and ISO/IEC 14496-10 (lower bit-rate video) in a packetized stream
    protected int $streamType = 27;

    // 66  Baseline
    // 77  Main
    // 88  Extended
    // 100 Hight
    // 110 Hight 10
    // 122 High 4:2:2
    // 244 High 4:4:4 Predictive
    // 44 CAVLC 4:4:4 Intra
    const PROFILE = [
        66 => 'Baseline',
        77 => 'Main',
        88 => 'Extended',
        100 => 'Hight',
        110 => 'Hight 10',
        122 => 'High 4:2:2',
        244 => 'High 4:4:4 Predictive',
        44 => 'CAVLC 4:4:4 Intra',
    ];

    public $horizontalSize;

    public $verticalSize;

    public $profileIdc;

    public $levelIdc;

    public function __toString()
    {
        return sprintf("Video: h264 (%s), %sx%s, level %s",
            self::PROFILE[$this->profileIdc],
            $this->horizontalSize?:0,
            $this->verticalSize?:0,
            sprintf('%.1f', $this->levelIdc / 10));
    }

}