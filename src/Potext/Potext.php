<?php

namespace Potext;

use Sepia\PoParser;
use Sepia\FileHandler;

class Potext {
    private $parser;
    private $entries;
    private $headers;
    public $pluralExpression = null;
    const HARD_CHECK_LENGTH = 40;

    public function getHeader($name) {
        foreach ($this->headers as $header) {
            if (preg_match("/$name:\s*(.+)\\\/", $header, $match)) {
                return $match[1];
                break;
            }
        }

        return null;
    }

    public function getEntries() {
        return $this->entries;
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

    public function getEntryValue($msgid, $value) {
        $result = null;

        if (isset($this->entries[$msgid]) &&
            isset($this->entries[$msgid][$value])
        ) {
            $result = $this->entries[$msgid][$value];
        } else if (strlen($msgid) > self::HARD_CHECK_LENGTH) {
            $search = substr($msgid, 0, self::HARD_CHECK_LENGTH);

            foreach ($this->entries as $entryKey => $entry) {
                if (
                    strpos($entryKey, $search) != false &&
                    isset($entry['msgid']) &&
                    isset($entry[$value]) &&
                    implode('', $entry['msgid']) == $msgid
                ) {
                    $result = $entry[$value];
                    break;
                }
            }
        }

        if (count($result)) {
            $result = implode('', $result);
        }

        return $result;
    }

    public function getText($msgid) {
        return $this->getEntryValue($msgid, 'msgstr');
    }

    public function getPlural($key, $n) {
        $index = $this->getPluralForm($n);

        if (is_numeric($index)) {
            return $this->getEntryValue($key, "msgstr[$index]");
        } else {
            return null;
        }
    }

    public function __construct(
        $file,
        $options = array(
            'parsePluralCode' => true
        )
    ) {
        $this->parser = new PoParser(new FileHandler($file), $options);
        $this->entries = $this->parser->parse();
        $this->headers = $this->parser->getHeaders();

        if ($options['parsePluralCode'] == true) {
            $this->parsePluralExpression();
        }
    }
}
