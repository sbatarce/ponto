<?php
/**
 * 	function setDebug()
 *  function mErro($e, $local)
 *	function connect($user, $pass, $amb, $chset, $role)
 *  function libStmt()
 *  function disconnect()
 *  function beginTransaction()
 *  function commit()
 *  function rollback()
 *  function parse($sql)
 *  function execProcVar( $proc, $parms )
 *  function execProc( $proc, $parms )
 *  function execInsert($query, $seq)
 *  function execDelUpd($query)
 *  function execSelect($sql)
 *
 */
class ORAConn
  {
  var $user, $amb, $chset, $role;
  var $conn = NULL;
  var $stid = false;
  var $mode = OCI_COMMIT_ON_SUCCESS;
	var $debug = false;
  //
  function __construct()
    {
    }
		
	function setDebug()
		{
		$this->debug = true;
		echo "ORACOnn.PHP Debug habilitado<br>";
		}
  //
  function mErro($e, $local)
    {
    $mes = str_replace( "\"", "'", $e['message'] );
		$err = str_replace( "\n", " ", $mes );
		$mes = str_replace( "\r", " ", $err );

    $off = $e['offset'];
    return <<<EOD
      { 
      "status": "erro", 
      "erro": "$mes", 
      "local": "$local",
      "offset": "$off"
      }
EOD;
    }
  //  connect
function connect($user, $pass, $amb, $chset, $role)
	{
	$this->conn = oci_connect($user, $pass, $amb, $chset, OCI_DEFAULT);
	if (!$this->conn)
		{
		$e = oci_error();
		return $this->mErro($e, "oci_connect");
		}
	if ($role == NULL OR $role == "" )
		return "OK";
	//	libera o acesso do usuario 
	$this->stid = oci_parse($this->conn, "set role ".$role);
	if (!$this->stid)
		{
		$e = oci_error($this->conn);
		return $this->mErro($e, "connect oci_parse");
		}
	// Perform the logic of the query
	$r = oci_execute($this->stid);
	if (!$r)
		{
		$e = oci_error($this->stid);
		$this->libStmt();
		return $this->mErro($e, "connect oci_execute");
		}
	$this->libStmt();
	return "OK";
	}
  //	
  function libStmt()
    {
    if ($this->stid)
      oci_free_statement($this->stid);
    $this->stid = false;
    }
  //
  function disconnect()
    {
    $this->libStmt();
    if ($this->conn)
      oci_close($this->conn);
    $this->conn = NULL;
    }
  //	
  function beginTransaction()
    {
    $this->mode = OCI_NO_AUTO_COMMIT;
    }
  //
  function commit()
    {
    oci_commit($this->conn);
    $this->mode = OCI_COMMIT_ON_SUCCESS;
    }
  //
  function rollback()
    {
    oci_rollback($this->conn);
    $this->mode = OCI_COMMIT_ON_SUCCESS;
    }
  //	
  function parse($sql)
    {
    if ($this->stid)
      {
      $mes = "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"ORAConn->parse\" }";
      return $mes;
      }
    $this->stid = @oci_parse($this->conn, $sql);
    if (!$this->stid)
      {
      $e = oci_error($this->conn);
      return $this->mErro($e, "oci_parse");
      }
    return "OK";
    }
	//	execProcVar	chama uma procedure do oracle que retorna um valor (não um cursor)
  //  proc: nome da procedure a chamar
  //  parms
  function execProcVar( $proc, $parms )
    {
		if( $this->debug )
			{
			echo "ORAConn execProcVar<br>";
			echo "<br>";
			echo "<br>";
			}
    if (!$this->conn)
      return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execInsert\" }";
    $retor = 0;
    if( strlen($parms) > 0 )
      $aux = "begin $proc( $parms, :ret ); end;";
    else
      $aux = "begin $proc( :ret ); end;";
    $stm = oci_parse( $this->conn, $aux );
    if( !oci_bind_by_name($stm, ":ret", $retor, 32, SQLT_CHR ) )
			{
      $e = oci_error($stm);
			$this->libStmt();
      return $this->mErro($e, "oci_bind_by_name stid");
			}
    $r = @oci_execute( $stm );
		if( !$r )
			{
      $e = oci_error($stm);
			$this->libStmt();
      return $this->mErro($e, "@oci_execute stid");
			}
		$this->libStmt();
		
		$aux = str_replace( "\"", "'", $retor );
    return "{ \"status\": \"OK\", \"retorno\": \"$aux\" }";
    }
	//	
  //  proc: nome da procedure a chamar
  //  parms
  function execProc( $proc, $parms )
    {
		if( $this->debug )
			{
			echo "ORAConn execProc<br>";
			echo "proc=$proc<br>";
			echo "parms=$parms<br>";
			}
    if (!$this->conn)
      return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execInsert\" }";
    $curs = oci_new_cursor($this->conn);
    if( strlen($parms) > 0 )
      $aux = "begin $proc( $parms, :cur ); end;";
    else
      $aux = "begin $proc( :cur ); end;";
    $stm = oci_parse( $this->conn, $aux );
    oci_bind_by_name($stm, ":cur", $curs, -1, OCI_B_CURSOR );
    $r = @oci_execute( $stm );
    if (!$r)
      {
      $e = oci_error($stm);
      return $this->mErro($e, "oci_execute stid");
      }
    $r = @oci_execute( $curs );
    if (!$r)
      {
      $e = oci_error($stm);
      return $this->mErro($e, "oci_execute curs");
      }
    //	monta a resposta
    $dad = "\"dados\": [ {";
    $lin = 0;
    while ($row = oci_fetch_array($curs, OCI_ASSOC + OCI_RETURN_NULLS))
      {
      if ($lin == 0)
				{
				$lin++;
				$dad .= " \"_linha\": \"$lin\"";
				}
      else
				{
				$lin++;
				$dad .= "}, { \"_linha\": \"$lin\"";
				}
      foreach ($row as $cmp => $val)
				{
				$aux = str_replace( "\"", "'", $val );
				$dad .= ", \"$cmp\": \"$aux\"";
				}
      }
    $ret = "{ \"status\": \"OK\", \"linhas\":\"$lin\", $dad"."} ] }";
    return $ret;
    }
  //	sql	=>	instrucao a executar
  //	seq	=>	campo de sequencia. se # """ retorna a sequencia
  function execInsert($query, $seq)
    {
    if(!$this->conn)
      return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execInsert\" }";
    if($this->stid)
      return "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"execInsert\" }";
    //
    $res = $this->parse($query);
    if ($res != "OK")
      return $res;
    //	executa o query
    $r = oci_execute($this->stid, $this->mode);
    if (!$r)
      {
      $e = oci_error($this->stid);
      $this->libStmt();
      return $this->mErro($e, "oci_execute");
      }
    $this->libStmt();
    if ($seq == "")
      {
      return "{ \"status\": \"OK\" }";
      }
    //	obtem o valor da chave inserida
    $sql = "SELECT $seq.currval FROM DUAL";
    $res = $this->parse($sql);
    if ($res != "OK")
      return $res;
    //	obtem o currval
    $r = oci_execute($this->stid, $this->mode);
    if (!$r)
      {
      $e = oci_error();
      $this->libStmt();
      return $this->mErro($e, "oci_execute ID");
      }
    $row = oci_fetch_array($this->stid, OCI_ASSOC + OCI_RETURN_NULLS);
    $idn = $row['CURRVAL'];
    $this->libStmt();
    return "{ \"status\": \"OK\", \"idnovo\":\"$idn\" }";
    }
  function execDelUpd($query)
    {
    if (!$this->conn)
      return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execDelUpd\" }";
    if ($this->stid)
      return "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"execDelUpd\" }";
    //
    $res = $this->parse($query);
    if ($res != "OK")
      return "{ \"status\": \"erro\", \"erro\":\"$res\", \"local\": \"oci_parse\" }";
    //
    $r = oci_execute($this->stid, $this->mode);
    if (!$r)
      {
      $e = oci_error($this->stid);
      $this->libStmt();
      return $this->mErro($e, "oci_execute");
      }
    $qtd = oci_num_rows ( $this->stid );
    $this->libStmt();
    return "{ \"status\": \"OK\", \"qtd\": \"$qtd\" }";
    }
  function execSelect($sql)
    {
    if (!$this->conn)
      return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execSelect\" }";
    if ($this->stid)
      return "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"execSelect\" }";
    // Prepare the statement
    $this->stid = oci_parse($this->conn, $sql);
    if (!$this->stid)
      {
      $e = oci_error($this->conn);
      return $this->mErro($e, "oci_parse");
      }
    // Perform the logic of the query
    $r = oci_execute($this->stid);
    if (!$r)
      {
      $e = oci_error($this->stid);
      $this->libStmt();
      return $this->mErro($e, "oci_execute");
      }
    $dad = "\"dados\": [ {";
    $lin = 0;
    while ($row = oci_fetch_array($this->stid, OCI_ASSOC + OCI_RETURN_NULLS))
      {
      if ($lin == 0)
				{
				$lin++;
				$dad .= " \"_linha\": \"$lin\"";
				}
      else
				{
				$lin++;
				$dad .= "}, { \"_linha\": \"$lin\"";
				}
      foreach ($row as $cmp => $val)
				{
				$aux = str_replace( "\"", "'", $val );
				$dad .= ", \"$cmp\": \"$aux\"";
				}
      }
    $this->libStmt();
    $ret = "{ \"status\": \"OK\", \"linhas\":\"$lin\", $dad} ] }";
    return $ret;
    }
//	execFunction
	function execFunction( $func, $parms )
		{
		if( $this->debug )
			{
			echo "execFunction( $func, $parms )<br>";
			}
    if (!$this->conn)
      return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execFunction\" }";
    $retor = 0;
    $aux = "begin :ret:=$func( $parms ); end;";
    $stm = oci_parse( $this->conn, $aux );
		if( $this->debug )
			{
			var_dump($stm);
			}
    if( !oci_bind_by_name($stm, ":ret", $retor, 500, SQLT_CHR ) )
			{
      $e = oci_error($stm);
			$this->libStmt();
      return $this->mErro($e, "oci_bind_by_name stid");
			}
    $r = @oci_execute( $stm );
		if( $this->debug )
			{
			var_dump( $r );
			}
		if( !$r )
			{
      $e = oci_error($stm);
			$this->libStmt();
      return $this->mErro($e, "@oci_execute stid");
			}
		if( $this->debug )
			{
			echo "retor: |$retor| ";
			}
		
		$this->libStmt();

		return $retor;
		}
  }

