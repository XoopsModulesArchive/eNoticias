<?php

// $Id: v 1.00 2004/12/30 Gustavo S. Villa Exp
//  ------------------------------------------------------------------------ //
//                            e-Notícias                                     //
//                              E-WARE                                       //
//                   <http://www.e-ware.com.br>                             //
//  ------------------------------------------------------------------------ //
//  Você não pode substituir ou alterar qualquer parte desses comentários    //
//  ou créditos dos titulares e autores os quais são considerados direitos   //
//  reservados.                                                              //
//  ------------------------------------------------------------------------ //
//  Autor: Gustavo S. Villa  <guvilladev@e-ware.com.br>                      //
//  ------------------------------------------------------------------------ //

include 'header.php';

require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';

$GLOBALS['xoopsOption']['template_main'] = 'eNoticias_article.html';

global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $XOOPS_URL;
$myts = MyTextSanitizer::getInstance();

$articleID = isset($_GET['articleID']) ? (int)$_GET['articleID'] : 0;
$storypage = isset($_GET['page']) ? (int)$_GET['page'] : 0;

$story = [];

if (!$articleID) {
    $result = $xoopsDB->query(
        'SELECT articleID, columnID, headline, lead, bodytext, teaser, uid, submit, datesub, counter, weight, html, smiley, xcodes, breaks, block, artimage, votes, rating, comments, offline, notifypub FROM '
        . $xoopsDB->prefix('eNoticias_Artigos')
        . ' WHERE datesub < '
        . time()
        . ' AND datesub > 0 AND (submit = 0) ORDER BY datesub DESC',
        1,
        0
    );
} else {
    $result = $xoopsDB->query(
        'SELECT articleID, columnID, headline, lead, bodytext, teaser, uid, submit, datesub, counter, weight, html, smiley, xcodes, breaks, block, artimage, votes, rating, comments, offline, notifypub FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = $articleID"
    );
}

[$articleID, $columnID, $headline, $lead, $bodytext, $teaser, $uid, $submit, $datesub, $counter, $weight, $html, $smiley, $xcodes, $breaks, $block, $artimage, $votes, $rating, $comments, $offline, $notifypub] = $xoopsDB->fetchRow($result);

if (!$xoopsUser || ($xoopsUser->isAdmin($xoopsModule->mid()) && 1 == $xoopsModuleConfig['adminhits']) || ($xoopsUser && !$xoopsUser->isAdmin($xoopsModule->mid()))) {
    $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('eNoticias_Artigos') . " SET counter=counter+1 WHERE articleID = $articleID ");
}

$result2 = $xoopsDB->query('SELECT author, name, description, total, colimage FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = $columnID");
[$author, $name, $description, $total, $colimage] = $xoopsDB->fetchRow($result2);

$result3 = $xoopsDB->query('SELECT name, uname FROM ' . $xoopsDB->prefix('users') . " WHERE uid = $author");
[$name3, $uname3] = $xoopsDB->fetchRow($result3);
$authorname = $name3;

if (1 == $html) {
    $html = 1;
} else {
    $html = 0;
}
if (1 == $smiley) {
    $smiley = 1;
} else {
    $smiley = 0;
}
if (1 == $breaks) {
    $breaks = 1;
} else {
    $breaks = 0;
}
if (1 == $xcodes) {
    $xcodes = 1;
} else {
    $xcodes = 0;
}
if (1 == $comments) {
    $comments = 1;
} else {
    $comments = 0;
}

$story['id'] = (int)$articleID;
$story['columnID'] = $columnID;
$story['posted'] = formatTimestamp($datesub, $xoopsModuleConfig['dateformat']);
$story['headline'] = htmlspecialchars($headline, ENT_QUOTES | ENT_HTML5);
$templead = cleanTags($lead);
$story['lead'] = htmlspecialchars($templead, ENT_QUOTES | ENT_HTML5);

// includes code by toshimitsu
if ('' != trim($bodytext)) {
    $articletext = explode('[pagebreak]', $bodytext);

    $story_pages = count($articletext);

    if ($story_pages > 1) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

        $pagenav = new XoopsPageNav($story_pages, 1, $storypage, 'page', 'articleID=' . $articleID);

        $xoopsTpl->assign('pagenav', $pagenav->renderNav());

        if (0 == $storypage) {
            $templead = cleanTags($story['lead']);

            $story['bodytext'] = $templead . '<br><br>' . $myts->displayTarea($bodytext, $html, $smiley, $xcodes, 1, $breaks);
        } else {
            $story['bodytext'] = $myts->displayTarea($bodytext, $html, $smiley, $xcodes, 1, $breaks);
        }
    } else {
        $templead = cleanTags($story['lead']);

        $story['bodytext'] = $templead . '<br><br>' . $myts->displayTarea($bodytext, $html, $smiley, $xcodes, 1, $breaks);
    }
}

if (0.0000 != $rating) {
    $story['rating'] = '' . _MD_eNoticias_RATING . ': ' . $myts->stripSlashesGPC(number_format($rating, 2));

    $story['votes'] = '' . _MD_eNoticias_VOTES . ': ' . $myts->stripSlashesGPC($votes);
} else {
    $story['rating'] = _MD_eNoticias_NOTRATED;
}

if (is_object($xoopsUser)) {
    $xoopsTpl->assign(
        'authorpm_link',
        "<a href=\"javascript:openWithSelfMain('" . XOOPS_URL . '/pmlite.php?send2=1&amp;to_userid=' . $author . "', 'pmlite', 450, 380);\"><img src=\"" . XOOPS_URL . '/' . $xoopsModuleConfig['sbimgdir'] . '/writeauthor.gif" alt="' . _MD_eNoticias_WRITEAUTHOR . '"></a>'
    );
} else {
    $xoopsTpl->assign('user_pmlink', '');
}

$story['author'] = getLinkedUnameFromId($author, 0);
$story['uid'] = $author;

if (empty($authorname)) {
    $story['authorname'] = ucfirst($uname3);
} else {
    $story['authorname'] = $authorname;
}

$story['colname'] = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);
$story['coldesc'] = htmlspecialchars($description, ENT_QUOTES | ENT_HTML5);

$story['counter'] = $myts->stripSlashesGPC($counter);
$story['colimage'] = $colimage;
$story['artimage'] = $artimage;

$xoopsTpl->assign('story', $story);

// $xoopsTpl->assign('comments', $comments);
$xoopsTpl->assign('xcodes', $xcodes);
$xoopsTpl->assign('mail_link', 'mailto:?subject=' . sprintf(_MD_eNoticias_INTARTICLE, $xoopsConfig['sitename']) . '&amp;body=' . sprintf(_MD_eNoticias_INTARTFOUND, $xoopsConfig['sitename']) . ':  ' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/article.php?articleID=' . $articleID);
$xoopsTpl->assign('articleID', $articleID);
$xoopsTpl->assign('lang_ratethis', _MD_eNoticias_RATETHIS);
$xoopsTpl->assign('lang_modulename', $xoopsModule->name());
$xoopsTpl->assign('lang_moduledirname', $xoopsModule->dirname());
$xoopsTpl->assign('imgdir', $xoopsModuleConfig['sbimgdir']);
$xoopsTpl->assign('uploaddir', $xoopsModuleConfig['sbuploaddir']);

$listarts = [];

$result4 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE columnID = $columnID AND submit = 0 AND offline = 0");
[$totalartsbyauthor] = $xoopsDB->fetchRow($result4);
$result5 = $xoopsDB->query('SELECT articleID, headline, datesub FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE columnID = $columnID AND articleID != $articleID AND submit = 0 AND offline = 0 ORDER BY datesub DESC", $xoopsModuleConfig['morearts'], 0);

if ($totalartsbyauthor > 0) { // That is, if there are articles in this column...
    while (list($articleID, $headline, $published) = $xoopsDB->fetchRow($result5)) {
        $link = [];

        $link['articleID'] = $articleID;

        $link['arttitle'] = htmlspecialchars($headline, ENT_QUOTES | ENT_HTML5);

        $link['published'] = formatTimestamp($published, $xoopsModuleConfig['dateformat']);

        $listarts['links'][] = $link;
    }

    $xoopsTpl->assign('listarts', $listarts);

    $xoopsTpl->assign('readmore', "<a style='font-size: 9px;' href=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/column.php?columnID=' . $columnID . '>' . _MD_eNoticias_READMORE . " [$totalartsbyauthor]</a> ");
}

if (1 == $comments) {
    require XOOPS_ROOT_PATH . '/include/comment_view.php';
}

$xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="style.css">');

require_once XOOPS_ROOT_PATH . '/footer.php';
