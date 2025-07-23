<?php

/**
 * MIT License
 *
 * Copyright (c) 2018 Samuel CHEMLA
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace PhpBg\DvbPsi\Descriptors;

use PhpBg\DvbPsi\Descriptors\Values\ServiceType;

/**
 * Class ServiceList
 * @see Final draft ETSI EN 300 468 V1.15.1 (2016-03), 6.2.33 Service descriptor
 */
class SatelliteDeliverySystem
{
    // The frequency is coded in GHz, where the decimal point occurs after the third character.
    // EXAMPLE: A frequency value of 0x01175725 indicates a frequency of 11,75725 GHz
    public $frequency;

    // The orbital_position values specifying 4 characters of the orbital
    // position in degrees where the decimal point occurs after the third character (e.g. 019,2°).
    public $orbitalPosition;

    // 0b0 western position
    // 0b1 eastern position
    public $westEastFlag;
    public $polarization;

    // 0b0 DVB-S
    // 0b1 DVB-S2
    public $modulationSystem;

    // 0b00 α = 0,35
    // 0b01 α = 0,25
    // 0b10 α = 0,20
    // 0b11 reserved for future use
    public $rollOff;

    // 0b00 auto
    // 0b01 Quaternary Phase Shift Keying (QPSK)
    // 0b10 8-ary Phase Shift Keying (8PSK)
    // 0b11 16QAM (n/a for DVB-S2)
    public $modulationType;

    // The decimal point occurs after the third character.
    // EXAMPLE: A symbol_rate value of 0x0274500 indicates a symbol rate of 27,4500 Msymbol/s
    public $symbolRate;

    // 0b0000 not defined
    // 0b0001 1/2 convolutional code rate
    // 0b0010 2/3 convolutional code rate
    // 0b0011 3/4 convolutional code rate
    // 0b0100 5/6 convolutional code rate
    // 0b0101 7/8 convolutional code rate
    // 0b0110 8/9 convolutional code rate
    // 0b0111 3/5 convolutional code rate
    // 0b1000 4/5 convolutional code rate
    // 0b1001 9/10 convolutional code rate
    // 0b1010 to 0b1110 reserved for future use
    // 0b1111 no convolutional coding
    // NOTE: Not all convolutional code rates apply for all modulation schemes.
    public $FEC_inner;

    /**
     * ServiceDescriptor constructor.
     * @param $data
     * @throws \PhpBg\DvbPsi\Exception
     */
    public function __construct($data)
    {
        $pointer = 0;
        $this->frequency = unpack('N', substr($data, $pointer, 4))[1];
        $pointer += 4;

        $this->orbitalPosition = unpack('n', substr($data, $pointer, 2))[1];
        $pointer += 2;

        $tmp = unpack('C', substr($data, $pointer, 1))[1];
        $pointer += 1;

        $this->westEastFlag = $tmp >> 7;
        $this->polarization = ($tmp >> 5) & 0x3;
        $reserved = ($tmp >> 3) & 0x3;
        if ($this->modulationSystem = ($tmp >> 4) & 0x1) {
            $this->rollOff = $reserved;
        }
        $this->modulationType = $tmp & 0x3;

        $tmp = unpack('N', substr($data, $pointer, 4))[1];
        $this->symbolRate = $tmp >> 4;
        $this->FEC_inner = $tmp & 0xF;
    }

    public function __toString()
    {
        $msg = sprintf("Frequency: %s\n", $this->frequency);
        $msg .= "Orbital position: $this->orbitalPosition\n";
        $msg .= "Polarization: $this->polarization\n";

        return $msg;
    }
}