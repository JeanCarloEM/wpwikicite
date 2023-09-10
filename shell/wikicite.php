<?php

abstract class GuinaPress {

  CONST version = "1.0.0.0";

  /*
   * A INSTÂNCIA REAL DO OBJETO
   * COMO ESTA É UMA VARIÁVEL STATICA, SEU VALOR É ATRELADO À CLASSE
   * E NÃO AO OBJETO, ASSIM, QUANDO ATRIBUIMOS UM VALOR À ELA, ATRAVÉS DE CÓDIGO
   * SEU VALOR PASSA A SER ÚNICO INDEPENDENDE DO OBJETO OU DO LOCAL
   * OU SEJA, SEU VALOR É GUARDADO NA CLASSE E NÃO NO OBJETO
   * BASTA USAR: SELF:: PARA ACESSAR O OBJETO CRIADO DINAMICAMENTE ANTERIORMENTE
   */

  private static $InstanciaReal;

  /*
   *  Retorna a Instância de Session
   *  A sessão é automaticamente inicializada se for o caso
   *
   *  @return    Objeto
   */

  public static function &getInstancia() {
    # SE A INSTANCIA NÃO EXISTIR, CRIAMOS
    if ((!isset(self::$InstanciaReal)) || (!is_object(self::$InstanciaReal)))
    # CRIANDO UMA SESSÃO PROTEGIDA
      self::$InstanciaReal = new static();

    # RETORNANDO A SESSÃO
    return self::$InstanciaReal;
  }

}

?>
