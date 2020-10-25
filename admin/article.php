<?php

// $Id: v 1.00 2004/12/30 Gustavo S. Villa Exp
//  ------------------------------------------------------------------------ //
//                            e-Notícias                                        //
//                              E-WARE                                       //
//                   <http://www.e-ware.com.br>                             //
//  ------------------------------------------------------------------------ //
//  Você não pode substituir ou alterar qualquer parte desses comentários    //
//  ou créditos dos titulares e autores os quais são considerados direitos   //
//  reservados.                                                              //
//  ------------------------------------------------------------------------ //
//  Autor: Gustavo S. Villa  <guvilladev@e-ware.com.br>                      //
//  ------------------------------------------------------------------------ //

// -- General Stuff -- //
include 'admin_header.php';

$op = '';

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

if (isset($_GET['op'])) {
    $op = $_GET['op'];
}
if (isset($_POST['op'])) {
    $op = $_POST['op'];
}

// -- Edit function -- //
function editarticle($articleID = '')
{
    global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $XOOPS_URL;

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    /**
     * Clear all variables before we start
     */

    if (!isset($block)) {
        $block = 1;
    }

    if (!isset($html)) {
        $html = 1;
    } else {
        $html = (int)$html;
    }

    if (!isset($smiley)) {
        $smiley = 1;
    } else {
        $smiley = (int)$smiley;
    }

    if (!isset($xcodes)) {
        $xcodes = 1;
    } else {
        $xcodes = (int)$xcodes;
    }

    if (!isset($breaks)) {
        $breaks = 1;
    } else {
        $breaks = (int)$breaks;
    }

    if (!isset($weight)) {
        $weight = 1;
    }

    if (!isset($comments)) {
        $comments = 1;
    }

    if (!isset($offline)) {
        $offline = 0;
    }

    if (!isset($votes)) {
        $votes = 0;
    }

    if (!isset($rating)) {
        $rating = 0.00;
    }

    if (!isset($columnID)) {
        $columnID = 1;
    }

    if (!isset($headline)) {
        $headline = '';
    }

    if (!isset($lead)) {
        $lead = '';
    }

    if (!isset($bodytext)) {
        $bodytext = '';
    }

    if (!isset($teaser)) {
        $teaser = '';
    }

    if (!isset($title)) {
        $title = '';
    }

    $artimage = 'blank.png';

    // If there is a parameter, and the id exists, retrieve data: we're editing an article

    if ($articleID) {
        $result = $xoopsDB->query('SELECT columnID, headline, lead, bodytext, teaser, weight, html, smiley, xcodes, breaks, block, artimage, votes, rating, comments, offline FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = '$articleID'");

        [$columnID, $headline, $lead, $bodytext, $teaser, $weight, $html, $smiley, $xcodes, $breaks, $block, $artimage, $votes, $rating, $comments, $offline] = $xoopsDB->fetchRow($result);

        if (!$xoopsDB->getRowsNum($result)) {
            redirect_header('index.php', 1, _AM_eNoticias_NOARTTOEDIT);

            exit();
        }

        adminMenu(2, _AM_eNoticias_enot . _AM_eNoticias_EDITING . $headline . "'");

        echo "<h3 style='color: #2F5376; '>" . _AM_eNoticias_ADMINARTMNGMT . '</h5>';

        $sform = new XoopsThemeForm(_AM_eNoticias_MODART . ": $headline", 'op', xoops_getenv('PHP_SELF'));
    } else { // there's no parameter, so we're adding an article
        $result01 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ');

        [$totalcolumns] = $xoopsDB->fetchRow($result01);

        if (0 == $totalcolumns) {
            redirect_header('index.php', 1, _AM_eNoticias_NEEDONECOLUMN);

            exit();
        }

        adminMenu(2, _AM_eNoticias_enot . _AM_eNoticias_CREATINGART);

        echo "<h3 style='color: #2F5376; '>" . _AM_eNoticias_ADMINARTMNGMT . '</h5>';

        $sform = new XoopsThemeForm(_AM_eNoticias_NEWART, 'op', xoops_getenv('PHP_SELF'));
    }

    $sform->setExtra('enctype="multipart/form-data"');

    // COLUMN

    /*
    * Get information for pulldown menu using XoopsTree.
    * First var is the database table
    * Second var is the unique field ID for the categories
    * Last one is not set as we do not have sub menus in WF-FAQ
    */

    $mytree = new XoopsTree($xoopsDB->prefix('eNoticias_Colunas'), 'columnID', '0');

    ob_start();

    $sform->addElement(new XoopsFormHidden('columnID', $columnID));

    $mytree->makeMySelBox('name', 'name', $columnID);

    $sform->addElement(new XoopsFormLabel(_AM_eNoticias_COLNAME, ob_get_contents()));

    ob_end_clean();

    // HEADLINE, LEAD, BODYTEXT

    // This part is common to edit/add

    $sform->addElement(new XoopsFormText(_AM_eNoticias_ARTHEADLINE, 'headline', 50, 80, $headline), true);

    $sform->addElement(new XoopsFormTextArea(_AM_eNoticias_ARTLEAD, 'lead', $lead, 5, 60));

    // TEASER

    $sform->addElement(new XoopsFormTextArea(_AM_eNoticias_ARTTEASER, 'teaser', $teaser, 5, 60));

    $autoteaser_radio = new XoopsFormRadioYN(_AM_eNoticias_AUTOTEASER, 'autoteaser', 0, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($autoteaser_radio);

    $sform->addElement(new XoopsFormText(_AM_eNoticias_AUTOTEASERAMOUNT, 'teaseramount', 4, 4, 100));

    // BODY

    $sform->addElement(new XoopsFormDhtmlTextArea(_AM_eNoticias_ARTBODY, 'bodytext', $bodytext, 15, 60));

    // IMAGE

    // The article CAN have its own image :)

    // First, if the article's image doesn't exist, set its value to the blank file

    if (!file_exists(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $artimage) || !$artimage) {
        $artimage = 'blank.png';
    }

    // Code to create the image selector

    $graph_array = XoopsLists:: getImgListAsArray(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir']);

    $artimage_select = new XoopsFormSelect('', 'artimage', $artimage);

    $artimage_select->addOptionArray($graph_array);

    $artimage_select->setExtra("onchange='showImgSelected(\"image5\", \"artimage\", \"" . $xoopsModuleConfig['sbuploaddir'] . '", "", "' . XOOPS_URL . "\")'");

    $artimage_tray = new XoopsFormElementTray(_AM_eNoticias_SELECT_IMG, '&nbsp;');

    $artimage_tray->addElement($artimage_select);

    $artimage_tray->addElement(new XoopsFormLabel('', "<br><br><img src='" . XOOPS_URL . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $artimage . "' name='image5' id='image5' alt=''>"));

    $sform->addElement($artimage_tray);

    // Code to call the file browser to select an image to upload

    $sform->addElement(new XoopsFormFile(_AM_eNoticias_UPLOADIMAGE, 'cimage', $xoopsModuleConfig['maxfilesize']), false);

    // COMMENTS

    // Code to allow comments

    $addcomments_radio = new XoopsFormRadioYN(_AM_eNoticias_ALLOWCOMMENTS, 'comments', $comments, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($addcomments_radio);

    // OFFLINE

    // Code to take article offline, for maintenance purposes

    $offline_radio = new XoopsFormRadioYN(_AM_eNoticias_SWITCHOFFLINE, 'offline', $offline, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($offline_radio);

    // ARTICLE IN BLOCK

    // Code to put article in block

    $block_radio = new XoopsFormRadioYN(_AM_eNoticias_BLOCK, 'block', $block, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($block_radio);

    // VARIOUS OPTIONS

    $options_tray = new XoopsFormElementTray(_AM_eNoticias_OPTIONS, '<br>');

    $html_checkbox = new XoopsFormCheckBox('', 'html', $html);

    $html_checkbox->addOption(1, _AM_eNoticias_DOHTML);

    $options_tray->addElement($html_checkbox);

    $smiley_checkbox = new XoopsFormCheckBox('', 'smiley', $smiley);

    $smiley_checkbox->addOption(1, _AM_eNoticias_DOSMILEY);

    $options_tray->addElement($smiley_checkbox);

    $xcodes_checkbox = new XoopsFormCheckBox('', 'xcodes', $xcodes);

    $xcodes_checkbox->addOption(1, _AM_eNoticias_DOXCODE);

    $options_tray->addElement($xcodes_checkbox);

    $breaks_checkbox = new XoopsFormCheckBox('', 'breaks', $breaks);

    $breaks_checkbox->addOption(1, _AM_eNoticias_BREAKS);

    $options_tray->addElement($breaks_checkbox);

    $sform->addElement($options_tray);

    $sform->addElement(new XoopsFormHidden('articleID', $articleID));

    $button_tray = new XoopsFormElementTray('', '');

    $hidden = new XoopsFormHidden('op', 'addart');

    $button_tray->addElement($hidden);

    if (!$articleID) { // there's no articleID? Then it's a new article
        $butt_create = new XoopsFormButton('', '', _AM_eNoticias_CREATE, 'submit');

        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addart\'"');

        $button_tray->addElement($butt_create);

        $butt_clear = new XoopsFormButton('', '', _AM_eNoticias_CLEAR, 'reset');

        $button_tray->addElement($butt_clear);

        $butt_cancel = new XoopsFormButton('', '', _AM_eNoticias_CANCEL, 'button');

        $butt_cancel->setExtra('onclick="history.go(-1)"');

        $button_tray->addElement($butt_cancel);
    } else { // else, we're editing an existing article
        $butt_create = new XoopsFormButton('', '', _AM_eNoticias_MODIFY, 'submit');

        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addart\'"');

        $button_tray->addElement($butt_create);

        $butt_cancel = new XoopsFormButton('', '', _AM_eNoticias_CANCEL, 'button');

        $butt_cancel->setExtra('onclick="history.go(-1)"');

        $button_tray->addElement($butt_cancel);
    }

    $sform->addElement($button_tray);

    $sform->display();

    unset($hidden);
}

/* -- Available operations -- */
switch ($op) {
    case 'mod':
        xoops_cp_header();
        $articleID = isset($_POST['articleID']) ? (int)$_POST['articleID'] : (int)$_GET['articleID'];
        editarticle($articleID);
        break;
    case 'addart':
        //		global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig	;

        $articleID = (isset($_POST['articleID'])) ? (int)$_POST['articleID'] : 0;
        $columnID = (isset($_POST['columnID'])) ? (int)$_POST['columnID'] : 0;
        $block = (isset($_POST['block'])) ? (int)$_POST['block'] : 1;
        $offline = (isset($_POST['offline'])) ? (int)$_POST['offline'] : 0;
        $breaks = (isset($_POST['breaks'])) ? (int)$_POST['breaks'] : 0;

        $comments = isset($comments) ? (int)$comments : 0;
        $html = isset($html) ? (int)$html : 0;
        $smiley = isset($smiley) ? (int)$smiley : 0;
        $xcodes = isset($xcodes) ? (int)$xcodes : 0;

        $weight = (isset($_POST['weight'])) ? (int)$_POST['weight'] : 1;
        $artimage = ('blank.png' != $_POST['artimage']) ? $myts->addSlashes($_POST['artimage']) : '';
        $headline = htmlspecialchars($_POST['headline'], ENT_QUOTES | ENT_HTML5);
        $lead = $myts->addSlashes($_POST['lead']);
        $bodytext = $myts->addSlashes($_POST['bodytext']);

        $votes = (isset($_POST['votes'])) ? (int)$_POST['votes'] : 0;
        $rating = (isset($_POST['rating'])) ? (int)$_POST['rating'] : 0.00;

        if ($_POST['autoteaser']) {
            $charlength = $_POST['teaseramount'];

            $teasertemp = $_POST['bodytext'];

            if (!XOOPS_USE_MULTIBYTES) {
                $teasertemp = strip_tags($teasertemp, '');

                $teasertemp = $myts->addSlashes(mb_substr($teasertemp, 0, ($charlength - 1))) . '...';
            }

            $teaser = $teasertemp;
        } else {
            $teaser = $myts->addSlashes($_POST['teaser']);
        }

        $date = time();

        // ARTICLE IMAGE
        // Define variables
        $error = 0;
        $word = null;
        $uid = $xoopsUser->uid();
        $submit = 1;
        $date = time();

        if ('' != $HTTP_POST_FILES['cimage']['name']) {
            require_once XOOPS_ROOT_PATH . '/class/uploader.php';

            if (file_exists(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $HTTP_POST_FILES['cimage']['name'])) {
                redirect_header('index.php', 1, _AM_eNoticias_FILEEXISTS);
            }

            $allowed_mimetypes = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'];

            uploading($allowed_mimetypes, $HTTP_POST_FILES['cimage']['name'], 'index.php', 0, $xoopsModuleConfig['sbuploaddir']);

            $artimage = $HTTP_POST_FILES['cimage']['name'];
        } elseif ('blank.png' != $_POST['artimage']) {
            $artimage = $myts->addSlashes($_POST['artimage']);
        } else {
            $artimage = '';
        }

        // Save to database
        if (!$articleID) {
            if ($xoopsDB->query(
                'INSERT INTO '
                . $xoopsDB->prefix('eNoticias_Artigos')
                . " (articleID, columnID, headline, lead, bodytext, teaser, weight, html, smiley, xcodes, breaks, block, artimage, votes, rating, comments, offline, datesub ) VALUES ('', '$columnID', '$headline', '$lead', '$bodytext', '$teaser', '$weight', '$html', '$smiley', '$xcodes', '$breaks', '$block', '$artimage', '$votes', '$rating', '$comments', '$offline', '$date' )"
            )) {
                $this->db = Database:: getInstance();

                $newid = $GLOBALS['xoopsDB']->getInsertId();

                $result = $xoopsDB->query('SELECT columnID FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = '$newid'");

                [$columnID] = $xoopsDB->fetchRow($result);

                $result = $xoopsDB->query('SELECT name, total FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE colid = '$columnID'");

                [$name, $total] = $xoopsDB->fetchRow($result);

                $total++;

                $tags = [];

                $tags['ARTICLE_NAME'] = $headline;

                $tags['ARTICLE_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php?art&amp;lid=' . $newid;

                $tags['COLUMN_NAME'] = $name;

                $tags['COLUMN_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php?op=col&columnID=' . $columnID;

                $notificationHandler = xoops_getHandler('notification');

                $notificationHandler->triggerEvent('global', 0, 'new_article', $tags);

                $notificationHandler->triggerEvent('category', $columnID, 'new_article', $tags);

                if ($xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('eNoticias_Colunas') . " SET total = '$total' WHERE columnID = '$columnID'")) {
                    redirect_header('index.php', 1, _AM_eNoticias_ARTCREATEDOK);
                }
            } else {
                redirect_header('index.php', 1, _AM_eNoticias_ARTNOTCREATED);
            }
        } else {  // That is, $articleID exists, thus we're editing an article
            if ($xoopsDB->query(
                'UPDATE '
                . $xoopsDB->prefix('eNoticias_Artigos')
                . " SET columnID = '$columnID', headline = '$headline', lead = '$lead', bodytext = '$bodytext', teaser = '$teaser', weight = '$weight', html = '$html', smiley = '$smiley', xcodes = '$xcodes', breaks = '$breaks', block = '$block', artimage = '$artimage', votes = '$votes', rating = '$rating', comments = '$comments', offline = '$offline', datesub =  '$date' WHERE articleID = '$articleID'"
            )) {
                redirect_header('index.php', 1, _AM_eNoticias_ARTMODIFIED);
            } else {
                redirect_header('index.php', 1, _AM_eNoticias_ARTNOTUPDATED);
            }
        }
        exit();
        break;
    case 'del':
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB;

        $confirm = (isset($confirm)) ? 1 : 0;

        if ($confirm) {
            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = $articleID");

            xoops_comment_delete($xoopsModule->getVar('mid'), $articleID);

            redirect_header('index.php', 1, sprintf(_AM_eNoticias_ARTISDELETED, $headline));

            exit();
        }
            $articleID = (isset($_POST['articleID'])) ? (int)$_POST['articleID'] : (int)$articleID;
            $result = $xoopsDB->query('SELECT articleID, headline FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = $articleID");
            [$articleID, $headline] = $xoopsDB->fetchRow($result);
            xoops_cp_header();
            xoops_confirm(['op' => 'del', 'articleID' => $articleID, 'confirm' => 1, 'headline' => $headline], 'article.php', _AM_eNoticias_DELETETHISARTICLE . '<br><br>' . $headline, _AM_eNoticias_DELETE);
            xoops_cp_footer();

        exit();
        break;
    case 'default':
    default:
        xoops_cp_header();
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule;
        $myts = MyTextSanitizer::getInstance();
        editarticle();
        showArticles(0);
        break;
}
xoops_cp_footer();
