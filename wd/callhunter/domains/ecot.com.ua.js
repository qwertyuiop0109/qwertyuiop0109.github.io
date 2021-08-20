

var intepriceIPL = 'ecot.inteprice.com';
var intepriceCRT = '14.07.2015';


//отключаем siteheart
window.jqIPAcode("div#sh_button").ready(function () {
	window.jqIPAcode("<style>").prop("type", "text/css").html("div#sh_button { display: none!important; visibility: hidden!important; }").appendTo("head");
});


(function() {
    	var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
		s.src = '//online.inteprice.com/support/support.js?h=88b2a8468b320d64abd731568724dcb4';
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
		"MMFileType": "1",
//есть магазин, не подключаем формы
//		"ExtFormFCStop": {RegExp: '<[^>]+search', Flags: 'i'},
//		"ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', [type=submit], input[type=image], input[type=button], button:not([type=reset])',
		"ButtonSensitivity":	"0",
		"consultantOn": "1",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":""

	}
