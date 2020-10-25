<?php

/**
 * $Id: search.inc.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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
 * @param mixed $queryarray
 * @param mixed $andor
 * @param mixed $limit
 * @param mixed $offset
 * @param mixed $userid
 * //  ------------------------------------------------------------------------
 * @return array
 * @return array
 */
function eNoticias_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsUser, $xoopsUser, $xoopsConfig, $xoopsDB, $xoopsModule, $xoopsModuleConfig;

    $groups = ($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

    if (is_object($xoopsModule)) {
        $xoopsModule = XoopsModule::getByDirname('eNoticias');

        $module_id = $xoopsModule->getVar('mid');
    }

    $gpermHandler = xoops_getHandler('groupperm');

    $ret = [];

    if (0 != $userid) {
        return $ret;
    }

    $sql = 'SELECT articleID, headline, lead, bodytext, uid, datesub FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE submit = 0 AND offline = 0 ';

    // because count() returns 1 even if a supplied variable

    // is not an array, we must check if $querryarray is really an array

    $count = count($queryarray);

    if ($count > 0 && is_array($queryarray)) {
        $sql .= "AND ((headline LIKE '%$queryarray[0]%' OR lead LIKE '%$queryarray[0]%' OR bodytext LIKE '%$queryarray[0]%')";

        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";

            $sql .= "(headline LIKE '%$queryarray[$i]%' OR lead LIKE '%$queryarray[0]%' OR bodytext LIKE '%$queryarray[$i]%')";
        }

        $sql .= ') ';
    }

    $sql .= 'ORDER BY articleID DESC';

    $result = $xoopsDB->query($sql, $limit, $offset);

    $i = 0;

    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        //		if ( $gpermHandler -> checkRight( 'Column Permissions', $columnID, $groups, $module_id ) )

        //			{

        $ret[$i]['image'] = 'images/sb.png';

        $ret[$i]['link'] = 'article.php?articleID=' . $myrow['articleID'];

        $ret[$i]['title'] = $myrow['headline'];

        $ret[$i]['time'] = $myrow['datesub'];

        $ret[$i]['uid'] = $myrow['uid'];

        $i++;

        //			}
    }

    return $ret;
}
