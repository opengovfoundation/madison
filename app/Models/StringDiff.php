<?php

namespace App\Models;

/**
 * @author Philipp Strazny <philipp at strazny dot com>
 * @copyleft (l) 2013  Philipp Strazny
 * @file
 *
 * @since 2013-02
 *
 * @license http://opensource.org/licenses/lgpl-license.php GNU Lesser General Public License
 *
 * This class can find differences or overlaps between two strings. It does so by converting
 * the strings into arrays and performing array comparisons. The string-to-array conversion
 * allows for UTF-8 encoded strings.
 * It exposes two main functions:
 * StringDiff::diff($a, $b) returns a merged string where added/deleted substrings are marked with tags.
 * StringDiff::overlap($a, $b) returns a copy of $a where overlaps with $b are marked with tags.
 * Please see examples.php.
 *
 * In initial stages, I used code by Christopher Bloom for finding the longest common substring.
 * url: http://www.christopherbloom.com/category/programming/code-snippets/
 * Since I wanted to support unicode strings and a simple port from Christopher's ascii version
 * to one using mb functions would have been too slow, I changed the logic:
 * longest_common_substring now converts both strings to arrays and then searches for the
 * longest common subarray. A unicode-aware function is only needed for splitting the strings
 * to arrays here - the array comparison code is agnostic about character encoding.
 */
class StringDiff
{
    // since we are looking for *differences* between strings
    // we require that the strings being compared have a significant
    // overlap; if at least a third of the two strings match, this seems
    // to yield results that seem natural
    // the $divisor is used to divide the string lengths to determine
    // the minimum required length of any overlap
    private static $divisor = 0;

    /**
     * @return the CSS needed to display diff/overlap results in html
     */
    public static function getCSS()
    {
        $css = '<style type="text/css">';
        $css .= 'del {color:red;text-decoration: line-through;} ';
        $css .= 'ins {color:blue;} ';
        $css .= 'overlap {color:green; text-decoration:underline;} ';
        $css .= '</style>';

        return $css;
    }

    /**
     * calculate the difference between 2 strings.
     *
     * @param string $a
     * @param string $b
     * @param float  $divisor for calculating the minimum required percentage of overlap
     *
     * @return string a merged version of $a and $b, with deleted and inserted
     *                portions indicated by <del></del> and <ins></ins> tags
     */
    public static function diff($a, $b, $divisor = 3.0)
    {
        self::$divisor = $divisor;
        if ($a == $b) {
            return $a;
        } elseif (empty($a)) {
            return '<ins>'.$b.'</ins>';
        } elseif (empty($b)) {
            return '<del>'.$a.'</del>';
        }
        $lcs = StringDiff::longest_common_substring($a, $b);
        if (empty($lcs)) {
            return '<del>'.$a.'</del><ins>'.$b.'</ins>';
        }
        $atripartite = StringDiff::tripartite($a, $lcs);
        $btripartite = StringDiff::tripartite($b, $lcs);
        $headdiff = StringDiff::diff($atripartite[0], $btripartite[0]);
        $taildiff = StringDiff::diff($atripartite[2], $btripartite[2]);

        return $headdiff.$lcs.$taildiff;
    }
    /**
     * @param string $a
     * @param string $b
     * @param float  $divisor for calculating the minimum required percentage of overlap
     *
     * @return string a copy of $a, where the overlap is indicated with <overlap></overlap>
     *                tags
     */
    public static function overlap($a, $b, $divisor = 3.0)
    {
        self::$divisor = $divisor;
        if ($a == $b) {
            return $a;
        } elseif (empty($a)) {
            return '';
        } elseif (empty($b)) {
            return $a;
        }
        $lcs = StringDiff::longest_common_substring($a, $b);
        if (empty($lcs)) {
            return $a;
        }
        $atripartite = StringDiff::tripartite($a, $lcs);
        $btripartite = StringDiff::tripartite($b, $lcs);
        $headdiff = StringDiff::overlap($atripartite[0], $btripartite[0]);
        $taildiff = StringDiff::overlap($atripartite[2], $btripartite[2]);

        return $headdiff.'<overlap>'.$lcs.'</overlap>'.$taildiff;
    }

    /**
     * splits the provided string into three parts:
     * head: the substring before $middle
     * $middle
     * tail: the substring after middle.
     *
     * @param string $s
     * @param string $middle
     *
     * @return array (head, middle, tail)
     */
    public static function tripartite($s, $middle)
    {
        $startmiddle = mb_strpos($s, $middle, 0, 'utf-8');
        $schars = preg_split('//u', $s, -1, PREG_SPLIT_NO_EMPTY);
        $mchars = preg_split('//u', $middle, -1, PREG_SPLIT_NO_EMPTY);
        //$endmiddle = $startmiddle+mb_strlen($middle, 'utf-8');
        $endmiddle = $startmiddle+count($mchars);
        $head = ($startmiddle>0) ? implode(array_slice($schars, 0, $startmiddle)) : '';
        $tail = ($endmiddle<count($schars)) ? implode(array_slice($schars, $endmiddle)) : '';

        return array($head, $middle, $tail);
    }

    /**
     * finds the longest common substring for the two provided strings.
     *
     * @param string $a
     * @param string $b
     *
     * @return string the longest common substring
     */
    public static function longest_common_substring($a, $b)
    {
        $achars = preg_split('//u', $a, -1, PREG_SPLIT_NO_EMPTY);
        $bchars = preg_split('//u', $b, -1, PREG_SPLIT_NO_EMPTY);
        $arr = StringDiff::longest_common_subarray($achars, $bchars);

        return implode($arr);
    }
    /**
     * find the longest_common_subarray between $achars and $bchars
     * requirement: a subarray must have at least a third of the
     * of $achars and $bchars (or whatever percentage the setting of
     * $divisor will yield).
     */
    public static function longest_common_subarray($achars, $bchars)
    {
        $alen = count($achars);
        $blen = count($bchars);
        if ($alen > $blen) {
            //swap
            $cchars = $achars;
            $achars = $bchars;
            $bchars = $cchars;
            $clen = $alen;
            $alen = $blen;
            $blen = $clen;
        }
        $binverted = StringDiff::array_invert($bchars);
        $longest_common_subarray = array();
        $idmap = StringDiff::getIDMap($achars, $alen, $binverted);
        $commonarrays = StringDiff::getCommonArrays($idmap, $alen, $achars);
        if (empty($commonarrays)) {
            return array();
        }
        $longestarray = StringDiff::getLongestCommonArray($commonarrays);
        if (count($longestarray) < ceil($alen/StringDiff::$divisor)
            && count($longestarray) < ceil($blen/StringDiff::$divisor)) {
            return array(); // overlap is too short
        }

        return $longestarray; // long enough
    }

    /**
     * given an array of form
     * array (
     *  key1 => array(),
     *  key2 => array();
     *  ...
     * )
     *  this returns the longest value.
     *
     * @param array $commonarrays
     *
     * @return Ambigous <>
     */
    public static function getLongestCommonArray(array $commonarrays)
    {
        $longestIdx = -1;
        $maxlen = -1;
        $num = count($commonarrays);
        foreach ($commonarrays as $key => $val) {
            $len = count($val);
            if ($len > $maxlen) {
                $maxlen = $len;
                $longestIdx = $key;
            }
        }
        //      print_r($longest_common_substring);
        return $commonarrays[$longestIdx];
    }
    /**
     * uses idmap to find all contiguous elements in array achars
     * that have a corresponding contiguous section in the mapped array
     * (i.e. bchars); returns an array with a mapping from the start
     * position in achars to the mapped contiguous array of elements.
     *
     * @param array        $idmap
     * @param unknown_type $alen
     * @param array        $achars
     *
     * @return multitype:multitype:Ambigous <>  Ambigous <>
     */
    public static function getCommonArrays(array $idmap, $alen, array $achars)
    {
        $commonarrays = array();
        for ($i = 0; $i<$alen; $i++) {
            if (isset($idmap[$i])) {
                foreach ($idmap[$i] as $bkey) {
                    $key = $i.'-'.$bkey;
                    $prevkey = ($i-1).'-'.($bkey-1);
                    if (isset($commonarrays[$prevkey])) {
                        $commonarrays[$key] = $commonarrays[$prevkey];
                        $commonarrays[$key][] = $achars[$i];
                    } else {
                        $commonarrays[$key] = array($achars[$i]);
                    }
                }
            }
        }

        return $commonarrays;
    }
    /**
     * provides a mapping from each position in array a
     * to all positions in array b where the same character
     * occurs
     * e.g.
     *  $a = 'abc',
     *  $b = 'xyaac',
     *  $idmap = array(
     *      'a' => array(2,3),
     *      'b' => array()
     *      'c' => array(4)
     *  ).
     *
     * @param array $achars
     * @param int   $alen
     * @param array $binverted
     *
     * @return multitype:NULL Ambigous <>
     */
    public static function getIDMap(array $achars, $alen, array $binverted)
    {
        $idmap = array();
        for ($i = 0; $i<$alen; $i++) {
            if (isset($binverted[$achars[$i]])) {
                $idmap[$i] = $binverted[$achars[$i]];
            } else {
                $idmap[$i] = null;
            }
        }

        return $idmap;
    }

    /**
     * inverts an array, which means here: returns an index list for each item in the array
     * e.g.
     *  $a = array (
     *      0 => 'a',
     *      1 => 'b',
     *      2 => 'a'
     * )
     *  $ainverted = array (
     *      'a' => array(0,2),
     *      'b' => array(1),
     *  ).
     *
     * @param array $a
     *
     * @return array
     */
    public static function array_invert(array $a)
    {
        $inverted = array();
        $alen = count($a);
        for ($i = 0; $i<$alen; $i++) {
            if (!isset($inverted[$a[$i]])) {
                $inverted[$a[$i]] = array();
            }
            $inverted[$a[$i]][] = $i;
        }

        return $inverted;
    }
}

/*
$s1 = 'thisisalongsubstringinastring';
$s2 = 'theotherlongishsubstringinastr';

print $s1."\n";
print $s2."\n";

$t1 = microtime(true);
for($i=0; $i<10000; $i++){
$lcs = StringDiff::longest_common_substring($s1, $s2);
}
$t2 = microtime(true);
$d = $t2-$t1;
print $d." $lcs\n";
*/
