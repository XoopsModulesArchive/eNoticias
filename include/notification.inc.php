<?php

/**
 * $Id: notification.inc.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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
 * @param mixed $column
 * @param mixed $item_id
 * //  ------------------------------------------------------------------------
 * @return mixed
 * @return mixed
 */
function eNoticias_notify_iteminfo($column, $item_id)
{
    global $xoopsModule, $xoopsModuleConfig, $xoopsConfig;

    if (empty($xoopsModule) || 'eNoticias' != $xoopsModule->getVar('dirname')) {
        $moduleHandler = xoops_getHandler('module');

        $module = $moduleHandler->getByDirname('eNoticias');

        $configHandler = xoops_getHandler('config');

        $config = &$configHandler->getConfigsByCat(0, $module->getVar('mid'));
    } else {
        $module = &$xoopsModule;

        $config = &$xoopsModuleConfig;
    }

    if ('global' == $category) {
        $item['name'] = '';

        $item['url'] = '';

        return $item;
    }

    global $xoopsDB;

    echo $column;

    if ('column' == $category) {
        // Assume we have a valid category id

        $sql = 'SELECT name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' WHERE columnID  = ' . $item_id;

        $result = $xoopsDB->query($sql); // TODO: error check

        $result_array = $xoopsDB->fetchArray($result);

        $item['name'] = $result_array['name'];

        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/column.php?columnID=' . $item_id;

        return $item;
    }

    if ('article' == $category) {
        echo $item_id;

        // Assume we have a valid story id

        $sql = 'SELECT question FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE articleID = ' . $item_id;

        $result = $xoopsDB->query($sql); // TODO: error check

        $result_array = $xoopsDB->fetchArray($result);

        $item['name'] = $result_array['headline'];

        $item['url'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/article.php?articleID=' . $item_id;

        return $item;
    }
}
