<?php
    // Echo a translated string.
    function _e($text, $domain = 'elgg') {
        global $l10n;

        if (isset($l10n[$domain])) {
            echo $l10n[$domain]->translate($text);
        } else {
            echo $text;
        }
    }

    // Return the plural form.
    function __ngettext($single, $plural, $number, $domain = 'elgg') {
    	global $l10n;

    	if (isset($l10n[$domain])) {
    		return $l10n[$domain]->ngettext($single, $plural, $number);
    	} else {
    		if ($number != 1)
    			return $plural;
    		else
    			return $single;
    	}
    }

    function load_textdomain($domain, $mofile) {
    	global $l10n;

    	if (isset($l10n[$domain]))
    		return;

    	if ( is_readable($mofile))
    		$input = new CachedFileReader($mofile);
    	else
    		return;

    	$l10n[$domain] = new gettext_reader($input);
    }

    function parse_http_accept_language($str = null) {
        global $CFG;

        $browser_lang = "";

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browser_lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        }
        
        // getting http instruction if not provided
        $str = $str ? $str : $browser_lang;

        // Local default short code
        $local_code = substr($CFG->defaultlocale, 0, 2);

        // exploding accepted languages
        $langs = explode(',',$str);

        // creating output list
        $accepted = array();

        foreach ($langs as $lang) {
            // parsing language preference instructions 
            // 2_digit_code[-longer_code][;q=coefficient]
            ereg('([a-z]{1,2})(-([a-z0-9]+))?(;q=([0-9\.]+))?', $lang, $found);

            // 2 digit lang code
            $code = $found[1];

            // lang code complement
            $morecode = $found[3];

            if (empty($morecode)) {
                $fullcode = $code . "_" . strtoupper($code);
            } else {
                $fullcode = $code . "_" . strtoupper($morecode);
            }

            // coefficient
            $coef = sprintf('%3.1f', $found[5] ? $found[5] : '1');

            // for sorting by coefficient
            $key = $coef.'-'.$code;

            // Three conditions:
            // 1 - either translation file exists,
            // 2 - or the default fullcode matches the user fullcode,
            // 3 - or the default short code matches the default
            if (file_exists(textdomain_file($fullcode, $domain = 'elgg')) || $fullcode == $CFG->defaultlocale || $local_code == $code) {
                // adding
                $accepted[$key] = array('code'     => $code,
                                        'coef'     => $coef,
                                        'morecode' => $morecode,
                                        'fullcode' => $fullcode);
            }        
        }

        // sorting the list by coefficient desc
        krsort($accepted);

        return $accepted;
    }

    function init_i18n($domain, $native = false) {
        global $CFG;

        // To prevent config file error
        if (!defined($CFG->defaultlocale) || empty($CFG->defaultlocale)) {
            $CFG->locale = "en_GB";
        }

        $languages = parse_http_accept_language();

        // Get the language array key
        $lang_key = array_shift(array_keys($languages));

        if (!empty($languages[$lang_key]['fullcode'])) {
            //Set initial user locale
            $CFG->userlocale = $languages[$lang_key]['fullcode'];
        } else {
            $CFG->userlocale = $CFG->defaultlocale;
        }

        if ($native == false) {
            $textdomainfile = textdomain_file($CFG->defaultlocale);
            load_textdomain($domain, $textdomainfile);
        }

        return;
    }

    function textdomain_file($locale, $domain = 'elgg') {
        global $CFG;

        return $CFG->dirroot.'languages/'.$locale.'/LC_MESSAGES/'.$domain.'.mo';
    }

    if (!function_exists('gettext')) {
        // No native gettext support
        function gettext($text, $domain = 'elgg') {
            _e($text, $domain);
        }
    } else {
        // Have gettext
        $domain = "elgg";
        init_i18n($domain, true);

        $encoding = 'UTF-8';
        $locale = $CFG->userlocale;

        putenv("LANGUAGE = $locale");
        putenv("LANG = $locale");

        T_setlocale(LC_ALL, $locale);

        // This will stop Windows from whining
        if (!defined('LC_MESSAGES'))
            define('LC_MESSAGES', 6);

        T_setlocale(LC_MESSAGES, $locale);

        bindtextdomain($domain, $CFG->dirroot."languages");

        // bind_textdomain_codeset is supported only in PHP 4.2.0+
        if (function_exists('bind_textdomain_codeset'))
            bind_textdomain_codeset($domain, $encoding);

        textdomain($domain);
    }
?>
