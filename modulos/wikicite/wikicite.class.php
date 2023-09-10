<?php

define("CITE_DEFAULT_GROUP", "");

/* CLASSE */

class WikiCite extends GuinaPressParaWordpress {

  /**
   * True se uma tag [ref] estiver sendo processada agora
   * evitar recursão infinita
   *
   * @var boolean
   */
  var $mInCite = false;

  /**
   * Contador para controlar o número total de [ref] útil
   */
  var $mCallCnt = 0;

  /**
   * Group used when in [references] block
   *
   * @var string
   */
  var $mReferencesGroup = '';

  /**
   * True quando a tag [references] está sendo processada.
   * Utilizado para detectar a utilização de [referências] para definir refs
   *
   * @var boolean
   */
  var $mInReferences = false;

  /**
   * Error stack used when defining refs in <references>
   *
   * @var array
   */
  var $mReferencesErrors = array();

  /**
   * Datastructure representing <ref> input, in the format of:
   * <code>
   * array(
   * 	'user supplied' => array(
   * 		'text' => 'user supplied reference & key',
   * 		'count' => 1, // occurs twice
   * 		'number' => 1, // The first reference, we want
   * 		               // all occourances of it to
   * 		               // use the same number
   * 	),
   * 	0 => 'Anonymous reference',
   * 	1 => 'Another anonymous reference',
   * 	'some key' => array(
   * 		'text' => 'this one occurs once'
   * 		'count' => 0,
   * 		'number' => 4
   * 	),
   * 	3 => 'more stuff'
   * );
   * </code>
   *
   * This works because:
   * * PHP's datastructures are guaranteed to be returned in the
   *   order that things are inserted into them (unless you mess
   *   with that)
   * * User supplied keys can't be integers, therefore avoiding
   *   conflict with anonymous keys
   *
   * @var array
   * */
  var $mRefs = array();

  /**
   * <ref> call stack
   * Used to cleanup out of sequence ref calls created by #tag
   * See description of function rollbackRef.
   *
   * @var array
   */
  var $mRefCallStack = array();

  /**
   * Count for user displayed output (ref[1], ref[2], ...)
   *
   * @var int
   */
  var $mOutCnt = 0;
  var $mGroupCnt = array();

  /**
   * The backlinks, in order, to pass as $3 to
   * 'cite_references_link_many_format', defined in
   * 'cite_references_link_many_format_backlink_labels
   *
   * @var array
   */
  var $mBacklinkLabels;

  # Ids produced by <ref>

  const cite_reference_link_prefix = 'cite_ref-';
  const cite_reference_link_suffix = '';
  const cite_reference_link = '<sup id="%1$s" class="reference"><a href="#%2$s">[%3$s]</a></sup>';
  const cite_reference_link_key_with_num = '%1$s_%2$s';

  # Ids produced by <references>
  const cite_references_link_prefix = 'cite_note-';
  const cite_references_link_suffix = '';
  const cite_references_prefix = '<div id="reference_list" class="collapse wikicite references reference_list"><input type="checkbox" /><div class="wikicite_expand"><span>&#11021;</span></div><ol class="references colunas colunas2 col2">';
  const cite_references_suffix = '</ol></div>';
  const cite_references_link_one = '<li id="%1$s" class="%4$s"><span class="mw-cite-backlink"><!--<span class="id">%5$s.</span>--><a href="#%2$s">↑</a></span> %3$s</li>';
  const cite_references_no_link = '<p id="%1$s">%2$s</p>';
  const cite_references_link_many_format = '<sup><a href="#%1$s">%2$s</a></sup>';
  const cite_references_link_many_format_backlink_labels = 'a b c d e f g h i j k l m n o p q r s t u v w x y z aa ab ac ad ae af ag ah ai aj ak al am an ao ap aq ar as at au av aw ax ay az ba bb bc bd be bf bg bh bi bj bk bl bm bn bo bp bq br bs bt bu bv bw bx by bz ca cb cc cd ce cf cg ch ci cj ck cl cm cn co cp cq cr cs ct cu cv cw cx cy cz da db dc dd de df dg dh di dj dk dl dm dn do dp dq dr ds dt du dv dw dx dy dz ea eb ec ed ee ef eg eh ei ej ek el em en eo ep eq er es et eu ev ew ex ey ez fa fb fc fd fe ff fg fh fi fj fk fl fm fn fo fp fq fr fs ft fu fv fw fx fy fz ga gb gc gd ge gf gg gh gi gj gk gl gm gn go gp gq gr gs gt gu gv gw gx gy gz ha hb hc hd he hf hg hh hi hj hk hl hm hn ho hp hq hr hs ht hu hv hw hx hy hz ia ib ic id ie if ig ih ii ij ik il im in io ip iq ir is it iu iv iw ix iy iz ja jb jc jd je jf jg jh ji jj jk jl jm jn jo jp jq jr js jt ju jv jw jx jy jz ka kb kc kd ke kf kg kh ki kj kk kl km kn ko kp kq kr ks kt ku kv kw kx ky kz la lb lc ld le lf lg lh li lj lk ll lm ln lo lp lq lr ls lt lu lv lw lx ly lz ma mb mc md me mf mg mh mi mj mk ml mm mn mo mp mq mr ms mt mu mv mw mx my mz na nb nc nd ne nf ng nh ni nj nk nl nm nn no np nq nr ns nt nu nv nw nx ny nz oa ob oc od oe of og oh oi oj ok ol om on oo op oq or os ot ou ov ow ox oy oz pa pb pc pd pe pf pg ph pi pj pk pl pm pn po pp pq pr ps pt pu pv pw px py pz qa qb qc qd qe qf qg qh qi qj qk ql qm qn qo qp qq qr qs qt qu qv qw qx qy qz ra rb rc rd re rf rg rh ri rj rk rl rm rn ro rp rq rr rs rt ru rv rw rx ry rz sa sb sc sd se sf sg sh si sj sk sl sm sn so sp sq sr ss st su sv sw sx sy sz ta tb tc td te tf tg th ti tj tk tl tm tn to tp tq tr ts tt tu tv tw tx ty tz ua ub uc ud ue uf ug uh ui uj uk ul um un uo up uq ur us ut uu uv uw ux uy uz va vb vc vd ve vf vg vh vi vj vk vl vm vn vo vp vq vr vs vt vu vv vw vx vy vz wa wb wc wd we wf wg wh wi wj wk wl wm wn wo wp wq wr ws wt wu wv ww wx wy wz xa xb xc xd xe xf xg xh xi xj xk xl xm xn xo xp xq xr xs xt xu xv xw xx xy xz ya yb yc yd ye yf yg yh yi yj yk yl ym yn yo yp yq yr ys yt yu yv yw yx yy yz za zb zc zd ze zf zg zh zi zj zk zl zm zn zo zp zq zr zs zt zu zv zw zx zy zz';
  const cite_references_link_many = '<li id="%1$s"><span class="mw-cite-backlink">↑ %2$s</span> %3$s</li>';
  const cite_references_link_many_sep = "&#32;";
  const cite_references_link_many_and = "&#32;";

  /* FUNÇÃO CHAMANDA PARA INSTALAR O PLUGIN */

  function instalar() {
    /* adição obrigatória, mesmo sem conteúdo, abstrata */
  }

  /* FUNÇÃO CHAMADA PARA DESINSTALAR O PLUGIN */

  function desinstalar() {
    /* adição obrigatória, mesmo sem conteúdo, abstrata */
  }

  /*
   * ADICIONA OS RECUROS ESPECÍFICO DESTA CLASSE, EXCETO AQUELES DEFINISO
   * PELO PRÓPRIO NOME DO PLUGIN (PADRÃO
   */

  public function setRecursos() {
    parent::setRecursos();

    $css = Array(
        1 => Array(
            'nome' => 'tipsy',
            'url' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery.tipsy/1.0.3/jquery.tipsy.min.css',
            'deps' => /* depends on= */ array(),
            'ver' => null,
            'media' => "screen"
        ),
        2 => Array(
            'nome' => 'tipsy_personalizado',
            'url' => plugins_url() . '/wikicite/wikicite.css',
            'deps' => /* depends on= */ array(),
            'ver' => null,
            'media' => "screen"
        ),

    );

    $js = Array(
        1 => Array(
            'nome' => 'tipsy',
            'url' => "https://cdnjs.cloudflare.com/ajax/libs/jquery.tipsy/1.0.3/jquery.tipsy.min.js",
            'deps' => /* depends on= */ array('jquery'),
            'ver' => self::version,
            'in_footer' => null
        ),
        2 => Array(
            'nome' => 'tipsy_start',
            'url' => plugins_url() . '/wikicite/tipsy.star.min.js',
            'deps' => /* depends on= */ array('jquery', 'tipsy'),
            'ver' => self::version,
            'in_footer' => null
        ),

    );

    $this->addHeadJS($js);
    $this->addHeadCSS($css);
  }

  /*
   * Esta funcção adiciona os HOOKS Wordpress
   * Ela é automaticamente chamada por GuinaPress::__construct()
   * Não há necessidade portanto de chamá-la manualmente, exceto se sobrepor
   * o construct
   */

  public function setHooks() {
    /* ADICIONANDO OS SHORTCODES, PARA CONVERSÃO E SUBSTITUIÇÃO */
    add_shortcode('ref', array(&$this, 'ref'));
    add_shortcode('references', array(&$this, 'references'));
  }

  /**
   * função chamado por [ref]
   *
   * @param $att array de argumentos presentes na TAG
   * @param $conteudo é o conteúdo existente entre a tags
   * @return string
   */
  /* EXECUTADO QUANDO É ENCONTRADO A TAG [REF] */
  function ref($att, $conteudo) {
//		$conteudo = 'AAAA';
//		$conteudo = iconv(mb_detect_encoding($conteudo, mb_detect_order(), true), "UTF-8", $conteudo);
    $conteudo = trim($conteudo);
    if ($this->mInCite) {
      return htmlspecialchars("[ref]$conteudo" . "[/ref]");
    } else {
      /* acrescenta */
      $this->mCallCnt++;

      /* desabilita novo processamento até concluir este */
      $this->mInCite = true;

      /* processa a tag */
      $ret = $this->guardedRef($conteudo, $att);

      /* recabilita processamento */
      $this->mInCite = false;

      /* retorno o novo conteúdo */
      return $ret;
    }
  }

  /**
   * @param $str o conteudo da tag
   * @param $argv array de argumentos
   * @param $default_group string
   * @return string
   */
  function guardedRef($str, $argv, $default_group = CITE_DEFAULT_GROUP) {
    /* EM WORDPRESS, SE NÃO HOUVER PARAM, $argv POSSUI COUNT 1 VAZIO, CORRIGINDO */
    if ($argv == null)
      $argv = Array();

    # The key here is the "name" attribute.
    list( $key, $group, $follow ) = $this->refArg($argv);

    # Split these into groups.
    if ($group === null) {
      if ($this->mInReferences) {
        $group = $this->mReferencesGroup;
      } else {
        $group = $default_group;
      }
    }

    # This section deals with constructions of the form
    #
		# <references>
    # <ref name="foo"> BAR </ref>
    # </references>
    #
		if ($this->mInReferences) {
      if ($group != $this->mReferencesGroup) {
        # <ref> and <references> have conflicting group attributes.
        $this->mReferencesErrors[] = $this->error('cite_error_references_group_mismatch', htmlspecialchars($group));
      } elseif ($str !== '') {
        if (!isset($this->mRefs[$group])) {
          # Called with group attribute not defined in text.
          $this->mReferencesErrors[] = $this->error('cite_error_references_missing_group', htmlspecialchars($group));
        } elseif ($key === null || $key === '') {
          # <ref> calls inside <references> must be named
          $this->mReferencesErrors[] = $this->error('cite_error_references_no_key');
        } elseif (!isset($this->mRefs[$group][$key])) {
          # Called with name attribute not defined in text.
          $this->mReferencesErrors[] = $this->error('cite_error_references_missing_key', $key);
        } else {
          # Assign the text to corresponding ref
          $this->mRefs[$group][$key]['text'] = $str;
        }
      } else {
        # <ref> called in <references> has no content.
        $this->mReferencesErrors[] = $this->error('cite_error_empty_references_define', $key);
      }

      return '';
    }

    if ($str === '') {
      # <ref ...></ref>.  This construct is  invalid if
      # it's a contentful ref, but OK if it's a named duplicate and should
      # be equivalent <ref ... />, for compatability with #tag.
      if ($key == false) {
        $this->mRefCallStack[] = false;
        return $this->error('cite_error_ref_no_input');
      } else {
        $str = null;
      }
    }

    if ($key === false) {
      # TODO: Comment this case; what does this condition mean?
      $this->mRefCallStack[] = false;
      return $this->error('cite_error_ref_too_many_keys');
    }

    if ($str === null && $key === null) {
      # Something like <ref />; this makes no sense.
      $this->mRefCallStack[] = false;
      return $this->error('cite_error_ref_no_key');
    }

    if (preg_match('/^[0-9]+$/', $key) || preg_match('/^[0-9]+$/', $follow)) {
      # Numeric names mess up the resulting id's, potentially produ-
      # cing duplicate id's in the XHTML.  The Right Thing To Do
      # would be to mangle them, but it's not really high-priority
      # (and would produce weird id's anyway).

      $this->mRefCallStack[] = false;
      return $this->error('cite_error_ref_numeric_key');
    }

    if (preg_match(
                    '/<ref\b[^<]*?]/', preg_replace('#<([^ ]+?).*?>.*?</\\1 *>|<!--.*?-->#', '', $str)
            )) {
      # (bug 6199) This most likely implies that someone left off the
      # closing </ref> tag, which will cause the entire article to be
      # eaten up until the next <ref>.  So we bail out early instead.
      # The fancy regex above first tries chopping out anything that
      # looks like a comment or SGML tag, which is a crude way to avoid
      # false alarms for <nowiki>, <pre>, etc.
      #
			# Possible improvement: print the warning, followed by the contents
      # of the <ref> tag.  This way no part of the article will be eaten
      # even temporarily.

      $this->mRefCallStack[] = false;
      return $this->error('cite_error_included_ref');
    }

    if (is_string($key) || is_string($str)) {
      # We don't care about the content: if the key exists, the ref
      # is presumptively valid.  Either it stores a new ref, or re-
      # fers to an existing one.  If it refers to a nonexistent ref,
      # we'll figure that out later.  Likewise it's definitely valid
      # if there's any content, regardless of key.

      return $this->stack($str, $key, $group, $follow, $argv);
    }

    # Not clear how we could get here, but something is probably
    # wrong with the types.  Let's fail fast.
    $this->croak('cite_error_key_str_invalid', serialize("$str; $key"));
  }

  /**
   * Parse the arguments to the [ref] tag
   *
   *  "name" : a chave única, nome da referenca.
   *  "group" : Grupo a que pertence. Necessidades a serem passados ​​para [/references] tbm.
   *  "follow" : Se a referência atual é a continuação de outro, a chave dessa referência.
   *
   *
   * @param $argv array de argumentos
   * @return mixed false on invalid input, a string on valid
   *               input and null on no input
   */
  function refArg($argv) {
    global $wgAllowCiteGroups;
    $cnt = count($argv);
    $group = null;
    $key = null;
    $follow = null;

    if ($cnt > 2) {
      // There should only be one key or follow parameter, and one group parameter
      // FIXME : this looks inconsistent, it should probably return a tuple
      return false;
    } elseif ($cnt >= 1) {
      if (isset($argv['name']) && isset($argv['follow'])) {
        return array(false, false, false);
      }

      if (isset($argv['name'])) {
        // Key given.
        $key = Sanitizer::escapeId($argv['name'], 'noninitial');
        unset($argv['name']);
        --$cnt;
      }

      if (isset($argv['follow'])) {
        // Follow given.
        $follow = Sanitizer::escapeId($argv['follow'], 'noninitial');
        unset($argv['follow']);
        --$cnt;
      }

      if (isset($argv['group'])) {
        if (!$wgAllowCiteGroups) {
          // remove when groups are fully tested.
          return array(false);
        }
        // Group given.
        $group = $argv['group'];
        unset($argv['group']);
        --$cnt;
      }

      if ($cnt == 0) {
        return array($key, $group, $follow);
      } else {
        // Invalid key
        return array(false, false, false);
      }
    } else {
      // No key
      return array(null, $group, false);
    }
  }

  /**
   * Populate $this->mRefs based on input and arguments to [ref]
   *
   * @param $str string Input from the [ref]tag
   * @param $key mixed Argument to the [ref] tag as returned by $this->refArg()
   * @param $group
   * @param $follow
   * @param $call
   *
   * @return string
   */
  function stack($str, $key = null, $group, $follow, $call) {
    if (!isset($this->mRefs[$group])) {
      $this->mRefs[$group] = array();
    }
    if (!isset($this->mGroupCnt[$group])) {
      $this->mGroupCnt[$group] = 0;
    }

    if ($follow != null) {
      if (isset($this->mRefs[$group][$follow]) && is_array($this->mRefs[$group][$follow])) {
        // adicionar texto para a nota que está sendo seguido
        $this->mRefs[$group][$follow]['text'] = $this->mRefs[$group][$follow]['text'] . ' ' . $str;
      } else {
        // inserir parte da nota, no início do grupo
        for ($k = 0; $k < count($this->mRefs[$group]); $k++) {
          if ($this->mRefs[$group][$k]['follow'] == null) {
            break;
          }
        }
        array_splice($this->mRefs[$group], $k, 0, array(array('count' => - 1,
                'text' => $str,
                'key' => ++$this->mOutCnt,
                'follow' => $follow)));
        array_splice($this->mRefCallStack, $k, 0, array(array('new', $call, $str, $key, $group, $this->mOutCnt)));
      }
      // return an empty string : this is not a reference
      return '';
    }
    if ($key === null) {
      // No key
      // $this->mRefs[$group][] = $str;
      $this->mRefs[$group][] = array('count' => - 1, 'text' => $str, 'key' => ++$this->mOutCnt);
      $this->mRefCallStack[] = array('new', $call, $str, $key, $group, $this->mOutCnt);

      return $this->linkRef($group, $this->mOutCnt);
    } elseif (is_string($key)) {
      // Valid key
      if (!isset($this->mRefs[$group][$key]) || !is_array($this->mRefs[$group][$key])) {
        // First occurrence
        $this->mRefs[$group][$key] = array(
            'text' => $str,
            'count' => 0,
            'key' => ++$this->mOutCnt,
            'number' => ++$this->mGroupCnt[$group]
        );
        $this->mRefCallStack[] = array('new', $call, $str, $key, $group, $this->mOutCnt);

        return
                $this->linkRef(
                        $group, $key, $this->mRefs[$group][$key]['key'] . "-" . $this->mRefs[$group][$key]['count'], $this->mRefs[$group][$key]['number'], "-" . $this->mRefs[$group][$key]['key']
        );
      } else {
        // We've been here before
        if ($this->mRefs[$group][$key]['text'] === null && $str !== '') {
          // If no text found before, use this text
          $this->mRefs[$group][$key]['text'] = $str;
          $this->mRefCallStack[] = array('assign', $call, $str, $key, $group,
              $this->mRefs[$group][$key]['key']);
        } else {
          $this->mRefCallStack[] = array('increment', $call, $str, $key, $group,
              $this->mRefs[$group][$key]['key']);
        }
        return
                $this->linkRef(
                        $group, $key, $this->mRefs[$group][$key]['key'] . "-" . ++$this->mRefs[$group][$key]['count'], $this->mRefs[$group][$key]['number'], "-" . $this->mRefs[$group][$key]['key']
        );
      }
    } else {
      $this->croak('cite_error_stack_invalid_input', serialize(array($key, $str)));
    }
  }

  /**
   * Generate a link (<sup ...) for the <ref> element from a key
   * and return XHTML ready for output
   *
   * @param $group
   * @param $key string The key for the link
   * @param $count int The index of the key, used for distinguishing
   *                   multiple occurrences of the same key
   * @param $label int The label to use for the link, I want to
   *                   use the same label for all occourances of
   *                   the same named reference.
   * @param $subkey string
   *
   * @return string
   */
  function linkRef($group, $key, $count = null, $label = null, $subkey = '') {
    $label = is_null($label) ? ++$this->mGroupCnt[$group] : $label;

    return sprintf(self::cite_reference_link, $this->refKey($key, $count), $this->referencesKey($key . $subkey), $label
    );
  }

  /**
   * Return an id for use in wikitext output based on a key and
   * optionally the number of it, used in <references>, not <ref>
   * (since otherwise it would link to itself)
   *
   * @static
   *
   * @param string $key The key
   * @param int $num The number of the key
   * @return string A key for use in wikitext
   */
  function refKey($key, $num = null) {
    $prefix = self::cite_reference_link_prefix;
    $suffix = self::cite_reference_link_suffix;

    if (isset($num)) {
      $key = sprintf(self::cite_reference_link_key_with_num, $key, $num);
    }

    return "$prefix$key$suffix";
  }

  /**
   * Return an id for use in wikitext output based on a key and
   * optionally the number of it, used in <ref>, not <references>
   * (since otherwise it would link to itself)
   *
   * @static
   *
   * @param string $key The key
   * @param int $num The number of the key
   * @return string A key for use in wikitext
   */
  function referencesKey($key, $num = null) {
    $prefix = self::cite_references_link_prefix;
    $suffix = self::cite_references_link_suffix;

    if (isset($num)) {
      $key = sprintf(self::cite_reference_link_key_with_num, $key, $num);
    }

    return "$prefix$key$suffix";
  }

  /**
   * Callback function for <references>
   *
   * @param $str string Input
   * @param $argv array Arguments
   * @param $parser Parser
   *
   * @return string
   */
  function references($str, $argv, $parser) {
    if ($this->mInCite || $this->mInReferences) {
      if (is_null($str)) {
        return htmlspecialchars("<references/>");
      } else {
        return htmlspecialchars("<references>$str</references>");
      }
    } else {
      /* acrescenta */
      $this->mCallCnt++;

      /* desabilita novo processamento até concluir este */
      $this->mInReferences = true;

      /* processa a tag */
      $ret = $this->guardedReferences($str, $argv);

      /* recabilita processamento */
      $this->mInReferences = false;

      /* retorno o novo conteúdo */
      return $ret;
    }
  }

  /**
   * Returns formatted reference text
   * @param String $key
   * @param String $text
   * @return String
   */
  function referenceText($key, $text) {
    if ($text == '') {
      return $this->error('cite_error_references_no_text', $key, 'noparse');
    }
    return '<span class="reference-text">' . rtrim($text, "\n") . "</span>\n";
  }

  /**
   * @param $str string
   * @param $argv array
   * @param $parser Parser
   * @param $group string
   * @return string
   */
  function guardedReferences($str, $argv, $group = CITE_DEFAULT_GROUP) {
    /* EM WORDPRESS, SE NÃO HOUVER PARAM, $argv POSSUI COUNT 1 VAZIO, CORRIGINDO */
    if ($argv == null)
      $argv = Array();

    global $wgAllowCiteGroups;

    if (isset($argv['group']) && $wgAllowCiteGroups) {
      $group = $argv['group'];
      unset($argv['group']);
    }

    if (strval($str) !== '') {
      $this->mReferencesGroup = $group;

      # Detect whether we were sent already rendered <ref>s
      # Mostly a side effect of using #tag to call references
      $count = substr_count($str, "-ref-");
      for ($i = 1; $i <= $count; $i++) {
        if (count($this->mRefCallStack) < 1) {
          break;
        }

        # The following assumes that the parsed <ref>s sent within
        # the <references> block were the most recent calls to
        # <ref>.  This assumption is true for all known use cases,
        # but not strictly enforced by the parser.  It is possible
        # that some unusual combination of #tag, <references> and
        # conditional parser functions could be created that would
        # lead to malformed references here.
        $call = array_pop($this->mRefCallStack);
        if ($call !== false) {
          list( $type, $ref_argv, $ref_str,
                  $ref_key, $ref_group, $ref_index ) = $call;

          # Undo effects of calling <ref> while unaware of containing <references>
          $this->rollbackRef($type, $ref_key, $ref_group, $ref_index);

          # Rerun <ref> call now that mInReferences is set.
          $this->guardedRef($ref_str, $ref_argv, $parser);
        }
      }

      # Parse $str to process any unparsed <ref> tags.
//			$parser->recursiveTagParse( $str );
      # Reset call stack
      $this->mRefCallStack = array();
    }

    if (count($argv) && $wgAllowCiteGroups) {
      return $this->error('cite_error_references_invalid_parameters_group');
    } elseif (count($argv)) {
      return $this->error('cite_error_references_invalid_parameters');
    } else {
      $s = $this->referencesFormat($group);
      /*
        if ( $parser->getOptions()->getIsSectionPreview() ) {
        return $s;
        }
       */
      # Append errors generated while processing <references>
      if (count($this->mReferencesErrors) > 0) {
        $s .= "\n" . implode("<br />\n", $this->mReferencesErrors);
        $this->mReferencesErrors = array();
      }
      return $s;
    }
  }

  /**
   * Make output to be returned from the references() function
   *
   * @param $group
   *
   * @return string XHTML ready for output
   */
  function referencesFormat($group) {
    if (( count($this->mRefs) == 0 ) || ( empty($this->mRefs[$group]) )) {
      return '';
    }

    $ent = array();
    $total = (count($this->mRefs[$group]) % 2) + ((int) (count($this->mRefs[$group]) / 2));

    foreach ($this->mRefs[$group] as $k => $v) {
      $ent[] = @$this->referencesFormatEntry($k, $v, ($k + 1) <= $total ? 'esquerda' : 'direita');
    }

//      echo " <hr> ent :: <br /> " . print_r($ent, true) . " <hr> "; flush();

    $prefix = self::cite_references_prefix;
    $suffix = self::cite_references_suffix;
    $content = implode("\n", $ent);

    // Prepare the parser input. We add new lines between the pieces to avoid a confused tidy (bug 13073)
    $parserInput = $prefix . "\n" . $content . "\n" . $suffix;

    // Let's try to cache it.
//		global $wgMemc;
//		$cacheKey = wfMemcKey( 'citeref', md5( $parserInput ), $this->mParser->Title()->getArticleID() );
//		global $wgCiteCacheReferences;
//		$data = false;
//		if ( $wgCiteCacheReferences ) {
//			$data = $wgMemc->get( $cacheKey );
//		}
    // Live hack: parse() adds two newlines on WM, can't reproduce it locally -ævar
    $ret = rtrim($parserInput, "\n");

    // done, clean up so we can reuse the group
    unset($this->mRefs[$group]);
    unset($this->mGroupCnt[$group]);

    return $ret;
  }

  /**
   * Format a single entry for the referencesFormat() function
   *
   * @param string $key The key of the reference
   * @param mixed $val The value of the reference, string for anonymous
   *                   references, array for user-suppplied
   * @return string Wikitext
   */
  function referencesFormatEntry($key, $val, $lado = 'esquerda') {
    // Anonymous reference
    if (!is_array($val)) {
      return sprintf(
              self::cite_references_link_one, $this->referencesKey($key), $this->refKey($key), $this->referenceText($key, $val), $lado, $key + 1
      );
    }
    $text = $this->referenceText($key, $val['text']);
    if (isset($val['follow'])) {
      return sprintf(
              self::cite_references_no_link, $this->referencesKey($val['follow']), $text, null, $lado, $key + 1
      );
    } elseif ($val['text'] == '') {
      return sprintf(
              self::cite_references_link_one, $this->referencesKey($key), $this->refKey($key, $val['count']), $text, $lado, $key
      );
    }

    if ($val['count'] < 0) {
      return sprintf(
              self::cite_references_link_one, $this->referencesKey($val['key']),
              # $this->refKey( $val['key'], $val['count'] ),
              $this->refKey($val['key']), $text, $lado, $key + 1
      );
      // Standalone named reference, I want to format this like an
      // anonymous reference because displaying "1. 1.1 Ref text" is
      // overkill and users frequently use named references when they
      // don't need them for convenience
    } elseif ($val['count'] === 0) {
      return sprintf(
              self::cite_references_link_one, $this->referencesKey($key . "-" . $val['key']),
              # $this->refKey( $key, $val['count'] ),
              $this->refKey($key, $val['key'] . "-" . $val['count']), $text, $lado, $key + 1
      );
      // Named references with >1 occurrences
    } else {
      $links = array();
      // for group handling, we have an extra key here.
      for ($i = 0; $i <= $val['count']; ++$i) {
        $links[] = sprintf(
                self::cite_references_link_many_format, $this->refKey($key, $val['key'] . "-$i"),
                //	$this->referencesFormatEntryNumericBacklinkLabel( $val['number'], $i, $val['count'] ),
                $this->referencesFormatEntryAlternateBacklinkLabel($i), null, $lado, $key + 1
        );
      }

      $list = $this->listToText($links);

      return sprintf(self::cite_references_link_many, $this->referencesKey($key . "-" . $val['key']), $list, $text, $lado, $key + 1
      );
    }
  }

  /**
   * This does approximately the same thing as
   * Language::listToText() but due to this being used for a
   * slightly different purpose (people might not want , as the
   * first separator and not 'and' as the second, and this has to
   * use messages from the content language) I'm rolling my own.
   *
   * @static
   *
   * @param array $arr The array to format
   * @return string
   */
  function listToText($arr) {
    $cnt = count($arr);

    $sep = self::cite_references_link_many_sep;
    $and = self::cite_references_link_many_and;

    if ($cnt == 1) {
      // Enforce always returning a string
      return (string) $arr[0];
    } else {
      $t = array_slice($arr, 0, $cnt - 1);
      return implode($sep, $t) . $and . $arr[$cnt - 1];
    }
  }

  /**
   * Generate a custom format backlink given an offset, e.g.
   * $offset = 2; = c if $this->mBacklinkLabels = array( 'a',
   * 'b', 'c', ...). Return an error if the offset > the # of
   * array items
   *
   * @param int $offset The offset
   *
   * @return string
   */
  function referencesFormatEntryAlternateBacklinkLabel($offset) {
    if (!isset($this->mBacklinkLabels)) {
      $this->genBacklinkLabels();
    }
    if (isset($this->mBacklinkLabels[$offset])) {
      return $this->mBacklinkLabels[$offset];
    } else {
      // Feed me!
      return $this->error('cite_error_references_no_backlink_label', null, 'noparse');
    }
  }

  /**
   * Generate a numeric backlink given a base number and an
   * offset, e.g. $base = 1, $offset = 2; = 1.2
   * Since bug #5525, it correctly does 1.9 -> 1.10 as well as 1.099 -> 1.100
   *
   * @static
   *
   * @param int $base The base
   * @param int $offset The offset
   * @param int $max Maximum value expected.
   * @return string
   */
  function referencesFormatEntryNumericBacklinkLabel($base, $offset, $max) {
    global $wgContLang;
    $scope = strlen($max);
    $ret = number_format(sprintf("%s.%0{$scope}s", $base, $offset), 0, ',', '.');
    return $ret;
  }

  /**
   * Generate the labels to pass to the
   * 'cite_references_link_many_format' message, the format is an
   * arbitrary number of tokens separated by [\t\n ]
   */
  function genBacklinkLabels() {
    $text = self::cite_references_link_many_format_backlink_labels;
    $this->mBacklinkLabels = preg_split('#[\n\t ]#', $text);
  }

  /**
   * Partially undoes the effect of calls to stack()
   *
   * Called by guardedReferences()
   *
   * The option to define <ref> within <references> makes the
   * behavior of <ref> context dependent.  This is normally fine
   * but certain operations (especially #tag) lead to out-of-order
   * parser evaluation with the <ref> tags being processed before
   * their containing <reference> element is read.  This leads to
   * stack corruption that this function works to fix.
   *
   * This function is not a total rollback since some internal
   * counters remain incremented.  Doing so prevents accidentally
   * corrupting certain links.
   *
   * @param $type
   * @param $key
   * @param $group
   * @param $index
   */
  function rollbackRef($type, $key, $group, $index) {
    if (!isset($this->mRefs[$group])) {
      return;
    }

    if ($key === null) {
      foreach ($this->mRefs[$group] as $k => $v) {
        if ($this->mRefs[$group][$k]['key'] === $index) {
          $key = $k;
          break;
        }
      }
    }

    # Sanity checks that specified element exists.
    if ($key === null) {
      return;
    }
    if (!isset($this->mRefs[$group][$key])) {
      return;
    }
    if ($this->mRefs[$group][$key]['key'] != $index) {
      return;
    }

    switch ($type) {
      case 'new':
        # Rollback the addition of new elements to the stack.
        unset($this->mRefs[$group][$key]);
        if (count($this->mRefs[$group]) == 0) {
          unset($this->mRefs[$group]);
          unset($this->mGroupCnt[$group]);
        }
        break;
      case 'assign':
        # Rollback assignment of text to pre-existing elements.
        $this->mRefs[$group][$key]['text'] = null;
      # continue without break
      case 'increment':
        # Rollback increase in named ref occurrences.
        $this->mRefs[$group][$key]['count'] --;
        break;
    }
  }

  /**
   * Return an error message based on an error ID
   *
   * @param string $key   Message name for the error
   * @param string $param Parameter to pass to the message
   * @return string XHTML
   */
  public function error($key, $param = null) {
    $erro = new WP_Error('broke', __($messages['pt-br'][$key]));
    return $erro->get_error_message();
  }

  /**
   * Die with a backtrace if something happens in the code which
   * shouldn't have
   *
   * @param int $error  ID for the error
   * @param string $data Serialized error data
   */
  function croak($error, $data) {
    $erro = new WP_Error('broke', __($messages['pt-br'][$key]));
    return $erro->get_error_message();
  }

}

/* FIM DA CLASSE WIKICIT */
