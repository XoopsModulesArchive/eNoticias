<?php

/**
 * $Id: modinfo.php v 1.00 2004/12/30 Gustavo S. Villa Exp
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

// Module Info
// The name of this module
global $xoopsModule;
define('_MI_eNoticias_MD_NAME', 'eNoticias');

// A brief description of this module
define('_MI_eNoticias_MD_DESC', 'OpEd for your site');

// Sub menus in main menu block
define('_MI_eNoticias_SUB_SMNAME1', 'Submit an article');

// A brief description of this module
define('_MI_eNoticias_ALLOWSUBMIT', '1. User submissions:');
define('_MI_eNoticias_ALLOWSUBMITDSC', 'Allow users to submit articles to your website?');

define('_MI_eNoticias_AUTOAPPROVE', '2. Auto approve articles:');
define('_MI_eNoticias_AUTOAPPROVEDSC', 'Auto approves submitted articles without admin intervention.');

define('_MI_eNoticias_ALLOWADMINHITS', '3. Admin counter reads:');
define('_MI_eNoticias_ALLOWADMINHITSDSC', 'Allow admin hits for counter stats?');

define('_MI_eNoticias_PERPAGE', '4. Maximum articles per page (Admin side):');
define('_MI_eNoticias_PERPAGEDSC', 'Maximum number of articles per page to be displayed at once in Articles Admin.');

define('_MI_eNoticias_PERPAGEINDEX', '5. Maximum articles per page (User side):');
define('_MI_eNoticias_PERPAGEINDEXDSC', 'Maximum number of articles per page to be displayed at once in the user side of the module.');

define('_MI_eNoticias_IMGDIR', '6. Image base directory:');
define('_MI_eNoticias_IMGDIRDSC', 'This is the directory that holds the operational images. (No trailing \'/\')');

define('_MI_eNoticias_UPLOADDIR', '7. Image upload directory:');
define('_MI_eNoticias_UPLOADDIRDSC', 'This is the directory where columnists\' and articles\' pics will be stored. (No trailing \'/\')');

define('_MI_eNoticias_IMGWIDTH', '8. Maximum image width:');
define('_MI_eNoticias_IMGWIDTHDSC', 'Sets the maximum allowed width of an image when uploading.');

define('_MI_eNoticias_IMGHEIGHT', '9. Maximum image height:');
define('_MI_eNoticias_IMGHEIGHTDSC', 'Sets the maximum allowed height of an image when uploading.');

define('_MI_eNoticias_MAXFILESIZE', '10. Maximum upload size:');
define('_MI_eNoticias_MAXFILESIZEDSC', 'Sets the maximum file size allowed when uploading files. Restricted to max upload permitted on the server.');

define('_MI_eNoticias_DATEFORMAT', '11. Date format:');
define('_MI_eNoticias_DATEFORMATDSC', 'Sets the display date format for articles.');

define('_MI_eNoticias_ALLOWCOMMENTS', '12. Control comments at the story level:');
define('_MI_eNoticias_ALLOWCOMMENTSDSC', 'If you set this option to "Yes", you\'ll see comments only on those articles that have their comment checkbox marked in the admin form. <br><br>Select "No" to have comments managed at the global level (look below under the tag "Comment rules".');

define('_MI_eNoticias_MOREARTS', '13. Articles in author&#8217s side-box:');
define('_MI_eNoticias_MOREARTSDSC', 'Specify the number of articles to display in the lateral box.');

define('_MI_eNoticias_COLSINMENU', '14. Include columns in menu?:');
define('_MI_eNoticias_COLSINMENUDSC', 'If you set this option to "Yes", authorized users will be able to see the columns names in their module menu. This is obviously not recommended for sites with MANY columns.');

define('_MI_eNoticias_COLSPERINDEX', '15. How many column teasers do you want to see in each index page?:');
define('_MI_eNoticias_COLSPERINDEXDSC', 'How many columns should appear per index page? [Default = 3]');

// Names of admin menu items
define('_MI_eNoticias_ADMENU1', 'Index');
define('_MI_eNoticias_ADMENU2', 'Columns');
define('_MI_eNoticias_ADMENU3', 'Articles');
define('_MI_eNoticias_ADMENU4', 'Permissions');
define('_MI_eNoticias_ADMENU5', 'Blocks');
define('_MI_eNoticias_ADMENU6', 'Go to module');

//Names of Blocks and Block information
define('_MI_eNoticias_enotRATED', 'Best rated articles');
define('_MI_eNoticias_enotNEW', 'Recent articles');
define('_MI_eNoticias_enotTOP', 'Most read articles');

// Text for notifications

define('_MI_eNoticias_GLOBAL_NOTIFY', 'Global');
define('_MI_eNoticias_GLOBAL_NOTIFYDSC', 'Global notification options.');

define('_MI_eNoticias_COLUMN_NOTIFY', 'Column');
define('_MI_eNoticias_COLUMN_NOTIFYDSC', 'Notification options that apply to the current column.');

define('_MI_eNoticias_ARTICLE_NOTIFY', 'Article');
define('_MI_eNoticias_ARTICLE_NOTIFYDSC', 'Notification options that apply to the current article.');

define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFY', 'New column');
define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFYCAP', 'Notify me when a new column is created.');
define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFYDSC', 'Receive notification when a new column is created.');
define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New column');

define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFY', 'Modify article requested');
define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFYCAP', 'Notify me of any article modification request.');
define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFYDSC', 'Receive notification when any article modification request is submitted.');
define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Article modification requested');

define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFY', 'Broken article submitted');
define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFYCAP', 'Notify me of any broken article report.');
define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFYDSC', 'Receive notification when any broken article report is submitted.');
define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Broken article reported');

define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFY', 'Article submitted');
define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFYCAP', 'Notify me when any new article is submitted and is awaiting approval.');
define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFYDSC', 'Receive notification when any new article is submitted and is waiting approval.');
define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New article submitted');

define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFY', 'New article');
define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFYCAP', 'Notify me when any new article is published.');
define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFYDSC', 'Receive notification when any new article is published.');
define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New article');

define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFY', 'Article submitted');
define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFYCAP', 'Notify me when a new article is submitted and waiting approval to the current column.');
define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFYDSC', 'Receive notification when a new article is submitted and waiting approval in the current column.');
define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New file submitted in column');

define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFY', 'New article');
define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFYCAP', 'Notify me when a new article is posted in the current column.');
define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFYDSC', 'Receive notification when a new article is posted in the current column.');
define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : New article in column');

define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFY', 'Article approved');
define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFYCAP', 'Notify me when this article is approved.');
define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFYDSC', 'Receive notification when this article is approved.');
define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Article approved');

define('_MI_eNoticias_ALLOWEDSUBMITGROUPS', 'Which groups can submit?');
define('_MI_eNoticias_ALLOWEDSUBMITGROUPSDSC', 'User groups that can submit articles.');
