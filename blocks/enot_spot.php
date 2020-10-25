<?php

// $Id: enot_spot.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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

function b_enot_spot_show($options)
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig, $xoopsUser;

    $myts = MyTextSanitizer:: getInstance();

    $gpermHandler = xoops_getHandler('groupperm');

    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

    $block = [];

    // To handle options in the template

    if (1 == $options[2]) {
        $block['showdateask'] = 1;
    } else {
        $block['showdateask'] = 0;
    }

    if (1 == $options[3]) {
        $block['showbylineask'] = 1;
    } else {
        $block['showbylineask'] = 0;
    }

    if (1 == $options[4]) {
        $block['showstatsask'] = 1;
    } else {
        $block['showstatsask'] = 0;
    }

    if ('ver' == $options[5]) {
        $block['verticaltemplate'] = 1;
    } else {
        $block['verticaltemplate'] = 0;
    }

    if (1 == $options[6]) {
        $block['showpicask'] = 1;
    } else {
        $block['showpicask'] = 0;
    }

    // Retrieve the latest article in the selected column

    $resultA = $xoopsDB->query('SELECT articleID, columnID, headline, teaser, uid, datesub, counter, rating, votes FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE columnID = ' . $options[0] . " AND submit = '0' AND offline = 0 ORDER BY datesub DESC", 1, 0);

    [$articleID, $columnID, $headline, $teaser, $authorID, $datesub, $counter, $rating, $votes] = $xoopsDB->fetchRow($resultA);

    $artID = (int)$articleID;

    // Retrieve the column's name

    $resultB = $xoopsDB->query('SELECT name, colimage FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' WHERE columnID = ' . $options[0] . ' ');

    [$name, $colimage] = $xoopsDB->fetchRow($resultB);

    $hModule = xoops_getHandler('module');

    $hModConfig = xoops_getHandler('config');

    if ($noticiaModule = $hModule->getByDirname('eNoticias')) {
        $module_id = $noticiaModule->getVar('mid');

        $module_name = $noticiaModule->getVar('dirname');

        $noticiaConfig = &$hModConfig->getConfigsByCat(0, $noticiaModule->getVar('mid'));

        if ($gpermHandler->checkRight('Column Permissions', $options[0], $groups, $module_id)) {
            // We get the author's ID and name

            $block['userID'] = ((int)$authorID);

            $block['authorname'] = XoopsUserUtility::getUnameFromId((int)$authorID);

            // -- Then we get the columns name and ID and pic

            $block['name'] = $name;

            $block['colID'] = $options[0];

            $block['colimage'] = stripslashes($colimage);

            // Assign main story variables to block array

            // -- First, the main story title and ID

            $block['storyID'] = (int)$articleID;

            $block['title'] = htmlspecialchars($headline, ENT_QUOTES | ENT_HTML5);

            // -- Then the teaser text and assorted data

            $block['introtext'] = $myts->displayTarea($teaser);

            $block['moduledir'] = $module_name;

            $block['date'] = formatTimestamp($datesub, $noticiaConfig['dateformat']);

            $block['hits'] = (int)$counter;

            $block['rating'] = number_format($rating, 2, '.', '');

            $block['votes'] = (int)$votes;

            // Now, to get the other articles' parameters

            $resultC = $xoopsDB->query('SELECT articleID, headline, datesub FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE columnID = ' . $options[0] . ' AND articleID != ' . $block['storyID'] . ' AND submit = 0 AND offline = 0 ORDER BY datesub DESC ', $options[1], 0);

            $i = 0;

            while (false !== ($myrow = $xoopsDB->fetchArray($resultC))) {
                if ($i < $options[1]) {
                    $morelinks = [];

                    $morelinks['id'] = $myrow['articleID'];

                    $morelinks['head'] = htmlspecialchars($myrow['headline'], ENT_QUOTES | ENT_HTML5);

                    $morelinks['subdate'] = formatTimestamp($datesub, $noticiaConfig['dateformat']);

                    $i++;

                    $block['links'][] = $morelinks;
                }
            }
        }
    }

    return $block;
}

function b_enot_spot_edit($options)
{
    global $xoopsDB;

    $resultcat = $xoopsDB->query('SELECT columnID, name FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . ' ORDER BY columnID');

    $form = _MB_eNoticias_SELECTCOL . '<select name="options[]">';

    while (list($categoryID, $name) = $xoopsDB->fetchRow($resultcat)) {
        $form .= "<option value=\"$categoryID\">$categoryID : $name</option>";
    }

    $form .= "</select>\n";

    $form .= '' . _MB_eNoticias_enotTOSHOW . "<input type='text' name='options[]' value='" . $options[1] . "'>&nbsp; articles.<br>";

    $chk2 = '';

    $form .= _MB_eNoticias_SHOWDATE;

    if (1 == $options[2]) {
        $chk2 = ' checked';
    }

    $form .= "<input type='radio' name='options[2]' value='1'" . $chk2 . '>&nbsp;' . _YES;

    $chk2 = '';

    if (0 == $options[2]) {
        $chk2 = ' checked';
    }

    $form .= '&nbsp;<input type="radio" name="options[2]" value="0"' . $chk2 . '>' . _NO . '<br>';

    $chk3 = '';

    $form .= _MB_eNoticias_SHOWBYLINE;

    if (1 == $options[3]) {
        $chk3 = ' checked';
    }

    $form .= "<input type='radio' name='options[3]' value='1'" . $chk3 . '>&nbsp;' . _YES;

    $chk3 = '';

    if (0 == $options[3]) {
        $chk3 = ' checked';
    }

    $form .= '&nbsp;<input type="radio" name="options[3]" value="0"' . $chk3 . '>' . _NO . '<br>';

    $chk4 = '';

    $form .= _MB_eNoticias_SHOWSTATS;

    if (1 == $options[4]) {
        $chk4 = ' checked';
    }

    $form .= "<input type='radio' name='options[4]' value='1'" . $chk4 . '>&nbsp;' . _YES;

    $chk4 = '';

    if (0 == $options[4]) {
        $chk4 = ' checked';
    }

    $form .= '&nbsp;<input type="radio" name="options[4]" value="0"' . $chk3 . '>' . _NO . '<br>';

    $form .= _MB_eNoticias_TEMPLATE . "<select name='options[]'>";

    $form .= "<option value='ver'";

    if ('ver' == $options[5]) {
        $form .= " selected='selected'";
    }

    $form .= '>' . _MB_eNoticias_VERTICAL . "</option>\n";

    $form .= "<option value='hor'";

    if ('hor' == $options[5]) {
        $form .= " selected='selected'";
    }

    $form .= '>' . _MB_eNoticias_HORIZONTAL . '</option>';

    $form .= '</select><br>';

    $chk6 = '';

    $form .= _MB_eNoticias_SHOWPIC;

    if (1 == $options[6]) {
        $chk6 = ' checked';
    }

    $form .= "<input type='radio' name='options[6]' value='1'" . $chk6 . '>&nbsp;' . _YES;

    $chk6 = '';

    if (0 == $options[6]) {
        $chk6 = ' checked';
    }

    $form .= '&nbsp;<input type="radio" name="options[6]" value="0"' . $chk6 . '>' . _NO . '<br>';

    return $form;
}
