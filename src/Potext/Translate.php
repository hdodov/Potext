<?php

namespace Potext;

class Translate {
    public static $potext = null;

    public static function text($text) {
        if (self::$potext) {
            $potext = self::$potext->getText($text);

            if ($potext) {
                return $potext;
            }
        }

        return $text;
    }

    public static function plural($text1, $text2, $n) {
        if (self::$potext) {
            $potext = self::$potext->getPlural($text1, $n);

            if ($potext) {
                return $potext;
            }
        }

        if ($n != 1) {
            return $text2;
        } else {
            return $text1;
        }
    }
}
