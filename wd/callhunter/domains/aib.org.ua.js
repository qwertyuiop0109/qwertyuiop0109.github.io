
var SSCHunterCallback = function(type,undefined,undefined,undefined,undefined,undefined,Conv_value) { if (type == 200) { console.log ( 'ConvTime exist' ) } else if ((type >= 1)&&(type < 200)) { console.log ( 'ConvCHSnd exist' ) } else if (type == 201) { console.log ( 'ConvСlick exist' ) } else if (type == 202) { console.log ( 'ConvSubmit exist' ) } else if (type == 203) { console.log ( 'ConvHit exist' ) } else if (type == 204) { console.log ( 'ConvJivo exist' ) } else if (type == 205) { console.log ( 'CallWidget exist' ) } ; if (typeof Conv_value != 'undefined') { console.log ( 'ConvValue exist: ' + Conv_value ) }   }


/*
		var SSCHunterCallback = function(type) { 
//			if (type == 200) {
			if (type == 1) {
				alert(1);

			}
		}
*/





var pagecur=window.location.href.toString().split(window.location.host)[1];
pagecur = pagecur.replace( /^\//, '' ); 
pagecur = pagecur.replace( /(.*?)[\?].*$/, '$1' );
pagecur = pagecur.replace( /\/$/, '' ); 

if ((pagecur=="webadvert.html")||(pagecur=="aa/callhunter.html")||(pagecur=="callhunter.html")||(pagecur=="optimizator.html")||(pagecur=="cpo.html")||(pagecur=="adv.html")||(pagecur=="web.html")) {
  



function isMobile() {
    			return(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino|android|ipad|playbook|silk/i.test(navigator.userAgent || navigator.vendor || window.opera) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test((navigator.userAgent || navigator.vendor || window.opera).substr(0, 4)))
}




//if (!isMobile()) {
//if (0) {


    (function() {
    	var s = document.createElement('script');
		s.type ='text/javascript';
		s.id = 'supportScript';
		s.charset = 'utf-8';
		s.async = true;
//		s.src = 'https://lcab.sms-uslugi.ru/support/support.js?h=ccc956fc437607e143cf9eaee2ff2fa1';
//        s.src = '//online.inteprice.com/support/support.js?h=6302a55d9eef792b2ba28d937f7cf794';
	        s.src = '//me-talk.ru/support/support.js?h=6302a55d9eef792b2ba28d937f7cf794';

		var sc = document.getElementsByTagName('script')[0];
		
		var callback = function(){

            
            /*
				Здесь вы можете вызывать API. Например, чтобы изменить отступ по высоте:
				supportAPI.setSupportTop(200);
			*/

		}
		
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



//}




var IPchatConfig = {
//    "widget_color": "#474747",
//    "widget_color": "#3e8cd6",
    "widget_color": "#427cd4",
//    "widget_padding": "100",
    "widget_padding": "75",
    "widget_orientation": "bottom",
//    "widget_padding": "50",
//    "widget_orientation": "right",

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


//(function(){ var widget_id = '8KWGxr0Aws';
//var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/geo-widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})()







//(function(){ var s = document.createElement('script'); s.type = 'text/javascript'; s.charset="UTF-8"; s.async = true; var a;a="http";a+="\x73\x3a\x2f\x2f";a+="cdn.";a+="sale";a+="-storm";a+=".com";a+="/wd";a+="/";a+="\x63\x61\x6C\x6C\x68\x75\x6E\x74\x65\x72\x2F\x63\x68\x61\x74\x2F\x6A\x73\x2F\x77\x69\x64\x67\x65\x74\x5F\x65\x6E\x5F\x55\x53\x2E\x6A\x73\x3F\x72\x61\x6E\x64\x3D\x31\x34\x34\x35\x32\x36\x37\x39\x39\x37"; s.src = a; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);})();




//только на эти страницах грузим чат
}




    var IPclhrDataUpdate = {



//	"HitElement": ".DOWNLOAD_CALLHUNTER_TARGET", 
//	"TRVClass": "body, .test2", 
//	"TRVClass": ".test2, body", 
//	"TRVClass": ".test2, .test1", 
//	"TRVClass": "body", 
//	"TRVClassValue": ".col-md-6.footer_copyright", 
//	"TRVFunValue": function(producttype,value,currency) { return '345.55'}, 




//	"TRVClass": ".clhr-form", 
//	"TRVClassValue": ".clhr-phone, .clhr-txt", 
//	"TRVFunValue": function(producttype,value,currency) { return value}, 
//	"TRVFunValue": function(producttype,value,currency) { return '345.55'}, 
//	"TRVClassCurrency": ".clhr-phone, .clhr-txt", 
//	"TRVFunCurrency": function(producttype,value,currency) { return 'USDR'},





//	"PRIClass": '.priclhr-ring, .top-logo, .ty-float-left, .top-block-grid, .tel',
//	"PRIOneClick": 1,
//	"PRInightOn": 1,

//	"PRITelMask": "734 41 23, 734 41 24, 234 85 03, 0000-737, 0000-337",
	"PRIRingID": "123",
//	"PRIFormData": {title:'Введите ID прямого звонка', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*", button: "Отправить", callTime: 0},
//	"PRIFormDataPh": {title:'Введите ID прямого звонка', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*", button: "Отправить", callTime: 0},

//	"PRIFormData": {title:'Акция для ПРЯМЫХ звонков с сайта', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*, +ХОЧУ СПЕЦАКЦИЮ!(Ваш телефон для участия в акции)", button: "Отправить", callTime: 0},
//	"PRIFormDataPh": {title:'Акция для ПРЯМЫХ звонков с сайта', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у нас)*, +ХОЧУ СПЕЦАКЦИЮ!(Ваш телефон для участия в акции)", button: "Отправить", callTime: 0},



//	"PRIFormData": {title:'Акция для ПРЯМЫХ звонков с сайта', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у менеджера)*, +ХОЧУ СПЕЦАКЦИЮ!(Ваш телефон для участия в акции)", button: "Отправить", callTime: 0},
//	"PRIFormDataPh": {title:'Акция для ПРЯМЫХ звонков с сайта', titleworktime:'Позвоните нам, узнайте подробности', titlenight:'Позвоните нам в рабочее время,<br>узнайте подробности', titlenightsize: "12px", fields: "<Одноразовый ID(уточните ID у менеджера)*, +ХОЧУ СПЕЦАКЦИЮ!(Ваш телефон для участия в акции)", button: "Отправить", callTime: 0},
//	"PRIRingID": "01(0),02(1)",
//	"PRIOneClick": 1,

//	"TimeOnSite": 1,


        
        "MMFileType": "0",

	"ButtonSensitivity":	"0",

	"ButtonYMobile": 80,
	"ButtonXMobile": 0,
	"ButtonMobilePlace": 'RightTop',
	"ButtonMobileCSS": "background:url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADsAAACACAYAAAC1KBEFAAAEWUlEQVR4nO3ZX2hbVRwH8N+DgojgXkQQBR2CyGruOQ+CIKIP0vVBfBERFkGfCkqLbZrfASE6KQhjEhhM2IYgFDE+KD5simC7pLnn6GLIkDJSq6w0IsXVSlfHQsQ8/HzoTsm6JrlJc//k3POF72v5fbjn3nN6AlwJiksh7AEs1mIt1mIt1mINrsWaWos1tRZrai3W1FqsqbVYU2uxptZiTa3FmlqLNbUWa2ot1tRarKmNPDZZyVK92SAiourWGuVWFyhZOkncRfOwhY2faW/qzQZNlE8T6xEcaWymOncHtBU8upghLg3BVrdrbbFERPn1y+TkU8OP7fRUW3NMnfD8/kYWu/nvtifs+ZoiVpgZXmzu97wnKBFR7caf5CxMDyc2Wcl6huo4308NJ3Zy6Ux8sFwJurB+yTO03myQMz/EWK4EnVv91hO2vLFMzkVv209ksVztLGl9VGyX3NX54f4a7wV3ihH7rO5YaXYgS3gosJ323LO/nCe2mDYDO1aabfvO9vIVHgps+e+VgXyYIo/96Ncv20L7+fcusthOy7ffpxpJ7Fhplmo3r7WF9nLwjzx2v2uY1kz81Pt1jO/YZCVLmStzngcbK81S4VpnaK9bTSDY1r3Ry9VJt6Xr9e8Eis1U5/YdutOgmepc1/NvryclX7HJSrbr5djeJehl2RLtfJBGi+/1vM0MHOt14Fbw0R8+oFwt3/VpDhp6YGynE85BU95YplH3/YFBD4zttnT7zSA+RgPH9nJ94jX9no58x3Y6v/aazcZ1mih/3PeBwXcsV94vszsld3WejqrjA30/fcFOLp3peiDYL/Vmg/Lrl+nYpf5+fgwFy9XOFpSrebvFr26t0dmVnS3I7yfpC1Y3WclSrnaRqtfXbnuCB/0ROZLYqNdiTa3FmlqLNbUWa2ot1tRarKm1WFNrsabWYk2txZpaizW1FmtqIeHiK1yK5bAHCQQLAPBo4c17mEq/y6TYCnsg37E6iWL6Mabws7CHCgSrw1x8lit0wx4uEKyO46ZfZ1Kshj1kIFgAgMPz4/czhR8yiTfDHtZ37O77nBdPcIVfhT1wIFgdLtMvMoXlsAcPBKvDpBhnUvwRNiAQLAAAdycfYBJPcYX/hQ3xHbuLlimHKfFN2JhAsDqOxJe4xKWwUYFgAQCg8MJdjovTTIq/wsb5j72VRGHyYabwk7CBgWB12OLM01ziQtjQQLC7aImvMoUrscACADx0YfxeR2KGK7FtPFbnKTl9mEn8PBZYncQiPsckqlhgdZiaeYMpsRYLLAAAK0wd4jJ9gitRNx6rM1KcepJJ/DoWWB2niKNcikossDrcxbe4wvVYYAEARhbeeZC74jSXomk8VscppjiX+F0ssDqJoniZKbwSCywAAJwbv5spTHOJm+Zjb2WkmHqESfFpLLA6CZV6hinMxwKr4xTxNSbxt1hgAQCOFN6+j8n0ca7wH+OxOkcK049zKb6IBVaHu/g8V/jj/y/rYstz59Y5AAAAAElFTkSuQmCC) center center no-repeat!important; position:fixed!important;bottom:0;right:0!important;height:128px!important;width:59px!important;cursor:pointer",


//заказы через магазин отключаем        
//        "ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', form.well.span8 input.btn.btn-default, input.button.btn[type=submit][value="Оформить заказ"]',
        "ExtFormBtnFC": IPclhrData.ExtFormBtnFC + ', form.well.span8 input.btn.btn-default',
        "consultantOn":"1",


        "consultantSmsMaxNotify":"3",
	"consultantSmsTimeout":"60",
	"consultantTimeoutNoSnd":"0",
	"consultantSendContacts": 'Ваши контакты для обратной связи',
        "consultantAlwaysShowSupport":"1",
	"consultantMMFolder":"",

	"consultantOnlineAct": "2",


	"consultantSettings": {
	        tpo: 0,
	        phone_field: 1,
	        phone_required: 1,
	        email_field: 0,
	        email_required: 0,
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
//		answer: 'Спасибо!</br>Ваш запрос принят, но линия консультанта сейчас занята</br></br>Консультант обязательно свяжется с Вами, если Вы указали верный номер телефона или email<script type="text/javascript">var p = document.getElementById("offline-form-inner");if(p&&p.style&&p.style.height){p.style.height="70%";}</script>',
//		answer: 'Спасибо!</br>Ваш запрос принят, но линия консультанта сейчас занята</br></br>Консультант обязательно свяжется с Вами, если Вы указали верный номер телефона или email<script type="text/javascript">var p = document.getElementById("offline-form-inner");alert(p.style.height)</script>',
//		answer: 'Спасибо!</br>Ваш запрос принят, но линия консультанта сейчас занята</br></br>Консультант обязательно свяжется с Вами, если Вы указали верный номер телефона или email',
		answer: 'Спасибо!</br>Ваш запрос уже передан консультанту,</br>но его линия сейчас занята.</br></br>Консультант свяжется с Вами чуть позже.',
		timeout: 15,
	        days: 15,
	        mobile: 0,
	        worktime: 0,
	        message: "Здравствуйте! Если у Вас есть вопросы по рекламным услугам, я с удовольствием на них отвечу.",
	        invite_off_forced: 1,
	        invite_on_forced: 1,
	        submit: "Получить консультацию",
	        submit_forced: 1,
	        name: 'Консультант по рекламе',
	        name_forced: 1,
//	        avatar: 'data:image/png;base64,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',
	        avatar: '/uploads/avatars/6302a55d9eef792b2ba28d937f7cf794/e63bba5da3f77a8201ac9c75edf1ea56.png',


	        avatar_forced: 0
	 },


        "consultantAutoMsg1": {timeout:15, days:15, mobile:0, worktime:0, name:"Консультант по рекламе", message:"Здравствуйте! Если у Вас есть вопросы, я с удовольствием на них отвечу.",avatar:"https://f19aff30a120de38ca0c24b3eca9b09de686b08c.googledrive.com/host/0B5XusCO6HztZYjgxejdXTThCSDQ/aib.inteprice.com-chat.png"}
    }

if (!((pagecur=="webadvert.html")||(pagecur=="aa/callhunter.html")||(pagecur=="callhunter.html")||(pagecur=="optimizator.html")||(pagecur=="cpo.html")||(pagecur=="adv.html")||(pagecur=="web.html"))) {
	IPclhrDataUpdate.consultantOn='0';
}
