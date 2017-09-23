<?php

require __DIR__ . '/../vendor/autoload.php';

use Potext\Potext;
use Potext\Translate;

function echoText() {
    printf("<h3>%s</h3>", _po("Hello!"));

    for ($i=0; $i < 5; $i++) { 
        printf(_npo("%d apple", "%d apples", $i) . '<br>', $i);
    }

    _enpo("%d banana", "%d bananas", 1);
    echo '<br>';
    _enpo("%d banana", "%d bananas", 3);
    echo '<br>';
    _epo("Goodbye!");
}

echoText();

$potext_bg = new Potext('./locales/bg.po');
$potext_es = new Potext('./locales/es.po', false);
$potext_es->pluralExpression = '$plural = ($n != 1);';

printf("<hr>Bulgarian plurals (parsed from .po file):<br><pre>%s</pre>", $potext_bg->pluralExpression);
Translate::$potext = $potext_bg;
echoText();

printf("<hr>Spanish plurals (manually set):<br><pre>%s</pre>", $potext_es->pluralExpression);
Translate::$potext = $potext_es;
echoText();