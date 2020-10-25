<?php

/**
 * $Id: submit.php v 1.00 2004/12/30 Gustavo S. Villa Exp
 * //  ------------------------------------------------------------------------ //
 * //                            e-Notícias                                     //
 * //                              E-WARE                                       //
 * //                   <http://www.e-ware.com.br>                             //
 * //  ------------------------------------------------------------------------ //
 * //  Você não pode substituir ou alterar qualquer parte desses comentários    //
 * //  ou créditos dos titulares e autores os quais são considerados direitos   //
 * //  reservados.                                                              //
 * //  ------------------------------------------------------------------------ //
 * //  Autor: Gustavo S. Villa  <guvilladev@e-ware.com.br>                      //
 * //  ------------------------------------------------------------------------ */
include '../../mainfile.php';
require XOOPS_ROOT_PATH . '/header.php';

global $xoopsUser, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;

$result = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . '');
if (0 == $xoopsDB->getRowsNum($result)) {
    redirect_header('index.php', 1, _AM_eNoticias_NOCOLEXISTS);

    exit();
}

if (!is_object($xoopsUser) || 0 == $xoopsModuleConfig['allowsubmit']) {
    redirect_header('index.php', 1, _NOPERM);

    exit();
}

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

$op = 'form';

if (isset($_POST['post'])) {
    $op = trim('post');
} elseif (isset($_POST['edit'])) {
    $op = trim('edit');
}

switch ($op) {
    case 'post':

        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/functions.php';

        $myts = MyTextSanitizer:: getInstance();
        global $xoopsUser, $xoopsUxer, $xoopsConfig, $xoopsModule, $xoopsModuleConfig, $myts, $xoopsDB;

        $html = 1;
        if ($xoopsUser) {
            $uid = $xoopsUser->getVar('uid');

            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                $html = empty($html) ? 0 : 1;
            }
        } else {
            if (1 == $xoopsModuleConfig['anonpost']) {
                $uid = 0;
            } else {
                redirect_header('index.php', 3, _NOPERM);

                exit();
            }
        }

        $block = isset($block) ? (int)$block : 1;
        $smiley = isset($smiley) ? (int)$smiley : 1;
        $xcodes = isset($xcodes) ? (int)$xcodes : 1;
        $breaks = isset($breaks) ? (int)$breaks : 1;
        $comments = isset($comments) ? (int)$comments : 1;

        $columnID = $myts->addSlashes($_POST['columnID']);
        $headline = htmlspecialchars($_POST['headline'], ENT_QUOTES | ENT_HTML5);
        $lead = $myts->addSlashes($_POST['lead']);
        $bodytext = $myts->addSlashes($_POST['bodytext']);
        $artimage = ('blank.png' != $_POST['artimage']) ? $myts->addSlashes($_POST['artimage']) : '';
        $datesub = time();

        $submit = 1;
        $offline = 1;

        if ($_POST['autoteaser']) {
            $charlength = $_POST['teaseramount'];

            $teasertemp = $_POST['bodytext'];

            // includes code by toshimitsu

            if (mb_strlen($teasertemp) <= $charlength) {
                $teaser = $teasertemp;
            }

            if (XOOPS_USE_MULTIBYTES == 1 && function_exists('mb_internal_encoding') && @mb_internal_encoding(_CHARSET)) {
                $teaser = mb_strcut($teasertemp, 0, $charlength - 3) . '...';
            } else {
                $teaser = mb_substr($teasertemp, 0, $charlength - 3) . '...';
            }
        } else {
            $teaser = $myts->addSlashes($_POST['teaser']);
        }

        if (1 == $xoopsModuleConfig['autoapprove']) {
            $submit = 0;

            $offline = 0;
        }

        if ('blank.png' != $_POST['artimage']) {
            $artimage = $myts->addSlashes($_POST['artimage']);
        } else {
            $artimage = '';
        }

        $result = $xoopsDB->query(
            'INSERT INTO '
            . $xoopsDB->prefix('eNoticias_Artigos')
            . " (articleID, columnID, headline, lead, bodytext, teaser, submit, datesub, html, smiley, xcodes, breaks, artimage, comments, offline, notifypub ) VALUES ('', '$columnID', '$headline', '$lead', '$bodytext', '$teaser', '$submit', '$datesub', '$html', '$smiley', '$xcodes', '$breaks', '$artimage', '$comments', '$offline', '$notifypub')"
        );
        $articleID = $xoopsDB->getInsertId();

        if ($result) {
            // Notify of new link (anywhere) and new link in category

            $notificationHandler = xoops_getHandler('notification');

            $tags = [];

            $tags['ART_NAME'] = $headline;

            $tags['ART_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/article.php?art=' . $articleID;

            $sql = 'SELECT name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' WHERE columnID=' . $columnID;

            $result = $xoopsDB->query($sql);

            $row = $xoopsDB->fetchArray($result);

            $tags['COLUMN_NAME'] = $row['name'];

            $tags['COLUMN_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php?op=col&columnID=' . $columnID;

            if (1 == $xoopsModuleConfig['autoapprove']) {
                $notificationHandler->triggerEvent('global', 0, 'new_article', $tags);

                $notificationHandler->triggerEvent('column', $columnID, 'new_article', $tags);

                redirect_header('index.php', 2, _MD_eNoticias_RECEIVEDANDAPPROVED);
            } else {
                $tags['WAITINGFILES_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/admin/submissions.php?op=col';

                redirect_header('index.php', 2, _MD_eNoticias_RECEIVED);
            }
        } else {
            redirect_header('submit.php', 2, _MD_eNoticias_ERRORSAVINGDB);
        }
        exit();
        break;
    case 'form':
    default:

        global $xoopsUser, $HTTP_SERVER_VARS;
        $name = ucfirst($xoopsUser->getVar('uname'));

        echo '<table id="mod_header"><tr><td width="100%"><span class="h18px">';
        echo '<a href=' . XOOPS_URL . '>' . _MD_eNoticias_HOME . '</a> > <a href=' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . '/index.php>' . ucfirst($xoopsModule->name()) . '</a> > ' . _MD_eNoticias_SUBMITART . '</span></td>';
        echo '<td width="100"><span class="h18right">' . $xoopsModule->name() . '</span></td></tr></table>';

        echo "<div style='margin: 8px 0; line-height: 160%;'>" . _MD_eNoticias_GOODDAY . "<b>$name</b>, " . _MD_eNoticias_SUB_SNEWNAMEDESC . '</div>';

        $block = 0;
        $html = 0;
        $smiley = 0;
        $xcodes = 0;
        $headline = '';
        $lead = '';
        $bodytext = '';
        $teaser = '';
        $weight = 1;
        $breaks = 1;
        $artimage = 'blank.png';
        $comments = 1;
        $offline = 0;
        $columnID = 0;
        $notifypub = 1;

        $title = '';
        $votes = 0;
        $rating = 0.00;

        require_once __DIR__ . '/include/storyform.inc.php';

        $xoopsTpl->assign('xoops_module_header', '<link rel="stylesheet" type="text/css" href="style.css">');
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
}
