<?php

require __DIR__ . '/../vendor/autoload.php';

use Potext\Potext;
use Potext\Translate;

function echoText() {
    printf("<h3>%s</h3>", _po("Hello!"));

    for ($i=0; $i < 5; $i++) { 
        printf(_npo("%d apple", "%d apples", $i) . '<br>', $i);
    }

    _epo(
        "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book."
    );
    echo '<br>';
    _enpo(
        "It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of %d Letraset sheet containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
        "It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of %d Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
        3
    );
    echo '<br>';
    _enpo("%d banana", "%d bananas", 1);
    echo '<br>';
    _enpo("%d banana", "%d bananas", 3);
    echo '<br>';
    _epo("Goodbye!");
}

echoText();

$potext_bg = new Potext('./locales/bg.po');
$potext_es = new Potext('./locales/es.po', array(
    'parsePluralCode' => false
));
$potext_es->pluralExpression = '$plural = ($n != 1);';

printf("<hr>Bulgarian plurals (parsed from .po file):<br><pre>%s</pre>", $potext_bg->pluralExpression);
Translate::$potext = $potext_bg;
echoText();

printf("<hr>Spanish plurals (manually set):<br><pre>%s</pre>", $potext_es->pluralExpression);
Translate::$potext = $potext_es;
echoText();