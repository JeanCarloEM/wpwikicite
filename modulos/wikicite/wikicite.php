<?php

define('WIKICITE_V', '1.0.0');

global $wgAllowCiteGroups;
$wgAllowCiteGroups = true;

/* INCLUINDO FUNCÇÕES E CLASSES DO MEDIAWIKI, UTILIZADOS NESTA PLUGIN */
require_once ( WIKICITE_DEPENDENCIAS_MEDIAWIKI . '/Sanitizer.php' );

/* INCLUINDO A CLASSE PRINCIPAL */
require_once ( ArrumarPath( __DIR__ ) . 'wikicite.class.php' );

/* INCLUINDO OS ARQUIVOS DO PLUGIN MEDIAWIKI CITE */
require_once ( WIKICITE_DEPENDENCIAS_MEDIAWIKI . '/Cite.i18n.php' );

?>