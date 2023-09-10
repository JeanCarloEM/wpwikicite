<?php

class wikilink extends GuinaPressParaWordpress {
  /* POSSUI OS ENDEREÇOS DOS SITES PADRÃO, JÁ PREPARADO PARA SOBRPOSIÇÃO */

  var $lincarPara = array(
      /* SITES MEDIAWIKI */
      'wm' => Array(
          Array(
              'w' => Array(
                  'http://%2$s.wikipedia.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'wikt' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'b' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'n' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'q' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'm' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              's' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'commons' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'wikispecies' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'voy' => Array(
                  'http://%2$s.wiktionary.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
          ),
      ),
      /* SITES WIKINALZ */
      'wn' => Array(
          Array(
              's' => Array(
                  'http://%2$s.wikiscrito.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              ),
              'n' => Array(
                  'http://%2$s.wikinalz.org/wiki/%1$s',
                  'pt', /* valor padrão, equivale ao $param de ProcessarURL */
                  '' /* valor padrão, equivale ao $att de ProcessarURL */
              )
          ),
      ),
  );

  /* modelo de TAG utilizada pelo processador */
  var $tagA = '<a href="%1$s" title="%2$s" target="%3$s" class="wikilink">%2$s</a>';

  /* FUNÇÃO CHAMANDA PARA INSTALAR O PLUGIN */

  function instalar() {
    /* adição obrigatória, mesmo sem conteúdo, abstrata */
  }

  /* FUNÇÃO CHAMADA PARA DESINSTALAR O PLUGIN */

  function desinstalar() {
    /* adição obrigatória, mesmo sem conteúdo, abstrata */
  }

  /*
   * Esta funcção adiciona os HOOKS Wordpress
   * Ela é automaticamente chamada por GuinaPress::__construct()
   * Não há necessidade portanto de chamá-la manualmente, exceto se sobrepor
   * o construct
   */

  public function setHooks() {
    // WordPress Hooks
    add_filter('the_content', array(&$this, 'processar'));
  }

  /*
   * PROCESSA O CONTEÚDO PROCURANDO A MARCAÇÃO [[]]
   *
   * @param $conteudo, é o conteúdo passado pelo wordpress no filtro the_content
   *
   * @return, retorna o conteúdo processado, com as devidas tag convertidas
   *          para marcação HTML "<a>"
   */

  public function &processar($conteudo) { /*
   * EFETUANDO A PESQUISA DE TOTO CONTEÚDO ENTTRE DOIS [[ E ]]
   * EM $conteudo E COLOCANDO O RSULTADOS DA PESQUISA E $resultado
   */
//		preg_match_all('/\[\[([^\]]+)\]\]/', $conteudo, $resultado);
    preg_match_all('/.?\[\[([^\]]+)\]\].?/', $conteudo, $resultado);

    /*
     * $resultado, é um array de mutidimencional, ele possui dois arrays
     *
     * $resultado[0] é uma array com a lista (string) de resultados junto com
     *               os [[, por exemplo [[ISO_639]]
     *
     * $resultado[1] é uma array com a lista (string) de resultados se os [[
     *               por exeplo ISO_639
     * agora vamos substituir o conteudo do array $resultado[1], pelo código
     * HTML <a>
     *
     */
    foreach ($resultado[1] as $indice => $valor) {
//          echo "|".$resultado[0][$indice] . " - " . substr(trim($resultado[0][$indice]), 0, 1);
      /* VERIFICANDO SE ESTE FOI ESCAPADO, POR QUE SE FOI NÃO PODEMOS TRATAR */
      if (substr(trim($resultado[0][$indice]), 0, 1) === '\\') {
//          echo " Excetuando :: " . ' <span>' . substr(trim($resultado[0][$indice]), 1, 2) . '</span>' . substr(trim($resultado[0][$indice]), 3) ." <hr>"; flush();
        $resultado[1][$indice] = ' <span>' . substr(trim($resultado[0][$indice]), 1, 1) . '</span>' . substr(trim($resultado[0][$indice]), 2); /* ESCAPADO, NÃO TRATANDO */
      } else {/* TRATANDO */
//          echo " Tratando <hr>"; flush();
        /* inicializando, para tratar o último caractere */
        $charFinal = '';
        $CharAdd = '';

        /* obtendo o último caractere */
        $chr = substr($resultado[0][$indice], -1);

        if (($chr === ",") || ($chr === " ") || ($chr === ".") || ($chr === "<")) {
          $CharAdd = $chr; /* exclui do link e inclui no final */
        } else {
          $charFinal = $chr; /* incui no link e exclui do final */
        }

        /* criando o link com base no conteudo das [[ e ]], e substituindo o
         * valor original no array
         */
        $resultado[1][$indice] = substr($resultado[0][$indice], 0, 1) . /* restitui o caractre inicial, */
                $this->CriarLink($valor, $charFinal) . /* se for o caso inclu no link o caractere final */
                $CharAdd; /* e se for o caso restitui o caractere final */
      }
    }

    /*
     * agora temos dois array, $resultado[0], com o valor original inclusive
     * [[ e ]], e $resultado[1] com a tag pronta para ser inserida no HTML
     * Basta agora fazermos a substituição usando str_replace
     * Sibstituindo cada ocorrencia de $resultado[0] por $resultado[1]
     *
     */
    $conteudo = str_replace($resultado[0], $resultado[1], $conteudo);

    return $conteudo;
  }

  /*
   * CRIA UMA TAG DE LINK <A> BASEAD NO TEXTO $STR
   *
   * @param $str, é o texto éxistente entrer [[ e ]], que possibilita a criação
   *        de um link através da TAG a
   *
   * @return, o retorno será a tag HTML a, pronta para inserção no HTML
   *
   */

  public function CriarLink($str, $charFinal = '') {
    if (strpos($str, '|') === false) {
      $link = $nome = trim($str);
    } else {
      list($link, $nome, $AbrirEm) = explode('|', trim($str));

      if (trim($nome) === '') {
        $nome = $link;
      } else
        $nomeDefinido = true; /* O NOME FOI DEFINIDO MANUALMENTE */
    }

    /* se começar com http:// entao é um link de verdade, nada a fazer */
    if (strpos(strtolower($link), 'http://') === 0) {
      $link = strtolower(trim($link));
    } else { /* se não começar, então teremos que ver direito */
      /* a inexistencia de dois pontos indica link para este sitio */
      if (strpos($link, ':') === false) {
        $link = trim($link);
      } else {/* a existência por outro lado indica se tratar de um sitio externo */
        /* vamos separar as partes */
        $partes = explode(':', $link);

        /* o link em si, ou o nome da página sempe deve se a útima parte */
        $link = trim($partes[count($partes) - 1]);

        /* ELIMINANDO O REGISTRO DE LINK */
        unset($partes[count($partes) - 1]);

        /* REPROCESANDO $link PARA SER PASSADO PARA $this->ProcessarURL sem o Link */
        $param = Array();
        foreach ($partes as $key => $value)
        /* ignorando o último, que é  link */
          if (($key < count($partes)) && (isset($value))) {
            $param[] = $value;
          }

        /* setando o nome sem os dois pontos, caso já não esteja definido */
        if (!$nomeDefinido)
          $nome = $link;
      }

      /* vamos processar a url com base nas tags, considerando o máximo de 3 tags */
      $link = $this->ProcessarURL($link, $param);
    }

    /* se houver # temos que retirar, mas somente aqui, antes dah erro */
    if (strpos($nome, '#') === 0)
      $nome = substr($nome, 1);

    return $this->CriaTagLink($link, $nome . $charFinal, $AbrirEm);
  }

  /*
   * Prepara uma URL
   * USADO QUANDO O LINK NÃO POSSI HTTP:// NO INÍCIO, COM FIM DE DESCOBRIR
   * A URL CORRETA
   *
   * @param $página, é uma sctring com o endereço ou o nome da pagina
   *
   * @param $Argumentos é um array que contem os argumentos para processamento
   *         da url:
   *
   *       [0] = o indice que identifica o sitio ou o grupo
   *
   *       [1] = se [0] for sitio, este representa argumentos, como idioma,
   *             se [0] for um grupo, este representa a indenficação do sitio
   *
   *       [2] = se [0] for grupo, então este é um paramentro como idioma
   *             se [0] for sitio ete é o $atributos adicionais
   *
   *       [3] = se [0] for grupo este é um atributo adicional
   *
   * @return: retorna a url completa e pronta com as subtituições
   *          necessárias realizadas apartir dos parametros
   *
   */

  public function ProcessarURL($pagina, $argumentos) {
    /* evitando espaços e falhas */
    if ((is_array($argumentos)) && (isset($argumentos[0])) && ($argumentos[0] !== null))
      $sitio = trim($argumentos[0]);
    else
      $sitio = '';

    if ($sitio === '') { /* É UM LINK PARA PÁGINA/POST DESTE BLOG */
      $pagina = trim($pagina);

      if (strpos($pagina, '#') === 0) /* linca um titulo ou parte da página atual */
        $url = get_permalink() . '%1$s'; /* a URL da página ou POST */
      else
        $url = get_site_url() . '/%1$s'; /* obtem a URL do site */
    }else if (isset($this->lincarPara[strtolower($sitio)])) {/* É UM LINK PARA SITE EXTERNO */
      /* CONFIGURANDO OS PARAMENTRO */
      $param = $argumentos[1];
      $att = $argumentos[2];

      /* DETERMINANDO O ÍNDICE */
      $indice = strtolower($sitio);

      /* OBTENDO O O ARRAY DO SITE EXTERNO ATRAVES DO ARRAY */
      $externo = $this->lincarPara;

      /*
       * SE FOR UM SUBARRAY, ENTÃO É UM GRUPO
       * REDEFININDO INFORMAÇÕES PARA APONTAR PARA O SUBARRAY
       */
      if (is_array($this->lincarPara[$indice][0])) {
        /* avançando, RECONFIGURANDO O SITIO, para o correto em $externo */
        if ((isset($argumentos[1])) && ($argumentos[1] !== null))
          $sitio = trim($argumentos[1]); /* SITIO NA VERDADE É O PRÓXIMO ARGUMENTO */
        else {
          return $sitio; /* ERRO */
        }

        /* OBTENDO O O ARRAY DO SITE EXTERNO ATRAVES DO SUBARRAY */
        $externo = $this->lincarPara[$indice][0];

        /* ERRO, RETORNAMOS, POIS TEMOS UM PROBLEMA */
        if (!isset($externo[strtolower($sitio)])) { /* temos um problema, não casa com nada */
          return $sitio; /* devolvemos sem fazer qualquer alteração */
        } else {
          /* sitio, foi redefinido, então temos que redeterminar o indice */
          $indice = strtolower($sitio);

          /* RECONFIGURANDO OS PARAMETROS */
          $param = $argumentos[2];
          $att = $argumentos[3];
        }
      }

      /* SETANDO A URL */
      $url = $externo[$indice][0];
      /* se não houver sido passado o $param, obtemos o padrão */
      $param = $param == '' ? $externo[$indice][1] : $param;

      /* se não houver sido passado $att, pbtemos o padrão */
      $att = $att == '' ? $externo[$indice][2] : $att;
    } else { /* temos um problema, não casa com nada */
      return $sitio; /* devolvemos sem fazer qualquer alteração */
    }

    /* RETORNANDO COM AS SUBSTITUIÇÕES NECESSÁRIAS */
    return sprintf($url, $pagina, $param, $att);
  }

  /*
   * CRIA A TAG <a> com as informações passadas no parametro
   *
   * @param $titulo é o texto vísivel marcado como link, o texto que vem
   *         entras as tag HTML "<a>"
   * @param $link, é o caminho DNS completo para a página específica começando
   *        com HTTP://
   * @param $title, é o atributo title da tag HTML <a>
   *
   * @return: retorna o link com marcação HTML "<a href='*'>link</a>
   *
   */

  public function CriaTagLink($link, $nome, $AbrirEm = "_self") {
    if ((trim($AbrirEm) === '') || ($AbrirEm === false) || ($AbrirEm === null))
      if (strpos(trim($link), get_site_url()) === 0)
        $AbrirEm = "_self";
      else
        $AbrirEm = "_blank";

    return sprintf($this->tagA, trim($link), $nome, trim($AbrirEm));
  }

}

?>
