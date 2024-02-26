
var SSCHunterGa=function(b,c){try{var a=window[window.GoogleAnalyticsObject];a(function(){a.getAll().forEach(function(a){a.send("event",b,c)})})}catch(d){}};
//var SSCHunterCallback = function(type,undefined,undefined,undefined,undefined,undefined,Conv_value) { if (type == 200) { console.log ( 'ConvTime exist' ); if (typeof SSCHunterGa == 'function') { SSCHunterGa('Hot Activity', 'ss_hot_activity'); } } else if ((type >= 1)&&(type < 200)) { console.log ( 'ConvCHSnd exist' ); if (typeof SSCHunterGa == 'function') { SSCHunterGa('Client Hunter', 'ss_send_message'); } } else if (type == 201) { console.log ( 'ConvСlick exist' ) } else if (type == 202) { console.log ( 'ConvSubmit exist' ) } else if (type == 203) { console.log ( 'ConvHit exist' ); if (typeof SSCHunterGa == 'function') { SSCHunterGa('View Page', 'ss_page_hit'); } } else if (type == 204) { console.log ( 'ConvJivo exist' ) } else if (type == 205) { console.log ( 'CallWidget exist' ) } ; if (typeof Conv_value != 'undefined') { console.log ( 'ConvValue exist: ' + Conv_value ) }   }
var SSCHunterCallback=function(type,undefined1,undefined2,undefined3,undefined4,undefined5,Conv_value){if(type==200){console.log("ConvTime exist");if(typeof SSCHunterGa=="function")SSCHunterGa("Hot Activity","ss_hot_activity")}else if(type>=1&&type<200){console.log("ConvCHSnd exist");if(typeof SSCHunterGa=="function")SSCHunterGa("Client Hunter","ss_send_message")}else if(type==201){console.log("ConvClick exist");if(typeof SSCHunterGa=="function")SSCHunterGa("Click","ss_click_event")}else if(type==202){console.log("ConvSubmit exist");if(typeof SSCHunterGa=="function")SSCHunterGa("Submit","ss_form_submit")}else if(type==203){console.log("ConvHit exist");if(typeof SSCHunterGa=="function")SSCHunterGa("View Page","ss_page_hit")}else if(type==204){console.log("ConvJivo exist");if(typeof SSCHunterGa=="function")SSCHunterGa("Jivo Chat","ss_jivo_message")}else if(type==205){console.log("CallWidget exist");if(typeof SSCHunterGa=="function")SSCHunterGa("Call Widget","ss_widget_callback")}if(typeof Conv_value!="undefined")console.log("ConvValue exist: "+Conv_value)};



	var notexisthunter = 0;

	var pagecur=window.location.href.toString().split(window.location.host)[1];
	pagecur = pagecur.replace( /^\//, '' ); 
	pagecur = pagecur.replace( /^(.*?)\/.*$/, '$1' ); 

//у юзера product - основная страница с продукцией при посещении сайта, нельзя убирать на ней охотника
//	if ((pagecur.indexOf('user') == 0)||(pagecur.indexOf('product') == 0)) {
	if (pagecur.indexOf('user') == 0) {
		notexisthunter = 1;
	}



//без предоплаты, логин менеджера
var intepriceIPL = 'kiaparts.inteprice.com';
var intepriceCRT = '18.09.2017';


var IPchatConfig = {
//    "widget_color": "#474747",
    "widget_color": "#427cd4",
//    "widget_padding": "100",
//    "widget_padding": "50",
//    "widget_padding": "0",
    "widget_padding": "75",
//    "widget_orientation": "right",
    "widget_orientation": "bottom",
//    "font_type": "normal",
    "font_type": "bold",
    "offline_widget_label": "Отправьте нам сообщение",
    "offline_form_text": "Оставьте свое сообщение в этой форме, и мы получим его на e-mail и обязательно ответим!",
    "custom_langpack": {
	"lblOfflineMessageSend": "Сообщение отправлено",
        "lblMessageError": "К сожалению, сообщение не было отправлено по техническим причинам",
        "lblLookingForFreeAgent": "Подключаемся...",
        "lblIntroduceName": "Ваше имя",
        "lblEvaluateCancel": "Отменить",
        "lblSend": "Отправить",
        "lblThankYouMessage": "Спасибо за ваше сообщение! Мы&nbsp;обязательно свяжемся с&nbsp;Вами в ближайшее время",
        "lblYourEmail": "Ваш E-mail",
        "lblYourMessage": "Ваше сообщение",
        "lblYourPhone": "Ваш телефон",
        "lnkCloseThisWindow": "Закрыть",
        "ofl_enterMessage": "Пожалуйста, введите сообщение",
        "ofl_enterName": "Пожалуйста, введите имя",
        "ofl_enterValidEmail": "Указан неверный e-mail",
        "ofl_enterValidPhone": "Указан неверный телефон"
    }

};


(function(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.charset="UTF-8"; s.async = true; var a;a="http";a+="\x73\x3a\x2f\x2f";a+="cdn.";a+="sale";a+="-storm";a+=".com";a+="/wd";a+="/";a+="\x63\x61\x6C\x6C\x68\x75\x6E\x74\x65\x72\x2F\x63\x68\x61\x74\x2F\x6A\x73\x2F\x77\x69\x64\x67\x65\x74\x5F\x65\x6E\x5F\x55\x53\x2E\x6A\x73\x3F\x72\x61\x6E\x64\x3D\x31\x34\x34\x35\x32\x36\x37\x39\x39\x37"; s.src = a; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();


if (0) {



	(function() {
    	var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
		s.src = '//me-talk.ru/support/support.js?h=.......................................';
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

}


	var IPclhrDataUpdate = {

/*

может не учитывать некоторые результаты

	"sodship": {
	        'SMSName': 'DROPSHIP',  //начало СМС-сообщения (без |)
	        'LetterName': 'DROPSHIP', //начало заголовка в почтовом сообщении (без |)
	        'Days': 45 //сколько дней хранить кук
	},

*/



		"ViewFormClick": '.callhunter_viewform_click, #search_callback, #search_callback_vin',



//	"showButton": 	"0",


//	"workStart": "09",
//	"workEnd": "18",
	"workDays": "Mon, Tue, Wed, Thu, Fri, Sat",



		"AdwConvRemark": [
		{
		google_conversion_id: 982175123,
		google_custom_params: window.google_tag_params,
		google_remarketing_only: true
			},
		{
		google_conversion_id: 746584469,
		google_custom_params: window.google_tag_params,
		google_remarketing_only: true
			}

		],


		"AdwConvTime": [
		{
		google_conversion_id: 982175123,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "ih76CJflp3UQk5ur1AM",
		google_remarketing_only: false
			},
		{
		google_conversion_id: 746584469,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "mQEjCNyJ4p8BEJXz_-MC",
		google_remarketing_only: false
			}
		],



		"AdwConvCHSnd": [
		{
		google_conversion_id: 982175123,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "eDdPCPrbj3UQk5ur1AM",
		google_remarketing_only: false
			},
		{
		google_conversion_id: 746584469,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "JjtiCNLW4p8BEJXz_-MC",
		google_remarketing_only: false
			}
		],





		"SetCallWidget": {button: "#bingc-passive-phone-form-button, #bingc-active-phone-form-button", phoneField:'#bingc-passive-get-phone-form-input, #bingc-active-get-phone-form-input', phoneRegExp:10, phoneFound:'#bingc-passive-content .bingc-we-will-call-you-later, #bingc-passive-content .bingc-we-already-call-you' },

		"AdwConvCallWidget": {
		google_conversion_id: 746584469,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "bmx4CO6ynaUBEJXz_-MC",
		google_remarketing_only: false
		},





		"HitElement": "#content > div > p > b",
		"HitElementHtml": {RegExp: 'Уважаемый клиент!', Flags: 'i'},


		"AdwConvHit": [
		{
		google_conversion_id: 982175123,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "e9d2CKn4oXUQk5ur1AM",
		google_remarketing_only: false
			},
		{
		google_conversion_id: 746584469,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "f6eBCO7R4p8BEJXz_-MC",
		google_remarketing_only: false
			}
		],








/*

вариант с регистрацией


		"HitElement": "#content > p:nth-child(1)",
		"HitElementHtml": {RegExp: 'Ваш аккаунт був успішно зареєстрований на нашому сайті', Flags: 'i'},


		"AdwConvHit": {
		google_conversion_id: 746584469,
		google_conversion_language: "en",
		google_conversion_format: "3",
		google_conversion_color: "ffffff",
		google_conversion_label: "aIcqCIHN9aEBEJXz_-MC",
		google_remarketing_only: false
			},



*/


		"title": "Получить консультацию",
		"titleworktime": "Мы свяжемся с Вами через несколько минут",
		"titlenight": "Мы свяжемся с Вами в рабочее время",
		"button": "Жду ответ!",
		"callTime": "0",
//		"fields": "+Введите ваш телефон(+380)*, Введите ваш Email*, -Тема (Ваш вопрос)*",
//		"fields": "+Введите ваш телефон(+380)*, -Тема (Ваш вопрос)*, VIN-код автомобиля (Необязательно), Ваш Email (Необязательно)",
		"fields": "+Введите ваш телефон(+380)*, -Тема (Ваш вопрос)*, VIN-код автомобиля (Необязательно)",
		"emptyPhone": "+380",


		"ButtonPlace":	"RightTop",
		"ButtonY":	"240",




		"MMFileType": "2",
		"ExtFormFCStop": {RegExp: '<[^>]+(search|subscribe|password)', Flags: 'i'},
//		"ExtFormBtnFC": '[type=submit], input[type=image], input[type=button], button:not([type=reset])',
		"ButtonSensitivity":	"0",
		"ButtonTransform": 3,
		"ButtonYMobile": 80,
		"ButtonXMobile": 0,
		"ButtonMobilePlace": 'RightTop',
		"ButtonMobileCSS": "background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADsAAACACAYAAAC1KBEFAAAEWUlEQVR4nO3ZX2hbVRwH8N+DgojgXkQQBR2CyGruOQ+CIKIP0vVBfBERFkGfCkqLbZrfASE6KQhjEhhM2IYgFDE+KD5simC7pLnn6GLIkDJSq6w0IsXVSlfHQsQ8/HzoTsm6JrlJc//k3POF72v5fbjn3nN6AlwJiksh7AEs1mIt1mIt1mINrsWaWos1tRZrai3W1FqsqbVYU2uxptZiTa3FmlqLNbUWa2ot1tRarKmNPDZZyVK92SAiourWGuVWFyhZOkncRfOwhY2faW/qzQZNlE8T6xEcaWymOncHtBU8upghLg3BVrdrbbFERPn1y+TkU8OP7fRUW3NMnfD8/kYWu/nvtifs+ZoiVpgZXmzu97wnKBFR7caf5CxMDyc2Wcl6huo4308NJ3Zy6Ux8sFwJurB+yTO03myQMz/EWK4EnVv91hO2vLFMzkVv209ksVztLGl9VGyX3NX54f4a7wV3ihH7rO5YaXYgS3gosJ323LO/nCe2mDYDO1aabfvO9vIVHgps+e+VgXyYIo/96Ncv20L7+fcusthOy7ffpxpJ7Fhplmo3r7WF9nLwjzx2v2uY1kz81Pt1jO/YZCVLmStzngcbK81S4VpnaK9bTSDY1r3Ry9VJt6Xr9e8Eis1U5/YdutOgmepc1/NvryclX7HJSrbr5djeJehl2RLtfJBGi+/1vM0MHOt14Fbw0R8+oFwt3/VpDhp6YGynE85BU95YplH3/YFBD4zttnT7zSA+RgPH9nJ94jX9no58x3Y6v/aazcZ1mih/3PeBwXcsV94vszsld3WejqrjA30/fcFOLp3peiDYL/Vmg/Lrl+nYpf5+fgwFy9XOFpSrebvFr26t0dmVnS3I7yfpC1Y3WclSrnaRqtfXbnuCB/0ROZLYqNdiTa3FmlqLNbUWa2ot1tRarKm1WFNrsabWYk2txZpaizW1FmtqIeHiK1yK5bAHCQQLAPBo4c17mEq/y6TYCnsg37E6iWL6Mabws7CHCgSrw1x8lit0wx4uEKyO46ZfZ1Kshj1kIFgAgMPz4/czhR8yiTfDHtZ37O77nBdPcIVfhT1wIFgdLtMvMoXlsAcPBKvDpBhnUvwRNiAQLAAAdycfYBJPcYX/hQ3xHbuLlimHKfFN2JhAsDqOxJe4xKWwUYFgAQCg8MJdjovTTIq/wsb5j72VRGHyYabwk7CBgWB12OLM01ziQtjQQLC7aImvMoUrscACADx0YfxeR2KGK7FtPFbnKTl9mEn8PBZYncQiPsckqlhgdZiaeYMpsRYLLAAAK0wd4jJ9gitRNx6rM1KcepJJ/DoWWB2niKNcikossDrcxbe4wvVYYAEARhbeeZC74jSXomk8VscppjiX+F0ssDqJoniZKbwSCywAAJwbv5spTHOJm+Zjb2WkmHqESfFpLLA6CZV6hinMxwKr4xTxNSbxt1hgAQCOFN6+j8n0ca7wH+OxOkcK049zKb6IBVaHu/g8V/jj/y/rYstz59Y5AAAAAElFTkSuQmCC) center center no-repeat!important; position:fixed!important;bottom:0;right:0!important;height:128px!important;width:59px!important;cursor:pointer",


		"consultantOn": "4",


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
		answer: 'Спасибо!</br>Ваш запрос уже передан консультанту,</br>но его линия сейчас занята.</br></br>Консультант свяжется с Вами чуть позже.',
		timeout: 15,
	        days: 14,
	        mobile: 0,
	        worktime: 0,
	        message: "Здравствуйте! Если у Вас есть вопросы, я с удовольствием на них отвечу.",
	        invite_off_forced: 1,
	        invite_on_forced: 1,
	        submit: "Получить консультацию",
	        submit_forced: 1,
	        name: 'Консультант',
	        name_forced: 0,
	        avatar: 'data:image/png;base64,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',
	        avatar_forced: 0,
		reviewsrem_forced: 1,
	        tpo: 1
	 },


		"consultantOnlineAct": "3",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":""
	}


	if (notexisthunter) {
		IPclhrDataUpdate.hunterOn='0';
		IPclhrDataUpdate.showButton='0';
	}




//для настроенных бесплатных, которые оплачены
	try {
		if (window.intepriceCRT) {
			var match = window.intepriceCRT.match(/^(\d\d?)[\.\/](\d\d?)[\.\/](\d\d\d\d)$/m);
			if (match) {
				var dateCRT = match[2] + '/' + match[1] + '/' + match[3];
				var gmt = new Date(dateCRT);
				gmt.setTime(gmt.getTime() + (gmt.getTimezoneOffset()*60*1000) + Number(window.IPcallhunterDiff));
				var currd = new Date();
				if ((currd.getTime() - gmt.getTime())/(24*60*60*1000) < 30) {
					IPclhrDataUpdate.freefull=1;
				}
			}
		}
	} catch (e) { }
