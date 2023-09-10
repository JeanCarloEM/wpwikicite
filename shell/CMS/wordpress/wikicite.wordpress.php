<?php

/* DEPURADOR DE ERROS LIGADO */
if ((preg_match("#^(.*\.)?localhost$#si", $_SERVER["SERVER_NAME"]) === 1) && (!DEFINED("WP_DEBUG")))
  define('WP_DEBUG', true);

/*
 *
 * INCLUIDO TODOS OS REQUISITO PARA QUE A API FUNCIONE
 * VÁRIAS CONSTANTS QUE A DEFINEM
 *
 * INCLUI POR PADRÃO SCRIPTNALZ
 */

require_once ( ArrumarPath(__DIR__) . "wikiciteParaWordpress.php" );

/* INCLUINDO TODOS OS MÓDULOS */
includeAllPHPFiles(WIKICITE_MODULOS_PATH, true, true, true);

/*
 * finalmente...
 *
 * INCLUINDO TODOS OS PHPs DESTA RAIZ AINDA NÃO ADICIONADOS,
 * INCLUSIVE A CLASSE GUINALZPACK
 *
 */
includeAllPHPFiles(ArrumarPath(__DIR__));

/* CRIANDO E INICIALIZANDO CLASSE */
GuinapressPack::getInstancia();

/* DEFININDO A VERSÃO DO SISTEMA */
define('WIKICITE_VERSION', GuinapressPack::version);
?>