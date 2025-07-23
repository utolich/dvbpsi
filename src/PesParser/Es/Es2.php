<?php
namespace PhpBg\DvbPsi\PesParser\Es;

class Es2 extends EsAbstract
{
    // ITU-T Rec. H.262 and ISO/IEC 13818-2 (MPEG-2 higher rate interlaced video) in a packetized stream
    protected int $streamType = 2;

    // 0010 4:3
    // 0011 16:9
    // 0100 2.21:1
    const ASPECT_RATIO = [
        0b0010 => '4:3',
        0b0011 => '16:9',
        0b0100 => '2.21:1'
    ];

    const FRAME_RATE = [
        0b0011 => '25Hz',
        0b0110 => '50Hz',
        0b0001 => '24000/1001Hz',
        0b0010 => '24Hz',
        0b0100 => '30000/1001Hz',
        0b0101 => '30Hz',
        0b0111 => '60000/1001Hz',
        0b1000 => '60Hz'
    ];

    // mpeg-2
    public $horizontalSize;

    public $verticalSize;

    public $aspectRatio;

    public $frameRate;

    // Actual bit rate = bit rate * 400, rounded upwards.
    // Use 0x3FFFF for variable bit rate.
    public $bitrate;

    public function __toString()
    {
        $bitrate = $this->bitrate === 0x3FFFF ? 'VBR' : ceil($this->bitrate * 400) / 1000 . ' kbit/s' ;
        return sprintf("Video: mpeg2video %sx%s, %s, %s, %s",
            $this->horizontalSize,
            $this->verticalSize,
            self::ASPECT_RATIO[$this->aspectRatio]??$this->aspectRatio,
            self::FRAME_RATE[$this->frameRate]??$this->frameRate,
            $bitrate);
    }
}
