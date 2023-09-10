<?php

function RemoverBarraFinal($string) {
  return rtrim(rtrim($string, '/'), '\\');
}

function ArrumarPath($string) {
  return str_replace("/", DIRECTORY_SEPARATOR, str_replace("\\", DIRECTORY_SEPARATOR, RemoverBarraFinal($string))) . DIRECTORY_SEPARATOR;
}

function includeAllAPIFiles($directory) {
  includeAllPHPFiles($directory, false);
}

/*
 * @PARAM   string  $dirPath        Caminho a ser procurado
 * @PARAM   bool    $incSubDir      Indica se deve incluir os php de subpastas
 * @PARAM   bool    $incRaizFiles   Indica se deve incluir arquivos php da
 *                                  pasta raiz fornecida em $dirPath
 * @PARAM   bool    $FileIgualDir   Indica se é para incluir apenas arquivos
 *                                  cujo nome sejam igual à pasta em que nestão
 * @PARAM   array   $exArqs         Lista de arquivos a serem ignorados **
 *                                  ** NÃO IMPLEMENTADO
 */

function includeAllPHPFiles($dirPath, $incSubDir = false, $incRaizFiles = true, $FileIgualDir = false, $exArqs = false, $callback = null) {
  # GARANTINDO QUE SEJA UM DIRETÓRIO
  if (is_dir($dirPath) === false)
    $dirPath = dirname($dirPath);

  /* INCLUIR ARQUIVOS PHP DA PASTA RAIZ */
  if ($incRaizFiles !== false)
    if ($FileIgualDir === FALSE) {
      __includeAllPHPFiles_includeAllFiles($dirPath, false, $callback);
    } else {
      $mesmoNome = $dirPath . "/" . basename($dirPath) . ".php";

      if (file_exists($mesmoNome)) {
        require_once ($mesmoNome);

        if (($callback !== null) && (!empty($callback)) && ((is_callable($callback)) || (is_array($callback))))
          \call_user_func($callback, basename($dirPath), $mesmoNome);
      }
    }

  /* INCLUIR SUBDIRETÓRIOS */
  if (($incSubDir !== false) && ($incSubDir > 0)) {
    /* CRIANDO HANDLE DO DIRETÓRIO */
    $handler = opendir($dirPath);

    /* PERCORRER ITENS */
    while ($file = readdir($handler))
    /* SE O ARQUIVO FOR UM DIRETÓRIO E NÃO FOR PASTAS ATUAL E PASTA SUPERIOR */
      if ($file != "." && $file != ".." && is_dir("$dirPath/$file"))
      /* INCLUINDO SUBPASTAS... */
        includeAllPHPFiles("$dirPath/$file", (is_numeric($incSubDir) ? $incSubDir - 1 : $incSubDir), TRUE, $FileIgualDir, $exArqs, $callback);

    /* FECHANDO GANDLE */
    closedir($handler);
  }
}

/*
 *
 */

function __includeAllPHPFiles_includeAllFiles($directory, $Arqs = false, $callback = null) {
  /* CRIANDO HANDLE DO DIRETÓRIO */
  $handler = opendir($directory);

  /* PERCORRER ITENS */
  while ($file = readdir($handler)) {
    /* SE FOR ARQUIVO PHP NÃO PASTA E NÃO NIVEL SUPERIOR OU O PRÓPRIO
     * E NÃO FOR ARQUIVOS EXCLUIDOS EM exArgs */
    if ($file != "." && $file != ".." && $file != "_nopage.php" && !is_dir("$directory/$file")) {
      /* obtendo informações do arquivo */
      $info = pathinfo("$directory/$file");

      /* ADICIONANDO ARQUIVO PHP */
      if (isset($info['extension']))
        if ($info['extension'] === 'php') {
          require_once("$directory/$file");

          if (($callback !== null) && (!empty($callback)) && ((is_callable($callback)) || (is_array($callback))))
            \call_user_func($callback, basename($file, '.php'), "$dirPath/$file/$file");
        }
    }
  }

  /* FECHANDO GANDLE */
  closedir($handler);
}
