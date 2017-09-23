# Potext
Translate text in a gettext-like way by parsing .po files instead of actually using the PHP gettext implementation.

The default gettext implementation doesn't always work and is a bit hard to understand. Potext gives you the ability to still use shorthand functions like `_po()` and `_npo()` (for plurals), but use a .po file (parsed with [PHP-po-parser](https://github.com/raulferras/PHP-po-parser)) to display the actual text. No text domains or locales. You just put your text in those functions and then set a file whose text will be presented to the user.

# Installation

Clone this repo or install with Composer:

```
composer require hdodov/potext:dev-master
```

# Usage

## Setup

Firstly, make sure you `require` Composer's autoload file:

```php
require __DIR__ . '/vendor/autoload.php';
```

Then, create a Potext object. It allows you to extract data from the po file:

```php
$potext_bg = new Potext\Potext('./locales/bg.po');
```

Finally, set the Translate singleton to use the Potext object you've just created:

```php
Potext\Translate::$potext = $potext_bg;
```
This singleton is used by the translator functions (like `_po()`) to display text. If it has no `$potext` set, it would simply display the input text.

**Now you're ready to display some translated text!**

## Translator functions

```php
_po("Text");                        // Get translated string
_epo("Text");                       // Echo translated string
_npo("%d Text", "%d Texts", $n);    // Get translated plural string
_enpo("%d Text", "%d Texts", $n);   // Echo translated plural string
```

# Notes

If you're using a program like [Poedit](https://poedit.net/) to translate text, strings inside the Potext functions will not be collected unless you add them to Poedit's keyword list! You need to add this:

```
_po
_epo
_npo:1,2
_enpo:1,2
```
