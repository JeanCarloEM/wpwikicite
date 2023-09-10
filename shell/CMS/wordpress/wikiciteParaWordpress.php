<?php

abstract class GuinaPressParaWordpress extends GuinaPress {

  private $autoHeadReursoSend = Array();
  private $autoHeadReursoSendEnable = false;

  public function __construct() {
    /* AUTO SEND HABILITADO */
    $this->autoHeadReursoSendEnable = true;

    /* tenta adicionar os recursos referente à classe no motor Guinalz */
    $this->setRecursos();

    /* ADICIONANDO HOOKS */
    $this->__setHooks();
  }

  /*
   * Esta Classe Padrão, registra os recursos CSS e JS lincados ao motor Guinalz
   * referente ao nome da classe.
   *
   * Ela Poderá ser sobrepósta em classes derivadas, porém o ideal é chamá-la
   * com parent::__construct()
   *
   */

  public function setRecursos() {

  }

  final protected function __setHooks() {
    /* HEAD TRATTER, DEFAULT */
    add_action('init', array(&$this, 'sendDefaultHeadRecurso'));

    $this->setHooks();
  }

  /* esta função deve ser definida em filho */

  abstract function setHooks();

  /* FUNÇÃO CHAMANDA PARA INSTALAR O PLUGIN */

  abstract function instalar();

  /* FUNÇÃO CHAMADA PARA DESINSTALAR O PLUGIN */

  abstract function desinstalar();

  /*
   * wp_enqueue_style
   * wp_enqueue_script
   *
   */

  public function sendDefaultHeadRecurso() {
    if ($this->autoHeadReursoSendEnable)
      foreach ($this->autoHeadReursoSend as $key => $value) {
//      echo "<hr>".$value['func']." :: ".$value['nome']."<hr>";flush();
        if ((function_exists($value['func'])) && (isset($value['nome'])))
          $value['func']($value['nome']);
      }
  }

  public final function addHeadCSS($cssArr) {
    $this->addHeadRecurso('wp_register_style', $cssArr);
  }

  public final function addHeadJS($jsArr) {
    $this->addHeadRecurso('wp_register_script', $jsArr);
  }

  /*
   * wp_register_style
   * wp_register_script
   *
   */

  public final function addHeadRecurso($funcTipo, $recArr) {
    if ($this->autoHeadReursoSendEnable) {
      if (function_exists($funcTipo)) {
        if (is_array($recArr)) {
          if ((!isset($recArr['nome'])) && (($recArr >= 1))) {
            foreach ($recArr as $key => $value)
              if (isset($value['nome'])) {
                /* ADDICIONANDO CSS */
                $funcTipo(
                        $value['nome'], $value['url'], $value['deps'], $value['ver'], isset($value['media']) ? $value['media'] : (isset($value['in_footer']) ? $value['in_footer'] : '')
                );

                $this->autoHeadReursoSend[]['nome'] = $value['nome'];
                $this->autoHeadReursoSend[count($this->autoHeadReursoSend) - 1]['func'] = ($funcTipo === 'wp_register_style') ? 'wp_enqueue_style' : 'wp_enqueue_script';
              }
          } else if (isset($recArr['nome'])) {
            /* ADDICIONANDO CSS */
            $funcTipo(
                    $recArr['nome'], $recArr['url'], $recArr['deps'], $recArr['ver'], isset($recArr['media']) ? $recArr['media'] : (isset($recArr['in_footer']) ? $recArr['in_footer'] : '')
            );

            $this->autoHeadReursoSend[]['nome'] = $recArr['nome'];
            $this->autoHeadReursoSend[count($this->autoHeadReursoSend) - 1]['func'] = ($funcTipo === 'wp_register_style') ? 'wp_enqueue_style' : 'wp_enqueue_script';
          }
        } else
          new Exception('addHeadRecurso, a lista de css não é válida');
      } else
        new Exception('addHeadRecurso, a função passada não existe.');
    } else
      new Exception('addHeadRecurso não foi inicializado com parent::__construct().');
  }

  /* RETORNA O NOME DESTE PLUGIN */

  static function plugin_name() {
    return trim(str_replace(WP_PLUGIN_DIR . '/', '', self::plugin_path()));
  }

  /*
   * RETONA A URL DESTE PLUGIN
   */

  static public function plugin_url() {
    return self::reference_file_url(self::plugin_path);
  }

  /*
   * retona o path da pasta deste plugin
   */

  static public function plugin_path() {
    if (!defined(WIKICITE_PLUGIN_PATH))
      $realpath = dirname(__FILE__);
    else
    if (file_exists(WIKICITE_PLUGIN_PATH)) {
      if (is_dir(WIKICITE_PLUGIN_PATH)) {
        $realpath = WIKICITE_PLUGIN_PATH;
      } else
        $realpath = dirname(WIKICITE_PLUGIN_PATH);
    } else
      $realpath = dirname(__FILE__);

    return $realpath;
  }

  /*
   *  retornoa o caminho parcial a partir da pasta de plugins WP
   * para o aquivo ou diretório com caminho completo em path
   *
   * @Param $path, caminho comleto para uma pasta ou arquivo
   *
   * retona um caminho parcial a partid da pasta de plugins WP
   */

  static function reference_file_path($path) {
    $path = str_replace(WP_PLUGIN_DIR . '/', '', $path);
    return $path;
  }

  /*
   *  retornoa o caminho URL parcial a partir da raíz de plugins WP
   * para o aquivo ou diretório com caminho completo em path
   *
   * @Param $path, caminho comleto para uma pasta ou arquivo
   * retorna a url completa
   */

  static function reference_file_url($path) {
    $path = self::reference_file_path($path);
    return trailingslashit(WP_PLUGIN_URL . '/' . $path);
  }

}

?>
