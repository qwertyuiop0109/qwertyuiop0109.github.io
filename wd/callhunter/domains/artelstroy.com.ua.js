var intepriceIPL = 'artelstroy.inteprice.com';
var intepriceCRT = '28.08.2015';


(function() {
    	var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
		s.src = '//online.inteprice.com/support/support.js?h=dd902dcd30a7bf53743b10317c2185dd';
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

		"CSS": '.clhr-form textarea { line-height: 20px; height: 100px; }  .clhr-form .clhr-cls { line-height: 16px; }',

		"MMFileType": "1",
		"ExtFormFCStop": {RegExp: '<[^>]+search', Flags: 'i'},
		"ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', [type=submit], input[type=image], input[type=button], button:not([type=reset])',
		"consultantOn": "1",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":"",
	}
