<?php

namespace Potext;

use Sepia\PoParser;
use Sepia\FileHandler;

class Potext {
    private $parser;
    private $entries;
    private $headers;
    public $pluralExpression = null;

    public function getHeader($name) {
        foreach ($this->headers as $header) {
            if (preg_match("/$name:\s*(.+)\\\/", $header, $match)) {
                return $match[1];
                break;
            }
        }

        return null;
    }

    public function parsePluralExpression() {
        $expression = $this->getHeader("Plural-Forms");
        if (!$expression) {
            return false;
        }

        // Add '$' before variables (nplurals, plural, n).
        $code = preg_replace('/[a-z]+/i', '\$${0}', $expression);

        while (1) {
            // In PHP, when ternary operators are nested, their "else" part
            // must be enclosed in brackets, otherwise they don't function
            // properly.
            $replace = preg_replace('/\:\s*([^\(][^\;]+)/', ':(${1})', $code);
            if ($replace && $replace != $code) {
                $code = $replace;
            } else {
                break;
            }
        }
        
        $this->pluralExpression = $code;
        return true;
    }

    public function getPluralForm($n) {
        if ($this->pluralExpression) {
            eval($this->pluralExpression);

            if (isset($plural)) {
                return (int)$plural;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function getEntryValue($key, $value) {
        if (isset($this->entries[$key]) &&
            isset($this->entries[$key][$value]) &&
            isset($this->entries[$key][$value][0])
        ) {
            return $this->entries[$key][$value][0];
        } else {
            return null;
        }
    }

    public function getText($key) {
        return $this->getEntryValue($key, 'msgstr');
    }

    public function getPlural($key, $n) {
        $index = $this->getPluralForm($n);

        if (is_numeric($index)) {
            return $this->getEntryValue($key, "msgstr[$index]");
        } else {
            return null;
        }
    }

    public function __construct($file, $parsePluralCode = true) {
        $this->parser = new PoParser(new FileHandler($file));
        $this->entries = $this->parser->parse();
        $this->headers = $this->parser->getHeaders();

        if ($parsePluralCode) {
            $this->parsePluralExpression();
        }
    }
}
