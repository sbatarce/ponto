<?php

class ORAConn
	{
	var $conn = NULL;
	var $stid = NULL;
	var $mode = OCI_COMMIT_ON_SUCCESS;
	//
	function mErro( $e, $local )
		{
		$mes = $e['message'];
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
	//	
	function connect( $user, $pass, $amb, $chset )
		{
		$this->conn = @oci_connect( $user, $pass, $amb, $chset, OCI_DEFAULT );
		if( !$this->conn )
			{
			$e = oci_error();
			return $this->mErro($e, "oci_connect");
			}
		return "OK";
		}
	//	
	function libStmt()
		{
		if( $this->stid )
			oci_free_statement( $this->stid );
		$this->stid = NULL;
		}
	//
	function disconnect()
		{
		$this->libStmt();
		if( $this->conn )
			oci_close( $this->conn );
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
	function parse( $sql )
		{
		if( $this->stid != NULL )
			{
			$mes = "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"ORAConn->parse\" }";
			return $mes;
			}
		$this->stid = @oci_parse($this->conn, $sql );
		if( !$this->stid )
			{
			$e = oci_error($this->conn);
			return $this->mErro($e, "oci_parse");
			}
		return "OK";
		}
	//	sql	=>	instru��o a executar
	//	seq	=>	campo de sequencia. se # """ retorna a sequencia
	function execInsert( $query, $seq )
		{
		if( !$this->conn )
			return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execInsert\" }";
		if( $this->stid )
			return "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"execInsert\" }";
		//
		$res = $this->parse( $query );
		if( $res != "OK" )
			return $res;
		//	executa o query
		$r = oci_execute( $this->stid, $this->mode );
		if( !$r )
			{
			$e = oci_error($this->stid);
			return $this->mErro($e, "oci_execute");
			}
		$this->libStmt();
		if( $seq == "" )
			{
			return "{ \"status\": \"OK\" }";
			}
		//	obtem o valor da chave inserida
		$sql	=	"SELECT $seq.currval FROM DUAL";
		$res	=	$this->parse( $sql );
		if( $res != "OK" )
			return $res;
		//	obtem o currval
		$r = oci_execute( $this->stid, $this->mode );
		if( !$r )
			{
			$e = oci_error();
			return $this->mErro($e, "oci_execute ID");
			}
		$row = oci_fetch_array( $this->stid, OCI_ASSOC + OCI_RETURN_NULLS );
		$idn	=	$row['CURRVAL'];
		return "{ \"status\": \"OK\", \"idnovo\":\"$idn\" }";
		}

	function execDelUpd( $query )
		{
		if( !$this->conn )
			return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execDelUpd\" }";
		if( $this->stid )
			return "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"execDelUpd\" }";
		//
		$res = $this->parse( $query );
		if( $res != "OK" )
			return "{ \"status\": \"erro\", \"erro\":\"$res\", \"local\": \"oci_parse\" }";
		// Perform the logic of the query
		$r = oci_execute( $this->stid, $this->mode );
		if( !$r )
			{
			$e = oci_error($this->stid);
			$mes = $e[ 'message' ];
			return "{ \"status\": \"erro\", \"erro\":\"$mes\", \"local\": \"oci_execute\" }";
			}
		return "{ \"status\": \"OK\" }";
		}

	function execSelect( $sql )
		{
		if( !$this->conn )
			return "{ \"status\": \"erro\", \"erro\":\"desconectado\", \"local\": \"execSelect\" }";
		if( $this->stid )
			return "{ \"status\": \"erro\", \"erro\":\"ja ha uma execucao em curso\", \"local\": \"execSelect\" }";
		// Prepare the statement
		$this->stid = oci_parse( $this->conn, $sql );
		if( !$this->stid )
			{
			$e = oci_error();
			return $this->mErro( $e, "oci_parse" );
			}
		// Perform the logic of the query
		$r = oci_execute( $this->stid );
		if( !$r )
			{
			$e = oci_error($this->stid);
			return $this->mErro( $e, "oci_parse" );
			}
		$dad	=	"\"dados\": [ {";
		$lin  = 0;
		while( $row = oci_fetch_array( $this->stid, OCI_ASSOC + OCI_RETURN_NULLS ) )
			{
			if( $lin == 0 )
				{
				$lin++;
				$dad	.=	" \"_linha\": \"$lin\"";
				}
			else
				{
				$lin++;
				$dad	.=	"}, { \"_linha\": \"$lin\"";
				}
			foreach( $row as $cmp => $val)
				{
				$dad	.=	", \"$cmp\": \"$val\"";
				}
			}
		$ret  = "{ \"status\": \"OK\", \"linhas\":\"$lin\", $dad".
						"} ] }";
		return $ret;
		}	
	}										//	classe ORAConn
