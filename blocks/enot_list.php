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
function b_enot_list_show()
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig, $xoopsUser;

    $myts = MyTextSanitizer:: getInstance();

    $gpermHandler = xoops_getHandler('groupperm');

    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

    $block = [];

    // Lista todos os colunistas

    $sql = 'SELECT sbc.columnID as colid, user.uid as userid, user.uname as username FROM ' . $xoopsDB->prefix('users') . ' user, ' . $xoopsDB->prefix('eNoticias_Colunas') . ' sbc WHERE (sbc.author = user.uid) GROUP BY sbc.columnID';

    $colunistas = $xoopsDB->query($sql);

    // Lista todas as colunas do colunista

    while (false !== ($myrow = $xoopsDB->fetchArray($colunistas))) {
        $sql2 = 'SELECT sbc.colimage as colimage, sba.columnID as colid, sba.articleID as artid, sbc.name as colname, sba.headline as coltitle FROM '
                                 . $xoopsDB->prefix('eNoticias_Artigos')
                                 . ' sba, '
                                 . $xoopsDB->prefix('users')
                                 . ' user, '
                                 . $xoopsDB->prefix('eNoticias_Colunas')
                                 . " sbc WHERE (sba.uid = user.uid) AND (sba.columnID = sbc.columnID) AND sba.submit = '0' AND sba.offline = '0' AND sbc.columnID = "
                                 . $myrow['colid']
                                 . ' ORDER BY sba.datesub DESC';

        $colunas = $xoopsDB->query($sql2, 1, 0);

        $resultado['userid'] = $myrow['userid'];

        $resultado['username'] = $myrow['username'];

        while (false !== ($row = $xoopsDB->fetchArray($colunas))) {
            $resultado['artid'] = $row['artid'];

            $resultado['colid'] = $row['colid'];

            $resultado['colname'] = $row['colname'];

            $resultado['colimage'] = $row['colimage'];

            $resultado['coltitle'] = $row['coltitle'];

            $block['listcol'][] = $resultado;
        }
    }

    return $block;
}
