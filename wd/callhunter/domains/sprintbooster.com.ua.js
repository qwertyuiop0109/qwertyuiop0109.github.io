

(function() {
    	var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
//		s.src = '//online.inteprice.com/support/support.js?h=d1dda31b8d2e4970edcf37b23c4b9534';
	        s.src = '//me-talk.ru/support/support.js?h=d1dda31b8d2e4970edcf37b23c4b9534';

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
		"ExtFormFCStop": {RegExp: '<[^>]+(search|keyword|subscribe)', Flags: 'i'},
		"ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', [type=submit], input[type=image], input[type=button], button:not([type=reset])',
		"consultantOn": "1",
		"consultantOnlineAct": "2",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":"",

	"consultantSettings": {
	        phone_field: 1,
	        phone_required: 1,
	        email_field: 1,
	        email_required: 1,
	        name_field: 1,
	        name_required: 0,
	        fields_forced: 1,
		Simulation: 1,
	        Simulation_forced: 1,
	        ReqContactsTalk: 1,
	        ReqContactsTalk_forced: 1,
	        ReqContactsJivo: 1,
	        ReqContactsJivo_forced: 0,
	        prepare_message: 'Ваш запрос подготовлен к отправке консультанту!<br><br>Для повышения качества нашего обслуживания, просим Вас представиться:',
	        prepare_forced: 1,
	        title: 'Задать вопрос сейчас',
	        title_off_forced: 1,
	        title_on_forced: 1,
		answer: 'Спасибо!</br>Ваш запрос принят, но линия консультанта сейчас занята.</br></br>Консультант обязательно свяжется с Вами чуть позже.',
		timeout: 15,
	        days: 14,
	        mobile: 0,
	        worktime: 0,
	        message: "Предоставим компетентный ответ по вопросам СПРИНТ-БУСТЕР",
	        invite_off_forced: 1,
	        invite_on_forced: 1,
	        submit: "Получить консультацию",
	        submit_forced: 1,
	        name: 'Консультант',
	        name_forced: 0,
	        avatar: 'data:image/png;base64,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',
	        avatar_forced: 0,
		reviewsrem_forced: 1,
	        tpo: 0
	 },

        "consultantAutoMsg": {timeout:15, days:15, mobile:0, worktime:0, name:"Консультант", message:"Предоставим компетентный ответ по вопросам СПРИНТ-БУСТЕР",avatar:"https://f19aff30a120de38ca0c24b3eca9b09de686b08c.googledrive.com/host/0B5XusCO6HztZYjgxejdXTThCSDQ/sprintbooster.inteprice.com-chat.png"}
	}
