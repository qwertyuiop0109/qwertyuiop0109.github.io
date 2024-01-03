
var intepriceIPL = 'maksimed.inteprice.com';
var intepriceCRT = '23.07.2015';


(function() {
    	var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
		s.src = '//online.inteprice.com/support/support.js?h=ca0098d49cdad716642b8a00a7457c42';
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


	var IPclhrDataUpdate = {

		"MMFileType": "2",
		"ExtFormFCStop": {RegExp: '<[^>]+(search|subscribe)', Flags: 'i'},
		"ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', [type=submit], input[type=image], input[type=button], button:not([type=reset])',
		"ButtonSensitivity":	"0",
		"consultantOn": "1",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":""
	}
