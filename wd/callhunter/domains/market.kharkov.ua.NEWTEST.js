//IPclhrData["ExtFormBtnFCFormPseudo"] = '#popup-purchase-wrapper';
IPclhrData["ExtFormBtnFCForm"] = '#purchase-form';


//		"ExtFormBtnFC": '#popup-checkout-button',
//		"ExtFormBtnFCForm": "#purchase-form",


IPclhrData["testing"] = 'oleg@inteprice.com';
//IPclhrData.SubmitFullBlocked=1;
IPclhrData.NativeAjaxBlocked=1;



//IPclhrData["ExtFormBtnFCForm"] = '#purchase-form';


IPclhrData["ExtFormBtnFCvalidAttr"]='name'
IPclhrData["ExtFormBtnFCvalidVal"]='telephone'






			IPchlEev.clhrSend = function(jQuery,clickbutton,evnt,producttype) { // send data


					function closestform(elem) {
//находим ближайшую форму, даже с учетом невалидного кода
						var target = elem;
						var formclosest=undefined;
						while(1) { 
							if ((typeof (target.tagName) == 'string') && (target.tagName.toLowerCase() === 'form') ) {
								if (!formclosest) {
									formclosest=target;
									break;
								}
							} else if (target.form && (typeof (target.form.tagName) == 'string') && (target.form.tagName.toLowerCase() === 'form') ) {
								if (!formclosest) {
									formclosest=target.form;
									break;
								}
							}
							if (target.parentNode) {
								target = target.parentNode;
							} else {
								break;
							}
							continue;
						}
						return formclosest;
					}



					if(!window.intepriceCallHunterActive) {
						return 'no';
					}




//closest не найдет форму в случае невалидного кода html, например <table><form>.....</form></table>
//да еще и повиснет
//поэтому сделал другую функцию, которая не реагирует на невалидный код
//для универсальности лучше разбивать на два этапа (сначала поиск кнопки, потом поиск формы)
//					var form = jQuery(clickbutton).closest('form');


					if (!evnt.target) {
						evnt.target = evnt.srcElement;
					}
					var formclosest=closestform(evnt.target);


					if ((!formclosest)&&(((producttype == 11)&&(IPclhrData["ExtFormBtnFCForm"]))||((producttype == 10)&&(IPclhrData["ExtFormBtnForm"])))) {
//нет формы связанной с кнопкой, ищем отдельную форму без этой кнопки (такое может быть для ajax-запросов)

						var df = document.forms;
						for(var i = 0; i < df.length; i++){
							var jQdf = jQuery(df[i]);
							var testform = true;
							if((producttype == 11)&&(IPclhrData["ExtFormBtnFCForm"])) {
								testform = jQdf.is(IPclhrData["ExtFormBtnFCForm"])
							} else if ((producttype == 10)&&(IPclhrData["ExtFormBtnForm"])) {
								testform = jQdf.is(IPclhrData["ExtFormBtnForm"])
							}
							if(testform){
								formclosest=df[i];
								break;
							}
						}
					}



					var pseudoform=0;
					if (!formclosest) {
						if((producttype == 11)&&(IPclhrData["ExtFormBtnFCFormPseudo"])) {
//обворачиваем заданный элемент псевдоформой
							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).wrap('<form class="clhr-fcformpseudo" onsubmit="return false;"></form>');
							formclosest=closestform(evnt.target);
							pseudoform=1;
						} else if ((producttype == 10)&&(IPclhrData["ExtFormBtnFormPseudo"])) {
//обворачиваем заданный элемент псевдоформой
							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).wrap('<form class="clhr-formpseudo" onsubmit="return false;"></form>');
							formclosest=closestform(evnt.target);
							pseudoform=1;
						}
					}
					if (!formclosest) {
						return 'no';
					}


					var form = jQuery(formclosest);

					if(form.length == 0){
						return 'no';
					}


					if (!pseudoform) {
//делаем проверку та ли эта форма (если нужно проверить невалидный html-код для внешних форм 10 и 11)
						var testform = true;
						if((producttype == 11)&&(IPclhrData["ExtFormBtnFCForm"])) {
							testform = form.is(IPclhrData["ExtFormBtnFCForm"])
						} else if ((producttype == 10)&&(IPclhrData["ExtFormBtnForm"])) {
							testform = form.is(IPclhrData["ExtFormBtnForm"])
						}
						if(!testform){
							return 'no';
						}
					}



//проверяем наличие признаков, например поиска в outerHTML формы и отлуп для таких (сам outerHTML не кроссброузерный)
					if((producttype == 11)&&IPclhrData["ExtFormFCStop"]) {
						var str=form.clone().wrap('<p>').parent().html();
						var regarr = IPclhrData["ExtFormFCStop"];
//						var re = new RegExp('search','i');
//экранирование спецсимволов
						var regexp=regarr["RegExp"];
						regexp.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
						var re = new RegExp(regexp,regarr["Flags"]);
						if (re.test(str)) {
							return 'no';
						}
					}


//проверяем наличие обязательных признаков, например названия кнопки в outerHTML формы и отлуп для других (сам outerHTML не кроссброузерный)
					if((producttype == 11)&&IPclhrData["ExtFormFCOnly"]) {
						var str=form.clone().wrap('<p>').parent().html();
						var regarr = IPclhrData["ExtFormFCOnly"];
//						var re = new RegExp('search','i');
//экранирование спецсимволов
						var regexp=regarr["RegExp"];
						regexp.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
						var re = new RegExp(regexp,regarr["Flags"]);
						if (!re.test(str)) {
							return 'no';
						}
					}



					if(IPclhrData["testing"])
						IPchlIb.trpt(0);


//PRI!!!!
//удален запрос к статистике



//					if (form[0].ipsubmits) {
//						form[0].submit = form[0].ipsubmits;
//						form[0].ipsubmits = null;
//					}

//PRI!!!!
					var PRIMMFolder=null;
					var PRIIDS=null;


					if(producttype != 11) {

//						if(IPchlEev.chkSndF(jQuery,form) == 1) {
//							return false;
//						}

//PRI!!!!
						var ret = IPchlEev.chkSndF(jQuery,form);
						if (ret['error_sending'] == 1) {
							return false;
						}
						PRIMMFolder=ret['PRIMMFolder'];
						PRIIDS=ret['PRIIDS'];


					} else {

						var notvalid=IPchlEev.ExtF(form,clickbutton);


						if (notvalid == 1) {
							var vld = IPclhrData["ExtFormValid"];
							if (vld&&(vld == 'clhr')) {
								IPchlEev.forceFormFlg=0;
							} else {
//вроде немного рискованно, можно пропускать реальные запросы. Если так будет то, установить в clhr
								IPchlEev.forceFormFlg=4;

/*
//оказалось можно только использовать "старое" решение с IPchlEev.forceFormFlg=4;
//оказалось form.valid() это не решение, такой функции нет - она работает с плугином http://jqueryvalidation.org/valid/
//поэтому по сути происходило тоже что при IPchlEev.forceFormFlg=4; - во первых ошибка на уже этапе jQuery(evnt).preventDefault() - тут уже неправильно ( надо было что-то вроде if (evnt.preventDefault) { evnt.preventDefault(); } else { evnt.returnValue = false; }) не говоря даже об отсутствии функции form.valid(),
//и поэтому дальше после ошибки срабатывал стандартный обработчик, то есть происходило то же что при IPchlEev.forceFormFlg=4;, только не работал код чуть ниже - if (IPclhrData["alertFillRequired"])...
//нашел решение, теперь не надо "отпускать", все под контролем - не нашел
								IPchlEev.forceFormFlg=0;
								jQuery(evnt).preventDefault();
								form.valid();
*/

							}
							if (IPclhrData["alertFillRequired"])
								IPchlIb.clhrMsg(jQuery,form,"c_error", IPclhrData["alertFillRequired"], "");
							return false;
						}



//задача - запустить все обработчики onsubmit и проверить было ли прерывание действия по умолчанию (preventDefault или false)
//а также был ли запуск отправки формы .submit() в процессе выполнения обработчиков (из какого-либо обработчика)

//Наличие IPchlEev.fs (то есть где то к коде был вызов submit() ) перебивает флаг IPchlEev.DP - то есть при IPchlEev.fs=1 отправка формы должна обязательно произойти, даже если был preventDefault

						if ((!IPchlEev.fs)&&IPchlEev.DP) {
//это решение может быть неверным - может появится с задержкой submit поэтому в обработчиках вставлено отслеживание
							IPchlEev.SBMFormFlg=1;
							if (IPchlEev.aj) {
//PRI!!!!
								IPchlEev.SndF(PRIIDS,PRIMMFolder,jQuery,form,clickbutton,producttype,IPchlEev.aj);
//								IPchlEev.SndF(PRIMMFolder,jQuery,form,clickbutton,producttype,IPchlEev.aj);
//								IPchlEev.SndF(jQuery,form,clickbutton,producttype,IPchlEev.aj);
							}
//							IPchlEev.forceFormFlg=0;
							return false;
						} else {
//PRI!!!!
							IPchlEev.SndF(PRIIDS,PRIMMFolder,jQuery,form,clickbutton,producttype);
//							IPchlEev.SndF(PRIMMFolder,jQuery,form,clickbutton,producttype);
//							IPchlEev.SndF(jQuery,form,clickbutton,producttype);
//							IPchlEev.forceFormFlg=0;
							return;
						}


					}

//PRI!!!!
					IPchlEev.SndF(PRIIDS,PRIMMFolder,jQuery,form,clickbutton,producttype);
//					IPchlEev.SndF(PRIMMFolder,jQuery,form,clickbutton,producttype);
//					IPchlEev.SndF(jQuery,form,clickbutton,producttype);
			}
