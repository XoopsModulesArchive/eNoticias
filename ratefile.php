<?php

/**
 * $Id: ratefile.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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
include 'header.php';

foreach ($_POST as $k => $v) {
    ${$k} = $v;
}

foreach ($_GET as $k => $v) {
    ${$k} = $v;
}

if (empty($_POST['submit'])) {
    $_POST['submit'] = '';
}

require XOOPS_ROOT_PATH . '/header.php';

if ($_POST['submit']) {
    $ratinguser = (is_object($xoopsUser)) ? $xoopsUser->uid() : 0;

    $rating = ($_POST['rating']) ?: 0;

    // Make sure only 1 anonymous from an IP in a single day.

    $anonwaitdays = 1;

    $ip = getenv('REMOTE_ADDR');

    $lid = (int)$_POST['lid'];

    // Check if Rating is Null

    if (!$rating) {
        redirect_header("article.php?articleID=$lid", 1, _MD_eNoticias_NORATING);

        exit();
    }

    // Check if Download POSTER is voting (UNLESS Anonymous users allowed to post)

    if (0 != $ratinguser) {
        $result = $xoopsDB->query('SELECT uid FROM ' . $xoopsDB->prefix('eNoticias_Artigos') . " WHERE articleID=$lid");

        while (list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
            if ($ratinguserDB == $ratinguser) {
                redirect_header("article.php?articleID=$lid", 1, _MD_eNoticias_CANTVOTEOWN);

                exit();
            }
        }

        // Check if REG user is trying to vote twice.

        $result = $xoopsDB->query('SELECT ratinguser FROM ' . $xoopsDB->prefix('eNoticias_Votacoes') . " WHERE lid=$lid");

        while (list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
            if ($ratinguserDB == $ratinguser) {
                redirect_header("article.php?articleID=$lid", 1, _MD_eNoticias_VOTEONCE);

                exit();
            }
        }
    }

    // Check if ANONYMOUS user is trying to vote more than once per day.

    if (0 == $ratinguser) {
        $yesterday = (time() - (86400 * $anonwaitdays));

        $result = $xoopsDB->query('SELECT COUNT(*) FROM ' . $xoopsDB->prefix('eNoticias_Votacoes') . " WHERE lid = $lid AND ratinguser=0 AND ratinghostname = '$ip' AND ratingtimestamp > $yesterday");

        [$anonvotecount] = $xoopsDB->fetchRow($result);

        if ($anonvotecount >= 1) {
            redirect_header("article.php?articleID=$lid", 1, _MD_eNoticias_VOTEONCE);

            exit();
        }
    }

    // All is well.  Add to Line Item Rate to DB.

    $newid = $xoopsDB->genId($xoopsDB->prefix('eNoticias_Votacoes') . '_ratingid_seq');

    $datetime = time();

    $xoopsDB->query('INSERT INTO ' . $xoopsDB->prefix('eNoticias_Votacoes') . " (ratingid, lid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES ($newid, $lid, $ratinguser, $rating, '$ip', $datetime)");

    // All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.

    updaterating($lid);

    $ratemessage = _MD_eNoticias_VOTEAPPRE . '<br>' . sprintf(_MD_eNoticias_THANKYOU, $xoopsConfig['sitename']);

    redirect_header("article.php?articleID=$lid", 1, $ratemessage);

    exit();
}
    redirect_header("article.php?articleID=$lid", 1, _MD_eNoticias_UNKNOWNERROR);
    exit();

require __DIR__ . '/footer.php';
