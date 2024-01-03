

	(function() {
		var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;

	        s.src = '//me-talk.ru/support/support.js?h=d1dda31b8d2e4970edcf37b23c4b9534';

		var sc = document.getElementsByTagName('script')[0];
		
		
		s.onreadystatechange = s.onload = function(){
			var state = s.readyState;
			if (!callback.done && (!state || /loaded|complete/.test(state))) {
				callback.done = true;
				callback();
			}
		}
		
		if (sc) sc.parentNode.insertBefore(s, sc);
		else document.documentElement.firstChild.appendChild(s);
	})();



	var IPclhrDataUpdate = {
		"MMFileType": "1",
		"ExtFormFCStop": {RegExp: '<[^>]+(search|keyword|subscribe)', Flags: 'i'},
		"ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', [type=submit], input[type=image], input[type=button], button:not([type=reset])',

		"B1CFormData": {title:'Купить <font color="green"><!--item--></font>', button:"Заказать", fields:'+Ваш телефон(+380)*, =Артикул[<!--kod-->], =Продукция[<!--item-->], -Ваш комментарий(необязательно)'},
		"B1CFormDataPh": {title:'Купить <font color="green"><!--item--></font>', button:"Заказать", fields:'+Ваш телефон(+380)*, =Артикул[<!--kod-->], =Продукция[<!--item-->], -Ваш комментарий(необязательно)'},

//мешает кнопке "вверх"
	"ButtonX":	"200",


		"consultantOn": "1",
		"consultantOnlineAct": "2",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":""
	}

