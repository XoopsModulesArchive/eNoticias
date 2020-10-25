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
function b_enot_rated($options)
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig, $xoopsUser;

    $linktext = null;

    $myts = MyTextSanitizer:: getInstance();

    $gpermHandler = xoops_getHandler('groupperm');

    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

    $block = [];

    $resultado = $xoopsDB->query('SELECT articleID, columnID, headline, datesub, counter, weight, rating, votes FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . ' WHERE datesub < ' . time() . " AND datesub > 0 AND submit = '0' AND offline = '0' ORDER BY " . $options[0] . ' DESC');

    $i = 0;

    while (false !== ($myrow = $xoopsDB->fetchArray($resultado))) {
        if ($i < $options[1]) {
            $hModule = xoops_getHandler('module');

            $hModConfig = xoops_getHandler('config');

            if ($noticiaModule = $hModule->getByDirname('eNoticias')) {
                $module_id = $noticiaModule->getVar('mid');

                $module_name = $noticiaModule->getVar('dirname');

                $noticiaConfig = &$hModConfig->getConfigsByCat(0, $noticiaModule->getVar('mid'));

                if ($gpermHandler->checkRight('Column Permissions', $myrow['columnID'], $groups, $module_id)) {
                    $linktext = htmlspecialchars($myrow['headline'], ENT_QUOTES | ENT_HTML5);

                    if (!XOOPS_USE_MULTIBYTES) {
                        if (mb_strlen($myrow['headline']) >= $options[2]) {
                            $linktext = htmlspecialchars(mb_substr($myrow['headline'], 0, ($options[2] - 1)), ENT_QUOTES | ENT_HTML5) . '...';
                        }
                    }

                    $artlink['linktext'] = $linktext;

                    $artlink['id'] = $myrow['articleID'];

                    $artlink['dir'] = $module_name;

                    if ('datesub' == $options[0]) {
                        $artlink['new'] = formatTimestamp($myrow['datesub'], $noticiaConfig['dateformat']);
                    } elseif ('counter' == $options[0]) {
                        $artlink['new'] = $myrow['counter'];
                    } elseif ('weight' == $options[0]) {
                        $artlink['new'] = $myrow['weight'];
                    } elseif ('rating' == $options[0]) {
                        $artlink['new'] = number_format($myrow['rating'], 2, '.', '');

                        $artlink['votes'] = $myrow['votes'];
                    }

                    $i++;

                    $block['artslist'][] = $artlink;
                }
            }
        }
    }

    return $block;
}

function b_enot_rated_edit($options)
{
    $form = '' . _MB_eNoticias_ORDER . "&nbsp;<select name='options[]'>";

    $form .= "<option value='datesub'";

    if ('datesub' == $options[0]) {
        $form .= " selected='selected'";
    }

    $form .= '>' . _MB_eNoticias_DATE . "</option>\n";

    $form .= "<option value='counter'";

    if ('counter' == $options[0]) {
        $form .= " selected='selected'";
    }

    $form .= '>' . _MB_eNoticias_HITS . "</option>\n";

    $form .= "<option value='weight'";

    if ('weight' == $options[0]) {
        $form .= " selected='selected'";
    }

    $form .= '>' . _MB_eNoticias_WEIGHT . "</option>\n";

    $form .= "<option value='rating'";

    if ('rating' == $options[0]) {
        $form .= " selected='selected'";
    }

    $form .= '>' . _MB_eNoticias_RATING . "</option>\n";

    $form .= "</select>\n";

    $form .= '&nbsp;' . _MB_eNoticias_DISP . "&nbsp;<input type='text' name='options[]' value='" . $options[1] . "'>&nbsp;" . _MB_eNoticias_ARTCLS . '';

    $form .= '&nbsp;<br>' . _MB_eNoticias_CHARS . "&nbsp;<input type='text' name='options[]' value='" . $options[2] . "'>&nbsp;" . _MB_eNoticias_LENGTH . '';

    return $form;
}
