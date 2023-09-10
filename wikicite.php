<?php

/*
  Plugin Name: Wikicite para Wordpress
  Plugin URI: http://lab.guinalz.com/Guinapress/
  Description: Adapação do cite do mediawiki para Wordpress,
  Version: 1.0.0.0
  Author: Jean Carlo de Elias Moreira (@JeanCarloEM)
  Author URI: http://www.jeancarloem.com/
  License: Copyright &copy; 2012-
 */

/*
 * CÓDIGO PHP, CLASSE QUE CONFIGURA ESTABELECE POSSIBILIDDE DE MULTIPLOS WIKI
 * COM APENAS UMA FONTE
 *
 * BASEADO EM http://www.mediawiki.org/wiki/Manual:Wiki_family#Ultimate_minimalist_solution
 *

 * INCLUINDO MOTOR GUINALZ
 *
 * INCLUIDO TODOS OS REQUISITO PARA QUE A API FUNCIONE
 * VÁRIAS CONSTANTS QUE A DEFINEM
 *
 * INCLUI POR PADRÃO SCRIPTNALZ
 */

/*
 * Necessita das constantes:
 *  - WORDPRESS_ROOT_PATH
 *  - WORDPRESS_SIMBOLIC_PATH
 *  - SITE_RAIZ
 *  - WORDPRESS_ROOT_PATH
 *
 *  * Estas constantes são definidas em mutiwiki.php localizado na pasta raiz
 *    do wikipédia (WORDPRESS_ROOT_PATH)
 */

require_once 'wikicite.functions.php';

# DEFININDO - O PATH REAL (SEM SYMBOLIC LINK) DO WORDPRESS
if (!DEFINED("WORDPRESS_ROOT_PATH")) /* O PATH ABSOLUTE, SEM SYMBOLIC LINKS */
  DEFINE("WORDPRESS_ROOT_PATH", realpath(dirname(__FILE__)) . "/");

# DEFININDO O PATH SILBOLICO DO MEDIWKI
if (!DEFINED("WORDPRESS_SIMBOLIC_PATH")) /* O PATH SIMBOLICO (LINK) */
  DEFINE("WORDPRESS_SIMBOLIC_PATH", dirname($_SERVER["DOCUMENT_ROOT"] . $_SERVER["SCRIPT_NAME"]) . "/");

# VERIFICANDO SE RAIZ EXISTE, E SETANDO SE FOR O CASO, NIVEL ACIMA DE "w"
# VAMOS TENTAR DESCOBRIR O PATH SYMBOLICO DESTA PASTA, QUE POSSIVELMENTE SE CHAMA "w"
if (!DEFINED("SITE_RAIZ")) {
  if (file_exists($_SERVER["DOCUMENT_ROOT"]))
    DEFINE("SITE_RAIZ", $_SERVER["DOCUMENT_ROOT"] . "/"); /* WINDOWS */
}

if ((file_exists(WORDPRESS_ROOT_PATH)) || (file_exists(MEDIAWIKI_ROOT_PATH))) {
  if ((file_exists(WORDPRESS_SIMBOLIC_PATH)) || (file_exists(MEDIAWIKI_SIMBOLIC_PATH))) {
    if (file_exists(SITE_RAIZ)) {
      # DEFININDO O PATH DO PLUGIN
      if (!DEFINED("WIKICITE_PATH"))
        DEFINE("WIKICITE_PATH", ArrumarPath(__DIR__));

      /* PATH DA PASTA SHELL */
      define('WIKICITE_SHELL_PATH', ArrumarPath(__DIR__) . "shell/");

      /* PATH DA PASTA SHELL */
      define('WIKICITE_CMS_PATH', WIKICITE_SHELL_PATH . "CMS/");

      /* PATH DA PASTA MÓDULOS */
      define('WIKICITE_MODULOS_PATH', ArrumarPath(__DIR__) . "modulos/");

      /* PATH DA PASTA RECURSOS, RECURSOS EXTERNOS AO GUINALZ, COMO MEDIAWIKI */
      define('WIKICITE_RECURSOS_PATH', ArrumarPath(__DIR__) . "recursos/");

      /* PATH DA PASTA CSS DE GUINALZI */
      define('WIKICITE_CSS_PATH', WIKICITE_RECURSOS_PATH . "css/");

      /* PATH DA PASTA JS DE GUINALZ */
      define('WIKICITE_JSS_PATH', WIKICITE_RECURSOS_PATH . "jss/");

      /* PATH DA PASTA JS DE GUINALZ */
      define('WIKICITE_IMG_PATH', WIKICITE_RECURSOS_PATH . "img/");

      /* PATH DA PASTA DE RECURSOS, RECURSOS EXTERNOS AO GUINALZ, COMO MEDIAWIKI */
      define('WIKICITE_DEPENDENCIAS_PATH', ArrumarPath(__DIR__) . "dependencias/");

      /* PATH DA PASTA DO RECURSO MEDIAWIKI */
      define('WIKICITE_DEPENDENCIAS_MEDIAWIKI', WIKICITE_DEPENDENCIAS_PATH . "mediawiki/");

      # INCLUINDO O SEHLL GUINAPRESS
      require_once ( WIKICITE_SHELL_PATH . "wikicite.php" );


      # CASA CMS DEVE TRABALHAR PARA INCLUIR OS MÓDULOS E O RESTO EM VISTA QUE
      # CADA SISTEMA POSSUI UMA FORMA INDEPENDENTE DE FUNCIONAMENTO
      if (file_exists(WORDPRESS_ROOT_PATH)) {
        $CMS = "wordpress";
      } else if (file_exists(MEDIAWIKI_ROOT_PATH)) {
        $CMS = "mediawiki";
      }

      # INCLUINDO A ENGINE RESPOSNÁVEL POR CADA CMS
      if (file_exists(WIKICITE_CMS_PATH . "$CMS/wikicite.$CMS.php")) {
        require_once ( WIKICITE_CMS_PATH . "$CMS/wikicite.$CMS.php" );
      } else
        die("ERRO PREVISTO: <b>CMS " . strtoupper($CMS) . "</b> não localizado em <I>'" . $_SERVER["SCRIPT_NAME"] . "' => '" . WIKICITE_CMS_PATH . "$CMS/wikicite.$CMS.php" . "'</i>");
    } else /* SITE_RAIZ não existe */
      die("ERRO PREVISTO: <b>SITE_RAIZ</b> nao definido em <I>'" . $_SERVER["SCRIPT_NAME"] . "'</i>");
  } else /* WORDPRESS_SIMBOLIC_PATH não existe */
    die("ERRO PREVISTO: <b>WORDPRESS_SIMBOLIC_PATH</b> nao definido em <I>'" . $_SERVER["SCRIPT_NAME"] . "'</i>");
} else /* WORDPRESS_ROOT_PATH não existe */
  die("ERRO PREVISTO: <b>WORDPRESS_ROOT_PATH</b> nao definido em <I>'" . $_SERVER["SCRIPT_NAME"] . "'</i>");
?>
