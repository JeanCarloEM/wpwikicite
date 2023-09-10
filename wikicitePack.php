<?php

/* DEPURADOR DE ERROS LIGADO */
define('WP_DEBUG', true);

/*
 * INCLUINDO MOTOR GUINALZ
 *
 * INCLUIDO TODOS OS REQUISITO PARA QUE A API FUNCIONE
 * VÁRIAS CONSTANTS QUE A DEFINEM
 *
 * INCLUI POR PADRÃO SCRIPTNALZ
 */

# NO DIRETÓRIO SUPERIOR (UP), INCLUIR API.PHP
require_once ( dirname(dirname(__FILE__)) . '/MOTOR.php' );

/* INCLUINDO PATHS, SEM ELE NÃO DAH PARA FAZER O PRÓXIMO PASSO */
requisitar(ghp_files::dirname(__FILE__) . 'Guinapress.paths.php');

/* INCLUINDO TODOS OS PHP SHELL, SEM, ELE NÃO DAH PARA FAZER O PRÓXIMO PASSO */
ghp::includeAllPHPFiles(GP_SHELL_PATH, true, false, true);

/* INCLUINDO TODOS OS MÓDULOS */
ghp::includeAllPHPFiles(GP_MODULOS_PATH, true, true, true);

/*
 * finalmente...
 *
 * INCLUINDO TODOS OS PHPs DESTA RAIZ AINDA NÃO ADICIONADOS,
 * INCLUSIVE A CLASSE GUINALZPACK
 *
 */
ghp::includeAllPHPFiles(ghp_files::dirname(__FILE__));

/* INICIANDO VÁRIVEL DE CLASSE PRINCIPAL */
global $GuinalzWP;

/* CRIANDO E INICIALIZANDO CLASSE */
$GuinalzWP = new GuinapressPack();

/* DEFININDO A VERSÃO DO SISTEMA */
define('GP_VERSION', $GuinalzWP->version);
?>