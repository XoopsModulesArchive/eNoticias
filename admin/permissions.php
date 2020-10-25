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
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

$op = '';

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

switch ($op) {
    case 'default':
    default:
        global $xoopsDB, $xoopsModule;

        $item_list2 = [];
        $block2 = [];

        xoops_cp_header();
        adminMenu(4, _AM_eNoticias_PERMS);
        echo "<h3 style='color: #2F5376; '>" . _AM_eNoticias_PERMSMNGMT . '</h3>';

        $result2 = $xoopsDB->query('SELECT columnID, name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ');
        if ($xoopsDB->getRowsNum($result2)) {
            while (false !== ($myrow2 = $xoopsDB->fetchArray($result2))) {
                $item_list2['cid'] = $myrow2['columnID'];

                $item_list2['title'] = $myrow2['name'];

                $form2 = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'Column permissions', _AM_eNoticias_SELECT_COLS);

                $block2[] = $item_list2;

                foreach ($block2 as $itemlists) {
                    $form2->addItem($itemlists['cid'], $itemlists['title']);
                }
            }

            echo $form2->render();
        } else {
            echo '<p><div style="text-align:center;"><b>' . _AM_eNoticias_NOPERMSSET . '</b></div></p>';
        }
        echo _AM_eNoticias_PERMSNOTE;
}
xoops_cp_footer();
