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
class CableDeliverySystem
{
    // The frequency is a 32-bit field giving the 4-bit BCD values specifying 8 characters of the frequency value.
    // For the cable_delivery_system_descriptor, the frequency is coded in MHz, where the decimal occurs after the fourth character.
    // EXAMPLE: A frequency value of 0x0312 0000 indicates a frequency of 312,0000 MHz.
    public $frequency;

    // 0b0000 not defined
    // 0b0001 no outer FEC coding
    // 0b0010 (204;188) Reed-Solomon code (RS)
    // 0b0011 to 0b1111 reserved for future use
    public $FEC_outer;

    // 0x00 not defined
    // 0x01 16-ary Quadrature Amplitude Modulation (16QAM)
    // 0x02 32-ary Quadrature Amplitude Modulation (32QAM)
    // 0x03 64-ary Quadrature Amplitude Modulation (64QAM)
    // 0x04 128-ary Quadrature Amplitude Modulation (128QAM)
    // 0x05 256-ary Quadrature Amplitude Modulation (256QAM)
    // 0x06 to 0xFF reserved for future use
    public $modulation;

    // This is a 28-bit field giving the 4-bit BCD values specifying 7 characters of the symbol rate in Msymbol/s where the decimal point occurs after the third character.
    // EXAMPLE: A symbol_rate value of 0x0274500 indicates a symbol rate of 27.4500 Msymbol/s
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

        $tmp = unpack('n', substr($data, $pointer, 2))[1];
        $pointer += 2;

        $this->FEC_outer = $tmp & 0xF;

        $this->modulation = unpack('C', substr($data, $pointer, 1))[1];
        $pointer += 1;

        $tmp = unpack('N', substr($data, $pointer, 4))[1];
        $this->symbolRate = $tmp >> 4;
        $this->FEC_inner = $tmp & 0xF;
    }

    public function __toString()
    {
        $msg = sprintf("Frequency: %s\n", $this->frequency);

        return $msg;
    }
}