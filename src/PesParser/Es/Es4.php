<?php
namespace PhpBg\DvbPsi\PesParser\Es;

class Es4 extends EsAbstract
{
    // ISO/IEC 13818-3 (MPEG-2 halved sample rate audio) in a packetized stream
    protected int $streamType = 3;

    // "11" Layer I
    // "10" Layer II
    // "01" Layer III
    // "00" reserved
    const LAYER = [
        0b11 => 'Layer I',
        0b10 => 'Layer II',
        0b01 => 'Layer III'
    ];

    // bit_rate_index Layer I Layer II Layer III
    // bitrate_index bitrate specified (kbit/s) for Fs = 16, 22,05, 24 kHz
    //        Layer I  Layer II, Layer III
    // '0000' free     free
    // '0001' 32       8
    // '0010' 48       16
    // '0011' 56       24
    // '0100' 64       32
    // '0101' 80       40
    // '0110' 96       48
    // '0111' 112      56
    // '1000' 128      64
    // '1001' 144      80
    // '1010' 160      96
    // '1011' 176      112
    // '1100' 192      128
    // '1101' 224      144
    // '1110' 256      160
    // '1111' forbidden forbidden
    const BIT_RATE = [
        0b0001 => [
            0b11 => '32 kbit/s',
            0b10 => '8 kbit/s',
            0b01 => '8 kbit/s'
        ],
        0b0010 => [
            0b11 => '48 kbit/s',
            0b10 => '16 kbit/s',
            0b01 => '16 kbit/s'
        ],
        0b0011 => [
            0b11 => '56 kbit/s',
            0b10 => '24 kbit/s',
            0b01 => '24 kbit/s'
        ],
        0b0100 => [
            0b11 => '64 kbit/s',
            0b10 => '32 kbit/s',
            0b01 => '32 kbit/s'
        ],
        0b0101 => [
            0b11 => '80 kbit/s',
            0b10 => '40 kbit/s',
            0b01 => '40 kbit/s'
        ],
        0b0110 => [
            0b11 => '96 kbit/s',
            0b10 => '48 kbit/s',
            0b01 => '48 kbit/s'
        ],
        0b0111 => [
            0b11 => '112 kbit/s',
            0b10 => '56 kbit/s',
            0b01 => '56 kbit/s'
        ],
        0b1000 => [
            0b11 => '128 kbit/s',
            0b10 => '64 kbit/s',
            0b01 => '64 kbit/s'
        ],
        0b1001 => [
            0b11 => '144 kbit/s',
            0b10 => '80 kbit/s',
            0b01 => '80 kbit/s'
        ],
        0b1010 => [
            0b11 => '160 kbit/s',
            0b10 => '96 kbit/s',
            0b01 => '96 kbit/s'
        ],
        0b1011 => [
            0b11 => '176 kbit/s',
            0b10 => '112 kbit/s',
            0b01 => '112 kbit/s'
        ],
        0b1100 => [
            0b11 => '192 kbit/s',
            0b10 => '128 kbit/s',
            0b01 => '128 kbit/s'
        ],
        0b1101 => [
            0b11 => '224 kbit/s',
            0b10 => '144 kbit/s',
            0b01 => '144 kbit/s'
        ],
        0b1110 => [
            0b11 => '256 kbit/s',
            0b10 => '160 kbit/s',
            0b01 => '160 kbit/s'
        ],
    ];

    // '00' stereo
    // '01' joint_stereo (intensity_stereo and/or ms_stereo)
    // '10' dual_channel
    // '11' single_chann
    const MODE = [
        0b00 => 'stereo',
        0b01 => 'joint_stereo (intensity_stereo and/or ms_stereo)',
        0b10 => 'dual_channel',
        0b11 => 'mono'
    ];

    // '00' 22.5 kHz
    // '01' 24 kHz
    // '10' 16 kHz
    // '11' reserved
    const SAMPLING_FREQ = [
        0b00 => '22.5kHz',
        0b01 => '24kHz',
        0b10 => '16kHz',
    ];

    // '1' for MPEG audio, '0' is reserved.
    public $id;

    public $layer;

    public $bitRateIndex;

    public $samplingFrequency;

    public $mode;

    // Layer I, II
    // '00' subbands 4-31 in intensity_stereo, bound==4
    // '01' subbands 8-31 in intensity_stereo, bound==8
    // '10' subbands 12-31 in intensity_stereo, bound==12
    // '11' subbands 16-31 in intensity_stereo, bound==16

    // Layer III
    // intensity_stereo ms_stereo
    // '00' off off
    // '01' on off
    // '10' off on
    // '11' on o
    public $modeExtension;

    // 0 no copyright
    // 1 copyright protected
    public $copyright;

    // '0' if the bitstream is a copy, '1' if it is an original
    public $original;

    // '00' no emphasis
    // '01' 50/15 microsec. emphasis
    // '10' reserved
    // '11' CCITT J.17
    public $emphasis;

    // ...
    public function __toString()
    {
        return sprintf("Audio: mp2 (%s) %s, %s, %s",
            self::LAYER[$this->layer],
            self::MODE[$this->mode],
            self::SAMPLING_FREQ[$this->samplingFrequency]??$this->samplingFrequency,
            self::BIT_RATE[$this->bitRateIndex][$this->layer]);
    }

}
