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

function editcol($columnID = '')
{
    $weight = 1;

    $name = '';

    $author = '';

    $description = '';

    $colimage = 'blank.png';

    global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $xoopsModuleConfig, $xoopsModule, $_GET;

    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    // If there is a parameter, and the id exists, retrieve data: we're editing a column

    if ($columnID) {
        $result = $xoopsDB->query('SELECT columnID, author, name, description, total, weight, colimage, created FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = '$columnID'");

        [$columnID, $author, $name, $description, $total, $weight, $colimage, $created] = $xoopsDB->fetchRow($result);

        if (0 == $xoopsDB->getRowsNum($result)) {
            redirect_header('column.php', 1, _AM_eNoticias_NOCOLTOEDIT);

            exit();
        }

        xoops_cp_header();

        adminMenu(1, _AM_eNoticias_COLS . _AM_eNoticias_EDITING . $name . "'");

        echo "<h3 style='color: #2F5376; '>" . _AM_eNoticias_ADMINCOLMNGMT . '</h3>';

        $sform = new XoopsThemeForm(_AM_eNoticias_MODCOL . ": $name", 'op', xoops_getenv('PHP_SELF'));
    } else {
        xoops_cp_header();

        adminMenu(1, _AM_eNoticias_COLS . _AM_eNoticias_CREATINGCOL);

        echo "<h3 style='color: #2F5376; '>" . _AM_eNoticias_ADMINCOLMNGMT . '</h3>';

        $sform = new XoopsThemeForm(_AM_eNoticias_NEWCOL, 'op', xoops_getenv('PHP_SELF'));
    }

    $sform->setExtra('enctype="multipart/form-data"');

    $sform->addElement(new XoopsFormText(_AM_eNoticias_COLNAME, 'name', 50, 80, stripslashes($name)), true);

    // Selector to get author

    if (is_numeric($author)) {
        $authorinput = '';
    } else {
        $authorinput = $author;
    }

    ob_start();

    getuserForm((int)$author);

    $sform->addElement(new XoopsFormLabel(_AM_eNoticias_AUTHOR, ob_get_contents()));

    ob_end_clean();

    $sform->addElement(new XoopsFormTextArea(_AM_eNoticias_COLDESCRIPT, 'description', $description, 7, 60));

    $sform->addElement(new XoopsFormText(_AM_eNoticias_COLPOSIT, 'weight', 4, 4, $weight));

    if (!$colimage) {
        $colimage = 'nopicture.png';
    }

    $graph_array = XoopsLists:: getImgListAsArray(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir']);

    $colimage_select = new XoopsFormSelect('', 'colimage', $colimage);

    $colimage_select->addOptionArray($graph_array);

    $colimage_select->setExtra("onchange='showImgSelected(\"image3\", \"colimage\", \"" . $xoopsModuleConfig['sbuploaddir'] . '", "", "' . XOOPS_URL . "\")'");

    $colimage_tray = new XoopsFormElementTray(_AM_eNoticias_COLIMAGE, '&nbsp;');

    $colimage_tray->addElement($colimage_select);

    $colimage_tray->addElement(new XoopsFormLabel('', "<br><br><img src='" . XOOPS_URL . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $colimage . "' name='image3' id='image3' alt=''>"));

    $sform->addElement($colimage_tray);

    // Code to call the file browser to select an image to upload

    $sform->addElement(new XoopsFormFile(_AM_eNoticias_COLIMAGEUPLOAD, 'cimage', $xoopsModuleConfig['maxfilesize']), false);

    $sform->addElement(new XoopsFormHidden('columnID', $columnID));

    $button_tray = new XoopsFormElementTray('', '');

    $hidden = new XoopsFormHidden('op', 'addcol');

    $button_tray->addElement($hidden);

    // No ID for column -- then it's new column, button says 'Create'

    if (!$columnID) {
        $butt_create = new XoopsFormButton('', '', _AM_eNoticias_CREATE, 'submit');

        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');

        $button_tray->addElement($butt_create);

        $butt_clear = new XoopsFormButton('', '', _AM_eNoticias_CLEAR, 'reset');

        $button_tray->addElement($butt_clear);

        $butt_cancel = new XoopsFormButton('', '', _AM_eNoticias_CANCEL, 'button');

        $butt_cancel->setExtra('onclick="history.go(-1)"');

        $button_tray->addElement($butt_cancel);
    } else { // button says 'Update'
        $butt_create = new XoopsFormButton('', '', _AM_eNoticias_MODIFY, 'submit');

        $butt_create->setExtra('onclick="this.form.elements.op.value=\'addcol\'"');

        $button_tray->addElement($butt_create);

        $butt_cancel = new XoopsFormButton('', '', _AM_eNoticias_CANCEL, 'button');

        $butt_cancel->setExtra('onclick="history.go(-1)"');

        $button_tray->addElement($butt_cancel);
    }

    $sform->addElement($button_tray);

    $sform->display();

    unset($hidden);
}

switch ($op) {
    case 'mod':
        $columnID = isset($_POST['columnID']) ? (int)$_POST['columnID'] : (int)$_GET['columnID'];
        editcol($columnID);
        break;
    case 'addcol':

        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $modify, $myts, $columnID;

        if (isset($_POST['columnID'])) {
            $columnID = $_POST['columnID'];
        }

        if (($_POST['weight']) && is_numeric($_POST['weight'])) {
            $weight = $myts->addSlashes($_POST['weight']);
        } else {
            $weight = 1;
        }

        $name = htmlspecialchars($_POST['name'], ENT_QUOTES | ENT_HTML5);
        $description = $myts->addSlashes($_POST['description']);
        $description = $myts->xoopsCodeDecode($description, $allowimage = 1);

        if ('-1' == $_POST['author']) {
            $author = $myts->addSlashes($_POST['authorinput']);
        } else {
            $author = $myts->addSlashes($_POST['author']);
        }

        if ('' != $HTTP_POST_FILES['cimage']['name']) {
            if (file_exists(XOOPS_ROOT_PATH . '/' . $xoopsModuleConfig['sbuploaddir'] . '/' . $HTTP_POST_FILES['cimage']['name'])) {
                redirect_header('column.php', 1, _AM_eNoticias_FILEEXISTS);
            }

            $allowed_mimetypes = ['image/gif', 'image/jpeg', 'image/pjpeg', 'image/png'];

            uploading($allowed_mimetypes, $HTTP_POST_FILES['cimage']['name'], 'index.php', 0, $xoopsModuleConfig['sbuploaddir']);

            $colimage = $HTTP_POST_FILES['cimage']['name'];
        } elseif ('blank.png' != $_POST['colimage']) {
            $colimage = $myts->addSlashes($_POST['colimage']);
        } else {
            $colimage = '';
        }

        // Run the query and update the data
        if (!$_POST['columnID']) {
            if ($xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('eNoticias_Colunas') . " (columnID, name, author, description, weight, total, colimage) VALUES ('', '$name', '$author', '$description', '$weight', '0', '$colimage')")) {
                redirect_header('permissions.php', 1, _AM_eNoticias_COLCREATED);

                $newid = $xoopsDB->getInsertId();

                // Notify of new column

                global $xoopsModule;

                $tags = [];

                $tags['COLUMN_NAME'] = $name;

                $tags['COLUMN_URL'] = XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/index.php?op=col&columnID=' . $newid;

                $notificationHandler = xoops_getHandler('notification');

                $notificationHandler->triggerEvent('global', 0, 'new_column', $tags);
            } else {
                redirect_header('index.php', 1, _AM_eNoticias_NOTUPDATED);
            }
        } else {
            if ($xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('eNoticias_Colunas') . " SET name = '$name', author = '$author', description = '$description', weight = '$weight', colimage = '$colimage' WHERE columnID = '$columnID'")) {
                redirect_header('index.php', 1, _AM_eNoticias_COLMODIFIED);
            } else {
                redirect_header('index.php', 1, _AM_eNoticias_NOTUPDATED);
            }
        }
        exit();
        break;
    case 'del':

        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB;

        $groups = ($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
        $module_id = $xoopsModule->getVar('mid');
        $gpermHandler = xoops_getHandler('groupperm');

        $confirm = (isset($confirm)) ? 1 : 0;

        if ($confirm) {
            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = '$columnID'");

            $xoopsDB->query('DELETE FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE columnID = '$columnID'");

            xoops_groupperm_deletebymoditem($module_id, _AM_eNoticias_COLPERMS, $columnID);

            redirect_header('index.php', 1, sprintf(_AM_eNoticias_COLISDELETED, $name));

            exit();
        }
            $columnID = $_POST['columnID'] ?? $columnID;
            $result = $xoopsDB->query('SELECT columnID, name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = '$columnID'");
            [$colid, $name] = $xoopsDB->fetchRow($result);
            xoops_cp_header();
            xoops_confirm(['op' => 'del', 'columnID' => $columnID, 'confirm' => 1, 'name' => $name], 'column.php', _AM_eNoticias_DELETETHISCOL . '<br><br>' . $name, _AM_eNoticias_DELETE);
            xoops_cp_footer();

        exit();
        break;
    case 'cancel':
        redirect_header('index.php', 1, sprintf(_AM_eNoticias_BACK2IDX, ''));
        exit();

    case 'default':
    default:
        editcol();
        showColumns(0);
        break;
}
xoops_cp_footer();
