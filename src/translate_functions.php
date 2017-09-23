<?php

use Potext\Translate;

function _po($text) {
    return Translate::text($text);
}

function _npo($text1, $text2, $n) {
    return Translate::plural($text1, $text2, $n);
}

function _epo($text) {
    echo _po($text);
}

function _enpo($text1, $text2, $n) {
    printf(_npo($text1, $text2, $n), $n);
}