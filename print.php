<?php

/**
 * $Id: print.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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
 * //  ------------------------------------------------------------------------ */
require __DIR__ . '/header.php';

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

if (empty($articleID)) {
    redirect_header('index.php');
}

function PrintPage($articleID)
{
    global $xoopsConfig, $xoopsDB, $xoopsModule, $xoopsModuleConfig, $myts;

    $result1 = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID = '$articleID' and submit = '0' order by datesub");

    [$articleID, $columnID, $headline, $lead, $bodytext, $teaser, $uid, $submit, $datesub, $counter, $weight, $html, $smiley, $xcodes, $breaks, $block, $artimage, $votes, $rating, $comments, $offline, $notifypub] = $xoopsDB->fetchRow($result1);

    $result2 = $xoopsDB->query('SELECT name, author FROM ' . $xoopsDB->prefix('eNoticias_Colunas') . " WHERE columnID = '$columnID'");

    [$colname, $author] = $xoopsDB->fetchRow($result2);

    $result3 = $xoopsDB->query('SELECT name, uname FROM ' . $xoopsDB->prefix('users') . " WHERE uid = '$author'");

    [$authorname, $uname] = $xoopsDB->fetchRow($result3);

    if (!$authorname) {
        $authorname = htmlspecialchars($uname, ENT_QUOTES | ENT_HTML5);
    }

    $datetime = formatTimestamp($datesub, $xoopsModuleConfig['dateformat']);

    $bodytext = str_replace('[pagebreak]', '<br style="page-break-after:always;">', $bodytext);

    $bodytext = $myts->displayTarea($bodytext, $html, $smiley, $xcodes, '', $breaks);

    $authorname = htmlspecialchars(ucfirst($authorname), ENT_QUOTES | ENT_HTML5);

    echo "<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>\n";

    echo "<html>\n<head>\n";

    echo '<title>' . $xoopsConfig['sitename'] . "</title>\n";

    echo "<meta http-equiv='Content-Type' content='text/html; charset=" . _CHARSET . "'>\n";

    echo "<meta name='AUTHOR' content='" . $xoopsConfig['sitename'] . "'>\n";

    echo "<meta name='COPYRIGHT' content='Copyright (c) 2004 by " . $xoopsConfig['sitename'] . "'>\n";

    echo "<meta name='DESCRIPTION' content='" . $xoopsConfig['slogan'] . "'>\n";

    echo "<meta name='GENERATOR' content='" . XOOPS_VERSION . "'>\n\n\n";

    echo "<body bgcolor='#ffffff' text='#000000'>
			<div style='width: 600px; border: 1px solid #000; padding: 20px;'>
				<div style='text-align: center; display: block; padding-bottom: 12px; margin: 0 0 6px 0; border-bottom: 2px solid #ccc;'><img src='" . XOOPS_URL . '/modules/' . $xoopsModule->dirname() . "/images/eNoticias_slogo.png' border='0' alt=''><h2 style='margin: 0;'>" . $headline . '</h2></div>
				<div></div>
				<div>Column: <b>' . $colname . "</b></div>
				<div style='padding-bottom: 6px; border-bottom: 1px solid #ccc;'>Author: <b>" . $authorname . '</b></div>
				<p>' . $lead . '</p>
				<p>' . $bodytext . "</p>
				<div style='padding-top: 12px; border-top: 2px solid #ccc;'><small><b>Published: </b>&nbsp;" . $datetime . '<br></div>
			</div>
			<br>
		  </body>
		  </html>';
}

PrintPage($articleID);
