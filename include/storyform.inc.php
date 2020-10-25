<?php

/**
 * $Id: storyform.inc.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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
global $_POST;

require XOOPS_ROOT_PATH . '/class/xoopstree.php';
require XOOPS_ROOT_PATH . '/class/xoopslists.php';
require XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

$mytree = new XoopsTree($xoopsDB->prefix('eNoticias_Colunas'), 'columnID', '0');
$sform = new XoopsThemeForm(_MD_eNoticias_SUB_SMNAME, 'storyform', xoops_getenv('PHP_SELF'));

if (!empty($xoopsUser)) {
    if ($xoopsUser->isAdmin($xoopsModule->getVar('mid'))) {
        ob_start();

        $sform->addElement(new XoopsFormHidden('columnID', $columnID));

        $mytree->makeMySelBox('name', 'name', $columnID);

        $sform->addElement(new XoopsFormLabel(_MD_eNoticias_COLUMN, ob_get_contents()));

        ob_end_clean();
    } else {
        $user = $xoopsUser->getVar('uid');

        $query = $xoopsDB->query('SELECT name, columnID FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE author = $user ");

        [$name, $columnID] = $xoopsDB->fetchRow($query);

        $sform->addElement(new XoopsFormHidden('columnID', $columnID));

        $sform->addElement(new XoopsFormLabel(_MD_eNoticias_COLUMN, $name));
    }
}

// This part is common to edit/add
$sform->addElement(new XoopsFormText(_MD_eNoticias_ARTHEADLINE, 'headline', 50, 80, $headline), true);
$sform->addElement(new XoopsFormTextArea(_MD_eNoticias_ARTLEAD, 'lead', $lead, 5, 60));

// Teaser
$sform->addElement(new XoopsFormTextArea(_MD_eNoticias_ARTTEASER, 'teaser', $teaser, 5, 60));
$autoteaser_radio = new XoopsFormRadioYN(_MD_eNoticias_AUTOTEASER, 'autoteaser', 0, ' ' . _MD_eNoticias_YES . '', ' ' . _MD_eNoticias_NO . '');
$sform->addElement($autoteaser_radio);
$sform->addElement(new XoopsFormText(_MD_eNoticias_AUTOTEASERAMOUNT, 'teaseramount', 4, 4, 100));

$sform->addElement(new XoopsFormDhtmlTextArea(_MD_eNoticias_ARTBODY, 'bodytext', $bodytext, 15, 60));

// The article CAN have its own image :)
// First, if the article's image doesn't exist, set its value to the blank file
if (!file_exists(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $artimage) || !$artimage) {
    $artimage = 'blank.png';
}
// Code to create the image selector
$graph_array     = XoopsLists:: getImgListAsArray(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir']);
$artimage_select = new XoopsFormSelect('', 'artimage', $artimage);
$artimage_select->addOptionArray($graph_array);
$artimage_select->setExtra("onchange='showImgSelected(\"image5\", \"artimage\", \"" . $xoopsModuleConfig['sbuploaddir'] . '", "", "' . XOOPS_URL . "\")'");
$artimage_tray = new XoopsFormElementTray(_MD_eNoticias_SELECT_IMG, '&nbsp;');
$artimage_tray->addElement($artimage_select);
$artimage_tray->addElement(new XoopsFormLabel('', "<br><br><img src='" . XOOPS_URL . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $artimage . "' name='image5' id='image5' alt=''>"));
$sform->addElement($artimage_tray);

if (is_object($xoopsUser)) {
    $notify_checkbox = new XoopsFormCheckBox('', 'notifypub', $notifypub);

    $notify_checkbox->addOption(1, _MD_eNoticias_NOTIFY);

    $sform->addElement($notify_checkbox);
}

// Code to allow comments
$addcomments_radio = new XoopsFormRadioYN(_MD_eNoticias_ALLOWCOMMENTS, 'comments', $comments, ' ' . _MD_eNoticias_YES . '', ' ' . _MD_eNoticias_NO . '');
$sform->addElement($addcomments_radio);

$button_tray = new XoopsFormElementTray('', '');
$hidden = new XoopsFormHidden('op', 'post');
$button_tray->addElement($hidden);
$button_tray->addElement(new XoopsFormButton('', 'post', _MD_eNoticias_CREATE, 'submit'));

$sform->addElement($button_tray);
$sform->display();
unset($hidden);
