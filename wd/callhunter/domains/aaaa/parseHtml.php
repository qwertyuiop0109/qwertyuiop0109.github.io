<?php

//получить чистый исходник для страницы, для анализа парсера
//вырезаем скрипты и выводим страницу



	define('_DEBUGMODE',1);;
	if(_DEBUGMODE)
	{
	    ini_set('display_errors', 1);
	    ini_set('display_startup_errors', 1);
	    error_reporting(E_ALL);
	}
	else
	    error_reporting(0);



//Отключение волшебных кавычек во время выполнения скрипта (отключение экранирования ' " \ null)
//кавычки потом могут понадобится для защиты от SQL-инъекций (тогда addslashes)
//http://php.net/manual/ru/info.configuration.php#ini.magic-quotes-gpc
//http://www.controlstyle.ru/articles/text/magic-quotes-gpc/
//http://php.net/manual/ru/security.magicquotes.disabling.php
//если не отключать, то символы ' " \ и null будут экранироваться обратным слешем (и добавлять лишние символы в сообщения)
//а также экранирование удорожает СМС!
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}



//	$url = "http://жалюзи.liart-okna.com.ua/";
//	$url = "https://kitchen.ua/p/kastryulya-s-kryishkoy-berghoff-bistro-1-3-l-1100090-99617";
//	$url = "https://opt.odejonka.com/catalog/dzhinsovaja-vetrovka/";


if (isset($_POST['url'])&&$_POST['url']) {

	$url = substr( ($_POST['url']), 0, 500);

$style = 0;

if (isset($_POST['style'])&&($_POST['style']))
	$style = 1;



//если надо жестко задать чарсет
	define('CHARSET','UTF-8');






    $parsed_url = parse_url ($url);


    if (isset($parsed_url["host"])) {
	$host = $parsed_url["host"];;
    } else {
	$host = '';
    }


    if (($host != "")&&(preg_match('/([^a-zA-Z0-9\-\.])/', $host, $match) > 0)) {
	$host = EncodePunycodeIDN($host);


	$schemeIDN   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : ''; 
	$hostIDN     = isset($parsed_url['host']) ? $parsed_url['host'] : ''; 
	$portIDN     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : ''; 
	$userIDN     = isset($parsed_url['user']) ? $parsed_url['user'] : ''; 
	$passIDN     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass']  : ''; 
	$passIDN     = ($userIDN || $passIDN) ? "$passIDN@" : ''; 
	$pathIDN     = isset($parsed_url['path']) ? $parsed_url['path'] : ''; 
	$queryIDN    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : ''; 
	$fragmentIDN = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : ''; 
	$url = "$schemeIDN$userIDN$passIDN$host$portIDN$pathIDN$queryIDN$fragmentIDN"; 
	$parsed_url = parse_url ($url);

    }





if (isset($_POST['html'])&&($_POST['html'])) {

    $html = $_POST['html'];

} else {


    $headers = get_pageHeadersBody ($url, 1);

    $html = $headers['pagebody'];

}




		if (CHARSET == 'UTF-8') {


//пытаемся определить чарсет

			$charset = '';

			if ($charset == '') {
				$ch = strtolower ($html);
			//"charset=utf-8"
				if (preg_match('/charset\s*=\s*[\"\']?([^\"\'\>\/\s]+)/', $ch, $match) > 0) {
					$charset = $match[1];
				}
	
			}


			if ($charset == '') {
				$ch = strtolower ($html);
		//      <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" >
		//          <meta charset="utf-8" >
				if (preg_match('/\<meta[^\>]+charset\s*\=\s*[\'\"]?\s*([^\/\"\'\> ]+)/', $ch, $match) > 0) {
					$charset = $match[1];
				}
			}

			if ($charset != '') {
			//определили чарсет

				$charset = strtoupper ($charset);


			} else {

	        	        echo "Info: for $url not found valid charset";
	        	        echo "<br>\n<br>\n";
	        	        echo $html;


				exit;

			}

		} else {
			$charset = CHARSET;
		}


		$charset = strtolower ($charset);



if ($charset == 'windows-1251') {
	$html=utf8_convert($html,"u");
} else if ($charset != 'utf-8') {
       echo "Info: for $url<br>found valid charset ". $charset . ",<br>but it not supported in this script.<br>Use original AdwordsApp script for testing this page.";
       exit;
}


//header('Content-type:text/html; charset='.$charset);
header('Content-type:text/html; charset=utf-8');



$html2 = $html;

$html = preg_replace("/(\<script.*\<\/script\>)/sU", "<script></script>", $html);
$html = preg_replace("/(\<noscript.*\<\/noscript\>)/sU", "<noscript></noscript>", $html);

if (!$style) {
	$html = preg_replace("/(\<link .*\>)/sU", "<link >", $html);
	$html = preg_replace("/(\<style.*\<\/style\>)/sU", "<style></style>", $html);
	$html = preg_replace("/(\<[^\>\<]+ style=[\"\'])[^\"\'\>\<]*([\"\'])/sU", "$1visibility: visible$2", $html);
}
if (!preg_match("/\<base href\=/si", $html)) 
	$html = preg_replace("/(\<head.*\>)/sU", "$1<base href='".$parsed_url['scheme']."://".$parsed_url["host"]."'>" , $html);




//echo $html;

//преобразуем переменную, что бы можно было выдать ее ниже в js без ошибок

//$html2 может отличаться от $html визуально, так как браузер исправляет огрехи кода,
//а $html2 специально выдает реальный код с ошибками


//везде заменяем <script на 2346fsdgsgdkLLsdgsdf7693763456073094569037426gfsg что бы потом вернуть
$html2 = preg_replace("/\<(scr)(ipt)(.*)\<\/(scr)(ipt)\>/sU", '<${1}2346fsdgsgdkLLsdgsdf7693763456073094569037426gfsg${2}${3}<\/${4}2346fsdgsgdkLLsdgsdf7693763456073094569037426gfsg${5}>', $html2);


/*

раньше работал этот код ниже, но теперь нужен полный код страницы, поэтому заменено на одну строку выше

//обязательно заменять на <\/script> вместо </script>
$html2 = preg_replace("/(\<script.*\<\/script\>)/sU", "<script><\/script>", $html2);
$html2 = preg_replace("/(\<noscript.*\<\/noscript\>)/sU", "<noscript><\/noscript>", $html2);
$html2 = preg_replace("/(\<style.*\<\/style\>)/sU", "<style><\/style>", $html2);

$html2 = preg_replace("/(\<link .*\>)/sU", "<link >", $html2);

*/


//например
//https://ledkontur.com.ua/ru/sadoviy-svitilnik-na-sonyachnikh-batareyakh-j600-1w-100lum-z-universalnim-kriplennyam/
//выдает ошибку на тексте  "1 Вт\5 В поликристалическая" - надо так сделать "1 Вт\\5 В поликристалическая"
//такую ошибку https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Errors/Deprecated_octal
//посмотрел здесь https://stackoverflow.com/questions/36790143/php-convert-octal-hexadecimal-escape-sequences

$html2 = preg_replace('/([^\\\\])(\\\\)([0-7]{1,3})/sU', '$1$2$2$3', $html2);

//а так для hexadecimal (но ненужно, нет такой ошибки с) hexadecimal
//$html2 = preg_replace('/([^\\\\])(\\\\)(x[0-9A-F]{1,2})/sU', '$1$2$2$3', $html2);



//везде заменяем ` что бы потом вернуть
$html2 = preg_replace("/(\`)/sU", "2346fsdgsgdkLLsdgsdf76937634560730945690374aaeLPi", $html2);




} else {
	$html = '';
	$html2 = $html;
}


echo $html;


?>




<!--Startasdfasdfasd-->



<script type="text/javascript">

window.onload = function ()
{



	document.ondblclick = function() {
		var b = document.getElementById('win143526733');
		b.style.display = "block";
//пояляется выделение, нужно убрать
		clearPageSelection();
	}



	var b = document.getElementById('win143526733');
	b.style.display = "none";


	var c = document.getElementById('closewin143526733');
	c.onclick = function() {
		var b = document.getElementById('win143526733');
		b.style.display = "none";
	}




}

var OUTPUT = '';

function clearPageSelection() {
	if (window.getSelection) {
		if (window.getSelection().empty) { // Chrome
			window.getSelection().empty();
		} else if (window.getSelection().removeAllRanges) { // Firefox
			window.getSelection().removeAllRanges();
		}
	} else if (document.selection && document.selection.empty) { // IE?
		document.selection.empty();
	}
} 

function checkPage() {



	var b = document.getElementById('urlwin143526733')
	if (b&&b.value) {
		var b = document.getElementById('formwin143526733')
		b.submit()
		return;
	}


//	var b = document.getElementById('checkwin143526733');
//	alert(b.value)


/*
	var html = document.documentElement.outerHTML;
//обязательно вырезать наш код, иначе не сработает окно
	html = html.replace(/\<\!\-\-Startasdfasdfasd\-\-\>[\S\s]*\<\!\-\-Endasdfasdfasd\-\-\>/, "")
*/



//	var c = document.getElementById('outputwin143526733');
//	c.innerHTML = b.value + "<br>" + (new Date());
//	c.innerHTML = html + "<br>" + (new Date());
//console.log(html)


	OUTPUT = '';

	checksiteconfig()

	var c = document.getElementById('outputwin143526733');
	c.innerHTML = OUTPUT + "<br>" + (new Date());


}


function strip_www(dom) {
	if((dom.substr(0, 4) == 'www.') || (dom.substr(0, 4) == 'wap.')) return dom.substr(4, dom.length);
	else if(dom.substr(0, 2) == 'm.') return dom.substr(2, dom.length);
	else return dom;
}


function checksiteconfig() {

	var url = document.getElementById('urlhiddenwin143526733').value;

	if (url) {
		OUTPUT = '<br>Url ' + url;
	} else {
		OUTPUT = '<br>Url not set';
	}


//	var [reterr,FeedConfig,scanids,configChecksum,configChangeFLG] = readFeedConfig(IDName,mainfolder,domainsfolder,filesfolder);
	var [reterr,FeedConfig,scanids,configChecksum,configChangeFLG] = readFeedConfig('');

	if (reterr) {
		return;
	}


	var foundurlflg = 0;

	for (var i=0; i < FeedConfig.length; i++) {


		var OneSiteConfig = FeedConfig[i];



		if (url) {
			var parsed_url = parse_url(url);
			var urlhost = parsed_url['host'];

			var parsed_SITE = parse_url(OneSiteConfig['SITE']);
			var SITEhost = parsed_SITE['host'];

//с www и без проверяем одинаково
			if (strip_www(urlhost.toLowerCase()) !== strip_www(SITEhost.toLowerCase())) {
				continue;
			}
		}

		foundurlflg = 1;


		//OUTPUT += '<br><br>SITE: ' + OneSiteConfig['SITE'];
		OUTPUT += '<br><br><font style="color:blue;">SITE: ' + OneSiteConfig['SITE'] + '</font>';


//		ScanCreateFeeds(FeedConfig[i],feedsAlreadyChecked,foundURLHASHdublicates);
		ScanCreateFeeds(OneSiteConfig);


//		var ScanCreateFeedsGlobalVars = {errors: "", stopscan: 0};
//		ScanCreateFeedsGlobalVars.SITE = OneSiteConfig['SITE'];

//alert(OneSiteConfig['SITE'])
//		for (var ii in FeedConfig[i]) {
//			if (FeedConfig[i]['feeds'][ii]) {
//				FeedConfig[i][ii]['startDaysHash'] = {};
//			}
//		}
	}

	if (!foundurlflg) {
		alert("Not found in config current url: " + url);
	}

}




function preparecheckFeeds() {

	var didExitEarly = false;


	var url = document.getElementById('urlhiddenwin143526733').value;

	if (url) {
//		OUTPUT += '<br>url ' + url;
		var SITE = url;
	} else {
		var SITE = ScanCreateFeedsGlobalVars.SITE;
	}


	var parsed_url = parse_url(SITE);
	if (parsed_url === false) {
		ScanCreateFeedsGlobalVars.errors += "Not good parameter SITE.\n";
		//alert(ScanCreateFeedsGlobalVars.errors);
		return didExitEarly;
	}


	host = parsed_url['host'];




	var match;
	if ((host) && (match = host.match(/([^a-zA-Z0-9\-\.])/)) && match[1]) {

	        host = idndomain(host);

	        var schemeIDN = parsed_url['protocol'] ? parsed_url['protocol'] + '://' : '';
	        var portIDN = parsed_url['port'] ? ':' + parsed_url['port'] : '';
        	var queryIDN = parsed_url['query'] ? parsed_url['query'] : '';
	        var url = schemeIDN + host + portIDN + queryIDN;
        	parsed_url = parse_url(url);
		if (parsed_url === false) {
			ScanCreateFeedsGlobalVars.errors += "Not good parameter SITE.\n";
			//alert(ScanCreateFeedsGlobalVars.errors);
			return didExitEarly;
		}

	} else {
		var url = SITE;
	}




//	var url2 = urlEncoding(url);
	if (ScanCreateFeedsGlobalVars&&ScanCreateFeedsGlobalVars.no_urlsearchtoLowerCase) {
		var match = url.match(/^([^?]*)(\?.*)?$/);
		var url2 = (match && match[1] !== undefined ? match[1].toLowerCase() : '') + (match && match[2] !== undefined ? match[2] : '');
	} else {
		var url2 = url.toLowerCase();
	}
	if (url2 !== url) {
		url = url2;
		parsed_url = parse_url(url);
	} else {
		url = url2;
	}


	if (host != "")  {
//добавил условие host, что бы явно указать, что правило ниже используется только на изначально полном url (хост указан на странице)
		if (parsed_url["pathname"].substr(0, 1) != '/') {
//добавляем слеш, если нет (скорее всего нет pathname) - не по правилам сформирована ссылка на сайте, например http://domain.com?aaa=1
			parsed_url = parse_url(url);
			url = (parsed_url.protocol ? parsed_url.protocol + '://' : '') + parsed_url.hostname + parsed_url.port + '/' + parsed_url.pathname + parsed_url.search;
			parsed_url = parse_url(url);
		}
	}





	if (host)  {
//добавил условие host, что бы явно указать, что правило ниже используется только на изначально полном url (хост указан на странице)
		if (parsed_url["pathname"].substr(0, 1) != '/') {
//добавляем слеш, если нет (скорее всего нет pathname) - не по правилам сформирована ссылка на сайте, например http://domain.com?aaa=1
			parsed_url = parse_url(url);
			url = (parsed_url.protocol ? parsed_url.protocol + '://' : '') + parsed_url.hostname + parsed_url.port + '/' + parsed_url.pathname + parsed_url.search;
			parsed_url = parse_url(url);
			if (parsed_url === false) {
				ScanCreateFeedsGlobalVars.errors += "Not good parameter SITE.\n";
				//alert(ScanCreateFeedsGlobalVars.errors);
				return didExitEarly;
			}
		}
	}



	ScanCreateFeedsGlobalVars.SITE_SCHEME = parsed_url['protocol'];
	var SITE_SCHEME = ScanCreateFeedsGlobalVars.SITE_SCHEME;
	ScanCreateFeedsGlobalVars.SITE_HOST = parsed_url['host'];
	var SITE_HOST = ScanCreateFeedsGlobalVars.SITE_HOST;


	ScanCreateFeedsGlobalVars.url = url;


	if (!SITE_SCHEME) {
		ScanCreateFeedsGlobalVars.errors += "Not good parameter SITE (protocol not exist).\n";
		//alert(ScanCreateFeedsGlobalVars.errors);
		return didExitEarly;
	}

	var valid_scheme = ScanCreateFeedsGlobalVars.valid_scheme;
	if (valid_scheme && valid_scheme.length) {
		if (SITE_SCHEME) {
			var exists = 0;
			for (var v in valid_scheme) {
				if (valid_scheme[v] == SITE_SCHEME) {
					exists = 1;
				}
			}
			if (!exists) {
				ScanCreateFeedsGlobalVars.errors += "Not good parameter SITE (it protocol not exist in parameter valid_scheme).\n";
				//alert(ScanCreateFeedsGlobalVars.errors);
				return didExitEarly;
			}
		}
	}


}


function checkFeeds(OneSiteConfig) {

	var didExitEarly = false;

	var url = ScanCreateFeedsGlobalVars.url;

       	var parsed_url = parse_url(url);

	var checkurl = parsed_url["pathname"] + parsed_url["search"];
	var URLID = murmurhash3_32_gc(checkurl, OneSiteConfig.seed) + '';

/*
	var html = document.documentElement.outerHTML;
//обязательно вырезать наш код, иначе не сработает окно
	html = html.replace(/\<\!\-\-Startasdfasdfasd\-\-\>[\S\s]*\<\!\-\-Endasdfasdfasd\-\-\>/, "")
*/

	var html = `<?php if ($html2) { echo $html2; } else { echo ''; }?>`;
//возвращаем `
	html = html.replace(/2346fsdgsgdkLLsdgsdf7693763456073094569037426gfsg/g, "");
	html = html.replace(/2346fsdgsgdkLLsdgsdf76937634560730945690374aaeLPi/g, "`");


	if (Encoder.hasEncoded(html)) {
		html = Encoder.htmlDecode(html);
	}


//вставлено один раз перед всеми парсерами страницы
    html = html.replace(/\r/g, " "); // Remove newlines
    html = html.replace(/\n/g, " "); // Remove newlines
    html = html.replace(/\t/g, " "); // Remove tabs

//сохраняем полный html (может понадобиться текст скриптов например)
var fullhtml = html;

    html = html.replace(/(\<\!\-\-.*?\-\-\>)/g, ""); // Remove commented text
    html = html.replace(/(\<script.*?\<\/script\>)/igm, '<s'+'cript></s'+'cript>');
    html = html.replace(/(\<noscript.*?\<\/noscript\>)/igm, '<nos'+'cript></nos'+'cript>');
    html = html.replace(/(\<link .*?\>)/igm, '<l'+'ink>');
    html = html.replace(/(\<style.*?\<\/style\>)/igm, '<s'+'tyle></s'+'tyle>');


	var feedsfields = {};


	for (var i in OneSiteConfig) {
//перебираем разрешенные сейчас фиды сайта

		if (OneSiteConfig['feeds'][i]) {
		//проверяем, что i - это идентификатор фида из конфига сайта, а не другой параметр

				OUTPUT += '<br><br><font style="color:blue;">FEED ' + i + '</font><br>';


				//указываем, что есть такой фид для дочерних, и он сейчас выполняеться, можно использовать для дочерних
				//ниже будет значение, если будет
				feedsfields[i] = null;


				var OneFeedGASearch = {}

				var OneFeedConfig = OneSiteConfig[i];

/*
				if (OneFeedConfig.MAX_ADD_URLS&&(scanned.parced_urls[i] >= OneFeedConfig.MAX_ADD_URLS)) {
					continue;
				} else {
					ScanCreateFeedsGlobalVars.stopscan = 0;
				}
*/

				if (SkipParseURL(parsed_url["pathname"] + parsed_url["search"],OneFeedConfig)) {
					//не проверяются ссылки href='#...' (проверены при скане)
					//везде удалены из ссылок анчоры #...
					//        alert("Skip URL " + url);
					continue;
				}




//проверяем наличие полей на странице
				var checkedfields = ParceFeedFields(html,fullhtml,OneFeedConfig,OneSiteConfig,URLID,url,url + '!...!' + url,feedsfields[OneFeedConfig['parentFeed']]);


				if (checkedfields&&checkedfields.checkedfields&&(Object.keys(checkedfields.checkedfields).length)) {
//проверка полей прошла успешно, поля подходят для записи фида

//проверяем в конце поля через checkFieldsFun

					var checkFieldsFun = OneFeedConfig.checkFieldsFun;


					if (checkFieldsFun) {
						try {

					                var noskip = checkFieldsFun(checkedfields.checkedfields,url);
							if (!noskip)
								continue;
						} catch (e) {
							ScanCreateFeedsGlobalVars.errors += 'bad function checkFieldsFun:' + e.message + "\n";
							//alert(ScanCreateFeedsGlobalVars.errors);
						}
					}


					if (!ScanCreateFeedsGlobalVars.errors) {


						feedsfields[i] = checkedfields.checkedfields;



	for (var ii in checkedfields.checkedfields) {
		if (checkedfields.checkedfields[ii]['field_value']) {
			OUTPUT += '<br>FIELD ' + checkedfields.checkedfields[ii]['field_id'] + ' ' + ii + '<br><font style="color:black;">' + Encoder.htmlEncode(checkedfields.checkedfields[ii]['field_value']) + '</font><br>';
		} else {
			OUTPUT += '<br>FIELD ' + checkedfields.checkedfields[ii]['field_id'] + ' ' + ii + '<br>EMPTY FIELD<br>';
		}
	}


						if (checkedfields.search_exist) {
							CreateSearch(checkedfields.checkedfields,OneSiteConfig.shortWordsHash,OneFeedGASearch,OneFeedConfig,OneSiteConfig,URLID,i,url);
						}



					}

//if (ScanCreateFeedsGlobalVars.errors) {
//	alert(ScanCreateFeedsGlobalVars.errors)
//}




				} else {
					continue;
				}




		}
	}

}




function CreateSearch(checkedfields,shortWordsHash,OneFeedGASearch,OneFeedConfig,OneSiteConfig,URLID,feed_id,url) {

	function parvalue(parameterType,parameterName,parameter,checkedfields) {
		//вычисляем типовые значения параметров для поисковых кампаний


		var partype = { 
			'array3': function (parameterName,parameter,checkedfields) {
				var res = '';
				for (var i = 0; i < parameter.length; i++) {
//					if (typeof parameter[i] != 'string') {
//						ScanCreateFeedsGlobalVars.errors += 'bad parameter: ' + parameterName + " in GASearch\nfor url: " + url + "\n";
//						console.log(ScanCreateFeedsGlobalVars.errors);
//						return '';
//					}

					if (parameter[i]) {
						if (i == 1) {

//							var recfields = parameter[i].split(/[\s\,]+/);
							var recfields = parameter[i].split(/[\s\,]*\,[\s\,]*/);
							for (var ii = 0; ii < recfields.length; ii++) {

								if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['include_attributes']) {
//нельзя в поисковых параметрах использовать поля в виде вложенных аттрибутов 
									ScanCreateFeedsGlobalVars.errors += 'bad parameter: ' + parameterName + ' in GASearch,' + ' field_name ' + recfields[ii] + ' have "include attributes" type, not use it in GASearch.' + "\n";
									console.log(ScanCreateFeedsGlobalVars.errors);
									return '';

								} else if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
									if (res) {
										res += " " + checkedfields[recfields[ii]]['field_value'];
									} else {
										res += checkedfields[recfields[ii]]['field_value'];
									}

								} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует одно из полей в настройках фида
									ScanCreateFeedsGlobalVars.errors += 'bad parameter: ' + parameterName + ' in GASearch,' + ' not exists field_name ' + recfields[ii] + ' in feed fields.' + "\n";
									console.log(ScanCreateFeedsGlobalVars.errors);
									return '';
								}
							}

						} else {
							if (res) {
								res += " " + parameter[i];
							} else {
								res += parameter[i];
							}
						}
					}
				
				}

				return res;
			},
			'function': function (parameterName,parameter,checkedfields) {
		        	try {
					var res = parameter(checkedfields);
					if (typeof res != 'string') {
						ScanCreateFeedsGlobalVars.errors += 'bad function result (no string type), parameter: ' + parameterName + " in GASearch\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return '';
					} else if (/\n/.test(res)) {
						//проверяем наличие символа \n - его не должно быть, это признак поля в виде вложенных аттрибутов 
						ScanCreateFeedsGlobalVars.errors += 'bad parameter (function): ' + parameterName + ' in GASearch, some field have "include attributes" type or Newline symbol, not use it in GASearch function.' + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return '';
					}
					res = res.trim()

					return res;

				} catch (e) {
					ScanCreateFeedsGlobalVars.errors += 'bad function, parameter: ' + parameterName + " in GASearch\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return '';
				}
			},
			'': function (parameterName,parameter,checkedfields) {
				return '';
			}
		}

		try {
			return partype[parameterType](parameterName,parameter,checkedfields);
		} catch (e) {
			ScanCreateFeedsGlobalVars.errors += 'bad parameter: ' + parameterName + ', parameterType: ' + parameterType + " in GASearch\n";
			console.log(ScanCreateFeedsGlobalVars.errors);
			return '';
		}

	}




	var keywordtypesgen = {
		0: function(key,keys,phrase) {
			keys['EXACT'][key] = 1;
			keys['BROAD'][key.replace(/([^\s]+)/g, '+$1')] = 1;
		},
		1: function(key,keys,phrase) {
			keys['EXACT'][key] = 1;
		},
		2: function(key,keys,phrase) {
			keys['BROAD'][key.replace(/([^\s]+)/g, '+$1')] = 1;
		},
		3: function(key,keys,phrase) {
			keys['EXACT'][key] = 1;
			keys['PHRASE'][key] = 1;
		},
		4: function(key,keys,phrase) {
			keys['PHRASE'][key] = 1;
		},
		5: function(key,keys,phrase) {
			if (phrase) {
				keys['EXACT'][key] = 1;
				keys['PHRASE'][key] = 1;
			} else {
				keys['EXACT'][key] = 1;
				keys['BROAD'][key.replace(/([^\s]+)/g, '+$1')] = 1;
			}
		},
		6: function(key,keys,phrase) {
			if (phrase) {
				keys['PHRASE'][key] = 1;
			} else {
				keys['BROAD'][key.replace(/([^\s]+)/g, '+$1')] = 1;
			}
		}
	}



	var addkeystypegen = {
		0: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[0].length; ii++) {
				//цифровые КС
				keywordtypesgen[keywordtypes](feedkeys[0][ii],dkeys,keywordtypes > 4);
				//dkeys[feedkeys[0][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[1].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[1][ii],keys);
				//keys[feedkeys[1][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[2].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[2][ii],addkeys);
				//addkeys[feedkeys[2][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		1: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[0].length; ii++) {
				//цифровые КС
				keywordtypesgen[keywordtypes](feedkeys[0][ii],keys,keywordtypes > 4);
				//keys[feedkeys[0][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[1].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[1][ii],keys);
				//keys[feedkeys[1][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[2].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[2][ii],addkeys);
				//addkeys[feedkeys[2][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		2: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[1].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[1][ii],keys);
				//keys[feedkeys[1][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[2].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[2][ii],addkeys);
				//addkeys[feedkeys[2][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		3: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[0].length; ii++) {
				//цифровые КС
				keywordtypesgen[keywordtypes](feedkeys[0][ii],keys,keywordtypes > 4);
				//keys[feedkeys[0][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[1].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[1][ii],keys);
				//keys[feedkeys[1][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		4: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[0].length; ii++) {
				//цифровые КС
				keywordtypesgen[keywordtypes](feedkeys[0][ii],keys,keywordtypes > 4);
				//keys[feedkeys[0][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		5: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[1].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[1][ii],keys);
				//keys[feedkeys[1][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		6: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[0].length; ii++) {
				//цифровые КС
				keywordtypesgen[keywordtypes](feedkeys[0][ii],keys,keywordtypes > 4);
				//keys[feedkeys[0][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[1].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[1][ii],keys);
				//keys[feedkeys[1][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[2].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[2][ii],keys);
				//keys[feedkeys[2][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		7: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[1].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[1][ii],keys);
				//keys[feedkeys[1][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[2].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[2][ii],keys);
				//keys[feedkeys[2][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		},
		8: function(feedkeys,keys,dkeys,addkeys,keywordtypes) {
			for (var ii = 0; ii < feedkeys[2].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[2][ii],keys);
				//keys[feedkeys[2][ii]] = 1;
			}
			for (var ii = 0; ii < feedkeys[3].length; ii++) {
				keywordtypesgen[keywordtypes](feedkeys[3][ii],keys);
				//keys[feedkeys[3][ii]] = 1;
			}
		}
	}



	var GAS = OneFeedConfig['GASearch'];


	if (GAS&&(GAS['Id']||GAS['GAShtml'])) {

		if (GAS.hasOwnProperty('removeOutOfStockItem')) {
			if (!checkedfields.hasOwnProperty(GAS['removeOutOfStockItem'][1])) {
				ScanCreateFeedsGlobalVars.errors += 'bad parameter: removeOutOfStockItem in GASearch, not exists field_name ' + GAS['removeOutOfStockItem'][1] + ' in feed fields.' + "\n";
				console.log(ScanCreateFeedsGlobalVars.errors);
				return;
			}
			if (checkedfields[GAS['removeOutOfStockItem'][1]]['field_value'] == GAS['removeOutOfStockItem'][2]) {
				if (OneFeedGASearch[URLID]) {
					//остановить или удалить существующую группу
					if (GAS['removeOutOfStockItem'][0]) {
						removeAdGroupbuffer(OneFeedGASearch[URLID][0]);
					} else {
						pauseAdGroupbuffer(OneFeedGASearch[URLID][0]);
					}

					//убираем обработанную группу, что бы в конце остановить/удалить оставшиеся необработанные
					delete OneFeedGASearch[URLID];
				}
				return;
			}

		}



		//генерируем новые данные и хеши данных

		//генерируем GASvalues - хеш значений типовых переменных GAS РК (GASparams)
		var GASvalues = {};
		for (var ii = 0; ii < GAS.GASparams.length; ii++) {
			var GASparamName = GAS.GASparams[ii][0];
			var GASparamType = GAS.GASparams[ii][1];

			if ((GASparamName == 'keys')&&(typeof GASparamType == 'object')) {
				GASvalues[GASparamName] = [];
				for (var iii = 0; iii < GASparamType.length; iii++) {
					//если есть типовая переменная РК (из списка GAS.GASparams) то вычисляем ее значение (массив)
					GASvalues[GASparamName][iii] = parvalue(GASparamType[iii],GASparamName,GAS[GASparamName][iii],checkedfields);
				}
			} else {
				//если есть типовая переменная РК (из списка GAS.GASparams) то вычисляем ее значение
				GASvalues[GASparamName] = parvalue(GASparamType,GASparamName,GAS[GASparamName],checkedfields);
			}
		}


		//генерируем GASADvalues - хеш значений типовых переменных GAS объявлений (GASADparams)
		var GASADvalues = {};
		for (var ii = 0; ii < GAS['adids'].length; ii++) {
			var GASADId = GAS['adids'][ii];
			GASADvalues[GASADId] = {}
			for (var iii = 0; iii < GAS[GASADId].GASADparams.length; iii++) {
				var GASADparamName = GAS[GASADId].GASADparams[iii][0];
				var GASADparamType = GAS[GASADId].GASADparams[iii][1];

				//если есть типовая переменная объявления (из списка GASADparams) то вычисляем ее значение
				if (GAS[GASADId]['FromGAS'][GASADparamName]) {
					//этот параметр берется из настройки РК, а не Объявления, поэтому сокращаем время выполнения, присваиваем уже вычисленное значение
					GASADvalues[GASADId][GASADparamName] = GASvalues[GASADparamName];
				} else {
					if ((GASADparamName == 'keys')&&(typeof GASADparamType == 'object')) {
						GASADvalues[GASADId][GASADparamName] = [];
						for (var iii = 0; iii < GASADparamType.length; iii++) {
							GASADvalues[GASADId][GASADparamName][iii] = parvalue(GASADparamType[iii],GASADparamName,GAS[GASADId][GASADparamName][iii],checkedfields);
						}
					} else {
						GASADvalues[GASADId][GASADparamName] = parvalue(GASADparamType,GASADparamName,GAS[GASADId][GASADparamName],checkedfields);
					}
				}
			}
		}


		if (ScanCreateFeedsGlobalVars.errors)
			return;




//вычисляем значения для рекламы

		//проверяем, что хотя бы одно значение параметра keys не пустое
		var keysnotemptyflg = 0;


		var keys = {'EXACT': {},'BROAD': {},'PHRASE': {}};
		var dkeys = {'EXACT': {},'BROAD': {},'PHRASE': {}};
		var addkeys = {'EXACT': {},'BROAD': {},'PHRASE': {}};
		var ads = {};
		var params = '';
		var params2 = '';


		if (GASvalues['keys']) {
			if (typeof GASvalues['keys'] == 'object') {
				for (var ii = 0; ii < GASvalues['keys'].length; ii++) {
					if (GASvalues['keys'][ii]) {
						keysnotemptyflg = 1;
						var feedkeys = ItemKeys(GASvalues['keys'][ii],shortWordsHash,GAS.keysmixtype,GAS.maxNodigaddkeyNum,GASvalues['fixedkeys'],GAS.notReqfixedkeys,GASvalues['onekeys']);
						if (feedkeys) {

					        	try {
								addkeystypegen[GAS.addkeystype](feedkeys,keys,dkeys,addkeys,GAS.keywordtypes);
							} catch (e) {
								ScanCreateFeedsGlobalVars.errors += "bad parameter addkeystype in GASearch, check script code\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
							}

/*
						} else if (feedkeys !== '') {

есть непустое значение GASvalues['keys']
и это не ошибка еще (например название товара состоит из одного слова)

*/
                	
						}
					}

				}
			} else {
				keysnotemptyflg = 1;
				var feedkeys = ItemKeys(GASvalues['keys'],shortWordsHash,GAS.keysmixtype,GAS.maxNodigaddkeyNum,GASvalues['fixedkeys'],GAS.notReqfixedkeys,GASvalues['onekeys']);
				if (feedkeys) {

			        	try {
						addkeystypegen[GAS.addkeystype](feedkeys,keys,dkeys,addkeys,GAS.keywordtypes);
					} catch (e) {
						ScanCreateFeedsGlobalVars.errors += "bad parameter addkeystype in GASearch, check script code\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
					}

/*

				} else if (feedkeys !== '') {

есть непустое значение GASvalues['keys']
и это не ошибка еще (например название товара состоит из одного слова)

*/

				}
			}
		}
		if (GASvalues['masskeys']) {
			var masskeys = GASvalues['masskeys'].trim()
			masskeys = masskeys.split(/[\s\,]*\,[\s\,]*/);
			for (var iii = 0; iii < masskeys.length; iii++) {
				if (masskeys[iii]) {
					keysnotemptyflg = 1;
					var feedkeys = ItemKeys(masskeys[iii],shortWordsHash,GAS.masskeysmixtype,GAS.maxNodigaddkeyNum,GASvalues['fixedkeys'],GAS.notReqfixedkeys,GASvalues['onekeys']);
					if (feedkeys) {
				        	try {
							addkeystypegen[GAS.addkeystype](feedkeys,keys,dkeys,addkeys,GAS.keywordtypes);
						} catch (e) {
							ScanCreateFeedsGlobalVars.errors += "bad parameter addkeystype in GASearch, check script code\n";
							console.log(ScanCreateFeedsGlobalVars.errors);
						}

					}

				}
			}
		}


		//хотя бы одно объявление сформировать
		var checkadsnum=0
		for (var ii = 0; ii < GAS['adids'].length; ii++) {
			var GASADId = GAS['adids'][ii];

//начальная переделка под адаптивные объявления
//!!!@@@@@@@@@@@@@@@

//			var adheaders = ItemHeaders(GASADvalues[GASADId]['header'],shortWordsHash,GASADvalues[GASADId]['paths'],GASADvalues[GASADId]['header2'],GAS[GASADId]['keyword'],GASADvalues[GASADId]['startdescription'],GASADvalues[GASADId]['beforeprice'],GASvalues['price'],GASADvalues[GASADId]['afterprice'],GASADvalues[GASADId]['description'],GASADvalues[GASADId]['afterdescription'],GAS[GASADId]['priceaddtype'],GAS['priceRound'],GAS[GASADId]['noPaths'],GAS[GASADId]['headerTo1Line'],GAS[GASADId]['afterdescrNeed'],GAS[GASADId]['requiredescr'],0);
			var adheaders = ItemHeaders(GASADvalues[GASADId]['header'],shortWordsHash,GASADvalues[GASADId]['paths'],GASADvalues[GASADId]['header2'],GAS['header3'],GAS[GASADId]['keyword'],GASADvalues[GASADId]['startdescription'],GASADvalues[GASADId]['beforeprice'],GASvalues['price'],GASADvalues[GASADId]['afterprice'],GASADvalues[GASADId]['description'],GASADvalues[GASADId]['afterdescription'],GAS[GASADId]['priceaddtype'],GAS['priceRound'],GAS[GASADId]['noPaths'],GAS[GASADId]['headerTo1Line'],GAS[GASADId]['afterdescrNeed'],GAS[GASADId]['requiredescr'],0);

			if (adheaders) {

				ads[GASADId] = {};
				ads[GASADId].Headlines = adheaders.Headlines;
				ads[GASADId].Descriptions = adheaders.Descriptions;
				ads[GASADId].Paths = adheaders.Paths;
				ads[GASADId].Params = adheaders.Params;

				if (ads[GASADId].Params[0]) {
					//если хотя бы одно объявление имеет параметр (цену)
					params = ads[GASADId].Params[0];
				}
				if (ads[GASADId].Params[1]) {
					params2 = ads[GASADId].Params[1];
				}

				checkadsnum++;

				if (!(GAS[GASADId]['FromGAS']['addkeystype']&&GAS[GASADId]['FromGAS']['maxNodigaddkeyNum']&&GAS[GASADId]['FromGAS']['keys']&&GAS[GASADId]['FromGAS']['fixedkeys']&&GAS[GASADId]['FromGAS']['masskeys']&&GAS[GASADId]['FromGAS']['onekeys']&&GAS[GASADId]['FromGAS']['keysmixtype']&&GAS[GASADId]['FromGAS']['masskeysmixtype']&&GAS[GASADId]['FromGAS']['notReqfixedkeys'])) {
					//для КС исключаем повторную обработку (FromGAS)

					if (typeof GASADvalues[GASADId]['keys'] == 'object') {
						for (var iii = 0; iii < GASADvalues[GASADId]['keys'].length; iii++) {
							if (GASADvalues[GASADId]['keys'][iii]) {
								keysnotemptyflg = 1;
								var feedkeys = ItemKeys(GASADvalues[GASADId]['keys'][iii],shortWordsHash,GAS[GASADId]['keysmixtype'],GAS[GASADId]['maxNodigaddkeyNum'],GASADvalues[GASADId]['fixedkeys'],GAS[GASADId]['notReqfixedkeys'],GASADvalues[GASADId]['onekeys']);
								if (feedkeys) {

							        	try {
										addkeystypegen[GAS[GASADId]['addkeystype']](feedkeys,keys,dkeys,addkeys,GAS.keywordtypes);
									} catch (e) {
										ScanCreateFeedsGlobalVars.errors += "bad parameter addkeystype in GASearch in Ad number " + GASADId + ", check script code\n";
										console.log(ScanCreateFeedsGlobalVars.errors);
									}


								}
							}

						}


					} else {
						if (GASADvalues[GASADId]['keys']) {
							keysnotemptyflg = 1;
							var feedkeys = ItemKeys(GASADvalues[GASADId]['keys'],shortWordsHash,GAS[GASADId]['keysmixtype'],GAS[GASADId]['maxNodigaddkeyNum'],GASADvalues[GASADId]['fixedkeys'],GAS[GASADId]['notReqfixedkeys'],GASADvalues[GASADId]['onekeys']);
							if (feedkeys) {

						        	try {
									addkeystypegen[GAS[GASADId]['addkeystype']](feedkeys,keys,dkeys,addkeys,GAS.keywordtypes);
								} catch (e) {
									ScanCreateFeedsGlobalVars.errors += "bad parameter addkeystype in GASearch in Ad number " + GASADId + ", check script code\n";
									console.log(ScanCreateFeedsGlobalVars.errors);
								}


							}
						}

					}
					if (GASADvalues[GASADId]['masskeys']) {
						var masskeys = GASADvalues[GASADId]['masskeys'].trim()
						masskeys = masskeys.split(/[\s\,]*\,[\s\,]*/);
						for (var iii = 0; iii < masskeys.length; iii++) {
							if (masskeys[iii]) {
								keysnotemptyflg = 1;
								var feedkeys = ItemKeys(masskeys[iii],shortWordsHash,GAS[GASADId]['masskeysmixtype'],GAS[GASADId]['maxNodigaddkeyNum'],GASADvalues[GASADId]['fixedkeys'],GAS[GASADId]['notReqfixedkeys'],GASADvalues[GASADId]['onekeys']);
								if (feedkeys) {
							        	try {
										addkeystypegen[GAS[GASADId]['addkeystype']](feedkeys,keys,dkeys,addkeys,GAS.keywordtypes);
									} catch (e) {
										ScanCreateFeedsGlobalVars.errors += "bad parameter addkeystype in GASearch in Ad number " + GASADId + ", check script code\n";
										console.log(ScanCreateFeedsGlobalVars.errors);
									}

								}

							}
						}
					}


				}

			} else {

				if ((!GASADvalues[GASADId]['header'])||(!(GASADvalues[GASADId]['startdescription'] + GASADvalues[GASADId]['description'] + GASADvalues[GASADId]['afterdescription']))) {
					ScanCreateFeedsGlobalVars.errors += "bad parameters, header or etc. in GASearch\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
				} else {
					//если есть заголовки и описание, то это не ошибка, например, очень длинное слово не даст сформировать заголовок
					//просто выходим, но не сейчас, ниже, если нет ни одного объявления
				}
			}

		}


		var keysCOLLECT = [];
		var keysFULL = [];
		Object.keys(keys['EXACT']).forEach(function(key) { 
			keysFULL.push('[' + key + ']');
			keysCOLLECT.push(key);
		});
		Object.keys(keys['PHRASE']).forEach(function(key) { 
			keysFULL.push('"' + key + '"');
			keysCOLLECT.push(key);
		});
		Object.keys(keys['BROAD']).forEach(function(key) { 
			keysFULL.push(key);
			keysCOLLECT.push(key);
		});


		var dkeysCOLLECT = [];
		var dkeysFULL = [];
		Object.keys(dkeys['EXACT']).forEach(function(key) { 
			dkeysFULL.push('[' + key + ']');
			dkeysCOLLECT.push(key);
		});
		Object.keys(dkeys['PHRASE']).forEach(function(key) { 
			dkeysFULL.push('"' + key + '"');
			dkeysCOLLECT.push(key);
		});
		Object.keys(dkeys['BROAD']).forEach(function(key) { 
			dkeysFULL.push(key);
			dkeysCOLLECT.push(key);
		});


		var addkeysCOLLECT = [];
		var addkeysFULL = [];
		Object.keys(addkeys['EXACT']).forEach(function(key) { 
			addkeysFULL.push('[' + key + ']');
			addkeysCOLLECT.push(key);
		});
		Object.keys(addkeys['PHRASE']).forEach(function(key) { 
			addkeysFULL.push('"' + key + '"');
			addkeysCOLLECT.push(key);
		});
		Object.keys(addkeys['BROAD']).forEach(function(key) { 
			addkeysFULL.push(key);
			addkeysCOLLECT.push(key);
		});


		var checkkeysnum = 1;
		if ((!keysCOLLECT.length)&&(!dkeysCOLLECT.length)&&(!addkeysCOLLECT.length)) {
			checkkeysnum = 0;
			//если нет ключей - выходим, но это не ошибка еще (например название товара состоит из одного слова)
			if (!keysnotemptyflg) {
				//если все значения для генерации ключей пустые - это точно ошибка
				ScanCreateFeedsGlobalVars.errors += "bad parameter keys in GASearch\n";
				console.log(ScanCreateFeedsGlobalVars.errors);
			}
			//можно выходить, ниже выходим
		}


		if (ScanCreateFeedsGlobalVars.errors)
			return;


		//выходим, если нельзя создать хотя бы одно объявление или КС (нет данных для них)
		if ((!checkadsnum)||(!checkkeysnum))
			return;






	for (var ii in checkedfields.checkedfields) {
		if (checkedfields.checkedfields[ii]['field_value']) {
			OUTPUT += '<br>FIELD ' + checkedfields.checkedfields[ii]['field_id'] + ' ' + ii + '<br><font style="color:black;">' + Encoder.htmlEncode(checkedfields.checkedfields[ii]['field_value']) + '</font><br>';
		} else {
			OUTPUT += '<br>FIELD ' + checkedfields.checkedfields[ii]['field_id'] + ' ' + ii + '<br>EMPTY FIELD<br>';
		}
	}



			OUTPUT += '<br>GASEARCH<br>';





							for (var ii = 0; ii < GAS['adids'].length; ii++) {
								var GASADId = GAS['adids'][ii];
								if (ads[GASADId]) {


			OUTPUT += '<br>AD ' + GASADId;



			OUTPUT += '<br>url ' + '<br><font style="color:black;">' + Encoder.htmlEncode(GASvalues['url']) + '</font><br>';
			if (GASvalues['mobileurl'])
				OUTPUT += '<br>mobileurl ' + '<br><font style="color:black;">' + Encoder.htmlEncode(GASvalues['mobileurl']) + '</font><br>';
			OUTPUT += '<br>Headline1 ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Headlines[0]) + '</font><br>';
			OUTPUT += '<br>Headline2 ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Headlines[1]) + '</font><br>';
			if (ads[GASADId].Headlines[2])
				OUTPUT += '<br>Headline3 ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Headlines[2]) + '</font><br>';

			if (ads[GASADId].Paths[0])
				OUTPUT += '<br>Path1 ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Paths[0]) + '</font><br>';
			if (ads[GASADId].Paths[1])
				OUTPUT += '<br>Path2 ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Paths[1]) + '</font><br>';




			if (ads[GASADId].Descriptions[1]) {
				OUTPUT += '<br>Description1 ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Descriptions[0]) + '</font><br>';
				OUTPUT += '<br>Description2 ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Descriptions[1]) + '</font><br>';
			} else {
				OUTPUT += '<br>Description ' + '<br><font style="color:black;">' + Encoder.htmlEncode(ads[GASADId].Descriptions[0]) + '</font><br>';
			}



								}
							}







			OUTPUT += '<br>KEYS';


			for (var ii = 0; ii < keysCOLLECT.length; ii++) {

					if (keysCOLLECT[ii].length > 80) {
						//В ключевом слове должно быть не больше 80 символов (включается все символы, в т.ч. + "), кроме символов []"
						continue;
					}


			OUTPUT += '<br><font style="color:black;">' + Encoder.htmlEncode(keysFULL[ii]) + '</font>';

			}




			if (addkeysCOLLECT.length) {
				//значит это вариант с проверкой на "мало показов" и есть КС с 2-мя словами



//формируем дополнительные добавляемые КС

					for (var ii = 0; ii < addkeysCOLLECT.length; ii++) {


						if (addkeysCOLLECT[ii].length > 80) {
								//В ключевом слове должно быть не больше 80 символов (включается все символы, в т.ч. + "), кроме символов []"
								continue;
							}


			OUTPUT += '<br><font style="color:black;">' + Encoder.htmlEncode(addkeysFULL[ii]) + '</font>';


					}


			}





			if (dkeysCOLLECT.length) {
				//значит это вариант с проверкой на "мало показов" без цифровых ключей (сначала проверяем только 3 КС)


//формируем в конце отдельно цифровые добавляемые КС

				for (var ii = 0; ii < dkeysCOLLECT.length; ii++) {


						if (dkeysCOLLECT[ii].length > 80) {
							//В ключевом слове должно быть не больше 80 символов (включается все символы, в т.ч. + "), кроме символов []"
							continue;
						}

        
			OUTPUT += '<br><font style="color:black;">' + Encoder.htmlEncode(dkeysFULL[ii]) + '</font>';


				}


			}






	}









		return 1;




}





function ParceFeedFields(html,fullhtml,OneFeedConfig,OneSiteConfig,URLID,url,referrer_url,feedsfields) {
//находим все поля фида

	var checkedfields = {};

	var search_exist = 1;
	//var search_flg = OneFeedConfig['GASearch']&&OneFeedConfig['GASearch']['Id'];
	var search_flg = OneFeedConfig['GASearch']&&(OneFeedConfig['GASearch']['Id'] || OneFeedConfig['GASearch']['GAShtml']);
//ниже условие надо добавить, долго объяснять почему, иначе - например есть два поля в фиде, оба имеют field_required = 4 и нет настройки поиска (GASearch) но есть генерации файлов фида, то при пустом одном и непустом втором запись будет создана, что неправильно
	if ((!search_flg)&&(!OneFeedConfig['all_fields_not_required']))
		search_exist = 0;

	var feedfiles_exist = 1;
	var feedfiles_flg = Object.keys(OneFeedConfig['feed_fields_file_types']).length;
//ниже условие надо добавить, долго объяснять почему, иначе - например есть два поля в фиде, оба имеют field_required = 5 и есть настройка поиска (GASearch) но нет генерации файлов фида, то при пустом одном и непустом втором запись будет создана, что неправильно
	if ((!feedfiles_flg)&&(!OneFeedConfig['all_fields_not_required']))
		feedfiles_exist = 0;

	for (var i in OneFeedConfig) {
//перебираем разрешенные сейчас поля фида сайта
		if (OneFeedConfig['fields'][i]) {
			var fieldId = i;
			var fieldHash = OneFeedConfig[fieldId];

			var field_name = fieldHash['field_name'];

			if (checkedfields[field_name]&&checkedfields[field_name]['field_value']) {
				//если поле имеет непустое значение, то пропускаем (дубликат поля - не ошибка)
				continue;
			}

			var field_type = fieldHash['field_type'];
			var field_required = fieldHash['field_required'];
			var field_value = '';

//флаг поля с вложенными атрибутами (полями и их значениями)
			var include_attributes = 0;

			if (fieldHash.hasOwnProperty('field_length')) {
				var field_length = fieldHash['field_length'];
				if ((typeof field_length !== 'string')&&(typeof field_length !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_length, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}
				if (!/^\d+$/.test(field_length)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_length, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}
				field_length = field_length * 1; //переводим в число
			} else {
				var field_length = 0;
			}



			if (field_type === 'html') {

				if (typeof fieldHash['parsing_type'] !== 'string') {
					ScanCreateFeedsGlobalVars.errors += 'bad parsing_type, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				var badf = 0;
				var fvsArr = 0;
				if (fieldHash['field_value_selector']) {
					if (typeof fieldHash['field_value_selector'] === 'string') {
					} else if (({}.toString.call(fieldHash['field_value_selector']) === '[object Array]')&&fieldHash['field_value_selector'].length) {
						fvsArr = 1;
						for (var ii = 0; ii < fieldHash['field_value_selector'].length; ii++) {
							if ((typeof fieldHash['field_value_selector'][ii] !== 'string') || (!fieldHash['field_value_selector'][ii])) {
								badf = 1;
							}
						}
					} else {
						badf = 1;
					}
				} else if (typeof fieldHash['field_value_selector'] !== 'string') {
					badf = 1;
				}


				if (badf) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				var htmlhtml = html;

				if (fvsArr) {
					for (var ii = 0; ii < fieldHash['field_value_selector'].length - 1; ii++) {
						var h = parseHtml(htmlhtml, fieldHash['field_value_selector'][ii], 'text');
						if (h['errors']) {
							ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
							console.log(ScanCreateFeedsGlobalVars.errors);
							return;
						} else {
							htmlhtml = h['result'];
						}
					}
					var field_value_selector = fieldHash['field_value_selector'][fieldHash['field_value_selector'].length - 1];
				} else {
					var field_value_selector = fieldHash['field_value_selector'];
				}

				if ((fieldHash['parsing_type'] == 'iso4217price')||(fieldHash['parsing_type'] == 'nativeprice')) {
					var h = parseHtml(htmlhtml, field_value_selector, 'text', fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;

					} else if (h['result']) {
						if (fieldHash['parsing_type'] == 'nativeprice') {
							//nativeprice
				
							var parsedcena = getPriceCurrencyFromStr(h['result']);
		
							if (parsedcena[0]&&parsedcena[0][0]&&parsedcena[1]&&parsedcena[1][0]) {

								if (parsedcena[1][1]) {
									field_value = parsedcena[0][0] + ' ' + parsedcena[1][1];
								} else {
									field_value = parsedcena[0][0] + ' ' + parsedcena[1][0];
								}
							
							}

						} else {

							//iso4217price

							var parsedcena = getPriceCurrencyFromStr(h['result']);

							if (parsedcena[0]&&parsedcena[0][1]&&parsedcena[1]&&parsedcena[1][0]) {

								field_value = parsedcena[0][1] + ' ' + parsedcena[1][0];
						
							}



						}
					}




				} else {
					var h = parseHtml(htmlhtml, field_value_selector, fieldHash['parsing_type'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);
					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else {
						field_value = h['result'];

						field_value = digitkeysproc(field_value,fieldHash['field_digitkeys']);
					}
				}



//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


				if ((fieldHash['parsing_type'] == 'iso4217price')||(fieldHash['parsing_type'] == 'nativeprice')) {
//					if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
					if (field_length) {
//системный параметр
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
						ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}
				} else if ((fieldHash['parsing_type'] == 'text')||(fieldHash['parsing_type'] === '')) {

					field_value = getMaxStr(field_value, field_length);

				} else {
//					if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
					if (field_length) {
//src href и др. атрибуты тега селектора
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
						ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}

				}


//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (field_required === 3) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует выше одно из полей, указанных в field_required
								ScanCreateFeedsGlobalVars.errors += 'bad field_required:'+ ' not exists field_name ' + recfields[ii] + ' in previos fields, field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						}
//						if ((!fieldHash['field_Fun'])&&foundnotemptyfields) {
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}



			} else if (field_type === 'fullhtml') {
			//копия предыдущего пункта, но для fullhtml

				if (typeof fieldHash['parsing_type'] !== 'string') {
					ScanCreateFeedsGlobalVars.errors += 'bad parsing_type, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				var badf = 0;
				var fvsArr = 0;
				if (fieldHash['field_value_selector']) {
					if (typeof fieldHash['field_value_selector'] === 'string') {
					} else if (({}.toString.call(fieldHash['field_value_selector']) === '[object Array]')&&fieldHash['field_value_selector'].length) {
						fvsArr = 1;
						for (var ii = 0; ii < fieldHash['field_value_selector'].length; ii++) {
							if ((typeof fieldHash['field_value_selector'][ii] !== 'string') || (!fieldHash['field_value_selector'][ii])) {
								badf = 1;
							}
						}
					} else {
						badf = 1;
					}
				} else if (typeof fieldHash['field_value_selector'] !== 'string') {
					badf = 1;
				}


				if (badf) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				var htmlhtml = fullhtml;

				if (fvsArr) {
					for (var ii = 0; ii < fieldHash['field_value_selector'].length - 1; ii++) {
						var h = parseHtml(htmlhtml, fieldHash['field_value_selector'][ii], 'text');
						if (h['errors']) {
							ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
							console.log(ScanCreateFeedsGlobalVars.errors);
							return;
						} else {
							htmlhtml = h['result'];
						}
					}
					var field_value_selector = fieldHash['field_value_selector'][fieldHash['field_value_selector'].length - 1];
				} else {
					var field_value_selector = fieldHash['field_value_selector'];
				}

				if ((fieldHash['parsing_type'] == 'iso4217price')||(fieldHash['parsing_type'] == 'nativeprice')) {
					var h = parseHtml(htmlhtml, field_value_selector, 'text', fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;

					} else if (h['result']) {
						if (fieldHash['parsing_type'] == 'nativeprice') {
							//nativeprice
				
							var parsedcena = getPriceCurrencyFromStr(h['result']);
		
							if (parsedcena[0]&&parsedcena[0][0]&&parsedcena[1]&&parsedcena[1][0]) {

								if (parsedcena[1][1]) {
									field_value = parsedcena[0][0] + ' ' + parsedcena[1][1];
								} else {
									field_value = parsedcena[0][0] + ' ' + parsedcena[1][0];
								}
							
							}

						} else {

							//iso4217price

							var parsedcena = getPriceCurrencyFromStr(h['result']);

							if (parsedcena[0]&&parsedcena[0][1]&&parsedcena[1]&&parsedcena[1][0]) {

								field_value = parsedcena[0][1] + ' ' + parsedcena[1][0];
						
							}



						}
					}




				} else {
					var h = parseHtml(htmlhtml, field_value_selector, fieldHash['parsing_type'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);
					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else {
						field_value = h['result'];

						field_value = digitkeysproc(field_value,fieldHash['field_digitkeys']);
					}
				}



//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


				if ((fieldHash['parsing_type'] == 'iso4217price')||(fieldHash['parsing_type'] == 'nativeprice')) {
//					if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
					if (field_length) {
//системный параметр
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
						ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}
				} else if ((fieldHash['parsing_type'] == 'text')||(fieldHash['parsing_type'] === '')) {

					field_value = getMaxStr(field_value, field_length);

				} else {
//					if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
					if (field_length) {
//src href и др. атрибуты тега селектора
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
						ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}

				}


//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (field_required === 3) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует выше одно из полей, указанных в field_required
								ScanCreateFeedsGlobalVars.errors += 'bad field_required:'+ ' not exists field_name ' + recfields[ii] + ' in previos fields, field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						}
//						if ((!fieldHash['field_Fun'])&&foundnotemptyfields) {
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}



			} else if (({}.toString.call(field_type) === '[object Array]')&&(field_type.length == 2)) {


				if ((typeof field_type[0] !== 'string')||(!field_type[0])||(typeof field_type[1] !== 'string')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_type, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

//				var fields = field_type[0].split(/[\s\,]+/);
				var fields = field_type[0].split(/[\s\,]*\,[\s\,]*/);


//если отрицательное, то нельзя использовать в "хлебных крошках" и др. вариантах, требующих обязательного последовательного присутствия всех полей
				var NotEmptyFields = 0;
				var FoundEmptyField = 0;

//при конкатенации, если есть ограничение по длине, то ограничиваем по разделителю
				var MaxLengthFound = 0;

				var AddToValue = 1;

				var tmp_field_length = field_length;
				if (fieldHash['parsing_type']) {
//системный параметр (iso4217price или nativeprice)
//не обрезаем поле по field_length
					field_length = 0;
				}


				for (var ii = 0; ii < fields.length; ii++) {
					if (checkedfields[fields[ii]]&&checkedfields[fields[ii]]['field_value']) {
						if (FoundEmptyField) {
//обнаружено пустое или несуществующее поле в последовательности полей и потом опять непустое (как минимум один раз)
							NotEmptyFields = -1;
						} else {
							NotEmptyFields++;
						}
						if ((field_required === 3)&&((ii >= fields.length - 1)||(!(checkedfields[fields[ii+1]]&&checkedfields[fields[ii+1]]['field_value'])))) {
							//должно быть на один меньше элементов в обязательной полследовательной конкатенации
							AddToValue = 0;
						}
						if (AddToValue) {
							if (field_length) {
								if (!MaxLengthFound) {
									if (field_value) {
										var tmp_field_value = field_type[1] + checkedfields[fields[ii]]['field_value'];
									} else {
										var tmp_field_value = checkedfields[fields[ii]]['field_value'];
									}
									if (tmp_field_value.length > field_length) {
										MaxLengthFound = 1;
									} else {
										field_value += tmp_field_value;
									}
								}
							} else {
								if (field_value) {
									field_value += field_type[1] + checkedfields[fields[ii]]['field_value'];
								} else {
									field_value = checkedfields[fields[ii]]['field_value'];
								}
							}
						}
					} else {
						FoundEmptyField = 1;
					}
				}
				field_length = tmp_field_length;



				if (field_value) {

					var h = parseHtmlFUN(field_value, fieldHash['field_value_selector'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					//обязательно обнуляем, иначе останеться текущее значение (дальше будет присвоено значение повторно)
					field_value = '';

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else if (h['result']) {
						if (fieldHash['parsing_type']) {
							if (fieldHash['parsing_type'] === 'nativeprice') {
								//nativeprice
				
								var parsedcena = getPriceCurrencyFromStr(h['result']);
		
								if (parsedcena[0]&&parsedcena[0][0]&&parsedcena[1]&&parsedcena[1][0]) {

									if (parsedcena[1][1]) {
										field_value = parsedcena[0][0] + ' ' + parsedcena[1][1];
									} else {
										field_value = parsedcena[0][0] + ' ' + parsedcena[1][0];
									}
							
								}

							} else if (fieldHash['parsing_type'] === 'iso4217price') {

								//iso4217price

								var parsedcena = getPriceCurrencyFromStr(h['result']);

								if (parsedcena[0]&&parsedcena[0][1]&&parsedcena[1]&&parsedcena[1][0]) {

									field_value = parsedcena[0][1] + ' ' + parsedcena[1][0];
						
								}



							} else {
								ScanCreateFeedsGlobalVars.errors += 'bad parsing_type (need to remove parsing_type), field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						} else {
							field_value = h['result'];
							field_value = digitkeysproc(field_value,fieldHash['field_digitkeys']);
						}
					}

				}


//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}



				if (fieldHash['parsing_type']) {
//					if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
					if (field_length) {
//системный параметр (iso4217price или nativeprice)
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
						ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}
				}



//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					if (NotEmptyFields < 0) {
//обнаружено пустое или несуществующее поле в последовательности полей и потом опять непустое (как минимум один раз)
						return;
					}
				} else if (field_required === 3) {
					if (NotEmptyFields < 0) {
//обнаружено пустое или несуществующее поле в последовательности полей и потом опять непустое (как минимум один раз)
						return;
					}
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							}
						}
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}



			} else if (({}.toString.call(field_type) === '[object Array]')&&(field_type.length == 1)) {
//конкатенация в виде вложенных аттрибутов сделана из обычной конкатенации (код выше)

				include_attributes = 1;

				if ((typeof field_type[0] !== 'string')||(!field_type[0])) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_type, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

//				var fields = field_type[0].split(/[\s\,]+/);
				var fields = field_type[0].split(/[\s\,]*\,[\s\,]*/);


//если отрицательное, то нельзя использовать в "хлебных крошках" и др. вариантах, требующих обязательного последовательного присутствия всех полей
				var NotEmptyFields = 0;
				var FoundEmptyField = 0;


				var AddToValue = 1;


				for (var ii = 0; ii < fields.length; ii++) {
					if (checkedfields[fields[ii]]&&checkedfields[fields[ii]]['field_value']) {
						if (FoundEmptyField) {
//обнаружено пустое или несуществующее поле в последовательности полей и потом опять непустое (как минимум один раз)
							NotEmptyFields = -1;
						} else {
							NotEmptyFields++;
						}
						if ((field_required === 3)&&((ii >= fields.length - 1)||(!(checkedfields[fields[ii+1]]&&checkedfields[fields[ii+1]]['field_value'])))) {
							//должно быть на один меньше элементов в обязательной полследовательной конкатенации
							AddToValue = 0;
						}
						if (AddToValue) {
							if (field_value) {
								field_value += "\n" + fields[ii] + "\n" + checkedfields[fields[ii]]['field_value'];
							} else {
								field_value = fields[ii] + "\n" + checkedfields[fields[ii]]['field_value'];
							}
						}
					} else {
						FoundEmptyField = 1;
					}
				}


				if (field_value) {

					var h = parseHtmlFUN(field_value, fieldHash['field_value_selector'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					//обязательно обнуляем, иначе останеться текущее значение (дальше будет присвоено значение повторно)
					field_value = '';

					if (fieldHash['parsing_type']) {
						//здесь не должно быть точно, во всех других полях может быть парсинг цены
						ScanCreateFeedsGlobalVars.errors += 'bad parsing_type (need to remove parsing_type), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}


					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else {
						field_value = h['result'];
					}

				}


				if (field_length) {
//не должно присутствовать ограничения длины
					ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					if (NotEmptyFields < 0) {
//обнаружено пустое или несуществующее поле в последовательности полей и потом опять непустое (как минимум один раз)
						return;
					}
				} else if (field_required === 3) {
					if (NotEmptyFields < 0) {
//обнаружено пустое или несуществующее поле в последовательности полей и потом опять непустое (как минимум один раз)
						return;
					}
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							}
						}
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}




			} else if (field_type === 'fixed') {


				if (typeof fieldHash['field_value_selector'] !== 'string') {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				field_value = fieldHash['field_value_selector'];

				if (field_value) {

					var h = parseHtmlFUN(field_value, fieldHash['field_value_selector'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					//обязательно обнуляем, иначе останеться текущее значение (дальше будет присвоено значение повторно)
					field_value = '';

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else if (h['result']) {
						if (fieldHash['parsing_type']) {
							if (fieldHash['parsing_type'] === 'nativeprice') {
								//nativeprice
				
								var parsedcena = getPriceCurrencyFromStr(h['result']);
		
								if (parsedcena[0]&&parsedcena[0][0]&&parsedcena[1]&&parsedcena[1][0]) {

									if (parsedcena[1][1]) {
										field_value = parsedcena[0][0] + ' ' + parsedcena[1][1];
									} else {
										field_value = parsedcena[0][0] + ' ' + parsedcena[1][0];
									}
							
								}

							} else if (fieldHash['parsing_type'] === 'iso4217price') {

								//iso4217price

								var parsedcena = getPriceCurrencyFromStr(h['result']);

								if (parsedcena[0]&&parsedcena[0][1]&&parsedcena[1]&&parsedcena[1][0]) {

									field_value = parsedcena[0][1] + ' ' + parsedcena[1][0];
						
								}



							} else {
								ScanCreateFeedsGlobalVars.errors += 'bad parsing_type (need to remove parsing_type), field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						} else {
							field_value = h['result'];
							field_value = digitkeysproc(field_value,fieldHash['field_digitkeys']);
						}
					}


				}


//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


				if (fieldHash['parsing_type']) {
//					if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
					if (field_length) {
//системный параметр (iso4217price или nativeprice)
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
						ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}
				} else {
					field_value = getMaxStr(field_value, field_length);
				}

//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (field_required === 3) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует выше одно из полей, указанных в field_required
								ScanCreateFeedsGlobalVars.errors += 'bad field_required:'+ ' not exists field_name ' + recfields[ii] + ' in previos fields, field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						}
//						if ((!fieldHash['field_Fun'])&&foundnotemptyfields) {
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


			} else if (field_type === 'id') {

				field_value = URLID;

				if (field_value) {

					var h = parseHtmlFUN(field_value, fieldHash['field_value_selector'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					//обязательно обнуляем, иначе останеться текущее значение (дальше будет присвоено значение повторно)
					field_value = '';

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else {
						if (fieldHash['parsing_type']) {
							ScanCreateFeedsGlobalVars.errors += 'bad parsing_type (need to remove parsing_type), field: ' + field_name + "\n";
							console.log(ScanCreateFeedsGlobalVars.errors);
							return;
						} else {
							field_value = h['result'];
						}
					}

				}


//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


//				if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
				if (field_length) {
//системный параметр
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
					ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (field_required === 3) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует выше одно из полей, указанных в field_required
								ScanCreateFeedsGlobalVars.errors += 'bad field_required:'+ ' not exists field_name ' + recfields[ii] + ' in previos fields, field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						}
//						if ((!fieldHash['field_Fun'])&&foundnotemptyfields) {
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


			} else if (field_type === 'url') {

				field_value = url;

				if (field_value) {

					var h = parseHtmlFUN(field_value, fieldHash['field_value_selector'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					//обязательно обнуляем, иначе останеться текущее значение (дальше будет присвоено значение повторно)
					field_value = '';

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else {
						if (fieldHash['parsing_type']) {
							ScanCreateFeedsGlobalVars.errors += 'bad parsing_type (need to remove parsing_type), field: ' + field_name + "\n";
							console.log(ScanCreateFeedsGlobalVars.errors);
							return;
						} else {
							field_value = h['result'];
						}
					}


				}


				if (field_value) {

//ниже обработка url подразумевает, что из ссылок удалены #...

					if (!OneFeedConfig['noDropShip']) {
						var parsed_url = parse_url(field_value);
						if (parsed_url["search"]) {
							field_value += '&';
						} else {
							field_value += '?';
						}
						field_value += 'sodship=y';

					}
					if (OneFeedConfig['utmDropShip']) {
						var parsed_url = parse_url(field_value);
						if (parsed_url["search"]) {
							field_value += '&';
						} else {
							field_value += '?';
						}
						field_value += OneFeedConfig['utmDropShip'] + OneFeedConfig['ext'];
					}
				}


//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


//				if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
				if (field_length) {
//системный параметр
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
					ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (field_required === 3) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует выше одно из полей, указанных в field_required
								ScanCreateFeedsGlobalVars.errors += 'bad field_required:'+ ' not exists field_name ' + recfields[ii] + ' in previos fields, field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						}
//						if ((!fieldHash['field_Fun'])&&foundnotemptyfields) {
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


			} else if ((typeof field_type == 'string')&&(/^id.+$/.test(field_type))) {

				var f_name = field_type.replace(/^id(.+)$/, "$1");

				if (f_name&&checkedfields.hasOwnProperty(field_name)) {
					field_value = murmurhash3_32_gc(checkedfields[field_name], OneSiteConfig.seed);
				} else {

					ScanCreateFeedsGlobalVars.errors += 'bad field_type, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_value) {

					var h = parseHtmlFUN(field_value, fieldHash['field_value_selector'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					//обязательно обнуляем, иначе останеться текущее значение (дальше будет присвоено значение повторно)
					field_value = '';

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else {

						if (fieldHash['parsing_type']) {
							ScanCreateFeedsGlobalVars.errors += 'bad parsing_type (need to remove parsing_type), field: ' + field_name + "\n";
							console.log(ScanCreateFeedsGlobalVars.errors);
							return;
						} else {
							field_value = h['result'];
						}

					}

				}


//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

//				if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
				if (field_length) {
//системный параметр
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
					ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (field_required === 3) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует выше одно из полей, указанных в field_required
								ScanCreateFeedsGlobalVars.errors += 'bad field_required:'+ ' not exists field_name ' + recfields[ii] + ' in previos fields, field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						}
//						if ((!fieldHash['field_Fun'])&&foundnotemptyfields) {
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


			} else if (field_type === 'parentFeed') {


				if (!feedsfields) {
					//вся запись в родительском фиде отсутсвует, выходим
					//либо нет родительского элемента, но это проверяется при старте
					return;
				}



				//дальше есть поля в записи, проверяем нужное поле

				if (!feedsfields.hasOwnProperty(fieldHash['field_value_selector'])) {
					//нет такого поля в родительском фиде
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector for field_name: ' + field_name + ", no found field in parent feed\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


				if (feedsfields[fieldHash['field_value_selector']]['include_attributes']) {
					//нельзя использовать поля вложенных атрибутов в родительских фидах
					ScanCreateFeedsGlobalVars.errors += 'bad field_name in field_value_selector, field: ' + field_name + ' in this feed, field_name ' + fieldHash['field_value_selector'] + ' in parent feed  have "include attributes" type, not use it in this feed.' + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return '';
				}

				field_value = feedsfields[fieldHash['field_value_selector']]['field_value'];

				if (field_value) {

					var h = parseHtmlFUN(field_value, fieldHash['field_value_selector'], fieldHash['field_replace'], fieldHash['field_Fun'], url, checkedfields, field_name);

					//обязательно обнуляем, иначе останеться текущее значение (дальше будет присвоено значение повторно)
					field_value = '';

					if (h['errors']) {
						ScanCreateFeedsGlobalVars.errors += 'errors ' + h['errors'] + ', field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					} else if (h['result']) {
						if (fieldHash['parsing_type']) {
							if (fieldHash['parsing_type'] === 'nativeprice') {
								//nativeprice
				
								var parsedcena = getPriceCurrencyFromStr(h['result']);
		
								if (parsedcena[0]&&parsedcena[0][0]&&parsedcena[1]&&parsedcena[1][0]) {

									if (parsedcena[1][1]) {
										field_value = parsedcena[0][0] + ' ' + parsedcena[1][1];
									} else {
										field_value = parsedcena[0][0] + ' ' + parsedcena[1][0];
									}
							
								}

							} else if (fieldHash['parsing_type'] === 'iso4217price') {

								//iso4217price

								var parsedcena = getPriceCurrencyFromStr(h['result']);

								if (parsedcena[0]&&parsedcena[0][1]&&parsedcena[1]&&parsedcena[1][0]) {

									field_value = parsedcena[0][1] + ' ' + parsedcena[1][0];
						
								}



							} else {
								ScanCreateFeedsGlobalVars.errors += 'bad parsing_type (need to remove parsing_type), field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						} else {
							field_value = h['result'];
							field_value = digitkeysproc(field_value,fieldHash['field_digitkeys']);
						}
					}


				}


//не должно быть табуляций и перевода строки в любых полях, кроме конкатенации аттрибутов (это делается программно, но есть ручные типы fixed и обычная конкатенация, надо проверять всегда обязательно для ручного ввода, для остальных - на всякий случай)
				if (/[\t\n\r]/.test(field_value)) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_value_selector value (must delete \t\n\r symbols), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

/*
получается теже требования к длине что и ниже, поэтому дубликат закомментирован

				if (fieldHash['parsing_type']) {
//					if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
					if (field_length) {
//системный параметр (iso4217price или nativeprice)
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
						ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
						console.log(ScanCreateFeedsGlobalVars.errors);
						return;
					}
				}
*/


//				if (field_length&&field_value&&(field_length < field_value.length)) {
//сразу убираем, иначе может вылезти потом в процессе выполнения, и заблокирует фид, так как никто такие ошибки постоянно не отслеживает
				if (field_length) {
//родительские поля любые, поэтому для этого типа полей не выполняем "умную" обрезку длины поля
//в принципе не должно устанавливаться ограничение на длину, но если оно есть и вышли за него, то выдаем ошибку
					ScanCreateFeedsGlobalVars.errors += 'bad field_length (need to remove field_length), field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


//в конце анализ field_required
				if ((typeof field_required !== 'string')&&(typeof field_required !== 'number')) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}

				if (field_required === -1) {
					if (field_value)
						return;
				} else if (field_required === 0) {
//записываем необязательное поле, в т.ч. пустое
				} else if (field_required === 4) {
					if ((!field_value)&&feedfiles_flg)
						feedfiles_exist = 0;
				} else if (field_required === 5) {
					if ((!field_value)&&search_flg)
						search_exist = 0;
				} else if (field_required === 1) {
					if (!field_value)
						return;
				} else if (field_required === 2) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (field_required === 3) {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				} else if (typeof field_required == 'string') {
					if (!fieldHash['field_Fun']) {
//если есть field_Fun, то она определяет значение необязательного зависимого поля
//						var recfields = field_required.split(/[\s\,]+/);
						var recfields = field_required.split(/[\s\,]*\,[\s\,]*/);
						var foundnotemptyfields = 0;
						for (var ii = 0; ii < recfields.length; ii++) {
							if (checkedfields[recfields[ii]]&&checkedfields[recfields[ii]]['field_value']) {
								foundnotemptyfields++;
							} else if (!checkedfields.hasOwnProperty(recfields[ii])) {
//отсутствует выше одно из полей, указанных в field_required
								ScanCreateFeedsGlobalVars.errors += 'bad field_required:'+ ' not exists field_name ' + recfields[ii] + ' in previos fields, field: ' + field_name + "\n";
								console.log(ScanCreateFeedsGlobalVars.errors);
								return;
							}
						}
//						if ((!fieldHash['field_Fun'])&&foundnotemptyfields) {
						if (foundnotemptyfields) {
							field_value = '';
						}
					}
				} else {
					ScanCreateFeedsGlobalVars.errors += 'bad field_required, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}


			} else {
				ScanCreateFeedsGlobalVars.errors += 'bad field_type, field: ' + field_name + "\n";
				console.log(ScanCreateFeedsGlobalVars.errors);
				return;
			}


			if (fieldHash['keyword']) {
//				if (typeof fieldHash['keyword'] !== 'string') {
//					ScanCreateFeedsGlobalVars.errors += 'bad keyword, field: ' + field_name + "\n";
//					console.log(ScanCreateFeedsGlobalVars.errors);
//					return;
//				}
				field_value = KeyWordString(field_value, fieldHash['keyword']);
				if (field_value === null) {
					ScanCreateFeedsGlobalVars.errors += 'bad keyword, field: ' + field_name + "\n";
					console.log(ScanCreateFeedsGlobalVars.errors);
					return;
				}
			}


			//если оба флага "упали", то запись не заносится
			//могут "упасть" не все флаги
			if ((!search_exist)&&(!feedfiles_exist))
				return;


//			if (checkedfields[field_name]&&checkedfields[field_name]['field_value']) {
//				//если поле имеет непустое значение, то пропускаем (дубликат поля - не ошибка)
//				continue;
//			}


			checkedfields[field_name] = {field_value: field_value, field_id: fieldId, include_attributes: include_attributes, field_config: fieldHash}


		}
	}


	return { checkedfields: checkedfields, search_exist: search_exist, feedfiles_exist: feedfiles_exist};


}


function digitkeysproc(field_value,field_digitkeys) {

	if ((!field_value)||(!field_digitkeys))
		return field_value;


function replacer2(match, p1, offset, string) {

	//так как теперь еще заводим длинные слова с небольшим кол-вом цифр, то сначала проверяем кол-во цифр
	if (!(/\d.*\d.*\d/.test(p1))) {
		return match;
	}


//заменяем пробелы только внутри строки, не по краям (что бы можно было потом возвратить с пробелами return key)
	var key = p1.replace(/([^\s])\s+(?=[^\s])/g, '$1');


	if (field_digitkeys != 2) {
//если только цифры и 6 цифр или больше, то удаляем вообще из текста (гугл может посчитать это номером телефона в объявлении)
		if (/^\s*\d\d\d\d\d\d+\s*$/.test(key)) {
			return '';
		}
	}

	if ((field_digitkeys != 1)&&(p1 != key)) {
		var key2 = key.trim();
//не стоит убирать пробелы во всех цифро-буквенных словах запроса, можно получить потом "слитые" слова

		if (key2) {
//в общем объединяем (в запросе), если у нас только цифры, иначе не объединяем (в запросе)
			if (/^\d+$/.test(key2))
				return key;
//так же объединяем (в запросе), если есть только по одному символу между пробелами
			if (/(^|\s)[^\s](\s|$)/.test(p1))
				return key;
		}
		

	}
	return match;
}

//удаляем пробелы в ЦС, которые без скобок (повторяет код из function ItemKeys(name,requiredkeys) с дополнительным контролем только цифр (больше 5) и удалением в этом случае)
//нужно для заголовков типа "Тормозной диск BOSCH 0 986 479 771" что бы ЦС не разбилось между заголовками как попало (для того что бы красиво было с динамической вставкой КС)
//если быть более точным, то в примере будет полностью удалено ЦС из текста (иначе гугл может посчитать его номером телефона)
//то есть не удаляются ЦС в которых не только цифры или только цифры, но меньше 6
	//field_value = field_value.replace(/((?:^|\s)(?:(?:(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)*)|(?:[a-zA-Z0-9\.\-\_\/]{7,}))(?=\s|$))/g, replacer2);
//теперь еще глюк гугла убран (можно сравнить с верхним) <- пример глюка ищем в ItemKeys по этой фразе
	field_value = field_value.replace(/((?:^|\s)(?:(?:(?=[^\s\(\)\d]*\d+)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)(?:[^\s\(\)\d]*\d+[^\s\(\)\d]*\s*)*)|(?:[a-zA-Z0-9\.\-\_\/]{7,}))(?=\s|$))/g, replacer2);

	//удаляем пустые скобки если остались после удаления ЦС
	field_value = field_value.replace(/\s*\(\s*\)/g, '');


	field_value = field_value.trim();


	return field_value;


}



function getMaxStr(string, maxLen) {
//получить строку не более maxLen
//0 - неограничено, иначе обрезается по точке, запятой или пробелу


	if (typeof string != 'string')
		return '';

//для undefined null '' или 0
	if (!maxLen)
		return string;

	if (!/^\d+$/.test(maxLen))
		return string;

	maxLen = maxLen * 1; //переводим в число всегда

//для '0' на входе
	if (!maxLen)
		return string;


	if (maxLen >= string.length)
		return string;

//сначала ищем по точке
//мах длина строки для точки
	var maxLenDot = maxLen - 1;
	if (maxLenDot <= 0)
		maxLenDot = 1;

	var newstr = string.match('^.{1,' + maxLenDot + '}\\.(?=\\s|$)')
	if (newstr !== null) {
		return newstr[0];
	}


//ищем по запятой (не менее половины строки)
//мин длина строки для запятой
	var minLenComma = parseInt(maxLen/2);
	if (minLenComma <= 0)
		minLenComma = 1;


	var newstr = string.match('^.{' + minLenComma + ',' + maxLen + '}(?=\\s*\\,(?:\\s|$))')
	if (newstr !== null) {
		return newstr[0];
	}


//ищем по пробелу
	var newstr = string.match('^.{1,' + maxLen + '}(?=\\s|$)')
	if (newstr !== null) {
		return newstr[0];
	}

	return '';

}



function KeyWordString(string, keyword) {

//новая версия ( учитывает кавычки '"() и изменяет только буквенные слова в режимах Keyword и KeyWord)


	function replacerKeyWord(match, p1, p2, p3, p4, offset, string) {
		//p2 не undefined и p3 undefined  - признак что увеличивать одиночный символ, то есть это начало строки или один из .!|
		//p3 не undefined и p2 undefined - признак что не увеличивать одиночный символ
		//p4 - само слово
		if (typeof p2 == 'string') {
			//начало предложения
			//увеличиваем одиночный символ, как и любой другой
			var firstch = p4.toLowerCase().replace(/^([\"\'\(]*\s*[^\"\'\(\s]).*$/, '$1').toUpperCase();
			var fullchars = firstch + p4.toLowerCase().replace(/^[\"\'\(]*\s*[^\"\'\(\s](.*)$/, '$1');
		} else {
			//не увеличиваем одиночный символ без кавычек, а если слово или слово в кавычках или одиночный символ в кавычках то увеличиваем первый символ
			if (p4.length == 1) {
				//это одиночный символ без кавычек
				var fullchars = p4.toLowerCase();
			} else {
				//это слово или слово в кавычках или одиночный символ в кавычках, увеличиваем первый символ (с учетом символов кавычек)
				var firstch = p4.toLowerCase().replace(/^([\"\'\(]*\s*[^\"\'\(\s]).*$/, '$1').toUpperCase();
				var fullchars = firstch + p4.toLowerCase().replace(/^[\"\'\(]*\s*[^\"\'\(\s](.*)$/, '$1');
			}
		}
		return p1 + fullchars;
	}


	function replacerKeyword(match, p1, p2, p3, p4, offset, string) {
		if (typeof p2 == 'string') {
			var firstch = p4.toLowerCase().replace(/^([\"\'\(]*\s*[^\"\'\(\s]).*$/, '$1').toUpperCase();
			var fullchars = firstch + p4.toLowerCase().replace(/^[\"\'\(]*\s*[^\"\'\(\s](.*)$/, '$1');
		} else {
			var fullchars = p4.toLowerCase();
		}
		return p1 + fullchars;

	}


	if (keyword) {
		if (keyword == 'Keyword') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.replace(/(((?:^\s*)|(?:\.\s*)|(?:\!\s*)|(?:\|\s*))|([\,\s]+))([\"\'\(]*\s*[\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]+)(?=(?:(?:[\"\'\)\s\.\!\|\,])|(?:$)))/g, replacerKeyword);
		} else if (keyword == 'KeyWord') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.replace(/(((?:^\s*)|(?:\.\s*)|(?:\!\s*)|(?:\|\s*))|([\,\s]+))([\"\'\(]*\s*[\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]+)(?=(?:(?:[\"\'\)\s\.\!\|\,])|(?:$)))/g, replacerKeyWord);
		} else if (keyword == 'keyword') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.toLowerCase();
		} else if (keyword == 'KEYWORD') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.toUpperCase();
		} else {
			return null;
		}
		
	}

	return string;

}


function parseHtml(html, field_value_selector, type, field_replace, field_Fun, url, fields, fieldName, clearhtml) {
//выполняет парсинг html для field_value_selector 

//field_value_selector - нескольких селекторов (через запятую), функция берет первый попавшийся, где обнаружено совпадение в коде html
//наиболее приближенный вариант к браузеру - пробелы-разделители с/без nth-child(1) или > :nth-child(1) или > tag:nth-child(1)
//функция крайне редко может неверно найти для пробелов-разделителей
//всегда найдет для > но это не браузерный вариант
//вариант > - это не любые дочерние элементы, а первый дочерний элемент то есть :nth-child(1) (в отличие от браузера)
//наиболее быстро работает: > для всех селекторов и/или nth-child(1) (не проверяет переход на следующий "чужой" тег)
//:nth-child(2) и больший номер ищет среди любых потомков любой вложенности, а не только прямых потомков как браузер
//поэтому :nth-child(>1) более часто может неверно найти заданное браузером
//как работает
// пробел nth-child - ищет начиная с любого элемента среди любой вложенности (если >1 то проверяет переход на следующий "чужой" тег)
// > nth-child - ищет начиная с первого элемента среди любой вложенности (если >1 то проверяет переход на следующий "чужой" тег)
// пробел tag - ищет начиная с любого элемента среди любой вложенности (наиболее браузерный подход, хотя теоретически может перескочить на "чужой" тег и получить потом отлуп за чужой тег, хотя возможно надо было сразу переходить в этот следующий "чужой" тег а регулярки поступили по другому)
// > tag[attr=value] - ищет совпадение только в первом элементе (хотя не браузерный подход, но срабатывает во многих случаях, так как зачастую совпадает первый следующий элемент с искомым)
// tag:nth-child(n) - ищет n-ный tag любой вложенности, специально в корне отличается от браузера  (если >1 то проверяет переход на следующий "чужой" тег)
//			браузер ищет n-ный элемент среди прямых потомков, и проверяет совпадает ли он с tag
//			мы не можем искать прямых протомков, поэтому считать всех крайне неудобно, если далеко
//			поэтому вернул старый вариант - ищем по заданным tagам, а не по любым элементам
// > nth-child(1) - ищет совпадение только в первом элементе (браузерный подход на 100%)

    //применяются селекторы потомков (только > и пробел)
    //поддерживаются селекторы элемента: тег, id, класс, nth-child, атрибуты с полной поддержкой (= *= ^= $= |= ~=)
    //селекторы и классы можно переставлять.
    //параметры nth-child - только числа, его можно использовать с тегами, id, классами, атрибутами
    //nth-child только одноуровневый, то есть нельзя подряд указать несколько :nth-child
    //другие элементы не поддерживаются.

    //типовое использование: поддержка селекторов, генерируемых мозиллой "CSS-селектор" "Путь к CSS"
    //и оперой "Copy Selector" (кроме прямых потомков, которые заменяются на потомков)

    //могут иногда не сработать на правильном селекторе (так как используются регулярки, не строится DOM-модель)

    //отдает html (зависит от type), обнаруженный длинный селектор, короткий последний селектор и регулярное выражение поиска
    //в зависимости от type выдает разный html
    //type = undefined отдает обрезанный html (включая последний селектор и до конца страницы)
    //	зачем - может быть ситуация, когда другие варианты из-за сложной верстки 
    //	обрезают текст и пр., тогда с помощью field_Fun мы можем почистить этот код
    //type = 'text' отдает текст селектора
    //type = 'src' отдает src селектора
    //type = 'href' отдает href селектора
    //type = 'любой атрибут' отдает 'любой атрибут' селектора
    //url, fields только передаются в field_Fun, добавлены для совместимости field_Fun с другими функциями
    //fieldName - информационная переменная для ошибок (в каком поле произошла ошибка)
    //field_replace - поиск и замена регулярного выражения или строки, чистит html-код после выполнения type
    //			три параметра в массиве: что ищем и замены, на что меняем, флаги поиска (флаги необязательны)
    //			['^\\<([^\\s\\<\\>\\/]+)([\\s\\<\\>\\/]).*$', function(match, p1, p2) {},'gim']
    //			['^\\<([^\\s\\<\\>\\/]+)[\\s\\<\\>\\/].*$','$1','gim']
    //field_Fun - чистит html-код после выполнения type и field_replace
    //			function(html, selector, url, fields) { return html; }


    //комментарий по верстке
    //у атрибутов должны быть кавычки '" или вообще отсутствовать
    //если кавычки есть то допускается в значении атрибута использовать вторые кавычки, например, class='aa"a.html'
    //	функция действует аналогично
    //если закрывающая кавычка отсутствует, но она есть где-то дальше в теге, то браузеры будут объединять невалидный код, например href='aaa.html id=23' объединит в общую ссылку aaa.html%20id=23
    //	функция действует аналогично
    //если закрывающая кавычка отсутствует, и она где-то дальше ВНЕ тега, то браузеры объединят весь невалидный код до закрывающей кавычки в общее значение атрибута
    //	функция не найдет этот атрибут (если в пределах тега нет закрывающей кавычки)
    //если нет кавычек, то не может быть пробелов в значении аттрибута, иначе следующие за пробелом значения будут выделены в отдельные атрибуты
    //	функция действует аналогично
    //если нет кавычки в качестве первого символа в значении, то возможны обе кавычки в значениях аттрибутов
    //	функция действует аналогично



//можно привязываться к одиночным тегам, например, br hr meta
//для одиночных тегов "потомки" - это все одиночные теги до первого неодиночного
//аналогично определяется text для последнего одиночного - до первого неодиночного тега
//то есть так можно, типа div > br > br, хотя эта возможность (вставлять их в середине составного селектора) бесполезна


//nth-child в браузере ведет себя по разному при верстке и при вычислении CSS-селектора
//например, в верстке чилдами не считаются одиночные теги:
//area base basefont br col frame hr img input isindex link meta param https://www.w3.org/TR/1999/REC-html401-19991224/index/elements.html
//http://monetavinternete.ru/sozdaem-sajt-s-nulya/osnovy-html/chto-takoe-html-tegi-i-atributy-validator-validator-w3c-struktura-i-pravila-napisaniya-tegov/
//а при вычислении селектора, мозилла спокойно отдает br как чилда: body > article:nth-child(6) > div:nth-child(2) > br:nth-child(1)
//соотвественно, это у нас работает так как при вычислении селектора
//но главное другое - такой код наверняка неверно сработает у нас, так как с '>' вычисляются только прямые потомки-теги, а не все потомки, как у нас
//поэтому nth-child из мозиллы стоит оставлять только в последнем элементе: body > article > div > span:nth-child(2)
//и поэтому всегда желательна привязка по классам id или другим атрибутам



//конечный результат выдает в selector['result'], но также можно увидеть в массиве промежуточные результаты
//если выше selector['result'] == false то ошибка в настройках или в программе
//сама ошибка в selector['errors']
//все остальное не ошибка, а selector['result'] == '' - результат не найден



    function parseOneLastSelector(field_value_selector) {
        //создаем регулярное выражение для нахождения последнего тега в строке и обрезания всего до него

        //сначала нужно определить самый последний селектор (что бы потом обрезать до него, включая его)
        //перебираем в этом split так же как в следующем split
        //определяем число селекторов tag, id, class
        var numlastag = 0;
        //определяем число разрешенных элементов строки селектора space, >, tag, id, class
        var numarray = 0;
        field_value_selector.split(/(\s*\>\s*)|(\s+)/).forEach(function(token) {

            if ((token == '') || (typeof token == 'undefined')) return;
            token = token.trim()
            if (token == '') {
                //space
                numarray++
            } else if (token == '>') {
                //>
                numarray++
            } else {
                //selector
                var s = parse(token);
                if ((s.tags && s.tags[0]) || (s.child && s.child[0]) || (s.ids && s.ids[0]) || (s.classes && s.classes[0]) || (s.attrs && s.attrs[0] && s.attrs[0][0])) {
                    numlastag++;
                    numarray++
                }
            }
        });


        //сразу выходим, если нет разрешенных тегов, это ошибка настройки
        if (!numlastag) return false;

        var string = '^.*?';

        var checknumlastag = 0;
        var checknumarray = 0;


        var lastselector = '';

        var selectorsnum = 0;

	var notcheckchild = 0;
	var directchilds = [];

        field_value_selector.split(/(\s*\>\s*)|(\s+)/).forEach(function(token) {

            //что бы не перебирать дальше пустые пробелы или > в конце строки, на всякий случай, если есть пробел
            if (checknumlastag > numlastag) return;

            //это не найденные элементы
            if ((token == '') || (typeof token == 'undefined')) return;
            token = token.trim()
            if (token == '') {
                //space
                //alert(token + '=space')
                checknumarray++

                if (checknumarray > 1) {
                    //не вставляем первый элемент (дублирует начало строки string)
                    string += '.*?';
                }

            } else if (token == '>') {
                //>
                //alert(token + '=>')
                checknumarray++

                if (checknumarray > 1) {
                    //не вставляем первый элемент (дублирует начало строки string)
//		    string += '[^\\<]*';
//что бы пропускало одинокие < (которые не относятся к тегам)
//		    string += '(?:(?!\\<[a-zA-Z]).)*';
		    //string += '(?:(?!\\<[a-zA-Z][^\\>\\<]*\\>).)*';
//это набор символов, среди которых нет какого-либо открывающего тега
		    string += '(?:(?!\\<[a-zA-Z][a-zA-Z0-9]*(?:[\\s\\/][^\\>]*)?\\>).)*';

			//для прямых потомков не надо поверять вхождение потомка в innerHTML родителя (он сразу идет за тегом родителя)
			notcheckchild = 1;


                }

            } else {
                //selector
                //alert(token + '=selector')
                var s = parse(token);

                if ((s.tags && s.tags[0]) || (s.child && s.child[0]) || (s.ids && s.ids[0]) || (s.classes && s.classes[0]) || (s.attrs && s.attrs[0] && s.attrs[0][0])) {
                    checknumlastag++;
                    checknumarray++
                } else {
			//неизвестный селектор, продолжаем
			return;
		}

                if (checknumlastag == numlastag) {
                    var lastag = 1;
                    lastselector = token;
                } else {
                    var lastag = 0;
                }


		//выделяем каждый проверяемый мини-селектор кавычками и считаем их
		selectorsnum++;
		if (lastag) {
                	var tagstring = '';
		} else {
	                var tagstring = '(';
		}


		if (notcheckchild) {
//заносим в список прямых потомков, которых не нужно проверять
//не нужно проверять, есть есть знак > и если нет nth-child или nth-child = 1
			if (!(s.child && (s.child[0] > 1)))
				directchilds[selectorsnum] = 1;
		}
		notcheckchild = 0;


                var child = 0;

                    //вне конкурса tag
                if (s.tags && s.tags[0]) {
                    //есть tag
                    if (s.child && s.child[0]) {
                        //у него есть nth-child
                        s.child[0] -= 0;
//последнему чилду в последнем селекторе надо открыть скобку, иначе все чилды попадут в выделение
	                var maxnumchild = s.child[0] - 1;
                        for (var k = 0; k < s.child[0]; k++) {
			    child = 1;
/*

//например div:nth-child(4) это не 4-й div-потомок, а просто 4-й потомок у которого должен быть div
//то есть считаются любые потомки, а не только div, поэтому тег здесь мы указываем только у последнего
//правда считаются прямые потомки, а не потомки потомков, но это мы не можем сделать
                            if (!k) {
				if ((lastag)&&(maxnumchild == k)) {
        	                        tagstring += '(\\<' + s.tags[0];
				} else if (maxnumchild == k) {
                        	        tagstring += '\\<' + s.tags[0];
				} else {
	                                tagstring += '\\<[a-zA-Z][a-zA-Z0-9]*';
				}
       	                    } else {
				if ((lastag)&&(maxnumchild == k)) {
                	                tagstring += '.*?(\\<' + s.tags[0];
				} else if (maxnumchild == k) {
	                                tagstring += '.*?\\<' + s.tags[0];
				} else {
	                                tagstring += '.*?\\<[a-zA-Z][a-zA-Z0-9]*';
				}
                            }

*/
//крайне неудобно считать потом всех вложенных потомков, если их много перед тегом
//поэтому вернул старый вариант - считаем потомков только с указанным тегом

                            if (!k) {
				if ((lastag)&&(maxnumchild == k)) {
	                                tagstring += '(\\<' + s.tags[0];
				} else {
	                                tagstring += '\\<' + s.tags[0];
				}
                            } else {
				if ((lastag)&&(maxnumchild == k)) {
	                                tagstring += '.*?(\\<' + s.tags[0];
				} else {
	                                tagstring += '.*?\\<' + s.tags[0];
				}
                            }


			    tagstring += '(?=[\\s\\>\\/])';
			    tagstring = addattr(s,tagstring);

                        }
                    } else {
                        tagstring += '\\<' + s.tags[0];
	                tagstring += '(?=[\\s\\>\\/])';
			tagstring = addattr(s,tagstring);
                    }



                } else if (s.child && s.child[0]) {

			//тег любой, есть чилды
                        //у него есть nth-child
                        s.child[0] -= 0;
//последнему чилду в последнем селекторе надо открыть скобку, иначе все чилды попадут в выделение
	                var maxnumchild = s.child[0] - 1;
                        for (var k = 0; k < s.child[0]; k++) {
			    child = 1;
                            if (!k) {
				if ((lastag)&&(maxnumchild == k)) {

	                                tagstring += '(\\<[a-zA-Z][a-zA-Z0-9]*';
				} else {
	                                tagstring += '\\<[a-zA-Z][a-zA-Z0-9]*';
				}
                            } else {
				if ((lastag)&&(maxnumchild == k)) {
	                                tagstring += '.*?(\\<[a-zA-Z][a-zA-Z0-9]*';
				} else {
	                                tagstring += '.*?\\<[a-zA-Z][a-zA-Z0-9]*';
				}
                            }
	                    tagstring += '(?=[\\s\\>\\/])';
			    tagstring = addattr(s,tagstring);

                        }


		} else {
			//тег любой
			tagstring += '\\<[a-zA-Z][a-zA-Z0-9]*';
                        tagstring += '(?=[\\s\\>\\/])';
			tagstring = addattr(s,tagstring);
		}



		//закрываем тег
		tagstring += '(?:[\\s\\/][^\\>]*)?\\>';


                if (lastag) {
		    if (child) {
//чидлу уже открыли скобку выше
//	                    string += tagstring + '.*)$';
//кроме основного, дополнительно закрываем кавычки по числу обнаруженных мини-селекторов - 1
	                    string += tagstring + '.*)' + Array(selectorsnum).join(')') + '$';
		    } else {
//	                    string += '(' + tagstring + '.*)$';
//кроме основного, дополнительно закрываем кавычки по числу обнаруженных мини-селекторов - 1
	                    string += '(' + tagstring + '.*)' + Array(selectorsnum).join(')') + '$';
		    }
                } else {
                    string += tagstring;
                }



            }


        });
//итого вложенных пар кавычек в regexp = число мини-селекторов (последний - это селектор, который мы ищем)
        return {
            'regexp': string,
            'selector': lastselector,
            'selectorsnum': selectorsnum,
	    'directchilds': directchilds,
            'fullselector': field_value_selector
        };
    }


    function addattr(s,tagstring) {
//добавляем атрибуты, id, классы
//выделено отдельно для корректного использования :nth-child

                //затем добавляем все остальные варианты

                if (s.ids && s.ids[0]) {
			//id

			//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)

			//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
			//tagstring += '(?=(?:[^\\<\\>]*)? id\\s*=\\s*(?:(?:' + s.ids[0] +  ')|(?:[\\\'\\"]' + s.ids[0] +  '[\\\'\\"]))[\\s\\>\\/])';

			//исправлено (отдельно отслеживаются кавычки '"")
			//tagstring += '(?=(?:[^\\<\\>]*)? id\\s*=\\s*(?:(?:' + s.ids[0] +  ')|(?:\\\'' + s.ids[0] +  '\\\')|(?:\\"' + s.ids[0] +  '\\"))[\\s\\>\\/])';

			//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
			tagstring += '(?=(?:[^\\<\\>]*)? id\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.ids[0] +  ')|(?:\\\'' + s.ids[0] +  '\\\')|(?:\\"' + s.ids[0] +  '\\"))[\\s\\>\\/])';

			//проверены ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
			//хотя к id это врядли относиться


                }

		if (s.classes && s.classes[0]) {
                    //классы
                    for (var j in s.classes) {

			//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)


			//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
			//tagstring += '(?=(?:[^\\<\\>]*)? class\\s*=\\s*(?:(?:' + s.classes[j] +  ')|(?:[\\\'\\"]\\s*(?:(?:[^\\\'\\"\\>\\<\\/\\=]* ' + s.classes[j] + ' [^\\\'\\"\\>\\<\\/\\=]*)|(?:' + s.classes[j] + ' [^\\\'\\"\\>\\<\\/\\=]*)|(?:[^\\\'\\"\\>\\<\\/\\=]* ' + s.classes[j] + ')|(?:' + s.classes[j] + '))\\s*[\\\'\\"]))[\\s\\>\\/])';

			//исправлено (отдельно отслеживаются кавычки '"")
			//tagstring += '(?=(?:[^\\<\\>]*)? class\\s*=\\s*(?:(?:' + s.classes[j] +  ')|(?:\\\'\\s*(?:(?:[^\\\'\\>\\<\\/\\=]* ' + s.classes[j] + ' [^\\\'\\>\\<\\/\\=]*)|(?:' + s.classes[j] + ' [^\\\'\\>\\<\\/\\=]*)|(?:[^\\\'\\>\\<\\/\\=]* ' + s.classes[j] + ')|(?:' + s.classes[j] + '))\\s*\\\')|(?:\\"\\s*(?:(?:[^\\"\\>\\<\\/\\=]* ' + s.classes[j] + ' [^\\"\\>\\<\\/\\=]*)|(?:' + s.classes[j] + ' [^\\"\\>\\<\\/\\=]*)|(?:[^\\"\\>\\<\\/\\=]* ' + s.classes[j] + ')|(?:' + s.classes[j] + '))\\s*\\"))[\\s\\>\\/])';

			//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
			//tagstring += '(?=(?:[^\\<\\>]*)? class\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.classes[j] +  ')|(?:\\\'\\s*(?:(?:[^\\\'\\>\\<\\/\\=]* ' + s.classes[j] + ' [^\\\'\\>\\<\\/\\=]*)|(?:' + s.classes[j] + ' [^\\\'\\>\\<\\/\\=]*)|(?:[^\\\'\\>\\<\\/\\=]* ' + s.classes[j] + ')|(?:' + s.classes[j] + '))\\s*\\\')|(?:\\"\\s*(?:(?:[^\\"\\>\\<\\/\\=]* ' + s.classes[j] + ' [^\\"\\>\\<\\/\\=]*)|(?:' + s.classes[j] + ' [^\\"\\>\\<\\/\\=]*)|(?:[^\\"\\>\\<\\/\\=]* ' + s.classes[j] + ')|(?:' + s.classes[j] + '))\\s*\\"))[\\s\\>\\/])';

			//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
			tagstring += '(?=(?:[^\\<\\>]*)? class\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.classes[j] +  ')|(?:\\\'\\s*(?:(?:[^\\\'\\>\\<]* ' + s.classes[j] + ' [^\\\'\\>\\<]*)|(?:' + s.classes[j] + ' [^\\\'\\>\\<]*)|(?:[^\\\'\\>\\<]* ' + s.classes[j] + ')|(?:' + s.classes[j] + '))\\s*\\\')|(?:\\"\\s*(?:(?:[^\\"\\>\\<]* ' + s.classes[j] + ' [^\\"\\>\\<]*)|(?:' + s.classes[j] + ' [^\\"\\>\\<]*)|(?:[^\\"\\>\\<]* ' + s.classes[j] + ')|(?:' + s.classes[j] + '))\\s*\\"))[\\s\\>\\/])';


                    }
                }


		if (s.attrs && s.attrs[0] && s.attrs[0][0]) {
                    //атрибуты
		    //https://webdevelopernotes.ru/2011/03/27/attribute-selectors-list/
                    var atrs = '';
                    for (var j in s.attrs) {
			if (s.attrs[j][1]) {
				if (s.attrs[j][2] == '*') {
					//*= Значение атрибута содержит указанный текст

					//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)


					//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:[^\\\'\\"\\s\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[^\\\'\\"\\s\\>\\<\\/\\=]*)|(?:[\\\'\\"][^\\\'\\"\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[^\\\'\\"\\>\\<\\/\\=]*[\\\'\\"]))[\\s\\>\\/])';
	
					//исправлено (отдельно отслеживаются кавычки '"")
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:[^\\\'\\"\\s\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[^\\\'\\"\\s\\>\\<\\/\\=]*)|(?:\\\'[^\\\'\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[^\\\'\\>\\<\\/\\=]*\\\')|(?:\\"[^\\"\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[^\\"\\>\\<\\/\\=]*\\"))[\\s\\>\\/])';

					//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])[^\\s\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[^\\s\\>\\<\\/\\=]*)|(?:\\\'[^\\\'\\>\\<\\/\\=]*' + s.attrs[j][1] + '[^\\\'\\>\\<\\/\\=]*\\\')|(?:\\"[^\\"\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[^\\"\\>\\<\\/\\=]*\\"))[\\s\\>\\/])';


					//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
					tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])[^\\s\\>\\<]*' + s.attrs[j][1] +  '[^\\s\\>\\<]*)|(?:\\\'[^\\\'\\>\\<]*' + s.attrs[j][1] + '[^\\\'\\>\\<]*\\\')|(?:\\"[^\\"\\>\\<]*' + s.attrs[j][1] +  '[^\\"\\>\\<]*\\"))[\\s\\>\\/])';


				} else if (s.attrs[j][2] == '^') {
					//^= Значение атрибута начинается с указанного текста

					//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)

					//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  '[^\\\'\\"\\s\\>\\<\\/\\=]*)|(?:[\\\'\\"]' + s.attrs[j][1] +  '[^\\\'\\"\\>\\<\\/\\=]*\s*[\\\'\\"]))[\\s\\>\\/])';

					//исправлено (отдельно отслеживаются кавычки '"")
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  '[^\\\'\\"\\s\\>\\<\\/\\=]*)|(?:\\\'' + s.attrs[j][1] +  '[^\\\'\\>\\<\\/\\=]*\s*\\\')|(?:\\"' + s.attrs[j][1] +  '[^\\"\\>\\<\\/\\=]*\s*\\"))[\\s\\>\\/])';

					//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.attrs[j][1] +  '[^\\s\\>\\<\\/\\=]*)|(?:\\\'' + s.attrs[j][1] +  '[^\\\'\\>\\<\\/\\=]*\s*\\\')|(?:\\"' + s.attrs[j][1] +  '[^\\"\\>\\<\\/\\=]*\s*\\"))[\\s\\>\\/])';

					//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
					tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.attrs[j][1] +  '[^\\s\\>\\<]*)|(?:\\\'' + s.attrs[j][1] +  '[^\\\'\\>\\<]*\s*\\\')|(?:\\"' + s.attrs[j][1] +  '[^\\"\\>\\<]*\s*\\"))[\\s\\>\\/])';

				} else if (s.attrs[j][2] == '$') {
					//$= Значение атрибута оканчивается указанным текстом

					//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)

					//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:[^\\\'\\"\\s\\>\\<\\/\\=]*' + s.attrs[j][1] +  ')|(?:[\\\'\\"]\s*[^\\\'\\"\\>\\<\\/\\=]*' + s.attrs[j][1] +  '[\\\'\\"]))[\\s\\>\\/])';

					//исправлено (отдельно отслеживаются кавычки '"")
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:[^\\\'\\"\\s\\>\\<\\/\\=]*' + s.attrs[j][1] +  ')|(?:\\\'\s*[^\\\'\\>\\<\\/\\=]*' + s.attrs[j][1] +  '\\\')|(?:\\"\s*[^\\"\\>\\<\\/\\=]*' + s.attrs[j][1] +  '\\"))[\\s\\>\\/])';

					//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])[^\\s\\>\\<\\/\\=]*' + s.attrs[j][1] +  ')|(?:\\\'\s*[^\\\'\\>\\<\\/\\=]*' + s.attrs[j][1] +  '\\\')|(?:\\"\s*[^\\"\\>\\<\\/\\=]*' + s.attrs[j][1] +  '\\"))[\\s\\>\\/])';


					//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
					tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])[^\\s\\>\\<]*' + s.attrs[j][1] +  ')|(?:\\\'\s*[^\\\'\\>\\<]*' + s.attrs[j][1] +  '\\\')|(?:\\"\s*[^\\"\\>\\<]*' + s.attrs[j][1] +  '\\"))[\\s\\>\\/])';



				} else if (s.attrs[j][2] == '|') {
					//|= Дефис в значении атрибута
					//Весь атрибут либо начинается с этой части и затем следует дефис, либо состоит только из этой части


					//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)

		
					//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  '(?:[\\-][^\\\'\\"\\s\\>\\<\\/\\=]*)?)|(?:[\\\'\\"]' + s.attrs[j][1] +  '(?:[\\-][^\\\'\\"\\>\\<\\/\\=]*)?[\\\'\\"]))[\\s\\>\\/])';


					//исправлено (отдельно отслеживаются кавычки '"")
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  '(?:[\\-][^\\\'\\"\\s\\>\\<\\/\\=]*)?)|(?:\\\'' + s.attrs[j][1] +  '(?:[\\-][^\\\'\\>\\<\\/\\=]*)?\\\')|(?:\\"' + s.attrs[j][1] +  '(?:[\\-][^\\"\\>\\<\\/\\=]*)?\\"))[\\s\\>\\/])';

					//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.attrs[j][1] +  '(?:[\\-][^\\s\\>\\<\\/\\=]*)?)|(?:\\\'' + s.attrs[j][1] +  '(?:[\\-][^\\\'\\>\\<\\/\\=]*)?\\\')|(?:\\"' + s.attrs[j][1] +  '(?:[\\-][^\\"\\>\\<\\/\\=]*)?\\"))[\\s\\>\\/])';


					//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
					tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.attrs[j][1] +  '(?:[\\-][^\\s\\>\\<]*)?)|(?:\\\'' + s.attrs[j][1] +  '(?:[\\-][^\\\'\\>\\<]*)?\\\')|(?:\\"' + s.attrs[j][1] +  '(?:[\\-][^\\"\\>\\<]*)?\\"))[\\s\\>\\/])';



				} else if (s.attrs[j][2] == '~') {
					//~= Одно из нескольких значений атрибута
					//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)

		
					//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  ')|(?:[\\\'\\"]\\s*(?:(?:[^\\\'\\"\\>\\<\\/\\=]* ' + s.attrs[j][1] + ' [^\\\'\\"\\>\\<\\/\\=]*)|(?:' + s.attrs[j][1] + ' [^\\\'\\"\\>\\<\\/\\=]*)|(?:[^\\\'\\"\\>\\<\\/\\=]* ' + s.attrs[j][1] + ')|(?:' + s.attrs[j][1] + '))\\s*[\\\'\\"]))[\\s\\>\\/])';


					//исправлено (отдельно отслеживаются кавычки '"")
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  ')|(?:\\\'\\s*(?:(?:[^\\\'\\>\\<\\/\\=]* ' + s.attrs[j][1] + ' [^\\\'\\>\\<\\/\\=]*)|(?:' + s.attrs[j][1] + ' [^\\\'\\>\\<\\/\\=]*)|(?:[^\\\'\\>\\<\\/\\=]* ' + s.attrs[j][1] + ')|(?:' + s.attrs[j][1] + '))\\s*\\\')|(?:\\"\\s*(?:(?:[^\\"\\>\\<\\/\\=]* ' + s.attrs[j][1] + ' [^\\"\\>\\<\\/\\=]*)|(?:' + s.attrs[j][1] + ' [^\\"\\>\\<\\/\\=]*)|(?:[^\\"\\>\\<\\/\\=]* ' + s.attrs[j][1] + ')|(?:' + s.attrs[j][1] + '))\\s*\\"))[\\s\\>\\/])';

					//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.attrs[j][1] +  ')|(?:\\\'\\s*(?:(?:[^\\\'\\>\\<\\/\\=]* ' + s.attrs[j][1] + ' [^\\\'\\>\\<\\/\\=]*)|(?:' + s.attrs[j][1] + ' [^\\\'\\>\\<\\/\\=]*)|(?:[^\\\'\\>\\<\\/\\=]* ' + s.attrs[j][1] + ')|(?:' + s.attrs[j][1] + '))\\s*\\\')|(?:\\"\\s*(?:(?:[^\\"\\>\\<\\/\\=]* ' + s.attrs[j][1] + ' [^\\"\\>\\<\\/\\=]*)|(?:' + s.attrs[j][1] + ' [^\\"\\>\\<\\/\\=]*)|(?:[^\\"\\>\\<\\/\\=]* ' + s.attrs[j][1] + ')|(?:' + s.attrs[j][1] + '))\\s*\\"))[\\s\\>\\/])';

					//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
					tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.attrs[j][1] +  ')|(?:\\\'\\s*(?:(?:[^\\\'\\>\\<]* ' + s.attrs[j][1] + ' [^\\\'\\>\\<]*)|(?:' + s.attrs[j][1] + ' [^\\\'\\>\\<]*)|(?:[^\\\'\\>\\<]* ' + s.attrs[j][1] + ')|(?:' + s.attrs[j][1] + '))\\s*\\\')|(?:\\"\\s*(?:(?:[^\\"\\>\\<]* ' + s.attrs[j][1] + ' [^\\"\\>\\<]*)|(?:' + s.attrs[j][1] + ' [^\\"\\>\\<]*)|(?:[^\\"\\>\\<]* ' + s.attrs[j][1] + ')|(?:' + s.attrs[j][1] + '))\\s*\\"))[\\s\\>\\/])';


				} else {
					//= Значение атрибута равно указанному тексту

					//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)


					//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  ')|(?:[\\\'\\"]' + s.attrs[j][1] +  '[\\\'\\"]))[\\s\\>\\/])';

					//исправлено (отдельно отслеживаются кавычки '"")
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:' + s.attrs[j][1] +  ')|(?:\\\'' + s.attrs[j][1] +  '\\\')|(?:\\"' + s.attrs[j][1] +  '\\"))[\\s\\>\\/])';

					//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
					tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])' + s.attrs[j][1] +  ')|(?:\\\'' + s.attrs[j][1] +  '\\\')|(?:\\"' + s.attrs[j][1] +  '\\"))[\\s\\>\\/])';

					//проверены ограничения на \\/ \\= (они могут быть в значениях атрибутов!, особенно src href)


				}
			} else {
				//= Значение атрибута отсутсвует или любое

					//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)


					//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '(?:\\s*=\\s*(?:(?:[^\\\'\\"\\s\\>\\<\\/\\=]+)|(?:[\\\'\\"]\\s*[^\\\'\\"\\s\\>\\<\\/\\=]+[^\\\'\\"\\>\\<\\/\\=]*\\s*[\\\'\\"])))?[\\s\\>\\/])';

					//исправлено (отдельно отслеживаются кавычки '"")
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '(?:\\s*=\\s*(?:(?:[^\\\'\\"\\s\\>\\<\\/\\=]+)|(?:\\\'\\s*[^\\\'\\s\\>\\<\\/\\=]+[^\\\'\\>\\<\\/\\=]*\\s*\\\')|(?:\\"\\s*[^\\"\\s\\>\\<\\/\\=]+[^\\"\\>\\<\\/\\=]*\\s*\\")))?[\\s\\>\\/])';


					//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
					//tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '(?:\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])[^\\s\\>\\<\\/\\=]+)|(?:\\\'\\s*[^\\\'\\s\\>\\<\\/\\=]+[^\\\'\\>\\<\\/\\=]*\\s*\\\')|(?:\\"\\s*[^\\"\\s\\>\\<\\/\\=]+[^\\"\\>\\<\\/\\=]*\\s*\\")))?[\\s\\>\\/])';

					//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
					tagstring += '(?=(?:[^\\<\\>]*)? ' + s.attrs[j][0] + '(?:\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])[^\\s\\>\\<]+)|(?:\\\'\\s*[^\\\'\\s\\>\\<]+[^\\\'\\>\\<]*\\s*\\\')|(?:\\"\\s*[^\\"\\s\\>\\<]+[^\\"\\>\\<]*\\s*\\")))?[\\s\\>\\/])';



			}
        	    }
	
		}
		


	return tagstring;
    }




    //https://stackoverflow.com/questions/17888039/javascript-efficient-parsing-of-css-selector
    function parse(subselector) {
        var obj = {
            tags: [],
            classes: [],
            ids: [],
            child: [],
            attrs: []
        };


//доработал, иначе краш например на '[href="aaaa."]'
//        subselector.split(/(?=\.)|(?=#)|(?=\:)|(?=\[)/).forEach(function(token) {
        subselector.split(/(?=\.[^\[\]]+(?:$|\[))|(?=#[^\[\]]+(?:$|\[))|(?=\:[^\[\]]+(?:$|\[))|(?=\[)/).forEach(function(token) {
            switch (token[0]) {
                case '#':
                    obj.ids.push(token.slice(1));
                    break;
                case '.':
                    obj.classes.push(token.slice(1));
                    break;
                case ':':
                    obj.child.push(token.slice(11, -1));
                    break;
                case '[':
                    obj.attrs.push(token.slice(1, -1).split(/\s*=\s*/, 2));
		    var elnum = obj.attrs.length - 1;
		    if (obj.attrs[elnum].length == 2)
			//удаляем в значении атрибута кавычки '"
			obj.attrs[elnum][1] = obj.attrs[elnum][1].replace(/^[\'\"]*(.*?)[\'\"]*$/, "$1");
		    var match = obj.attrs[elnum][0].match(/^(.*)([\~\^\$\*\|])$/);
		    if (match&&match[2]) {
		            obj.attrs[elnum][0] = match[1];
			    //добавляем третий элемент в качестве признака ^= ~= $= *= |=
		            obj.attrs[elnum][2] = match[2];
		    }
                    break;
                default:
                    obj.tags.push(token);
                    break;
            }
        });
        return obj;
    }



//http://blog.stevenlevithan.com/archives/javascript-match-nested
//переделано под игнорирование регистра символов
//переделано под анализ незакрытых тегов (иначе удаляло часть кода вначале или при отсутсвии закрывающих полностью все)
//работает только results[0], остальные выдадут неверну инфу, иначе пришлось бы еще долго заморачиваться
//находит с заданного элемента и до его закрывающего элемента, с учетом вложенности


/*** matchRecursive
	accepts a string to search and a format (start and end tokens separated by "...").
	returns an array of matches, allowing nested instances of format.

	examples:
		matchRecursive("test",          "(...)")   -> []
		matchRecursive("(t(e)s)()t",    "(...)")   -> ["t(e)s", ""]
		matchRecursive("t<e>>st",       "<...>")   -> ["e"]
		matchRecursive("t<<e>st",       "<...>")   -> ["e"]
		matchRecursive("t<<e>>st",      "<...>")   -> ["<e>"]
		matchRecursive("<|t<e<|s|>t|>", "<|...|>") -> ["t<e<|s|>t"]
*/

var matchRecursive = function () {
	var	formatParts = /^([\S\s]+?)\.\.\.([\S\s]+)/,
		metaChar = /[-[\]{}()*+?.\\^$|,]/g,
		escape = function (str) {
			return str.replace(metaChar, "\\$&");
		};

	return function (str, format) {
		var p = formatParts.exec(format);
		if (!p) throw new Error("format must include start and end tokens separated by '...'");
		if (p[1] == p[2]) throw new Error("start and end format tokens cannot be identical");

		var	opener = p[1],
			closer = p[2],
			/* Use an optimized regex when opener and closer are one character each */
//			iterator = new RegExp(format.length == 5 ? "["+escape(opener+closer)+"]" : escape(opener)+"|"+escape(closer), "g"),
//игнорируем регистр
			iterator = new RegExp(format.length == 5 ? "["+escape(opener+closer)+"]" : escape(opener)+"|"+escape(closer), "ig"),
			results = [],
			openTokens, matchStartIndex, match;

		var foundfirst = 0, matchStartIndex2;

		do {
			openTokens = 0;
			while (match = iterator.exec(str)) {
//				if (match[0] == opener) {
//игнорируем регистр
				if (match[0].toLowerCase() == opener.toLowerCase()) {
					if (!openTokens)
						matchStartIndex = iterator.lastIndex;
					if (!foundfirst) {
//здесь реализован механизм сохранения с первого найденного тега, если нет части закрыващих тегов
//иначе сохранит только начиная с закрывающихся тегов
						matchStartIndex2 = iterator.lastIndex;
						foundfirst = 1;
					}
					openTokens++;
				} else if (openTokens) {
					openTokens--;
					if (!openTokens) {
//						results.push(str.slice(matchStartIndex, match.index));
//записываем с первого найденного (это важно для 0-го элемента, который мы используем, на остальных нам без разницы, хотя для них так неправильно)
//с остальными совпадениями даже не буду заморачиваться
						results.push(str.slice(matchStartIndex2, match.index));
//выходим полностью из всех циклов, что бы не тратить время на лишние итерации
						break;
					}
				}
			}

		} while (openTokens && (iterator.lastIndex = matchStartIndex));

		if (foundfirst&&(!results.length)) {
//если был обнаружен первый элемент, но нет выходных данных, значит нет ни одного закрывающего элемента
//надо весь массив занести до конца
			results.push(str.slice(matchStartIndex2));

		}

		return results;
	};
}();



    if (typeof field_value_selector == 'undefined') {
	field_value_selector = '';
    }

    if (typeof html != 'string') {
	if (fieldName) {
		var add = ', fieldName:' + fieldName;
	} else {
		var add = '';
	}
        return {
            'errors': 'Html is not string' + add,
            'result': false
        };
    }


    if (typeof field_value_selector != 'string') {
	if (fieldName) {
		var add = ', fieldName:' + fieldName;
	} else {
		var add = '';
	}
        return {
            'errors': 'parameter field_value_selector is not string' + add,
            'result': false
        };
    }


if (clearhtml) {
//это делать обязательно, но если еще не сделано, иначе лишняя трата времени
    html = html.replace(/\r/g, " "); // Remove newlines
    html = html.replace(/\n/g, " "); // Remove newlines
    html = html.replace(/\t/g, " "); // Remove tabs
    html = html.replace(/(\<\!\-\-.*?\-\-\>)/g, ""); // Remove commented text
    html = html.replace(/(\<script.*?\<\/script\>)/igm, '<s'+'cript></s'+'cript>');
    html = html.replace(/(\<noscript.*?\<\/noscript\>)/igm, '<nos'+'cript></nos'+'cript>');
    html = html.replace(/(\<link .*?\>)/igm, '<l'+'ink>');
    html = html.replace(/(\<style.*?\<\/style\>)/igm, '<s'+'tyle></s'+'tyle>');
}


    html = html.trim();
    if (html == '') {
//html пустой, не нашли там наши теги по определению, это не ошибка
        return {
            'errors': '',
            'html': '',
            'result': ''
        };
    }


    var htmllen = html.length;
    var selector = false;
    var error = '';

if (field_value_selector !== '') {
//если пусто то все значение html берется

    field_value_selector.split(/\s*\,\s*/).forEach(function(token) {

        var sel = parseOneLastSelector(token);

        if (!sel) {
		if (fieldName) {
		    error += 'Error in selector: ' + token + ' fieldName:' + fieldName + "\n";
		} else {
		    error += 'Error in selector: ' + token + "\n";
		}
	}
	if (error) {
		//если есть ошибка в селекторе, остальные просто проверяем на ошибки без дальнейшего парсинга
		 return;
	}

	//используем первый сработавший селектор
        if (selector) return;



        var re = new RegExp(sel['regexp'], "i");
	var match = html.match(re);
	if (match&&match[sel['selectorsnum']]) {
//найден непустой последний мини-селектор
//теперь проверяем вхождение каждого следующего селектора в предыдущий (является потомком или нет)
//так как у нас reqexp вместо DOM (просто ищем следующий без анализа он потомок или нет)
//то надо проверить потомков после (хотя в большинстве случаем сработает и так, так как мы ищем ближайщий тег к предыдущему, но надо проверить)
//если нет закрывающих тегов, то может тоже "пропустить" дальше конца своего тега
//практически полностью повторяет функционал для поиска text

		//флаг, что найдены вхождения всех следующих мини-селекторов в предыдущие (то есть все следующие точно потомки)
		var foundnextchilds = 1;

		for (var i=1; i<sel['selectorsnum']; i++) {
//от первого до предпоследнего селектора проверяем их потомков

//у этого элемента прямой потомок, которого не нужно проверять (то есть у него нет nth-child либо он равен 1)
			if (sel['directchilds'][i+1])
				continue;

//найти внутреннее содержимое элемента (innerHTML)
//обязательно начинается с открывающего тега элемента и до конца страницы
//предусмотрено даже для одиночных элементов (до открывающего неодиночного элемента)
//	то есть весь код после одиночного элемента до открывающего тега
//основная задача функции - найти текст "внутри" элемента


			var re = '^\\<([a-zA-Z][a-zA-Z0-9]*)[\\s\\<\\>\\/].*$';
			var tag = match[i].replace(new RegExp(re, "i"), "$1");
			if (tag == match[i]) {
//не нашли тег, такого не может быть, ошибка регулярных выражений селектора
				if (fieldName) {
				    error += 'Error in selector reqexp for fieldName:' + fieldName + "\n";
				} else {
				    error += "Error in selector reqexp\n";
				}
				foundnextchilds = 0;
				break;
			}

			var text = matchRecursive(match[i],'<' + tag + '...</' + tag + '>')
			if (!text.length) {
//не нашли открывающий тег, такого здесь не может быть, ошибка функции matchRecursive
				if (fieldName) {
				    error += 'Error in function matchRecursive for fieldName:' + fieldName + "\n";
				} else {
				    error += "Error in function matchRecursive\n";
				}
				foundnextchilds = 0;
				break;
			}

			text = text[0];


			//здесь осталась закрывающая часть тега, надо убрать (но запомнить, что бы позже добавить)
			var re = '^([^\\>]*\\>)(.*?)$';
			var match2 = text.match(new RegExp(re, "i"));

			if (!(match2&&match2[1])) {
//такого не должно быть, нет закрывающего > в открывающем теге, ошибка
				if (fieldName) {
				    error += 'Error: not found end tag symbol ">" for fieldName:' + fieldName + "\n";
				} else {
				    error += "Error: not found end tag symbol ">"\n";
				}
				foundnextchilds = 0;
				break;
			}

			text = match2[2];

			//здесь это несколько бредово - анализировать "потомков" одиночных тегов, но оставил, ничего не меняет, все равно их надо обнаруживать
			//правильно было их отбрасывать здесь, если вставлены в середине, а не последний мини-селектор
			//зато теперь есть сомнительная "уникальная" возможность - промежуточные мини-селекторы могут быть одиночными тегами
			//	после которых могут идти только такие же одиночные теги
			//типа div > br > hr

			//если это одиночный тег, то выводим все до первого неодиночного тега или неодиночного закрывающего тега
			var re = new RegExp('^(?:area|base|basefont|br|col|frame|hr|img|input|isindex|link|meta|param)$', "i");
			if (re.test(tag)) {
				var re = '^((?:(?:\\<area)|(?:\\<base)|(?:\\<basefont)|(?:\\<br)|(?:\\<col)|(?:\\<frame)|(?:\\<hr)|(?:\\<img)|(?:\\<input)|(?:\\<isindex)|(?:\\<link)|(?:\\<meta)|(?:\\<param)|(?:[^\\<]+))*).*?$';
				text = text.replace(new RegExp(re, "i"), "$1")
			}

			//нашли innerHTML (без самого оборачивающего тега)
			//теперь добавим обратно открывающий тег (закрывающий тег неважен), внутри тега должен быть потомок

			text = '<' + tag + match2[1] + text;


			//а теперь проверяем следующий найденный тег на предмет вхождения в этот (потомок или нет)
			//начало потомка должно входить в текущий тег

			//считаем элементы строки от 0, как в функциях строк обычно делается
			var endtagnum = htmllen - match[i].length + text.length;
			var startnexttagnum = htmllen - match[i+1].length;


			if (startnexttagnum > endtagnum) {
				foundnextchilds = 0;
				break;
			}


		}


		if (foundnextchilds) {
			selector = sel;
			//наш html - это html-код последнего мини-селектора
     		        selector['html'] = match[sel['selectorsnum']].trim();
		}


	}


/*
//так было, когда искали только один последний селектор
        var re = new RegExp(sel['regexp'], "i");
        if (re.test(html)) {
		var b = html.replace(re, "$1")
		selector = sel;
     	        selector['html'] = b.trim();
        }
*/


    });


} else {

	selector={};
	selector['fullselector'] = field_value_selector;

	selector['html'] = html;

}


    if (error) {
//ошибка в селекторах
        return {
            'errors': error,
            'result': false
        };
    }

    if (!selector) {
//селекторы не найдены на странице, не ошибка
        return {
            'errors': '',
            'html': '',
            'result': ''
        };
    }


    if (!selector['html']) {
//селектор найден, но пустое его содержимое, не ошибка
        selector['result'] = '';
        selector['errors'] = '';
	return selector;
    }



        if (type == 'text') {
            //если тип текст, то самый последний селектор должен иметь тег
            //здесь можеть быть некорректно обнаружено, если несколько этих тегов 
            //вложены в друг друга, или нет закрывающего тега - тогда используем type = undefined и field_Fun

//найти внутреннее содержимое элемента (innerHTML)
//обязательно начинается с открывающего тега элемента и до конца страницы
//предусмотрено даже для одиночных элементов (до открывающего неодиночного элемента)
//	то есть весь код после одиночного элемента до открывающего тега
//основная задача функции - найти текст "внутри" элемента


            var re = '^\\<([a-zA-Z][a-zA-Z0-9]*)[\\s\\<\\>\\/].*$';
            var tag = selector['html'].replace(new RegExp(re, "i"), "$1");
            if (tag == selector['html']) {
//не нашли тег, такого не может быть, ошибка регулярных выражений селектора
		if (fieldName) {
		    var error = 'Error in selector reqexp for fieldName:' + fieldName + "\n";
		} else {
		    var error = "Error in selector reqexp\n";
		}
		selector['result'] = false;
		selector['errors'] = error;
		return selector;
	    }

/*
            var re = '^[^\\>]*\\>(.*?)\\<\\/' + tag + '\\>.*$';

	    //попытка улучшить поиск закрывающего тега - находим последний тег, если есть вложенные
	    //это не работает, нужна рекурсия!
//            var re = '^[^\\>]*\\>((?:(?:\\<' + tag + '[\\s\\/]?[^\\>]*\\>.*?\\<\\/' + tag + '\\>)*(?!\\<\\/' + tag + '\\>).*?)*.*?)\\<\\/' + tag + '\\>.*$';
//            var re = '^[^\\>]*\\>((?:(?:\\<' + tag + '[\\s\\/]?[^\\>]*\\>.*?\\<\\/' + tag + '\\>)*(?!\\<\\/' + tag + '\\>).*?)*(?!\\<\\/' + tag + '\\>).*?)\\<\\/' + tag + '\\>.*$';

            var text = selector['html'].replace(new RegExp(re, "i"), "$1")

            if (text == selector['html']) {
//не нашли text, не ошибка
		selector['result'] = '';
		selector['errors'] = '';
		return selector;
	    }

*/

	    //улучшен поиск закрывающего тега - находим последний закрывающий тег, если есть вложенные

		var text = matchRecursive(selector['html'],'<' + tag + '...</' + tag + '>')
		if (!text.length) {
//не нашли открывающий тег, такого здесь не может быть, ошибка функции matchRecursive
			if (fieldName) {
			    var error = 'Error in function matchRecursive for fieldName:' + fieldName + "\n";
			} else {
			    var error = "Error in function matchRecursive\n";
			}
			selector['result'] = false;
			selector['errors'] = error;
			return selector;
		}

		text = text[0];
		//здесь осталась закрывающая часть тега, надо убрать
		var re = '^[^\\>]*\\>(.*?)$';
		text = text.replace(new RegExp(re, "i"), "$1")

            if (text == selector['html']) {
//такого не должно быть, нет закрывающего > в открывающем теге, ошибка
		if (fieldName) {
		    var error = 'Error: not found end tag symbol ">" for fieldName:' + fieldName + "\n";
		} else {
		    var error = "Error: not found end tag symbol ">"\n";
		}
		selector['result'] = false;
		selector['errors'] = error;
		return selector;
	    }



		//если это одиночный тег, то выводим все до первого неодиночного тега или неодиночного закрывающего тега
		var re = new RegExp('^(?:area|base|basefont|br|col|frame|hr|img|input|isindex|link|meta|param)$', "i");
		if (re.test(tag)) {
			var re = '^((?:(?:\\<area)|(?:\\<base)|(?:\\<basefont)|(?:\\<br)|(?:\\<col)|(?:\\<frame)|(?:\\<hr)|(?:\\<img)|(?:\\<input)|(?:\\<isindex)|(?:\\<link)|(?:\\<meta)|(?:\\<param)|(?:[^\\<]+))*).*?$';
			text = text.replace(new RegExp(re, "i"), "$1")
		}


	    //не чистим от тегов внутри, это делаем уже в дальнейшей функции, если нужно


            selector[type] = text.trim();

	    if (selector[type] == '') {
//не нашли text, не ошибка
		selector['result'] = '';
		selector['errors'] = '';
		return selector;
	    }

//            selector['result'] = text;
            selector['result'] = selector[type];


        } else if (type) {

	    //если есть '" в начале и в конце значения атрибута, то могут быть пробелы в значении аттрибута
	    //если нет '" в начале и в конце, то пробелов не может быть в значении атрибута (это правила верстки)
            //символы '" могут теоретически попадаться в атрибуте - тогда используем type = undefined и field_Fun


	//при отсутствии в начале и в конце символа '" не может быть пробелов в атрибуте (то есть только одно значение возможно)

	//не учитываем ошибку верстки, как браузер (не может быть один символ '" только вначале или только в конце)
	//var re = '^\\<[^\\<\\>]+ ' + type + '(?:\\s*=\\s*(?:([^\\\'\\"\\s\\>\\<\\/\\=]+)|(?:[\\\'\\"]\\s*([^\\\'\\"\\s\\>\\<\\/\\=]+[^\\\'\\"\\>\\<\\/\\=]*)\\s*[\\\'\\"])))?[\\s\\>\\/].*$';
	//var attr = selector['html'].replace(new RegExp(re, "i"), "$1$2")

	//исправлено (отдельно отслеживаются кавычки '"")
	//var re = '^\\<[^\\<\\>]+ ' + type + '(?:\\s*=\\s*(?:([^\\\'\\"\\s\\>\\<\\/\\=]+)|(?:\\\'\\s*([^\\\'\\s\\>\\<\\/\\=]+[^\\\'\\>\\<\\/\\=]*)\\s*\\\')|(?:\\"\\s*([^\\"\\s\\>\\<\\/\\=]+[^\\"\\>\\<\\/\\=]*)\\s*\\")))?[\\s\\>\\/].*$';
	//var attr = selector['html'].replace(new RegExp(re, "i"), "$1$2$3")

	//исправлено (возможны обе кавычки в значениях аттрибутов) (?=[^\\s\\\'\\"])
	//var re = '^\\<[^\\<\\>]+ ' + type + '(?:\\s*=\\s*(?:(?=[^\\s\\\'\\"])([^\\s\\>\\<\\/\\=]+)|(?:\\\'\\s*([^\\\'\\s\\>\\<\\/\\=]+[^\\\'\\>\\<\\/\\=]*)\\s*\\\')|(?:\\"\\s*([^\\"\\s\\>\\<\\/\\=]+[^\\"\\>\\<\\/\\=]*)\\s*\\")))?[\\s\\>\\/].*$';
	//var attr = selector['html'].replace(new RegExp(re, "i"), "$1$2$3")

	//исправлен последний вариант (просто была логическая ошибка, сравнил с //= Значение атрибута отсутсвует или любое)
	//var re = '^\\<[^\\<\\>]+ ' + type + '(?:\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])([^\\s\\>\\<\\/\\=]+))|(?:\\\'\\s*([^\\\'\\s\\>\\<\\/\\=]+[^\\\'\\>\\<\\/\\=]*)\\s*\\\')|(?:\\"\\s*([^\\"\\s\\>\\<\\/\\=]+[^\\"\\>\\<\\/\\=]*)\\s*\\")))?[\\s\\>\\/].*$';
	//var attr = selector['html'].replace(new RegExp(re, "i"), "$1$2$3")

	//убраны ограничения на \\/ \\= (может быть в значениях атрибутов!, особенно src href)
	var re = '^\\<[^\\<\\>]+ ' + type + '(?:\\s*=\\s*(?:(?:(?=[^\\s\\\'\\"])([^\\s\\>\\<]+))|(?:\\\'\\s*([^\\\'\\s\\>\\<]+[^\\\'\\>\\<]*)\\s*\\\')|(?:\\"\\s*([^\\"\\s\\>\\<]+[^\\"\\>\\<]*)\\s*\\")))?[\\s\\>\\/].*$';
	var attr = selector['html'].replace(new RegExp(re, "i"), "$1$2$3")



            if (attr == selector['html']) {
//не нашли атрибут, не ошибка
		selector['result'] = '';
		selector['errors'] = '';
		return selector;
	    }


            //selector[type] = attr.trim().toLowerCase();
            selector[type] = attr.trim();

	    if (selector[type] == '') {
//пустой атрибут, не ошибка
		selector['result'] = '';
		selector['errors'] = '';
		return selector;
	    }


//            selector['result'] = attr;
            selector['result'] = selector[type];


        } else {
            selector['result'] = selector['html'];
        }



	if (field_replace) {
		var errmes = '';
		if (typeof field_replace == 'object') {
			if (field_replace.length) {
				if ((typeof field_replace[0] == 'string') && ((typeof field_replace[1] == 'string')||(typeof field_replace[1] == 'function')) && ((typeof field_replace[2] == 'undefined')||(typeof field_replace[1] == 'string'))) {
			            try {
					if (field_replace[2]) {
						var re = new RegExp(field_replace[0], field_replace[2])
					} else {
						var re = new RegExp(field_replace[0])
					}
					if (re.test(selector['result'])) {
						selector['field_replace'] = selector['result'].replace(re, field_replace[1]);
				                selector['field_replace'] = selector['field_replace'].trim();
					} else {
						//не найдено регулярное выражение, не ошибка
						selector['field_replace'] = '';
						selector['result'] = '';
						selector['errors'] = '';
						return selector;
					}
			                selector['result'] = selector['field_replace'];
			            } catch (e) {
					errmes = 'bad parameter field_replace:' + e.message + ' , selector=' + selector['fullselector'];
			            }
				} else {
					errmes = 'bad parameter field_replace, selector=' + selector['fullselector'];
				}
			}
		} else {
			errmes = 'bad parameter field_replace, selector=' + selector['fullselector'];
		}
		if (errmes) {
			if (fieldName) {
				var add = ', fieldName:' + fieldName;
			} else {
				var add = '';
			}
			selector['result'] = false;
			selector['errors'] = errmes + add;
			return selector;
		}
	}


	if (field_Fun) {
		var errmes = '';
		if (typeof field_Fun == 'function') {
	            try {
	                selector['htmlClear'] = field_Fun(selector['result'], selector['fullselector'], url, fieldName, fields);
			if (typeof selector['htmlClear'] != 'string') {
				errmes = 'bad return from function field_Fun, selector=' + selector['fullselector'];
			} else {
	                	selector['htmlClear'] = selector['htmlClear'].trim();
		                selector['result'] = selector['htmlClear'];
			}
        	    } catch (e) {
				errmes = 'bad function field_Fun:' + e.message + ' , selector=' + selector['fullselector'];
	            }
		} else {
			errmes = 'parameter field_Fun is not function, selector=' + selector['fullselector'];
		}
		if (errmes) {
			if (fieldName) {
				var add = ', fieldName:' + fieldName;
			} else {
				var add = '';
			}
			selector['result'] = false;
			selector['errors'] = errmes + add;
			return selector;
		}
        }


    return selector;

}


function parseHtmlFUN(html, field_value_selector, field_replace, field_Fun, url, fields, fieldName) {
//функции очистки кода (если не используется parseHtml)

//field_value_selector - нескольких селекторов (через запятую)
//field_value_selector оставлено для совместимости с parseHtml

    //url, fields только передаются в field_Fun, добавлены для совместимости field_Fun с другими функциями
    //fieldName - информационная переменная для ошибок (в каком поле произошла ошибка)
    //field_replace - поиск и замена регулярного выражения или строки, чистит html-код
    //			три параметра в массиве: что ищем и замены, на что меняем, флаги поиска (флаги необязательны)
    //			['^\\<([^\\s\\<\\>\\/]+)([\\s\\<\\>\\/]).*$', function(match, p1, p2) {},'gim']
    //			['^\\<([^\\s\\<\\>\\/]+)[\\s\\<\\>\\/].*$','$1','gim']
    //field_Fun - чистит html-код после выполнения field_replace
    //			function(html, selector, url, fields) { return html; }


    if (typeof field_value_selector == 'undefined') {
	field_value_selector = '';
    }

    if (typeof html != 'string') {
	if (fieldName) {
		var add = ', fieldName:' + fieldName;
	} else {
		var add = '';
	}
        return {
            'errors': 'Html is not string' + add,
            'result': false
        };
    }


    if (typeof field_value_selector != 'string') {
	if (fieldName) {
		var add = ', fieldName:' + fieldName;
	} else {
		var add = '';
	}
        return {
            'errors': 'parameter field_value_selector is not string' + add,
            'result': false
        };
    }


	selector={};
	selector['fullselector'] = field_value_selector;

	selector['result'] = html;
	selector['html'] = html;



    if (!selector['html']) {
//селектор найден, но пустое его содержимое, не ошибка
        selector['result'] = '';
        selector['errors'] = '';
	return selector;
    }


	if (field_replace) {
		var errmes = '';
		if (typeof field_replace == 'object') {
			if (field_replace.length) {
				if ((typeof field_replace[0] == 'string') && ((typeof field_replace[1] == 'string')||(typeof field_replace[1] == 'function')) && ((typeof field_replace[2] == 'undefined')||(typeof field_replace[1] == 'string'))) {
			            try {
					if (field_replace[2]) {
						var re = new RegExp(field_replace[0], field_replace[2])
					} else {
						var re = new RegExp(field_replace[0])
					}
					if (re.test(selector['result'])) {
						selector['field_replace'] = selector['result'].replace(re, field_replace[1]);
				                selector['field_replace'] = selector['field_replace'].trim();
					} else {
						//не найдено регулярное выражение, не ошибка
						selector['field_replace'] = '';
						selector['result'] = '';
						selector['errors'] = '';
						return selector;
					}
			                selector['result'] = selector['field_replace'];
			            } catch (e) {
					errmes = 'bad parameter field_replace:' + e.message + ' , selector=' + selector['fullselector'];
			            }
				} else {
					errmes = 'bad parameter field_replace, selector=' + selector['fullselector'];
				}
			}
		} else {
			errmes = 'bad parameter field_replace, selector=' + selector['fullselector'];
		}
		if (errmes) {
			if (fieldName) {
				var add = ', fieldName:' + fieldName;
			} else {
				var add = '';
			}
			selector['result'] = false;
			selector['errors'] = errmes + add;
			return selector;
		}
	}


	if (field_Fun) {
		var errmes = '';
		if (typeof field_Fun == 'function') {
	            try {
	                selector['htmlClear'] = field_Fun(selector['result'], selector['fullselector'], url, fieldName, fields);
			if (typeof selector['htmlClear'] != 'string') {
				errmes = 'bad return from function field_Fun, selector=' + selector['fullselector'];
			} else {
	                	selector['htmlClear'] = selector['htmlClear'].trim();
		                selector['result'] = selector['htmlClear'];
			}
        	    } catch (e) {
				errmes = 'bad function field_Fun:' + e.message + ' , selector=' + selector['fullselector'];
	            }
		} else {
			errmes = 'parameter field_Fun is not function, selector=' + selector['fullselector'];
		}
		if (errmes) {
			if (fieldName) {
				var add = ', fieldName:' + fieldName;
			} else {
				var add = '';
			}
			selector['result'] = false;
			selector['errors'] = errmes + add;
			return selector;
		}
        }


    return selector;

}


function idndomain(domain, decodeflg) {


    //на базе https://github.com/bestiejs/punycode.js
    //что бы реально получить домен надо доработать
    //проверка
    //http://wservices.ru/idnconv.php

    if (typeof domain != 'string') {
        return domain;
    }

    if (domain == '') {
        return domain;
    }


    domain = domain.toLowerCase();


    var TMIN = 1;
    var TMAX = 26;
    var BASE = 36;
    var SKEW = 38;
    var DAMP = 700; // initial bias scaler
    var INITIAL_N = 128;
    var INITIAL_BIAS = 72;
    var MAX_INTEGER = Math.pow(2, 53);

    function adapt_bias(delta, n_points, is_first) {
        // scale back, then increase delta
        delta /= is_first ? DAMP : 2;
        delta += ~~(delta / n_points);

        var s = (BASE - TMIN);
        var t = ~~((s * TMAX) / 2); // threshold=455

        for (var k = 0; delta > t; k += BASE) {
            delta = ~~(delta / s);
        }

        var a = (BASE - TMIN + 1) * delta;
        var b = (delta + SKEW);

        return k + ~~(a / b);
    }

    function next_smallest_codepoint(codepoints, n) {
        var m = 0x110000; // unicode upper bound + 1

        for (var i = 0, len = codepoints.length; i < len; ++i) {
            var c = codepoints[i];
            if (c >= n && c < m) {
                m = c;
            }
        }

        // sanity check - should not happen
        if (m >= 0x110000) {
            throw new Error('Next smallest code point not found.');
        }

        return m;
    }

    function encode_digit(d) {
        return d + (d < 26 ? 97 : 22);
    }

    function decode_digit(d) {
        if (d >= 48 && d <= 57) {
            return d - 22; // 0..9
        }
        if (d >= 65 && d <= 90) {
            return d - 65; // A..Z
        }
        if (d >= 97 && d <= 122) {
            return d - 97; // a..z
        }
        throw new Error('Illegal digit #' + d);
    }

    function threshold(k, bias) {
        if (k <= bias + TMIN) {
            return TMIN;
        }
        if (k >= bias + TMAX) {
            return TMAX;
        }
        return k - bias;
    }

    function encode_int(bias, delta) {
        var result = [];

        for (var k = BASE, q = delta;; k += BASE) {
            var t = threshold(k, bias);
            if (q < t) {
                result.push(encode_digit(q));
                break;
            } else {
                result.push(encode_digit(t + ((q - t) % (BASE - t))));
                q = ~~((q - t) / (BASE - t));
            }
        }

        return result;
    }

    function encode(input) {
        if (typeof input != 'string') {
            return input;
        }

        input = input.split('').map(function(c) {
            return c.charCodeAt(0);
        });

        var output = [];
        var non_basic = [];

        for (var i = 0, len = input.length; i < len; ++i) {
            var c = input[i];
            if (c < 128) {
                output.push(c);
            } else {
                non_basic.push(c);
            }
        }

        var b, h;
        b = h = output.length;

        if (b) {
            output.push(45); // delimiter '-'
        }

        var n = INITIAL_N;
        var bias = INITIAL_BIAS;
        var delta = 0;

        for (var len = input.length; h < len; ++n, ++delta) {
            var m = next_smallest_codepoint(non_basic, n);
            delta += (m - n) * (h + 1);
            n = m;

            for (var i = 0; i < len; ++i) {
                var c = input[i];
                if (c < n) {
                    if (++delta == MAX_INTEGER) {
                        throw new Error('Delta overflow.');
                    }
                } else if (c == n) {
                    // TODO append in-place?
                    // i.e. -> output.push.apply(output, encode_int(bias, delta));
                    output = output.concat(encode_int(bias, delta));
                    bias = adapt_bias(delta, h + 1, b == h);
                    delta = 0;
                    h++;
                }
            }
        }

        return String.fromCharCode.apply(String, output);
    }

    function decode(input) {
        if (typeof input != 'string') {
            return input;
        }

        // find basic code points/delta separator
        var b = 1 + input.lastIndexOf('-');

        input = input.split('').map(function(c) {
            return c.charCodeAt(0);
        });

        // start with a copy of the basic code points
        var output = input.slice(0, b ? (b - 1) : 0);

        var n = INITIAL_N;
        var bias = INITIAL_BIAS;

        for (var i = 0, len = input.length; b < len; ++i) {
            var org_i = i;

            for (var k = BASE, w = 1;; k += BASE) {
                var d = decode_digit(input[b++]);

                // TODO overflow check
                i += d * w;

                var t = threshold(k, bias);
                if (d < t) {
                    break;
                }

                // TODO overflow check
                w *= BASE - t;
            }

            var x = 1 + output.length;
            bias = adapt_bias(i - org_i, x, org_i == 0);
            // TODO overflow check
            n += ~~(i / x);
            i %= x;

            output.splice(i, 0, n);
        }

        return String.fromCharCode.apply(String, output);
    }



    domain = domain.split('.');

    if (decodeflg) {
        for (var i = 0, len = domain.length; i < len; ++i) {
            if (domain[i] != '') {
                if (domain[i].substring(0, 4) == 'xn--') {
                    var d = domain[i].substring(4);
                    var c = decode(d);
                    if (c != d) {
                        domain[i] = c;
                    }
                }
            }
        }
    } else {
        for (var i = 0, len = domain.length; i < len; ++i) {
            var c = encode(domain[i]);
            var l = c.length;
            //если это английские буквы то в конце появится -, то есть на входе domain а на выходе domain-
            var d = c.substring(l - 1, l);
            if (!((d == '-') && (c.substring(0, l - 1) == domain[i]))) {
                if (c != '')
                    domain[i] = 'xn--' + c;
            }
        }
    }


    domain = domain.join('.');

    return domain;




}
//конец function idndomain


function urlEncode(string, escaped) {
    //https://support.google.com/adwords/answer/6305348
    //https://gist.github.com/jarrodbell/1214016
    function hex(code) {
        var hex = code.toString(16).toUpperCase();
        if (hex.length < 2) {
            hex = 0 + hex;
        }
        return '%' + hex;
    }

    if (!string) {
        return '';
    }

    string = string + '';


    //стандартный набор
    //		var reserved_chars = /[ \r\n!*"'();:@&=+$,\/?%#\[\]<>{}|`^\\\u0080-\uffff]/;



    if (escaped == 'beforequest') {
        //зачем перед отправкой еще раз кодировать: например на такой строке гугл fetch выдаст ошибку
        //http://technotest.com.ua/dlya-zernovoy-promyshlennosti/laboratornye-melnicy/laboratornaya-melnica-s-vodyanym-ohlazhdeniem-lm7020.html?utm_source=yandex&utm_medium=cpc&utm_campaign=cid|{campaign_id}|{source_type}&utm_content=gid|{gbid}|aid|{ad_id}|{phrase_id}_{retargeting_id}&utm_term={keyword}'
        //http://technotest.com.ua/dlya-zernovoy-promyshlennosti/laboratornye-melnicy/laboratornaya-melnica-s-vodyanym-ohlazhdeniem-lm7020.html?utm_source=yandex&utm_medium=cpc&utm_campaign=cid||&utm_content=gid||aid||_&utm_term=';
        //поэтому перед запросом еще раз обрабатываем url (но без дополнительной перекодировки кодированных символов, поэтому убрано %)
        //все исключенные символы: &=+/?%#:  - так не будет коряжится основной url, но проблемы уйдут
        var reserved_chars = /[ \r\n!*"'();@$,\[\]<>{}|`^\\\u0080-\uffff]/
    } else if (escaped == 'beforequest2') {
	//добавили &=?+ в кодирование beforequest, что бы полностью url можно было передавать в качестве параметра
	//+ обязательно тоже добавлен, иначе если в url есть + то заменит при раскодировке на пробелы
        var reserved_chars = /[ \r\n!*"'();@&=?+$,\[\]<>{}|`^\\\u0080-\uffff]/
    } else if (escaped) {
        //{escapedlpurl}
        //			var reserved_chars = /[:/?=%"#]/
        //чего не перекодирует escapedlpurl реально, а не по документации '() все остальное кодирует
        //еще у него глюк при перекодировке
        var reserved_chars = /[ \r\n!*";:@&=+$,\/?%#\[\]<>{}|`^\\\u0080-\uffff]/
    } else {
        //{lpurl}
        //			var reserved_chars = /[%?="#\t' ]/
        //чего не перекодирует lpurl реально, а не по документации :,/() все остальное кодирует
        //еще у него глюк при перекодировке
        var reserved_chars = /[ \r\n!*"';@&=+$?%#\[\]<>{}|`^\\\u0080-\uffff]/
    }

    var str_len = string.length,
        i, string_arr = string.split(''),
        c;

    for (i = 0; i < str_len; i++) {
        if (c = string_arr[i].match(reserved_chars)) {
            c = c[0].charCodeAt(0);

            if (c < 128) {
                string_arr[i] = hex(c);
            } else if (c < 2048) {
                string_arr[i] = hex(192 + (c >> 6)) + hex(128 + (c & 63));
            } else if (c < 65536) {
                string_arr[i] = hex(224 + (c >> 12)) + hex(128 + ((c >> 6) & 63)) + hex(128 + (c & 63));
            } else if (c < 2097152) {
                string_arr[i] = hex(240 + (c >> 18)) + hex(128 + ((c >> 12) & 63)) + hex(128 + ((c >> 6) & 63)) + hex(128 + (c & 63));
            }
        }
    }
    return string_arr.join('');
};


function urlDecode(string) {
    //https://gist.github.com/jarrodbell/1214016
	if (!string) {
		return '';
	}
	return string.replace(/%[a-fA-F0-9]{2}/ig, function (match) {
		return String.fromCharCode(parseInt(match.replace('%', ''), 16));
	});
};


var ScanCreateFeedsGlobalVars = {errors: "", stopscan: 0};


function ScanCreateFeeds(OneSiteConfig,feedsAlreadyChecked,foundURLHASHdublicates) {

	var didExitEarly = false;


	ScanCreateFeedsGlobalVars = {errors: "", stopscan: 0};

	//если нужн трассировка
//	ScanCreateFeedsGlobalVars.debug = 1;



			//feedsAlreadyChecked['config']
			//feedsAlreadyChecked['scanids']
			//feedsAlreadyChecked['scannId']
			//feedsAlreadyChecked['CURfeedsIds']
			//feedsAlreadyChecked['scanned']

//	var OneSiteConfig = feedsAlreadyChecked['config'][feedsAlreadyChecked['scannId']['IdNum']];



	ScanCreateFeedsGlobalVars.SITE = OneSiteConfig['SITE'];


	ScanCreateFeedsGlobalVars.CHARSET = OneSiteConfig['CHARSET'];


	ScanCreateFeedsGlobalVars.seed = OneSiteConfig['seed'];


	if (OneSiteConfig.hasOwnProperty('skip_query'))
		ScanCreateFeedsGlobalVars.skip_query = OneSiteConfig['skip_query'];


	if (OneSiteConfig.hasOwnProperty('only_query'))
		ScanCreateFeedsGlobalVars.only_query = OneSiteConfig['only_query'];


	if (OneSiteConfig.hasOwnProperty('scanFun'))
		ScanCreateFeedsGlobalVars.scanFun = OneSiteConfig['scanFun'];


	if (OneSiteConfig.hasOwnProperty('skip_errors'))
		ScanCreateFeedsGlobalVars.skip_errors = OneSiteConfig['skip_errors'];

	if (OneSiteConfig.hasOwnProperty('repeat_errors'))
		ScanCreateFeedsGlobalVars.repeat_errors = OneSiteConfig['repeat_errors'];


	if (OneSiteConfig.hasOwnProperty('valid_scheme'))
		ScanCreateFeedsGlobalVars.valid_scheme = OneSiteConfig['valid_scheme'];


	if (OneSiteConfig.hasOwnProperty('REQUEST_DELAY'))
		ScanCreateFeedsGlobalVars.REQUEST_DELAY=OneSiteConfig['REQUEST_DELAY'];



	if (OneSiteConfig.hasOwnProperty('IGNORE_EMPTY_CONTENT_TYPE'))
		ScanCreateFeedsGlobalVars.IGNORE_EMPTY_CONTENT_TYPE = OneSiteConfig['IGNORE_EMPTY_CONTENT_TYPE'];


	if (OneSiteConfig.hasOwnProperty('MAX_TRIES'))
		ScanCreateFeedsGlobalVars.MAX_TRIES = OneSiteConfig['MAX_TRIES'];


//нет такого параметра у сайта, есть у фидов
//	if (OneSiteConfig.hasOwnProperty('MAX_ADD_URLS'))
//		ScanCreateFeedsGlobalVars.MAX_ADD_URLS=OneSiteConfig['MAX_ADD_URLS'];

	if (OneSiteConfig.hasOwnProperty('MAX_CHECKED_URLS'))
		ScanCreateFeedsGlobalVars.MAX_CHECKED_URLS=OneSiteConfig['MAX_CHECKED_URLS'];


	if (OneSiteConfig.no_urlsearchtoLowerCase)
		ScanCreateFeedsGlobalVars.no_urlsearchtoLowerCase=1;



	//перед startInitFun
	ScanCreateFeedsGlobalVars.init = {};
	ScanCreateFeedsGlobalVars.init.OneSiteConfig = OneSiteConfig;


	//preparestartFeed();
	preparecheckFeeds();


	if ((!ScanCreateFeedsGlobalVars.errors)&&OneSiteConfig.hasOwnProperty('startInitFun')) {

		try {
	        	OneSiteConfig.startInitFun(1);
			if (ScanCreateFeedsGlobalVars.stopscan) {
				console.log('All feeds added MAX_ADD_URLS num urls, stop Scan and exit');
				didExitEarly = false;
			}


		} catch (e) {
			ScanCreateFeedsGlobalVars.errors += 'bad function startInitFun:' + e.message + "\n";
			console.log(ScanCreateFeedsGlobalVars.errors);
		}

	}


	if ((!ScanCreateFeedsGlobalVars.errors)&&(!ScanCreateFeedsGlobalVars.stopscan)) {

		//didExitEarly = startFeed(feedsAlreadyChecked,foundURLHASHdublicates,IDName);
		didExitEarly = checkFeeds(OneSiteConfig);

		if ((!ScanCreateFeedsGlobalVars.errors)&&OneSiteConfig.hasOwnProperty('endInitFun')) {

			try {

				if (ScanCreateFeedsGlobalVars.stopscan) {
					var stscex = 1;
				} else {
					var stscex = 0;
				}

				//что бы понять что это последний шаг, передаем !didExitEarly (флаг последнего шага EndScanFlg)
	        		OneSiteConfig.endInitFun(1);
				if ((!stscex)&&ScanCreateFeedsGlobalVars.stopscan) {
					//stopscan появился в endInitFun (до этого не было)
					console.log('All feeds added MAX_ADD_URLS num urls, stop Scan and exit');
					didExitEarly = false;
				}

			} catch (e) {
				ScanCreateFeedsGlobalVars.errors += 'bad function endInitFun:' + e.message + "\n";
				console.log(ScanCreateFeedsGlobalVars.errors);
			}

		}


	} else {
		//есть stopscan в startInitFun или ошибка, не запускаем endInitFun, запускаем startFeed до создания начальных данных (что бы уменьшить вероятность возникновения ошибок)
		// для stopscan didExitEarly уже установлен, для ошибки didExitEarly не имеет значения
		//в обоих случаях didExitEarly все равно равен false (еще нигде не мог изменится)
		//startFeed(feedsAlreadyChecked,foundURLHASHdublicates,IDName);
		checkFeeds(OneSiteConfig);

	}


	if (ScanCreateFeedsGlobalVars.errors) {
		alert(ScanCreateFeedsGlobalVars.errors)
	}


	return didExitEarly;

}


function SkipParseURL(url,OneFeedConfig) {
	var skip_url = OneFeedConfig.skip_parse;
	var only_url = OneFeedConfig.only_parse;
	var parseUrlFun = OneFeedConfig.parseUrlFun;

	if ((skip_url && skip_url.length) || (only_url && only_url.length) || parseUrlFun) {


		var firsts = url.substr(0, 1);
		if (firsts != '/') {
			url = '/' + url;
		}


		if (skip_url && skip_url.length) {
			for (var v in skip_url) {
				if (skip_url[v] == '/') {
					if (url == '/') return true;
				} else {
					if (url.substr(0, skip_url[v].length) == skip_url[v]) return true; // Skip this URL
				}
			}
		}

		if (only_url && only_url.length) {
			var exist = 0;
			for (var v in only_url) {
				if (only_url[v] == '/') {
					if (url == '/') exist = 1;
				} else {
					if (url.substr(0, only_url[v].length) == only_url[v]) exist = 1; // Skip this URL
				}
			}
			if (!exist)
				return true;
		}

		if (parseUrlFun) {
	            try {
	                var noskip = parseUrlFun(url);
			if (!noskip)
				return true;
        	    } catch (e) {
				ScanCreateFeedsGlobalVars.errors += 'bad function parseUrlFun:' + e.message + "\n";
				//alert(ScanCreateFeedsGlobalVars.errors);
	            }
		}


	}

	return false;
}


function readConfFile(fileName, folder) {

	try {

/*
		var fileIter = folder.getFilesByName(fileName);
		if (!fileIter.hasNext()) {
			//нет файла
			return [null, 1, 'File: ' + fileName + ' not found'];
		}
		var fileData = fileIter.next().getBlob().getDataAsString();

*/
		var fileData = document.getElementById('checkwin143526733').value;

		if (fileData) {
			var ret = eval('(' + fileData + ')');
			return [ret, 0];
		} else {
			//файл пустой
			return [null, 2, 'File: ' + fileName + ' is empty'];
		}
	} catch (e) {
		//ошибка чтения файла
		return [null, 3, 'Could not read file: ' + fileName + ' Error: ' + e.message];
	}

}


function readFeedConfig(IDName, mainfolder, domainsfolder, filesfolder) {
	//читаем и записываем конфиг фидов



	//читаем файл конфигурации и проверяем ошибки


	var confFile = IDName + '.merchantfeed.txt';

	var FeedConfig = readConfFile(confFile, mainfolder);
	var confFolderName = 'context_settings/' + confFile;
	var confFolderName2 = '/' + confFile;
	var confFolderName3 = '/' + confFile;

/*
	if ((!FeedConfig[0]) && (FeedConfig[1] == 1)) {
		//если нет файла в основной папке, ищем в папке domains
		FeedConfig = readConfFile(confFile, domainsfolder);
		confFolderName = 'context_settings/callhunter/domains/' + confFile;
		confFolderName2 = "\n" + '/callhunter/domains/' + confFile;
		confFolderName3 += "\n" + '/callhunter/domains/' + confFile;
	}
*/

	var CSVwarnings = '';

	var reterr = 0;

	if (FeedConfig[1]) {
		//описание ошибки
		var readRes = FeedConfig[2] + "\n" + confFolderName3;
		reterr = 1;
	} else {
		var readRes = "Read Config File Ok" + "\n" + confFolderName2;
	}



	if ((!reterr) && (FeedConfig[1] || (!FeedConfig[0]) || (typeof FeedConfig[0] != 'object') || (!FeedConfig[0].length))) {
		//заодно проверили что это обычный массив (length) (хотя может быть дата)
		readRes += "\n" + "Config is empty";
		reterr = 1;
	}

	//https://learn.javascript.ru/class-instanceof - проверка типа массива ([object Array] и [object Object])
	//	var toString = {}.toString;


	if ((!reterr) && ({}.toString.call(FeedConfig[0]) !== '[object Array]')) {
		readRes += "\n" + "Config is not Array (first level)";
		reterr = 1;
	}


	//допустимые расширения файлов фидов
	var correctfeedExt = ['.html', '.ga.xml', '.ga.txt', '.ga.tsv', '.yml.xml', '.yrl.xml', '.ycar.xml', '.ya.tsv'];

	//получаем текущий массив перебора фидов
	var scansites = {};
	var scanids = {};
	var zipnames = {};
	var searchADids = {};
	if (!reterr) {
		for (var i in FeedConfig[0]) {
			//настройки сканирования и парсинга одного сайта
			if ({}.toString.call(FeedConfig[0][i]) !== '[object Object]') {
				//должны быть только хеши у сайтов
				readRes += "\n" + "Config Array element number " + i + " is not Hash";
				reterr = 1;
				break;
			}


//этот фрагмент другой в скрипте мерчанта
if (FeedConfig[0][i]['soft_shutdown']) {
	//параматр должен быть выключен в скрипте тестового парсинга
	readRes += "\n" + "Config Array element number " + i + " have active parameter 'soft_shutdown', turn off if for this parsing test";
	reterr = 1;
	break;
}

			if ((!FeedConfig[0][i]['SITE']) || (typeof FeedConfig[0][i]['SITE'] != 'string')) {
				//должна быть ссылка у сайта
				readRes += "\n" + "Config Array element number " + i + " is not have parameter SITE";
				reterr = 1;
				break;
			}
			if (/\s/.test(FeedConfig[0][i]['SITE'])) {
				//не должно быть пробелов в ссылке
				readRes += "\n" + "Config Array element number " + i + " have space in parameter SITE";
				reterr = 1;
				break;
			}
			var parsed_url = parse_url(FeedConfig[0][i]['SITE']);
			if ((!parsed_url['hostname']) || (!parsed_url['protocol'])) {
				//должны быть protocol и hostname у сайта
				readRes += "\n" + "Config Array element number " + i + " is not have correct parameter SITE";
				reterr = 1;
				break;
			}
			//уже сделано в parse_url
			//parsed_url['hostname'] = parsed_url['hostname'].toLowerCase();


/*
не разрешено повторять сайты
могут возникнуть неучтенные коллизии URL ID (каждый скан одного сайта будет работать по разной схеме и пропустит часть дубликатов из другого скана)
	это произойдет так, как мы не храним базу URL ID между сканами
по этой же причине нужно хранить на время скана ID всех URL (точнее все уникальные ID),
а массив коллизии URL ID нужно хранить всегда (для заданного seed)

если же все же потом разрешим (если хранить базу URL), то в настройках Id скана:
если несколько сканов для одного сайта, то Id нельзя оставлять пустым (что бы не было дублирования Id)

*/
			if (!scansites.hasOwnProperty(parsed_url['hostname'])) {
				scansites[parsed_url['hostname']] = FeedConfig[0][i]['SITE'];
			} else {
				//не должны повторяться сайты
				readRes += "\n" + "Config Array element number " + i + " have dublicate hostname in parameter SITE";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}

			var seed = FeedConfig[0][i]['seed'];
			if (!seed) {
//				seed = 0;
				seed = undefined;
			} else if (isInteger(seed)) {
				seed = parseInt(seed);
			} else {
				//некорректный параметр seed
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter seed";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}
			FeedConfig[0][i]['seed'] = seed;




			var timeHoursHash = {};
			var timeHoursNotCorrect = 0;
			//не может быть в итоге пустой массив
			//если установлен test то не запускать никогда, кроме режима просмотра
			//если установлено в параметре пусто, 0 или нет параметра, то 0 часов
			if (!FeedConfig[0][i].hasOwnProperty('timeHours')) {
				var timeHours = ['0'];
			} else if (!FeedConfig[0][i]['timeHours']) {
				var timeHours = ['0'];
			} else if (typeof FeedConfig[0][i]['timeHours'] != 'string') {
				//некорректный параметер
				timeHoursNotCorrect = 1;
				var timeHours = [];
			} else if (FeedConfig[0][i]['timeHours'] === 'test') {
				//test
				var timeHours = ['test'];
			} else {
				var timeHours = FeedConfig[0][i]['timeHours'].split(/[\s\,]+/);
			}
			if (timeHoursNotCorrect) {
				//некорректный параметр timeHours
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter timeHours";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}
			for (var ii = 0; ii < timeHours.length; ii++) {
				if (isInteger(timeHours[ii])) {
					timeHours[ii] = parseInt(timeHours[ii]);
					timeHoursHash[timeHours[ii]] = 1;
					if ((timeHours[ii] < 0) || (timeHours[ii] > 23)) {
						//некорректный параметр timeHours
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter timeHours " + timeHours[ii];
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					}
				} else if ((timeHours[ii] == 'test') && (timeHours.length == 1)) {
					timeHoursHash[timeHours[ii]] = 1;
				} else {
					//некорректный параметр timeHours
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter timeHours " + timeHours[ii];
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}
			}
			//сначала инициализируем немассивов, что бы знать, когда фиды заполнять начнут этот параметр
			FeedConfig[0][i]['timeHours'] = [];
			FeedConfig[0][i]['timeHoursHash'] = {};




			var startDaysHash = {};
			var startDaysNotCorrect = 0;
			//может быть в итоге пустой массив (означает что скан при каждом запуске)
			//если установлено в параметре пусто, 0 или нет параметра, то скан при каждом запуске
			if (!FeedConfig[0][i].hasOwnProperty('startDays')) {
				var startDays = [];
			} else if (!FeedConfig[0][i]['startDays']) {
				var startDays = [];
			} else if (typeof FeedConfig[0][i]['startDays'] != 'string') {
				//некорректный параметер
				startDaysNotCorrect = 1;
				var startDays = [];
			} else {
				var startDays = FeedConfig[0][i]['startDays'].split(/[\s\,]+/);
			}
			if (startDaysNotCorrect) {
				//некорректный параметр startDays
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter startDays";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}
			for (var ii = 0; ii < startDays.length; ii++) {
				if (isInteger(startDays[ii])) {
					startDays[ii] = parseInt(startDays[ii]);
					startDaysHash[startDays[ii]] = 1;
					if ((startDays[ii] < 1) || (startDays[ii] > 31)) {
						//некорректный параметр startDays
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter startDays " + startDays[ii];
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					}
				} else {
					//некорректный параметр startDays
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter startDays";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}
			}
			//сначала инициализируем немассивов, что бы знать, когда фиды заполнять начнут этот параметр
			FeedConfig[0][i]['startDays'] = '';
			FeedConfig[0][i]['startDaysHash'] = '';


			if (FeedConfig[0][i]['repeaterUrl'] && (typeof FeedConfig[0][i]['repeaterUrl'] != 'string')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter repeaterUrl";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			} else if (FeedConfig[0][i]['repeaterUrl']) {
				if (!/\/$/.test(FeedConfig[0][i]['repeaterUrl'])) {
					FeedConfig[0][i]['repeaterUrl'] += '/';
				}
				FeedConfig[0][i]['repeaterUrl'] += 'srvrepeat.php';
				var parce_repeater = parse_url(FeedConfig[0][i]['repeaterUrl']);
				if ((!parce_repeater)||(!parce_repeater['host'])||(!parce_repeater['protocol'])||(!parce_repeater['pathname'])||parce_repeater['search']||parce_repeater['hash']) {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter repeaterUrl";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {
					FeedConfig[0][i]['repeaterUrl'] += '?url=';
				}
			}


			if ((FeedConfig[0][i].hasOwnProperty('utmDropShip')) && (typeof FeedConfig[0][i]['utmDropShip'] != 'string')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter utmDropShip";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}

			if ((FeedConfig[0][i].hasOwnProperty('title')) && (typeof FeedConfig[0][i]['title'] != 'string')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter title";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if ((FeedConfig[0][i].hasOwnProperty('description')) && (typeof FeedConfig[0][i]['description'] != 'string')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter description";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if ((!FeedConfig[0][i].hasOwnProperty('CHARSET')) || (typeof FeedConfig[0][i]['CHARSET'] != 'string')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CHARSET";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}



			if ((FeedConfig[0][i].hasOwnProperty('scanFun')) && (typeof FeedConfig[0][i]['scanFun'] != 'function')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter scanFun";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if ((FeedConfig[0][i].hasOwnProperty('startInitFun')) && (typeof FeedConfig[0][i]['startInitFun'] != 'function')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter startInitFun";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if ((FeedConfig[0][i].hasOwnProperty('endInitFun')) && (typeof FeedConfig[0][i]['endInitFun'] != 'function')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter endInitFun";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if (FeedConfig[0][i].hasOwnProperty('firstNewPages')) {
				if ((typeof FeedConfig[0][i]['firstNewPages'] != 'number')||(FeedConfig[0][i]['firstNewPages'] < 0)) {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter firstNewPages";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}
			}

			if (FeedConfig[0][i].hasOwnProperty('reverseNewUrls')) {
				if ((typeof FeedConfig[0][i]['reverseNewUrls'] != 'number')||(FeedConfig[0][i]['reverseNewUrls'] < 0)) {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter reverseNewUrls";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}
			}

			if (FeedConfig[0][i].hasOwnProperty('Debug_On')) {
				if ((typeof FeedConfig[0][i]['Debug_On'] != 'number')||(FeedConfig[0][i]['Debug_On'] < 0)) {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter Debug_On";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}
			}


			if ((FeedConfig[0][i].hasOwnProperty('No_Critical_Errors')) && ((typeof FeedConfig[0][i]['No_Critical_Errors'] != 'number')||(FeedConfig[0][i]['No_Critical_Errors'] < 0)||(FeedConfig[0][i]['No_Critical_Errors'] > 5))) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter No_Critical_Errors";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if ((FeedConfig[0][i].hasOwnProperty('Write_emptyFeed_File')) && ((typeof FeedConfig[0][i]['Write_emptyFeed_File'] != 'number')||(FeedConfig[0][i]['Write_emptyFeed_File'] < 0)||(FeedConfig[0][i]['Write_emptyFeed_File'] > 2))) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter Write_emptyFeed_File";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if (FeedConfig[0][i].hasOwnProperty('CSVSITE')) {
				if ({}.toString.call(FeedConfig[0][i]['CSVSITE']) !== '[object Array]') {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {
					FeedConfig[0][i]['CSVConfig'] = {}

					var CSVSITE = FeedConfig[0][i]['CSVSITE'];
					//проверяем первые пять элементов массива CSVSITE

					if ((typeof CSVSITE[0] !== 'number')||(CSVSITE[0] < 0)||(CSVSITE[0] > 1)) {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[0] (parseUrls)";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					} else {
						FeedConfig[0][i]['CSVConfig']['parseUrls'] = CSVSITE[0];
					}

					if ((!CSVSITE[1])||({}.toString.call(CSVSITE[1]) !== '[object Array]')) {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[1] (skip_errors)";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					} else {
						FeedConfig[0][i]['CSVConfig']['skip_errors'] = CSVSITE[1];
					}


					if ((typeof CSVSITE[2] !== 'number')||(CSVSITE[2] < 0)) {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[2] (MAX_TRIES)";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					} else {
						FeedConfig[0][i]['CSVConfig']['MAX_TRIES'] = CSVSITE[2];
					}

					if ((typeof CSVSITE[3] !== 'number')||(CSVSITE[3] < 0)) {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[3] (REQUEST_DELAY)";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					} else {
						FeedConfig[0][i]['CSVConfig']['REQUEST_DELAY'] = CSVSITE[3];
					}


					if (typeof CSVSITE[4] !== 'string') {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[4] (repeaterUrl)";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					} else {

						if (CSVSITE[4]) {
							if (!/\/$/.test(CSVSITE[4])) {
								CSVSITE[4] += '/';
							}
							CSVSITE[4] += 'srvrepeat.php';
							var parce_repeater = parse_url(CSVSITE[4]);
							if ((!parce_repeater)||(!parce_repeater['host'])||(!parce_repeater['protocol'])||(!parce_repeater['pathname'])||parce_repeater['search']||parce_repeater['hash']) {
								//некорректный параметер
								readRes += "\n" + "Config Array element number " + i + " is not have correct parameter CSVSITE[4] (repeaterUrl)";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;
							} else {
								CSVSITE[4] += '?url=';
								FeedConfig[0][i]['CSVConfig']['repeaterUrl'] = CSVSITE[4];

							}
						}


					}



					//проверяем остальные элементы массива CSVSITE (массив массивов ссылок)
					if (CSVSITE.length < 6) {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[5] (Url Array)";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					} else {

						FeedConfig[0][i]['CSVConfig']['CSVurls'] = []

						//перебираем массивы ссылок

						for (var ii = 5; ii < CSVSITE.length; ii++) {
							if (({}.toString.call(CSVSITE[ii]) !== '[object Array]')||(CSVSITE[ii].length != 7)) {
								//некорректный параметер
								readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "] (Urls Array)";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;
							} else {


								//попали в массив отдельной ссылки

								var num = FeedConfig[0][i]['CSVConfig']['CSVurls'].length;

								FeedConfig[0][i]['CSVConfig']['CSVurls'][num] = {};

								var CSVurlHash = FeedConfig[0][i]['CSVConfig']['CSVurls'][num];

								//хеш для отслеживания дубликатов ссылок
								var CSVurlsDubl = {}

								//перебираем параметры ссылки
								//проверяем первые семь элементов в массиве каждой ссылки (больше там нет и меньше тоже)


								if ((typeof CSVSITE[ii][0] !== 'string')||(!CSVSITE[ii][0])) {
									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][0] (CSVUrl in Url Array)";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;
								} else {

									//это ссылка

									CSVurlHash['url'] = CSVSITE[ii][0];

									if (/\s/.test(CSVurlHash['url'])) {
										//не должно быть пробелов в ссылке
										readRes += "\n" + "Config Array element number " + i + " have space in parameter CSVSITE[" + ii + "][0] (CSVUrl in Url Array)";
										reterr = 1;
										//можно идти дальше, что бы весь конфиг проверить
										//			break;
									}
									var parsed_csvurl = parse_url(CSVurlHash['url']);
									if ((!parsed_csvurl['hostname']) || (!parsed_csvurl['protocol'])) {
										//должны быть protocol и hostname у сайта
										readRes += "\n" + "Config Array element number " + i + " is not have correct parameter CSVSITE[" + ii + "][0] (CSVUrl in Url Array)";
										reterr = 1;
										//можно идти дальше, что бы весь конфиг проверить
										//			break;
									}


									if (CSVurlsDubl[CSVurlHash['url']]) {
										CSVwarnings += "\n" + "Config Array element number " + i + " have dublicate URL in parameter CSVSITE[" + ii + "][0] (CSVUrl in Url Array)";
									/*
										//проверяем на дубликаты
										readRes += "\n" + "Config Array element number " + i + " have dublicate URL in parameter CSVSITE[" + ii + "][0] (CSVUrl in Url Array)";
										reterr = 1;
										//можно идти дальше, что бы весь конфиг проверить
										//			break;
									*/
									} else {
										CSVurlsDubl[CSVurlHash['url']] = 1
									}


								}


								if ((typeof CSVSITE[ii][1] !== 'string')||(!CSVSITE[ii][1])) {
									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][1] (CHARSET in Url Array)";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;
								} else {
									//это чарсет
									CSVurlHash['CHARSET'] = CSVSITE[ii][1];
								}



								var xml_flg = 0;

								if ({}.toString.call(CSVSITE[ii][6]) !== '[object Object]') {
									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][6] (csvParserConfig in Url Array)";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;
								} else {

									if (CSVSITE[ii][6].hasOwnProperty('xml')) {

										xml_flg = 1;

										if ({}.toString.call(CSVSITE[ii][6]['xml']) !== '[object Array]') {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][6] (xml in csvParserConfig in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										} else if (!CSVSITE[ii][6]['xml'].length) {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][6] (xml in csvParserConfig in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										} else {
											for (var iii = 0; iii < CSVSITE[ii][6]['xml'].length; iii++) {
												if ((typeof CSVSITE[ii][6]['xml'][iii] != 'string')||(!CSVSITE[ii][6]['xml'][iii])) {
													//некорректный параметер
													readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][6].xml[" + iii + "] (in xml in csvParserConfig in Url Array)";
													reterr = 1;
													//можно идти дальше, что бы весь конфиг проверить
													//			break;
												}
											}
										}
									}


									//это csvParserConfig
									CSVurlHash['csvParserConfig'] = CSVSITE[ii][6];

								}




								if (({}.toString.call(CSVSITE[ii][2]) !== '[object Array]')||(CSVSITE[ii][2].length > 2)) {
									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][2] (url_column_replace in Url Array)";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;
								} else {
									//это url_column_replace
									CSVurlHash['url_column_replace'] = CSVSITE[ii][2];


									if (xml_flg) {

										if ({}.toString.call(CSVSITE[ii][2][0]) !== '[object Array]') {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][2][0] (url_column_replace in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										} else if (!CSVSITE[ii][2][0].length) {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][2][0] (url_column_replace in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										} else {
											for (var iii = 0; iii < CSVSITE[ii][2][0].length; iii++) {
												if ((typeof CSVSITE[ii][2][0][iii] != 'string')||(!CSVSITE[ii][2][0][iii])) {
													//некорректный параметер
													readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][2][0][" + iii + "] (url_column_replace in Url Array)";
													reterr = 1;
													//можно идти дальше, что бы весь конфиг проверить
													//			break;
												}
											}
										}



									} else {

										if ((typeof CSVSITE[ii][2][0] !== 'number')||(CSVSITE[ii][2][0] < 0)) {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][2][0] (url_column_replace in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										}


									}




									if (typeof CSVSITE[ii][2][1] == 'undefined') {
										//обрезаем массив url_column_replace, оставляем в нем только первый элемент, если есть undefined второй
										CSVSITE[ii][2].length = 1
									} else {
										if (typeof CSVSITE[ii][2][1] !== 'function') {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][2][1] (url_column_replace in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										}
									}

								}


								if (({}.toString.call(CSVSITE[ii][3]) !== '[object Array]')||(CSVSITE[ii][3].length > 2)||(CSVSITE[ii][3].length == 1)) {
									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][3] (onlySeparator in Url Array)";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;
								} else {
									//это onlySeparator
									CSVurlHash['onlySeparator'] = CSVSITE[ii][3];


									if (CSVSITE[ii][3].length) {


										if (xml_flg) {

											if ({}.toString.call(CSVSITE[ii][3][0]) !== '[object Array]') {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][3][0] (onlySeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
											} else if (!CSVSITE[ii][3][0].length) {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][3][0] (onlySeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
											} else {
												for (var iii = 0; iii < CSVSITE[ii][3][0].length; iii++) {
													if ((typeof CSVSITE[ii][3][0][iii] != 'string')||(!CSVSITE[ii][3][0][iii])) {
														//некорректный параметер
														readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][3][0][" + iii + "] (onlySeparator in Url Array)";
														reterr = 1;
														//можно идти дальше, что бы весь конфиг проверить
														//			break;
													}
												}
											}



										} else {

											if ((typeof CSVSITE[ii][3][0] !== 'number')||(CSVSITE[ii][3][0] < 0)) {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][3][0] (onlySeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
							
											}

										}


										if (({}.toString.call(CSVSITE[ii][3][1]) !== '[object Array]')||(!CSVSITE[ii][3][1].length)) {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][3][1] (onlySeparator in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										} else {


											var badseparators = 0;


											for (var iii = 0; iii < CSVSITE[ii][3][1].length; iii++) {
												if (CSVSITE[ii][3][1][iii]) {
													var typeSepr = {}.toString.call(CSVSITE[ii][3][1][iii]);
													if (!((typeSepr == '[object String]')||(typeSepr == '[object RegExp]'))) {
														badseparators = 1;
														break;
													}
												} else {
													badseparators = 1;
													break;
												}
                                                                                	}
											if (badseparators) {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][3][1] (onlySeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
											}


										}



									}




								}






								if (({}.toString.call(CSVSITE[ii][4]) !== '[object Array]')||(CSVSITE[ii][4].length > 2)||(CSVSITE[ii][4].length == 1)) {
									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][4] (skipSeparator in Url Array)";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;
								} else {
									//это skipSeparator
									CSVurlHash['skipSeparator'] = CSVSITE[ii][4];


									if (CSVSITE[ii][4].length) {


										if (xml_flg) {

											if ({}.toString.call(CSVSITE[ii][4][0]) !== '[object Array]') {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][4][0] (skipSeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
											} else if (!CSVSITE[ii][4][0].length) {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][4][0] (skipSeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
											} else {
												for (var iii = 0; iii < CSVSITE[ii][4][0].length; iii++) {
													if ((typeof CSVSITE[ii][4][0][iii] != 'string')||(!CSVSITE[ii][4][0][iii])) {
														//некорректный параметер
														readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][4][0][" + iii + "] (skipSeparator in Url Array)";
														reterr = 1;
														//можно идти дальше, что бы весь конфиг проверить
														//			break;
													}
												}
											}



										} else {

											if ((typeof CSVSITE[ii][4][0] !== 'number')||(CSVSITE[ii][4][0] < 0)) {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][4][0] (skipSeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
							
											}

										}


										if (({}.toString.call(CSVSITE[ii][4][1]) !== '[object Array]')||(!CSVSITE[ii][4][1].length)) {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][4][1] (skipSeparator in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										} else {


											var badseparators = 0;


											for (var iii = 0; iii < CSVSITE[ii][4][1].length; iii++) {
												if (CSVSITE[ii][4][1][iii]) {
													var typeSepr = {}.toString.call(CSVSITE[ii][4][1][iii]);
													if (!((typeSepr == '[object String]')||(typeSepr == '[object RegExp]'))) {
														badseparators = 1;
														break;
													}
												} else {
													badseparators = 1;
													break;
												}
                                                                                	}
											if (badseparators) {
												//некорректный параметер
												readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][4][1] (skipSeparator in Url Array)";
												reterr = 1;
												//можно идти дальше, что бы весь конфиг проверить
												//			break;
											}


										}



									}




								}


								if (({}.toString.call(CSVSITE[ii][5]) !== '[object Array]')||(CSVSITE[ii][5].length > 2)) {

									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][5] (StartEndLines in Url Array)";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;


								} else {


									//это StartEndLines
									CSVurlHash['StartEndLines'] = CSVSITE[ii][5];

									if (!CSVSITE[ii][5].length) {
										//делаем всегда 2 элемента
										CSVurlHash['StartEndLines'] = [0,0];
									} else {


										if ((typeof CSVSITE[ii][5][0] !== 'number')||(CSVSITE[ii][5][0] < 0)) {
											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][5][0] (StartEndLines in Url Array)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;

										} else {


											if (CSVSITE[ii][5].length == 1) {
												//делаем всегда 2 элемента
												CSVurlHash['StartEndLines'] = [CSVSITE[ii][5][0],0];
											} else {
					
												if ((typeof CSVSITE[ii][5][1] !== 'number')||(CSVSITE[ii][5][1] < 0)||(CSVSITE[ii][5][0] > CSVSITE[ii][5][1])) {
													//некорректный параметер
													readRes += "\n" + "Config Array element number " + i + " have incorrect parameter CSVSITE[" + ii + "][5][0] (StartEndLines in Url Array)";
													reterr = 1;
													//можно идти дальше, что бы весь конфиг проверить
													//			break;

												}


											}
										}


									}


								}




							}
						}
					}

					//сформировали хеш
					//['CSVConfig']['parseUrls']
					//['CSVConfig']['skip_errors']
					//['CSVConfig']['MAX_TRIES']
					//['CSVConfig']['REQUEST_DELAY']
					//['CSVConfig']['repeaterUrl']
					//['CSVConfig']['CSVurls'].length - массив ссылок и их параметров 
					//['CSVConfig']['CSVurls'][i]['url']
					//['CSVConfig']['CSVurls'][i]['CHARSET']
					//['CSVConfig']['CSVurls'][i]['url_column_replace'] - массив с 1.номером столбца со ссылкой и 2.функцией для анализа/вывода ссылки по данным в массиве CSV-строки
					//['CSVConfig']['CSVurls'][i]['onlySeparator']
					//['CSVConfig']['CSVurls'][i]['skipSeparator']
					//['CSVConfig']['CSVurls'][i]['StartEndLines']
					//['CSVConfig']['CSVurls'][i]['csvParserConfig']



				}
			}


			if (FeedConfig[0][i].hasOwnProperty('addscanUrls')) {

				if ({}.toString.call(FeedConfig[0][i]['addscanUrls']) !== '[object Array]') {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter addscanUrls";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else if (!FeedConfig[0][i]['addscanUrls'].length) {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter addscanUrls";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {                

					if ((FeedConfig[0][i].hasOwnProperty('valid_scheme')) && ({}.toString.call(FeedConfig[0][i]['valid_scheme']) !== '[object Array]')) {
						var valid_scheme = null;
					} else {
						var valid_scheme = FeedConfig[0][i]['valid_scheme'];
					}

					for (var ii = 0; ii < FeedConfig[0][i]['addscanUrls'].length; ii++) {
						if ((typeof FeedConfig[0][i]['addscanUrls'][ii] != 'string')||(!FeedConfig[0][i]['addscanUrls'][ii])) {
							//некорректный параметер
							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter addscanUrls[" + ii + "]";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;
						} else {

							var parsed_addscanUrl = parse_url(FeedConfig[0][i]['addscanUrls'][ii]);

							if ((!parsed_addscanUrl)||(parsed_addscanUrl['hostname']&&(parsed_addscanUrl['hostname'] !== parsed_url['hostname']))) {
								//некорректный параметер
								readRes += "\n" + "Config Array element number " + i + " have incorrect parameter addscanUrls[" + ii + "]";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;
								continue;
							}

							if (parsed_addscanUrl&&parsed_addscanUrl["protocol"]&&valid_scheme) {


								var scheme = parsed_addscanUrl["protocol"];


								if (valid_scheme && valid_scheme.length) {
									if (scheme) {
										var exists = 0;
										for (var v in valid_scheme) {
											if (valid_scheme[v] == scheme) {
												exists = 1;
											}
										}
										if (!exists) {

											//некорректный параметер
											readRes += "\n" + "Config Array element number " + i + " have incorrect parameter addscanUrls[" + ii + "]";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
											continue;

										}

									}

								}



							}

						}
					}
				}
			}


			if (FeedConfig[0][i].hasOwnProperty('scanSelectors')) {

				if ({}.toString.call(FeedConfig[0][i]['scanSelectors']) !== '[object Array]') {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter scanSelectors";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else if (!FeedConfig[0][i]['scanSelectors'].length) {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter scanSelectors";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {                
					for (var ii = 0; ii < FeedConfig[0][i]['scanSelectors'].length; ii++) {
						if ((typeof FeedConfig[0][i]['scanSelectors'][ii] != 'string')||(!FeedConfig[0][i]['scanSelectors'][ii])) {
							//некорректный параметер
							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter scanSelectors[" + ii + "]";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;
						}
					}
				}
			}




			if (FeedConfig[0][i].hasOwnProperty('onlyORskipSeparators')) {
				if ({}.toString.call(FeedConfig[0][i]['onlyORskipSeparators']) == '[object Array]') {

					if (FeedConfig[0][i]['onlyORskipSeparators'].length) {

						for (var ik = 0; ik < FeedConfig[0][i]['onlyORskipSeparators'].length; ik++) {

							if ((FeedConfig[0][i]['onlyORskipSeparators'][ik].length == 3) || (FeedConfig[0][i]['onlyORskipSeparators'][ik].length == 4))  {
								var typeSepr1 = {}.toString.call(FeedConfig[0][i]['onlyORskipSeparators'][ik][0]);
								var typeSepr2 = {}.toString.call(FeedConfig[0][i]['onlyORskipSeparators'][ik][1]);
								var typeSepr3 = {}.toString.call(FeedConfig[0][i]['onlyORskipSeparators'][ik][2]);
								var typeSepr4 = typeof FeedConfig[0][i]['onlyORskipSeparators'][ik][3];

								//2-й и 3-й массивы могут быть пустыми, второй элемент первого массива может отсутствовать, но первый элемент первого массива должен быть всегда
								//4-й элемент функция или отсутсвует
								if ( ( (typeSepr1 == '[object Array]') && (typeSepr2 == '[object Array]') && (typeSepr3 == '[object Array]') && ((typeSepr4 == 'function')||(typeSepr4 == 'undefined')) ) && FeedConfig[0][i]['onlyORskipSeparators'][ik][0].length && (FeedConfig[0][i]['onlyORskipSeparators'][ik][0].length < 3) && FeedConfig[0][i]['onlyORskipSeparators'][ik][0][0] ) {

									var badseparators = 0;

									for (var ii = 0; ii < FeedConfig[0][i]['onlyORskipSeparators'][ik][0].length; ii++) {
										var onlyORskipSeparatorSet = FeedConfig[0][i]['onlyORskipSeparators'][ik][0][ii];
										if (onlyORskipSeparatorSet) {
											if (!(({}.toString.call(onlyORskipSeparatorSet) == '[object String]')||({}.toString.call(onlyORskipSeparatorSet) == '[object RegExp]'))) {
												badseparators = 1;
												break;
											}
										} else {
											badseparators = 1;
											break;
										}
									}


									for (var ii = 0; ii < FeedConfig[0][i]['onlyORskipSeparators'][ik][1].length; ii++) {
										var onlyORskipSeparatorSet = FeedConfig[0][i]['onlyORskipSeparators'][ik][1][ii];
										if (onlyORskipSeparatorSet) {
											if (!(({}.toString.call(onlyORskipSeparatorSet) == '[object String]')||({}.toString.call(onlyORskipSeparatorSet) == '[object RegExp]'))) {
												badseparators = 1;
												break;
											}
										} else {
											badseparators = 1;
											break;
										}
									}


									for (var ii = 0; ii < FeedConfig[0][i]['onlyORskipSeparators'][ik][2].length; ii++) {
										if (onlyORskipSeparatorSet) {
											var onlyORskipSeparatorSet = FeedConfig[0][i]['onlyORskipSeparators'][ik][2][ii];
											if (!(({}.toString.call(onlyORskipSeparatorSet) == '[object String]')||({}.toString.call(onlyORskipSeparatorSet) == '[object RegExp]'))) {
												badseparators = 1;
												break;
											}
										} else {
											badseparators = 1;
											break;
										}
									}


									if (typeSepr4 == 'function') {
										try {
											FeedConfig[0][i]['onlyORskipSeparators'][ik][3]('   ');
										} catch (e) {
											badseparators = 1;
										}

									}


									if (badseparators) {
										//некорректный параметер
										readRes += "\n" + "Config Array element number " + i + " have incorrect parameter onlyORskipSeparators";
										reterr = 1;
										//можно идти дальше, что бы весь конфиг проверить
										//			break;
									} else {

										//удаляем пустые массивы 2, 3 и 4 (не забываем это при обработке!)
										if ((!FeedConfig[0][i]['onlyORskipSeparators'][ik][1].length)&&(!FeedConfig[0][i]['onlyORskipSeparators'][ik][2].length)&&(!FeedConfig[0][i]['onlyORskipSeparators'][ik][3]))
											FeedConfig[0][i]['onlyORskipSeparators'][ik].length = 1;


									}

								} else {
									//некорректный параметер
									readRes += "\n" + "Config Array element number " + i + " have incorrect parameter onlyORskipSeparators";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//			break;
								}

							} else {
								//некорректный параметер
								readRes += "\n" + "Config Array element number " + i + " have incorrect parameter onlyORskipSeparators";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;
							}

						}

					} else {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter onlyORskipSeparators";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					}
				} else {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter onlyORskipSeparators";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}
			}

			if (FeedConfig[0][i].hasOwnProperty('noSeparatorsNotScanUrls')) {

				if (FeedConfig[0][i].hasOwnProperty('onlyORskipSeparators')) {

					if (!((typeof FeedConfig[0][i]['noSeparatorsNotScanUrls'] == 'number')&&(FeedConfig[0][i]['noSeparatorsNotScanUrls'] >= 0))) {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter noSeparatorsNotScanUrls";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					}

				} else {
					readRes += "\n" + "Config Array element number " + i + " have parameter noSeparatorsNotScanUrls without parameter onlyORskipSeparators";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}

			}





			if ((FeedConfig[0][i].hasOwnProperty('valid_scheme')) && ({}.toString.call(FeedConfig[0][i]['valid_scheme']) !== '[object Array]')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter valid_scheme";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}



			if ((FeedConfig[0][i].hasOwnProperty('skip_query')) && ({}.toString.call(FeedConfig[0][i]['skip_query']) !== '[object Array]')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter skip_query";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			} else {

				var skip_url = FeedConfig[0][i].skip_query;

				if (skip_url && skip_url.length) {
					for (var v in skip_url) {
				        	var parsed_url_tmp = parse_url(skip_url[v]);
						if (typeof parsed_url_tmp != 'object') {

							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter skip_query";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;

						} else if (parsed_url_tmp.hash || parsed_url_tmp.protocol || parsed_url_tmp.hostname || parsed_url_tmp.port) {

							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter skip_query " + parsed_url_tmp;
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;

//							skip_url[v] = parsed_url.pathname + parsed_url.search;
						} else if (parsed_url_tmp.pathname.substr(0, 1) != '/') {

							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter skip_query " + parsed_url_tmp;
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;

//							skip_url[v] = '/' + parsed_url.pathname + parsed_url.search;
						}
				        }
				}



			}



			if ((FeedConfig[0][i].hasOwnProperty('only_query')) && ({}.toString.call(FeedConfig[0][i]['only_query']) !== '[object Array]')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter only_query";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			} else {


				var only_url = FeedConfig[0][i].only_query;

				if (only_url && only_url.length) {
					for (var v in only_url) {
				        	var parsed_url_tmp = parse_url(only_url[v]);
						if (typeof parsed_url_tmp != 'object') {

							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter only_query";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;


						} else if (parsed_url_tmp.hash || parsed_url_tmp.protocol || parsed_url_tmp.hostname || parsed_url_tmp.port) {

							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter only_query " + parsed_url_tmp;
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;

//							only_url[v] = parsed_url.pathname + parsed_url.search;
						} else if (parsed_url_tmp.pathname.substr(0, 1) != '/') {

							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter only_query " + parsed_url_tmp;
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;
//							only_url[v] = '/' + parsed_url.pathname + parsed_url.search;
						}
				        }
				}



			}



			if ((FeedConfig[0][i].hasOwnProperty('skip_errors')) && ({}.toString.call(FeedConfig[0][i]['skip_errors']) !== '[object Array]')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter skip_errors";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}

			if ((FeedConfig[0][i].hasOwnProperty('repeat_errors')) && ({}.toString.call(FeedConfig[0][i]['repeat_errors']) !== '[object Array]')) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter repeat_errors";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}



			FeedConfig[0][i]['shortWordsHash'] = {};
			if (FeedConfig[0][i].hasOwnProperty('shortWords')) {
				if ({}.toString.call(FeedConfig[0][i]['shortWords']) !== '[object Array]') {
					//некорректный параметер
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter shortWords";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {
					var ferr = 0;
					for (var ii = 0; ii < FeedConfig[0][i]['shortWords'].length; ii++) {
						if ((!FeedConfig[0][i]['shortWords'][ii])||(typeof FeedConfig[0][i]['shortWords'][ii] != 'string')) {
							ferr = 1;
						} else {
							FeedConfig[0][i]['shortWordsHash'][FeedConfig[0][i]['shortWords'][ii].toLowerCase()] = 1;
						}
					}
					if (ferr) {
						//некорректный параметер
						readRes += "\n" + "Config Array element number " + i + " have incorrect parameter shortWords";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//			break;
					}
				}
			}


			if ((FeedConfig[0][i].hasOwnProperty('REQUEST_DELAY')) && ((typeof FeedConfig[0][i]['REQUEST_DELAY'] != 'number') || (FeedConfig[0][i]['REQUEST_DELAY'] <= 0))) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter REQUEST_DELAY";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			if ((FeedConfig[0][i].hasOwnProperty('MAX_TRIES')) && ((typeof FeedConfig[0][i]['MAX_TRIES'] != 'number') || (FeedConfig[0][i]['MAX_TRIES'] <= 0))) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter MAX_TRIES";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}



			if ((FeedConfig[0][i].hasOwnProperty('MAX_CHECKED_URLS')) && ((typeof FeedConfig[0][i]['MAX_CHECKED_URLS'] != 'number')||(FeedConfig[0][i]['MAX_CHECKED_URLS'] < 0))) {
				//некорректный параметер
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter MAX_CHECKED_URLS";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}


			var existId = 1;
			if ((FeedConfig[0][i].hasOwnProperty('Id')) && (typeof FeedConfig[0][i]['Id'] != 'string')) {
				/*
				повторяет контроль, который внизу
							readRes += "\n" + "Config Array element number " + i +  " have incorrect parameter Id";
							reterr = 1;

				//можно идти дальше, что бы весь конфиг проверить
				//			break;
				*/
			} else {
				existId = 0;
				var Id = FeedConfig[0][i]['Id'];
				Id = ((typeof Id == 'string') && Id) ? Id.toLowerCase() : '';
				if (!Id) {
					//автоматически назначает hostname, если не указан Id
					Id = parsed_url['hostname'];
				} else if (isInteger(Id)) {
					//если id целочисленные, то перебор хеша дальше сработает непредсказуемо!!! (нужно что бы были буквы в id)
					//https://learn.javascript.ru/object-for-in
					Id = 'id' + Id;
				}
				//обязательно удаляем точки!!
				Id = Id.replace(/\.+/igm, '');
				FeedConfig[0][i]['Id'] = Id;
				if (/\s/.test(Id)) {
					//не должно быть пробелов в идентификаторе скана сайтов
					readRes += "\n" + "Config Array element number " + i + " have space in parameter Id";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}
			}
			//только английские символы, должен быть виден из сети
			FeedConfig[0][i]['Id'] = idndomain(FeedConfig[0][i]['Id']);
			FeedConfig[0][i]['Id'] = FeedConfig[0][i]['Id'].replace(/[^a-z0-9\_\-\.]+/igm, '');



			if (!FeedConfig[0][i].hasOwnProperty('priorityLev')) {
				var priorityLev = 0;
			} else if (typeof FeedConfig[0][i]['priorityLev'] != 'number') {
				//некорректный параметер
				var priorityLev = 0;
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter priorityLev";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			} else if (!isInteger(FeedConfig[0][i]['priorityLev'])) {
				//некорректный параметер
				var priorityLev = 0;
				readRes += "\n" + "Config Array element number " + i + " have incorrect parameter priorityLev";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			} else {
				var priorityLev = FeedConfig[0][i]['priorityLev'];
			}
			FeedConfig[0][i]['priorityLev'] = priorityLev;



			var mainall_fields_not_required_err = 0;


			//проверяем расширения фидов в пределах сайта
			var exts = {};

			var feedfiles = {};


			var feedsnum = 0;
			for (var j in FeedConfig[0][i]) {
				//только целые числа-строки пропускаем (фиды)
				//			if (!isInteger(j))
				//только id + целые числа пропускаем (фиды)
				if (!(((typeof j == 'string') && (/^id\d+$/.test(j)))))
					continue
				if ({}.toString.call(FeedConfig[0][i][j]) !== '[object Object]') {
					//должен быть хеш у фида
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " is not Hash";
					reterr = 1;
					break;
				}


				//добавляем признак фида, названия/ключи хеша скана, которые являются ключами хешей/идентификаторами фидов в скане
				if (!FeedConfig[0][i]['feeds']) {
					FeedConfig[0][i]['feeds'] = {};
				}
				FeedConfig[0][i]['feeds'][j] = 1;




				var ext = FeedConfig[0][i][j]['ext'];
				ext = ((typeof ext == 'string') && ext) ? ext.toLowerCase() : '';
				if (!ext) {
					//автоматически назначает j (номер фида), если не указан ext
					ext = j;
				}
				//обязательно убираем точки, что бы идентифицировать ext и другие элементы файлов (если разбить массив по количеству точек)
				ext = ext.replace(/\.+/igm, '');

				if (/\s/.test(ext)) {
					//не должно быть пробелов в ext
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have space in ext " + ext;
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}
				//только английские символы, должен быть виден из сети
				ext = idndomain(ext);
				ext = ext.replace(/[^a-z0-9\_\-\.]+/igm, '');
				FeedConfig[0][i][j]['ext'] = ext;

				if (!exts.hasOwnProperty(ext)) {
					exts[ext] = 1;
				} else {
					//не должны повторяться ext
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have dublicate ext " + ext;
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}


				//генерируемый параметр feedId с точкой в конце (используем для формирования файла фида, но без расширения)
//				FeedConfig[0][i][j]['feedId'] = FeedConfig[0][i]['Id'] + ".feed." + ext + ".";
				//без точки (точка есть в расширениях файлов)
				FeedConfig[0][i][j]['feedId'] = FeedConfig[0][i]['Id'] + ".feed." + ext;

				//необязательная проверка, так как все проверяется выше, но feedfiles может использоваться ниже
				if (!feedfiles.hasOwnProperty(FeedConfig[0][i][j]['feedId'])) {
					feedfiles[FeedConfig[0][i][j]['feedId']] = parsed_url['hostname'];
				} else {
					//не должны повторяться Id фидов 
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have dublicated feedId  " + FeedConfig[0][i][j]['feedId'];
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}


				if (FeedConfig[0][i][j]['ftp'] && FeedConfig[0][i][j]['ftp']['zipfilename']) {
					var zipfilename = FeedConfig[0][i][j]['ftp']['zipfilename']
					if (!zipnames.hasOwnProperty(zipfilename)) {
						zipnames[zipfilename] = 1;
					} else {
						//не должны повторяться zipfilename
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have dublicate zipfilename " + zipfilename;
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					}
				}


				if (FeedConfig[0][i][j].hasOwnProperty('keyword') && (typeof FeedConfig[0][i][j]['keyword'] !== 'string')) {
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct parameter keyword";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//					break;
				}


	function parcheck(parameterName,parameter,readRes) {
		//проверяем типовые значения параметров для поисковых кампаний

		var reterr = 0;
		var partype = '';

		if (({}.toString.call(parameter) === '[object Array]')&&(parameter.length > 0)) {
			partype = 'array3';
			if (typeof parameter[0] == "string") {
				if (parameter.length < 4) {
					for (var i = 0; i < parameter.length; i++) {
						if (typeof parameter[i] != 'string') {
							readRes += "\n" + 'bad parameter: ' + parameterName + " in GASearch";
							reterr = 1;
						}
				
					}
				} else {
					readRes += "\n" + 'bad parameter: ' + parameterName + " in GASearch";
					reterr = 1;
				}
			} else if ((parameterName == 'keys')&&(({}.toString.call(parameter[0]) === '[object Array]')||(typeof parameter[0] === 'function'))) {

					partype = [];
					for (var i = 0; i < parameter.length; i++) {
						if (({}.toString.call(parameter[i]) === '[object Array]')&&(parameter[i].length > 0)&&(parameter[i].length < 4)) {
							partype[i] = 'array3'
							for (var ii = 0; ii < parameter[i].length; ii++) {
								if (typeof parameter[i][ii] != 'string') {
									readRes += "\n" + 'bad parameter: ' + parameterName + " in GASearch";
									reterr = 1;
								}
				
							}
						} else if (typeof parameter[i] === 'function') {
							partype[i] = 'function';
						} else {
							partype[i] = '';
							readRes += "\n" + 'bad parameter: ' + parameterName + " in GASearch";
							reterr = 1;
						}
					}

			} else {
				readRes += "\n" + 'bad parameter: ' + parameterName + " in GASearch";
				reterr = 1;
			}
		} else if (typeof parameter === 'function') {
			partype = 'function';
		} else {
			readRes += "\n" + 'bad parameter: ' + parameterName + " in GASearch";
			reterr = 1;
		}


		return [reterr,readRes,partype];

	}




				if (FeedConfig[0][i][j].hasOwnProperty('childAddByRank')) {
					if (!FeedConfig[0][i][j].hasOwnProperty('parentFeed')) {
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter childAddByRank (without parentFeed)";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//				break;
					} else if ((typeof FeedConfig[0][i][j]['childAddByRank'] != 'number')||(FeedConfig[0][i][j]['childAddByRank'] < 0)) {
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter childAddByRank";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//				break;
					}
				}



				if (FeedConfig[0][i][j].hasOwnProperty('parentFeed')) {
					//родительский фид есть у фида, проверяем корректность

					var nocorrectfeed = 0;
					if (typeof FeedConfig[0][i][j]['parentFeed'] != 'string') {
						nocorrectfeed = 1;
					} else if (!FeedConfig[0][i][j]['parentFeed']) {
						nocorrectfeed = 1;
					} else if (FeedConfig[0][i][j]['parentFeed'] == j) {
						nocorrectfeed = 1;
					} else if (!FeedConfig[0][i]['feeds'][FeedConfig[0][i][j]['parentFeed']]) {
						nocorrectfeed = 1;
					}

					if (nocorrectfeed) {
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter parentFeed";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//				break;
						delete FeedConfig[0][i][j]['parentFeed'];
					} else {
						//формируем массив идентификаторов дочерних фидов
						var parFeed = FeedConfig[0][i][FeedConfig[0][i][j]['parentFeed']];
						if (!parFeed['childrenFeeds']) {
							parFeed['childrenFeeds'] = [];
						}
						parFeed['childrenFeeds'][parFeed['childrenFeeds'].length] = j;

						if (!FeedConfig[0][i][j]['childAddByRank']) {
							//устанавливаем родителю флаг, что есть хотя бы один child, который не участвует в игре "кто первый запишет"
							if (FeedConfig[0][i][FeedConfig[0][i][j]['parentFeed']])
								FeedConfig[0][i][FeedConfig[0][i][j]['parentFeed']]['ExistNotUsechildAddByRank'] = 1;
						}

					}
				}


				//для каждого фида вычисляем свои timeHours
				//не может быть в итоге пустой массив
				//может быть установлен только test для фида и не запускать никогда, кроме режима просмотра
				//если установлено в параметре пусто или 0, то 0 часов
				//если нет параметра, то берем вышестоящий параметр (для скана)
				var timeHoursFeedHash = {};
				var timeHoursFeedNotCorrect = 0;
				if (FeedConfig[0][i][j]['parentFeed']) {
					//копируем массив из родительского фида
					var timeHoursFeed = JSON.parse(JSON.stringify(FeedConfig[0][i][FeedConfig[0][i][j]['parentFeed']]['timeHours']));
					if (FeedConfig[0][i][j].hasOwnProperty('timeHours')) {
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have parameter timeHours in children feed, remove it";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//				break;
					}
				} else if (!FeedConfig[0][i][j].hasOwnProperty('timeHours')) {
					//копируем массив
					var timeHoursFeed = JSON.parse(JSON.stringify(timeHours));
				} else if (!FeedConfig[0][i][j]['timeHours']) {
					var timeHoursFeed = ['0'];
				} else if (typeof FeedConfig[0][i][j]['timeHours'] != 'string') {
					//некорректный параметер
					var timeHoursFeed = [];
					timeHoursFeedNotCorrect = 1;
				} else if (FeedConfig[0][i][j]['timeHours'] === 'test') {
					//test
					var timeHoursFeed = ['test'];
				} else {
					var timeHoursFeed = FeedConfig[0][i][j]['timeHours'].split(/[\s\,]+/);
				}
				if (timeHoursFeedNotCorrect) {
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter timeHours";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}
				for (var ii = 0; ii < timeHoursFeed.length; ii++) {
					if (isInteger(timeHoursFeed[ii])) {
						timeHoursFeed[ii] = parseInt(timeHoursFeed[ii]);
						timeHoursFeedHash[timeHoursFeed[ii]] = 1;
						if ((timeHoursFeed[ii] < 0) || (timeHoursFeed[ii] > 23)) {
							//некорректный параметр timeHoursFeed
							readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter timeHours " + timeHoursFeed[ii];
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//						break;
						}
					} else if ((timeHoursFeed[ii] == 'test') && (timeHoursFeed.length == 1)) {
						timeHoursFeedHash[timeHoursFeed[ii]] = 1;
					} else {
						//некорректный параметр timeHours
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter timeHours";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					}
				}
				FeedConfig[0][i][j]['timeHours'] = JSON.parse(JSON.stringify(timeHoursFeed));
				FeedConfig[0][i][j]['timeHoursHash'] = JSON.parse(JSON.stringify(timeHoursFeedHash));


				//собираем общий timeHours из чисел-часов (не может быть пустой в результате)
				//если у фида test, то добавляем этот параметр в общий timeHours
				for (var ii = 0; ii < timeHoursFeed.length; ii++) {
					if (!FeedConfig[0][i]['timeHoursHash'][timeHoursFeed[ii]]) {
						FeedConfig[0][i]['timeHoursHash'][timeHoursFeed[ii]] = 1;
						FeedConfig[0][i]['timeHours'][FeedConfig[0][i]['timeHours'].length] = timeHoursFeed[ii];
					}
				}


				if (FeedConfig[0][i][j]['timeHours']&&(FeedConfig[0][i][j]['timeHours'].indexOf('test') != -1)&&(FeedConfig[0][i][j]['timeHours'].length == 1)) {
					if (!FeedConfig[0][i][j].hasOwnProperty('all_fields_not_required')) {
						//без проверок корректности, просто флаг
						FeedConfig[0][i][j]['all_fields_not_required'] = FeedConfig[0][i]['all_fields_not_required'];
					}
				} else {
					//FeedConfig[0][i][j]['all_fields_not_required'] = 0;

					var mainall_fields_not_required = 0;
					//сначала установить значение all_fields_not_required для фида
					//если не пусто, то ошибка (он не может быть без test в timeHours)
					if (!FeedConfig[0][i][j].hasOwnProperty('all_fields_not_required')) {
						//без проверок корректности, просто флаг
						FeedConfig[0][i][j]['all_fields_not_required'] = FeedConfig[0][i]['all_fields_not_required'];
						mainall_fields_not_required = 1;
					}
					if (!FeedConfig[0][i][j]['all_fields_not_required']) {
						FeedConfig[0][i][j]['all_fields_not_required'] = 0;
					} else {
						if (mainall_fields_not_required) {
							if (!mainall_fields_not_required_err) {
								mainall_fields_not_required_err = 1;
								readRes += "\n" + "Config Array element number " + i + " have parameter all_fields_not_required without 'test' in timeHours, remove it or add  'test' in timeHours";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//				break;
							}
						} else {
							readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have parameter all_fields_not_required without 'test' in timeHours, remove it or add  'test' in timeHours";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//				break;
						}
					}
				}




				//для каждого фида вычисляем свои startDays
				//может быть в итоге пустой массив (означает что скан при каждом запуске)
				//если установлено в параметре пусто или 0, то скан при каждом запуске
				//если нет параметра, то берем вышестоящий параметр (для скана)
				var startDaysFeedHash = {};
				var startDaysFeedNotCorrect = 0;
				if (FeedConfig[0][i][j]['parentFeed']) {
					//копируем массив из родительского фида
					var startDaysFeed = JSON.parse(JSON.stringify(FeedConfig[0][i][FeedConfig[0][i][j]['parentFeed']]['startDays']));
					if (FeedConfig[0][i][j].hasOwnProperty('startDays')) {
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have parameter startDays in children feed, remove it";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//				break;
					}
				} else if (!FeedConfig[0][i][j].hasOwnProperty('startDays')) {
					var startDaysFeed = JSON.parse(JSON.stringify(startDays));
				} else if (!FeedConfig[0][i][j]['startDays']) {
					//скан при каждом запуске
					var startDaysFeed = [];
				} else if (typeof FeedConfig[0][i][j]['startDays'] != 'string') {
					//некорректный параметер
					var startDaysFeed = [];
					startDaysFeedNotCorrect = 1;
				} else {
					var startDaysFeed = FeedConfig[0][i][j]['startDays'].split(/[\s\,]+/);
				}
				if (startDaysFeedNotCorrect) {
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter startDays";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}
				for (var ii = 0; ii < startDaysFeed.length; ii++) {
					if (isInteger(startDaysFeed[ii])) {
						startDaysFeed[ii] = parseInt(startDaysFeed[ii]);
						startDaysFeedHash[startDaysFeed[ii]] = 1;
						if ((startDaysFeed[ii] < 1) || (startDaysFeed[ii] > 31)) {
							//некорректный параметр startDaysFeed
							readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter startDays " + startDaysFeed[ii];
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//						break;
						}
					} else {
						//некорректный параметр startDays
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter startDays";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					}
				}
				FeedConfig[0][i][j]['startDays'] = JSON.parse(JSON.stringify(startDaysFeed));
				FeedConfig[0][i][j]['startDaysHash'] = JSON.parse(JSON.stringify(startDaysFeedHash));


				//собираем общий startDays из чисел месяца (может быть пустой в результате, то есть скан при каждом запуске)
				if (!startDaysFeed.length) {
					//если пустой в фиде, то общий делаем пустой (больше в общий нельзя добавлять значения)
					//даже если повторно здесь сделаем общий пустым - это не страшно
					FeedConfig[0][i]['startDays'] = [];
					FeedConfig[0][i]['startDaysHash'] = {};
				} else {

					if (!FeedConfig[0][i]['startDays']) {
						//еще не был создан массив (даже пустой), создаем, клонируем
						FeedConfig[0][i]['startDays'] = JSON.parse(JSON.stringify(startDaysFeed));
						FeedConfig[0][i]['startDaysHash'] = JSON.parse(JSON.stringify(startDaysFeedHash));
					} else if (FeedConfig[0][i]['startDays'].length) {
						//уже есть массив, добавляем значения - но только если общий массив не пустой (иначе нельзя добавлять, так как уже установлен скан при каждом запуске)
						for (var ii = 0; ii < startDaysFeed.length; ii++) {
							if (!FeedConfig[0][i]['startDaysHash'][startDaysFeed[ii]]) {
								FeedConfig[0][i]['startDaysHash'][startDaysFeed[ii]] = 1;
								FeedConfig[0][i]['startDays'][FeedConfig[0][i]['startDays'].length] = startDaysFeed[ii];
							}
						}
					}
				}




				//для каждого фида вычисляем свой noDropShip
				if (!FeedConfig[0][i][j].hasOwnProperty('noDropShip')) {
					if (FeedConfig[0][i].hasOwnProperty('noDropShip')) {
						FeedConfig[0][i][j]['noDropShip'] = FeedConfig[0][i]['noDropShip'];
					}
				}



				//для каждого фида вычисляем свой utmDropShip
				if (!FeedConfig[0][i][j].hasOwnProperty('utmDropShip')) {
					if (FeedConfig[0][i].hasOwnProperty('utmDropShip')) {
						FeedConfig[0][i][j]['utmDropShip'] = FeedConfig[0][i]['utmDropShip'];
					}
				} else if (typeof FeedConfig[0][i][j]['utmDropShip'] != 'string') {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter utmDropShip";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}



				if (FeedConfig[0][i][j].hasOwnProperty('file_type')) {
					var file_type = FeedConfig[0][i][j]['file_type']
					if ({}.toString.call(file_type) !== '[object Array]') {
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter file_type";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					} else {
						for (var ii = 0; ii < file_type.length; ii++) {
							if ((typeof file_type[ii] != 'string') || (correctfeedExt.indexOf(file_type[ii]) == -1)) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter file_type";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//							break;
							}
						}
					}
				}




				if (FeedConfig[0][i][j].hasOwnProperty('ftp')) {
					var ftp_config = FeedConfig[0][i][j]['ftp'];
					if (!(ftp_config['login'] && ftp_config['password'] && ftp_config['host'] && ftp_config['files_types'] && ftp_config['files_types'].length)) {
						readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter ftp";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					}
				}


	
				if ((FeedConfig[0][i][j].hasOwnProperty('skip_parse')) && ({}.toString.call(FeedConfig[0][i][j]['skip_parse']) !== '[object Array]')) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter skip_parse";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {

					var skip_url = FeedConfig[0][i].skip_parse;

					if (skip_url && skip_url.length) {
						for (var v in skip_url) {
				        		var parsed_url_tmp = parse_url(skip_url[v]);
							if (typeof parsed_url_tmp != 'string') {

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter skip_parse";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;

							} else if (parsed_url_tmp.hash || parsed_url_tmp.protocol || parsed_url_tmp.hostname || parsed_url_tmp.port) {

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter skip_parse " + parsed_url_tmp;
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;

							} else if (parsed_url_tmp.pathname.substr(0, 1) != '/') {


								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter skip_parse " + parsed_url_tmp;
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;

//								skip_url[v] = '/' + parsed_url.pathname + parsed_url.search;

							}
					        }
					}

				}



	
				if ((FeedConfig[0][i][j].hasOwnProperty('only_parse')) && ({}.toString.call(FeedConfig[0][i][j]['only_parse']) !== '[object Array]')) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter only_parse";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {

					var only_url = FeedConfig[0][i].only_parse;

					if (only_url && only_url.length) {
						for (var v in only_url) {
				        		var parsed_url_tmp = parse_url(only_url[v]);
							if (typeof parsed_url_tmp != 'string') {

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter only_parse";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;

							} else if (parsed_url_tmp.hash || parsed_url_tmp.protocol || parsed_url_tmp.hostname || parsed_url_tmp.port) {

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter only_parse " + parsed_url_tmp;
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;

							} else if (parsed_url_tmp.pathname.substr(0, 1) != '/') {

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter only_parse " + parsed_url_tmp;
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;
//								only_url[v] = '/' + parsed_url.pathname + parsed_url.search;

							}
					        }
					}

				}






				if ((FeedConfig[0][i][j].hasOwnProperty('parseUrlFun')) && (typeof FeedConfig[0][i][j]['parseUrlFun'] != 'function')) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter parseUrlFun";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}

				if ((FeedConfig[0][i][j].hasOwnProperty('checkFieldsFun')) && (typeof FeedConfig[0][i][j]['checkFieldsFun'] != 'function')) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter checkFieldsFun";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}


				if ((FeedConfig[0][i][j].hasOwnProperty('MAX_ADD_URLS')) && ((typeof FeedConfig[0][i][j]['MAX_ADD_URLS'] != 'number')||(FeedConfig[0][i][j]['MAX_ADD_URLS'] < 0))) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter MAX_ADD_URLS";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}


				//для каждого фида вычисляем свой No_Critical_Errors
				if (!FeedConfig[0][i][j].hasOwnProperty('No_Critical_Errors')) {
					if (FeedConfig[0][i].hasOwnProperty('No_Critical_Errors')) {
						FeedConfig[0][i][j]['No_Critical_Errors'] = FeedConfig[0][i]['No_Critical_Errors'];
					}
				} else if ((typeof FeedConfig[0][i][j]['No_Critical_Errors'] != 'number')||(FeedConfig[0][i][j]['No_Critical_Errors'] < 0)||(FeedConfig[0][i][j]['No_Critical_Errors'] > 5)) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter No_Critical_Errors";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}


				//для каждого фида вычисляем свой Write_emptyFeed_File
				if (!FeedConfig[0][i][j].hasOwnProperty('Write_emptyFeed_File')) {
					if (FeedConfig[0][i].hasOwnProperty('Write_emptyFeed_File')) {
						FeedConfig[0][i][j]['Write_emptyFeed_File'] = FeedConfig[0][i]['Write_emptyFeed_File'];
					}
				} else if ((typeof FeedConfig[0][i][j]['Write_emptyFeed_File'] != 'number')||(FeedConfig[0][i][j]['Write_emptyFeed_File'] < 0)||(FeedConfig[0][i][j]['Write_emptyFeed_File'] > 2)) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter Write_emptyFeed_File";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}


				if ((FeedConfig[0][i][j].hasOwnProperty('SKIP_FIRST_URLS')) && ((typeof FeedConfig[0][i][j]['SKIP_FIRST_URLS'] != 'number')||(FeedConfig[0][i][j]['SKIP_FIRST_URLS'] < 0))) {
					//некорректный параметер
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter SKIP_FIRST_URLS";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				}




				var GAS = FeedConfig[0][i][j]['GASearch'];


				if (GAS && ({}.toString.call(GAS) !== '[object Object]')) {
					//настройки GASearch должны быть кешем
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter 'GASearch'";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				} else if (GAS) {


					if (GAS.hasOwnProperty('Id')) {
						if (typeof GAS['Id'] != 'string') {
							//Id в GASearch должен быть строкой
							readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter 'Id' in 'GASearch'";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//						break;
						} else {

							//сохраняем начальное установленное значение (требуется дальше)
							//Idmanual - это признак ручного задания Id, а не автоматического
							//нигде не используется сейчас
							GAS['Idmanual'] = GAS['Id'];

							if (!GAS['Id']) {
								//генерируем название кампании
								GAS['Id'] = FeedConfig[0][i][j]['feedId'];
							}

							//убираем какие-то символы, которые могут вызвать ошибку при создании кампании
							GAS['Id'] = GAS['Id'].replace(/[^\!\|\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\(\)\" ]+/g, '');

							if (GAS['Id'].length > 100) {
								//ограничиваем длину названия кампании, что бы поместилась служебная информация
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " very long (> 100 chars) parameter 'Id' in 'GASearch' " + GAS['Id'];
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//							break;
							}

							//проверяем дубликаты РК по всем сайтам и фидам (так как один аккаунт GAds)
							if (!searchADids.hasOwnProperty(GAS['Id'])) {
								searchADids[GAS['Id']] = 1;
							} else {
								//не должны повторяться Id
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have dublicate parameter 'Id' in 'GASearch' " + GAS['Id'];
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//							break;
							}


							//должен быть указан бюджет
							if ((!GAS.hasOwnProperty('Budget')) || (typeof GAS['Budget'] !== 'number') || (GAS['Budget'] <= 0)) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter 'Budget' in 'GASearch' " + GAS['Id'];
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//							break;
							}



							if (GAS.hasOwnProperty('keyword') && (typeof GAS['keyword'] !== 'string')) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'keyword' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}



							if (GAS.hasOwnProperty('removeOutOfStockItem')) {
								if (!(({}.toString.call(GAS['removeOutOfStockItem']) === '[object Array]')&&(GAS['removeOutOfStockItem'].length == 3))) {
									readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'removeOutOfStockItem' in 'GASearch' ";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}
							}


							if (GAS.hasOwnProperty('removeNotFoundItem')) {
								if (typeof GAS['removeNotFoundItem'] != 'number') {
									readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'removeNotFoundItem' in 'GASearch' ";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}
							}


							if (GAS.hasOwnProperty('removeLSVKeywords')) {

								//контролировать наличие и целое число! (добавляется в название группы)

								if ((typeof GAS['removeLSVKeywords'] != 'number')||(!/^\d+$/.test(String(GAS['removeLSVKeywords'])))||(GAS['removeLSVKeywords'] < 0)||(GAS['removeLSVKeywords'] > 2)) {
									readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'removeLSVKeywords' in 'GASearch' ";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}
							} else {
								GAS['removeLSVKeywords'] = 0;
							}


							if (GAS.hasOwnProperty('powerControl')) {
								if (typeof GAS['powerControl'] != 'number') {
									readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'powerControl' in 'GASearch' ";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}
							}


							if ((!(GAS.hasOwnProperty('addkeystype')))||(typeof GAS['addkeystype'] != 'number')||(!/^\d+$/.test(String(GAS['addkeystype'])))||(GAS['addkeystype'] < 0)||(GAS['addkeystype'] > 8)) {

								//контролировать наличие и целое число! (добавляется в название группы)

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'addkeystype' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}


							if ((!(GAS.hasOwnProperty('maxNodigaddkeyNum')))||(typeof GAS['maxNodigaddkeyNum'] != 'number')||(!/^\d+$/.test(String(GAS['maxNodigaddkeyNum'])))||(GAS['maxNodigaddkeyNum'] < 3)) {

								//контролировать наличие и целое число! (добавляется в название группы)

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'maxNodigaddkeyNum' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}


							if ((!(GAS.hasOwnProperty('keywordtypes')))||(typeof GAS['keywordtypes'] != 'number')||(!/^\d+$/.test(String(GAS['keywordtypes'])))||(GAS['keywordtypes'] < 0)||(GAS['keywordtypes'] > 6)) {

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'keywordtypes' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}


							if ((!(GAS.hasOwnProperty('modifykeystype')))||(typeof GAS['modifykeystype'] != 'number')||(!/^\d+$/.test(String(GAS['modifykeystype'])))||(GAS['modifykeystype'] < 0)||(GAS['modifykeystype'] > 3)) {

								//контролировать наличие и целое число! (добавляется в название группы)

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'modifykeystype' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}


							if ((!(GAS.hasOwnProperty('modifykeysEnable')))||(typeof GAS['modifykeysEnable'] != 'number')||(!/^\d+$/.test(String(GAS['modifykeysEnable'])))||(GAS['modifykeysEnable'] < 0)||(GAS['modifykeysEnable'] > 2)) {

								//контролировать наличие и целое число! (добавляется в название группы)

								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'modifykeysEnable' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}


							if ((!GAS['priceRound'])||(typeof GAS['priceRound'] != 'number')||(GAS['priceRound'] < 0)) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'priceRound' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}


							if ((!(GAS.hasOwnProperty('startbid')))||(typeof GAS['startbid'] != 'number')||(GAS['startbid'] <= 0)) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'startbid' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}

							//проверяем обязательные моменты в настройках поисковой РК (если существуют параметры header и price, то проверяем на валидность)

							//обязательный параметр, поэтому добавляем что-то обязательно, если нет его
							if (!GAS.hasOwnProperty('header2')) 
								GAS['header2'] = [parsed_url['hostname']];

//начальная переделка под адаптивные объявления
//!!!@@@@@@@@@@@@@@@

							//обязательный параметр, поэтому добавляем фиксированное значение {Keyword:domain.com}
                                                        //при создании кампаний значение header3 не проверяется, вставляется как есть отсюда, поэтому внимательно его формируем
							var dm = parsed_url['hostname'];
//обрезаем если вдруг длинный домен
							dm = dm.substring(0,30);
							GAS['header3'] = '{KeyWord:' + dm + '}';


							if ((GAS.hasOwnProperty('keysmixtype'))&&((typeof GAS['keysmixtype'] != 'number')||(GAS['keysmixtype'] < 0)||(GAS['keysmixtype'] > 4))) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'keysmixtype' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;
							} else if (!GAS['keysmixtype']) {
								GAS['keysmixtype'] = 0;
							}

							if ((GAS.hasOwnProperty('masskeysmixtype'))&&((typeof GAS['masskeysmixtype'] != 'number')||(GAS['masskeysmixtype'] < 0)||(GAS['masskeysmixtype'] > 4))) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'masskeysmixtype' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;
							} else if (!GAS['masskeysmixtype']) {
								GAS['masskeysmixtype'] = 0;
							}


							if ((GAS.hasOwnProperty('notReqfixedkeys'))&&((typeof GAS['notReqfixedkeys'] != 'number')||(GAS['notReqfixedkeys'] < 0)||(GAS['notReqfixedkeys'] > 4))) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have correct 'notReqfixedkeys' in 'GASearch' ";

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;
							} else if (!GAS['notReqfixedkeys']) {
								GAS['notReqfixedkeys'] = 0;
							}


							var GASparams = ['url','mobileurl','keys','fixedkeys','masskeys','onekeys','header','paths','header2','price','description','afterdescription'];
							GAS.GASparams = [];
							for (var ii = 0; ii < GASparams.length; ii++) {
								if (GAS.hasOwnProperty(GASparams[ii])) {
									var [reterr2,readRes,partype] = parcheck(GASparams[ii],GAS[GASparams[ii]],readRes)
									if (reterr2) {
										readRes += ", Feed number " + j + " in Config Array element number " + i;
										reterr = 1;
									}
									GAS.GASparams[GAS.GASparams.length] = [GASparams[ii],partype];
								} else {
									GAS.GASparams[GAS.GASparams.length] = [GASparams[ii],''];
								}
							}


							//проверка наличия обязательных полей
							if (!GAS['url']) {
								//должна быть хотя бы одна настройка у объявления
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have 'url' in 'GASearch' " + GAS['Id'];

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}

							if (!GAS['GAShtmlAd']) {
								GAS['GAShtmlAd'] = 'Ad';
							} else if (typeof GAS['GAShtmlAd'] != 'string') {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect 'GAShtmlAd' in 'GASearch' " + GAS['Id'];

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;
							}

							if (!GAS['GAShtmlComment']) {
								GAS['GAShtmlComment'] = 'Examples of keywords. Will be filtered when added to a campaign';
							} else if (typeof GAS['GAShtmlComment'] != 'string') {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect 'GAShtmlComment' in 'GASearch' " + GAS['Id'];

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;
							}

							if (!GAS['GAShtmlExt']) {
								GAS['GAShtmlExt'] = '';
							} else if (typeof GAS['GAShtmlExt'] != 'string') {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect 'GAShtmlExt' in 'GASearch' " + GAS['Id'];

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;
							} else if (/^\s+$/.test(GAS['GAShtmlExt'])) {
								GAS['GAShtmlExt'] = '';
							} else if (!/^\s/.test(GAS['GAShtmlExt'])) {
								//добавляем пробел вначале
								GAS['GAShtmlExt'] = ' ' + GAS['GAShtmlExt'];
							}


							GAS['adids'] = [];

							var adsnum = 0;

							//проверяем настройки объявлений поисковой рекламы
							for (var k in GAS) {
								//только целые числа-строки пропускаем сейчас (объявления)
								//							if (!isInteger(k))
								//только id + целые числа пропускаем сейчас (объявления)
								if (!(((typeof k == 'string') && (/^id\d+$/.test(k)))))
									continue

								if ({}.toString.call(GAS[k]) !== '[object Object]') {
									//должен быть хеш у объявления
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not Hash";
									reterr = 1;
									break;
								}

								if (!GAS[k].hasOwnProperty('priceaddtype')) {
									//если закомментировано, то ставим 0
									GAS[k]['priceaddtype'] = 0;
								}

								var GASADparams = ['header','paths','header2','keys','fixedkeys','masskeys','onekeys','startdescription','description','beforeprice','afterprice','afterdescription'];
								GAS[k].GASADparams = [];
								for (var ii = 0; ii < GASADparams.length; ii++) {
									if (GAS[k].hasOwnProperty(GASADparams[ii])) {
										var [reterr2,readRes,partype] = parcheck(GASADparams[ii],GAS[k][GASADparams[ii]],readRes)
										if (reterr2) {
											readRes += ", Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i;
											reterr = 1;
										}
										GAS[k].GASADparams[GAS[k].GASADparams.length] = [GASADparams[ii],partype];
									} else {
										GAS[k].GASADparams[GAS[k].GASADparams.length] = [GASADparams[ii],''];
									}
								}




								//проверяем обязательные моменты в настройках объявлений


								//параметры из основных настроек РК
								GAS[k]['FromGAS'] = {};

								if (!GAS[k].hasOwnProperty('header')) {
									if (GAS.hasOwnProperty('header')) {
										GAS[k]['header'] = GAS['header'];
									}
									GAS[k]['FromGAS']['header'] = 1;
								}

								if (!GAS[k].hasOwnProperty('paths')) {
									if (GAS.hasOwnProperty('paths')) {
										GAS[k]['paths'] = GAS['paths'];
									}
									GAS[k]['FromGAS']['paths'] = 1;
								}

								if (!GAS[k].hasOwnProperty('header2')) {
									if (GAS.hasOwnProperty('header2')) {
										GAS[k]['header2'] = GAS['header2'];
									}
									GAS[k]['FromGAS']['header2'] = 1;
								}


								if (!GAS[k].hasOwnProperty('addkeystype')) {
									if (GAS.hasOwnProperty('addkeystype')) {
										GAS[k]['addkeystype'] = GAS['addkeystype'];
									}
									GAS[k]['FromGAS']['addkeystype'] = 1;
								}


								if (!GAS[k].hasOwnProperty('maxNodigaddkeyNum')) {
									if (GAS.hasOwnProperty('maxNodigaddkeyNum')) {
										GAS[k]['maxNodigaddkeyNum'] = GAS['maxNodigaddkeyNum'];
									}
									GAS[k]['FromGAS']['maxNodigaddkeyNum'] = 1;
								}


								if (!GAS[k].hasOwnProperty('keys')) {
									if (GAS.hasOwnProperty('keys')) {
										GAS[k]['keys'] = GAS['keys'];
									}
									GAS[k]['FromGAS']['keys'] = 1;
								}

								if (!GAS[k].hasOwnProperty('fixedkeys')) {
									if (GAS.hasOwnProperty('fixedkeys')) {
										GAS[k]['fixedkeys'] = GAS['fixedkeys'];
									}
									GAS[k]['FromGAS']['fixedkeys'] = 1;
								}


								if (!GAS[k].hasOwnProperty('masskeys')) {
									if (GAS.hasOwnProperty('masskeys')) {
										GAS[k]['masskeys'] = GAS['masskeys'];
									}
									GAS[k]['FromGAS']['masskeys'] = 1;
								}


								if (!GAS[k].hasOwnProperty('onekeys')) {
									if (GAS.hasOwnProperty('onekeys')) {
										GAS[k]['onekeys'] = GAS['onekeys'];
									}
									GAS[k]['FromGAS']['onekeys'] = 1;
								}


								if (!GAS[k].hasOwnProperty('description')) {
									if (GAS.hasOwnProperty('description')) {
										GAS[k]['description'] = GAS['description'];
									}
									GAS[k]['FromGAS']['description'] = 1;
								}

								if (!GAS[k].hasOwnProperty('afterdescription')) {
									if (GAS.hasOwnProperty('afterdescription')) {
										GAS[k]['afterdescription'] = GAS['afterdescription'];
									}
									GAS[k]['FromGAS']['afterdescription'] = 1;
								}

								if (!GAS[k].hasOwnProperty('noPaths')) {
									if (GAS.hasOwnProperty('noPaths')) {
										GAS[k]['noPaths'] = GAS['noPaths'];
									}
									GAS[k]['FromGAS']['noPaths'] = 1;
								}


								if (!GAS[k].hasOwnProperty('afterdescrNeed')) {
									if (GAS.hasOwnProperty('afterdescrNeed')) {
										GAS[k]['afterdescrNeed'] = GAS['afterdescrNeed'];
									}
									GAS[k]['FromGAS']['afterdescrNeed'] = 1;
								}


								if (!GAS[k].hasOwnProperty('keysmixtype')) {
									if (GAS.hasOwnProperty('keysmixtype')) {
										GAS[k]['keysmixtype'] = GAS['keysmixtype'];
									}
									GAS[k]['FromGAS']['keysmixtype'] = 1;
								}

								if (!GAS[k].hasOwnProperty('masskeysmixtype')) {
									if (GAS.hasOwnProperty('masskeysmixtype')) {
										GAS[k]['masskeysmixtype'] = GAS['masskeysmixtype'];
									}
									GAS[k]['FromGAS']['masskeysmixtype'] = 1;
								}

								if (!GAS[k].hasOwnProperty('notReqfixedkeys')) {
									if (GAS.hasOwnProperty('notReqfixedkeys')) {
										GAS[k]['notReqfixedkeys'] = GAS['notReqfixedkeys'];
									}
									GAS[k]['FromGAS']['notReqfixedkeys'] = 1;
								}


								if (!GAS[k].hasOwnProperty('requiredescr')) {
									if (GAS.hasOwnProperty('requiredescr')) {
										GAS[k]['requiredescr'] = GAS['requiredescr'];
									}
									GAS[k]['FromGAS']['requiredescr'] = 1;
								}

								if (!GAS[k].hasOwnProperty('keyword')) {
									if (GAS.hasOwnProperty('keyword')) {
										GAS[k]['keyword'] = GAS['keyword'];
									}
									GAS[k]['FromGAS']['keyword'] = 1;
								}

								//header проверяем на уровне объявлений
								if (!GAS[k]['header']) {
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have parameter header";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}

								//keys проверяем на уровне объявлений (может отсутсвовать, если есть masskeys или onekeys)
								if ((!GAS[k]['keys'])&&(!GAS[k]['masskeys'])&&(!GAS[k]['onekeys'])) {
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have parameter keys";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}

								if ((!GAS[k].hasOwnProperty('priceaddtype'))||(typeof GAS[k]['priceaddtype'] != 'number')) {
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have correct parameter priceaddtype";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}

								if ((GAS[k].hasOwnProperty('headerTo1Line'))&&(typeof GAS[k]['headerTo1Line'] != 'number')) {
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have correct parameter headerTo1Line";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								}


								//addkeystype проверяем на уровне объявлений и на уровне кампании тоже
								if ((!(GAS[k].hasOwnProperty('addkeystype')))||(typeof GAS[k]['addkeystype'] != 'number')||(!/^\d+$/.test(String(GAS[k]['addkeystype'])))||(GAS[k]['addkeystype'] < 0)||(GAS[k]['addkeystype'] > 8)) {

									//контролировать наличие и целое число! (добавляется в название группы)
			
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have correct parameter addkeystype";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;

								}


								//maxNodigaddkeyNum проверяем на уровне объявлений и на уровне кампании тоже
								if ((!(GAS[k].hasOwnProperty('maxNodigaddkeyNum')))||(typeof GAS[k]['maxNodigaddkeyNum'] != 'number')||(!/^\d+$/.test(String(GAS[k]['maxNodigaddkeyNum'])))||(GAS[k]['maxNodigaddkeyNum'] < 3)) {

									//контролировать наличие и целое число! (добавляется в название группы)

									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have correct parameter maxNodigaddkeyNum";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;

								}


								//keysmixtype проверяем на уровне объявлений и на уровне кампании тоже
								if ((GAS[k].hasOwnProperty('keysmixtype'))&&(!GAS[k]['FromGAS']['keysmixtype'])&&((typeof GAS[k]['keysmixtype'] != 'number')||(GAS[k]['keysmixtype'] < 0)||(GAS[k]['keysmixtype'] > 4))) {
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have correct parameter keysmixtype";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								} else if (!GAS[k]['keysmixtype']) {
									GAS[k]['keysmixtype'] = 0;
								}

								//masskeysmixtype проверяем на уровне объявлений и на уровне кампании тоже
								if ((GAS[k].hasOwnProperty('masskeysmixtype'))&&(!GAS[k]['FromGAS']['masskeysmixtype'])&&((typeof GAS[k]['masskeysmixtype'] != 'number')||(GAS[k]['masskeysmixtype'] < 0)||(GAS[k]['masskeysmixtype'] > 4))) {
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have correct parameter masskeysmixtype";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								} else if (!GAS[k]['masskeysmixtype']) {
									GAS[k]['masskeysmixtype'] = 0;
								}

								//notReqfixedkeys проверяем на уровне объявлений и на уровне кампании тоже
								if ((GAS[k].hasOwnProperty('notReqfixedkeys'))&&(!GAS[k]['FromGAS']['notReqfixedkeys'])&&((typeof GAS[k]['notReqfixedkeys'] != 'number')||(GAS[k]['notReqfixedkeys'] < 0)||(GAS[k]['notReqfixedkeys'] > 4))) {
									readRes += "\n" + "Ad number " + k + " in 'GASearch' in Feed number " + j + " in Config Array element number " + i + " is not have correct parameter notReqfixedkeys";

									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//								break;
								} else if (!GAS[k]['notReqfixedkeys']) {
									GAS[k]['notReqfixedkeys'] = 0;
								}

								GAS['adids'][GAS['adids'].length] = k;

								adsnum++;

							}


							if (!adsnum) {
								//должно быть хотя бы одно объявление
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " not have ads in 'GASearch' " + GAS['Id'];

								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//								break;

							}



						}


					}


					if (GAS.hasOwnProperty('GAShtml')) {
						if ((typeof GAS['GAShtml'] != 'number')||(GAS['GAShtml'] > 2)||(GAS['GAShtml'] < 0)) {
							readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " have incorrect parameter 'GAShtml' in 'GASearch'";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//						break;
						}
						//добавляем параметры для фида GAShtml
						FeedConfig[0][i][j]['GAShtmlForfeed'] = [GAS['GAShtmlAd'], GAS['GAShtmlComment'], GAS['GAShtmlExt'], parsed_url['hostname'] + '/']

					}

					//здесь уже должен быть GAS['Id'], если он раскомментирован и пустой
					if (!GAS['Id']) {
						if (GAS.hasOwnProperty('GAShtml')) {
							//обязательно удаляем, нет GAS['Id'], иначе пропустит дальше без проверки полей, если вдруг захочется отдельно запускать GAShtml то нужно что бы поля рекламы проверялись
							delete GAS['GAShtml']
						}
					}



				}


				if (FeedConfig[0][i][j]['all_fields_not_required']&&FeedConfig[0][i][j].hasOwnProperty('GASearch')) {
					//удаляем поиск, иначе возникнут ошибки при выполнении
					delete FeedConfig[0][i][j]['GASearch'];
					if (FeedConfig[0][i][j]['GAShtmlForfeed'])
						delete FeedConfig[0][i][j]['GAShtmlForfeed'];
				}



				var same_field_name_attrs = {};

				//проверка что хотя бы одно поле видимое в фиде (если нет заданий это подсказка)
				FeedConfig[0][i][j]['onefieldvisible'] = 0

				var fieldsnum = 0;
				//дальше просто проверка, что есть хотя бы одно поле у фида
				for (var k in FeedConfig[0][i][j]) {
					//только целые числа-строки пропускаем сейчас (поля фида)
					//				if (!isInteger(k))
					//только id + целые числа пропускаем сейчас (поля фида)
					if (!(((typeof k == 'string') && (/^id\d+$/.test(k)))))
						continue
					if ({}.toString.call(FeedConfig[0][i][j][k]) !== '[object Object]') {
						//должен быть хеш у поля фида
						readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " is not Hash";
						reterr = 1;
						break;
					}


					//добавляем признак поля фида, названия/ключи хеша фида, которые являются ключами хешей/идентификаторами полей в фиде
					if (!FeedConfig[0][i][j]['fields']) {
						FeedConfig[0][i][j]['fields'] = {};
					}
					FeedConfig[0][i][j]['fields'][k] = 1;


					//проверка, что есть хотя бы одна настройка у поля фида (field_name)
					if (!FeedConfig[0][i][j][k].hasOwnProperty('field_name')) {
						//должна быть хотя бы одна настройка у поля фида сайта
						readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " is not have field_name";
						reterr = 1;
						//можно идти дальше, что бы весь конфиг проверить
						//					break;
					} else {
						if (typeof FeedConfig[0][i][j][k]['field_name'] != 'string') {
							//field_name должно быть строкой
							readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter field_name";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//					break;
						} else {
							if (FeedConfig[0][i][j][k].hasOwnProperty('multifield_name')) {
								if ((typeof FeedConfig[0][i][j][k]['multifield_name'] != 'string')||(!FeedConfig[0][i][j][k]['multifield_name'])) {
									readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter multifield_name";
									reterr = 1;
									//можно идти дальше, что бы весь конфиг проверить
									//					break;
								} else {
									if (FeedConfig[0][i][j][k]['multifield_name'].length > FeedConfig[0][i][j][k]['field_name'].length) {
										readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter multifield_name";
										reterr = 1;
										//можно идти дальше, что бы весь конфиг проверить
										//					break;
									} else if (FeedConfig[0][i][j][k]['field_name'].slice(0, FeedConfig[0][i][j][k]['multifield_name'].length) !== FeedConfig[0][i][j][k]['multifield_name']) {
//multifield_name должен быть началом параметра field_name
										readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter multifield_name";
										reterr = 1;
										//можно идти дальше, что бы весь конфиг проверить
										//					break;
									}
								}
							}
						}
					}


					//делаем другие обязательные проверки


					if (FeedConfig[0][i][j][k].hasOwnProperty('field_visible')) {
						if (typeof FeedConfig[0][i][j][k]['field_visible'] != 'number') {
							readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter field_visible";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//						break;
						}
					} else {
						FeedConfig[0][i][j][k]['field_visible'] = 0;
					}


				
					if (FeedConfig[0][i][j][k]['field_visible']) {
						FeedConfig[0][i][j]['onefieldvisible'] = 1;
						if (FeedConfig[0][i][j][k].hasOwnProperty('file_type')) {
							var file_type = FeedConfig[0][i][j][k]['file_type'];
							if ({}.toString.call(file_type) !== '[object Array]') {
								readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter file_type";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//						break;
							} else {
								for (var ii = 0; ii < file_type.length; ii++) {
									if ((typeof file_type[ii] != 'string') || (correctfeedExt.indexOf(file_type[ii]) == -1)) {
										readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter file_type";
										reterr = 1;
										//можно идти дальше, что бы весь конфиг проверить
										//								break;
									}
								}
							}
						} else if (FeedConfig[0][i][j].hasOwnProperty('file_type')) {
							FeedConfig[0][i][j][k]['file_type'] = FeedConfig[0][i][j]['file_type'];
						}
					} else {
						//удаляем file_type если есть (можно присвоить undefined, без разницы)
						FeedConfig[0][i][j][k]['file_type'] = [];
					}



					//проверяем, что у одинаковых по названию полей должны быть одинаковые field_visible и file_type
					if (FeedConfig[0][i][j][k]['multifield_name']&&(typeof FeedConfig[0][i][j][k]['multifield_name'] == 'string')) {
						if (!same_field_name_attrs.hasOwnProperty(FeedConfig[0][i][j][k]['multifield_name'])) {
							same_field_name_attrs[FeedConfig[0][i][j][k]['multifield_name']] = JSON.stringify(FeedConfig[0][i][j][k]['field_visible']) + "\t" + JSON.stringify(FeedConfig[0][i][j][k]['file_type']);
						} else if ( same_field_name_attrs[FeedConfig[0][i][j][k]['multifield_name']] !== JSON.stringify(FeedConfig[0][i][j][k]['field_visible']) + "\t" + JSON.stringify(FeedConfig[0][i][j][k]['file_type']) ) {
							readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter field_visible or file_type (not equal to another same multifield_name)";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//						break;
						}
						
					} else if (FeedConfig[0][i][j][k]['field_name']&&(typeof FeedConfig[0][i][j][k]['field_name'] == 'string')) {
						if (!same_field_name_attrs.hasOwnProperty(FeedConfig[0][i][j][k]['field_name'])) {
							same_field_name_attrs[FeedConfig[0][i][j][k]['field_name']] = JSON.stringify(FeedConfig[0][i][j][k]['field_visible']) + "\t" + JSON.stringify(FeedConfig[0][i][j][k]['file_type']);
						} else if ( same_field_name_attrs[FeedConfig[0][i][j][k]['field_name']] !== JSON.stringify(FeedConfig[0][i][j][k]['field_visible']) + "\t" + JSON.stringify(FeedConfig[0][i][j][k]['file_type']) ) {
							readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter field_visible or file_type (not equal to another same field_name)";
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//						break;
						}
					}


//выше уже проверены/установлены field_name multifield_name file_type для текущего поля
//в этом блоке делаются два действия
//	1.создание хеша фида feed_fields_file_types - состоит из списка типов расширений, которые в свою очередь состоят из списка названий полей field_name/multifield_name, входящих в эти расширения (поля multifield_name в свою очередь состоят из списка field_name)
//	2.проверка дублирования field_name и multifield_name в разных полях фида (поэтому всегда добавлен тип 'CheckAlwaysExistNoext', который потом удаляется)
//этот блок нужен для формирования файлов фидов, поэтому в хеш вносятся списки хешей названий полей для каждого файла фида:
//									если есть multifield_name, то вносится multifield_name и массив относящихся к нему field_name,
//									если нет multifield_name, то вносится field_name
					if (!FeedConfig[0][i][j]['feed_fields_file_types'])
						FeedConfig[0][i][j]['feed_fields_file_types']={}
					if ({}.toString.call(FeedConfig[0][i][j][k]['file_type']) === '[object Array]') {
						var file_type = JSON.parse(JSON.stringify(FeedConfig[0][i][j][k]['file_type']));
						file_type[file_type.length] = 'CheckAlwaysExistNoext';
					} else {
						var file_type = ['CheckAlwaysExistNoext'];
					}
					var field_name = FeedConfig[0][i][j][k]['field_name'];
					var multifield_name = FeedConfig[0][i][j][k]['multifield_name'];
					var feed_fields_file_types = FeedConfig[0][i][j]['feed_fields_file_types'];
					if ((typeof field_name == 'string')&&((typeof multifield_name == 'string')||(typeof multifield_name == 'undefined'))) {
						for (var ii = 0; ii < file_type.length; ii++) {
							if (typeof file_type[ii] == 'string') {
								if (!feed_fields_file_types[file_type[ii]])
									feed_fields_file_types[file_type[ii]] = {};
								var file_type_hash = feed_fields_file_types[file_type[ii]];
								if (multifield_name) {
									if (file_type_hash.hasOwnProperty(multifield_name)) {
										if ({}.toString.call(file_type_hash[multifield_name]) != '[object Array]') {
											readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter multifield_name (equal to param field_name form another feed field)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//	break;// здесь надо делать двойной выход из двух циклов

											break; //этот break не связан с закомментированным break выше
										}
									} else {
										file_type_hash[multifield_name] = [];
									}
									if (file_type_hash[multifield_name].indexOf(field_name) == -1)
										file_type_hash[multifield_name][file_type_hash[multifield_name].length] = field_name;
								} else {
									if (file_type_hash.hasOwnProperty(field_name)) {
										if (typeof file_type_hash[field_name] != 'number') {
											readRes += "\n" + "Field number " + k + " in Feed number " + j + " in Config Array element number " + i + " have incorrect parameter field_name (equal to param multifield_name form another feed field)";
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//	break;// здесь надо делать двойной выход из двух циклов
								
											break; //этот break не связан с закомментированным break выше
										}
									}
									file_type_hash[field_name] = 1;
								}
							}
						}
					}




					if ((!FeedConfig[0][i][j][k].hasOwnProperty('htmlencode')) && (FeedConfig[0][i][j].hasOwnProperty('htmlencode')))
						FeedConfig[0][i][j][k]['htmlencode'] = FeedConfig[0][i][j]['htmlencode'];




					if (FeedConfig[0][i][j]['all_fields_not_required']) {
						FeedConfig[0][i][j][k]['field_required'] = 0;

/*
поле всегда необязательное.
что бы не получилось других вариантов из-за строки в field_required - поэтому закомментировано здесь и вставлено выше 

						if (FeedConfig[0][i][j][k].hasOwnProperty('field_required')) {
							if (typeof FeedConfig[0][i][j][k]['field_required'] == 'number') {
								//только числа изменяем, строка - это необязатальное поле
								FeedConfig[0][i][j][k]['field_required'] = 0;
							}
						} else {
							FeedConfig[0][i][j][k]['field_required'] = 0;
						}
*/
					}



					fieldsnum++;
				}

				if (FeedConfig[0][i][j]['feed_fields_file_types']) {
//после проверки дублирования field_name и multifield_name в разных полях фида больше не нужен элемент 'CheckAlwaysExistNoext'
					delete FeedConfig[0][i][j]['feed_fields_file_types']['CheckAlwaysExistNoext'];
				} else {
					FeedConfig[0][i][j]['feed_fields_file_types']={};
				}




				feedsnum++;
				if (!fieldsnum) {
					//должно быть хотя бы одно поле у фида сайта
					readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " is not have fields";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;
				}
			}

			if (!FeedConfig[0][i]['timeHours'].length) {
				//если не заполнено фидами, то заполняем значения по умолчанию
				//такое возможно в некоторых ситуациях
				FeedConfig[0][i]['timeHours'] = JSON.parse(JSON.stringify(timeHours));
				FeedConfig[0][i]['timeHoursHash'] = JSON.parse(JSON.stringify(timeHoursHash));
			}


			if (!FeedConfig[0][i]['startDays']) {
				//если не заполнено фидами, то заполняем значения по умолчанию
				//такое возможно в некоторых ситуациях
				FeedConfig[0][i]['startDays'] = JSON.parse(JSON.stringify(startDays));
				FeedConfig[0][i]['startDaysHash'] = JSON.parse(JSON.stringify(startDaysHash));
			}



			if (!feedsnum) {
				//должен быть хотя бы один фид у сайта
				readRes += "\n" + "Config Array element number " + i + " is not have feeds";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			} else {
				for (var j in FeedConfig[0][i]['feeds']) {

					if (FeedConfig[0][i][j]['GASearch']&&FeedConfig[0][i][j]['GASearch']['GAShtml']) {
						FeedConfig[0][i][j]['onefieldvisible'] = 1;
					}

					if (!((FeedConfig[0][i][j]['GASearch']&&FeedConfig[0][i][j]['GASearch']['Id'])||(Object.keys(FeedConfig[0][i][j]['feed_fields_file_types']).length)||(FeedConfig[0][i][j]['GASearch']&&FeedConfig[0][i][j]['GASearch']['GAShtml']))) {
//нет никакого задания на выполнение (нет заданий на создание файлов фида или создание поисковой кампании)

						if (!(FeedConfig[0][i][j]['childrenFeeds']&&FeedConfig[0][i][j]['childrenFeeds'].length)) {
//а так же у фида нет дочерних фидов, что по сути тоже является заданием (если дочерние фиды используют поля родительского - то есть родитель раздает поля детям)
							if (FeedConfig[0][i][j]['onefieldvisible']) {
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " is not have jobs (create feed and/or create search company and/or have children feeds) ";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//				break;
							} else {
//специально подсказка что нет ни одного видимого поля в фиде
								readRes += "\n" + "Feed number " + j + " in Config Array element number " + i + " is not have jobs (create feed with VISIBLE fields and/or create search company and/or have children feeds) ";
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//				break;
							}
						}

					}
				}
			}



			FeedConfig[0][i]['feedsFullExts'] = {}
			//добавляем признак полного расширения фида, для быстрой проверки наличия расширения в скане (для удаления файлов)
			for (var j in FeedConfig[0][i]['feeds']) {
				for (var feed_file_ext in FeedConfig[0][i][j]['feed_fields_file_types']) {
//feed_file_ext - это допустимые расширения файлов фидов - '.html', '.ga.xml', '.ga.txt', '.ga.tsv', '.yml.xml', '.yrl.xml', '.ycar.xml', '.ya.tsv'
					FeedConfig[0][i]['feedsFullExts'][FeedConfig[0][i][j]['ext'] + feed_file_ext] = 1
				}
				if (FeedConfig[0][i][j]['GASearch']&&FeedConfig[0][i][j]['GASearch']['GAShtml'])
					FeedConfig[0][i]['feedsFullExts'][FeedConfig[0][i][j]['ext'] + '.gas.html'] = 1
			}


			//pausedFeedsIds используется только для анализа запуска фидов (эти не запускать)
			FeedConfig[0][i]['pausedFeedsIds'] = {}
			//pausedFeedsFullExts используется только для анализа удаления файлов фидов (сюда нужно добавлять GAShtml расширение)
			FeedConfig[0][i]['pausedFeedsFullExts'] = {}
			if (FeedConfig[0][i].hasOwnProperty('pausedFeeds')) {
				if (typeof FeedConfig[0][i]['pausedFeeds'] != 'string') {
					//некорректный параметр timeHours
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter pausedFeeds";
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//			break;
				} else {
					var pausedFeeds = FeedConfig[0][i]['pausedFeeds'].split(/[\s\,]+/);
					var pausedFeedsIds = FeedConfig[0][i]['pausedFeedsIds']
					for (var ii = 0; ii < pausedFeeds.length; ii++) {
						var pausedFeedId = pausedFeeds[ii];
//						if (FeedConfig[0][i][pausedFeedId]) {
						if (FeedConfig[0][i]['feeds'][pausedFeedId]) {
							if (!pausedFeedsIds[pausedFeedId]) {
								pausedFeedsIds[pausedFeedId] = 1
								//формируем массив названий файлов остановленных фидов скана
								for (var feed_file_ext in FeedConfig[0][i][pausedFeedId]['feed_fields_file_types']) {
//feed_file_ext - это допустимые расширения файлов фидов - '.html', '.ga.xml', '.ga.txt', '.ga.tsv', '.yml.xml', '.yrl.xml', '.ycar.xml', '.ya.tsv'
									FeedConfig[0][i]['pausedFeedsFullExts'][FeedConfig[0][i][pausedFeedId]['ext'] + feed_file_ext] = 1
								}
								if (FeedConfig[0][i][pausedFeedId]['GASearch']&&FeedConfig[0][i][pausedFeedId]['GASearch']['GAShtml'])
									FeedConfig[0][i]['pausedFeedsFullExts'][FeedConfig[0][i][pausedFeedId]['ext'] + '.gas.html'] = 1
								if (FeedConfig[0][i][pausedFeedId]['childrenFeeds']&&FeedConfig[0][i][pausedFeedId]['childrenFeeds'].length) {
									//это родительский фид, проверяем что все дети остановлены у него
									var childrenFeeds = FeedConfig[0][i][pausedFeedId]['childrenFeeds'];
									for (var iii = 0; iii < childrenFeeds.length; iii++) {
										var childrenFeed = childrenFeeds[iii];
										if (FeedConfig[0][i][childrenFeed]&&(pausedFeeds.indexOf(childrenFeed) == -1)) {
											//некорректный параметр timeHours
											readRes += "\n" + "Config Array element number " + i + ' not have paused children feed ' + childrenFeed + ' in parameter pausedFeeds for paused parent feed ' + pausedFeedId;
											reterr = 1;
											//можно идти дальше, что бы весь конфиг проверить
											//			break;
										}
									}
								}
							} else {
								//некорректный параметр pausedFeeds
								readRes += "\n" + "Config Array element number " + i + " have dublicate in parameter pausedFeeds: " + pausedFeedId;
								reterr = 1;
								//можно идти дальше, что бы весь конфиг проверить
								//			break;
							}
						} else {
							//некорректный параметр pausedFeeds
							readRes += "\n" + "Config Array element number " + i + " have incorrect parameter pausedFeeds. Not found feed: " + pausedFeedId;
							reterr = 1;
							//можно идти дальше, что бы весь конфиг проверить
							//			break;
						}
					}
				}
			}


			if ((!FeedConfig[0][i]['Id']) || (typeof FeedConfig[0][i]['Id'] != 'string')) {
				//идентификаторы сайтов должны быть непустой строкой
				if (existId) {
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter Id for site";
				} else {
//так как разрешено дублировать сайты, то нужно указать разные Id вручную
					readRes += "\n" + "Config Array element number " + i + " have incorrect parameter Id for site (not empty Id for dublicate hostname)";
				}
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			} else if (!scanids.hasOwnProperty(FeedConfig[0][i]['Id'])) {
				//записываем очередной идентификатор сайта
				try {
					scanids[FeedConfig[0][i]['Id']] = {
						Id: FeedConfig[0][i]['Id'],
						IdNum: i,
						timeHours: FeedConfig[0][i]['timeHours'],
						startDays: FeedConfig[0][i]['startDays'],
						priorityLev: FeedConfig[0][i]['priorityLev'],
						checksum: murmurhash3_32_gc(JSON.stringify(FeedConfig[0][i]))
					};
				} catch (e) {

					readRes += "\n" + "Config Array element number " + i + " have bad structure for JSON.stringify: " + e.message;
					reterr = 1;
					//можно идти дальше, что бы весь конфиг проверить
					//				break;

				}

			} else {
				//не должны повторяться идентификаторы сайтов
				readRes += "\n" + "Config Array element number " + i + " have dublicate parameter Id for site";
				reterr = 1;
				//можно идти дальше, что бы весь конфиг проверить
				//			break;
			}




		}



	}



	var configChecksum = false;

	if (!reterr) {
		try {
			configChecksum = murmurhash3_32_gc(JSON.stringify(FeedConfig[0]));
			readRes += "\n" + "Ok. Config checksum: " + configChecksum;

		} catch (e) {
			readRes += "\n" + "Config have bad structure for JSON.stringify: " + e.message;
			reterr = 1;
		}
	}

	if (reterr) {
		readRes += "\n" + "Config is not working . Exit";
	}

	if (CSVwarnings) {
		if (reterr) {
			readRes = "Warning, dublicate csv-lists:" + CSVwarnings + "\n\nERRORS:\n" + readRes;
		} else {
			readRes = "Warning, dublicate csv-lists:" + CSVwarnings + "\n\n" + readRes;
		}
//в это проге записываем как ошибку, что бы выдало alert
		reterr = 1;
	}


	var configChangeFLG = 0;

/*

	if (!AdWordsApp.getExecutionInfo().isPreview()) {
		//записываем отчет по результату чтения конфига (нужно в т.ч. как флаг в другом скрипте для удаления всех файлов сайта при окончании договора и затем удалить его самого)
		var confFileReport = IDName + '.confreport.txt';
		var fileData = '';
		var fileIter = filesfolder.getFilesByName(confFileReport);
		var filesN = 0;
		if (fileIter.hasNext()) {
			fileData = fileIter.next().getBlob().getDataAsString();
			filesN++;
		}

		if ((fileData != readRes) || (filesN > 1)) {
			//записываем только если есть изменения (что бы каждый час не передавало файлы)
			var confFileReportid = filesfolder.getFilesByName(confFileReport);
			if (confFileReportid.hasNext()) {
				var nfile = confFileReportid.next();
				filesfolder.removeFile(nfile);
			}
			filesfolder.createFile(confFileReport, readRes);
			//заодно передаем информацию, что конфиг изменился
			configChangeFLG = 1;
		}
	}

	alert(readRes);

*/

	if (reterr)
		alert(readRes);

	return [reterr, FeedConfig[0], scanids, configChecksum, configChangeFLG];
}




function parse_url(url) {
    var url = url.trim();
    if (ScanCreateFeedsGlobalVars&&ScanCreateFeedsGlobalVars.no_urlsearchtoLowerCase) {
    	var match = url.match(/^([^?]*)(\?.*)?$/);
    	url = (match && match[1] !== undefined ? match[1].toLowerCase() : '') + (match && match[2] !== undefined ? match[2] : '');
    } else {
    	url = url.toLowerCase();
    }
//    var match = url.match(/^((https?)\:\/\/)?((([^:\/?&#]+[\.][^:\/?&#]+)|(\[[^\/?&#]+\]))?(?:\:([0-9]+))?)?((\/[^?&#]*)?(\?[^#]*)?(#.*)?)?$/);
    //учтены вроде все варианты, в том числе плохие ссылки не будут отдаваться, например /pathname/&, даже IP ipv6 и ../aaaa ./fffff
//    var match = url.match(/^((https?)\:\/\/)?((([^:\/?&#]+[\.][^:\/?&#]+)|(\[[^\/?&#]+\]))?(?:\:([0-9]+))?(?!\.))?(([\/\.][^?&#]*)?(\?[^#]*)?(#.*)?)?$/);
//еще нужно учеть вариант без слеша типа href = aaaa.html?3333 (такие ссылки обрабатываются в ValidateUrl и т.п.)
    var match = url.match(/^((https?)\:\/\/)?((([^:\/?&#]+[\.][^:\/?&#]+)|(\[[^\/?&#]+\]))?(?:\:([0-9]+))?(?!\.))?(([\/\.\w\-][^?&#]*)?(\?[^#]*)?(#.*)?)?$/);
    if (match && ( match[2] || match[3] || match[4] || match[7] ) && match[9] && !(match[9].substr(0, 1) == '/' || match[9].substr(0, 1) == '.'))
//если нет слеша у pathname, то не может быть protocol и host
         match = null


    //http://example.com:3000/pathname/?search=test#hash  
    var parsed = {};
    parsed.href = url; // => "http://example.com:3000/pathname/?search=test#hash"
    parsed.protocol = match && match[2] !== undefined ? match[2] : ''; // => "http:"
    parsed.host = match && match[3] !== undefined ? match[3] : ''; // => "example.com:3000"
    parsed.hostname = match && match[4] !== undefined ? match[4] : ''; // => "example.com"
    parsed.port = match && match[7] !== undefined ? match[7] : ''; // => "3000"
    parsed.query = match && match[8] !== undefined ? match[8] : ''; // => "/pathname/?search=test#hash"
    parsed.pathname = match && match[9] !== undefined ? match[9] : ''; // => "/pathname/"
    parsed.search = match && match[10] !== undefined ? match[10] : ''; // => "?search=test"
    parsed.hash = match && match[11] !== undefined ? match[11] : ''; // => "#hash"

//если только отдельно делать toLowerCase() для элементов, то следующая проверка ниже не пройдет при наличии больших букв

    if ((parsed.protocol ? parsed.protocol + '://' : '') + parsed.hostname + parsed.port + parsed.pathname + parsed.search + parsed.hash !== url) {
        return false;
    }

    return parsed;

}


function isInteger(num) {
	//целое число-строка или целое число
	if (typeof num == 'number') num = String(num);
	if (typeof num != 'string') return false;
	if (/^\d+$/.test(num)) {
		return true;
	} else {
		return false;
	}
}


function getPriceCurrencyFromStr(a) {
	//находим цену и валюту, и по ней код валюты (ISO 4217)

	//https://gist.github.com/beautyfree/8fba66ee44bb2124b2b9
	//https://github.com/chartbeat-labs/visualbigboard/blob/master/closure-library/closure/goog/i18n/.svn/text-base/currency.js.svn-base

	if (a) {

		a = a.replace(/\<[^\>]*\>/g, '');

		//убираем пробелы внутри чисел
		//var v = a.replace(/(?<=[\d\.\,])\s+(?=[\d\.\,])/g, ''); //просмотр назад не работает в гугле
		function replacer(match, offset, string) {
			match = match.replace(/\s+/g, '');
			return match;
		}
		var v = a.replace(/[\d\.\,][\d\.\,\s]+[\d\.\,]/g, replacer);

		var match = v.match(/((?:[\d]|(?:[\.\,][\d]))[\d\.\,]*)/);
		if (match && match[1]) {
			var sum = match[1];
			//принудительная замена запятых на точки
			//условие: количество запятых только одна и нет точек и после запятой не больше 2-х цифр
			if ((sum.match(/,/g)||[]).length == 1) {
				if (/\,\d\d?$/.test(sum)) {
					sum = sum.replace(/^(?!\.)(.*)\,/, '$1.');
				}
			}
//проверяем, что это число (могут быть запятые, которые удаляем для проверки) и число не равно 0
//sum - для nativeprice (здесь будет 0 на 0 грн, то есть непустое значение)
//sum2 - для iso4217price (здесь будет null на 0 грн, то есть пустое значение)
			var sumt = sum.replace(/\,/g, '');
			x = Number(sumt)
			if (x !== x) { //NaN
				sum2 = null;
			} else if (!x) { //zero
				sum2 = null;
			} else {
				sum2 = sum;
			}
		} else {
			var sum = null;
			var sum2 = null;
		}



		//                    a = a.toUpperCase();
		var b = [{
				pattern: /((?:EUR)|€)/i,
				currency: "EUR"
			}, {
				pattern: /((?:USD)|(?:У\.Е\.)|\$)/i,
				currency: "USD"
			}, {
				pattern: /((?:UAH)|(?:ГРН\.?)|(?:₴))/i,
				currency: "UAH"
			}, {
				pattern: /((?:RUR)|(?:RUB)|(?:Р\.)|(?:РУБ\.?))/i,
				currency: "RUB"
			}, {
				pattern: /((?:ТГ)|(?:KZT)|(?:₸)|(?:ТҢГ)|(?:TENGE)|(?:ТЕНГЕ))/i,
				currency: "KZT"
			}, {
				pattern: /((?:[A-Z][A-Z][A-Z]))/,
				currency: "MAYBE"
			}],
			c = b.map(function(b) {
				return {
					currency: b.currency,
					index: a.search(b.pattern),
					match: a.match(b.pattern)
				}
			}).filter(function(a) {
				return a.index > -1
			}).sort(function(a, b) {
				return a.index - b.index
			});
		//                    return c.length ? c[0].currency : void 0

		return [[sum, sum2], c.length ? [c[0].currency == 'MAYBE' ? c[0].match[1] : c[0].currency, c[0].match[1]] : null];
	}
}


function murmurhash3_32_gc(key, seed) {
	var remainder, bytes, h1, h1b, c1, c1b, c2, c2b, k1, i;

	remainder = key.length & 3; // key.length % 4
	bytes = key.length - remainder;
	h1 = seed;
	c1 = 0xcc9e2d51;
	c2 = 0x1b873593;
	i = 0;

	while (i < bytes) {
		k1 =
			((key.charCodeAt(i) & 0xff)) |
			((key.charCodeAt(++i) & 0xff) << 8) |
			((key.charCodeAt(++i) & 0xff) << 16) |
			((key.charCodeAt(++i) & 0xff) << 24);
		++i;

		k1 = ((((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16))) & 0xffffffff;
		k1 = (k1 << 15) | (k1 >>> 17);
		k1 = ((((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16))) & 0xffffffff;

		h1 ^= k1;
		h1 = (h1 << 13) | (h1 >>> 19);
		h1b = ((((h1 & 0xffff) * 5) + ((((h1 >>> 16) * 5) & 0xffff) << 16))) & 0xffffffff;
		h1 = (((h1b & 0xffff) + 0x6b64) + ((((h1b >>> 16) + 0xe654) & 0xffff) << 16));
	}

	k1 = 0;

	switch (remainder) {
		case 3:
			k1 ^= (key.charCodeAt(i + 2) & 0xff) << 16;
		case 2:
			k1 ^= (key.charCodeAt(i + 1) & 0xff) << 8;
		case 1:
			k1 ^= (key.charCodeAt(i) & 0xff);

			k1 = (((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16)) & 0xffffffff;
			k1 = (k1 << 15) | (k1 >>> 17);
			k1 = (((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16)) & 0xffffffff;
			h1 ^= k1;
	}

	h1 ^= key.length;

	h1 ^= h1 >>> 16;
	h1 = (((h1 & 0xffff) * 0x85ebca6b) + ((((h1 >>> 16) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
	h1 ^= h1 >>> 13;
	h1 = ((((h1 & 0xffff) * 0xc2b2ae35) + ((((h1 >>> 16) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
	h1 ^= h1 >>> 16;

	return h1 >>> 0;
}



Encoder = {

	// When encoding do we convert characters into html or numerical entities
	EncodeType : "entity",  // entity OR numerical

	isEmpty : function(val){
		if(val){
			return ((val===null) || val.length==0 || /^\s+$/.test(val));
		}else{
			return true;
		}
	},
	
	// arrays for conversion from HTML Entities to Numerical values
	arr1: ['&nbsp;','&iexcl;','&cent;','&pound;','&curren;','&yen;','&brvbar;','&sect;','&uml;','&copy;','&ordf;','&laquo;','&not;','&shy;','&reg;','&macr;','&deg;','&plusmn;','&sup2;','&sup3;','&acute;','&micro;','&para;','&middot;','&cedil;','&sup1;','&ordm;','&raquo;','&frac14;','&frac12;','&frac34;','&iquest;','&Agrave;','&Aacute;','&Acirc;','&Atilde;','&Auml;','&Aring;','&AElig;','&Ccedil;','&Egrave;','&Eacute;','&Ecirc;','&Euml;','&Igrave;','&Iacute;','&Icirc;','&Iuml;','&ETH;','&Ntilde;','&Ograve;','&Oacute;','&Ocirc;','&Otilde;','&Ouml;','&times;','&Oslash;','&Ugrave;','&Uacute;','&Ucirc;','&Uuml;','&Yacute;','&THORN;','&szlig;','&agrave;','&aacute;','&acirc;','&atilde;','&auml;','&aring;','&aelig;','&ccedil;','&egrave;','&eacute;','&ecirc;','&euml;','&igrave;','&iacute;','&icirc;','&iuml;','&eth;','&ntilde;','&ograve;','&oacute;','&ocirc;','&otilde;','&ouml;','&divide;','&oslash;','&ugrave;','&uacute;','&ucirc;','&uuml;','&yacute;','&thorn;','&yuml;','&quot;','&amp;','&lt;','&gt;','&OElig;','&oelig;','&Scaron;','&scaron;','&Yuml;','&circ;','&tilde;','&ensp;','&emsp;','&thinsp;','&zwnj;','&zwj;','&lrm;','&rlm;','&ndash;','&mdash;','&lsquo;','&rsquo;','&sbquo;','&ldquo;','&rdquo;','&bdquo;','&dagger;','&Dagger;','&permil;','&lsaquo;','&rsaquo;','&euro;','&fnof;','&Alpha;','&Beta;','&Gamma;','&Delta;','&Epsilon;','&Zeta;','&Eta;','&Theta;','&Iota;','&Kappa;','&Lambda;','&Mu;','&Nu;','&Xi;','&Omicron;','&Pi;','&Rho;','&Sigma;','&Tau;','&Upsilon;','&Phi;','&Chi;','&Psi;','&Omega;','&alpha;','&beta;','&gamma;','&delta;','&epsilon;','&zeta;','&eta;','&theta;','&iota;','&kappa;','&lambda;','&mu;','&nu;','&xi;','&omicron;','&pi;','&rho;','&sigmaf;','&sigma;','&tau;','&upsilon;','&phi;','&chi;','&psi;','&omega;','&thetasym;','&upsih;','&piv;','&bull;','&hellip;','&prime;','&Prime;','&oline;','&frasl;','&weierp;','&image;','&real;','&trade;','&alefsym;','&larr;','&uarr;','&rarr;','&darr;','&harr;','&crarr;','&lArr;','&uArr;','&rArr;','&dArr;','&hArr;','&forall;','&part;','&exist;','&empty;','&nabla;','&isin;','&notin;','&ni;','&prod;','&sum;','&minus;','&lowast;','&radic;','&prop;','&infin;','&ang;','&and;','&or;','&cap;','&cup;','&int;','&there4;','&sim;','&cong;','&asymp;','&ne;','&equiv;','&le;','&ge;','&sub;','&sup;','&nsub;','&sube;','&supe;','&oplus;','&otimes;','&perp;','&sdot;','&lceil;','&rceil;','&lfloor;','&rfloor;','&lang;','&rang;','&loz;','&spades;','&clubs;','&hearts;','&diams;'],
	arr2: ['&#160;','&#161;','&#162;','&#163;','&#164;','&#165;','&#166;','&#167;','&#168;','&#169;','&#170;','&#171;','&#172;','&#173;','&#174;','&#175;','&#176;','&#177;','&#178;','&#179;','&#180;','&#181;','&#182;','&#183;','&#184;','&#185;','&#186;','&#187;','&#188;','&#189;','&#190;','&#191;','&#192;','&#193;','&#194;','&#195;','&#196;','&#197;','&#198;','&#199;','&#200;','&#201;','&#202;','&#203;','&#204;','&#205;','&#206;','&#207;','&#208;','&#209;','&#210;','&#211;','&#212;','&#213;','&#214;','&#215;','&#216;','&#217;','&#218;','&#219;','&#220;','&#221;','&#222;','&#223;','&#224;','&#225;','&#226;','&#227;','&#228;','&#229;','&#230;','&#231;','&#232;','&#233;','&#234;','&#235;','&#236;','&#237;','&#238;','&#239;','&#240;','&#241;','&#242;','&#243;','&#244;','&#245;','&#246;','&#247;','&#248;','&#249;','&#250;','&#251;','&#252;','&#253;','&#254;','&#255;','&#34;','&#38;','&#60;','&#62;','&#338;','&#339;','&#352;','&#353;','&#376;','&#710;','&#732;','&#8194;','&#8195;','&#8201;','&#8204;','&#8205;','&#8206;','&#8207;','&#8211;','&#8212;','&#8216;','&#8217;','&#8218;','&#8220;','&#8221;','&#8222;','&#8224;','&#8225;','&#8240;','&#8249;','&#8250;','&#8364;','&#402;','&#913;','&#914;','&#915;','&#916;','&#917;','&#918;','&#919;','&#920;','&#921;','&#922;','&#923;','&#924;','&#925;','&#926;','&#927;','&#928;','&#929;','&#931;','&#932;','&#933;','&#934;','&#935;','&#936;','&#937;','&#945;','&#946;','&#947;','&#948;','&#949;','&#950;','&#951;','&#952;','&#953;','&#954;','&#955;','&#956;','&#957;','&#958;','&#959;','&#960;','&#961;','&#962;','&#963;','&#964;','&#965;','&#966;','&#967;','&#968;','&#969;','&#977;','&#978;','&#982;','&#8226;','&#8230;','&#8242;','&#8243;','&#8254;','&#8260;','&#8472;','&#8465;','&#8476;','&#8482;','&#8501;','&#8592;','&#8593;','&#8594;','&#8595;','&#8596;','&#8629;','&#8656;','&#8657;','&#8658;','&#8659;','&#8660;','&#8704;','&#8706;','&#8707;','&#8709;','&#8711;','&#8712;','&#8713;','&#8715;','&#8719;','&#8721;','&#8722;','&#8727;','&#8730;','&#8733;','&#8734;','&#8736;','&#8743;','&#8744;','&#8745;','&#8746;','&#8747;','&#8756;','&#8764;','&#8773;','&#8776;','&#8800;','&#8801;','&#8804;','&#8805;','&#8834;','&#8835;','&#8836;','&#8838;','&#8839;','&#8853;','&#8855;','&#8869;','&#8901;','&#8968;','&#8969;','&#8970;','&#8971;','&#9001;','&#9002;','&#9674;','&#9824;','&#9827;','&#9829;','&#9830;'],
		
	// Convert HTML entities into numerical entities
	HTML2Numerical : function(s){
		return this.swapArrayVals(s,this.arr1,this.arr2);
	},	

	// Convert Numerical entities into HTML entities
	NumericalToHTML : function(s){
		return this.swapArrayVals(s,this.arr2,this.arr1);
	},


	// Numerically encodes all unicode characters
	numEncode : function(s){ 
		if(this.isEmpty(s)) return ""; 

		var a = [],
			l = s.length; 
		
		for (var i=0;i<l;i++){ 
			var c = s.charAt(i); 
			if (c < " " || c > "~"){ 
				a.push("&#"); 
				a.push(c.charCodeAt()); //numeric value of code point 
				a.push(";"); 
			}else{ 
				a.push(c); 
			} 
		} 
		
		return a.join(""); 	
	}, 
	
	// HTML Decode numerical and HTML entities back to original values
	htmlDecode : function(s){

		var c,m,d = s;
		
		if(this.isEmpty(d)) return "";

		// convert HTML entites back to numerical entites first
		d = this.HTML2Numerical(d);
		
		// look for numerical entities &#34;
		arr=d.match(/&#[0-9]{1,5};/g);
		
		// if no matches found in string then skip
		if(arr!=null){
			for(var x=0;x<arr.length;x++){
				m = arr[x];
				c = m.substring(2,m.length-1); //get numeric part which is refernce to unicode character
				// if its a valid number we can decode
				if(c >= -32768 && c <= 65535){
					// decode every single match within string
					d = d.replace(m, String.fromCharCode(c));
				}else{
					d = d.replace(m, ""); //invalid so replace with nada
				}
			}			
		}

		return d;
	},		

	// encode an input string into either numerical or HTML entities
	htmlEncode : function(s,dbl){
			
		if(this.isEmpty(s)) return "";

		// do we allow double encoding? E.g will &amp; be turned into &amp;amp;
		dbl = dbl || false; //default to prevent double encoding
		
		// if allowing double encoding we do ampersands first
		if(dbl){
			if(this.EncodeType=="numerical"){
				s = s.replace(/&/g, "&#38;");
			}else{
				s = s.replace(/&/g, "&amp;");
			}
		}

		// convert the xss chars to numerical entities ' " < >
		s = this.XSSEncode(s,false);
		
		if(this.EncodeType=="numerical" || !dbl){
			// Now call function that will convert any HTML entities to numerical codes
			s = this.HTML2Numerical(s);
		}

		// Now encode all chars above 127 e.g unicode
		s = this.numEncode(s);

		// now we know anything that needs to be encoded has been converted to numerical entities we
		// can encode any ampersands & that are not part of encoded entities
		// to handle the fact that I need to do a negative check and handle multiple ampersands &&&
		// I am going to use a placeholder

		// if we don't want double encoded entities we ignore the & in existing entities
		if(!dbl){
			s = s.replace(/&#/g,"##AMPHASH##");
		
			if(this.EncodeType=="numerical"){
				s = s.replace(/&/g, "&#38;");
			}else{
				s = s.replace(/&/g, "&amp;");
			}

			s = s.replace(/##AMPHASH##/g,"&#");
		}
		
		// replace any malformed entities
		s = s.replace(/&#\d*([^\d;]|$)/g, "$1");

		if(!dbl){
			// safety check to correct any double encoded &amp;
			s = this.correctEncoding(s);
		}

		// now do we need to convert our numerical encoded string into entities
		if(this.EncodeType=="entity"){
			s = this.NumericalToHTML(s);
		}

		return s;					
	},

	// Encodes the basic 4 characters used to malform HTML in XSS hacks
	XSSEncode : function(s,en){
		if(!this.isEmpty(s)){
			en = en || true;
			// do we convert to numerical or html entity?
			if(en){
				s = s.replace(/\'/g,"&#39;"); //no HTML equivalent as &apos is not cross browser supported
				s = s.replace(/\"/g,"&quot;");
				s = s.replace(/</g,"&lt;");
				s = s.replace(/>/g,"&gt;");
			}else{
				s = s.replace(/\'/g,"&#39;"); //no HTML equivalent as &apos is not cross browser supported
				s = s.replace(/\"/g,"&#34;");
				s = s.replace(/</g,"&#60;");
				s = s.replace(/>/g,"&#62;");
			}
			return s;
		}else{
			return "";
		}
	},

	// returns true if a string contains html or numerical encoded entities
	hasEncoded : function(s){
		if(/&#[0-9]{1,5};/g.test(s)){
			return true;
		}else if(/&[A-Z]{2,6};/gi.test(s)){
			return true;
		}else{
			return false;
		}
	},

	// will remove any unicode characters
	stripUnicode : function(s){
		return s.replace(/[^\x20-\x7E]/g,"");
		
	},

	// corrects any double encoded &amp; entities e.g &amp;amp;
	correctEncoding : function(s){
		return s.replace(/(&amp;)(amp;)+/,"$1");
	},


	// Function to loop through an array swaping each item with the value from another array e.g swap HTML entities with Numericals
	swapArrayVals : function(s,arr1,arr2){
		if(this.isEmpty(s)) return "";
		var re;
		if(arr1 && arr2){
			//ShowDebug("in swapArrayVals arr1.length = " + arr1.length + " arr2.length = " + arr2.length)
			// array lengths must match
			if(arr1.length == arr2.length){
				for(var x=0,i=arr1.length;x<i;x++){
					re = new RegExp(arr1[x], 'g');
					s = s.replace(re,arr2[x]); //swap arr1 item with matching item from arr2	
				}
			}
		}
		return s;
	},

	inArray : function( item, arr ) {
		for ( var i = 0, x = arr.length; i < x; i++ ){
			if ( arr[i] === item ){
				return i;
			}
		}
		return -1;
	}

}




//начальная переделка под адаптивные объявления
//!!!@@@@@@@@@@@@@@@

//function ItemHeaders(name,shortWordsHash,paths,name2,keyword,startdescription,beforeprice,price,action,description,enddescription,pricetypeadd,priceRound,noPaths,headerTo1Line,needafterdescription,needdescription,notuseparams) {
function ItemHeaders(name,shortWordsHash,paths,name2,name3,keyword,startdescription,beforeprice,price,action,description,enddescription,pricetypeadd,priceRound,noPaths,headerTo1Line,needafterdescription,needdescription,notuseparams) {

//формируем массивы названия, описания и для видимой ссылки
//эти массивы формируются с учетом параметров price,description (если они есть)

//поддержифаются модификаторы объявлений, но обязательно с доп. инфокавычками справа {модификатор объявления}[max длина значения модификатора]
//кавычки удаляются в готовых объявлениях

//что стоит доделать - число дни.часы - параметр 2 в описании после полей beforeprice, price, afterprice
//добавить beforedayshours, dayshours, afterdayshours
//до конца акции ... 
//параметр можно будет использовать как дни.часы, дни или часы
//Таймер обратного отсчета до завершения распродажи
//https://developers.google.com/adwords/scripts/docs/solutions/sale-countdown?hl=ru

//alert(JSON.stringify(ItemHeaders(query,'KeyWord','','Есть в наличии','23 грн.','Лучшая цена, бесплатная доставка',"Описание балалайки: " + query)));
//alert(JSON.stringify(ItemHeaders(query,'KeyWord','','Есть в наличии','23 грн.','Лучшая цена, бесплатная доставка')));
//alert(JSON.stringify(ItemHeaders(query,'KeyWord','','| Есть в наличии','23 грн. ','| Лучшая цена, бесплатная доставка.', ' | ' + query + ' | ', '| Звоните сейчас!',1)));
//alert(JSON.stringify(ItemHeaders(query,'KeyWord','','','3453.00грн.','Лучшая цена, бесплатная доставка!',query, 'Звоните сейчас!')));


//цена (если есть) добавляется во второй заголовок (если поместится) и в описание перед action, description и enddescription
//action (если есть) добавляется первой (после цены) в описание, затем description
//beforeprice (если есть) добавляется до цены в описание, затем action и description
//enddescription (если есть) добавляется после description
//startdescription (если есть) добавляется в самом начале description, перед остальными параметрами

//priceRound - Начиная с какой цены отбрасывать дробную часть цены

//pricetypeadd
//0 - не добавляем цену в заголовки и описание
//1 - пытаемся добавить цену в заголовки и описание
//2 - пытаемся добавить цену в заголовки, если не получилось, то добавляем в описание (не добавляем beforeprice, если не добавляем цену в описание)
//3 - пытаемся добавить цену в только в заголовки (не добавляем beforeprice)
//4 - пытаемся добавить цену в только в описание


//после beforeprice точка не ставится, что бы можно было вставить типа "По цене от"

//алгоритм рассчитан только на 2 заголовка

//любые параметры чистятся от нежелательных символов


//начальная переделка под адаптивные объявления
//!!!@@@@@@@@@@@@@@@

//	if ((!name)||(!name2)||(typeof name !== 'string')||(typeof name2 !== 'string'))
	if ((!name)||(!name2)||(!name3)||(typeof name !== 'string')||(typeof name2 !== 'string')||(typeof name3 !== 'string'))
		return;

	if (paths&&(typeof paths !== 'string'))
		return;


	if (needdescription&&(!description))
		return;

if (!pricetypeadd) {
//это важно (число а не undefined) для дальшейшего сравнения, иначе неверный результат
	pricetypeadd=0;
}



var changerArr = []
var changerFrom = {}
function replacerTO(match, p1, p2, p3, offset, string) {
//ищем массив параметров в формате {....}[max длина параметра], например {KeyWord:Наша Марка}[15]
//в каждом передаваемом параметре
//и готовим соотв хеш
//параметры должны отделяться пробелом или концом/началом строки
//name = name.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);
//формат замены :  [номер в массиве замены][\u00BF]+

	var num = Number(p3)
	//не меньше 3-х символов замены, не больше 100 замен
	if (num < 3) return match;

	if (notuseparams) {
		//убираем параметры для визуального восприятия
		var Change = p2.replace(/\{[^\{\:]+\:([^\}\{]*)\}/gm, '$1');
		//если осталась неизвестная замена без :
		Change = Change.replace(/\{[^\{]+\}/gm, '{ERROR!!!}');
	} else {

		//номер замены и его длина
		var next = String(changerArr.length);
		var nextlen = next.length;

		//для справки замена на всю длину пар-ра так
		//var Change = Array(p2.length + 1).join("\u00BF")
		//ниже с учетом длины номера замены
	

		var Change = next + Array(num + 1 - nextlen).join("\u00BF")

		changerArr[next] = p2;
		//этот хеш для возврата параметров назад
		changerFrom[Change] = p2;

	}


	return p1 + Change;

}

name = name.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);
name2 = name2.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);
startdescription = startdescription.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);
beforeprice = beforeprice.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);
action = action.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);
description = description.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);
enddescription = enddescription.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);







//	name = name.toLowerCase();

function replacer1(match, p1, p2, p3, offset, string) {
	if (/\d/.test(p2)) {
//если цифровые слова в скобках, то удаляем все в скобках
		return p1 + ' ' + p3;
	}
	if (/[^\s]\s+[^\s]/.test(p2)) {
//если несколько слов в скобках, то удаляем все в скобках
		return p1 + ' ' + p3;
	}
	return p1 + ' ' + p2 + ' ' + p3;
}



//удаляем из скобок текстовые слова, если их несколько и любые цифровые
	name = name.replace(/^([^\(]*)\(([^\)]*)\)(.*)$/, replacer1);

//чистим если остались скобки
	name = name.replace(/\(([^\)]*)\)/g, ' ');
	name = name.replace(/[\(\)]+/g, '');



//если есть запятые, точки или |, то разделяем по ним и выбираем слова (из первых двух) по наличию цифр или максимуму букв
	var qarr = name.split(/[\,\.\|](?![^\s\-\_\\\/\d]*[\-\_\\\/\d])/);
	if ((qarr.length > 1)&&qarr[0].length&&qarr[1].length&&((qarr[0].length + qarr[1].length) >= 10)) {
		var del = qarr[0].length/qarr[1].length;
		var koef = 10/13; // (koef должен быть < 1),  10 и 13 берем для базовой пропорции длины сравнения слов
		if ((del <= 1/koef)&&(del >= koef)) {
//если длина словосочетаний соизмерима, то ищем цифры в первую очередь
			if (/[\d]/.test(qarr[0])) {
				if (/[\d]/.test(qarr[1])) {
					if ((/^\s*[^\s\d]+\s[^\s]+/.test(qarr[0]))&&(/^\s*[^\s]*[\d][^\s]*\s[^\s]+/.test(qarr[1]))) {
						//если везде цифры, то если первое слово с цифрами (во втором блоке), а в первом блоке первое слово без цифр, то берем первый блок
						name=qarr[0];
					} else if (qarr[0].length > qarr[1].length) {
						name=qarr[0];
					} else {
						name=qarr[1];
					}
				} else {
					name=qarr[0];
				}
			} else if (/[\d]/.test(qarr[1])) {
				name=qarr[1];
			} else {
				if (qarr[0].length > qarr[1].length) {
					name=qarr[0];
				} else {
					name=qarr[1];
				}
			}
		} else {
//иначе по длине выбираем
			if ((/[\d]/.test(qarr[0]))&&(/^\s*[^\s\d]+\s[^\s]+/.test(qarr[0]))&&(/^\s*[^\s]*[\d][^\s]*\s[^\s]+/.test(qarr[1]))) {
				//если везде цифры, то если первое слово с цифрами (во втором блоке), а в первом блоке первое слово без цифр, то берем первый блок
				name=qarr[0];
			} else if (qarr[0].length > qarr[1].length) {
				name=qarr[0];
			} else {
				name=qarr[1];
			}
		}
	}


//требования для текстов
//https://support.google.com/adspolicy/answer/6021546?hl=ru
//https://support.google.com/google-ads/answer/1704389?hl=ru


	//заменяем специальные символы на пробел
	//name = name.replace(/[\!\|\>\*\<\^\=\~\`]+/g, ' ');
	//новый вариант
	name = name.replace(/[^\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\(\)\%]+/g, ' ');

//убираем везде кавычки - иначе гугл отклоняет объявления (лучше убрать, так как видел что разрешено Магазин "ЖЖУК", но у меня отклонено было что-то типа "simplest")
	name = name.replace(/[\'\"]+/g, '');

//убираем одинокие + - точки запятые
	name = name.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//	name = name.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

//убираем дубликаты точки запятые ;
	//name = name.replace(/([\!\|\.\,\;\-\+])(?:\s*[\!\|\.\,\;\-\+]+)+/g, '$1');
	name = name.replace(/([\!\|\.\,\;])(?:\s*[\!\|\.\,\;]+)+/g, '$1');
	//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
	//name = name.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
	//name = name.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
	name = name.replace(/([\|])(?:\s*[\|]+)+/g, '$1');


//обязательно чистим пробелы
	name = name.replace(/\s+/g, ' ');
	name = name.trim();

	if (/^\s*$/.test(name)) {
		return;
	}


function keywordString(string, keyword) {

//новая версия ( учитывает кавычки '"() и изменяет только буквенные слова в режимах Keyword и KeyWord)


	function replacerKeyWord(match, p1, p2, p3, p4, offset, string) {
		//p2 не undefined и p3 undefined  - признак что увеличивать одиночный символ, то есть это начало строки или один из .!|
		//p3 не undefined и p2 undefined - признак что не увеличивать одиночный символ
		//p4 - само слово
		if (typeof p2 == 'string') {
			//начало предложения
			//увеличиваем одиночный символ, как и любой другой
			var firstch = p4.toLowerCase().replace(/^([\"\'\(]*\s*[^\"\'\(\s]).*$/, '$1').toUpperCase();
			var fullchars = firstch + p4.toLowerCase().replace(/^[\"\'\(]*\s*[^\"\'\(\s](.*)$/, '$1');
		} else {
			//не увеличиваем одиночный символ без кавычек, а если слово или слово в кавычках или одиночный символ в кавычках то увеличиваем первый символ
			if (p4.length == 1) {
				//это одиночный символ без кавычек
				var fullchars = p4.toLowerCase();
			} else {
				//это слово или слово в кавычках или одиночный символ в кавычках, увеличиваем первый символ (с учетом символов кавычек)
				var firstch = p4.toLowerCase().replace(/^([\"\'\(]*\s*[^\"\'\(\s]).*$/, '$1').toUpperCase();
				var fullchars = firstch + p4.toLowerCase().replace(/^[\"\'\(]*\s*[^\"\'\(\s](.*)$/, '$1');
			}
		}
		return p1 + fullchars;
	}


	function replacerKeyword(match, p1, p2, p3, p4, offset, string) {
		if (typeof p2 == 'string') {
			var firstch = p4.toLowerCase().replace(/^([\"\'\(]*\s*[^\"\'\(\s]).*$/, '$1').toUpperCase();
			var fullchars = firstch + p4.toLowerCase().replace(/^[\"\'\(]*\s*[^\"\'\(\s](.*)$/, '$1');
		} else {
			var fullchars = p4.toLowerCase();
		}
		return p1 + fullchars;

	}


	if (keyword) {
		if (keyword == 'Keyword') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.replace(/(((?:^\s*)|(?:\.\s*)|(?:\!\s*)|(?:\|\s*))|([\,\s]+))([\"\'\(]*\s*[\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]+)(?=(?:(?:[\"\'\)\s\.\!\|\,])|(?:$)))/g, replacerKeyword);
		} else if (keyword == 'KeyWord') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.replace(/(((?:^\s*)|(?:\.\s*)|(?:\!\s*)|(?:\|\s*))|([\,\s]+))([\"\'\(]*\s*[\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]+)(?=(?:(?:[\"\'\)\s\.\!\|\,])|(?:$)))/g, replacerKeyWord);
		} else if (keyword == 'keyword') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.toLowerCase();
		} else if (keyword == 'KEYWORD') {
			if (string) //для экономии времени вычислений на пустой строке
				string = string.toUpperCase();
		}
		
	}

	return string;

}


        //отодвинуть от знаков окончания предложений новые предложения (не задействуем знак \u00BF , который исп. в модификаторах)
//	name = name.replace(/(?![\_\d]+)([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
//	name = name.replace(/(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
	name = name.replace(/([\u00C0-\u1FFF\u2C00-\uD7FF\wa-z]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FFa-z])/ig, '$1 $2');

	//убрать знаки препинания в начале строки
	name = name.replace(/^[\s\.\,\!]+/, '');

	name = keywordString(name, keyword);



function replacerTO2(match, p1, p2, offset, string) {
//ищем в name число и слово, например 12 мм
//и добавляем в соотв массивы changerArr и changerFrom (из replacerTO)
//формат замены :  [номер в массиве замены][\u00BF]+

	var num = p2.length
	//не меньше 3-х символов замены, не больше 100 замен
	if (num < 3) return match;

	//номер замены и его длина
	var next = String(changerArr.length);
	var nextlen = next.length;

	//для справки замена на всю длину пар-ра так
	//var Change = Array(p2.length + 1).join("\u00BF")
	//ниже с учетом длины номера замены
	

	var Change = next + Array(num + 1 - nextlen).join("\u00BF")

	changerArr[next] = p2;
	//этот хеш для возврата параметров назад
	changerFrom[Change] = p2;


	return p1 + Change;

}

//не переносим в следующий заголовок по отдельности число и слово (например 12 мм), также не удаляем ниже короткое слово (задействуем знак \u00BF , который исп. в модификаторах)
name = name.replace(/(^|\s)([\d\.\,]*\d\s[\u00C0-\u1FFF\u2C00-\uD7FFA-Za-z]+)(?=\s|$)/g, replacerTO2);



//https://support.google.com/google-ads/answer/1704389?hl=ru
//теперь создаем два Заголовка По 30 символов
//и два Пути По 15 символов в каждом
//Описание 1	90 символов

	var Headlines=[];
//максимальная длина поля
	var HeadlinesMax=30;
//сколько полей может быть
	var HeadlinesMaxNum=2;
	var HeadlinesN=0;
	var Descriptions=[];
//максимальная длина поля
	var DescriptionsMax=90;
//сколько полей может быть
	var DescriptionsMaxNum=2;
	var DescriptionsN=0;
	var Paths=[];
//максимальная длина поля
	var PathsMax=15;
//сколько полей может быть
	var PathsMaxNum=2;
	var PathsN=0;

if (headerTo1Line)
	HeadlinesMaxNum=1;

//создаем дубликат заголовков но без обратной замены слов, для проверки маленьких слов без цифр с учетом замен (модификаторов и replacerTO2)
	var HeadlinesTest=[];

//также выводим числовые параметры объявления для КС (в адвордс есть param1 и param2, соотв.номер в массиве идет с 0, отличается на 1, то есть, значение param1 = Params[0], значение param2 = Params[1])
//мы используем только param1 для цены
	var Params=[];

	var narr = name.split(/\s+/);
        for (var i = 0; i < narr.length; i++) {
		if (changerFrom[narr[i]]) {
			var changeword = changerFrom[narr[i]];
		} else {
			var changeword = narr[i];
		}
		if (!HeadlinesN) {
			if (narr[i].length <= HeadlinesMax) {
				Headlines[Headlines.length] = changeword;
				HeadlinesTest[HeadlinesTest.length] = narr[i];
				HeadlinesN = narr[i].length;
			} else {
				if (Headlines.length >= HeadlinesMaxNum)
					break;
//первое слово больше мах длины первого заголовка, выходим невозможно создать заголовки
				return;
			}
		} else {
			if (narr[i].length <= HeadlinesMax) {
				if ((narr[i].length + 1 + HeadlinesN) <= HeadlinesMax) {
					Headlines[Headlines.length - 1] += ' ' + changeword;
					HeadlinesTest[HeadlinesTest.length - 1] += ' ' + narr[i];
					HeadlinesN += narr[i].length + 1;
				} else {
					if (Headlines.length >= HeadlinesMaxNum)
						break;
					Headlines[Headlines.length] = changeword;
					HeadlinesTest[HeadlinesTest.length] = narr[i];
					HeadlinesN = narr[i].length;
				}
			} else {
				if (Headlines.length >= HeadlinesMaxNum)
					break;
//одно из следующих слов в запросе больше мах длины следующего заголовка, не выходим, обрезаем число заголовков
//				return;
				break;
			}
		}
	}


	if (Headlines.length) {
		////удаляем последнее маленькое слово без цифр, точки и пр. в последней строке, у которого меньше 4-х символов
		//Headlines[Headlines.length - 1] = Headlines[Headlines.length - 1].replace(/^(.*?)\s+[^\s\d\_\\\.]{1,3}\s*$/, '$1');

		var foundm = HeadlinesTest[HeadlinesTest.length - 1].match(/^(.*?)\s+([^\s\d\_\\\.\!\|]{1,3})\s*$/);
		if (foundm && foundm[1] && foundm[2]) {
			if (changerFrom[foundm[2]]) {
				var word = changerFrom[foundm[2]].toLowerCase();
			} else {
				var word = foundm[2].toLowerCase();
			}
			if (!shortWordsHash[word]) {
				//удаляем последнее маленькое слово без цифр, точки и пр. в последней строке, у которого меньше 4-х символов, если перед ним нет числа

				//если несколько букв и все большие, то не удаляем - (для KIA) например
				if (!((foundm[2] == foundm[2].toUpperCase())&&(foundm[2].length > 1))) {

					//если перед этим словом есть другое маленькое слово, то не удаляем - (для KIA) например
					var foundm2 = foundm[1].match(/^(.*?)\s+([^\s\d\_\\\.\!\|]{1,3})\s*$/);
					if (!(foundm2 && foundm2[1] && foundm2[2])) {

						//нужно в foundm[1] везде сделать замену обратно на слова
						var foundm1ch = '';
						var narr = foundm[1].split(/\s+/);
					        for (var i = 0; i < narr.length; i++) {
							if (changerFrom[narr[i]]) {
								var changeword = changerFrom[narr[i]];
							} else {
								var changeword = narr[i];
							}

							if (foundm1ch)
								foundm1ch += ' ';
							foundm1ch += changeword;


						}

						//теперь точно удаляем
						//Headlines[Headlines.length - 1] = foundm[1];
						Headlines[Headlines.length - 1] = foundm1ch;

					}
				}
			}
		}


		//убираем в конце каждого заголовка отдельные слова без цифр и букв (дальше будет разделитель или конец объявления для последнего) (не задействуем знак \u00BF , который исп. в модификаторах)

		Headlines[0] = Headlines[0].replace(/(?:^|(?:\s+))[^\s\d\u00C0-\u1FFF\u2C00-\uD7FFA-Za-z]+\s*$/, '');
		if ((!Headlines[0])&&(Headlines.length == 1)) {
			//нет заголовков, выходим
			return;
		}
	        for (var i = 1; i < Headlines.length; i++) {
			Headlines[i] = Headlines[i].replace(/(?:^|(?:\s+))[^\s\d\u00C0-\u1FFF\u2C00-\uD7FFA-Za-z]+\s*$/, '');
			if (!Headlines[i]) {
				//удаляем пустой заголовок
				Headlines.splice(i,1);
				i--;
			}
		}

	}



	if (noPaths) {
		var pathstmp = '';
	} else {


		if (paths) {

			//полностью повторяем обработку name (даже то что не нужно для путей, так на всякий случай)

			paths = paths.replace(/(^|\s)(\{[^\{]+\})\[(\d+)\](?=\s|$)/g, replacerTO);


//удаляем из скобок текстовые слова, если их несколько и любые цифровые
			paths = paths.replace(/^([^\(]*)\(([^\)]*)\)(.*)$/, replacer1);

//чистим если остались скобки
			paths = paths.replace(/\(([^\)]*)\)/g, ' ');
			paths = paths.replace(/[\(\)]+/g, '');



//если есть запятые, точки или |, то разделяем по ним и выбираем слова (из первых двух) по наличию цифр или максимуму букв
	var qarr = paths.split(/[\,\.\|](?![^\s\-\_\\\/\d]*[\-\_\\\/\d])/);
	if ((qarr.length > 1)&&qarr[0].length&&qarr[1].length&&((qarr[0].length + qarr[1].length) >= 10)) {
		var del = qarr[0].length/qarr[1].length;
		var koef = 10/13; // (koef должен быть < 1),  10 и 13 берем для базовой пропорции длины сравнения слов
		if ((del <= 1/koef)&&(del >= koef)) {
//если длина словосочетаний соизмерима, то ищем цифры в первую очередь
			if (/[\d]/.test(qarr[0])) {
				if (/[\d]/.test(qarr[1])) {
					if ((/^\s*[^\s\d]+\s[^\s]+/.test(qarr[0]))&&(/^\s*[^\s]*[\d][^\s]*\s[^\s]+/.test(qarr[1]))) {
						//если везде цифры, то если первое слово с цифрами (во втором блоке), а в первом блоке первое слово без цифр, то берем первый блок
						paths=qarr[0];
					} else if (qarr[0].length > qarr[1].length) {
						paths=qarr[0];
					} else {
						paths=qarr[1];
					}
				} else {
					paths=qarr[0];
				}
			} else if (/[\d]/.test(qarr[1])) {
				paths=qarr[1];
			} else {
				if (qarr[0].length > qarr[1].length) {
					paths=qarr[0];
				} else {
					paths=qarr[1];
				}
			}
		} else {
//иначе по длине выбираем
			if ((/[\d]/.test(qarr[0]))&&(/^\s*[^\s\d]+\s[^\s]+/.test(qarr[0]))&&(/^\s*[^\s]*[\d][^\s]*\s[^\s]+/.test(qarr[1]))) {
				//если везде цифры, то если первое слово с цифрами (во втором блоке), а в первом блоке первое слово без цифр, то берем первый блок
				paths=qarr[0];
			} else if (qarr[0].length > qarr[1].length) {
				paths=qarr[0];
			} else {
				paths=qarr[1];
			}
		}
	}



			//заменяем специальные символы на пробел
			//paths = paths.replace(/[\!\|\>\*\<\^\=\~\`]+/g, ' ');
			//новый вариант
			paths = paths.replace(/[^\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\(\)\%]+/g, ' ');

//убираем везде кавычки - иначе гугл отклоняет объявления (лучше убрать, так как видел что разрешено Магазин "ЖЖУК", но у меня отклонено было что-то типа "simplest")
			paths = paths.replace(/[\'\"]+/g, '');

//убираем одинокие + - точки запятые
			paths = paths.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//			paths = paths.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

//убираем дубликаты точки запятые ;
			//paths = paths.replace(/([\!\|\.\,\;\-\+])(?:\s*[\!\|\.\,\;\-\+]+)+/g, '$1');
			paths = paths.replace(/([\!\|\.\,\;])(?:\s*[\!\|\.\,\;]+)+/g, '$1');
			//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
			//paths = paths.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
			//paths = paths.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
			paths = paths.replace(/([\|])(?:\s*[\|]+)+/g, '$1');


//обязательно чистим пробелы
			paths = paths.replace(/\s+/g, ' ');
			paths = paths.trim();

			if (!/^\s*$/.test(paths)) {

//отодвинуть от знаков окончания предложений новые предложения (не задействуем знак \u00BF , который исп. в модификаторах)
//				paths = paths.replace(/(?![\_\d]+)([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
//				paths = paths.replace(/(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
				paths = paths.replace(/([\u00C0-\u1FFF\u2C00-\uD7FFa-z]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FFa-z])/ig, '$1 $2');

//убрать знаки препинания в начале строки
				paths = paths.replace(/^[\s\.\,\!]+/, '');

				paths = keywordString(paths, keyword);


//не переносим в следующий заголовок по отдельности число и слово (например 12 мм), также не удаляем ниже короткое слово (задействуем знак \u00BF , который исп. в модификаторах)
				paths = paths.replace(/(^|\s)([\d\.\,]*\d\s[\u00C0-\u1FFF\u2C00-\uD7FFA-Za-z]+)(?=\s|$)/g, replacerTO2);

			}

			var pathstmp = paths;
		} else {
			var pathstmp = name;
		}

		//удаляем параметры замены (могут быть неожиданности, если оставить)
		//var pathstmp = pathstmp.replace(/(^|\s)(\d+\u00BF+)(\s|$)/g, ' ');
		//pathstmp = pathstmp.trim();


		//если единичный символ - все удалить, начиная с него для path
		pathstmp = pathstmp.replace(/^(.*?)\s+[^\s]\s+.*$/, '$1');

	}


	var narr = pathstmp.split(/\s+/);


	//флаг что последнее - маленькое нецифровое слово
	var lastmflg=0
        for (var i = 0; i < narr.length; i++) {

		if (changerFrom[narr[i]]) {
			var changeword = changerFrom[narr[i]];
		} else {
			var changeword = narr[i];
		}


		if (!PathsN) {
			if (narr[i].length <= PathsMax) {
				Paths[Paths.length] = changeword;
				PathsN = narr[i].length;
			} else {
				break;
			}
		} else {
			if (narr[i].length <= PathsMax) {
				if ((narr[i].length + 1 + PathsN) <= PathsMax) {
					Paths[Paths.length - 1] += '_' + changeword;
					PathsN += narr[i].length + 1;
				} else {
					if (Paths.length >= PathsMaxNum)
						break;
					Paths[Paths.length] = changeword;
					PathsN = narr[i].length;
				}
			} else {
				break;
			}
		}
		if (!PathsN) {
			//если это первый элемент массива путей, то еще не включаем анализ маленьких нецифровых слов
			lastmflg=0;
		//} else if (narr[i].length < 5) {
		} else if ((!shortWordsHash[changeword.toLowerCase()])&&(narr[i].length < 5)) {
			if (/[\d\/]/.test(changeword)) {
				lastmflg=0;
			} else {
				lastmflg=narr[i].length;
			}
		} else {
			lastmflg=0;
		}

	}


	if (lastmflg) {
//если последнее - маленькое нецифровое слово, то смотрим в каком варианте выдачи оно больше
//то есть если в предыдущем варианте закончилось например на "по", 
//то возможно, если здесь мы возьмем первые два слова, то последнее (второе если есть) слово будет большей длины чем при первом варианте расчета
//задача - по возможности не допустить окончания путей на предлогах
		var lastmflg2 = 0
	        for (var i = 0; i < PathsMaxNum; i++) {
			if (changerFrom[narr[i]]) {
				var changeword = changerFrom[narr[i]];
			} else {
				var changeword = narr[i];
			}
			if (changeword) {
				if (!i) {
				//если это первый элемент массива путей, то еще не включаем анализ маленьких нецифровых слов
					lastmflg2=0;
				} else if (narr[i].length > PathsMax) {
					break;
				//} else if (narr[i].length < 5) {
				} else if ((!shortWordsHash[changeword.toLowerCase()])&&(narr[i].length < 5)) {
					if (/[\d\/]/.test(changeword)) {
						lastmflg2=0;
					} else {
//прекращаем поиск на первом нецифровом маленьком слове (что бы можно было оставить предыдущее, иначе все сброситься, так как у нас всего 2 слова)
//						break;  //лучше вообще-то не оставлять (это надо было в первом варианте анализировать, но надо ломать голову, а это не столь принципиально), например "Сумка Для Обуви На 2 Пары Мягкая с Рисунком"
						lastmflg2=narr[i].length;
					}
				} else {
					lastmflg2=0;
				}
			}
		}
		if (lastmflg&&(lastmflg <= 3)&&lastmflg2&&(lastmflg2 <= 3)) {
			//если оба варианта дают нецифровые слова длиной 3, 2 или 1 то вообще не указываем пути
			Paths = [];
		} else if ((!lastmflg2)||(lastmflg2 > lastmflg)) {
			Paths = [];
		        for (var i = 0; i < PathsMaxNum; i++) {
				if (changerFrom[narr[i]]) {
					var changeword = changerFrom[narr[i]];
				} else {
					var changeword = narr[i];
				}
				if (changeword) {
					if (!i) {
					//если это первый элемент массива путей, то еще не включаем анализ маленьких нецифровых слов
						Paths[i] = changeword;
					} else if (narr[i].length > PathsMax) {
						break;
					//} else if (narr[i].length < 5) {
					} else if ((!shortWordsHash[changeword.toLowerCase()])&&(narr[i].length < 5)) {
						if (/[\d\/]/.test(changeword)) {
							Paths[i] = changeword;
						} else {
//прекращаем поиск на первом нецифровом маленьком слове (что бы можно было оставить предыдущее, иначе все сброситься, так как у нас всего 2 слова)
//							break;  //лучше вообще-то не оставлять (это надо было в первом варианте анализировать, но надо ломать голову, а это не столь принципиально), например "Сумка Для Обуви На 2 Пары Мягкая с Рисунком"
							Paths[i] = changeword;
						}
					} else {
						Paths[i] = changeword;
					}
				}
			}
		}
	}



	//добавлено - путь точно не может содержать символы :.,/\ (возможно и какие-то другие)
	//поэтому заменяем их на _ и убираем дубликаты _ если появятся при замене
        for (var i = 0; i < Paths.length; i++) {
		Paths[i] = Paths[i].replace(/[\:\.\,\/\\\_]+/g, '_');
	}



	var cena = '';
	var currency = '';


	if (price) {

		//на всякий случай чистим теги (можно убрать, так как должно заходить без тегов)
		price = price.replace(/\<[^\>]\>/g, '');

		//чистим пробелы внутри цены (числа)
		//price = price.replace(/(?<=[\d\.\,])\s+(?=[\d\.\,])/g, ''); //просмотр назад не работает в гугле
		function pricereplacer(match, offset, string) {
			match = match.replace(/\s+/g, '');
			return match;
		}
		price = price.replace(/[\d\.\,][\d\.\,\s]+[\d\.\,]/g, pricereplacer);


		//делаем пробел между числом и валютой
		//price = price.replace(/([\d\.\,]+)(?=[^\s\d\.\,])/, '$1 ');

		price = price.toLowerCase();

		//разбиваем прайс на число и валюту и символ \.\,\!\| в конце
		var match = price.match(/([\d\.\,]+)\s*([^\d\s]*(?:\s*[\.\,\!\|])?)/);
		if (match && match[1]) {
			cena = match[1];
			currency = match[2];

			if (typeof priceRound == 'number') {
				if (Number(cena.replace(/[\s\,]+/g, '')) >= priceRound) {
					cena = cena.replace(/\..*$/, '');
				}
			}


			price = cena;

			if (currency) {
				//удаляем специальные символы
				currency = currency.replace(/[\>\*\<\^\=\~\`]+/g, '');

				//убираем дубликаты точки запятые ;
				//currency = currency.replace(/([\!\.\,\;\-\+])(?:\s*[\!\.\,\;\-\+]+)+/g, '$1');
				currency = currency.replace(/([\!\.\,\;])(?:\s*[\!\.\,\;]+)+/g, '$1');
				//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
				//currency = currency.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
				//currency = currency.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
				currency = currency.replace(/([\|])(?:\s*[\|]+)+/g, '$1');

				currency = currency.replace(/\s+(?![\.\,\!\|])/g, '');


			}
			if (currency)
				price += ' ' + currency;


		} else {
			price = '';
		}


	}

	if (price) {
		price = price.replace(/\s+/g, ' ');
		price = price.trim();
	}


	var priceParam = '';

	if (!price) {
		beforeprice = '';
	} else {


		if (notuseparams) {
			//убираем параметр для визуального восприятия
			priceParam = String(cena);
		} else {
			priceParam = '{param1:' + cena + '}';
		}

//для заголовка формируем отдельные параметры priceHeader(price) priceParamHeader(priceParam)
		var priceParamHeader = priceParam;
		var priceHeader = cena;

		//для заголовка удаляем в конце [\,\!\|], для описания оставляем
		var currencyHeader = currency.replace(/\s*[\,\!\|]$/, '');
		if (currencyHeader) {
			priceHeader += ' ' + currencyHeader;
			priceParamHeader += ' ' + currencyHeader;
		}

		//для описания оставляем в конце [\,\!\|]
		if (currency)
			priceParam += ' ' + currency;


		var addpricetoHeader = 0;

		var addpricetoAny = 0;

		if ((!headerTo1Line)&&(pricetypeadd > 0)&&(pricetypeadd < 4)&&((Headlines.length == 1)||((priceHeader.length + 3 + Headlines[1].length) <= HeadlinesMax))) {
//добавляем во второй заголовок цену (длину учитывает только priceHeader а не priceParamHeader)
			if (!Headlines[1]) {
//				Headlines[1] = priceHeader;
				Headlines[1] = priceParamHeader;
				
			} else {
//				Headlines[1] += ' | ' + priceHeader;
				Headlines[1] += ' | ' + priceParamHeader;
			}

			addpricetoHeader = 1;
			addpricetoAny = 1;


		}


		if (!((pricetypeadd == 4)||(pricetypeadd == 1)||((pricetypeadd == 2)&&(!addpricetoHeader)))) {
			//варианты, когда не добавлять цену в описание

			priceParam = '';
			beforeprice = '';

			//так на всякий случай, хотя не требуется
			cena = '';
			currency = '';

		} else {
			addpricetoAny = 1;
		}


		if (addpricetoAny)
		//номер параметра соответсвует его номеру в массиве + 1
			Params[0]=cena;


	}





	if (description) {
		//заменяем специальные символы на пробел
		//description = description.replace(/[\>\*\<\^\=\~\`]+/g, ' ');
		//новый вариант
		description = description.replace(/[^\!\|\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\%]+/g, ' ');


		//убираем одинокие + - точки запятые
		description = description.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
		//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//		description = description.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

		//убираем дубликаты точки запятые ;
		//description = description.replace(/([\!\.\,\;\-\+])(?:\s*[\!\.\,\;\-\+]+)+/g, '$1');
		description = description.replace(/([\!\.\,\;])(?:\s*[\!\.\,\;]+)+/g, '$1');
		//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
		//description = description.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
		//description = description.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
		description = description.replace(/([\|])(?:\s*[\|]+)+/g, '$1');
	}


	if (action) {
		//заменяем специальные символы на пробел
		//action = action.replace(/[\>\*\<\^\=\~\`]+/g, ' ');
		//новый вариант
		action = action.replace(/[^\!\|\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\%]+/g, ' ');

		//убираем одинокие + - точки запятые
		action = action.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
		//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//		action = action.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

		//убираем дубликаты точки запятые ;
		//action = action.replace(/([\!\.\,\;\-\+])(?:\s*[\!\.\,\;\-\+]+)+/g, '$1');
		action = action.replace(/([\!\.\,\;])(?:\s*[\!\.\,\;]+)+/g, '$1');
		//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
		//action = action.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
		//action = action.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
		action = action.replace(/([\|])(?:\s*[\|]+)+/g, '$1');
	}

	if (beforeprice) {
		//заменяем специальные символы на пробел
		//beforeprice = beforeprice.replace(/[\>\*\<\^\=\~\`]+/g, ' ');
		//новый вариант
		beforeprice = beforeprice.replace(/[^\!\|\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\%]+/g, ' ');

		//убираем одинокие + - точки запятые
		beforeprice = beforeprice.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
		//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//		beforeprice = beforeprice.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

		//убираем дубликаты точки запятые ;
		//beforeprice = beforeprice.replace(/([\!\.\,\;\-\+])(?:\s*[\!\.\,\;\-\+]+)+/g, '$1');
		beforeprice = beforeprice.replace(/([\!\.\,\;])(?:\s*[\!\.\,\;]+)+/g, '$1');
		//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
		//beforeprice = beforeprice.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
		//beforeprice = beforeprice.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
		beforeprice = beforeprice.replace(/([\|])(?:\s*[\|]+)+/g, '$1');
	}



	if (startdescription) {
		//заменяем специальные символы на пробел
		//startdescription = startdescription.replace(/[\>\*\<\^\=\~\`]+/g, ' ');
		//новый вариант
		startdescription = startdescription.replace(/[^\!\|\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\%]+/g, ' ');

		//убираем одинокие + - точки запятые
		startdescription = startdescription.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
		//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//		startdescription = startdescription.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

		//убираем дубликаты точки запятые ;
		//startdescription = startdescription.replace(/([\!\.\,\;\-\+])(?:\s*[\!\.\,\;\-\+]+)+/g, '$1');
		startdescription = startdescription.replace(/([\!\.\,\;])(?:\s*[\!\.\,\;]+)+/g, '$1');
		//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
		//startdescription = startdescription.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
		//startdescription = startdescription.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
		startdescription = startdescription.replace(/([\|])(?:\s*[\|]+)+/g, '$1');
	}


	if (enddescription) {
		//заменяем специальные символы на пробел
		//enddescription = enddescription.replace(/[\>\*\<\^\=\~\`]+/g, ' ');
		//новый вариант
		enddescription = enddescription.replace(/[^\!\|\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\%]+/g, ' ');

		//убираем одинокие + - точки запятые
		enddescription = enddescription.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
		//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//		enddescription = enddescription.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

		//убираем дубликаты точки запятые ;
		//enddescription = enddescription.replace(/([\!\.\,\;\-\+])(?:\s*[\!\.\,\;\-\+]+)+/g, '$1');
		enddescription = enddescription.replace(/([\!\.\,\;])(?:\s*[\!\.\,\;]+)+/g, '$1');
		//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
		//enddescription = enddescription.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
		//enddescription = enddescription.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
		enddescription = enddescription.replace(/([\|])(?:\s*[\|]+)+/g, '$1');
	}




	if (!description) {
		if (description === null)
			description = name;
	} else {
		description = description.replace(/\s+/g, ' ');
		description = description.trim();
		description = keywordString(description, keyword);
	}



//должен остаться один восклицательный знак, в name их удалили раньше
	if (startdescription&&(/[\!]/.test(startdescription))) {
		if (beforeprice)
			beforeprice = beforeprice.replace(/[\!]/g, '.');
		if (price)
			price = price.replace(/[\!]/g, '.');
		if (description)
			description = description.replace(/[\!]/g, '.');
		if (enddescription)
			enddescription = enddescription.replace(/[\!]/g, '.');
		if (action)
			action = action.replace(/[\!]/g, '.');
	} else if (beforeprice&&(/[\!]/.test(beforeprice))) {
		if (price)
			price = price.replace(/[\!]/g, '.');
		if (description)
			description = description.replace(/[\!]/g, '.');
		if (enddescription)
			enddescription = enddescription.replace(/[\!]/g, '.');
		if (action)
			action = action.replace(/[\!]/g, '.');
	} else if (price&&(/[\!]/.test(price))) {
		if (description)
			description = description.replace(/[\!]/g, '.');
		if (enddescription)
			enddescription = enddescription.replace(/[\!]/g, '.');
		if (action)
			action = action.replace(/[\!]/g, '.');
	} else if (action&&(/[\!]/.test(action))) {
		if (description)
			description = description.replace(/[\!]/g, '.');
		if (enddescription)
			enddescription = enddescription.replace(/[\!]/g, '.');
	} else if (description&&(/[\!]/.test(description))) {
		if (enddescription)
			enddescription = enddescription.replace(/[\!]/g, '.');
	}





	if (enddescription) {


		enddescription = enddescription.replace(/\s+/g, ' ');
		enddescription = enddescription.trim();
	}

	if (enddescription) {

//enddescription добавляется или не добавляется целиком ниже
		enddescription = keywordString(enddescription, keyword);
		if (description) {
/*
//так как enddescription добавляется сразу в массив, то другой алгоритм
			if (/[\.\,\|\!]\s*$/.test(description)) {
				enddescription = ' ' + enddescription;
			} else {
				enddescription = '. ' + enddescription;
			}
*/
			if (!(/[\.\,\|\!]\s*$/.test(description)))
				description += '.'
		}

	}



	if (action) {
		action = action.replace(/\s+/g, ' ');
		action = action.trim();
	}

	if (action) {

		action = keywordString(action, keyword);

		if (description) {
			if ((/[\.\,\|\!]\s*$/.test(action))||(/^\s*[\.\,\|\!]/.test(description))) {
				description = action + ' ' + description;
			} else {
				description = action + '. ' + description;
			}
		} else {
			if (/[\.\,\|\!]\s*$/.test(action)) {
				description = action;
			} else {
				description = action + '.';
			}
		}
	}


	if (price&&priceParam) {
		//можно добавлять цену в описание
		//заменяем прайс на вставку для последующей замены в description
		var priceChange = Array(price.length + 1).join('`')

//добавляем в описание цену (точнее вставку, которую потом заменим на цену, это если цена не помещается в описание)
		if (description) {
			if ((/[\.\,\|\!]\s*$/.test(price))||(/^\s*[\.\,\|\!]/.test(description))) {
				description = priceChange + ' ' + description;
			} else {
				description = priceChange + ', ' + description;
			}
		} else {
			description = priceChange;
		}
	}


	if (beforeprice) {
		beforeprice = beforeprice.replace(/\s+/g, ' ');
		beforeprice = beforeprice.trim();
	}

	if (beforeprice) {
		beforeprice = keywordString(beforeprice, keyword);
		if (description) {
//			if ((/[\.\,\|\!]\s*$/.test(beforeprice))||(/^\s*[\.\,\|\!]/.test(description))) {
				description = beforeprice + ' ' + description;
//			} else {
//				description = beforeprice + '. ' + description;
//			}
		} else {
//			if (/[\.\,\|\!]\s*$/.test(beforeprice)) {
				description = beforeprice;
//			} else {
//				description = beforeprice + '.';
//			}
		}
	}


	if (startdescription) {
		startdescription = startdescription.replace(/\s+/g, ' ');
		startdescription = startdescription.trim();
	}

	if (startdescription) {
		startdescription = keywordString(startdescription, keyword);
		if (description) {
			if ((/[\.\,\|\!]\s*$/.test(startdescription))||(/^\s*[\.\,\|\!]/.test(description))) {
				description = startdescription + ' ' + description;
			} else {
				description = startdescription + '. ' + description;
			}
		} else {
			if (/[\.\,\|\!]\s*$/.test(startdescription)) {
				description = startdescription;
			} else {
				description = startdescription + '.';
			}
		}
	}


//убираем везде кавычки - иначе гугл отклоняет объявления (лучше убрать, так как видел что разрешено Магазин "ЖЖУК", но у меня отклонено было что-то типа "simplest")
	description = description.replace(/[\'\"]+/g, '');

//прижимаем влево одинокие точки запятые ! во всем описании
	description = description.replace(/(?:(?:^\s*)|(?:\s+))([\.\,\!])(?:\s*[\.\,\!])*(?=(?:\s+)|(?:\s*$))/g, '$1');
//убираем дубликаты точки запятые ; во всем описании (enddescription сюда не попадает)
	//description = description.replace(/([\!\.\,\;\-\+])(?:\s*[\!\.\,\;\-\+]+)+/g, '$1');
	description = description.replace(/([\!\.\,\;])(?:\s*[\!\.\,\;]+)+/g, '$1');
	//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
	//description = description.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
	//description = description.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
	description = description.replace(/([\|])(?:\s*[\|]+)+/g, '$1');


        //отодвинуть от знаков окончания предложений новые предложения (не задействуем знак \u00BF , который исп. в модификаторах)
//	description = description.replace(/(?![\_\d]+)([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
//	description = description.replace(/(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
	description = description.replace(/([\u00C0-\u1FFF\u2C00-\uD7FFa-z]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FFa-z])/ig, '$1 $2');
	//убрать знаки препинания в начале строки
	description = description.replace(/^[\s\.\,\!]+/, '');
	//продублировать keyword на всем описании (по сути это сделано из-за строк выше), а другие keyword выше оставил из-за модификаторов объявлений
	description = keywordString(description, keyword);



	var narr = description.split(/\s+/);
	if (enddescription) {

//убираем везде кавычки - иначе гугл отклоняет объявления (лучше убрать, так как видел что разрешено Магазин "ЖЖУК", но у меня отклонено было что-то типа "simplest")
		enddescription = enddescription.replace(/[\'\"]+/g, '');

//убираем дубликаты точки запятые ; в enddescription
		//if (/[\!\.\,\;\-\+]\s*$/.test(description))
		//	enddescription = enddescription.replace(/^(?:\s*[\!\.\,\;\-\+]+\s*)+/, '');
		if (/[\!\.\,\;]\s*$/.test(description))
			enddescription = enddescription.replace(/^(?:\s*[\!\.\,\;]+\s*)+/, '');
		//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
		//if (/[\+]\s*$/.test(description))
		//	enddescription = enddescription.replace(/^(?:\s*[\+]+\s*)+/, '');
		//if (/[\-]\s*$/.test(description))
		//	enddescription = enddescription.replace(/^(?:\s*[\-]+\s*)+/, '');
		if (/[\|]\s*$/.test(description))
			enddescription = enddescription.replace(/^(?:\s*[\|]+\s*)+/, '');

//прижимаем влево одинокие точки запятые !
		enddescription = enddescription.replace(/(?:(?:^\s*)|(?:\s+))([\.\,\!])[\.\,\!]*(?=(?:\s+)|(?:\s*$))/g, '$1');

	        //отодвинуть от знаков окончания предложений новые предложения (не задействуем знак \u00BF , который исп. в модификаторах)
//		enddescription = enddescription.replace(/(?![\_\d]+)([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
//		enddescription = enddescription.replace(/(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
		enddescription = enddescription.replace(/([\u00C0-\u1FFF\u2C00-\uD7FFa-z]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FFa-z])/ig, '$1 $2');
		//убрать знаки препинания в начале строки
		enddescription = enddescription.replace(/^[\s\.\,\!]+/, '');


		enddescription = keywordString(enddescription, keyword);
		if (!needafterdescription)
			narr[narr.length] = enddescription;
	}

	var enddescriptionlength = enddescription.length
	if (needafterdescription) {
		//не может быть вставлено никогда
		if (enddescriptionlength > DescriptionsMax)
			return;
		if (enddescriptionlength) {
			enddescriptionlength += 3 //если задан needafterdescription, то добавляется пробел перед afterdescription и может быть добавлено ' |' (если нет точки в конце описания)
		} else {
			enddescriptionlength = 0 //если не задан afterdescription, то ничего не добавляем
						//нельзя добавлять точку, строка могла быть неудачно обрезана здесь или еще в парсере фида
		}
	}

        for (var i = 0; i < narr.length; i++) {
		if (changerFrom[narr[i]]) {
			var changeword = changerFrom[narr[i]];
		} else {
			var changeword = narr[i];
		}
		if (needafterdescription) {

			//контроль перехода на последний элемент 
			//обрезание массива, если попадается слишком длинное слово

			if (DescriptionsMaxNum < 2) {
				//только одна строка, постоянно контролируем размер с учетом enddescriptionlength
				if (!DescriptionsN) {
					if (narr[i].length + enddescriptionlength <= DescriptionsMax) {
						//создание новой строки описания объявления
						if (priceParam) {
							Descriptions[Descriptions.length] = changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length] = changeword;
						}
						DescriptionsN = narr[i].length;
					} else {
//завершаем, если первое слово не поместится вместе с enddescription
						break;
					}
				} else {

					if (narr[i].length + enddescriptionlength <= DescriptionsMax) {
						if ((narr[i].length + 1 + DescriptionsN + enddescriptionlength) <= DescriptionsMax) {
							if (priceParam) {
								Descriptions[Descriptions.length - 1] += ' ' + changeword.replace(/\`+/g, priceParam);
							} else {
								Descriptions[Descriptions.length - 1] += ' ' + changeword;
							}
							DescriptionsN += narr[i].length + 1;
						} else {
							//создание новой строки описания объявления, выходим
							break;
						}
					} else {
//завершаем, если следующее слово не поместится вместе с enddescription
						break;
					}

				}

			} else if (!Descriptions.length) {
				//еще нет элементов, но здесь и дальше везде строк больше одной
				//поэтому здесь нет еще контроля размера новой строки с учетом enddescriptionlength
				//так как нет еще элементов
				//его функция - создать первый элемент

				if (narr[i].length <= DescriptionsMax) {
					//создание новой строки описания объявления
					if (priceParam) {
						Descriptions[Descriptions.length] = changeword.replace(/\`+/g, priceParam);
					} else {
						Descriptions[Descriptions.length] = changeword;
					}
					DescriptionsN = narr[i].length;
				} else {
//обрезаем массив, если попалось слишком длинного слова
					break;
				}



			} else if (Descriptions.length == DescriptionsMaxNum - 1) {
				//предпоследний элемент, контроль размера новой строки с учетом enddescriptionlength
				if (narr[i].length <= DescriptionsMax) {
					if ((narr[i].length + 1 + DescriptionsN) <= DescriptionsMax) {
						if (priceParam) {
							Descriptions[Descriptions.length - 1] += ' ' + changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length - 1] += ' ' + changeword;
						}
						DescriptionsN += narr[i].length + 1;
					} else {
						//создание новой строки описания объявления
						//завершаем, если следующее слово не поместится вместе с enddescription
						if (narr[i].length + enddescriptionlength > DescriptionsMax) {
							break;
						}
						if (priceParam) {
							Descriptions[Descriptions.length] = changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length] = changeword;
						}
						DescriptionsN = narr[i].length;
					}
				} else {
//обрезаем массив, если попалось слишком длинного слова
					break;
				}


			} else if (Descriptions.length < DescriptionsMaxNum - 1) {

				//не предпоследний элемент и не последний
				if (narr[i].length <= DescriptionsMax) {
					if ((narr[i].length + 1 + DescriptionsN) <= DescriptionsMax) {
						if (priceParam) {
							Descriptions[Descriptions.length - 1] += ' ' + changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length - 1] += ' ' + changeword;
						}
						DescriptionsN += narr[i].length + 1;
					} else {
						//создание новой строки описания объявления
						if (priceParam) {
							Descriptions[Descriptions.length] = changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length] = changeword;
						}
						DescriptionsN = narr[i].length;
					}
				} else {
//обрезаем массив, если попалось слишком длинного слова
					break;
				}


			} else {
				//последний элемент, кроме случая массива из одной строки
				if (narr[i].length + enddescriptionlength <= DescriptionsMax) {
					if ((narr[i].length + 1 + DescriptionsN + enddescriptionlength) <= DescriptionsMax) {
						if (priceParam) {
							Descriptions[Descriptions.length - 1] += ' ' + changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length - 1] += ' ' + changeword;
						}
						DescriptionsN += narr[i].length + 1;
					} else {
						//создание новой строки описания объявления, выходим
						break;
					}
				} else {
//завершаем, если следующее слово не поместится вместе с enddescription
					break;
				}


			}


		} else {
			//обычная обработка

			if (!DescriptionsN) {
				if (narr[i].length <= DescriptionsMax) {
					//создание новой строки описания объявления
					if (priceParam) {
						Descriptions[Descriptions.length] = changeword.replace(/\`+/g, priceParam);
					} else {
						Descriptions[Descriptions.length] = changeword;
					}
					DescriptionsN = narr[i].length;
				} else {
					if (Descriptions.length >= DescriptionsMaxNum)
						break;
//первое слово больше мах длины первого описания, выходим невозможно создать описания
					return;
				}
			} else {
				if (narr[i].length <= DescriptionsMax) {
					if ((narr[i].length + 1 + DescriptionsN) <= DescriptionsMax) {
						if (priceParam) {
							Descriptions[Descriptions.length - 1] += ' ' + changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length - 1] += ' ' + changeword;
						}
						DescriptionsN += narr[i].length + 1;
					} else {
						//создание новой строки описания объявления
						if (Descriptions.length >= DescriptionsMaxNum)
							break;
						if (priceParam) {
							Descriptions[Descriptions.length] = changeword.replace(/\`+/g, priceParam);
						} else {
							Descriptions[Descriptions.length] = changeword;
						}
						DescriptionsN = narr[i].length;
					}
				} else {
					if (Descriptions.length >= DescriptionsMaxNum)
						break;
//одно из следующих слов в запросе больше мах длины следующего описания, не выходим, обрезаем число описаний
//					return;
					break;
				}
			}


		}

	}


function replacerShort1(match, p1, p2, offset, string) {
	//word - короткое слово, которое нужно удалить
	if (changerFrom[p2]) {
		var word = changerFrom[p2].toLowerCase();
	} else {
		var word = p2.toLowerCase();
	}
	if (shortWordsHash[word]) {
		//нельзя удалять, оно занесено в список реальных слов
		return match;
	} else {
		//можно удалять
		return p1;
	}
}
	if (Descriptions.length) {
		var elNumb = Descriptions.length - 1;
		//удаляем последнее маленькое слово без цифр, точки и пр. в последней строке, у которого меньше 4-х символов, если перед ним нет числа
		if (/^(.*?)\s+[^\s\d\_\\\.\!\|]{1,3}\s*$/.test(Descriptions[elNumb]))
			//Descriptions[elNumb] = Descriptions[elNumb].replace(/^(.*?)\s+[^\s\d\_\\\.\!\|]{1,3}\s*$/, '$1');
			Descriptions[elNumb] = Descriptions[elNumb].replace(/^(.*?)\s+([^\s\d\_\\\.\!\|]{1,3})\s*$/, replacerShort1);
		Descriptions[elNumb] = Descriptions[elNumb].replace(/[\s\:\,\/\\\_]+$/, '');
	}


	if (needafterdescription) {
		//добавляем enddescription

		if (changerFrom[enddescription]) {
			var changeword = changerFrom[enddescription];
		} else {
			var changeword = enddescription;
		}

		//строки описания объединяются единым потоком, поэтому визуально склеиваются,
		//но если есть следующая строка, то гугл автоматически ставит дополнительно точку в конце предыдущей (если там нет точки или !)
		if (DescriptionsN&&(DescriptionsN + enddescriptionlength <= DescriptionsMax)) {
			//вставляем в текущий элемент

			var elNumb = Descriptions.length - 1;


//двоеточие нельзя добавлять! гугл не пропустит, используем |

			if (enddescription) {

				if (!(/[\.\!\|]$/.test(Descriptions[elNumb]))) {

					//нет окончания предложения, значит обрезана строка
					//так как была точка обязательно вставлена выше (если есть enddescription)
					//если нет точки, может коряво отображаться строка неоконченного предложения, ищем точку где-нибудь

					var newstr = Descriptions[elNumb].match('^.+[\\.\\!\\|](?=\\s|$)')
					if (newstr !== null) {
						Descriptions[elNumb] = newstr[0];
					} else {
						Descriptions[elNumb] += ' |';
					}
				}


			}

			if (priceParam) {
				Descriptions[elNumb] += ' ' + changeword.replace(/\`+/g, priceParam);
			} else {
				Descriptions[elNumb] += ' ' + changeword;
			}
		} else {
			//создаем новый, гугл автоматом поставит точку в предыдущей строке (если там нет .!)
			if (priceParam) {
				Descriptions[Descriptions.length] = changeword.replace(/\`+/g, priceParam);
			} else {
				Descriptions[Descriptions.length] = changeword;
			}
		}
	}



	if (Headlines.length == 1) {
		//вставляем name2 в пустой 2-й заголовок (2-й заголовок обязателен)


		var HeadlinesN2=0;

/*

2-й заголовок практически не обрабатываем, убрал все лишнее

	        //отодвинуть от знаков окончания предложений новые предложения (не задействуем знак \u00BF , который исп. в модификаторах)
//		name2 = name2.replace(/(?![\_\d]+)([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
//		name2 = name2.replace(/(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w]\s*[\.\!\,])(?![\_\d])([\u00C0-\u1FFF\u2C00-\uD7FF\w])/g, '$1 $2');
		name2 = name2.replace(/([\u00C0-\u1FFF\u2C00-\uD7FFa-z]\s*[\.\!\,])([\u00C0-\u1FFF\u2C00-\uD7FFa-z])/ig, '$1 $2');

*/
		//убрать знаки препинания в начале строки
		name2 = name2.replace(/^[\s\.\,\!]+/, '');


/*

		name2 = keywordString(name2, keyword);
*/


		//заменяем специальные символы на пробел
		//name2 = name2.replace(/[\!\|\>\*\<\^\=\~\`]+/g, ' ');
		//новый вариант
		name2 = name2.replace(/[^\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\(\)\%]+/g, ' ');


/*

//убираем везде кавычки - иначе гугл отклоняет объявления (лучше убрать, так как видел что разрешено Магазин "ЖЖУК", но у меня отклонено было что-то типа "simplest")
		name2 = name2.replace(/[\'\"]+/g, '');
*/


//убираем одинокие + - точки запятые
		name2 = name2.replace(/(?:(?:^\s*)|(?:\s))[\+\-\.\,]+(?=(?:\s)|(?:\s*$))/g, '');
//убираем одинокие + - точки запятые внутри текста - (так не стоит делать)
//		name2 = name2.replace(/(?:(?!^\s*)(?:\s))[\+\-\.\,]+(?=(?:\s)(?!\s*$))/g, '');

//убираем дубликаты точки запятые ;
		//name2 = name2.replace(/([\!\|\.\,\;\-\+])(?:\s*[\!\|\.\,\;\-\+]+)+/g, '$1');
		name2 = name2.replace(/([\!\|\.\,\;])(?:\s*[\!\|\.\,\;]+)+/g, '$1');
		//здесь нельзя, например антифириз G12++ (можно откорректировать в настройке задания, если нужно)
		//name2 = name2.replace(/([\+])(?:\s*[\+]+)+/g, '$1');
		//name2 = name2.replace(/([\-])(?:\s*[\-]+)+/g, '$1');
		name2 = name2.replace(/([\|])(?:\s*[\|]+)+/g, '$1');

//обязательно чистим пробелы
		name2 = name2.replace(/\s+/g, ' ');
		name2 = name2.trim();


		if (/^\s*$/.test(name2)) {
			return;
		}


/*
		name2 = keywordString(name2, keyword);

*/

		var Headline2 = ''

		var narr = name2.split(/\s+/);
        	for (var i = 0; i < narr.length; i++) {
			if (changerFrom[narr[i]]) {
				var changeword = changerFrom[narr[i]];
			} else {
				var changeword = narr[i];
			}
			if (!HeadlinesN2) {
				if (narr[i].length <= HeadlinesMax) {
					Headline2 = changeword;
					HeadlinesN2 = narr[i].length;
				} else {
					return;
				}
			} else {
				if (narr[i].length <= HeadlinesMax) {
					if ((narr[i].length + 1 + HeadlinesN2) <= HeadlinesMax) {
						Headline2 += ' ' + changeword;
						HeadlinesN2 += narr[i].length + 1;
					} else {
						break;
					}
				} else {
					break;
				}
			}
		}


		Headlines[1] = Headline2;


	}


//начальная переделка под адаптивные объявления
//!!!@@@@@@@@@@@@@@@

	if (Headlines.length == 2) {
		//вставляем без проверок заранее сформированную name3 в пустой 3-й заголовок (3-й заголовок обязателен)
		Headlines[2] = name3;
	}



	if (Descriptions.length == 1) {
//нужно разбить на 2 строки, 2-я обязательна


		var found = Descriptions[0].match(/^(.*)(\{[^\}\{]+\}[\s\.\!]*)$/);
		if (found && found[1] && found[2]) {
//проверяем на наличие в конце строки фигурных скобок {....}
			Descriptions[0] = found[1].trim()
			Descriptions[1] = found[2]
		} else {
//проверяем на наличие в середине строки символов .! (но не внутри фигурных скобок)
			var found = Descriptions[0].match(/^(.*[\.\!])(?![ ]+[^\}\{]+\})([ ]+[^\s].*)$/);
			if (found && found[1] && found[2]) {
				Descriptions[0] = found[1]
				Descriptions[1] = found[2].trim()
			} else {
//проверяем на наличие в середине строки символа , (но не внутри фигурных скобок)
//удаляем запятую, гугл поставит точку
				var found = Descriptions[0].match(/^(.*)[\,](?![ ]+[^\}\{]+\})([ ]+[^\s].*)$/);
				if (found && found[1] && found[2]) {
					Descriptions[0] = found[1].trim()
					Descriptions[1] = found[2].trim()
				} else {
//проверяем на наличие в середине строки слова из 3-х символов (но не внутри фигурных скобок)
					var found = Descriptions[0].match(/^(.*[^\s][^\s][^\s])(?![ ]+[^\}\{]+\})([ ]+[^\s].*)$/);
					if (found && found[1] && found[2]) {
						Descriptions[0] = found[1]
						Descriptions[1] = found[2].trim()
					} else {
						return
					}
				}
			}
		}

		if (/^\s*$/.test(Descriptions[0])) {
			return;
		}
		if (/^\s*$/.test(Descriptions[1])) {
			return;
		}

	}

	
//Окончательная проверка - заголовков не менее 3-х, описаний не менее 2-x

//if (Headlines.length < 2)
if (Headlines.length < 3)
	return

//if (!Descriptions.length)
if (Descriptions.length < 2)
	return


//alert(name)
//alert(JSON.stringify(Headlines));
//alert(JSON.stringify(Descriptions));
//alert(JSON.stringify(Paths));

return {Headlines: Headlines, Descriptions: Descriptions, Paths: Paths, Params:Params};



}





function ItemKeys(name,shortWordsHash,mixtype,maxNodigaddkeyNum,requiredkeys,notReqrequiredkeys,onekeys) {
	//чистим название товара, по аналогии с запросами getkey clearQuery
	//затем разбираем на массивы ключевых слов (по 2 и по 3 слова в КС, цифры и пр.)

//методом тыка доработал, надеюсь, правильную генерацию КС

//alert(JSON.stringify(ItemKeys(query,'kiass, Samsung')));
//alert(JSON.stringify(ItemKeys(query)));
//alert(JSON.stringify(ItemKeys(query,'kia, киа, hyundai,хендай')));


//requiredkeys - строка обязательных слов (через пробел и/или запятую), которые должны входить в КС (по одному значению)
//обязательны при отсутствии флага notReqrequiredkeys, иначе это просто дополнительные но необязательные слова во всех КС
//зачем это нужно:
//если продукт name относится ко всем этим маркам/словам
//если КС получатся слишком общие на основе названия name
//например name - это фильтр масляный, который мы продаем только для 'kia киа hyundai хендай', а в названии этого нет
//requiredkeys не добавляются к массиву цифровых КС


//'maxNodigaddkeyNum': 3, //большее из чисел кол-ва слов в КС (для не ЦБКС) в addkeystype (допускается от 3 и больше)
			//сколько будет добавлено слов в КС вместо 3-х и 2-х в addkeystype (по умолчанию 3, то есть 3 и 2)
			//то есть, если 4, то вместо 3-х и 2-х используется соответственно 4 и 3 в addkeystype
			// если 5, то вместо 3-х и 2-х используется соответственно 5 и 4 в addkeystype
			//и т.д.
			//используется если слишком много КС при малом бюджете и надо уменьшить кол-во активных, а потом после проведения оптимизации можно увеличивать кол-во КС, путем уменьшения addkeysnodigit, при этом остануться "длинные хвосты"


//возвращает undefined либо три массива
//первый массив КС - массив цифровых КС (может быть пустой)  записывать с фразовым соответствием
//второй массив КС - массив по 3 слова в КС (может быть пустой) записывать с широким соответствием с модификаторами
//третий массив КС - массив по 2 слова в КС (может быть пустой) записывать с широким соответствием с модификаторами
//все массивы КС не пересекаются, формируются в порядке следования (поэтому в первом массиве теоретически могут оказаться значения из второго или третьего)
//если пустые первые два массива КС или первые два массива КС выдадут все слова "мало показов", то добавляем третий массив КС (потом в алгоритме)
//если есть requiredkeys, то стоит добавлять КС из всех трех массивов всегда
//КС могут быть "перевернуты" (типа 'фильтр киа' и 'киа фильтр'), это сложно вычислять, оставляем так
//в КС могут дублироваться слова на разных языках, особенно с requiredkeys (типа 'фильтр киа kia'), это крайне сложно вычислять, оставляем так





function everyPermutation(args, fn) {
//множественные циклы без eval, с рекурсией
//http://qaru.site/questions/284220/variable-amount-of-nested-for-loops/1416629
//откорректировано

    if (!args.length)
	return;

    for (var j = 0; j < args.length; j++) {
	if ((args[j].min > args[j].max)||(args[j].min < 0)||(args[j].max < 0))
		return;
    }

    //var indices = args.map(a => a.min);
	//стрелочные функции не поддерживаются в AdsApp
	//https://developer.mozilla.org/ru/docs/Web/JavaScript/Reference/Functions/Arrow_functions
    var indices = args.map(function(a) {
	return a.min;
    });

    for (var j = args.length; j >= 0;) {
	//обязательно .slice() - копировать массив
        //fn.apply(null, indices.slice());
        fn.call(null, indices.slice());
        //fn(indices.slice());


        // go through indices from right to left setting them to 0
        for (j = args.length; j--;) {
            // until we find the last index not at max which we increment
            if (indices[j] < args[j].max) {
                ++indices[j];
                break;
            }
            indices[j] = args[j].min;
        }
    }
}

/*

everyPermutation([
    {min:4, max:7},
    {min:5, max:7},
//    {min:5, max:7},
//], function(a,b,c,d) {
//    console.log(a + ',' + b + ',' + c + ',' + d);
], function(arr) {
	//так как низзя что бы генерировалась куча перекрестных КС, ограничиваем только со следующего элемента (что бы не было перекрестных повторений)
    for (var j = 1; j < arr.length; j++) {
	if (arr[j] <= arr[j-1])
		return;
    }
    console.log(arr.join(','));
});

*/




	if ((!name)||(typeof name !== 'string'))
		return;

	if ((typeof maxNodigaddkeyNum !== 'number')||(maxNodigaddkeyNum < 3))
		return;

	if (requiredkeys&&(typeof requiredkeys === 'string')) {
		requiredkeys = requiredkeys.trim();
		requiredkeys = requiredkeys.split(/[\s\,]+[\+]*/);
		if (requiredkeys.length) {
			var requiredkeysHash={};
			var	metaChar = /[-[\]{}()*+?.\\^$|,]/g,
				escape = function (str) {
					return str.replace(metaChar, "\\$&");
				};
			var requiredkeysReqExp = [];
		        for (var i = 0; i < requiredkeys.length; i++) {
				var rk = requiredkeys[i].toLowerCase();
				if (!requiredkeysHash[rk]) {
					requiredkeys[i] = rk;
					requiredkeysReqExp[i] = new RegExp("(?:^|\\s)" + escape(requiredkeys[i]) + "(?:\\s|$)", "i");
					requiredkeysHash[rk] = 1;
				} else {
//дубликаты в т.ч. с большими буквами удаляем
					requiredkeys.splice(i,1);
					i--;
				}
			}
		} else {
			requiredkeys = null;
		}
	} else {
		requiredkeys = null;
	}



	var query = name;


	query = query.toLowerCase();



//если есть запятые или |, то разделяем по ним и выбираем слова (из первых двух) по наличию цифр или максимуму букв
	var qarr = query.split(/[\,\|](?![^\s\-\_\\\/\d]*[\-\_\\\/\d])/);
	if ((qarr.length > 1)&&qarr[0].length&&qarr[1].length&&((qarr[0].length + qarr[1].length) >= 10)) {
		var del = qarr[0].length/qarr[1].length;
		var koef = 10/13; // (koef должен быть < 1),  10 и 13 берем для базовой пропорции длины сравнения слов
		if ((del <= 1/koef)&&(del >= koef)) {
//если длина словосочетаний соизмерима, то ищем цифры в первую очередь
			if (/[\d]/.test(qarr[0])) {
				if (/[\d]/.test(qarr[1])) {
					if (qarr[0].length > qarr[1].length) {
						query=qarr[0];
					} else {
						query=qarr[1];
					}
				} else {
					query=qarr[0];
				}
			} else if (/[\d]/.test(qarr[1])) {
				query=qarr[1];
			} else {
				if (qarr[0].length > qarr[1].length) {
					query=qarr[0];
				} else {
					query=qarr[1];
				}
			}
		} else {
//иначе по длине выбираем
			if (qarr[0].length > qarr[1].length) {
				query=qarr[0];
			} else {
				query=qarr[1];
			}
		}
	}



//минус-слова разрешены в КС, поэтому удаляем их из КС
	query = query.replace(/((^\s*)|(\s))\-[^\s\+]+/g, '');


/*

Ниже расширил круг специсмволов (иначе будут проблемы с другими символами, которые гугл не перечислил, но выдает на них ошибку)

	//Ключевые слова не могут содержать следующие специальные символы: ! @ % , *
	//https://support.google.com/google-ads/answer/7476658?hl=ru
	// , ! @ % ^ * () = {} ; ~ ` <> ? \ |
	//поэтому удаляем эти символы, заменяем на пробелы
	if (/[\!\@\%\,\*]/.test(query)) {
		//заменяем специальные символы обязательно на пробел
//		query = query.replace(/[\!\@\%\,\*]+/g, ' ');
		//заменяем специальные символы обязательно на пробел (кроме круглых скобок)
		//круглые скобки анализируем позже и удаляем
		query = query.replace(/[\!\@\%\*\^\=\{\}\;\~\`\<\>\?\\\|]+/g, ' ');
//заменяем запятую на точку (точка вроде игнорируется)
		query = query.replace(/[\,]+/g, '.');
		if (/[^\s\[\]\"\-\+]/.test(query)) {} else {
			//если нет буквенно-цифровых символов, то выходим
			return;
		}

	}

*/

//какие символы считаем спецсимволами (KEYWORD_HAS_INVALID_CHARS):
	//  /[^\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\']/
	if (/[^\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+./:\-\[\]\'\(\)]/.test(query)) {
		//заменяем специальные символы обязательно на пробел (кроме круглых скобок и запятой)
		//круглые скобки анализируем позже и удаляем
		query = query.replace(/[^\u00BF-\u1FFF\u2C00-\uD7FF\w#$&_ "+.,/:\-\[\]\'\(\)]+/g, ' ');
//заменяем запятую на точку (точка вроде игнорируется)
		query = query.replace(/[\,]+/g, '.');
		if (/[^\s\[\]\"\-\+]/.test(query)) {} else {
			//если нет буквенно-цифровых символов, то выходим
			return;
		}
	}



	//так неверно, будем удалять из запроса или КС например знаки "минус" : +бактерицидный +облучатель +обн-150
	//	query = query.replace(/[\"\[\]\-\+]+/g, '');

	query = query.replace(/[\"\[\]]+/g, '');
	query = query.replace(/((^\s*)|(\s))[\+\-]+([^\s\+\-]+)/g, '$1$4');
//убираем одинокие + -
	query = query.replace(/((^\s*)|(\s))[\+\-]+((\s)|(\s*$))/g, ' ');
	query = query.replace(/^\s*(.*?)\s*$/, '$1');


//убираем одинокие точки
	query = query.replace(/\s+\.(?=\s)/g, '');


	if (/^\s*$/.test(query)) {
		return;
	}

//	return query;


//вроде начально очистили, теперь ищем и разбиваем на КС

//массив цифровых и по 3 слова в КС
	var keys = [];

//массив по 3 слова в КС
	var keys3 = [];

//массив по два слова в КС (если пустой первый массив или первый массив выдаст все слова "мало показов", то его добавляем потом)
	var keys2 = [];

//массив по одному слову
	var keys1 = [];

	if (onekeys&&(typeof onekeys === 'string')) {
		onekeys = onekeys.trim();
		onekeys = onekeys.split(/[\s\,]+[\+]*/);
		if (onekeys.length) {
			var onekeysHash={};
		        for (var i = 0; i < onekeys.length; i++) {
				var rk = onekeys[i].toLowerCase();
				if (!onekeysHash[rk]) {
					onekeysHash[rk] = 1;
				}
			}
                        onekeys = Object.keys(onekeysHash)
		} else {
			onekeys = [];
		}
	        for (var ii = 0; ii < onekeys.length; ii++) {
			keys1[keys1.length] = onekeys[ii];
		}
//обработка keys1 завершена, дальше нигде не используется и не обрабатывается onekeys и keys1
	}


	var afterskobki = '';

function replacer1(match, p1, p2, p3, offset, string) {

//чистим если остались скобки
	p3 = p3.replace(/\(([^\)]*)\)/g, ' ');
//чистим если остались одиночные скобки
	p3 = p3.replace(/[\(\)]+/g, '');
//все слова после первых скобок (но не в последующих скобках), удаляем и резервируем для обнаружения ЦС
	afterskobki = p3;
	p3 = '';

//теоретически здесь тоже могут остаться скобки (при некорректном наполнении КС)
	p2 = p2.replace(/\(([^\)]*)\)/g, ' ');
	p2 = p2.replace(/[\(\)]+/g, '');


//	if (/\d.*\d.*\d.*\d.*\d/.test(p2)) {
	if ((/\d.*\d.*\d.*\d.*\d/.test(p2))||(/(?=[^\s]*\d[^\s]*\d[^\s]*\d)[a-zA-Z0-9\.\-\_\/]{7,}/.test(p2))) {
//много цифр (5 и более), либо длинное английское слово (7 и более знаков, у которых не менее 3-х цифр)
		var key = p2.trim();
		keys[keys.length] = key;
		var key2 = key.replace(/\s+/g, '');
		if (key != key2) {
//если между буквенно-цифровыми символами есть пробелы, то добавляем еще КС без пробелов
			if (key2.length&&(key2.length <= 80))
				keys[keys.length] = key2;
		}
//удаляем слово из запроса
		return p1 + ' ' + p3;
	} else if (/\d.*\d/.test(p2)) {
//заменяем скобки на символ ` слева, что бы на следующем шаге не объединило цифры в скобках с другими цифрами, которые без скобок
//например Кровельный саморез по дереву окрашенный 4.8х19 ( 8017 ) 350 штук в упаковке
		var key2 = p2.replace(/\s+/g, '');
		if (key2.length >=5) {
			if (key2.length&&(key2.length <= 80))
				keys[keys.length] = key2;
		}
//если несколько слов в скобках, то лучше их объединить, что бы потом не выдало их как отдельные КС, что неверно
		return p1 + ' `' + key2 + ' ' + p3;
//		return p1 + ' .' + key2 + ' ' + p3;
//		return ' .' + p1.trim() + ' ';
	} else {
		var key2 = p2.replace(/\s+/g, '');
//если несколько слов в скобках, то лучше их объединить, что бы потом не выдало их как отдельные КС, что неверно
		return p1 + ' ' + key2 + ' ' + p3;
//		return ' ' + p1.trim() + ' ';
	}

}

//удаляем скобки внутри скобок (могут появится, так как теперь мы могли вручную добавить скобки для выделения ключей)
	query = query.replace(/(\([^\(\)]*)\([^\(\)]*\)([^\(\)]*\))/g, '$1$2');

//находим цифры внутри первых скобок (black) или (SM3253452)
//если много цифр (5 и более), либо длинное английское слово (7 и более знаков, у которых не менее 3-х цифр),
//то добавляем в кс и удаляем, иначе просто убираем скобки
//все слова после первых скобок (но не в последующих скобках), удаляем и резервируем для обнаружения ЦС
//	query = query.replace(/\(([^\)]*)\)/g, replacer1);
	query = query.replace(/^([^\(]*)\(([^\)]*)\)(.*)$/, replacer1);

//чистим если остались скобки
//	query = query.replace(/\(([^\)]*)\)/g, ' ');
//чистим если остались одиночные скобки
	query = query.replace(/[\(\)]+/g, '');





function replacer2(match, p1, offset, string) {

	//так как теперь еще заводим длинные слова с небольшим кол-вом цифр, то сначала проверяем кол-во цифр
	if (!(/\d.*\d.*\d/.test(p1))) {
		return match;
	}

//в конце разрешена точка, убираем если есть
//	keys[keys.length] = p1.trim().replace(/\.$/, '');
	keys[keys.length] = p1.trim();
//заменяем пробелы только внутри строки, не по краям (что бы можно было потом возвратить с пробелами return key)
	var key = p1.replace(/([^\s])\s+(?=[^\s])/g, '$1');
	if (p1 != key) {
//если между буквенно-цифровыми символами есть пробелы, то добавляем еще КС без пробелов (объединяем в одно слово)
//		keys[keys.length] = key.trim();
//в конце разрешена точка, убираем если есть
//		var key2 = key.trim().replace(/\.$/, '');
		var key2 = key.trim();
		if (key2.length&&(key2.length <= 80))
			keys[keys.length] = key2;
//не стоит убирать пробелы в цифро-буквенных словах запроса, можно получить потом "слитные" ключи с неизвестными параметрами
//хотя с другой стороны, мы получим мало пересечений слов будет что-то типа МАСЛЯНЫЙ ФИЛЬТР BOSCH 0 (то есть любые запросы), вместо МАСЛЯНЫЙ ФИЛЬТР BOSCH 0451103349
//так что опять раскомментировал (у нас есть "неслитные" цифровые ключи в keys[keys.length] = p1.trim();)

//в общем объединяем (в запросе), если у нас только цифры, иначе не объединяем (в запросе)
		if (/^\d+$/.test(key2))
			return key;
//так же объединяем (в запросе), если есть только по одному символу между пробелами
		if (/(^|\s)[^\s](\s|$)/.test(p1))
			return key;
		

		

	}
	return match;
}



//находим разные варианты, где много цифр (5 и более), добавляем в кс
//	query = query.replace(/((?:^|\s)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)*)/g, replacer2);
//	query = query.replace(/((?:^|\s)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)*(?:\s|\.|$))/g, replacer2);
//	query = query.replace(/((?:^|\s)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)(?:[^\+\-\.\s\d]*\d+[^\+\-\.\s\d]*\s*)*(?:\s|\.\s|$))/g, replacer2);

//это рабочий вариант (с точкой слева)
//	query = query.replace(/((?:^|\s)(?:[^\+\-\.\/\s\d]*\d+[^\+\-\.\/\s\d]*\s*)(?:[^\+\-\.\/\s\d]*\d+[^\+\-\.\/\s\d]*\s*)(?:[^\+\-\.\/\s\d]*\d+[^\+\-\.\/\s\d]*\s*)(?:[^\+\-\.\/\s\d]*\d+[^\+\-\.\/\s\d]*\s*)(?:[^\+\-\.\/\s\d]*\d+[^\+\-\.\/\s\d]*\s*)(?:[^\+\-\.\/\s\d]*\d+[^\+\-\.\/\s\d]*\s*)*(?:\s|\.\s|$))/g, replacer2);


//	query = query.replace(/((?:^|\s)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)*(?:\s|$))/g, replacer2);
//добавлено, проверяем теперь еще длинные английские слова (7 и более знаков, у которых не менее 3-х цифр)
//	query = query.replace(/((?:^|\s)(?:(?:(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)*)|(?:[a-zA-Z0-9\.\-\_\/]{7,}))(?:\s|$))/g, replacer2);

//исправлено, что бы пробелом не заканчивалось выражение, а только проверялось наличие пробела в конце (иначе, например, если сначала не ЦС и затем два ЦС подряд, то второе ЦС не будет обнаружено)
//	query = query.replace(/((?:^|\s)(?:(?:(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)(?:[^\`\/\s\d]*\d+[^\`\/\s\d]*\s*)*)|(?:[a-zA-Z0-9\.\-\_\/]{7,}))(?=\s|$))/g, replacer2);

//доработано
//	query = query.replace(/((?:^|\s)(?:(?:(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)*)|(?:[a-zA-Z0-9\.\-\_\/]{7,}))(?=\s|$))/g, replacer2);
/*
верхнее опять доработано из-за глюка гугла, пример:
var querytest = 'фильтр воздух во внутренном пространстве mann-filter cuk 2019'
//Работает        
var found = querytest.match(/((?:^|\s)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)*(?=\s|$))/)
//Не работает                
var found = querytest.match(/((?:^|\s)(?:(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)*)(?=\s|$))/)
//а вот так заработало
var found = querytest.match(/((?:^|\s)(?:(?=[^\`\s\d]*\d+)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)*)(?=\s|$))/)
//сильно слило бюджет у клиента на запросах с 2019 (это год) (то есть появилось фразовое "2019" и точное слова [2019])
Logger.log(found)
*/

//теперь еще глюк гугла убран (можно сравнить с верхним)
	query = query.replace(/((?:^|\s)(?:(?:(?=[^\`\s\d]*\d+)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)*)|(?:[a-zA-Z0-9\.\-\_\/]{7,}))(?=\s|$))/g, replacer2);

//ищем цс в резервном тексте после первых скобок (полностью повторяет предыдущее рег. выражение)
	afterskobki.replace(/((?:^|\s)(?:(?:(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)(?:[^\`\s\d]*\d+[^\`\s\d]*\s*)*)|(?:[a-zA-Z0-9\.\-\_\/]{7,}))(?=\s|$))/g, replacer2);



//дальше начинается обработка нецифровых слов


//чистим лишние точки слева от слов, если появились за один шаг до этого
//	query = query.replace(/(\s)\./g, '$1');
//чистим символы ` слева от слов, если появились за один шаг до этого
	query = query.replace(/[\`]+/g, '');


//убираем везде отдельные слова без цифр и букв
	query = query.replace(/(?:^|(?:\s+))[^\s\d\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]+(?=\s|$)/g, '');


//предварительно нужно убрать пробелы в начале и в конце, если есть
	query = query.trim();

//сколько больших слов являются стартовыми для генерации КС (всегда 2)
//два варианта для одного стартового вместо двух:
//1. второе слово меньше 4-х букв
//2. mixtype > 0
	var startkeysNum = 2;

	if (mixtype) {
		startkeysNum = 1;
	}


//проверяем второе слово на длину 4 символа (так как перые два слова используются как базовые для старта генерации КС)
//зачем - например "Сумка Для Обуви На 2 Пары Мягкая с Рисунком",
//то будет удалено Для и первые два слова будут Сумка и Обуви, слова начиная с Обуви дадут нецелевой трафик точно, например, "Обуви Мягкая Рисунком"
//то есть если второе слово Для то после его удаления третье слово нельзя делать стартовым для генерации
//так же всегда использовать вторым словом в КС следующее длинное слово (это контролируется ниже), то есть должны присутсвовать всегда в примере 2 слова Сумка и Обуви
	var shortsecondkey = 0;
//	if (/^[^\s]+\s+[^\s\d\-\_\/\\]{1,3}(\s+|$)/.test(query)) {
//нужно проверять конкретно наличие только букв, а не слова без цифр и пр.
	if (/^[^\s]+\s+[\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]{1,3}(\s+|$)/.test(query)) {
		shortsecondkey = 1;
		startkeysNum = 1;
	}


//убираем внутри строки слова без цифр и пр., у которых меньше 4-х символов
//	query = query.replace(/\s+[^\s\d\-\_\/\\]{1,3}(?=\s+)/g, '');
//правильно так:
//убираем внутри строки слова только с буквами, у которых меньше 4-х символов
//	query = query.replace(/\s+[\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]{1,3}(?=\s+)/g, '');
//новый вариант:
function replacerShort1(match, p1, offset, string) {
	//word - короткое слово, которое нужно удалить
	var word = p1.toLowerCase();
	if (shortWordsHash[word]) {
		//нельзя удалять, оно занесено в список реальных слов
		return match;
	} else {
		//можно удалять
		return '';
	}
}
	query = query.replace(/\s+([\u00BF-\u1FFF\u2C00-\uD7FFA-Za-z]{1,3})(?=\s+)/g, replacerShort1);



//обязательно чистим пробелы
	query = query.replace(/\s+/g, ' ');
	query = query.trim();


//alert(query)

	if (!query) {
		//обязательно учесть, что теперь может не остаться слов после добавления ЦС
		if (keys.length||keys2.length||keys3.length||keys1.length) {
			return [keys,keys3,keys2,keys1];
		} else {
			return;
		}
	}


//с цифрами разобрались, теперь разбиваем по 2-3 слова в КС
//с первым и вторым словом из запроса генерируем по три слова КС (если после добавления КС будут все слова со статусом "мало показов", то сделать по 2 слова)
	var keysarr = query.split(/\s+/);


	var SecondKeysArrMaxNum = keysarr.length;
//если второе слово было коротким, то всегда вторым словом в КС должно быть только следующее длинное слово
	if (shortsecondkey) {
//сколько вторых слов может быть (только одно, отсчет начинается со второго по счету), то есть значение равно 2
		SecondKeysArrMaxNum = 2;
		SecondKeysArrMaxNum = (keysarr.length >= SecondKeysArrMaxNum) ? SecondKeysArrMaxNum : keysarr.length;
	}


	//количество стартовых слов в КС (обычно 2 (первое и второе), но есть исключение (только 1 - читаем выше про startkeysNum))
	var maxStartElements = ((startkeysNum >= keysarr.length) ? keysarr.length : startkeysNum);


	//теперь везде используется не 3 и 2 слова, а maxNodigaddkeyNum и maxNodigaddkeyNum-1 слов

	//идея ниже в том что вместо одного 3-го слова, нам нужно добавить в keys3 все неповторяющиеся слова от 3-го до maxNodigaddkeyNum включительно

	//предварительный массив массивов номеров слов из keysarr для 3-х слов (он уточнится при вызове everyPermutation)
	//массив массивов номеров элементов для перебора 3-х слов с учетом maxNodigaddkeyNum (это все слова от 3-го и дальше, их перебор)
	//все переборы делаются в одном массиве keysarr и каждый следующий цикл начинается со значения предыдущего +1
	var NumsforWords3 = [];
	if ((mixtype == 2)||(mixtype == 4)) {
		//max не может быть больше чем min
		NumsforWords3.push({min: 0, max: (maxStartElements - 1 > 0 ? 0 : maxStartElements - 1)});
		NumsforWords3.push({min: 1, max: (SecondKeysArrMaxNum - 1 > 1 ? 1 : SecondKeysArrMaxNum - 1)});
	        for (var i = 2; i < maxNodigaddkeyNum; i++) {
			//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
			NumsforWords3.push({min: i, max: (keysarr.length - 1 > i ? i : keysarr.length - 1)});
			//можно и так, потом очистится при вызове everyPermutation
			//NumsforWords3.push({min: 2, max: (keysarr.length - 1 > i ? i : keysarr.length - 1)});
		}
	} else if (mixtype == 3) {
		//max не может быть больше чем min для первых двух слов
		NumsforWords3.push({min: 0, max: (maxStartElements - 1 > 0 ? 0 : maxStartElements - 1)});
		NumsforWords3.push({min: 1, max: (SecondKeysArrMaxNum - 1 > 1 ? 1 : SecondKeysArrMaxNum - 1)});
	        for (var i = 2; i < maxNodigaddkeyNum; i++) {
			//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
			NumsforWords3.push({min: i, max: keysarr.length - 1});
			//можно и так, потом очистится при вызове everyPermutation
			//NumsforWords3.push({min: 2, max: keysarr.length - 1});
		}
	} else {
		NumsforWords3.push({min: 0, max: maxStartElements - 1});
		NumsforWords3.push({min: 1, max: SecondKeysArrMaxNum - 1});
	        for (var i = 2; i < maxNodigaddkeyNum; i++) {
			//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
			NumsforWords3.push({min: i, max: keysarr.length - 1});
			//можно и так, потом очистится при вызове everyPermutation
			//NumsforWords3.push({min: 2, max: keysarr.length - 1});
		}
	}


//учитываем requiredkeys (но всего должно быть по 3 слова, не больше и не меньше)
//с учетом requiredkeys может появится 4 слова, поэтому надо следиить за количеством
//сначала по 3 с первым словом, потом по 3 со вторым словом

	everyPermutation(NumsforWords3, function(arr) {
//вызов рекурсивного перебора по любому количеству циклов
//каждый следующий цикл начинается с индекса предыдущего +1, иначе будут дубликаты и пр. каша
		//console.log(arr.join(','));

		//копируем все слова в другой массив
	        var keysarr2 = keysarr.slice();

		var key = ''
		var keysd = {}
		for (var j = 0; j < arr.length; j++) {
			//одновременно делаем проверку, что номер следующего элемента больше предыдущего
			if (j&&(arr[j] <= arr[j-1]))
				return;

			//делаем пометку, что слово задействовано в КС
			keysarr2[arr[j]] = '';

			//одновременно делаем проверку на дубликаты внутри КС (могут быть в передаваемом поле)
			if (keysd[keysarr[arr[j]]]) {
				return;
			} else {
				keysd[keysarr[arr[j]]] = 1;
			}
			if (key) {
				key += ' ' + keysarr[arr[j]];
			} else {
				key = keysarr[arr[j]];
			}
		}

		//не все слова задействованы в КС, не добавляем КС
		if ((mixtype == 4)&&(keysarr2.join('') !== ''))
			return;

		//на всякий случай проверяем, хотя не должно быть пусто
		if (key) {
			if ((keys.indexOf(key) == -1)&&(keys2.indexOf(key) == -1)&&(keys3.indexOf(key) == -1))  {
				if (key.length&&(key.length <= 80)) {
					if (requiredkeys) {
						var addkey = 0;
					        for (var ii = 0; ii < requiredkeys.length; ii++) {
							if (requiredkeysReqExp[ii].test(key)) {
//только 3 символа можно, поэтому разрешено записывать, если совпадают. Если не совпадают, то это будет 4 - низзя
//то есть мы можем записать здесь только если одно из слов совпадает с одним из слов requiredkeys, иначе не пишем
								keys3[keys3.length] = key;
								addkey = 1;
							}
						}
						if (notReqrequiredkeys&&(!addkey)) {
							keys3[keys3.length] = key;
						}
					} else {
						keys3[keys3.length] = key;
					}
				}
			}
		}
	});



	//идея ниже в том что вместо одного 2-го слова, нам нужно добавить в keys2 все неповторяющиеся слова от 2-го до maxNodigaddkeyNum-1 включительно

	//предварительный массив массивов номеров слов из keysarr для 2-х слов (он уточнится при вызове everyPermutation)
	//массив массивов номеров элементов для перебора 2-х слов с учетом maxNodigaddkeyNum (это все слова от 2-го и дальше, их перебор)
	var NumsforWords2 = [];
	if ((mixtype == 2)||(mixtype == 4)) {
		//max не может быть больше чем min
		NumsforWords2.push({min: 0, max: (maxStartElements - 1 > 0 ? 0 : maxStartElements - 1)});
		NumsforWords2.push({min: 1, max: (SecondKeysArrMaxNum - 1 > 1 ? 1 : SecondKeysArrMaxNum - 1)});
        	for (var i = 2; i < maxNodigaddkeyNum - 1; i++) {
			//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
			NumsforWords2.push({min: i, max: (keysarr.length - 1 > i ? i : keysarr.length - 1)});
			//можно и так, потом очистится при вызове everyPermutation
			//NumsforWords2.push({min: 2, max: (keysarr.length - 1 > i ? i : keysarr.length - 1)});
		}
	} else if (mixtype == 3) {
		//max не может быть больше чем min для первых двух слов
		NumsforWords2.push({min: 0, max: (maxStartElements - 1 > 0 ? 0 : maxStartElements - 1)});
		NumsforWords2.push({min: 1, max: (SecondKeysArrMaxNum - 1 > 1 ? 1 : SecondKeysArrMaxNum - 1)});
        	for (var i = 2; i < maxNodigaddkeyNum - 1; i++) {
			//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
			NumsforWords2.push({min: i, max: keysarr.length - 1});
			//можно и так, потом очистится при вызове everyPermutation
			//NumsforWords2.push({min: 2, max: keysarr.length - 1});
		}
	} else {
		NumsforWords2.push({min: 0, max: maxStartElements - 1});
		NumsforWords2.push({min: 1, max: SecondKeysArrMaxNum - 1});
        	for (var i = 2; i < maxNodigaddkeyNum - 1; i++) {
			//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
			NumsforWords2.push({min: i, max: keysarr.length - 1});
			//можно и так, потом очистится при вызове everyPermutation
			//NumsforWords2.push({min: 2, max: keysarr.length - 1});
		}
	}


//учитываем requiredkeys (но всего должно быть по 2 слова, не больше и не меньше)
//с учетом requiredkeys может появится 3 слова, поэтому надо следиить за количеством и добавлять в соотв. массив
//теперь по 2 с первым словом, потом по 2 со вторым словом

	everyPermutation(NumsforWords2, function(arr) {
//вызов рекурсивного перебора по любому количеству циклов
//каждый следующий цикл начинается с индекса предыдущего +1, иначе будут дубликаты и пр. каша
		//console.log(arr.join(','));

		//копируем все слова в другой массив
	        var keysarr2 = keysarr.slice();

		var key = ''
		var keysd = {}
		for (var j = 0; j < arr.length; j++) {
			//одновременно делаем проверку, что номер следующего элемента больше предыдущего
			if (j&&(arr[j] <= arr[j-1]))
				return;

			//делаем пометку, что слово задействовано в КС
			keysarr2[arr[j]] = '';

			//одновременно делаем проверку на дубликаты внутри КС (могут быть в передаваемом поле)
			if (keysd[keysarr[arr[j]]]) {
				return;
			} else {
				keysd[keysarr[arr[j]]] = 1;
			}
			if (key) {
				key += ' ' + keysarr[arr[j]];
			} else {
				key = keysarr[arr[j]];
			}
		}

		//не все слова задействованы в КС, не добавляем КС
		if ((mixtype == 4)&&(keysarr2.join('') !== ''))
			return;

		//на всякий случай проверяем, хотя не должно быть пусто
		if (key) {
			if ((keys.indexOf(key) == -1)&&(keys2.indexOf(key) == -1)&&(keys3.indexOf(key) == -1))  {
				if (key.length&&(key.length <= 80)) {
					if (requiredkeys) {
						var addkey = 0;
					        for (var ii = 0; ii < requiredkeys.length; ii++) {
							if (requiredkeysReqExp[ii].test(key)) {
//только 2 символа можно, поэтому разрешено записывать, если совпадают. Если не совпадают, то это будет 3
								keys2[keys2.length] = key;
								addkey = 1;
							} else {
								var key2 = key + ' ' +  requiredkeys[ii];
								if ((keys.indexOf(key2) == -1)&&(keys2.indexOf(key2) == -1)&&(keys3.indexOf(key2) == -1))  {
//здесь пишем в 3-х буквенный массив
									if (key2.length&&(key2.length <= 80)) {
										keys3[keys3.length] = key2;
									}
								}
							}
						}
						if (notReqrequiredkeys&&(!addkey)) {
							keys2[keys2.length] = key;
						}
					} else {
						keys2[keys2.length] = key;
					}
				}
			}


		}
	});




//если есть requiredkeys, то проходим и по одному слову, так как добавляем и будет 2
	if (requiredkeys) {

		//идея ниже в том что вместо одного 1-го слова + слово из requiredkeys, нам нужно добавить в keys2 все неповторяющиеся слова от 1-го до maxNodigaddkeyNum-2 включительно

		//предварительный массив массивов номеров слов из keysarr для 1-го слова (он уточнится при вызове everyPermutation)
		//массив массивов номеров элементов для перебора по 1-му слову с учетом maxNodigaddkeyNum (это все слова от 1-го и дальше, их перебор)
		var NumsforWords1 = [];
		if ((mixtype == 2)||(mixtype == 4)) {
			//max не может быть больше чем min
			NumsforWords1.push({min: 0, max: (maxStartElements - 1 > 0 ? 0 : maxStartElements - 1)});
			if (maxNodigaddkeyNum >= 4) {
				NumsforWords1.push({min: 1, max: (SecondKeysArrMaxNum - 1 > 1 ? 1 : SecondKeysArrMaxNum - 1)});
			        for (var i = 2; i < maxNodigaddkeyNum - 2; i++) {
					//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
					NumsforWords1.push({min: i, max: (keysarr.length - 1 > i ? i : keysarr.length - 1)});
					//можно и так, потом очистится при вызове everyPermutation
					//NumsforWords1.push({min: 0, max: (keysarr.length - 1 > i ? i : keysarr.length - 1)});
				}
			}
		} else if (mixtype == 3) {
			//max не может быть больше чем min для первых двух слов
			NumsforWords1.push({min: 0, max: (maxStartElements - 1 > 0 ? 0 : maxStartElements - 1)});
			if (maxNodigaddkeyNum >= 4) {
				NumsforWords1.push({min: 1, max: (SecondKeysArrMaxNum - 1 > 1 ? 1 : SecondKeysArrMaxNum - 1)});
			        for (var i = 2; i < maxNodigaddkeyNum - 2; i++) {
					//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
					NumsforWords1.push({min: i, max: keysarr.length - 1});
					//можно и так, потом очистится при вызове everyPermutation
					//NumsforWords1.push({min: 0, max: keysarr.length - 1});
				}
			}
		} else {
			NumsforWords1.push({min: 0, max: maxStartElements - 1});
			if (maxNodigaddkeyNum >= 4) {
				NumsforWords1.push({min: 1, max: SecondKeysArrMaxNum - 1});
			        for (var i = 2; i < maxNodigaddkeyNum - 2; i++) {
					//третий и выше массивы начинаются со 2-го элемента (3-е слово), каждый следующий увеличиваем на 1
					NumsforWords1.push({min: i, max: keysarr.length - 1});
					//можно и так, потом очистится при вызове everyPermutation
					//NumsforWords1.push({min: 0, max: keysarr.length - 1});
				}
			}
		}


		everyPermutation(NumsforWords1, function(arr) {
//вызов рекурсивного перебора по любому количеству циклов
//каждый следующий цикл начинается с индекса предыдущего +1, иначе будут дубликаты и пр. каша
			//console.log(arr.join(','));

			//копируем все слова в другой массив
		        var keysarr2 = keysarr.slice();

			var key = ''
			var keysd = {}
			for (var j = 0; j < arr.length; j++) {
				//одновременно делаем проверку, что номер следующего элемента больше предыдущего
				if (j&&(arr[j] <= arr[j-1]))
					return;

				//делаем пометку, что слово задействовано в КС
				keysarr2[arr[j]] = '';

				//одновременно делаем проверку на дубликаты внутри КС (могут быть в передаваемом поле)
				if (keysd[keysarr[arr[j]]]) {
					return;
				} else {
					keysd[keysarr[arr[j]]] = 1;
				}
				if (key) {
					key += ' ' + keysarr[arr[j]];
				} else {
					key = keysarr[arr[j]];
				}
			}

			//не все слова задействованы в КС, не добавляем КС
			if ((mixtype == 4)&&(keysarr2.join('') !== ''))
				return;

			//на всякий случай проверяем, хотя не должно быть пусто
			if (key) {
			        for (var ii = 0; ii < requiredkeys.length; ii++) {
					if (!requiredkeysReqExp[ii].test(key)) {
//здесь пишем в 2-х буквенный массив
						var key2 = key + ' ' +  requiredkeys[ii];
						if ((keys.indexOf(key2) == -1)&&(keys2.indexOf(key2) == -1)&&(keys3.indexOf(key2) == -1))  {
							if (key.length&&(key.length <= 80)) {
								keys2[keys2.length] = key2;
							}
						}
					}
				}
			}
		});


	}


	if (keys.length||keys2.length||keys3.length||keys1.length) {
		return [keys,keys3,keys2,keys1];
	} else {
		return;
	}




}




function XMLfindArray (XMLobj, setarr, setarrnum) {
//ищем массив (список товарных записей) в последнем элементе (согласно setarr)
//промежуточные элементы могут быть хеши или массивы

//пример
//var n = ['yml_catalog','shop','offers','offer'];
//var a = '{"yml_catalog":{"date":"2020-03-20 20:20","shop":{"name":{"Text":"EVIE"},"company":{"Text":"EVIE.shoes"},"url":{"Text":"https://evie.ua"},"platform":{"Text":"1C-Bitrix"},"currencies":{"currency":{"id":"UAH","rate":"1"},"Text":""},"categories":{"category":[{"id":"1","Text":"Обувь девочкам"},{"id":"115","parentId":"1","Text":"Школа"},{"id":"8","parentId":"1","Text":"Деми"},{"id":"82","parentId":"8","Text":"Ботинки"},{"id":"84","parentId":"8","Text":"Кроссовки"},{"id":"87","parentId":"8","Text":"Мокасины"},{"id":"90","parentId":"8","Text":"Резиновые сапоги"},{"id":"83","parentId":"8","Text":"Туфли"},{"id":"2","parentId":"1","Text":"Зима"},{"id":"4","parentId":"2","Text":"Ботинки"},{"id":"3","parentId":"2","Text":"Сапоги"},{"id":"11","parentId":"1","Text":"Лето"},{"id":"89","parentId":"11","Text":"Босоножки"},{"id":"93","parentId":"11","Text":"Кеды"},{"id":"71","Text":"Обувь мальчикам"},{"id":"116","parentId":"71","Text":"Школа"},{"id":"72","parentId":"71","Text":"Деми"},{"id":"85","parentId":"72","Text":"Ботинки"},{"id":"86","parentId":"72","Text":"Кроссовки"},{"id":"88","parentId":"72","Text":"Мокасины"},{"id":"91","parentId":"72","Text":"Резиновые сапоги"},{"id":"92","parentId":"72","Text":"Туфли"},{"id":"73","parentId":"71","Text":"Зима"},{"id":"74","parentId":"73","Text":"Ботинки"},{"id":"77","parentId":"71","Text":"Лето"},{"id":"94","parentId":"77","Text":"Босоножки"},{"id":"95","parentId":"77","Text":"Кеды"},{"id":"96","parentId":"77","Text":"Мокасины"},{"id":"120","Text":"SALE"}],"Text":""},"offers":{"offer":[{"id":"3540","available":"true","url":{"Text":"https://evie.ua/catalog/obuv_devochkam_2/botinki_timberland_navy.html?offerID=3540"},"price":{"Text":"847.5"},"oldprice":{"Text":"1695"},"currencyId":{"Text":"UAH"},"categoryId":{"Text":"120"},"picture":{"Text":"https://evie.ua/upload/iblock/d12/d12a2362bcf23a59fccb8bad3b580c07.jpg"},"vendor":{"Text":"Украина"},"name":{"Text":"Ботинки арт.034-1 Navy размер 24 (15,5)"},"description":{"Text":"Ботинки"},"country_of_origin":{"Text":"Украина"},"param":[{"name":"Вид застежки","Text":"Молния"},{"name":"Подкладка","Text":"100% шерсть"},{"name":"Материал верха","Text":"Натуральный нубук"},{"name":"Сезон","Text":"Зима"},{"name":"Вид товара","Text":"Ботинки"},{"name":"Артикул1","Text":"034-1 Navy"},{"name":"Дополнительные фотографии","Text":"https://evie.ua/upload/iblock/ff2/ff2fe072e6ed64fd70ee22b69d359b29.jpg, https://evie.ua/upload/iblock/7a6/7a6f869195c7621c716073f45c69007c.jpg"}],"Text":""},{"id":"3541","available":"true","url":{"Text":"https://evie.ua/catalog/obuv_devochkam_2/botinki_timberland_navy.html?offerID=3541"},"price":{"Text":"847.5"},"oldprice":{"Text":"1695"},"currencyId":{"Text":"UAH"},"categoryId":{"Text":"120"},"picture":{"Text":"https://evie.ua/upload/iblock/d12/d12a2362bcf23a59fccb8bad3b580c07.jpg"},"vendor":{"Text":"Украина"},"name":{"Text":"Ботинки арт.034-1 Navy размер 25 (6)"},"description":{"Text":"Ботинки"},"country_of_origin":{"Text":"Украина"},"param":[{"name":"Вид застежки","Text":"Молния"},{"name":"Подкладка","Text":"100% шерсть"},{"name":"Материал верха","Text":"Натуральный нубук"},{"name":"Сезон","Text":"Зима"},{"name":"Вид товара","Text":"Ботинки"},{"name":"Артикул1","Text":"034-1 Navy"},{"name":"Дополнительные фотографии","Text":"https://evie.ua/upload/iblock/ff2/ff2fe072e6ed64fd70ee22b69d359b29.jpg, https://evie.ua/upload/iblock/7a6/7a6f869195c7621c716073f45c69007c.jpg"}],"Text":""},{"id":"3545","available":"true","url":{"Text":"https://evie.ua/catalog/obuv_devochkam_2/mini_martens_black.html?offerID=3545"},"price":{"Text":"957"},"oldprice":{"Text":"1595"},"currencyId":{"Text":"UAH"},"categoryId":{"Text":"82"},"picture":{"Text":"https://evie.ua/upload/iblock/2a8/2a8c3aedaee6829f1e53fabe07069b2d.jpg"},"vendor":{"Text":"Украина"},"name":{"Text":"Mini Martens арт.018-1L Black размер 27(17)"},"description":{"Text":"Mini Martens Black 018-1L"},"country_of_origin":{"Text":"Украина"},"param":[{"name":"Вид застежки","Text":"Молния"},{"name":"Подкладка","Text":"Натуральная кожа"},{"name":"Материал верха","Text":"Лак на основе микрофибры, со специальным покрытием, который не поддается царапинам"},{"name":"Сезон","Text":"Деми"},{"name":"Вид товара","Text":"Ботинки"},{"name":"Артикул1","Text":"018-1L Black"},{"name":"Дополнительные фотографии","Text":"https://evie.ua/upload/iblock/236/23692247db3e0470392b00afe9395537.jpg, https://evie.ua/upload/iblock/505/505c8f1133e7217d28ebd1aa1b8dbca0.jpg, https://evie.ua/upload/iblock/0b0/0b05d847f921cf945e43b5cd5a8dd316.JPG, https://evie.ua/upload/iblock/27e/27e9149c13d2ee71520d247bb7f37d9b.jpg, https://evie.ua/upload/iblock/9ce/9cef2fb1634f1a80dbd9234f401e515b.JPG, https://evie.ua/upload/iblock/8cf/8cf8c45e3c308e5f2c777210f81c1722.jpg"}],"Text":""},{"id":"3546","available":"true","url":{"Text":"https://evie.ua/catalog/obuv_devochkam_2/mini_martens_black.html?offerID=3546"},"price":{"Text":"957"},"oldprice":{"Text":"1595"},"currencyId":{"Text":"UAH"},"categoryId":{"Text":"82"},"picture":{"Text":"https://evie.ua/upload/iblock/2a8/2a8c3aedaee6829f1e53fabe07069b2d.jpg"},"vendor":{"Text":"Украина"},"name":{"Text":"Mini Martens арт.018-1L Black размер 28(18)"},"description":{"Text":"Mini Martens Black 018-1L"},"country_of_origin":{"Text":"Украина"},"param":[{"name":"Вид застежки","Text":"Молния"},{"name":"Подкладка","Text":"Натуральная кожа"},{"name":"Материал верха","Text":"Лак на основе микрофибры, со специальным покрытием, который не поддается царапинам"},{"name":"Сезон","Text":"Деми"},{"name":"Вид товара","Text":"Ботинки"},{"name":"Артикул1","Text":"018-1L Black"},{"name":"Дополнительные фотографии","Text":"https://evie.ua/upload/iblock/236/23692247db3e0470392b00afe9395537.jpg, https://evie.ua/upload/iblock/505/505c8f1133e7217d28ebd1aa1b8dbca0.jpg, https://evie.ua/upload/iblock/0b0/0b05d847f921cf945e43b5cd5a8dd316.JPG, https://evie.ua/upload/iblock/27e/27e9149c13d2ee71520d247bb7f37d9b.jpg, https://evie.ua/upload/iblock/9ce/9cef2fb1634f1a80dbd9234f401e515b.JPG, https://evie.ua/upload/iblock/8cf/8cf8c45e3c308e5f2c777210f81c1722.jpg"}],"Text":""},{"id":"3559","available":"true","url":{"Text":"https://evie.ua/catalog/obuv_devochkam_2/mini_martens_gold.html?offerID=3559"},"price":{"Text":"478.5"},"oldprice":{"Text":"1595"},"currencyId":{"Text":"UAH"},"categoryId":{"Text":"120"},"picture":{"Text":"https://evie.ua/upload/iblock/594/59454cf9bfead890afdd0d293618b4bb.jpg"},"vendor":{"Text":"Украина"},"name":{"Text":"Mini Martens Gold арт.018-5L размер 25 (15)"},"description":{"Text":"Mini Martens Gold"},"country_of_origin":{"Text":"Украина"},"param":[{"name":"Вид застежки","Text":"Молния"},{"name":"Подкладка","Text":"Натуральная кожа"},{"name":"Материал верха","Text":"Натуральный лак на основе микрофибры с защитным покрытием"},{"name":"Сезон","Text":"Деми"},{"name":"Вид товара","Text":"Ботинки"},{"name":"Артикул1","Text":"018-5L Gold"},{"name":"Дополнительные фотографии","Text":"https://evie.ua/upload/iblock/173/17318c250035a4b20c227722335f4079.jpg"}],"Text":""},{"id":"3566","available":"true","url":{"Text":"https://evie.ua/catalog/obuv_devochkam_2/mini_martens_pink.html?offerID=3566"},"price":{"Text":"448.5"},"oldprice":{"Text":"1495"},"currencyId":{"Text":"UAH"},"categoryId":{"Text":"120"},"picture":{"Text":"https://evie.ua/upload/iblock/d41/d41a7b6553164971031b4d4fea61445f.jpg"},"vendor":{"Text":"Украина"},"name":{"Text":"Mini Martens Pink арт.018-2L размер 25 (15)"},"description":{"Text":"Mini Martens Pink"},"country_of_origin":{"Text":"Украина"},"param":[{"name":"Вид застежки","Text":"Молния"},{"name":"Подкладка","Text":"Натуральная кожа"},{"name":"Материал верха","Text":"Натуральный лак на основе микрофибры с защитным покрытием"},{"name":"Сезон","Text":"Деми"},{"name":"Вид товара","Text":"Ботинки"},{"name":"Дополнительные фотографии","Text":"https://evie.ua/upload/iblock/1e2/1e2d43fd381c0d1b9c27487730d00cf8.jpg, https://evie.ua/upload/iblock/747/74773eb6aed0f26cca889557094510c0.jpg, https://evie.ua/upload/iblock/830/830a54c3f776e776c05645df8d95b040.jpg, https://evie.ua/upload/iblock/ac1/ac17e7f1a410f00fc470d53dd7a0e59a.jpg, https://evie.ua/upload/iblock/711/71116601486f3faf24355369c1dadd0a.jpg, https://evie.ua/upload/iblock/312/31213a823e5c339fdce22d3330359bc7.jpg, https://evie.ua/upload/iblock/8b4/8b44445053c8011d22b28de278a157a4.jpg, https://evie.ua/upload/iblock/566/56668959b92661950d4e1f8e3412afb1.jpg, https://evie.ua/upload/iblock/95a/95a162bc0dba779f51d20459105ecc77.jpg"}],"Text":""}],"Text":""},"Text":""},"Text":""}}';
//XMLfindArray (a2, n)


	if (!setarrnum)
		setarrnum = 0;


	if (typeof XMLobj !== 'object')
		return;

	if ({}.toString.call(setarr) !== '[object Array]')
		return;

	if (typeof setarrnum !== 'number')
		return;


	var objt = {}.toString.call(XMLobj[setarr[setarrnum]]);

	if ((objt == '[object Array]')||(objt == '[object Object]')) {
		//здесь есть возможность перехода дальше на след уровень глобального хеша
		if (setarr.length <= setarrnum + 1) {
			//последний элемент
			if (objt !== '[object Array]') {
				//должен быть массив, а не хеш (эта функция используется только для выхода на список товаров, иначе убрать ограничение с возвратом undefined)
				return;
			} else {
				return XMLobj[setarr[setarrnum]];
			}
		} else {
			//индекс только цифровой элемент, если массив
			if ((objt == '[object Array]')&&(typeof setarr[setarrnum + 1] !== 'number'))
				return;

			//если не последний элемент, то всегда идет переход вглубь по setarr (и для массивов и для хешей)
			return XMLfindArray (XMLobj[setarr[setarrnum]], setarr, setarrnum + 1)
		}
	} else {
		return;
	}

}





function XMLobjectValue (XMLobj, setarr, setarrnum) {
//ищем значение в последнем элементе (согласно setarr) (может быть что угодно)
//если промежуточный элемент массив, то перебираем его и проходим дальше с помощь трех следущих элементов setarr (один - параметр, второй - его искомое значение, третий - элемент, значение которого надо получить или пройти дальше)
//промежуточные элементы могут быть хеши или массивы

//пример
//var nn = ['param','name','Сезон','Text']; //это поиск 'param' c 'name' = 'Сезон' и показ текста внутри тега 'param'
//var nn = ['param','name']; //это показ значения параметра 'name' тега 'param'
//var nn = ['param']; //это показ списка (массива) тегов 'param'
//var nn = ['url','Text']; //это показ текста внутри тега 'url'
//var aa = '{"id":"3540","available":"true","url":{"Text":"https://evie.ua/catalog/obuv_devochkam_2/botinki_timberland_navy.html?offerID=3540"},"price":{"Text":"847.5"},"oldprice":{"Text":"1695"},"currencyId":{"Text":"UAH"},"categoryId":{"Text":"120"},"picture":{"Text":"https://evie.ua/upload/iblock/d12/d12a2362bcf23a59fccb8bad3b580c07.jpg"},"vendor":{"Text":"Украина"},"name":{"Text":"Ботинки арт.034-1 Navy размер 24 (15,5)"},"description":{"Text":"Ботинки"},"country_of_origin":{"Text":"Украина"},"param":[{"name":"Вид застежки","Text":"Молния"},{"name":"Подкладка","Text":"100% шерсть"},{"name":"Материал верха","Text":"Натуральный нубук"},{"name":"Сезон","Text":"Зима"},{"name":"Вид товара","Text":"Ботинки"},{"name":"Артикул1","Text":"034-1 Navy"},{"name":"Дополнительные фотографии","Text":"https://evie.ua/upload/iblock/ff2/ff2fe072e6ed64fd70ee22b69d359b29.jpg, https://evie.ua/upload/iblock/7a6/7a6f869195c7621c716073f45c69007c.jpg"}],"Text":""}';
//XMLobjectValue (aa2, nn)

	if (!setarrnum)
		setarrnum = 0;

	if (typeof XMLobj !== 'object')
		return '';

	if ({}.toString.call(setarr) !== '[object Array]')
		return '';

	if (typeof setarrnum !== 'number')
		return '';

	var objt = {}.toString.call(XMLobj[setarr[setarrnum]]);

	if ((objt == '[object Array]')||(objt == '[object Object]')) {
		//здесь есть возможность перехода дальше на след уровень глобального хеша

		if (setarr.length <= setarrnum + 1) {
			//последний элемент
			return XMLobj[setarr[setarrnum]];
		} else {


			if (objt !== '[object Array]') {
				//если не массив а хеш, то переходим дальше на след уровень глобального хеша
				return XMLobjectValue (XMLobj[setarr[setarrnum]], setarr, setarrnum + 1)
			} else {

				if (setarr.length <= setarrnum + 3) {
					//нет вариантов выбора, так как это предпоследний элемент, отдаем первый попавшийся (нулевой как правило)

					if (setarr.length <= setarrnum + 1)
						return '';

					for (var i=0; i < XMLobj[setarr[setarrnum]].length; i++) {
						if (typeof XMLobj[setarr[setarrnum]][i][setarr[setarrnum + 1]] !== 'string')
							return '';
						return XMLobjectValue (XMLobj[setarr[setarrnum]][i], setarr, setarrnum + 1);
					}

				} else {
					//не последний элемент, массив - перебираем,
					//ищем следующий за ним элемент в качестве значения, 
					//когда найдем первый из возможных, то выдаем значение элемента после следующего
					for (var i=0; i < XMLobj[setarr[setarrnum]].length; i++) {
						if (typeof XMLobj[setarr[setarrnum]][i][setarr[setarrnum + 1]] !== 'string')
							return '';
						if (XMLobj[setarr[setarrnum]][i][setarr[setarrnum + 1]] === setarr[setarrnum + 2]) {
		
							//идем дальше по третьему элементу
							return XMLobjectValue (XMLobj[setarr[setarrnum]][i], setarr, setarrnum + 3);

						}
					}
				}

				return '';


			}
		}
	} else if (objt == '[object String]') {
		return XMLobj[setarr[setarrnum]];
	} else {
		return '';
	}

}





</script>


<style>

.dm-overlay124152345 .modal-confirm {
  font: inherit;

  display: inline-block;
  overflow: visible;

  min-width: 70px;
  margin: 0;
  padding: 12px 0;

  cursor: pointer;
  transition: background 0.2s;
  text-align: center;
  vertical-align: middle;
  text-decoration: none;

  border: 0;
  outline: 0;
}

.dm-overlay124152345 .modal-confirm {
  color: #fff;
  background: #81c784;
}

.dm-overlay124152345 .modal-confirm:hover,
.dm-overlay124152345 .modal-confirm:focus {
  background: #66bb6a;
}


/* Стили модального окна и содержания */
.dm-overlay124152345 {/* слой затемнения */
    position: absolute;
    top: 0;
    left: 0;
    display: none;
    overflow: auto;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.65);
}
/* активируем модальное окно */
.dm-overlay124152345:target {
    display: block;
    -webkit-animation: fade .6s;
    -moz-animation: fade .6s;
    animation: fade .6s;
}
/* блочная таблица */
.dm-overlay124152345 .dm-table {
    display: table;
    width: 100%;
    height: 100%;
}
/* ячейка блочной таблицы */
.dm-overlay124152345 .dm-cell {
    display: table-cell;
    padding: 0 1em;
    vertical-align: middle;
    text-align: center;
}
/* модальный блок */
.dm-overlay124152345 .dm-modal {
    display: inline-block;
    padding: 20px;
    max-width: 50em;
    background: #607d8b;
    -webkit-box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.22), 0px 19px 60px rgba(0, 0, 0, 0.3);
    -moz-box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.22), 0px 19px 60px rgba(0, 0, 0, 0.3);
    box-shadow: 0px 15px 20px rgba(0, 0, 0, 0.22), 0px 19px 60px rgba(0, 0, 0, 0.3);
    color: #cfd8dc;
    text-align: left;
    -webkit-animation: fade .8s;
    -moz-animation: fade .8s;
    animation: fade .8s;
}

/* кнопка закрытия */
.dm-overlay124152345 .close {
    z-index: 9999;
    float: right;
    width: 30px;
    height: 30px;
    color: #cfd8dc;
    text-align: center;
    text-decoration: none;
    line-height: 26px;
    cursor: pointer;
}
.dm-overlay124152345 .close:after {
    display: block;
    border: 2px solid #cfd8dc;
    -webkit-border-radius: 50%;
    -moz-border-radius: 50%;
    border-radius: 50%;
    content: 'X';
    -webkit-transition: all 0.6s;
    -moz-transition: all 0.6s;
    transition: all 0.6s;
    -webkit-transform: scale(0.85);
    -moz-transform: scale(0.85);
    -ms-transform: scale(0.85);
    transform: scale(0.85);
}
/* кнопка закрытия при наведении */
.dm-overlay124152345 .close:hover:after {
    border-color: #fff;
    color: #fff;
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -ms-transform: scale(1);
    transform: scale(1);
}


</style>


    <div class="dm-overlay124152345" id="win143526733">
        <div class="dm-table">
            <div class="dm-cell">
                <div class="dm-modal">
		   <center>
                    <a class="close" id="closewin143526733"></a>
<form method="post" id="formwin143526733">
Merchant
		     <textarea name=text id="checkwin143526733"><?php if (isset($_POST['text'])&&($_POST['text'])) echo $_POST['text']; ?></textarea>
<br>
Url
		     <input name=url type=text id="urlwin143526733">
		     <input name=urlhidden type=hidden id="urlhiddenwin143526733" value="<?php if (isset($_POST['url'])&&($_POST['url'])) echo $_POST['url']; ?>">
<br>
With CSS
		     <input name=style type=checkbox>

<br>
Html
		     <textarea name=html id="checkhtml7857587"><?php if (isset($_POST['html'])&&($_POST['html'])) echo htmlentities($_POST['html']); ?></textarea>

</form>
		<div id="outputwin143526733"></div>
		<br>
		    <button class="modal-confirm" onclick="checkPage()" >OK</button>
		</center>
                </div>
            </div>
        </div>
    </div>

<!--Endasdfasdfasd-->




<?php


function get_pageHeadersBody($Url, $Format= 0, $Depth= 0) {
//по мотивам http://php.net/manual/en/function.get-headers.php
//действует как get-headers (с редиректами), но еще передает последний body в параметре pagebody

    if ($Depth > 5) return false; //множественные редиректы (больше 5)
    $Parts = parse_url($Url);
    if (!array_key_exists('path', $Parts))   $Parts['path'] = '/';
    if (!array_key_exists('scheme', $Parts)) $Parts['scheme'] = 'http';
    if (!array_key_exists('port', $Parts))   $Parts['port'] = $Parts['scheme'] != 'http' ? 443 : 80;


    $Return = array();
    if ($Parts['scheme'] == 'https') {

//	$fp=fsockopen('ssl://'.$Parts['host'],$Parts['port'],$errno,$errstr,10);

	$context = stream_context_create();
	stream_context_set_option($context, 'ssl', 'verify_peer', false);
	$fp = stream_socket_client('ssl://'.$Parts['host'].':'.$Parts['port'], $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context);


    } else {
	$fp=fsockopen($Parts['host'],$Parts['port'],$errno,$errstr,10);
    }


    if ($fp) {
        $Out = 'GET '.$Parts['path'].(isset($Parts['query']) ? '?'.@$Parts['query'] : '').' HTTP/1.0'."\r\n".

//так не работает на части серверов, например https://ledkontur.com.ua/
//	          'Host: '.$Parts['host'].($Parts['port'] != 80 ? ':'.$Parts['port'] : '')."\r\n".

	          'Host: '.$Parts['host']."\r\n".

		  "Accept: text/html;charset=UTF-8\r\n".
//можно указать User-Agent, что бы видеть реакцию сайта на поисковик (если сайт заблокирует "поисковик", то нужен ретранслятор)
//		  "User-Agent: Mozilla/5.0 (compatible; Google-Apps-Script)\r\n".
		  "Accept-Charset: utf-8\r\n".
        	  'Connection: Close'."\r\n\r\n";

	fputs($fp, $Out);
        $Redirect = false; $RedirectUrl = '';
	$body_flg=0;
	$Buffer='';


        while (!feof($fp) && $InLine = fgets($fp)) {
	    if ($InLine == "\r\n") {
		$body_flg=1;
		continue;
	    }
	    if ($body_flg) {
		$Buffer.=$InLine;
	    } else {

	            $InLine = rtrim($InLine);
		    $array_exp = explode(': ', $InLine, 2);
		    $Key = $array_exp[0];
		    if (isset($array_exp[1])) {
			$Value = $array_exp[1];
		    } else {
			$Value = '';
		    }
	            if ($Key == $InLine) {
	                if ($Format == 1)
	                        $Return[$Depth] = $InLine;
	                else    $Return[] = $InLine;

//	                if (strpos($InLine, 'Moved') > 0) $Redirect = true;
	                if (strpos($InLine, ' 301 ') > 0) $Redirect = true;
	                if (strpos($InLine, ' 302 ') > 0) $Redirect = true;
	            } else {
        	        if ($Key == 'Location') $RedirectUrl = $Value;
	                if ($Format == 1)
        	                $Return[$Key] = $Value;
                	else    $Return[] = $Key.': '.$Value;
	            }

	    }
        }
        fclose($fp);
        if ($Redirect && !empty($RedirectUrl)) {
            $NewParts = parse_url($RedirectUrl);
            if (!array_key_exists('host', $NewParts))   $RedirectUrl = $Parts['host'].$RedirectUrl;
            if (!array_key_exists('scheme', $NewParts)) $RedirectUrl = $Parts['scheme'].'://'.$RedirectUrl;
            $RedirectHeaders = get_pageHeadersBody($RedirectUrl, $Format, $Depth+1);
            if ($RedirectHeaders) {
		$Return = array_merge_recursive($Return, $RedirectHeaders);
	    } else {
		return false;
	    }
        } else {
		$Return['pagebody'] = trim ($Buffer);
	}
        return $Return;
    }
    return false;

}



//взято здесь
//https://belousovv.ru/myscript/phpIDN

function ordUTF8($c, $index = 0, &$bytes = null) 
  { 
    $len = strlen($c); 
    $bytes = 0; 
    if ($index >= $len) 
    return false; 
    $h = ord($c{$index}); 
    if ($h <= 0x7F) { 
    $bytes = 1; 
    return $h; 
    } 
    else if ($h < 0xC2) 
    return false; 
    else if ($h <= 0xDF && $index < $len - 1) { 
    $bytes = 2; 
    return ($h & 0x1F) << 6 | (ord($c{$index + 1}) & 0x3F); 
    } 
    else if ($h <= 0xEF && $index < $len - 2) { 
    $bytes = 3; 
    return ($h & 0x0F) << 12 | (ord($c{$index + 1}) & 0x3F) << 6 
    | (ord($c{$index + 2}) & 0x3F); 
    } 
    else if ($h <= 0xF4 && $index < $len - 3) { 
    $bytes = 4; 
    return ($h & 0x0F) << 18 | (ord($c{$index + 1}) & 0x3F) << 12 
    | (ord($c{$index + 2}) & 0x3F) << 6 
    | (ord($c{$index + 3}) & 0x3F); 
    } 
    else 
    return false; 
  } 
  
/** 
 * Encode UTF-8 domain name to IDN Punycode 
 * 
 * @param string $value Domain name 
 * @return string Encoded Domain name 
 * 
 * @author Igor V Belousov <igor@belousovv.ru> 
 * @copyright 2013, 2015 Igor V Belousov 
 * @license http://opensource.org/licenses/LGPL-2.1 LGPL v2.1 
 * @link http://belousovv.ru/myscript/phpIDN 
 */ 
function EncodePunycodeIDN( $value ) 
  { 
    if ( function_exists( 'idn_to_ascii' ) ) { 
      return idn_to_ascii( $value ); 
    } 
  
    /* search subdomains */ 
    $sub_domain = explode( '.', $value ); 
    if ( count( $sub_domain ) > 1 ) { 
      $sub_result = ''; 
      foreach ( $sub_domain as $sub_value ) { 
        $sub_result .= '.' . EncodePunycodeIDN( $sub_value ); 
      } 
      return substr( $sub_result, 1 ); 
    } 
  
    /* http://tools.ietf.org/html/rfc3492#section-6.3 */ 
    $n      = 0x80; 
    $delta  = 0; 
    $bias   = 72; 
    $output = array(); 
  
    $input  = array(); 
    $str    = $value; 
    while ( mb_strlen( $str , 'UTF-8' ) > 0 ) 
      { 
        array_push( $input, mb_substr( $str, 0, 1, 'UTF-8' ) ); 
        $str = (version_compare(PHP_VERSION, '5.4.8','<'))?mb_substr( $str, 1, mb_strlen($str, 'UTF-8') , 'UTF-8' ):mb_substr( $str, 1, null, 'UTF-8' ); 
      } 
  
    /* basic symbols */ 
    $basic = preg_grep( '/[\x00-\x7f]/', $input ); 
    $b = $basic; 
  
    if ( $b == $input ) 
      { 
        return $value; 
      } 
    $b = count( $b ); 
    if ( $b > 0 ) { 
      $output = $basic; 
      /* add delimeter */ 
      $output[] = '-'; 
    } 
    unset($basic); 
    /* add prefix */ 
    array_unshift( $output, 'xn--' ); 
  
    $input_len = count( $input ); 
    $h = $b; 
  
    $ord_input = array(); 
  
    while ( $h < $input_len ) { 
      $m = 0x10FFFF; 
      for ( $i = 0; $i < $input_len; ++$i ) 
        { 
          $ord_input[ $i ] = ordUtf8( $input[ $i ] ); 
          if ( ( $ord_input[ $i ] >= $n ) && ( $ord_input[ $i ] < $m ) ) 
            { 
              $m = $ord_input[ $i ]; 
            } 
        } 
      if ( ( $m - $n ) > ( 0x10FFFF / ( $h + 1 ) ) ) 
        { 
          return $value; 
        } 
      $delta += ( $m - $n ) * ( $h + 1 ); 
      $n = $m; 
  
      for ( $i = 0; $i < $input_len; ++$i ) 
        { 
          $c = $ord_input[ $i ]; 
          if ( $c < $n ) 
            { 
              ++$delta; 
              if ( $delta == 0 ) 
                { 
                  return $value; 
                } 
            } 
          if ( $c == $n ) 
            { 
              $q = $delta; 
              for ( $k = 36;; $k += 36 ) 
                { 
                  if ( $k <= $bias ) 
                    { 
                      $t = 1; 
                    } 
                  elseif ( $k >= ( $bias + 26 ) ) 
                    { 
                      $t = 26; 
                    } 
                  else 
                    { 
                      $t = $k - $bias; 
                    } 
                  if ( $q < $t ) 
                    { 
                      break; 
                    } 
                    $tmp_int = $t + ( $q - $t ) % ( 36 - $t ); 
                  $output[] = chr( ( $tmp_int + 22 + 75 * ( $tmp_int < 26 ) ) ); 
                  $q = ( $q - $t ) / ( 36 - $t ); 
                } 
  
              $output[] = chr( ( $q + 22 + 75 * ( $q < 26 ) ) ); 
              /* http://tools.ietf.org/html/rfc3492#section-6.1 */ 
              $delta = ( $h == $b ) ? $delta / 700 : $delta>>1; 
  
              $delta += intval( $delta / ( $h + 1 ) ); 
  
              $k2 = 0; 
              while ( $delta > 455 ) 
                { 
                  $delta /= 35; 
                  $k2 += 36; 
                } 
              $bias = intval( $k2 + 36 * $delta / ( $delta + 38 ) ); 
              /* end section-6.1 */ 
              $delta = 0; 
              ++$h; 
            } 
        } 
      ++$delta; 
      ++$n; 
    } 
    return implode( '', $output ); 
  } 
  
/** 
 * Decode IDN Punycode to UTF-8 domain name 
 * 
 * @param string $value Punycode 
 * @return string Domain name in UTF-8 charset 
 * 
 * @author Igor V Belousov <igor@belousovv.ru> 
 * @copyright 2013, 2015 Igor V Belousov 
 * @license http://opensource.org/licenses/LGPL-2.1 LGPL v2.1 
 * @link http://belousovv.ru/myscript/phpIDN 
 */ 
function DecodePunycodeIDN( $value ) 
  { 
    if ( function_exists( 'idn_to_utf8' ) ) { 
      return idn_to_utf8( $value ); 
    } 
  
    /* search subdomains */ 
    $sub_domain = explode( '.', $value ); 
    if ( count( $sub_domain ) > 1 ) { 
      $sub_result = ''; 
      foreach ( $sub_domain as $sub_value ) { 
        $sub_result .= '.' . DecodePunycodeIDN( $sub_value ); 
      } 
      return substr( $sub_result, 1 ); 
    } 
  
    /* search prefix */ 
    if ( substr( $value, 0, 4 ) != 'xn--' ) 
      { 
        return $value; 
      } 
    else 
      { 
        $bad_input = $value; 
        $value = substr( $value, 4 ); 
      } 
  
    $n      = 0x80; 
    $i      = 0; 
    $bias   = 72; 
    $output = array(); 
  
    /* search delimeter */ 
    $d = strrpos( $value, '-' ); 
  
    if ( $d > 0 ) { 
      for ( $j = 0; $j < $d; ++$j) { 
        $c = $value[ $j ]; 
        $output[] = $c; 
        if ( $c > 0x7F ) 
          { 
            return $bad_input; 
          } 
      } 
      ++$d; 
    } else { 
      $d = 0; 
    } 
  
    while ($d < strlen( $value ) ) 
      { 
        $old_i = $i; 
        $w = 1; 
  
        for ($k = 36;; $k += 36) 
          { 
            if ( $d == strlen( $value ) ) 
              { 
                return $bad_input; 
              } 
            $c = $value[ $d++ ]; 
            $c = ord( $c ); 
  
            $digit = ( $c - 48 < 10 ) ? $c - 22 : 
              ( 
                ( $c - 65 < 26 ) ? $c - 65 : 
                  ( 
                    ( $c - 97 < 26 ) ? $c - 97 : 36 
                  ) 
              ); 
            if ( $digit > ( 0x10FFFF - $i ) / $w ) 
              { 
                return $bad_input; 
              } 
            $i += $digit * $w; 
  
            if ( $k <= $bias ) 
              { 
                $t = 1; 
              } 
            elseif ( $k >= $bias + 26 ) 
              { 
                $t = 26; 
              } 
            else 
              { 
                $t = $k - $bias; 
              } 
            if ( $digit < $t ) { 
                break; 
              } 
  
            $w *= 36 - $t; 
  
          } 
  
        $delta = $i - $old_i; 
  
        /* http://tools.ietf.org/html/rfc3492#section-6.1 */ 
        $delta = ( $old_i == 0 ) ? $delta/700 : $delta>>1; 
  
        $count_output_plus_one = count( $output ) + 1; 
        $delta += intval( $delta / $count_output_plus_one ); 
  
        $k2 = 0; 
        while ( $delta > 455 ) 
          { 
            $delta /= 35; 
            $k2 += 36; 
          } 
        $bias = intval( $k2 + 36  * $delta / ( $delta + 38 ) ); 
        /* end section-6.1 */ 
        if ( $i / $count_output_plus_one > 0x10FFFF - $n ) 
          { 
            return $bad_input; 
          } 
        $n += intval( $i / $count_output_plus_one ); 
        $i %= $count_output_plus_one; 
        array_splice( $output, $i, 0, 
            html_entity_decode( '&#' . $n . ';', ENT_NOQUOTES, 'UTF-8' ) 
         ); 
        ++$i; 
      } 
  
    return implode( '', $output ); 
  }



function utf8_convert_bad($str, $type)
{
   static $conv = '';
   if (!is_array($conv))
   {
      $conv = array();
      for ($x=128; $x <= 143; $x++)
      {
         $conv['utf'][] = chr(209) . chr($x);
         $conv['win'][] = chr($x + 112);
      }
      for ($x=144; $x<= 191; $x++)
      {
         $conv['utf'][] = chr(208) . chr($x);
         $conv['win'][] = chr($x + 48);
      }
      $conv['utf'][] = chr(208) . chr(129);
      $conv['win'][] = chr(168);
      $conv['utf'][] = chr(209) . chr(145);
      $conv['win'][] = chr(184);
   }
   if ($type === 'w')
   {
      return str_replace($conv['utf'], $conv['win'], $str);
   }
   elseif ($type === 'u')
   {
      return str_replace($conv['win'], $conv['utf'], $str);
   }
   else
   {
      return $str;
   }
}



//самая правильная функция, все остальное работает плохо на символах "' и пр.
function utf8_convert($str, $type) {
   if ($type === 'w')
   {
//узнать локали:  locale -a
	setlocale(LC_CTYPE, 'russian','ru_RU','ru_RU.utf8');
	$str2 = iconv('utf-8', 'cp1251//TRANSLIT', $str);

//бывают ситуации обрезания строки, например 'Riga/Rīga/Latvia'
//но вроде //TRANSLIT и setlocale исправляют эту проблему, а также возможно проблему описанную ниже

//в каких-то ситуациях обнуляется строка, поэтому вызываем "плохую" функцию, которая не обнуляет строку (нет другого выхода)
	if (($str!='')&&($str2=='')) {
		$str2 = utf8_convert_bad($str,"w");
	}


   }
   elseif ($type === 'u')
   {
//узнать локали:  locale -a
	setlocale(LC_CTYPE, 'russian','ru_RU','ru_RU.cp1251');
	$str2 = iconv('cp1251','utf-8//TRANSLIT',$str);

//в каких-то ситуациях обнуляется строка, поэтому вызываем "плохую" функцию, которая не обнуляет строку (нет другого выхода)
	if (($str!='')&&($str2=='')) {
		$str2 = utf8_convert_bad($str,"u");
	}


   }
   return $str2;
}





?>
