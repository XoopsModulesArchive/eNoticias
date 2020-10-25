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

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}
foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $XOOPS_URL, $indexp;
$myts = MyTextSanitizer::getInstance();

$columnID = isset($_GET['columnID']) ? (int)$_GET['columnID'] : 0;

$groups = ($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$module_id = $xoopsModule->getVar('mid');
$gpermHandler = xoops_getHandler('groupperm');

require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

$GLOBALS['xoopsOption']['template_main'] = 'eNoticias_column.html';

$resultcols = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = $columnID ");
$totalcols = $xoopsDB->getRowsNum($resultcols);

$resultarts = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE columnID = '$columnID' AND submit ='0' AND offline = 0 ORDER BY datesub DESC");
$totalarts = $xoopsDB->getRowsNum($resultarts);

$sql2 = 'SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE columnID = '$columnID' AND submit ='0' AND offline = 0 ORDER BY datesub DESC";
$result = $xoopsDB->query($sql2, $xoopsModuleConfig['indexperpage'], $start);

[$columnID, $author, $name, $description, $total, $weight, $colimage, $created] = $xoopsDB->fetchRow($resultcols);

$result3 = $xoopsDB->query('SELECT name FROM ' . $xoopsDB->prefix('users') . " WHERE uid = $author");
[$name3] = $xoopsDB->fetchRow($result3);
$authorname = $name3;

$category = [];
$articles = [];

if (0 == $totalcols) {
    redirect_header('javascript:history.go(-1)', 1, _MD_eNoticias_MAINNOSELECTCAT);

    exit();
}
if (0 == $totalarts) {
    redirect_header('javascript:history.go(-1)', 1, _MD_eNoticias_MAINNOTOPICS);

    exit();
}

$category['colid'] = $columnID;
$category['name'] = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);
$category['authorname'] = $authorname;
$category['author'] = getLinkedUnameFromId($author, 0);
$category['image'] = $colimage;
$category['total'] = $totalarts;
$category['description'] = $myts->displayTarea($description);
$category['description'] = $myts->xoopsCodeDecode($description, $allowimage = 1);
$xoopsTpl->assign('category', $category);

$totalarts = 0;

while (false !== ($col_data = $xoopsDB->fetchArray($result))) {
    if ($gpermHandler->checkRight('Column Permissions', $columnID, $groups, $module_id)) {
        $articles['id'] = $col_data['articleID'];

        $articles['columnID'] = $col_data['columnID'];

        if ($col_data['artimage'] && 'blank.png' != $col_data['artimage'] && file_exists(XOOPS_ROOT_PATH . '/' . "{$xoopsModuleConfig['sbuploaddir']}/{$col_data['artimage']}")) {
            $articles['image'] = htmlspecialchars(XOOPS_URL . '/' . "{$xoopsModuleConfig['sbuploaddir']}/{$col_data['artimage']}", ENT_QUOTES | ENT_HTML5);
        } else {
            $articles['image'] = '';
        }

        $rating = $col_data['rating'];

        $votes = $col_data['votes'];

        if (0.00 != $rating) {
            $articles['rating'] = _MD_eNoticias_RATING . ': ' . $myts->stripSlashesGPC(number_format($rating, 2));

            $articles['votes'] = _MD_eNoticias_VOTES . ': ' . $myts->stripSlashesGPC($votes);
        } else {
            $articles['rating'] = _MD_eNoticias_RATING . ': 0.00';

            $articles['votes'] = _MD_eNoticias_VOTES . ': 0';
        }

        $tempteaser = cleanTags($col_data['teaser']);

        $articles['teaser'] = $myts->displayTarea($tempteaser);

        $articles['headline'] = $myts->displayTarea($col_data['headline']);

        $articles['lead'] = $myts->displayTarea($col_data['lead']);

        $articles['datesub'] = formatTimestamp($col_data['datesub'], $xoopsModuleConfig['dateformat']);

        $articles['articleID'] = $col_data['articleID'];

        $articles['poster'] = XoopsUserUtility::getUnameFromId($col_data['uid']);

        $articles['counter'] = $myts->stripSlashesGPC($col_data['counter']);

        // Functional links

        if ($xoopsUser) {
            if ($xoopsUser->isAdmin()) {
                $articles['adminlinks'] = '<a href="admin/article.php?op=mod&articleID='
                                          . $articles['articleID']
                                          . '" target="_blank"><img src="images/links/edit.gif" border="0" alt="'
                                          . _MD_eNoticias_EDITART
                                          . '" width="15" height="11"></a>&nbsp;<a href="admin/article.php?op=del&articleID='
                                          . $articles['articleID']
                                          . '" target="_blank"><img src="images/links/delete.gif" border="0" alt="'
                                          . _MD_eNoticias_DELART
                                          . '" width="15" height="11"></a>&nbsp;';

                $articles['userlinks'] = '<a href="print.php?articleID='
                                          . $articles['articleID']
                                          . '" target="_blank"><img src="images/links/print.gif" border="0" alt="'
                                          . _MD_eNoticias_PRINTART
                                          . '" width="15" height="11"></a>&nbsp;<a href="mailto:?subject='
                                          . sprintf(
                                              _MD_eNoticias_INTART,
                                              $xoopsConfig['sitename']
                                          )
                                          . '&amp;body='
                                          . sprintf(_MD_eNoticias_INTARTFOUND, $xoopsConfig['sitename'])
                                          . ':  '
                                          . XOOPS_URL
                                          . '/modules/'
                                          . $xoopsModule->dirname()
                                          . '/article.php?articleID='
                                          . $articles['articleID']
                                          . ' " target="_blank"><img src="images/links/friend.gif" border="0" alt="'
                                          . _MD_eNoticias_SENDTOFRIEND
                                          . '" width="15" height="11"></a>&nbsp;';
            } else {
                $articles['adminlinks'] = '';

                $articles['userlinks'] = '<a href="print.php?articleID='
                                          . $articles['articleID']
                                          . '" target="_blank"><img src="images/links/print.gif" border="0" alt="'
                                          . _MD_eNoticias_PRINTART
                                          . '" width="15" height="11"></a>&nbsp;<a href="mailto:?subject='
                                          . sprintf(
                                              _MD_eNoticias_INTART,
                                              $xoopsConfig['sitename']
                                          )
                                          . '&amp;body='
                                          . sprintf(_MD_eNoticias_INTARTFOUND, $xoopsConfig['sitename'])
                                          . ':  '
                                          . XOOPS_URL
                                          . '/modules/'
                                          . $xoopsModule->dirname()
                                          . '/article.php?articleID='
                                          . $articles['articleID']
                                          . ' " target="_blank"><img src="images/links/friend.gif" border="0" alt="'
                                          . _MD_eNoticias_SENDTOFRIEND
                                          . '" width="15" height="11"></a>&nbsp;';
            }
        } else {
            $articles['adminlinks'] = '';

            $articles['userlinks'] = '<a href="print.php?articleID='
                                      . $articles['articleID']
                                      . '" target="_blank"><img src="images/links/print.gif" border="0" alt="'
                                      . _MD_eNoticias_PRINTART
                                      . '" width="15" height="11"></a>&nbsp;<a href="mailto:?subject='
                                      . sprintf(
                                          _MD_eNoticias_INTART,
                                          $xoopsConfig['sitename']
                                      )
                                      . '&amp;body='
                                      . sprintf(_MD_eNoticias_INTARTFOUND, $xoopsConfig['sitename'])
                                      . ':  '
                                      . XOOPS_URL
                                      . '/modules/'
                                      . $xoopsModule->dirname()
                                      . '/article.php?articleID='
                                      . $articles['articleID']
                                      . ' " target="_blank"><img src="images/links/friend.gif" border="0" alt="'
                                      . _MD_eNoticias_SENDTOFRIEND
                                      . '" width="15" height="11"></a>&nbsp;';
        }

        $xoopsTpl->append('articles', $articles);

        $totalarts++;
    }
}

$pagenav = new XoopsPageNav($category['total'], $xoopsModuleConfig['indexperpage'], $start, 'start', 'op=col&columnID=' . $articles['columnID']);
$category['navbar'] = '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

$xoopsTpl->assign('category', $category);
$xoopsTpl->assign('lang_modulename', $xoopsModule->name());
$xoopsTpl->assign('lang_moduledirname', $xoopsModule->dirname());
$xoopsTpl->assign('imgdir', $xoopsModuleConfig['sbimgdir']);
$xoopsTpl->assign('uploaddir', $xoopsModuleConfig['sbuploaddir']);

$data = '';

$xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="style.css">');

require XOOPS_ROOT_PATH . '/footer.php';
