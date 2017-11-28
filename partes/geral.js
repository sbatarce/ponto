//	geral.js		rotinas de uso geral
//

var user;
var basicAuth = {};
var flbasic = false;
var bloq = false;

function bloqueia( )
  {
  if( !bloq )
    {
    $(".waitajax").css("display", "block");
    bloq = true;
    }
  }
function libera( )
  {
  if( bloq )
    {
    $(".waitajax").css("display", "none");
    bloq = false;
    }
  }
$(document).ready(function()
  {		
  $(document).ajaxStart(function()
    {
    bloqueia();
    });
  $(document).ajaxComplete(function()
    {
    libera();
    });
  });

//  combina 2 objetos tipo { k1: v1, k2: v2,... }
//  adiciona ou modifica obj2[kx] em obj1
function combina( obj1, obj2 )
  {
  for( key in obj2 )
    {
    obj1[key] = obj2[key];
    }
  }

function setBasicAuth( us, ps )
  {
  if( us == undefined || ps == undefined )
    {
    basicAuth = {};
    flbasic = false;
    return;
    }
   if( us == null || ps == null )
    {
    basicAuth = {};
    flbasic = false;
    return;
    }
   if( us == "" || ps == "" )
    {
    basicAuth = {};
    flbasic = false;
    return;
    }
  basicAuth = { Authorization: "Basic " + btoa(us+":"+ps) };
  flbasic = true;
  }
  
function repserviceA( metodo, funcao, ip, mac, sistema, adhea )
  {
  var resul = { };
  var host = window.location.hostname;
  if( host == "egov.santos.sp.gov.br" )
    host = "vmp-webserv03.santos.sp.gov.br";
  var url = "http://" + host + "/cgi-bin/RepService.cgi/" + funcao;
  var hea = { };
  if( adhea != null && adhea != undefined )
    hea = adhea;
  hea["REPIP"] = ip;
  hea["REPMAC"] = mac;
  hea["REPSIS"] = sistema;
  if( flbasic )
    combina( hea, basicAuth );
  $.ajax(
    {
    headers: hea,
    url: url,
    type: metodo,
    contentType: "application/json",
    datatype: 'json',
    async: false,
    success: function( resp, textStatus, jqXHR )
      {
      resul.resp = resp;
      resul.status = "OK";
      },
    error: function( responseData, textStatus, errorThrown )
      {
      resul.status = responseData.status;
      if( responseData.responseText != undefined )
        resul.erro = responseData.responseText
      else
        resul.erro = textStatus;
      }
    } );
  return resul;
  }

//  chama o repservice com BODY
function repserviceB( metodo, funcao, idapal, sistema, adhea, body )
  {
  var resul = { };
  var host = window.location.hostname;
  if( host == "egov.santos.sp.gov.br" )
    host = "vmp-webserv03.santos.sp.gov.br";
  var url = "http://" + host + "/cgi-bin/RepService.cgi/" + funcao;
  var dat = { };
  if( body != null && body != undefined )
    dat = body;
  var hea = { };
  if( adhea != null && adhea != undefined )
    hea = adhea;
  hea["IDAPAL"] = idapal;
  hea["REPSIS"] = sistema;
  if( flbasic )
    combina( hea, basicAuth );
  $.ajax(
    {
    headers: hea,
    data: dat,
    url: url,
    type: metodo,
    contentType: "application/json",
    datatype: 'json',
    async: false,
    success: function( resp, textStatus, jqXHR )
      {
      resul.resp = resp;
      resul.status = "OK";
      },
    error: function( responseData, textStatus, errorThrown )
      {
      resul.status = responseData.status;
      if( responseData.responseText != undefined )
        resul.erro = responseData.responseText
      else
        resul.erro = textStatus;
      }
    } );
  return resul;
  }

function repservice( metodo, funcao, idapal, sistema, adhea )
  {
  var resul = { };
  var url = "http://" + window.location.hostname +
      "/cgi-bin/RepService.cgi/" + funcao;
  var hea = { };
  if( adhea != null && adhea != undefined )
    hea = adhea;
  hea["IDAPAL"] = idapal;
  hea["REPSIS"] = sistema;
  if( flbasic )
    combina( hea, basicAuth );
  $.ajax(
    {
    headers: hea,
    url: url,
    type: metodo,
    contentType: "application/json",
    datatype: 'json',
    async: false,
    success: function( resp, textStatus, jqXHR )
      {
      resul.resp = resp;
      resul.status = "OK";
      },
    error: function( responseData, textStatus, errorThrown )
      {
      resul.status = responseData.status;
      if( responseData.responseText != undefined )
        resul.erro = responseData.responseText
      else
        resul.erro = textStatus;
      }
    } );
  return resul;
  }

function normIPAddr( IP )
  {
  var ix = 0;
  while( 1 )
    {
      var ch = IP.substr( ix, 1 );
      if( ch != "0" )
        break;
      IP = IP.substr( 0, ix ) + ' ' + IP.substr( ix + 1 );
      ix++;
    }
  IP = IP.trim();
  while( 1 )
    {
      if( IP.indexOf( ".000" ) >= 0 )
        {
          IP = IP.replace( ".000", "." );
          continue;
        }
      if( IP.indexOf( ".00" ) >= 0 )
        {
          IP = IP.replace( ".00", "." );
          continue;
        }
      if( IP.indexOf( ".0" ) >= 0 )
        {
          IP = IP.replace( ".0", "." );
          continue;
        }
      if( IP.indexOf( ".." ) >= 0 )
        {
          IP = IP.replace( "..", ".#." );
          continue;
        }
      break;
    }
  IP = IP.replace( /#/g, "0" );
  if( IP.charAt( IP.length - 1 ) == "." )
    IP += "0";
  return IP;
  }

function Titulo( titu )
  {
  document.getElementById( "titu" ).innerHTML = titu;
  }
 
function com2Digs(number)
  {
  return( number < 10 ? '0' : '' ) + number;
  }
  
 // de Date =>  ou 
 //     tipo 1  DD/MM/YYYY
 //     tipo 2  YYYYMMDD
 //     default YYYY-MM-DD
 function toStDate( data, tipo )
  {
  let ano = data.getFullYear();
  let mes = data.getMonth()+1;
  let dia = data.getDate();
  let sdia, smes, sano;
  if( dia < 10 )
    sdia = "0" + dia;
  else
    sdia = "" + dia;
  
  if( mes < 10 )
    smes = "0" + mes;
  else
    smes = "" + mes;
  sano = "" + ano;
  switch( tipo )
    {
    case 1:
      return `${sdia}/${smes}/${sano}`;
      break;
    case 2:
      return `${sano}${smes}${sdia}`;
      break;
    default:
      return `${sano}-${smes}-${sdia}`;
      break;
    }
  }

//  de DD/MM/YYYY => YYYYMMDD
 function toDateInv( data )
  {
  var res = data.substr( 6 );
  res += data.substr( 3, 2 );
  res += data.substr( 0, 2 );
  return res;
  }
  
// de DD/MM/YYYY => Date() ou
// de YYYY-MM-DD => Date() ou
// de YYYYMMDD   => Date()
function toDate( data )
  {
  let ano, mes, dia;
  if( data.indexOf( "/" ) >= 0 )
    {
    ano = data.substr( 6 );
    mes = data.substr( 3, 2 );
    dia = data.substr( 0, 2 );
    }
  if( data.indexOf( "-" ) >= 0 )
    {
    ano = data.substr( 0, 4 );
    mes = data.substr( 5, 2 );
    dia = data.substr( 8, 2 );
    }
  if( data.indexOf( "-" ) < 0 && data.indexOf( "/" ) < 0 )
    {
    ano = data.substr( 0, 4 );
    mes = data.substr( 4, 2 );
    dia = data.substr( 6, 2 );
    }
  return new Date( ano, mes-1, dia );
  }
  
//  de YYYYMMDD => DD/MM/YYYY
 function toDateDir( data )
  {
  var res = data.substr( 6 );
  res += "/";
  res += data.substr( 4, 2 );
  res += "/";
  res += data.substr( 0, 4 );
  return res;
  }

//  minutos => hh:mm
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
    return ""+hh+":"+mm;
  }
			
//	de hh:mm para minutos
function hhmmToMin( hhmm )
  {
  if( hhmm.substring(2,3) != ":" )
    return -1;
  if( !$.isNumeric(hhmm.substring(0,2)) )
    return -1;
  if( !$.isNumeric(hhmm.substring(3,5)) )
    return -1;

  var hh = Number(hhmm.substring( 0, 2 ));
  var mm = Number(hhmm.substring( 3, 5 ));

  if( hh < 0 || hh > 23 )
    return -1;
  if( mm < 0 || mm > 59 )
    return -1;

  return hh*60+mm;
  }

//  formatação de componentes HTML
function titulo( titu )
  {
  document.getElementById( "titu" ).innerHTML = titu;
  }

function Deslogar()
  {
  $( "#menupri" ).collapse( 'hide' );
  $( "#menu" ).hide();
  matarCookie("user");
  matarCookie("uoraut");
  matarCookie("super");
  matarCookie("tiuser");
  window.location.href = "index.php";
  }
  
 function Voltar()
  {
  window.history.back();
  //window.location = "index.php";
  }
  
function remoto( url, us, ps )
  {
  let resul = { };
  let hea = { };
  if( us != undefined && ps != undefined )
    {
    if( us != null && ps != null )
      {
      if( us != "" && ps != "" )
        hea["Authorization"] = "Basic " + btoa(us+":"+ps);
      }
    }
  if( flbasic )
    combina( hea, basicAuth );
  $.ajax(
    {
    headers: hea,
    type: 'POST',
    dataType: "json",
    async: false,
    url: url,
    success: function( resp, textStatus, jqXHR )
              {
              resul = resp;
              },
    error: function( responseData, textStatus, errorThrown )
            {
            resul.status = textStatus + " - " + responseData.responseText;
            resul.erro = errorThrown;
            }
    });
  return resul;
  }

function Select( query, parms )
  {
  var resul = remoto( "partes/queries.php?query=" + query + parms );
  if( resul.status != "OK" )
    {
      alert( "Erro obtendo dados " + resul.erro );
      return null;
    }
  return resul;
  }
//	função de deleção em DB
function Delete( query, parms )
  {
  var url = "partes/updates.php?query=" + query + parms;
  var resul = remoto( url );
  if( resul.status != "OK" )
    {
      alert( "Erro obtendo dados " + resul.erro );
      return false;
    }
  return true;
  }
//	função de Update em DB
function Update( query, parms )
  {
  var resul = remoto( "partes/updates.php?query=" + query + parms );
  if( resul.status != "OK" )
    {
    alert( "Erro alterando dados dados " + resul.erro );
    return false;
    }
  return true;
  }
//	função de insersão em DB 
function Insert( query, parms, sequence )
  {
  var url = "partes/inserts.php?query=" + query + parms;
  if( sequence != null || sequence != undefined )
    url += "&sequence=" + sequence;
  return remoto( url );
  }

function criarCookie( name, value, horas, minutos )
  {
  var ho = 0;
  var mi = 0;
  var sec = 0;
  var maxage = "";
  if( horas == null )
    ho = horas;
  if( minutos == null )
    mi = minutos;
  
  if( ho != 0 || mi != 0 )
    {
     /*
    var date = new Date();
    date.setTime( date.getTime() + ((( horas * 60 ) + mi) * 60 * 1000 ) );
    var expires = "; expires=" + date.toGMTString();
    */
    maxage = "; max-age=" + ((ho*60)+mi)*60;
    }
  else
    maxage = "";
  document.cookie = name + "=" + value + maxage + "; path=/";
  }
function matarCookie( name )
  {
  criarCookie( name, "", -1 );
  }
//	le cookies e retorna um dos valores								
function obterCookie( name )
  {
  var nameEQ = name + "=";
  var todos = document.cookie;
  var separ = todos.split( ';' );
  for( var i = 0; i < separ.length; i++ )
    {
    var c = separ[i];
    while( c.charAt( 0 ) == ' ' )
      c = c.substring( 1, c.length );
    if( c.indexOf( nameEQ ) == 0 )
      return c.substring( nameEQ.length, c.length );
    }
  return null;
  }

function logout()
  {
  eraseCookie( "user" );
  eraseCookie( "pass" );
  }

function getUser(  )
  {
  user = readCookie( "user" );
  if( user == null )
    {
    window.location.href = "index.html";
    return;
    }
  }

function SelOptions( url )
  {
  var resul = "";
  var hea = {};
  if( flbasic )
    combina( hea, basicAuth );
  $.ajax(
    {
    headers: hea,
    type: 'GET',
    async: false,
    crossDomain: true,
    url: url,
    success: function( resp, textStatus, jqXHR )
      {
      resul = resp;
      },
    error: function( responseData, textStatus, errorThrown )
      {
      alert( "Erro obtendo Options: " + textStatus );
      }
    } );
  return resul;
  }

//	carrega os dados de uma Select2
//  sel     - selector jQuery
//  url     - url com o resultado do conteúdo
//  id      - campo que será usado como id do Select2
//  text    - campo que será mostrado no Select2
//  func    - nome da função a chamar quando houver uma escolha
//  minlen  - mínimo de caracteres a conter no campo de pesquisa
function SelInit( sel, url, id, text, func, minlen )
  {
  var min;
  var hea = { };
  if( minlen == undefined || minlen == null )
    min = 0;
  else
    min = minlen;
  if( flbasic )
    combina( hea, basicAuth );
  $.ajax(
    {
    headers: hea,
    type: 'GET',
    url: "partes/" + url,
    success: function( data )
      {
      json = eval( data );
      $( sel ).select2(
        {
        minimumInputLength: min,
        minimumResultsForSearch: 10,
        data: json,
        initSelection: function( element, callback )
          {
          callback( { id: id, text: text } );
          }
        })
      .on( "change", function( e )
        {
        if( func != undefined && func != null )
          func( "change", e.val, e.added.text );
        } );
      },
    error: function( responseData, textStatus, errorThrown )
      {
      alert( "erro acessando dados " + textStatus +
          "/" + errorThrown );
      }
    });
  }

///	campos e linhas
///	IniLinha		cria uma DIV apropriada a inserir uma linha
///		classe é um ou mais nomes de classes a associar à DIV
function IniLinha( classe )
  {
  if( classe == null )
    return "<div class='row linha' style='margin-top: 10px'>";
  else
    return "<div class='row linha " + classe + "' style='margin-top: 10px'>";
  }
  
function FimLinha()
  {
  return "</div>";
  }
  
function CampoLabel( campo )
  {
  var cmp = "";
  if( campo.label && campo.label.length > 0 )
    {
    if( campo.nocmp && campo.nocmp.length > 0 )
      {
      cmp +=	"<label for='"+campo.nocmp+"' style='margin-left: 20px;'>"+campo.label+"</label>";
      }
    }
  cmp += "<input type='text' style='margin-left: 10px; width: "+campo.width+";' class='input-small";
  if( campo.inpclass && campo.inpclass.length > 0 )
    cmp += " " + campo.inpclass;			//	adiciona a classe
  cmp += "'";												//	fecha o class do input
  if( campo.valor && campo.valor.length > 0 )
    cmp += " value='" + campo.valor + "' vaorig='" + campo.valor + "'";
  else
    cmp += " value='' vaorig=''";
  if( campo.nocmp && campo.nocmp.length > 0 )
    cmp += " nocmp='" + campo.nocmp + "'";
  if( campo.extra && campo.extra.length > 0 )
    cmp += " " + campo.extra;
  cmp += "/>";
  return cmp;
  }
  
///	CampoTexto		cria o HTML adequado ao Bootstrap de campo input
///		campo
///			{
///			divclass		classe da DIV externa ao campo
///			width				tamanho da DIV. usar na forma de %
///			titulo			título do campo
///			inpclass		classe do input do campo
///			valor				valor inicial do campo
///			nocmp				nome do campo
///			extra				atributo(s) extra a colocar no input do campo
///			}
function CampoTexto( campo )
  {
  var cmp = "<div";
  if( campo.divclass && campo.divclass.length > 0 )
    cmp += " class='" + campo.divclass + "'";
  if( campo.width && campo.width.length > 0 )
    cmp += " style='width:" + campo.width + ";'";
  cmp += ">";
  
  if( campo.titulo && campo.titulo.length > 0 )
    cmp += " " + campo.titulo;
  cmp += "<input type='text' style='width: 100%;' class='form-control input-xs";
  if( campo.inpclass && campo.inpclass.length > 0 )
    cmp += " " + campo.inpclass;			//	adiciona a classe
  cmp += "'";												//	fecha o class do input
  if( campo.valor && campo.valor.length > 0 )
    cmp += " value='" + campo.valor + "' vaorig='" + campo.valor + "'";
  else
    cmp += " value='' vaorig=''";
  if( campo.nocmp && campo.nocmp.length > 0 )
    cmp += " nocmp='" + campo.nocmp + "'";
  if( campo.extra && campo.extra.length > 0 )
    cmp += " " + campo.extra;
  cmp += "></div>";
  return cmp;
  }
