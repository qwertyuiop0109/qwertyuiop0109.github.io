    (function() {
		var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
		s.src = '//online.inteprice.com/support/support.js?h=bec84e4e9bff57ef955722928a18d868';
		var sc = document.getElementsByTagName('script')[0];
		
		var callback = function(){
			/*
				Здесь вы можете вызывать API. Например, чтобы изменить отступ по высоте:
				supportAPI.setSupportTop(200);
			*/
		};
		
		s.onreadystatechange = s.onload = function(){
			var state = s.readyState;
			if (!callback.done && (!state || /loaded|complete/.test(state))) {
				callback.done = true;
				callback();
			}
		};
		
		if (sc) sc.parentNode.insertBefore(s, sc);
		else document.documentElement.firstChild.appendChild(s);
	})();

/*
if (IPclhrData) {
    IPclhrData.consultantOn="1";
	IPclhrData.consultantSmsMaxNotify="2";
	IPclhrData.consultantSmsTimeout="60";
	IPclhrData.consultantTimeoutNoSnd="0";
	IPclhrData.consultantMMFolder=""; 
//	IPclhrData.ButtonPlace="RightTop"; 
	IPclhrData.ButtonSensitivity="100"; 
	IPclhrData.titleworktime="Мы перезвоним через 1 мин. 56 сек."; 
}
*/

	var IPclhrDataUpdate = {
		"MMFileType": "1",
		"ExtFormFCStop": {RegExp: '<[^>]+search', Flags: 'i'},
		"ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', [type=submit], input[type=image], input[type=button], button:not([type=reset])',
		"consultantOn": "1",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantMMFolder":"",
//		"consultantShowInFrame":1
	}