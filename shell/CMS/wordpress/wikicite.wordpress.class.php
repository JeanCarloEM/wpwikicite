<?php

/* CLASSE */

class GuinapressPack extends GuinaPressParaWordpress {

  var $wikicite;
  var $wikilink;

  /* FUNÇÃO CHAMANDA PARA INSTALAR O PLUGIN */

  function instalar() {
    /* adição obrigatória, mesmo sem conteúdo, abstrata */
  }

  /* FUNÇÃO CHAMADA PARA DESINSTALAR O PLUGIN */

  function desinstalar() {
    /* adição obrigatória, mesmo sem conteúdo, abstrata */
  }

  public function __construct() {
    /* CHAMANDO CONSTRUCT PAI */
    parent::__construct();

    /* CRIANDO OBJETOS */
    $this->createObjects();
  }

  /*
   * CRIA TODOS OS OBJETOS DAS CLASSE EXISTENTES EM /MÓDULO
   */

  public function createObjects() {
    $this->wikicite = new wikicite();
    $this->wikilink = new wikilink();
  }

  /*
   * Esta funcção adiciona os HOOKS Wordpress
   * Ela é automaticamente chamada por GuinaPress::__construct()
   * Não há necessidade portanto de chamá-la manualmente, exceto se sobrepor
   * o construct
   */

  public function setHooks() {
    /* adição obrigatória, mesmo sem conteúdo, abstrata */
  }

}

?>
