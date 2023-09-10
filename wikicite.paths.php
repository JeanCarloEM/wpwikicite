<?php

/* PATH DA PASTA SHELL */
define('GP_SHELL_PATH', ghp_files::dirname(__FILE__) . "/shell");

/* PATH DA PASTA MÓDULOS */
define('GP_MODULOS_PATH', ghp_files::dirname(__FILE__) . "/modulos");

/* PATH DA PASTA RECURSOS, RECURSOS EXTERNOS AO GUINALZ, COMO MEDIAWIKI */
define('GP_RECURSOS_PATH', ghp_files::dirname(__FILE__) . "/recursos");

/* PATH DA PASTA CSS DE GUINALZI */
define('GP_CSS_PATH', GP_RECURSOS_PATH . "/css");

/* PATH DA PASTA JS DE GUINALZ */
define('GP_JSS_PATH', GP_RECURSOS_PATH . "/jss");

/* PATH DA PASTA JS DE GUINALZ */
define('GP_IMG_PATH', GP_RECURSOS_PATH . "/img");


/* PATH DA PASTA DE RECURSOS, RECURSOS EXTERNOS AO GUINALZ, COMO MEDIAWIKI */
define('GP_DEPENDENCIAS_PATH', ghp_files::dirname(__FILE__) . "/dependencias");

/* PATH DA PASTA DO RECURSO MEDIAWIKI */
define('GP_DEPENDENCIAS_MEDIAWIKI', GP_DEPENDENCIAS_PATH . "/mediawiki");
?>
