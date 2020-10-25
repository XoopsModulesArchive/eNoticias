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

/* -- Available operations -- */
switch ($op) {
    case 'default':
    default:
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

        $startart = isset($_GET['startart']) ? (int)$_GET['startart'] : 0;
        $startcol = isset($_GET['startcol']) ? (int)$_GET['startcol'] : 0;
        $startsub = isset($_GET['startsub']) ? (int)$_GET['startsub'] : 0;
        $datesub = isset($_GET['datesub']) ? (int)$_GET['datesub'] : 0;

        xoops_cp_header();
        require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
        require_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->dirname() . '/include/cleantags.php';
        $module_id = $xoopsModule->getVar('mid');

        global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModuleConfig, $xoopsModule, $articleID;

        $myts = MyTextSanitizer::getInstance();
        adminMenu(0, _AM_eNoticias_INDEX);

        $result01 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ');
        [$totalcolumns] = $xoopsDB->fetchRow($result01);
        $result02 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE submit = 0');
        [$totalpublished] = $xoopsDB->fetchRow($result02);
        $result03 = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE submit = 1');
        [$totalsubmitted] = $xoopsDB->fetchRow($result03);
        echo '<h3 style="color: #2F5376; ">' . _AM_eNoticias_MODULEHEAD . '</h3>';
        echo "<fieldset><legend style='font-weight: bold; color: #900;'>" . _AM_eNoticias_INVENTORY . '</legend>';
        echo "<div style='padding: 12px;'>" . _AM_eNoticias_TOTALARTS . " <b>$totalpublished</b> | ";
        echo _AM_eNoticias_TOTALCOLS . " <b>$totalcolumns</b> | ";
        echo _AM_eNoticias_TOTALSUBM . " <b>$totalsubmitted</b></div>";
        echo '</fieldset><br>';

        showArticles(1);
        showColumns(1);

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

        break;
}
xoops_cp_footer();
