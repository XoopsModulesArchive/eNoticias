<?php

// $Id: enot_destaque01.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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

function b_enot_destaque02_show()
{
    global $xoopsDB, $xoopsUser;

    $myts = MyTextSanitizer:: getInstance();

    $gpermHandler = xoops_getHandler('groupperm');

    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

    $block = [];

    // Retrieve the latest article in the selected column

    $sql = $xoopsDB->query(
        'SELECT sba.artimage as artimage, sba.columnID as colid, sba.articleID as artid, sba.headline as arttitle FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " sba WHERE sba.submit = '0' AND sba.offline = '0' AND sba.columnID=2 ORDER BY sba.datesub DESC",
        1,
        0
    );

    $resultado = $xoopsDB->fetchArray($sql);

    $block['destaques']['artimage'] = $resultado['artimage'];

    $block['destaques']['colid'] = $resultado['colid'];

    $block['destaques']['artid'] = $resultado['artid'];

    $block['destaques']['arttitle'] = $resultado['arttitle'];

    return $block;
}
