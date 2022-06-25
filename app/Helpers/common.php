<?php

if (!function_exists('readable_random_string')) {
    function readable_random_string($length = 6)
    {
        $string = '';
        $vowels = ['a', 'e', 'i', 'o', 'u'];
        $consonants = [
            'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
            'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z',
        ];

        $max = $length / 2;
        for ($i = 1; $i <= $max; ++$i) {
            $string .= $consonants[rand(0, 19)];
            $string .= $vowels[rand(0, 4)];
        }

        return $string;
    }
}
