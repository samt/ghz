<?php
/*
 * ghz.me url shortener
 * when a long url hz.
 *
 * (c) 2014 Sam Thompson <contact@samt.us>
 * License: MIT
 */

namespace Ghz;

/*
 * Base62 is a concept I created a while ago when making this URL shorten
 * originally. One goal I had was to utilize all available space in the final
 * url, which meant doing something a little more elegant than, say asking the
 * database if some psudeo-random string, pointing to the record, existed.
 *
 * I decided instead to use the built-in primary key, auto-incrementing system
 * to power this. I took the concept of Base64 (same charset, minus the "+" and
 * the "/"). It was just a representation of a stored integer, so I wasn't
 * worried about it not fitting nicely within a binary block (like what Base64
 * can do). I simply wanted something alpha-numeric, so 26+26+10 = 62 items in
 * my character set.
 *
 * This class was originally based on a Base convertor found in the OpenFlame
 * project (now defunct). I wrote the original, and it was released under MIT.
 *
 * The resulting class is not magic voodoo, it's science!
 */
class Base62
{
    const CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /*
     * Produce a Base62 representation of a Base10 number.
     * @param string n - The base10 number as a string
     * @return string - Resulting base62 number.
     */
    public static function fromBase10($n = '')
    {
        $charset = str_split(static::CHARSET);
        $b62_output = '';

        do {
            $r = bcmod($n, '62');
            $n = bcdiv($n, '62');

            $b62_output .= $charset[$r];
        }
        while(bccomp($n, '1') != -1);

        return strrev($b62_output);
    }

    /*
     * Produce a Base10 representation of a Base64 number.
     * @param string b62 - The base64 number as a string
     * @return string - Resulting base10 number.
     */
    public static function toBase10($b62 = '')
    {
      	$_base62 = array_flip(str_split(static::CHARSET));
      	$b62_ary = str_split(strrev($b62));

      	$output = '';
      	for($i = 0; sizeof($b62_ary) > $i; $i++) {
      		  $output = bcadd($output, bcmul($_base62[$b62_ary[$i]], bcpow(62, $i)));
      	}

      	return $output;
    }
}
