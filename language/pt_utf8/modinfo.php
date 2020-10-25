<?php

/**
 * $Id: modinfo.php v 1.00 2004/12/30 Gustavo S. Villa Exp
 * // ------------------------------------------------------------------------ //
 * // e-Notícias //
 * // E-WARE //
 * // <http://www.e-ware.com.br> //
 * // ------------------------------------------------------------------------ //
 * // Você não pode substituir ou alterar qualquer parte desses comentários //
 * // ou créditos dos titulares e autores os quais são considerados direitos //
 * // reservados. //
 * // ------------------------------------------------------------------------ //
 * // Autor: Gustavo S. Villa <guvilladev@e-ware.com.br> //
 * // ------------------------------------------------------------------------ */

// Module Info
// The name of this module
global $xoopsModule;
define('_MI_eNoticias_MD_NAME', 'eNoticias');
// A brief description of this module
define('_MI_eNoticias_MD_DESC', 'Módulo de notícias com destaque e upload de imagens');
// Sub menus in main menu block
define('_MI_eNoticias_SUB_SMNAME1', 'Enviar um artigo');
// A brief description of this module
define('_MI_eNoticias_ALLOWSUBMIT', 'Submissões de visitantes:');
define('_MI_eNoticias_ALLOWSUBMITDSC', 'Permitir visitantes enviar artigos para seu website?');
define('_MI_eNoticias_MAXFILESIZE', 'Tamanho máximo para upload:');
define('_MI_eNoticias_MAXFILESIZEDSC', 'Configure o tamanho máximo permitido quando enviarem artigos. Restrito ao upload max permitido no servidor.');
define('_MI_eNoticias_IMGWIDTH', 'Largura máxima para imagem:');
define('_MI_eNoticias_IMGWIDTHDSC', 'Configure o tamanho máximo permitido a uma imagem quando enviada.');
define('_MI_eNoticias_IMGHEIGHT', 'Altura máxima da imagem:');
define('_MI_eNoticias_IMGHEIGHTDSC', 'Configure a altura máxima permitida a uma imagem quando enviada.');
define('_MI_eNoticias_USETHUMBS', 'Thumbnails:');
define('_MI_eNoticias_USETHUMBSDSC', 'O módulo criará thumbnails para imagens. Configure para \'Não\' para usar a imagem padrão se o servidor não suportar esta opção.');
define('_MI_eNoticias_UPLOADDIR', 'Diretório de imagem:');
define('_MI_eNoticias_UPLOADDIRDSC', 'Especifique o diretório para armazenar as imagens. (Sem barra \'/\')');
define('_MI_eNoticias_DATEFORMAT', 'formato da Data:');
define('_MI_eNoticias_DATEFORMATDSC', 'Configures o formato da data para os artigos.');
define('_MI_eNoticias_PERPAGE', 'Número máximo de artigos por página (Lado administrativo):');
define('_MI_eNoticias_PERPAGEDSC', 'Número máximo de artigos por página para ser exibidos por vez na Admin dos artigos.');
define('_MI_eNoticias_PERPAGEINDEX', 'Número máximo de artigos por página (Lado visitantes):');
define('_MI_eNoticias_PERPAGEINDEXDSC', 'Número máximo de artigos por página para ser exibidos por vez para os visitantes do site.');
define('_MI_eNoticias_ALLOWCOMMENTS', 'Controlar comentário no nível de história:');
define(
    '_MI_eNoticias_ALLOWCOMMENTSDSC',
    'Se você configurar esta opção para "Sim", você só poderá ver os comentários apenas nos artigos que tiverem seus checkbox de comentários marcados no form. de amdin.. <br><br>Selecione "Não" para ter os comentários gerenciados em nível global (Olhe abaixo sobre a tag "Regras de Comentários".'
);
define('_MI_eNoticias_ALLOWADMINHITS', 'Contar leituras feitas pelo admin:');
define('_MI_eNoticias_ALLOWADMINHITSDSC', 'Permitir as leituras dos administradores contar as estatísticas?');
define('_MI_eNoticias_AUTOAPPROVE', 'Auto-aprovar Notícias:');
define('_MI_eNoticias_AUTOAPPROVEDSC', 'Auto aprovar artigos enviados sem a intervenção dos admin.');
define('_MI_eNoticias_MOREARTS', 'Notícias do bloco de autores:');
define('_MI_eNoticias_MOREARTSDSC', 'Especifique o número de artigos para exibir na caixa lateral.');
define('_MI_eNoticias_DISPLAYEMAILADDRESS', 'Mostrar endereços de e-mail:');
define('_MI_eNoticias_DISPLAYEMAILADDRESSDSC', 'Mostrar endereços de e-mail.');
define('_MI_eNoticias_DISPLAYEMAILADDRESSPROT', 'Proteger endereço de e-mail ??');
// Names of admin menu items
define('_MI_eNoticias_ADMENU1', 'Índice');
define('_MI_eNoticias_ADMENU2', 'Colunas');
define('_MI_eNoticias_ADMENU3', 'Notícias');
define('_MI_eNoticias_ADMENU4', 'Permissões');
define('_MI_eNoticias_ADMENU5', 'Blocos');
define('_MI_eNoticias_ADMENU6', 'Ir para o módulo');
//Names of Blocks and Block information
define('_MI_eNoticias_enotRATED', 'Notícias mais votadas');
define('_MI_eNoticias_enotNEW', 'Notícias recentes');
define('_MI_eNoticias_enotTOP', 'Notícias mais lidas');
// Text for notifications
define('_MI_eNoticias_GLOBAL_NOTIFY', 'Global');
define('_MI_eNoticias_GLOBAL_NOTIFYDSC', 'Opções de notificação global.');
define('_MI_eNoticias_COLUMN_NOTIFY', 'Coluna');
define('_MI_eNoticias_COLUMN_NOTIFYDSC', 'Opção de notificação que aplica a coluna corrente.');
define('_MI_eNoticias_ARTICLE_NOTIFY', 'Artigo');
define('_MI_eNoticias_ARTICLE_NOTIFYDSC', 'Opção de notificação aplicável ao artigo atual.');
define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFY', 'Nova coluna');
define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFYCAP', 'Notifique-me quando uma nova coluna for criada.');
define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFYDSC', 'Receba uma notificação quando uma coluna é criada.');
define('_MI_eNoticias_GLOBAL_NEWCOLUMN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notificação : Nova coluna');
define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFY', 'Modificar requisição de artigo');
define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFYCAP', 'Notifique-me de qualquer requisição de modificação de artigo.');
define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFYDSC', 'Receive notification when any article modification request is submitted.');
define('_MI_eNoticias_GLOBAL_ARTICLEMODIFY_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notify : Article modification requested');
define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFY', 'Artigo quebrado enviado');
define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFYCAP', 'Notifique-me sobre qualquer artigo quebrado reportado.');
define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFYDSC', 'Receiba uma notificação quando qualquer artigo quebrado for enviado.');
define('_MI_eNoticias_GLOBAL_ARTICLEBROKEN_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notificação : Artigo quebrado reportado');
define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFY', 'Artigo enviado');
define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFYCAP', 'Notifique-me quando qualquer artigo for submetido e estiver esperando aprovação.');
define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFYDSC', 'Receba notificação quando qualquer novo arquivo for enviado e estiver esperando aprovação.');
define('_MI_eNoticias_GLOBAL_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notificação : Novo artigo enviado');
define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFY', 'Novo artigo');
define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFYCAP', 'Notifique-me quando qualquer novo artigo for publicado.');
define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFYDSC', 'Receba notificação quando um novo artigo for publicado.');
define('_MI_eNoticias_GLOBAL_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notificação : Novo artigo');
define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFY', 'Artigo enviado');
define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFYCAP', 'Notifique-me quando um novo artigo for submetido e estiver esperando aprovação na coluna atual.');
define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFYDSC', 'Receba notificação quando um novo artigo for enviado e estiver esperando aprovação na coluna atual.');
define('_MI_eNoticias_COLUMN_ARTICLESUBMIT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notificação : Novo arquivo enviado na coluna');
define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFY', 'Novo artigo');
define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFYCAP', 'Notifique-me quando um novo artigo for enviado a esta coluna.');
define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFYDSC', 'Receba uma notificação quando um novo artigo é postado nesta coluna.');
define('_MI_eNoticias_COLUMN_NEWARTICLE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notificação : Novo artigo na caluna');
define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFY', 'Notícias aprovadas');
define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFYCAP', 'Notificar-me quando este artigo for aprovado.');
define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFYDSC', 'Receive notification when this article is approved.');
define('_MI_eNoticias_ARTICLE_APPROVE_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE} auto-notificação : Artigo aprovado');
define('_MI_eNoticias_COLSINMENU', 'Colunas no menu?');
define('_MI_eNoticias_COLSINMENUDSC', 'Mostrar os nomes das colunas no menu?');
define('_MI_eNoticias_COLSPERINDEX', 'Colunas na página de índice?');
define('_MI_eNoticias_COLSPERINDEXDSC', 'Quantas colunas aparecerão no índice? [Padrão = 3]');
define('_MI_eNoticias_ALLOWEDSUBMITGROUPS', 'Quais grupos podem enviar?');
define('_MI_eNoticias_ALLOWEDSUBMITGROUPSDSC', 'Grupos de visitantes que podem enviar artigos.');
