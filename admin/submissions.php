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

// ---------- General Stuff ---------- //
include 'admin_header.php';
$myts = MyTextSanitizer::getInstance();
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
    /**
     * Clear all variables before we start
     */

    $block = 0;

    $html = 0;

    $smiley = 0;

    $xcodes = 0;

    $headline = '';

    $lead = '';

    $bodytext = '';

    $teaser = '';

    $groupid = '';

    $weight = 1;

    $breaks = 1;

    $artimage = 'blank.png';

    $htmlfile = '';

    $comments = 1;

    $offline = 0;

    $columnID = 0;

    $title = '';

    $votes = 0;

    $rating = 0.00;

    global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $myts;

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    // Since this is a submission, the id exists, so retrieve data: we're editing an article

    $result = $xoopsDB->query('SELECT columnID, headline, lead, bodytext, teaser, weight, html, smiley, xcodes, breaks, block, artimage, votes, rating, comments, offline FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = '$articleID'");

    [$columnID, $headline, $lead, $bodytext, $teaser, $weight, $html, $smiley, $xcodes, $breaks, $block, $artimage, $votes, $rating, $comments, $offline] = $xoopsDB->fetchRow($result);

    $query = $xoopsDB->query('SELECT name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = $columnID ");

    [$name] = $xoopsDB->fetchRow($query);

    if (0 == $xoopsDB->getRowsNum($result)) {
        redirect_header('index.php', 1, _AM_eNoticias_NOARTTOEDIT);

        exit();
    }

    // Module menu

    adminMenu(3, _AM_eNoticias_SUBMITS . " > '" . $headline . "'");

    echo "<h3 style='color: #2F5376; '>" . _AM_eNoticias_SUBMITSMNGMT . '</h3>';

    $sform = new XoopsThemeForm(_AM_eNoticias_AUTHART . ": $headline", 'op', xoops_getenv('PHP_SELF'));

    $sform->setExtra('enctype="multipart/form-data"');

    $sform->addElement(new XoopsFormLabel(_AM_eNoticias_COLNAME, $name));

    $headline = htmlspecialchars(stripslashes($headline), ENT_QUOTES | ENT_HTML5);

    $sform->addElement(new XoopsFormText(_AM_eNoticias_ARTHEADLINE, 'headline', 50, 80, $headline), true);

    $sform->addElement(new XoopsFormTextArea(_AM_eNoticias_ARTLEAD, 'lead', $lead, 5, 60));

    $sform->addElement(new XoopsFormTextArea(_AM_eNoticias_ARTTEASER, 'teaser', $teaser, 5, 60));

    $autoteaser_radio = new XoopsFormRadioYN(_AM_eNoticias_AUTOTEASER, 'autoteaser', 0, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($autoteaser_radio);

    $sform->addElement(new XoopsFormText(_AM_eNoticias_AUTOTEASERAMOUNT, 'teaseramount', 4, 4, 100));

    $sform->addElement(new XoopsFormDhtmlTextArea(_AM_eNoticias_ARTBODY, 'bodytext', $bodytext, 15, 60));

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

    // Code to allow comments

    $addcomments_radio = new XoopsFormRadioYN(_AM_eNoticias_ALLOWCOMMENTS, 'comments', $comments, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($addcomments_radio);

    // Code to take article offline, for maintenance purposes

    $offline_radio = new XoopsFormRadioYN(_AM_eNoticias_SWITCHOFFLINE, 'offline', $offline, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($offline_radio);

    // Code to put article in block

    $block_radio = new XoopsFormRadioYN(_AM_eNoticias_BLOCK, 'block', $block, ' ' . _AM_eNoticias_YES . '', ' ' . _AM_eNoticias_NO . '');

    $sform->addElement($block_radio);

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

    $sform->addElement(new XoopsFormHidden('columnID', $columnID));

    $button_tray = new XoopsFormElementTray('', '');

    $hidden = new XoopsFormHidden('op', 'authart');

    $button_tray->addElement($hidden);

    $butt_save = new XoopsFormButton('', '', _AM_eNoticias_AUTHORIZE, 'submit');

    $butt_save->setExtra('onclick="this.form.elements.op.value=\'authart\'"');

    $button_tray->addElement($butt_save);

    $butt_cancel = new XoopsFormButton('', '', _AM_eNoticias_CANCEL, 'button');

    $butt_cancel->setExtra('onclick="history.go(-1)"');

    $button_tray->addElement($butt_cancel);

    $sform->addElement($button_tray);

    $sform->display();

    unset($hidden);
}

/* -- Available operations -- */
switch ($op) {
    case 'mod':
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        xoops_cp_header();
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $modify, $myts;
        $articleID = $_POST['articleID'] ?? $articleID;
        editarticle($articleID);
        showSubmissions();
        break;
    case 'authart':
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $myts;

        $articleID = $_POST['articleID'] ?? 0;
        $columnID = $_POST['columnID'] ?? 0;
        $block = (isset($_POST['block'])) ? 1 : 0;
        $offline = $_POST['offline'] ?? 0;
        $comments = (isset($_POST['comments'])) ? 1 : 0;
        $breaks = (isset($_POST['breaks'])) ? 1 : 0;
        $html = (isset($_POST['html'])) ? 1 : 0;
        $smiley = (isset($_POST['smiley'])) ? 1 : 0;
        $xcodes = (isset($_POST['xcodes'])) ? 1 : 0;
        $weight = (isset($_POST['weight']) && is_numeric($_POST['weight'])) ? $myts->addSlashes($_POST['weight']) : 1;
        $artimage = ('blank.png' != $_POST['artimage']) ? $myts->addSlashes($_POST['artimage']) : '';
        $headline = $myts->addSlashes($_POST['headline']);
        $headline = str_replace('"', '&quot;', $headline);
        $lead = $myts->addSlashes($_POST['lead']);
        $bodytext = $myts->addSlashes($_POST['bodytext']);

        $submit = 0;
        $offline = 0;
        $date = time();

        if ($xoopsDB->query(
            'UPDATE '
            . $xoopsDB->prefix('eNoticias_Artigos')
            . " SET headline = '$headline', columnID = '$columnID', lead = '$lead', bodytext = '$bodytext', teaser = '$teaser', weight = '$weight', html = '$html', smiley = '$smiley', xcodes = '$xcodes', breaks = '$breaks', block = '$block', artimage = '$artimage', offline = '$offline', submit = '$submit' WHERE articleID = '$articleID'"
        )) {
            redirect_header('index.php', 1, _AM_eNoticias_ARTAUTHORIZED);
        } else {
            redirect_header('index.php', 1, _AM_eNoticias_ARTNOTUPDATED);
        }
        exit();
        break;
    case 'del':
        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB;

        $confirm = (isset($confirm)) ? 1 : 0;

        if ($confirm) {
            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = $articleID");

            redirect_header('index.php', 1, sprintf(_AM_eNoticias_ARTISDELETED, $headline));

            exit();
        }
            $articleID = $_POST['articleID'] ?? $articleID;
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
        adminMenu(3, _AM_eNoticias_SUBMITS);
        echo '<br>';
        showSubmissions();
        exit();
        break;
}
xoops_cp_footer();
