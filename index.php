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

$op = '';

require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';

$groups = ($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
$module_id = $xoopsModule->getVar('mid');
$gpermHandler = xoops_getHandler('groupperm');

require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;

$columna = [];

$resultcols = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . '');
$totalcols = $xoopsDB->getRowsNum($resultcols);

// Options
switch ($op) {
    case 'default':
    default:

        global $xoopsUser, $xoopsConfig, $xoopsDB, $myts, $xoopsModuleConfig, $xoopsModule;

        $GLOBALS['xoopsOption']['template_main'] = 'eNoticias_index.html';

        $xoopsTpl->assign('lang_mainhead', sprintf(_MD_eNoticias_MAINHEAD, $xoopsModule->name()));
        $xoopsTpl->assign('lang_modulename', $xoopsModule->name());
        $xoopsTpl->assign('lang_moduledirname', $xoopsModule->dirname());
        $xoopsTpl->assign('imgdir', $xoopsModuleConfig['sbimgdir']);
        $xoopsTpl->assign('uploaddir', $xoopsModuleConfig['sbuploaddir']);

        $countCols = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . '');
        $numCols = $xoopsDB->getRowsNum($countCols);
        if (0 == $numCols) {
            $xoopsTpl->assign('lang_nothing', _MD_eNoticias_NOTHING);
        }

        $resultA = $xoopsDB->query('SELECT columnID, name, description, author, colimage FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ORDER BY weight');

        $sqlA = 'SELECT columnID, name, description, author, colimage FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ORDER BY weight';
        $resultA = $xoopsDB->query($sqlA, $xoopsModuleConfig['colsperindex'], $start);

        while (list($columnID, $name, $description, $author, $colimage) = $xoopsDB->fetchRow($resultA)) {
            if ($gpermHandler->checkRight('Column Permissions', $columnID, $groups, $module_id)) {
                $columna['columnID'] = $columnID;

                $columna['name'] = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);

                $columna['description'] = $myts->xoopsCodeDecode($description, $allowimage = 0);

                $columna['description'] = cleanTags($columna['description']);

                $columna['colimage'] = $colimage;

                $resultB = $xoopsDB->query('SELECT name, uname FROM ' . $xoopsDB->prefix('users') . " WHERE uid = $author");

                [$authorname, $username] = $xoopsDB->fetchRow($resultB);

                if (empty($authorname)) {
                    $columna['authorname'] = ucfirst($username);
                } else {
                    $columna['authorname'] = ucfirst(htmlspecialchars($authorname, ENT_QUOTES | ENT_HTML5));
                }

                $resultC = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE columnID = ' . $columna['columnID'] . " AND submit ='0' AND offline = 0");

                $columna['totalarts'] = $xoopsDB->getRowsNum($resultC);

                $resultD = $xoopsDB->query('SELECT articleID, headline, teaser, datesub FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE columnID = ' . $columna['columnID'] . ' AND offline = 0 AND submit = 0 ORDER BY datesub DESC', 1, 0);

                while (false !== ($myrow = $xoopsDB->fetchArray($resultD))) {
                    // Functional links

                    if ($xoopsUser) {
                        if ($xoopsUser->isAdmin()) {
                            $myrow['adminlinks'] = '<a href="admin/article.php?op=mod&articleID='
                                                   . $myrow['articleID']
                                                   . '" target="_blank"><img src="images/links/edit.gif" border="0" alt="'
                                                   . _MD_eNoticias_EDITART
                                                   . '" width="15" height="11"></a>&nbsp;<a href="admin/article.php?op=del&articleID='
                                                   . $myrow['articleID']
                                                   . '" target="_blank"><img src="images/links/delete.gif" border="0" alt="'
                                                   . _MD_eNoticias_DELART
                                                   . '" width="15" height="11"></a>&nbsp;';

                            $myrow['userlinks'] = '<a href="print.php?articleID='
                                                   . $myrow['articleID']
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
                                                   . $myrow['articleID']
                                                   . ' " target="_blank"><img src="images/links/friend.gif" border="0" alt="'
                                                   . _MD_eNoticias_SENDTOFRIEND
                                                   . '" width="15" height="11"></a>&nbsp;';
                        } else {
                            $myrow['adminlinks'] = '';

                            $myrow['userlinks'] = '<a href="print.php?articleID='
                                                   . $myrow['articleID']
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
                                                   . $myrow['articleID']
                                                   . ' " target="_blank"><img src="images/links/friend.gif" border="0" alt="'
                                                   . _MD_eNoticias_SENDTOFRIEND
                                                   . '" width="15" height="11"></a>&nbsp;';
                        }
                    } else {
                        $myrow['adminlinks'] = '';

                        $myrow['userlinks'] = '<a href="print.php?articleID='
                                               . $myrow['articleID']
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
                                               . $myrow['articleID']
                                               . ' " target="_blank"><img src="images/links/friend.gif" border="0" alt="'
                                               . _MD_eNoticias_SENDTOFRIEND
                                               . '" width="15" height="11"></a>&nbsp;';
                    }

                    $tempteaser = cleanTags($myrow['teaser']);

                    $columna['content'][] = [
                        'articleID' => $myrow['articleID'],
                        'headline' => htmlspecialchars($myrow['headline'], ENT_QUOTES | ENT_HTML5),
                        'teaser' => htmlspecialchars($tempteaser, ENT_QUOTES | ENT_HTML5),
                        'datesub' => formatTimestamp($myrow['datesub'], $xoopsModuleConfig['dateformat']),
                        'adminlinks' => $myrow['adminlinks'],
                        'userlinks' => $myrow['userlinks'],
                    ];
                }

                $columna['total'] = $totalcols;

                $pagenav = new XoopsPageNav($columna['total'], $xoopsModuleConfig['colsperindex'], $start, 'start', '');

                $columna['navbar'] = '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

                $xoopsTpl->append_by_ref('cols', $columna);

                unset($columna);
            }
        }
}

$xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="style.css">');

require XOOPS_ROOT_PATH . '/footer.php';
