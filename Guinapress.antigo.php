<?php

/**
 * @package Guinapress para Wordpress
 * @version 1.0.0.0
 */
/*
 * FUNÇÃO
 * DESCROBRE O PATH DO PLUGIN
 * CONSIDERANDO QUE O PATH DESTE ARQUIVO SÃO DOIS, O REAL E O DA PASTA SIMBOLICA:
 *
 * @PARAM   string    $real       o path real do mediwiki onde este arquivo está
 * @PARAM   string    $simbolci   o path completo do link simbólico onde o
 *                                mediwiki é espelhado e onde este arquivo está
 *                                presente
 *
 * @RETURN  string    Retorna o path real/simbolico para o plugin, ou FALSE
 *                    em caso de insucesso
 */
function getPathEIncluiMotor($real, $simbolic) {

  function check($pasta, $relativePath) {
    if (file_exists("$pasta/$relativePath")) {
      die("$pasta/$relativePath");

      # INCLUI O MOTOR
      require_once ("$pasta/$relativePath");

      # RETORNA O PATH DO MOTOR
      return "$pasta/$relativePath";
    } else
      return false;
  }

  $relativePath = "__MOTOR__/MOTOR.php";

  /* PROCURANDO ATÉ NIVEIS SUPERIORES */
  $pasta = procurarNivelSuperiorCom(Array("__MOTOR__", "__DOM__", "__EXTERNO__"), $real);

  # VERIFICANDO SE REALMENTE ENCONTROU
  if (($pasta !== FALSE) && (check($pasta, $relativePath))) {
    return "$pasta/$relativePath";
  } else {
    /* NÃO ENCONTROU, PROCURANDO PELO PATH SIMBOLICO */
    /* PROCURANDO ATÉ NIVEIS SUPERIORES */
    $pasta = procurarNivelSuperiorCom(Array("__MOTOR__", "__DOM__", "__EXTERNO__"), $simbolic);

    # VERIFICANDO SE REALMENTE ENCONTROU
    if (($pasta !== FALSE) && (check($pasta, $relativePath))) {
      return "$pasta/$relativePath";
    } else {
      return false;
    }
  }
}

/*
 * LOCALIZA A PASTA SUPERIOR (RECURSIVAMENTE) QUE POSSUA
 * COMO CONTEÚDO A(S) PASTA(S) E/OU ARQUIVO(S) PRESENTES EM $procurado
 *
 * @PARAM   mixed   $procurado    Array ou string com o nome dos arquivos a
 *                                serem procurados
 * @PARAM   string  $pasta        O path da pasta inicial, a partir da qual deverá
 *                                ser localizado os arquivos
 * @RETURN  string  o path da pasta que contem o(s) arquivos/pastas, ou FALSE
 *                  em caso de insucesso
 */

function procurarNivelSuperiorCom($procurado, $pasta) {
  $files_exists = function($array, $pasta) {
    $existe = true;

    if (is_array($array)) {
      foreach ($array as $path)
        if (!file_exists("$pasta/$path")) {
          $existe = false;
          break;
        }
    } else {
      if (!file_exists("$pasta/$array"))
        $existe = false;
    }

    return $existe;
  };

  # CORRIGINDO E DEFINIDO A PASTA
  if (is_file($pasta))
    $pasta = dirname($pasta);

  # PROCURANDO O MOTOR
  while ((!$files_exists($procurado, $pasta)) && (( $pasta !== "/") && (preg_match('/^[a-zA-Z]\:\/$/i', trim($pasta)) == 0) && (preg_match('/^[a-zA-Z]\:\\$/i', trim($pasta)) == 0) && (preg_match('/^[a-zA-Z]\:\\\\$/i', trim($pasta)) == 0))) {
    $pasta = dirname($pasta);
  }

  # VERIFICANDO SE REALMENTE ENCONTROU
  if ($files_exists($procurado, $pasta)) {
    return $pasta;
  } else {
    return false;
  }
}

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

/* VERIFICANDO SE ESTÁ TUDO CERTO */
if (file_exists(WORDPRESS_ROOT_PATH)) { /* EXITE? */
  if (file_exists(WORDPRESS_SIMBOLIC_PATH)) { /* EXITE? */
    if ((defined("SITE_RAIZ")) && (file_exists(SITE_RAIZ))) { /* EXITE? */
      if (getPathEIncluiMotor(WORDPRESS_ROOT_PATH, WORDPRESS_SIMBOLIC_PATH) !== false) {
        # ... INCLUINDO
        MOTOR::engine("guinapress");
      } else # ... ERRO
        die("ERRO PREVISTO: <b>MOTOR</b> e <b>PLUGIN</b> não localizado <I>'" . $_SERVER["SCRIPT_NAME"] . "'</i>");
    } else /* SITE_RAIZ não existe */
      die("ERRO PREVISTO: <b>SITE_RAIZ</b> nao definido em <I>'" . $_SERVER["SCRIPT_NAME"] . "'</i>");
  } else /* WORDPRESS_SIMBOLIC_PATH não existe */
    die("ERRO PREVISTO: <b>WORDPRESS_SIMBOLIC_PATH</b> nao definido em <I>'" . $_SERVER["SCRIPT_NAME"] . "'</i>");
} else /* WORDPRESS_ROOT_PATH não existe */
  die("ERRO PREVISTO: <b>WORDPRESS_ROOT_PATH</b> nao definido em <I>'" . $_SERVER["SCRIPT_NAME"] . "'</i>");
?>
