function toHora( data )
	{
	var hh = data.getHours();
	if( hh < 10 )
		hh = "0" + hh;
	var mm = data.getMinutes();
	var mm = data.getHours();
	if( mm < 10 )
		mm = "0" + mm;
	return hh + ":" + mm;
	}

function minToHHMM( minutos )
	{
	var hh = Math.floor(Math.abs(minutos)/60);
	var mm = Math.abs(minutos)%60;
	if( hh < 10 )
		hh = "0" + hh;
	if( mm < 10 )
		mm = "0" + mm;
	if( minutos < 0 )
		return "-"+hh+":"+mm;
	else
		return hh+":"+mm;
	}
			
