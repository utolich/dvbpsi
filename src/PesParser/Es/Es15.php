<?php
namespace PhpBg\DvbPsi\PesParser\Es;

class Es15 extends EsAbstract
{
    // ISO/IEC 13818-7 ADTS AAC (MPEG-2 lower bit-rate audio) in a packetized stream
    protected int $streamType = 15;

    // "11" Layer I
    // "10" Layer II
    // "01" Layer III
    // "00" reserved
    const LAYER = [
        0b11 => 'Layer I',
        0b10 => 'Layer II',
        0b01 => 'Layer III'
    ];

    // index profile
    // 0 Main profile
    // 1 Low Complexity profile (LC)
    // 2 Scalable Sampling Rate profile (SSR)
    // 3 (reserved)
    const PROFILE = [
        0 => 'Main',
        1 => 'LC',
        2 => 'SSR'
    ];

    // sampling_frequency_index sampling frequeny [Hz]
    // 0x0 96000
    // 0x1 88200
    // 0x2 64000
    // 0x3 48000
    // 0x4 44100
    // 0x5 32000
    // 0x6 24000
    // 0x7 22050
    // 0x8 16000
    // 0x9 12000
    // 0xa 11025
    // 0xb 8000
    // 0xc reserved
    // 0xd reserved
    // 0xe reserved
    // 0xf reserved
    const SAMPLING_FREQ = [
        0x0 => '96KHz',
        0x1 => '88.2KHz',
        0x2 => '64KHz',
        0x3 => '48KHz',
        0x4 => '44.1KHz',
        0x5 => '32KHz',
        0x6 => '24KHz',
        0x7 => '22.05KHz',
        0x8 => '16KHz',
        0x9 => '12KHz',
        0xa => '11.025KHz',
        0xb => '8KHz'
    ];

    public $id;

    public $layer;

    public $profile;

    public $samplingFrequency;

    // 0 no copyright
    // 1 copyright protected
    public $copyright;

    // '0' if the bitstream is a copy, '1' if it is an original
    public $original;

    // ...

    public function __toString()
    {
        return sprintf("Audio aac (%s), %s",
            self::PROFILE[$this->profile],
            self::SAMPLING_FREQ[$this->samplingFrequency]??$this->samplingFrequency);
    }

}
