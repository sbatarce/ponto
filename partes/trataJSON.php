<?php
//	obtem o JSON do corpo do HTML
//	decodifica e trata o eventual erro
//	inclua o fmtErro no PHP que chama o trataJSON
//	retorna um objeto: $json

$body = @file_get_contents('php://input');
$json = json_decode($body);
switch (json_last_error()) 
	{
	case 0:
		break;
	case JSON_ERROR_DEPTH:
		fmtErro( "erro", "JSON_ERROR_DEPTH" );
		return;
	case JSON_ERROR_STATE_MISMATCH:
		fmtErro( "erro", "JSON_ERROR_STATE_MISMATCH" );
		return;
	case JSON_ERROR_CTRL_CHAR:
		fmtErro( "erro", "JSON_ERROR_CTRL_CHAR" );
		return;
	case JSON_ERROR_SYNTAX:
		fmtErro( "erro", "JSON_ERROR_SYNTAX" );
		return;
	case JSON_ERROR_UTF8:
		fmtErro( "erro", "JSON_ERROR_UTF8" );
		return;
	default:
		$err = json_last_error();
		fmtErro( "erro", "erro JSON ($err) desconhecido" );
		return;
  }
