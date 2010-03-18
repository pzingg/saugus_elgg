<?php
global $CFG;
// Initialise magpie

if (isset($CFG->rsspostsmaxage) && $CFG->rsspostsmaxage > 0) {
    $CFG->rsspostsmaxage = (int) $CFG->rsspostsmaxage;
} elseif (!isset($CFG->rsspostsmaxage)) {
    $CFG->rsspostsmaxage = 60;
} else {
    $CFG->rsspostsmaxage = 0;
}

define('rss','true');
define('MAGPIE_DIR', $CFG->dirroot . "units/magpie/");
define('MAGPIE_OUTPUT_ENCODING', 'utf8');
require_once(MAGPIE_DIR . 'rss_fetch.inc');

?>