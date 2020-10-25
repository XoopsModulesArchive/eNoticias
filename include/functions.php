<?php

/**
 * $Id: functions.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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
 * @param mixed $userid
 * @param mixed $name
 * //  ------------------------------------------------------------------------ */

/**
 * getLinkedUnameFromId()
 *
 * @param int $userid Userid of author etc
 * @param int $name   :  0 Use Usenamer 1 Use realname
 * @return int|mixed|string
 */
function getLinkedUnameFromId($userid = 0, $name = 0)
{
    if (!is_numeric($userid)) {
        return $userid;
    }

    $userid = (int)$userid;

    if ($userid > 0) {
        $memberHandler = xoops_getHandler('member');

        $user = $memberHandler->getUser($userid);

        if (is_object($user)) {
            $ts = MyTextSanitizer::getInstance();

            $username = $user->getVar('uname');

            $usernameu = $user->getVar('name');

            if (($name) && !empty($usernameu)) {
                $username = $user->getVar('name');
            }

            if (!empty($usernameu)) {
                $linkeduser = "$usernameu [<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . $ts->htmlSpecialChars($username) . '</a>]';
            } else {
                $linkeduser = "<a href='" . XOOPS_URL . '/userinfo.php?uid=' . $userid . "'>" . ucfirst($ts->htmlSpecialChars($username)) . '</a>';
            }

            return $linkeduser;
        }
    }

    return $GLOBALS['xoopsConfig']['anonymous'];
}

function updaterating($sel_id) // updates rating data in itemtable for a given item
{
    global $xoopsDB;

    $totalrating = 0;

    $votesDB = 0;

    $finalrating = 0;

    $query = 'select rating FROM ' . $xoopsDB->prefix('eNoticias_Votacoes') . " WHERE lid = $sel_id ";

    $voteresult = $xoopsDB->query($query);

    $votesDB = $xoopsDB->getRowsNum($voteresult);

    while (list($rating) = $xoopsDB->fetchRow($voteresult)) {
        $totalrating += $rating;
    }

    if (0 != ($totalrating) && 0 != $votesDB) {
        $finalrating = $totalrating / $votesDB;

        $finalrating = number_format($finalrating, 4);
    }

    $xoopsDB->queryF('UPDATE ' . $xoopsDB->prefix('eNoticias_Artigos') . " SET rating = '$finalrating', votes = '$votesDB' WHERE articleID  = $sel_id");
}

function countByCategory($c)
{
    global $xoopsUser, $xoopsDB, $xoopsModule;

    $count = 0;

    $sql = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE submit ='0' and columnID = '$c'");

    while (false !== ($myrow = $xoopsDB->fetchArray($sql))) {
        $perm_name = 'Column Permissions';

        $perm_itemid = $myrow['topicID'];

        if ($xoopsUser) {
            $groups = $xoopsUser->getGroups();
        } else {
            $groups = XOOPS_GROUP_ANONYMOUS;
        }

        $module_id = $xoopsModule->getVar('mid');

        $gpermHandler = xoops_getHandler('groupperm');

        if ($gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
            $count++;
        }
    }

    return $count;
}

function displayimage($image = 'blank.gif', $path = '', $imgsource = '', $alttext = '')
{
    global $xoopsConfig, $xoopsUser, $xoopsModule;

    $showimage = '';

    /**
     * Check to see if link is given
     */

    if ($path) {
        $showimage = '<a href=' . $path . '>';
    }

    /**
     * checks to see if the file is valid else displays default blank image
     */

    if (!is_dir(XOOPS_ROOT_PATH . '/' . $imgsource . '/' . $image) && file_exists(XOOPS_ROOT_PATH . '/' . $imgsource . '/' . $image)) {
        $showimage .= '<img src=' . XOOPS_URL . '/' . $imgsource . '/' . $image . " border='0' alt=" . $alttext . '></a>';
    } else {
        if ($xoopsUser && $xoopsUser->isAdmin($xoopsModule->mid())) {
            $showimage .= "<img src=images/brokenimg.png border='0' alt='" . _AM_eNoticias_ISADMINNOTICE . "'></a>";
        } else {
            $showimage .= "<img src=images/blank.png border='0' alt=" . $alttext . '></a>';
        }
    }

    // clearstatcache();

    return $showimage;
}

function uploading($allowed_mimetypes, $httppostfiles, $redirecturl = 'index.php', $num = 0, $dir = 'uploads', $redirect = 0)
{
    require_once XOOPS_ROOT_PATH . '/class/uploader.php';

    global $xoopsConfig, $xoopsModuleConfig, $_POST;

    $maxfilesize = $xoopsModuleConfig['maxfilesize'];

    $maxfilewidth = $xoopsModuleConfig['maximgwidth'];

    $maxfileheight = $xoopsModuleConfig['maximgheight'];

    $uploaddir = XOOPS_ROOT_PATH . '/' . $dir . '/';

    $uploader = new XoopsMediaUploader($uploaddir, $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);

    if ($uploader->fetchMedia($_POST['xoops_upload_file'][$num])) {
        if (!$uploader->upload()) {
            $errors = $uploader->getErrors();

            redirect_header($redirecturl, 1, $errors);
        } else {
            if ($redirect) {
                redirect_header($redirecturl, '1', 'Image Uploaded');
            }
        }
    } else {
        $errors = $uploader->getErrors();

        redirect_header($redirecturl, 1, $errors);
    }
}

function htmlarray($thishtmlpage, $thepath)
{
    global $xoopsConfig, $wfsConfig;

    $file_array = filesarray($thepath);

    echo "<select size='1' name='htmlpage'>";

    echo "<option value='-1'>------</option>";

    foreach ($file_array as $htmlpage) {
        if ($htmlpage == $thishtmlpage) {
            $opt_selected = "selected='selected'";
        } else {
            $opt_selected = '';
        }

        echo "<option value='" . $htmlpage . "' $opt_selected>" . $htmlpage . '</option>';
    }

    echo '</select>';

    return $htmlpage;
}

function filesarray($filearray)
{
    $files = [];

    $dir = opendir($filearray);

    while (false !== ($file = readdir($dir))) {
        if ((!preg_match('/^[.]{1,2}$/', $file) && preg_match('/[.htm|.html|.xhtml]$/i', $file) && !is_dir($file))) {
            if ('cvs' != mb_strtolower($file) && !is_dir($file)) {
                $files[$file] = $file;
            }
        }
    }

    closedir($dir);

    asort($files);

    reset($files);

    return $files;
}

function getuserForm($user)
{
    global $xoopsDB, $xoopsConfig;

    echo "<select name='author'>";

    echo "<option value='-1'>------</option>";

    $result = $xoopsDB->query('SELECT uid, uname FROM ' . $xoopsDB->prefix('users') . ' ORDER BY uname');

    while (list($uid, $uname) = $xoopsDB->fetchRow($result)) {
        if ($uid == $user) {
            $opt_selected = "selected='selected'";
        } else {
            $opt_selected = '';
        }

        echo "<option value='" . $uid . "' $opt_selected>" . $uname . '</option>';
    }

    echo '</select>';
}

function adminMenu($currentoption = 0, $breadcrumb = '')
{
    global $xoopsModule, $xoopsConfig;

    $tblColors = [];

    $tblColors[0] = $tblColors[1] = $tblColors[2] = $tblColors[3] = $tblColors[4] = $tblColors[5] = $tblColors[6] = $tblColors[7] = $tblColors[8] = '#DDE';

    $tblColors[$currentoption] = '#FFF';

    if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/modinfo.php')) {
        require_once dirname(__DIR__) . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
    } else {
        require_once dirname(__DIR__) . '/language/english/modinfo.php';
    }

    echo '<div style="font-size: 10px; text-align: right; color: #2F5376; margin: 0 0 8px 0; padding: 2px 6px; line-height: 18px; border: 1px solid #e7e7e7; "><b>' . $xoopsModule->name() . _AM_eNoticias_MODADMIN . '</b> ' . $breadcrumb . '</div>';

    echo '<div id="navcontainer"><ul style="padding: 3px 0; margin-left: 0; font: bold 12px Verdana, sans-serif; ">';

    echo '<li style="list-style: none; margin: 0; display: inline; line-height: 30px; vertical-align: top; "><a href="index.php" style="padding: 3px 0.5em; margin-left: 0px; border: 1px solid #778; background: '
         . $tblColors[0]
         . '; text-decoration: none; white-space: nowrap; ">'
         . _AM_eNoticias_INDEX
         . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="column.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ' . $tblColors[1] . '; text-decoration: none; white-space: nowrap; ">' . _AM_eNoticias_COLS . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="article.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ' . $tblColors[2] . '; text-decoration: none; white-space: nowrap; ">' . _AM_eNoticias_enot . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="submissions.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ' . $tblColors[3] . '; text-decoration: none; white-space: nowrap; ">' . _AM_eNoticias_SUBMITS . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="permissions.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ' . $tblColors[4] . '; text-decoration: none; white-space: nowrap; ">' . _AM_eNoticias_PERMS . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="myblocksadmin.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ' . $tblColors[5] . '; text-decoration: none; white-space: nowrap; ">' . _AM_eNoticias_BLOCKS . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="../../system/admin.php?fct=preferences&amp;op=showmod&amp;mod='
         . $xoopsModule->getVar('mid')
         . '" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '
         . $tblColors[6]
         . '; text-decoration: none; white-space: nowrap; ">'
         . _AM_eNoticias_OPTS
         . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="../index.php" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: ' . $tblColors[7] . '; text-decoration: none; white-space: nowrap; ">' . _AM_eNoticias_GOMOD . '</a></li>';

    echo '<li style="list-style: none; margin: 0; display: inline; "><a href="../help/eNoticias.html" target="_blank" style="padding: 3px 0.5em; margin-left: 3px; border: 1px solid #778; background: '
         . $tblColors[8]
         . '; text-decoration: none; white-space: nowrap; ">'
         . _AM_eNoticias_HELP
         . '</a></li></ul></div>';
}

function showColumns($showCreate = 0)
{
    global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $articleID;

    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';

    $module_id = $xoopsModule->getVar('mid');

    $startcol = isset($_GET['startcol']) ? (int)$_GET['startcol'] : 0;

    /* -- Code to show existing columns -- */

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_eNoticias_SHOWCOLS . '</legend><br>';

    if (1 == $showCreate) {
        echo "<a style='border: 1px solid #5E5D63; color: #000000; font-family: verdana, tahoma, arial, helvetica, sans-serif; font-size: 1em; padding: 4px 8px; text-align:center;' href='column.php'>" . _AM_eNoticias_CREATECOL . '</a><br><br>';
    }

    // To create existing columns table

    $resultC1 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ');

    [$numrows] = $xoopsDB->fetchRow($resultC1);

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ORDER BY weight';

    $resultC2 = $xoopsDB->query($sql, $xoopsModuleConfig['perpage'], $startcol);

    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";

    echo '<tr>';

    echo "<td width='40' class='bg3' align='center'><b>" . _AM_eNoticias_ID . '</b></td>';

    echo "<td class='bg3' align='center'><b>" . _AM_eNoticias_AUTHOR . '</b></td>';

    echo "<td class='bg3' align='center'><b>" . _AM_eNoticias_WEIGHT . '</b></td>';

    echo "<td width='20%' class='bg3' align='center'><b>" . _AM_eNoticias_ARTCOLNAME . '</b></td>';

    echo "<td class='bg3' align='center'><b>" . _AM_eNoticias_DESCRIP . '</b></td>';

    echo "<td width='60' class='bg3' align='center'><b>" . _AM_eNoticias_ACTION . '</b></td>';

    echo '</tr>';

    if ($numrows > 0) { // That is, if there ARE columns in the system
        while (list($columnID, $author, $name, $description, $total, $weight, $colimage, $created) = $xoopsDB->fetchRow($resultC2)) {
            $author = getLinkedUnameFromId($author, 0);

            $modify = "<a href='column.php?op=mod&columnID=" . $columnID . "'><img src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/edit.gif ALT='" . _AM_eNoticias_EDITCOL . "'></a>";

            $delete = "<a href='column.php?op=del&columnID=" . $columnID . "'><img src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/delete.gif ALT='" . _AM_eNoticias_DELETECOL . "'></a>";

            echo '<tr>';

            echo "<td class='head' align='center'>" . $columnID . '</td>';

            echo "<td class='even' align='left'>" . $author . '</td>';

            echo "<td class='even' align='center'>" . $weight . '</td>';

            echo "<td class='even' align='lefet'>" . $name . '</td>';

            echo "<td class='even' align='left'>" . cleanTags($description) . '</td>';

            echo "<td class='even' align='center'> $modify $delete </td>";

            echo '</tr>';
        }
    } else { // that is, $numrows = 0, there's no columns yet
        echo '<tr>';

        echo "<td class='head' align='center' colspan= '7'>" . _AM_eNoticias_NOCOLS . '</td>';

        echo '</tr>';

        $columnID = '0';
    }

    echo "</table>\n";

    $pagenav = new XoopsPageNav($numrows, $xoopsModuleConfig['perpage'], $startcol, 'startcol', 'columnID=' . $columnID);

    echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

    echo '</fieldset>';

    echo "<br>\n";
}

function showArticles($showCreate = 0)
{
    global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $articleID;

    $myts = MyTextSanitizer::getInstance();

    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';

    $module_id = $xoopsModule->getVar('mid');

    $startart = isset($_GET['startart']) ? (int)$_GET['startart'] : 0;

    /* -- Code to show existing articles -- */

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_eNoticias_SHOWARTS . '</legend><br>';

    if (1 == $showCreate) {
        echo "<a style='border: 1px solid #5E5D63; color: #000000; font-family: verdana, tahoma, arial, helvetica, sans-serif; font-size: 1em; padding: 4px 8px; text-align:center;' href='article.php'>" . _AM_eNoticias_CREATEART . '</a><br><br>';
    }

    // To create existing articles table

    $resultA1 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE submit = 0');

    [$numrows] = $xoopsDB->fetchRow($resultA1);

    $sql = 'SELECT articleID, columnID, headline, datesub, offline FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE submit = 0 ORDER BY articleID DESC';

    $resultA2 = $xoopsDB->query($sql, $xoopsModuleConfig['perpage'], $startart);

    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";

    echo '<tr>';

    echo "<td width='40' class='bg3' align='center'><b>" . _AM_eNoticias_ARTID . '</b></td>';

    echo "<td width='20%' class='bg3' align='center'><b>" . _AM_eNoticias_ARTCOLNAME . '</b></td>';

    echo "<td class='bg3' align='center'><b>" . _AM_eNoticias_ARTHEADLINE . '</b></td>';

    echo "<td width='90' class='bg3' align='center'><b>" . _AM_eNoticias_ARTCREATED . '</b></td>';

    echo "<td width='30' class='bg3' align='center'><b>" . _AM_eNoticias_STATUS . '</b></td>';

    echo "<td width='60' class='bg3' align='center'><b>" . _AM_eNoticias_ACTION . '</b></td>';

    echo '</tr>';

    if ($numrows > 0) { // That is, if there ARE articles in the system
        while (list($articleID, $columnID, $headline, $created, $offline) = $xoopsDB->fetchRow($resultA2)) {
            $resultA3 = $xoopsDB->query('SELECT name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = '$columnID'");

            [$name] = $xoopsDB->fetchRow($resultA3);

            $colname = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);

            $headline = htmlspecialchars($headline, ENT_QUOTES | ENT_HTML5);

            $created = formatTimestamp($created, $xoopsModuleConfig['dateformat']);

            $modify = "<a href='article.php?op=mod&articleID=" . $articleID . "'><img src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/edit.gif ALT='" . _AM_eNoticias_EDITART . "'></a>";

            $delete = "<a href='article.php?op=del&articleID=" . $articleID . "'><img src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/delete.gif ALT='" . _AM_eNoticias_DELETEART . "'></a>";

            if (0 == $offline) {
                $status = '<img src=' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/on.gif alt='" . _AM_eNoticias_ARTISON . "'>";
            } else {
                $status = '<img src=' . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/off.gif alt='" . _AM_eNoticias_ARTISOFF . "'>";
            }

            echo '<tr>';

            echo "<td class='head' align='center'>" . $articleID . '</td>';

            echo "<td class='even' align='left'>" . $colname . '</td>';

            echo "<td class='even' align='left'>" . $headline . '</td>';

            echo "<td class='even' align='center'>" . $created . '</td>';

            echo "<td class='even' align='center'>" . $status . '</td>';

            echo "<td class='even' align='center'> $modify $delete </td>";

            echo '</tr>';
        }
    } else { // that is, $numrows = 0, there's no columns yet
        echo '<tr>';

        echo "<td class='head' align='center' colspan= '7'>" . _AM_eNoticias_NOARTS . '</td>';

        echo '</tr>';
    }

    echo "</table>\n";

    $pagenav = new XoopsPageNav($numrows, $xoopsModuleConfig['perpage'], $startart, 'startart', 'articleID =' . $articleID);

    echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

    echo '</fieldset>';

    echo "<br>\n";
}

function showSubmissions()
{
    global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $articleID;

    $myts = MyTextSanitizer::getInstance();

    require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    $startsub = isset($_GET['startsub']) ? (int)$_GET['startsub'] : 0;

    $datesub = isset($_GET['datesub']) ? (int)$_GET['datesub'] : 0;

    require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

    require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';

    $module_id = $xoopsModule->getVar('mid');

    /* -- Code to show submitted articles -- */

    echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_eNoticias_SHOWSUBMISSIONS . '</legend><br>';

    $resultS1 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE submit = 1');

    [$numrows] = $xoopsDB->fetchRow($resultS1);

    $sql = 'SELECT articleID, columnID, headline, datesub FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE submit = 1 ORDER BY datesub DESC';

    $resultS2 = $xoopsDB->query($sql, $xoopsModuleConfig['perpage'], $startsub);

    echo "<table width='100%' cellspacing=1 cellpadding=3 border=0 class = outer>";

    echo '<tr>';

    echo "<td width='40' class='bg3' align='center'><b>" . _AM_eNoticias_ARTID . '</b></td>';

    echo "<td width='20%' class='bg3' align='center'><b>" . _AM_eNoticias_ARTCOLNAME . '</b></td>';

    echo "<td class='bg3' align='center'><b>" . _AM_eNoticias_ARTHEADLINE . '</b></td>';

    echo "<td width='90' class='bg3' align='center'><b>" . _AM_eNoticias_ARTCREATED . '</b></td>';

    echo "<td width='60' class='bg3' align='center'><b>" . _AM_eNoticias_ACTION . '</b></td>';

    echo '</tr>';

    if ($numrows > 0) { // That is, if there ARE unauthorized articles in the system
        while (list($articleID, $columnID, $headline, $created) = $xoopsDB->fetchRow($resultS2)) {
            $resultS3 = $xoopsDB->query('SELECT name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = '$columnID'");

            [$name] = $xoopsDB->fetchRow($resultS3);

            $colname = htmlspecialchars($name, ENT_QUOTES | ENT_HTML5);

            $headline = htmlspecialchars($headline, ENT_QUOTES | ENT_HTML5);

            $created = formatTimestamp($created, $xoopsModuleConfig['dateformat']);

            $modify = "<a href='submissions.php?op=mod&articleID=" . $articleID . "'><img src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/edit.gif ALT='" . _AM_eNoticias_EDITSUBM . "'></a>";

            $delete = "<a href='submissions.php?op=del&articleID=" . $articleID . "'><img src=" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/icon/delete.gif ALT='" . _AM_eNoticias_DELETESUBM . "'></a>";

            echo '<tr>';

            echo "<td class='head' align='center'>" . $articleID . '</td>';

            echo "<td class='even' align='left'>" . $colname . '</td>';

            echo "<td class='even' align='left'>" . $headline . '</td>';

            echo "<td class='even' align='center'>" . $created . '</td>';

            echo "<td class='even' align='center'> $modify $delete </td>";

            echo '</tr>';
        }
    } else { // that is, $numrows = 0, there's no columns yet
        echo '<tr>';

        echo "<td class='head' align='center' colspan= '7'>" . _AM_eNoticias_NOSUBMISSYET . '</td>';

        echo '</tr>';
    }

    echo "</table>\n";

    $pagenav = new XoopsPageNav($numrows, $xoopsModuleConfig['perpage'], $startsub, 'startsub', 'articleID =' . $articleID);

    echo '<div style="text-align:right;">' . $pagenav->renderNav() . '</div>';

    echo '</fieldset>';

    echo "<br>\n";
}
