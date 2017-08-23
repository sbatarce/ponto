<?php

function fmtErro( $stt, $txt )
	{
	echo "{ \"status\": \"$stt\", \"$stt\": \"$txt\" }";
	}
