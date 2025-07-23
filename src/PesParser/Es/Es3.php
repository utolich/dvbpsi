<?php
namespace PhpBg\DvbPsi\PesParser\Es;

class Es3 extends EsAbstract
{
    // ISO/IEC 11172-3 (MPEG-1 audio) in a packetized stream
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
    // '0000' free format free format free format
    // '0001' 32 kbit/s 32 kbit/s 32 kbit/s
    // '0010' 64 kbit/s 48 kbit/s 40 kbit/s
    // '0011' 96 kbit/s 56 kbit/s 48 kbit/s
    // '0100' 128 kbit/s 64 kbit/s 56 kbit/s
    // '0101' 160 kbit/s 80 kbit/s 64 kbit/s
    // '0110' 192 kbit/s 96 kbit/s 80 kbit/s
    // '0111' 224 kbit/s 112 kbit/s 96 kbit/s
    // '1000' 256 kbit/s 128 kbit/s 112 kbit/s
    // '1001' 288 kbit/s 160 kbit/s 128 kbit/s
    // '1010' 320 kbit/s 192 kbit/s 160 kbit/s
    // '1011' 352 kbit/s 224 kbit/s 192 kbit/s
    // '1100' 384 kbit/s 256 kbit/s 224 kbit/s
    // '1101' 416 kbit/s 320 kbit/s 256 kbit/s
    // '1110' 448 kbit/s 384 kbit/s 320 kbit/s
    const BIT_RATE = [
        0b0001 => [
            0b11 => '32 kbit/s',
            0b10 => '32 kbit/s',
            0b01 => '32 kbit/s'
        ],
        0b0010 => [
             0b11 => '64 kbit/s',
             0b10 => '48 kbit/s',
             0b01 => '40 kbit/s'
        ],
        0b0011 => [
            0b11 => '96 kbit/s',
            0b10 => '56 kbit/s',
            0b01 => '48 kbit/s'
        ],
        0b0100 => [
            0b11 => '128 kbit/s',
            0b10 => '64 kbit/s',
            0b01 => '56 kbit/s'
        ],
        0b0101 => [
            0b11 => '160 kbit/s',
            0b10 => '80 kbit/s',
            0b01 => '64 kbit/s'
        ],
        0b0110 => [
            0b11 => '192 kbit/s',
            0b10 => '96 kbit/s',
            0b01 => '80 kbit/s'
        ],
        0b0111 => [
            0b11 => '224 kbit/s',
            0b10 => '112 kbit/s',
            0b01 => '96 kbit/s'
        ],
        0b1000 => [
            0b11 => '256 kbit/s',
            0b10 => '128 kbit/s',
            0b01 => '112 kbit/s'
        ],
        0b1001 => [
            0b11 => '288 kbit/s',
            0b10 => '160 kbit/s',
            0b01 => '128 kbit/s'
        ],
        0b1010 => [
            0b11 => '320 kbit/s',
            0b10 => '192 kbit/s',
            0b01 => '160 kbit/s'
        ],
        0b1011 => [
            0b11 => '352 kbit/s',
            0b10 => '224 kbit/s',
            0b01 => '192 kbit/s'
        ],
        0b1100 => [
            0b11 => '384 kbit/s',
            0b10 => '256 kbit/s',
            0b01 => '224 kbit/s'
        ],
        0b1101 => [
            0b11 => '416 kbit/s',
            0b10 => '320 kbit/s',
            0b01 => '256 kbit/s'
        ],
        0b1110 => [
            0b11 => '448 kbit/s',
            0b10 => '384 kbit/s',
            0b01 => '320 kbit/s'
        ],
    ];

    // '00' 44.1 kHz
    // '01' 48 kHz
    // '10' 32 kHz
    // '11' reserved
    const SAMPLING_FREQ = [
        0b00 => '44.1kHz',
        0b01 => '48kHz',
        0b10 => '32kHz',
    ];

    // '00' stereo
    // '01' joint_stereo (intensity_stereo and/or ms_stereo)
    // '10' dual_channel
    // '11' single_chann
    const MODE = [
        0b00 => 'stereo',
        0b01 => 'joint_stereo (intensity_stereo and/or ms_stereo)',
        0b10 => 'dual_channel',
        0b11 => 'single_channel'
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
            self::SAMPLING_FREQ[$this->samplingFrequency],
            self::BIT_RATE[$this->bitRateIndex][$this->layer]);
    }

}
