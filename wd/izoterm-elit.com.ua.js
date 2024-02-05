if (typeof google_trackConversion_IP == 'function') {
google_trackConversion_IP({
	google_conversion_id: 949604063,
	google_custom_params: window.google_tag_params,
	google_remarketing_only: true
});
}



(function() {
    	var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
		s.src = '//online.inteprice.com/support/support.js?h=fbc980e12b5360720326a73bf1b3360d';
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

	"AdwConvRemark": {
	google_conversion_id: 949604063,
	google_custom_params: window.google_tag_params,
	google_remarketing_only: true
		},

		"MMFileType": "1",
//есть интернет-магазин, отключаем
//		"ExtFormFCStop": {RegExp: '<[^>]+search', Flags: 'i'},
//		"ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', [type=submit], input[type=image], input[type=button], button:not([type=reset])',


		"ButtonSensitivity":	"0",
		"consultantOn": "1",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":"",
        "consultantAutoMsg": {timeout:15, days:15, mobile:0, worktime:0, name:"Офис-менеджер", message:"Здравствуйте! Если у Вас есть вопросы, я с удовольствием на них отвечу.",avatar:"https://f19aff30a120de38ca0c24b3eca9b09de686b08c.googledrive.com/host/0B5XusCO6HztZYjgxejdXTThCSDQ/izoterm-elit.com.ua-chat.png"}
	}
