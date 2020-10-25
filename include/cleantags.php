<?php

/* This file comes from a post by tREXX [www.trexx.ch] in http://www.php.net/manual/en/function.strip-tags.php */

/**
 * Allow these tags
 */
$allowedTags = '<h1><b><i><a><ul><li><pre><hr><blockquote>';

/**
 * Disallow these attributes/prefix within a tag
 */
$stripAttrib = '';

/**
 * @param mixed $source
 * @return string
 * @desc Strip forbidden tags and delegate tag-source check to cleanAttributes()
 */
function cleanTags($source)
{
    global $allowedTags;

    $source = strip_tags($source, $allowedTags);

    $source = preg_replace('/<(.*?)>/ie', "'<'.cleanAttributes('\\1').'>'", $source);

    return $source;
}

/**
 * @param mixed $tagSource
 * @return string
 * @desc Strip forbidden attributes from a tag
 */
function cleanAttributes($tagSource)
{
    global $stripAttrib;

    return stripslashes(preg_replace("/$stripAttrib/i", 'forbidden', $tagSource));
}

// Will output: <a href="forbiddenalert(1);" target="_blank" forbidden =" alert(1)">test</a>
// echo cleanTags('<a href="javascript:alert(1);" target="_blank" onMouseOver = "alert(1)">test</a>');
