//viktoriabondarchuk.od.ua

//-- Yandex.Metrika counter --

(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter27053554 = new Ya.Metrika({id:27053554,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";


//-- add --
    window.YAcounter=0;
    s.onreadystatechange = s.onload = function(){
    var state = s.readyState;
	if ((!state)||(state&&((state=='loaded')||(state=='complete')))) {
		for (var key in window) {
			if ( (typeof(window[key]) == 'object') && (key.toString().search('yaCounter') != -1) && (typeof(window[key].reachGoal) == 'function') ) {
				s.onreadystatechange = s.onload = null;
				window.YAcounter=window[key];
				break;
			}
		}
		if (typeof window.trackRingHitIA == 'function') { window.trackRingHitIA(); }
	}
    }
//-- /add --


    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");

//-- /Yandex.Metrika counter --




    (function() {
		var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
//		s.src = '//online.inteprice.com/support/support.js?h=21acf82ac895b289653676221449b130';
	        s.src = '//me-talk.ru/support/support.js?h=21acf82ac895b289653676221449b130';

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



	"AdwConvCHSnd": {
	google_conversion_id: 966023720,
	google_conversion_language: "en",
	google_conversion_format: "3",
	google_conversion_color: "ffffff",
	google_conversion_label: "Axq5CKn_-GYQqLTRzAM",
	google_remarketing_only: false
		},




	"AdwConvTime": {
	google_conversion_id: 966023720,
	google_conversion_language: "en",
	google_conversion_format: "3",
	google_conversion_color: "ffffff",
	google_conversion_label: "4stfCO-UiGcQqLTRzAM",
	google_remarketing_only: false
		},


	"PRIClass": '.priclhr-ring, h3, .footer_elements span',
//	"PRIOneClick": 1,
	"PRITelMask": "(097) 563-08-50,(093) 882-13-97",
	"PRIRingID": "50",
	"PRIFormData": {title:'Введите ID прямого звонка', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*", button: "Отправить", callTime: 0},
	"PRIFormDataPh": {title:'Введите ID прямого звонка', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*", button: "Отправить", callTime: 0},

//	"PRIFormData": {title:'Акция для ПРЯМЫХ звонков с сайта', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*, +ХОЧУ СПЕЦАКЦИЮ!(Ваш телефон для участия в акции)", button: "Отправить", callTime: 0},
//	"PRIFormDataPh": {title:'Акция для ПРЯМЫХ звонков с сайта', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*, +ХОЧУ СПЕЦАКЦИЮ!(Ваш телефон для участия в акции)", button: "Отправить", callTime: 0},




		"MMFileType": "0",
		"ExtFormFCStop": {RegExp: '<[^>]+(search|subscribe)', Flags: 'i'},
		"ExtFormBtnFC": '.clhr-btnForceClick, [type=submit], input[type=image], input[type=button], button:not([type=reset])',
		"ButtonSensitivity":	"0",
		"consultantOn": "1",
		"consultantSmsMaxNotify":"3",
		"consultantSmsTimeout":"60",
		"consultantTimeoutNoSnd":"0",
		"consultantSendContacts": 'Ваши контакты для обратной связи',
		"consultantAlwaysShowSupport":"1",
		"consultantMMFolder":""
	}