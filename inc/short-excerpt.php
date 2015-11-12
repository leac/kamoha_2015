<?php

/* ----------------------------------------------------------------
 * Get short excerpt, length defined by location, and title length
 * -------------------------------------------------------------- */

// define title lengths
define('STICKY_TITLE_3_ROWS', 150);
define('STICKY_TITLE_2_ROWS', 110);
define('BLOCK1_TITLE_4_ROWS', 50); /* 11/2015 - New */
define('BLOCK1_TITLE_3_ROWS', 40); /* 11/2015 - changed from 75 */
define('BLOCK1_TITLE_2_ROWS', 25); /* 11/2015 - changed from 53 */
define('BLOCK2_TITLE_3_ROWS', 75);
define('BLOCK2_TITLE_2_ROWS', 53);
define('TAB_TITLE_3_ROWS', 75);
define('TAB_TITLE_2_ROWS', 50);

// define excerpt lengths
define('STICKY_EXCERPT_3_ROWS', 220);
define('STICKY_EXCERPT_2_ROWS', 200);
define('STICKY_EXCERPT_1_ROW', 250);
define('BLOCK1_EXCERPT_4_ROWS', 115); /* 11/2015 - New */
define('BLOCK1_EXCERPT_3_ROWS', 125); /* 11/2015 - changed from 60 */
define('BLOCK1_EXCERPT_2_ROWS', 135); /* 11/2015 - changed from 80 */
define('BLOCK1_EXCERPT_1_ROW', 180);
define('BLOCK2_EXCERPT_3_ROWS', 120);
define('BLOCK2_EXCERPT_2_ROWS', 160);
define('BLOCK2_EXCERPT_1_ROW', 180);
define('BLOCK3_EXCERPT_OTHER_POSTS', 250);
define('TAB_EXCERPT_3_ROWS', 50);
define('TAB_EXCERPT_2_ROWS', 70);
define('TAB_EXCERPT_1_ROW', 90);

/**
 * Get a limited part of the content - sans html tags and shortcodes -
 * according to the amount written in $limit. Make sure words aren't cut in the middle
 * @param int $limit - number of characters
 * @return string - the shortened content
 */
function kamoha_the_short_excerpt($limit) {
    $content = get_the_excerpt();
    /* sometimes there are <p> tags that separate the words, and when the tags are removed,
     * words from adjoining paragraphs stick together.
     * so replace the end <p> tags with space, to ensure unstickinees of words */
    $content = str_replace('</p>', ' ', $content);
    $content = strip_tags($content);
    $content = strip_shortcodes($content);
    $ret = $content; /* if the limit is more than the length, this will be returned */
    if (mb_strlen($content) >= $limit) {
        $ret = mb_substr($content, 0, $limit);
        // make sure not to cut the words in the middle:
        // 1. first check if the substring already ends with a space
        if (mb_substr($ret, - 1) !== ' ') {
            // 2. If it doesn't, find the last space before the end of the string
            $space_pos_in_substr = mb_strrpos($ret, ' ');
            // 3. then find the next space after the end of the string(using the original string)
            $space_pos_in_content = mb_strpos($content, ' ', $limit);
            // 4. now compare the distance of each space position from the limit
            if ($space_pos_in_content != false && $space_pos_in_content - $limit <= $limit - $space_pos_in_substr) {
                /* if the closest space is in the original string, take the substring from there */
                $ret = mb_substr($content, 0, $space_pos_in_content);
            } else {
                // else take the substring from the original string, but with the earlier (space) position
                $ret = mb_substr($content, 0, $space_pos_in_substr);
            }
        }
        $ret .= '&hellip;';
    }
    return $ret;
}

/**
 * Show a certain number of characters from the excerpt, depending on the length of the title.
 * If the title is one line - bring more of the excerpt. The more lines the title is, the less
 * of the excerpt should we show.
 * There is, of course, a difference between the various blocks, and therefore that parameter is passed by the caller
 * */
function kamoha_the_short_excerpt_by_len() {
    global $kamoha_homepage_part;
    $the_title = get_the_title();
    $title_len = mb_strlen($the_title);
    $ret = '';
    switch ($kamoha_homepage_part) {
        case KamohaHomepagePart::Sticky:
            /* case of three title rows */
            if ($title_len >= STICKY_TITLE_3_ROWS) {
                $ret = kamoha_the_short_excerpt(STICKY_EXCERPT_3_ROWS);
            } else {
                /* case of two title rows */
                if ($title_len >= STICKY_TITLE_2_ROWS) { //echo STICKY_EXCERPT_2_ROWS;
                    $ret = kamoha_the_short_excerpt(STICKY_EXCERPT_2_ROWS);
                } else {

                    /* case of one title row */
                    $ret = kamoha_the_short_excerpt(STICKY_EXCERPT_1_ROW);
                }
            }
            break;

        case KamohaHomepagePart::Newest:
            /* case of three title rows */
            if ($title_len >= BLOCK1_TITLE_4_ROWS) {
                $ret = kamoha_the_short_excerpt(BLOCK1_EXCERPT_4_ROWS);
            }elseif ($title_len >= BLOCK1_TITLE_3_ROWS) {
                $ret = kamoha_the_short_excerpt(BLOCK1_EXCERPT_3_ROWS);
            } elseif ($title_len >= BLOCK1_TITLE_2_ROWS) {
                /* case of two title rows */
                $ret = kamoha_the_short_excerpt(BLOCK1_EXCERPT_2_ROWS);
            } else {
                /* case of one title row */
                $ret = kamoha_the_short_excerpt(BLOCK1_EXCERPT_1_ROW);
            }
            break;

        case KamohaHomepagePart::Categories:
            /* case of three title rows */
            if ($title_len >= BLOCK2_TITLE_3_ROWS) {
                $ret = kamoha_the_short_excerpt(BLOCK2_EXCERPT_3_ROWS);
            } else {

                /* case of two title rows */
                if ($title_len >= BLOCK2_TITLE_2_ROWS) {
                    $ret = kamoha_the_short_excerpt(BLOCK2_EXCERPT_2_ROWS);
                } else {

                    /* case of one title row */
                    $ret = kamoha_the_short_excerpt(BLOCK2_EXCERPT_1_ROW);
                }
            }
            break;

        case KamohaHomepagePart::Blogs:
        case KamohaHomepagePart::Issues:
            global $kamoha_blog_post_index;
            /* case of three title rows */
            if ($title_len >= BLOCK1_TITLE_3_ROWS) {
                $ret = kamoha_the_short_excerpt(TAB_EXCERPT_3_ROWS);
            } else {

                /* case of two title rows */
                if ($title_len >= BLOCK1_TITLE_2_ROWS) {
                    $ret = kamoha_the_short_excerpt(TAB_EXCERPT_2_ROWS);
                } else {

                    /* case of one title row */
                    $ret = kamoha_the_short_excerpt(TAB_EXCERPT_1_ROW);
                }
            }

            break;

        case KamohaHomepagePart::Tabs:

            /* case of three title rows */
            if ($title_len >= TAB_TITLE_3_ROWS) {
                $ret = kamoha_the_short_excerpt(TAB_EXCERPT_3_ROWS);
            } else {

                if ($title_len >= TAB_TITLE_2_ROWS) {/* case of two title rows */
                    $ret = kamoha_the_short_excerpt(TAB_EXCERPT_2_ROWS);
                } else {
                    $ret = kamoha_the_short_excerpt(TAB_EXCERPT_1_ROW);
                }
            }
            break;
    }
    return $ret;
}
