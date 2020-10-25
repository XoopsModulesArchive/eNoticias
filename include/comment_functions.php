<?php

/**
 * $Id: comment_functions.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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
 * @param mixed $art_id
 * @param mixed $total_num
 * //  ------------------------------------------------------------------------ */
function eNoticias_com_update($art_id, $total_num)
{
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    $sql = 'UPDATE ' . $db->prefix('eNoticias_Artigos') . ' SET comments = ' . $total_num . ' WHERE articleID = ' . $art_id;

    $db->query($sql);
}

function eNoticias_com_approve(&$comment)
{
    // notification mail here
}
