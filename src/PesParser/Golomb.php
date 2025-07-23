<?php
namespace PhpBg\DvbPsi\PesParser;

class Golomb {

    public static function decode($str, $offset = 0, $signed = false) {
        $s = $str;
        $zeroBitLength = -1;
        for ($b = 0; !$b; $zeroBitLength++) {
            $b = self::read_bits($s, $offset + $zeroBitLength + 1, 1);
        }
        $num = pow(2, $zeroBitLength) - 1 + self::read_bits($s, $offset + $zeroBitLength + 1, $zeroBitLength);
        if ($signed) {
            $num = pow(-1, $num + 1) * ceil($num / 2);
        }
        return [
            0 => $offset + $zeroBitLength * 2 + 1,
            1 => $num,
        ];
    }

    public static function read_bits($s, $offset, $n) {
        $bytes = ceil(($offset + $n) / 8);
        $shift_r_count = $bytes * 8 - ($offset + $n);
        if ($r = unpack("C{$bytes}", substr($s, 0, $bytes))) {
            $r = array_reverse($r);
            $result = 0;
            foreach ($r as $k => $v) {
                $result += $v << ($k * 8);
            }
            $mask = bindec(str_repeat('1', $n));
            $result = ($result >> $shift_r_count) & $mask;
        } else {
            throw new \Exception();
        }
        return $result;
    }
}