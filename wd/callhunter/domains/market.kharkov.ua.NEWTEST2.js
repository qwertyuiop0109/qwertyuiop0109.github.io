//IPclhrData["ExtFormBtnFCFormPseudo"] = '#popup-purchase-wrapper';
IPclhrData["ExtFormBtnFCFormPseudo"] = '#purchase-form';

IPclhrData["testing"] = 'oleg@inteprice.com';
IPclhrData.SubmitFullBlocked=1;



				function startfirstclick(e) {
//forceFormFlg 0-не нажата кнопка 1-кликнута, обрабатывается 4 - ошибки валидации формы
//ошибка обязат полей при отправке должна обрабатываться стандартной процедурой проверки формы (4)
						var ret='';
						var button = '';
						if (!IPchlEev.forceFormFlg) {
							IPchlEev.forceFormFlg=1;

							button = jQuery(this)[0];
							button.onclick = button.clvlip;
							button.clvlip = false;
							if ((IpAid != "free")||((IpAid == "free")&&(IPclhrData["testing"]||IPclhrData["freefull"]))) {
								IPchlIb.restdubl();
								eval(IPclhrData["ExtFormFCEvent"]);
								IPchlIb.dubl(IPclhrData["ExtFormFCFormData"]);
							}
							ret=IPchlEev.clhrSend(jQuery,jQuery(this),e,11);
							if (IPchlEev.forceFormFlg == 4) {
								IPchlEev.forceFormFlg=0;
								return true;
							}
							IPchlEev.forceFormFlg=0;
						}
						if (ret == 'no') {
//если нет формы у кнопки, но которую кликнули - выполняем стандартное действие onclick
							if (typeof button.onclick == "function") {
//@@@@@@@@@@@@@@@@@@
//								return button.onclick.call(this,event);
								return button.onclick.call(this,e);
							} else {
								return true;
							}
						} else {
//это натуральный js-вызов, поэтому здесь нет для ie preventDefault, надо фиксить
							if (e.preventDefault) {
								e.preventDefault();
							} else {
								e.returnValue = false;
							}
//							e.preventDefault();
							return false;
						}
				}


//mouseup выполняется раньше всех обработчиков кликов
				jQuery(document).delegate(IPclhrData["ExtFormBtnFC"], 'mouseup', function(e) {
					if (jQuery(this).closest('.clhr-form').length) {
						return;
					}
					if (!IPchlEev.forceFormFlg) {
						IPchlEev.ar=0;
						var button = jQuery(this)[0];
						if (!button.clvlip) {
							button.clvlip = button.onclick;
						}
						button.onclick = function onclick(event) {
//							alert('firstclick()')
							event = event || window.event //для ie8
//считаем что это первый вызов обработчика кликов (onclick должен выполняться раньше всех, а onsubmit последним)
							startfirstclick.call(this,event);
						}
					}

				});


//это контрольный обработчик (вдруг onclick выполняется не первым обработчиком, возможно последним, хоть как-то выполним нашу задачу - выполним все обработчики тегa form[0] - form[0].dispatchEvent(evtsubmit))
				jQuery(document).delegate(IPclhrData["ExtFormBtnFC"], 'click', function(e) { 
					IPchlEev.ar=1;
				});





			IPchlEev.clhrSend = function(jQuery,clickbutton,evnt,producttype) { // send data

//alert(112333)


					function closestform(elem) {
//находим ближайшую форму, даже с учетом невалидного кода
						var target = elem;
						var formclosest=undefined;
//alert(target.tagName)

//alert(target.form)

						while(1) { 
//alert(1)
							if ((typeof (target.tagName) == 'string') && (target.tagName.toLowerCase() === 'form') ) {
//alert(2)
								if (!formclosest) {
									formclosest=target;
									break;
								}
							} else if (target.form && (typeof (target.form.tagName) == 'string') && (target.form.tagName.toLowerCase() === 'form') ) {
//alert(3)
								if (!formclosest) {
									formclosest=target.form;
									break;
								}
							}
							if (target.parentNode) {
//alert(4)
								target = target.parentNode;
							} else {
								break;
							}
//alert(2)
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
//alert(66666666666)
						evnt.target = evnt.srcElement;
					}
					var formclosest=closestform(evnt.target);
//alert(1123332222222222222)

					var pseudoform=0;
					if (!formclosest) {
						if((producttype == 11)&&(IPclhrData["ExtFormBtnFCFormPseudo"])) {
//обворачиваем заданный элемент псевдоформой
							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).wrap('<form class="clhr-fcformpseudo" onsubmit="return false;"></form>');
							formclosest=closestform(evnt.target);
//alert(999999)
//alert(formclosest)

							pseudoform=1;
						} else if ((producttype == 10)&&(IPclhrData["ExtFormBtnFormPseudo"])) {
//обворачиваем заданный элемент псевдоформой
							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).wrap('<form class="clhr-formpseudo" onsubmit="return false;"></form>');
							formclosest=closestform(evnt.target);
							pseudoform=1;
						}
					}


//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

					if ((!formclosest)&&IPclhrData["ExtFormBtnFCFormPseudo"]) {
//если не найдены форма или псевдоформа кнопки, то если разрешено и указана псевдоформа
//ищем первую попавшуюся псевдоформу (уже без привязки к кнопке) и отправляем только нашу форму, без отправки псевдоформы

var formclassName = 'clhr-fcformpseudo' + randomInteger(1,99999999999);
var buttonclassName = formclassName;

						if((producttype == 11)&&(IPclhrData["ExtFormBtnFCFormPseudo"])) {
alert('=============')
//добавляем в заданный элемент дочерний в конец
//
							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).append('<button class="' + buttonclassName + '" type="submit" hidden="" value="' + buttonclassName + '">' + buttonclassName + '</button>');
//обворачиваем заданный элемент псевдоформой
							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).wrap('<form class="' + formclassName + '" onsubmit="return false;"></form>');
							formclosest=findFormByClass(formclassName)

//получаем скрытую кнопку псефдоформы (что бы все дальше работало верно, может и не нужно, но на всякий)
//							clickbutton = jQuery(findButtonByClass(buttonclassName));

//console.log(clickbutton)

var str=jQuery(formclosest).clone().wrap('<p>').parent().html();
console.log(str);
alert(str);


alert(formclosest.elements.length);

formclosest=document.getElementById('purchase-form')
alert(formclosest.elements.length);


return;




							pseudoform=1;
						} else if ((producttype == 10)&&(IPclhrData["ExtFormBtnFormPseudo"])) {

//добавляем в заданный элемент дочерний в конец
//							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).append('<button class="' + buttonclassName + '" type="submit" hidden="" value="' + buttonclassName + '">' + buttonclassName + '</button>');

//обворачиваем заданный элемент псевдоформой
							jQuery(IPclhrData["ExtFormBtnFCFormPseudo"]).wrap('<form class="' + formclassName + '" onsubmit="return false;"></form>');
							formclosest=findFormByClass(formclassName)

//получаем скрытую кнопку псефдоформы (что бы все дальше работало верно, может и не нужно, но на всякий)
//							clickbutton = jQuery(findButtonByClass(buttonclassName));

							pseudoform=1;
						}
					}



function randomInteger(min, max) {
  // случайное число от min до (max+1)
  let rand = min + Math.random() * (max + 1 - min);
  return Math.floor(rand);
}

function findFormByClass(className) {

//https://coderoad.ru/3808808/Как-получить-элемент-по-классу-в-JavaScript

//	var descendants=document.getElementsByTagName('form'), i=-1, e, result=[];
	var descendants=document.getElementsByTagName('form'), i=-1, e, result=null;

  while (e=descendants[++i]) {
//ищем первую форму с таким классом
//    ((' '+(e['class']||e.className)+' ').indexOf(' '+className+' ') > -1) && result.push(e);

	if ((' '+(e['class']||e.className)+' ').indexOf(' '+className+' ') > -1) {
		result = e;
		break;
	}

//    ((' '+(e['class']||e.className)+' ').indexOf(' '+className+' ') > -1) && result = e && break;
  }
  return result;

}


function findButtonByClass(className) {

//https://coderoad.ru/3808808/Как-получить-элемент-по-классу-в-JavaScript

//	var descendants=document.getElementsByTagName('button'), i=-1, e, result=[];
	var descendants=document.getElementsByTagName('button'), i=-1, e, result=null;

  while (e=descendants[++i]) {
//ищем первую форму с таким классом
//    ((' '+(e['class']||e.className)+' ').indexOf(' '+className+' ') > -1) && result.push(e);

	if ((' '+(e['class']||e.className)+' ').indexOf(' '+className+' ') > -1) {
		result = e;
		break;
	}

//    ((' '+(e['class']||e.className)+' ').indexOf(' '+className+' ') > -1) && result = e && break;
  }
  return result;

}




//alert(112333222)

					if (!formclosest) {
						return 'no';
					}

alert('-------------')

					var form = jQuery(formclosest);

//alert(formclosest.elements.length)
//alert(form[0].elements.length)



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


//alert('!!!!!!!!!!!!!!!!!')

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



alert('IPchlEev.clhrsnd =' +IPchlEev.clhrsnd)



						var notvalid=IPchlEev.ExtF(form,clickbutton);
alert('IPchlEev.clhrsnd =' +IPchlEev.clhrsnd)

alert(notvalid)

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

alert('tttt222')


//задача - запустить все обработчики onsubmit и проверить было ли прерывание действия по умолчанию (preventDefault или false)
//а также был ли запуск отправки формы .submit() в процессе выполнения обработчиков (из какого-либо обработчика)

//Наличие IPchlEev.fs (то есть где то к коде был вызов submit() ) перебивает флаг IPchlEev.DP - то есть при IPchlEev.fs=1 отправка формы должна обязательно произойти, даже если был preventDefault

						if ((!IPchlEev.fs)&&IPchlEev.DP) {
//это решение может быть неверным - может появится с задержкой submit поэтому в обработчиках вставлено отслеживание
alert('tttt1111')
							IPchlEev.SBMFormFlg=1;
							if (IPchlEev.aj) {
alert('tttt11113333')
//PRI!!!!
								IPchlEev.SndF(PRIIDS,PRIMMFolder,jQuery,form,clickbutton,producttype,IPchlEev.aj);
//								IPchlEev.SndF(PRIMMFolder,jQuery,form,clickbutton,producttype,IPchlEev.aj);
//								IPchlEev.SndF(jQuery,form,clickbutton,producttype,IPchlEev.aj);
							}
//							IPchlEev.forceFormFlg=0;
							return false;
						} else {
//PRI!!!!
alert('tttt44444')
							IPchlEev.SndF(PRIIDS,PRIMMFolder,jQuery,form,clickbutton,producttype);
//							IPchlEev.SndF(PRIMMFolder,jQuery,form,clickbutton,producttype);
//							IPchlEev.SndF(jQuery,form,clickbutton,producttype);
//							IPchlEev.forceFormFlg=0;
							return;
						}

alert('tttt')


					}

//PRI!!!!
					IPchlEev.SndF(PRIIDS,PRIMMFolder,jQuery,form,clickbutton,producttype);
//					IPchlEev.SndF(PRIMMFolder,jQuery,form,clickbutton,producttype);
//					IPchlEev.SndF(jQuery,form,clickbutton,producttype);
			}







	IPchlEev.SndFssssss = function(PRIIDS,PRIMMFolder,jQuery,form,e,producttype,ajaxflg) {
//	IPchlEev.SndF = function(PRIIDS,PRIMMFolder,jQuery,form,e,producttype,ajaxflg) {
//	this.SndF = function(jQuery,form,e,producttype,ajaxflg) {


alert(IPchlEev.clhrsnd)

//alert(12113143)

/*
					if((IPclhrData["resendStop"] != "0")&&(producttype != 11)) {
//						if(jQuery(clhrForm).is(":visible")) {
						if(producttype != 10) {
//это форма охотника
//							jQuery(".clhr-form .clhr-btn").attr("disabled", "disabled");
							form.find(".clhr-btn").attr("disabled", "disabled");
						} else {
//это внешняя форма (но не форсированный клик)
							if (IPclhrData["ExtFormBtn"])
								form.find(IPclhrData["ExtFormBtn"]).attr("disabled", "disabled");
						}
					}
*/

//добавлена обработка форм с форсированным кликом (бывают нервные по пять раз делают клик)
					if(IPclhrData["resendStop"] != "0") {
						if(producttype == 10) {
//это внешняя форма (но не форсированный клик)
							if (IPclhrData["ExtFormBtn"])
								form.find(IPclhrData["ExtFormBtn"]).attr("disabled", "disabled");
						} else if(producttype == 11) {
//это внешняя форма (форсированный клик)
							if (IPclhrData["ExtFormBtnFC"]) {
//может не найти при невалидном html коде, поэтому заменено
//								form.find(IPclhrData["ExtFormBtnFC"]).attr("disabled", "disabled");
								var fe = form[0].elements;
								for(var i = 0; i < fe.length; i++) {
									var that=jQuery(fe[i]);
									if(jQuery(that).is(IPclhrData["ExtFormBtnFC"])) {
										jQuery(that).attr("disabled", "disabled");
									}
								}

//сам "disabled" очень важен по причине, указанной выше (бывают нервные по пять раз делают клик)
//добавлено (для случая, если например пустая форма отпралялась на интепрайс-сайтах (где нет флагов валидации required) - перешли на другую страницу с ошибкой "не заполнены поля", вернулись, затем повторно пытаемся отправить - кнопка заблокирована)
//то есть через 10 секунд все кнопки на странице всегда разблокируются
//если сбивает сторонний disabled, то убрать resendStop
//насколько я понял такой сброс важен в основном для мозиллы
								var setIntervalIPForm = setInterval(function() {
									var fe = form[0].elements;
									var foundd=0;
									for(var i = 0; i < fe.length; i++) {
										var that=jQuery(fe[i]);
										if(jQuery(that).is(IPclhrData["ExtFormBtnFC"])) {
											if (jQuery(that).attr("disabled")) {
												jQuery(that).attr("disabled", false);
												foundd=1;
											}
										}
									}
									if (!foundd)
										clearInterval(setIntervalIPForm);
				
										
								}, 10000);


							}
						} else {
//это форма охотника
//							jQuery(".clhr-form .clhr-btn").attr("disabled", "disabled");
							form.find(".clhr-btn").attr("disabled", "disabled");
						}
					}


					var clhrClr = IPchlIb['clhrClr'];

					var clhrHide = IPchlHid['clhrHide'];

					var clhrMsg = IPchlIb['clhrMsg'];


					var getCookie = IPchlIb['getCookie'];
					var setCookie = IPchlIb['setCookie'];

					var getScriptFolder = IPchlIb['getScriptFolder'];


					var FSubmit = IPchlIb['FSubmit'];



					if(IPclhrData["JSCheckName"]) {
				                if (!form.find('[name="' + IPclhrData["JSCheckName"] + '"]').val()){
				        	        form.append(
			        	        	    jQuery("<input type='hidden'>").attr( {
					                        name: IPclhrData["JSCheckName"], 
					                        value: 1 })
		                			);
						}
					}

alert(11111111111112)

					if (producttype == 11) {
						if (ajaxflg) {
//alert(111111111111122222233333333)
//отправка аяксом - коллхантер форма отправляется только один раз
							if (IPchlEev.clhrsnd) {
alert(1122222234)
								return;
							} else {
alert(1122222234445)
								IPchlEev.clhrsnd=1;
//пропускаем дальше - там блокируется отправка submit при наличии ajaxflg
							}
						} else {
//отправка не аяксом - submit делается каждый раз, коллхантер форма отправляется только один раз
							if (IPchlEev.clhrsnd) {
								if (IPchlEev.alredyformsnd) {
									FSubmit(jQuery,form[0],e);
								} else {					
//мы не отслеживаем точное время когда пришел предыдущий запрос (то есть была отпрака формы каллхантера), поэтому делаем гарантированную задержку
									setTimeout(function() {	IPchlEev.alredyformsnd=1; FSubmit(jQuery,form[0],e); }, 1000); //увеличить если не будет где-то отправляться наше уведомление, здесь всегда 1 сек (миним. задержка отправки данных форм на сервер клиента)
								}
								return;
							} else {
								IPchlEev.clhrsnd=1;
//пропускаем дальше - делаем отправку формы каллхантера и submit
							}
						}

					}


alert(1111111111111)


					clhrMsg(jQuery,form,"sending", IPclhrData["alertSending"], "");
					var cntime = getCookie("callhunter-sent"); // load sent time
//					if((!cntime)||(producttype == 11)) {
//добавлены не только формы с форс-кликом, но и другие формы, для которых можно блокировать/разблокировать повторную отправку через resendStop
//					if((!cntime)||(producttype == 11)||(producttype == 10)) {
//блокируем любую неотправку при форс-клике, в т.ч. блокируем контроль периода отправки
//PRI!!!!
					if((!cntime)||(producttype == 11)||(producttype == 10)||(producttype == 5)) {
//добавлен свободный пропуск (сброс неотправки) для регистрации прямых звонков - менеджер во первых не знает что там у юзера и во-вторых сам определяет регистрировать или нет прямой звонок, в третьих повторная отправка за сессию прямого звонка (точнее показ его формы) блокируется куком
						cntime = 0;
					}

					var cs3 = [];
					var os3 = [];


					var bmattr=IPclhrData["mailAttribute"];
					if (bmattr) {

//PRI!!!!
				                var addAttr = e.data('clhr')||false;
				                if (addAttr){
				                        cs3.push(bmattr);
				                        os3.push(addAttr);
				                } else if (producttype == 5){
				                        cs3.push(bmattr);
				                        os3.push('Ring');
			        	        } else {

//нам важно значения, которые были на момент клика _ipname _ipvalue, а не после каких-то изменений - эти атрибуты появятся только для forceclick (для producttype==11)
							var aname=jQuery(e).attr("_ipname");
							if (!aname) {
								aname=jQuery(e).attr("name");
							}
							var aval=jQuery(e).attr("_ipvalue");
							if (!aval) {
								aval=jQuery(e).val();
							}
							if (!aval) {
								aval=jQuery(e).text();
							}
							if (aval) {
								if (aname) {
						                        cs3.push(bmattr + ' ' + aname);
								} else {
						                        cs3.push(bmattr);
								}
					                        os3.push(aval);
							} else if (aname) {
					                        cs3.push(bmattr);
					                        os3.push(aname);
							} else if (producttype < 10) {
					                        cs3.push(bmattr);
					                        os3.push(IPclhrData["button"]);
							}
						}
					}



					var cs = [];
					var os = [];

					var smsfolder=IPclhrData["MMFolder"];

					if ((producttype >= 10)&&(IPclhrData["ExtFormMMFolder"]))
						smsfolder=IPclhrData["ExtFormMMFolder"];

/*
//это не работает для невалидного html кода (для producttype==11)
//то есть jQuery.find ничего не найдет на невалидном html <table><form>.....</form></table>
//поэтому все заменено
//					form.find('[type=text], textarea, [type=email], [type=tel]').each(function() { // текстовые поля и textarea и добавлены email tel
					form.find('[type=text], textarea, [type=email], [type=tel], [type=url]').each(function() { // текстовые поля и textarea и добавлены email tel url
						if(jQuery(this).val().length) {
							cs.push(jQuery(this).attr("name"));
							os.push(jQuery(this).val());
						}
					});


					form.find('select').each(function() { // селекты

						if(!jQuery(this).hasClass("clhr-ct-start") && !jQuery(this).hasClass("clhr-ct-finish")) { // кроме времени
							cs.push(jQuery(this).attr("name"));
							var v = jQuery(this).val();
							var t = jQuery(this).find(":selected").text();
							if (v != t) {
								os.push(t + "(" + v + ")");
							} else {
								os.push(t);
							}
							if(jQuery(this).hasClass("clhr-selectOffice")) {
								smsfolder=v;
							}
						}
					});


					form.find('[type=checkbox]').each(function() { // чекбоксы

						cs.push(jQuery(this).attr("name"));
						os.push(jQuery(this).is(":checked") ? IPclhrData["CheckboxYes"] : IPclhrData["CheckboxNo"]);
					});



					form.find('[type=radio]').each(function() { // радио
						if(jQuery(this).is(':checked')) {
							cs.push(jQuery(this).attr("name"));
							os.push(jQuery(this).val());
						}
					});


//PRI!!!!
					var RingPhone='';


					form.find('[type=hidden]').each(function() { //скрытые поля добавляем в конце

//PRI!!!!
						var attrName=jQuery(this).attr("name");
						var attrVal=jQuery(this).val();
	 					if ((producttype == 5)&&(attrName == 'RingPhoNeHidD')) {
							RingPhone=attrVal;
						} else {
							if(attrVal.length) {
								cs.push(attrName);
								os.push(attrVal);
							}
						}
					});


*/


//PRI!!!!
					var RingPhone='';



					if (producttype == 11) {
//только для внешних форм (с отправкой внешней формы) структурируем вывод полей форм
						var fsrt = 1;
					} else {
//в остальных случаях - вывод полей в порядке их следования в форме
						var fsrt = 0;
					}



					var csSrt = [];
					var osSrt = [];



					var fe = form[0].elements;


					for(var i = 0; i < fe.length; i++){ 
						var ftype=null;
						var csT = '';
						var osT = '';
						var that=jQuery(fe[i]);
//						if(jQuery(that).is('[type=text], textarea, [type=email], [type=tel]')) { // текстовые поля и textarea и добавлены email tel
						if(jQuery(that).is('[type=text], textarea, [type=email], [type=tel], [type=url]')) { // текстовые поля и textarea и добавлены email tel url
							ftype=0;
							csT = jQuery(that).attr("name");
							osT = jQuery(that).val();
						} else if(jQuery(that).is('select')) { // селекты
							if(!jQuery(that).hasClass("clhr-ct-start") && !jQuery(that).hasClass("clhr-ct-finish")) { // кроме времени
								ftype=1;
								csT = jQuery(that).attr("name");
								var v = jQuery(that).val();
								var t = jQuery(that).find(":selected").text();
								if (v != t) {
									osT = t + "(" + v + ")";
								} else {
									osT = t;
								}
								if(jQuery(that).hasClass("clhr-selectOffice")) {
									smsfolder=v;
								}
							}
						} else if(jQuery(that).is('[type=checkbox]')) { // чекбоксы
							ftype=2;
							csT = jQuery(that).attr("name");
							osT = jQuery(that).is(":checked") ? IPclhrData["CheckboxYes"] : IPclhrData["CheckboxNo"];
						} else if(jQuery(that).is('[type=radio]')) { // радио
							if(jQuery(that).is(':checked')) {
								ftype=3;
								csT = jQuery(that).attr("name");
								osT = jQuery(that).val();
							}
						} else if(jQuery(that).is('[type=hidden]')) { //скрытые поля добавляем в конце при сортировке

//PRI!!!!
							var hval=jQuery(that).val();
							var hname = jQuery(that).attr("name");
		 					if ((producttype == 5)&&(hname == 'RingPhoNeHidD')) {
								RingPhone=hval;
							} else {
								if(hval.length) {
									if(!(IPclhrData["JSCheckName"]&&(hname == IPclhrData["JSCheckName"]))) {
										ftype=4;
										csT = hname;
										osT = hval;
									}
								}
							}

						}

						if (ftype !== null) {
							if (fsrt) {
								if (!csSrt[ftype]) {
									csSrt[ftype]=[];
									osSrt[ftype]=[];
								}
								csSrt[ftype].push(csT);
								osSrt[ftype].push(osT);
							} else {
								cs.push(csT);
								os.push(osT);
							}
						}

						
					}

					if (fsrt) {
						for(var i = 0; i < csSrt.length; i++){
							if (csSrt[i]) {
								cs = cs.concat(csSrt[i]);
								os = os.concat(osSrt[i]);
							}
						}
					}


//PRI!!!!
//перебиваем любой smsfolder если есть PRIMMFolder
					if (PRIMMFolder != null) {
//при нестрогом сравнении будет отлуп для null или undefined
						smsfolder=PRIMMFolder;
					}

					var IPIDS = '';
					if (PRIIDS != null) {
						IPIDS = PRIIDS;
					}


					if (producttype < 10) {
						if(jQuery(".clhr-ct-start").find(":selected").val() > 0) { // время звонка
							cs.push(IPclhrData["txtCallTime"]);
							os.push(IPclhrData["txtFrom"] + " " + jQuery(".clhr-ct-start").find(":selected").text() + " " + IPclhrData["txtTill"] + " " + jQuery(".clhr-ct-finish").find(":selected").text() + " " + IPclhrData["txtHours"]);
						}
					}


					var cs2 = [];
					var os2 = [];


					if (IPclhrData["mailUrl"]) {
						cs2.push(IPclhrData["mailUrl"]); // страница с запросом
						os2.push(location.href);
					}
					var rf = getCookie("chRef"); // источник трафика
					if((rf) && (rf.length > 0)) {
						if (IPclhrData["mailReferrer"]) {
							cs2.push(IPclhrData["mailReferrer"]);
							os2.push(rf);
							var rflp = getCookie("chRefLandPg"); // страница входа для источника трафика
							if((rflp) && (rflp.length > 0)) {
								if (IPclhrData["mailRefLandPage"]) {
									cs2.push(IPclhrData["mailRefLandPage"]);
								} else {
									cs2.push("Referrer Landing Page");
								}
								os2.push(rflp);
							}
						}

					}


					var IPIDSonline = '';

					var rfids = getCookie("chRefIDS");
					if((rfids) && (rfids.length > 0)) {
//записываем ids отдела
						IPIDSonline = rfids;
					}


					if(typeof IPCHFEmail !== "string") {
						IPCHFEmail='';
					}

					if(typeof IPCHrCU !== "string") {
						IPCHrCU='';
					}
					if(typeof IPCHrLS !== "string") {
						IPCHrLS='';
					}
					if(typeof IPCHrDS !== "string") {
						IPCHrDS='';
					}
					if(typeof intepriceIPL !== "string") {
						intepriceIPL='';
					}

//PRI!!!!
					if (intepriceACode.snddr) {
						var snddr = intepriceACode.snddr;
					} else {
						var snddr = intepriceACode.dr;
					}


					var sodshipSession = getCookie('sodshipSession');

					if ((!sodshipSession)&&IPclhrData['sodship']) {
						//проверяем еще раз (хотя parseUrlQuery не сработает, если перешел на след страницу). Было однажды зафиксировано, что не отправлено sodshipSession
						var RefData = {};
						var rflp = getCookie("chRefLandPg"); // проверяем наличие страницы входа sodship для источника трафика
						if((rflp) && (rflp.length > 0)) {
							var parsed_url = IPchlIb.parse_url(rflp);
							if (parsed_url['search']) {
								var pair = (parsed_url['search'].substr(1)).split('&');
								for(var i = 0; i < pair.length; i ++) {
									var param = pair[i].split('=');
									RefData[param[0]] = param[1];
								}
							}
						}

						var URLdata=IPchlIb.parseUrlQuery();
						if (URLdata.hasOwnProperty('sodship')||RefData.hasOwnProperty('sodship')) {
							sodshipSession = IPclhrData['sodship']['SMSName']+'|'+IPclhrData['sodship']['LetterName']; //признак sodship для сессии
						}
					}



					if (!sodshipSession)
						sodshipSession = ''


//@@@@@@@@@@@@@@@@@@

					function sndconv() {
//вынесен сюда вызов findconv, что бы при testing можно было видеть в логах обнаружение ценности конверсии
						var foundconversion=IPchlrFunc.findconv(producttype,form,e);
//						if (!(IPclhrData["SubmitFullBlocked"]||IPclhrData["testing"]||((producttype == 11)&&IPclhrData["testting"]))) {
//для testting регистируем конверсии, но не отправляем клиенту запросы форм через почту и смс и не вносим в статистику его запросов в аккаунте inteprice
						if (!(IPclhrData["SubmitFullBlocked"]||IPclhrData["testing"])) {
							if (foundconversion&&IPclhrData["AdwConvCHSndValue"]) {
								var sndconv=IPclhrData["AdwConvCHSndValue"];
								sndconv["google_conversion_value"]=foundconversion['value'];
								sndconv["google_conversion_currency"]=foundconversion['currency'];
							} else {
								var sndconv=IPclhrData["AdwConvCHSnd"];
							}
							if (sndconv) {
								try {
									SSCHunterAdwordstrack(sndconv);
								} catch (e) { }
							}
							var und=undefined;
							if (!RingPhone)
								RingPhone=und;
							var convval=(foundconversion) ? foundconversion['value'] : und;
							var convcurr=(foundconversion) ? foundconversion['currency'] : und;
							IPcdchcl.funch6(producttype,e,und,cs,os,RingPhone,convval,convcurr,jQuery,jQuery);
						}
					}


//					jQuery.getJSON(getScriptFolder() + "lib/send.php?callback=?", { // отправка данных
//)))))))))))))))))))))))))))))
//без https не будет работать на сайтах с протоколом https (также у сервера должен быть легальный сертификат ssl)
					jQuery.getJSON(('https:' == document.location.protocol ? 'https' : 'http') + "://inteprice.com/callhunter/lib/?callback=?", { // отправка данных
						scriptCharset: "utf-8",
						contentType: "text/html",
						tt: IPclhrData["testting"],
						sh: window.location.hostname,
						IpAid: IpAid,
						pt: producttype,
						ss: 1,
						is: IPIDS,
						io: IPIDSonline,
						fe: IPCHFEmail,
						cu: IPCHrCU,
						ls: IPCHrLS,
						ds: IPCHrDS,
						im: IPchlIb.isMobile,
						ip: IPchlIb.isMobilePh,
						it: IPchlIb['isTouch'],
						il: intepriceIPL,
						wo: IPclhrData["timeZone"],
//						wk: IPchlHid._worktime[0],
						wk: IPchlHid._worktime[3],
						wd: IPchlHid._worktime[1],
						wl: IPchlHid._worktime[2],
//						wf: IPclhrData["workStart"],
//						wt: IPclhrData["workEnd"],
						wf: IPchlHid._worktime[4],
						wt: IPchlHid._worktime[5],
						ti: IPclhrData["tmpid"],
						st: IPclhrData["MMFileType"]?Number(IPclhrData["MMFileType"]):0,
						tf: IPclhrData["testing"],
						ods: sodshipSession,
						sf: smsfolder,
						ci: getScriptFolder(),
						cd: window.intepriceCRTdays,
						ctime: cntime,
						cs: cs,
						os: os,
						cs2: cs2,
						os2: os2,
						cs3: cs3,
						os3: os3,
						cj: window.intepriceCallHunterIni,
//PRI!!!!
						dr: snddr,
						rf: RingPhone
					}, function(i) {
						if(i.message == 'sendResendError') {
							i.message = IPclhrData["sendResendError"];
						} else if(i.message == 'sendOkMessage') {
							i.message = IPclhrData["sendOkMessage"];
						} else if(i.message == 'sendError') {
							i.message = IPclhrData["sendError"];
						}
//!!!!!!!!
						clhrMsg(jQuery,form,i.cls, i.message, i.num);
						if(i.result == "success") {

//PRI!!!!
//не выключаем охотника и не блокируем на 10 мин отправку любых форм callhunter, если это был запрос прямого звонка
//иначе человек позвонил (прямой звонок), что-то узнал у менеджера и не может 10 мин сделать заказ, например, покупки в 1 клик, или запросить теперь обратный звонок
//точнее выключаем охотника, но не блокируем отправку сообщений

							var itime=i.time;

							if(producttype == 5) {
//побольше минус делаем, что бы не перекрылось ручными установками
								itime=itime - (365*24*60*60);
							}

							if((IPclhrData["hunterOn"] != "0") && (IPclhrData["hunterDays"] != "0")) {
								setCookie("callhunter-sent", itime, IPclhrData["hunterDays"]);
							} else {
								setCookie("callhunter-sent", itime, 5);
							}


//							if ((!ajaxflg)&&((producttype == 10)||(producttype == 11))) {
//							if ((!ajaxflg)&&(producttype == 11))
//								IPchlEev.alredyformsnd=1;
//								FSubmit(jQuery,form[0],e);
//							}
							if(IPclhrData["alertQuickclose"] == "0") {
								setTimeout(function() {	clhrHide(jQuery); }, 3000); //это сработает только для popup callhunter
								setTimeout(function() {	clhrClr(jQuery,form); }, 4000);

//								setTimeout(function() {	clhrHide(jQuery); }, 4000);
//								setTimeout(function() {	clhrClr(jQuery); }, 5000);
//не работает в таг менеджере eval в settimeout
//								dl("clhrHide", 4);
//								dl("clhrClr", 5);
							}

//PRI!!!!
//@@@@@@@@@@@@@@@@@@
//здесь не успеет выполнится отправка для внешних форм при submit (форсированный клик)
//однако остальные нужно отсылать только когда реальное сообщение было успешно отправлено, без повторов, поэтому отправляем конверсии здесь
							if (producttype != 11)
								sndconv();


						}
					//}).error(function(jqXHR, textStatus, errorThrown) {
					//fail есть везде, error в старших удалена (эти функции взаимозаменяемы)
					}).fail(function(jqXHR, textStatus, errorThrown) {
						// Inspect the values of jqXHR, textStatus, errorThrown here.
						//alert(jqXHR.responseText);
						//alert(jQuery.parseJSON(jqXHR.responseText));
						clhrMsg(jQuery,form,"c_error", "Error 0 : " + errorThrown, "");
					});


//@@@@@@@@@@@@@@@@@@
//для внешних форм (форсированный клик) надо сразу отправлять, иначе не успеем передать конверсии
					if (producttype == 11)
						sndconv();



//					if ((!ajaxflg)&&((producttype == 10)||(producttype == 11)))
					if ((!ajaxflg)&&(producttype == 11))
						setTimeout(function() {	IPchlEev.alredyformsnd=1; FSubmit(jQuery,form[0],e); }, 1000); //увеличить если не будет где-то отправляться наше уведомление, здесь всегда 1 сек (миним. задержка отправки данных форм на сервер клиента)
					if(IPclhrData["alertQuickclose"] != "0") {
//!!!!!!!!!
						clhrMsg(jQuery,form,"c_success", IPclhrData["alertQuickmessage"], "");

						setTimeout(function() {	clhrHide(jQuery); }, 3000); //это сработает только для popup callhunter
						setTimeout(function() {	clhrClr(jQuery,form); }, 4000);
//						setTimeout(function() {	clhrHide(jQuery); }, 4000);
//						setTimeout(function() {	clhrClr(jQuery); }, 5000);
//не работает в таг менеджере eval в settimeout
//						dl("clhrHide", 4);
//						dl("clhrClr", 5);
					}
	}





	IPchlIb.FSubmit = function(jQuery,form,button) {

alert('FSubmit')
		if (IPchlEev.nosubmit)
			return;

		if (IPclhrData["NativeSubmitBlocked"] || IPclhrData["SubmitFullBlocked"])
			return;
alert('FSubmit2')


//известная проблема - не передается submit кнопка в запросе, который генерируется функцией submit() - добавляем скрытое поле для "нативных" отправок формы
//в случае вызова submit из скрипат браузер не передает данные о кнопках формы (так как неизвестно на какую кнопку нажали)
//http://stackoverflow.com/questions/5721724/jquery-how-to-get-which-button-was-clicked-upon-form-submission
//http://stackoverflow.com/questions/4007942/jquery-serializearray-doesnt-include-the-submit-button-that-was-clicked
//http://johnnycode.com/2010/04/08/jquery-form-serialize-doesnt-post-submit-and-button-values-duh/
//https://groups.google.com/forum/#!topic/jquery-en/j5mZaj4Vyms
//http://www.clicklinks.be/tutorials/javascript/prototype/forms-with-prototype/

		var nativecheck=0;
//при "нативном" субмите (не было превентдефаулт или вызова субмита где-то в скрипте) - добавляем скрытое поле, имитируем передачу нативного субмита
		if ((!IPchlEev.fs)&&(!IPchlEev.DP))
			nativecheck=1;


		var foundname=0;
		if(jQuery(button).attr('name')) {
			var name = jQuery(button).attr('name');
//			var value = jQuery(button).attr('value');
			var value = jQuery(button).val();
		} else {
			var name = false;
		}
		if(nativecheck&&name) {
//на всякий случай проверяем, может кликнули на поле не кнопку, тогда оно возможно будет в массиве serializeArray
			var formData = jQuery(form).serializeArray();
			for (var key in formData) {
				for (var key2 in formData[key]) {
					if (formData[key][key2] == name) {
						foundname=1;
						break;
					}
//					alert(key2 + '=' + formData[key][key2])
				}
			}

			if (!foundname) {
//удаляем имя на всякий случай (вроде были проблемы с одинаковыми именами, тормозить начинал браузер, но может не от этого)
				jQuery(button).removeAttr('name');


/*
				var input = document.createElement('input');
				input.type = 'hidden';
				input.name = name;
				input.value = value;
				form.appendChild(input);
*/

	        	        jQuery(form).append(
	                	    jQuery("<input type='hidden'>").attr( {
		                        name: name, 
		                        value: value })
		                );
			}
		}


//		jQuery(form).trigger('submit');
//только отправка формы без триггера (срабатывания событий)
//		form.submit();

		var sbmt = form._ipsubmits;
		if (sbmt) {
			sbmt.call(form);
		} else {
			form.submit();
		}



		if(nativecheck&&name&&(!foundname)) {
//удаляем добавленное поле
			jQuery(form).find('[type="hidden"][name="' + name + '"]').remove();
			jQuery(button).attr( "name", name );
		}


	}















































	IPchlEev.ExtF = function(form,button) {


//если такие как я умники удалят название кнопки по дороге, нам оно нужно будет потом
		jQuery(button).attr('_ipname',jQuery(button).attr('name'));
		jQuery(button).attr('_ipvalue',jQuery(button).val());



		IPchlEev.clhrsnd=0;
		IPchlEev.alredyformsnd=0;


		IPchlEev.notvalid=0;


//Field Validation for browsers not supporting html5 required attribute
//не все браузеры поддерживают проверку заполнения обязательных полей
//как сделать выдачу родного сообщения браузера - не знаю, поэтому выдаем ошибку в стандартное наше поле
//делаем проверку, если есть параметр alertFillRequired
//валидация срабатывает только после выполнения всех обработчиков onclick 
//		if ((!IPchlEev.DP)&&(!IPchlEev.fs)&&IPclhrData["alertFillRequired"]) { //это вариант - валидация при отсутсвии submit и превентдефаулт в обработчиках onclick onsubmit
//при наличии submit() в onclick Мозилла проверяет валидацию, хром и опера не проверяют, сразу делают отправку формы
//при наличии submit() в onsubmit все проверяют валидацию
//		if ((!IPchlEev.fs)&&IPclhrData["alertFillRequired"]) {  //это вариант - валидация при отсутсвии submit в обработчиках onclick onsubmit
//оставил - "валидацию делать в любом случае" так как такое поведение у большинства проверенных браузеров в большинстве случаев
//кстати вроде такое же поведение у GA в режиме callback

		//флаг, что нужно применять свой алгоритм валидации полей формы
		var checkvalidate=0;

		if (IPclhrData["alertFillRequired"]) {
			if (IPclhrData["ExtFormBtnFCvalidAttr"]&&(!IPclhrData["ExtFormBtnFCnovalidOnly"])) {
//форсируем включение режима без проверки обязательных полей (будем сами проверять их и отправлять параллельно основной проверке на сайте)
				IPchlEev.notvalid=-2;
				checkvalidate=1;
			} else if ((button.attr('formnovalidate') == undefined)&&(form.attr('novalidate') == undefined)) {
				var fe = form[0].elements;
				for(var i = 0; i < fe.length; i++){
					if (fe[i].validity) {
						IPchlEev.notvalid=-1;
//проверка выполнена, новый браузер html5, все поля валидные, можно отсылать submit и форму коллхантера
						if (fe[i].validity.valid !== true) {
//проверка выполнена, новый браузер html5, есть невалидные поля, нельзя отсылать форму коллхантера и submit
//alert('set IPchlEev.notvalid')
							IPchlEev.notvalid=1;
							break;
						}
					}
				}
			} else {
//проверка не нужна, можно отсылать submit и пустую форму коллхантера
				//форма без валидации браузером
				IPchlEev.notvalid=-2;
				if (IPclhrData["ExtFormBtnFCvalidAttr"]) {
					checkvalidate=1;
				}
			}
		} else {
//проверка не нужна, можно отсылать submit и пустую форму коллхантера
			IPchlEev.notvalid=-2;
		}


//если  IPchlEev.notvalid==0; - проверка не проводилась, можно отсылать submit, старый браузер, нужно проверить поля вручную, что бы не отсылать пустую форму



//наконец нашел как сделать валидацию браузерную
//		if (IPchlEev.notvalid == 1)
//			form.valid();


//		if (IPchlEev.notvalid == 0) {
		if (IPchlEev.notvalid < 1) {
alert("t1")

//Для старых браузеров небольшая проверка валидности (точнее заполненности некоторых полей или на предмет всех пустых полей)
//а так же для новых, если ппытаются отправить пустую форму
//это чтобы не отсылать нашу форму коллхантера на пустых требуемых полях или на пустых формах
//здесь же мы учитываем, что при невалидном html поля формы не будут найдены, поэтому совмещаем js и jQuery(а на ie8 кстати работает полный jQuery на невалидном коде html)
//то есть jQuery.find ничего не найдет на невалидном html <table><form>.....</form></table>
			var formempty=1;
			var fe = form[0].elements;
alert(fe.length)
			var radiob={};

			
			//флаг, как проверять обязательные для заполнения поля здесь, через callhunter
			var checkrequired=0;
			//флаг что есть незаполненные обязательные поля
			var notfill=0;
			if (checkvalidate) {
				if (!IPclhrData.hasOwnProperty("ExtFormBtnFCvalidVal")) {
					//тип проверки - просто наличие какого-то непустого значений
					checkrequired=1;
				} else if (typeof IPclhrData["ExtFormBtnFCvalidVal"] == 'function') {
					//тип проверки - для текстовых полей - функция, которая возвращает непустое значение, для checkbox и radio - наличие непустого значения
					checkrequired=2;
				} else {
					//тип проверки - нежесткое совпадение значений (== а не ===)
					checkrequired=3;
				}
			}


			for(var i = 0; i < fe.length; i++){
alert("t3")
				var that=window.jqIPAcode(fe[i]);
				if(window.jqIPAcode(that).is('input:not([type=hidden], [type=submit], [type=button], [type=image], [type=reset]), textarea')) {

					if(window.jqIPAcode(that).is("[type=checkbox], [type=radio]")) {
						if(window.jqIPAcode(that).is(':checked')) {
							formempty=0;
						}
					} else {
//						if(window.jqIPAcode(that).val().length > 0) {
						var value = window.jqIPAcode(that).val();
						var d = new RegExp("^\\s*$");
						if (!d.test(value)) {
							formempty=0;
						}
					}

					if (checkrequired) {
						//флаг, что поле является обязательным для заполнения
						var checkfill = 0;

						if (window.jqIPAcode(that).attr(IPclhrData["ExtFormBtnFCvalidAttr"]) != undefined) {
							if (checkrequired==1) {
								checkfill = 1;
							} else if (checkrequired==2) {
								checkfill = 1;
							} else if (IPclhrData["ExtFormBtnFCvalidVal"] == window.jqIPAcode(that).attr(IPclhrData["ExtFormBtnFCvalidAttr"])) {
								checkfill = 1;
							}
						}


						if (checkfill) {

							if(window.jqIPAcode(that).is("[type=checkbox]")) {
								if(!window.jqIPAcode(that).is(':checked')) {
									notfill=1;
								}
							} else if(window.jqIPAcode(that).is("[type=radio]")) {
								if (!radiob.hasOwnProperty(window.jqIPAcode(that).attr('name'))) {
									radiob[window.jqIPAcode(that).attr('name')]=window.jqIPAcode(that).is(':checked');
								} else if (window.jqIPAcode(that).is(':checked')) {
									radiob[window.jqIPAcode(that).attr('name')]=window.jqIPAcode(that).is(':checked');
								}
							} else {
//								if(window.jqIPAcode(that).val().length < 1) {
								var value = window.jqIPAcode(that).val();

								if (checkrequired==2) {
								//функция должна вернуть непустое значение
									try {
										var d=IPclhrData["ExtFormBtnFCvalidVal"](IPclhrData["ExtFormBtnFCvalidAttr"],window.jqIPAcode(that).attr(IPclhrData["ExtFormBtnFCvalidAttr"]),value,i,window.jqIPAcode(that),button,form);
										if (!d) notfill=1;
									} catch (e) { if (window.console&&window.console.log) console.log('bad function ExtFormBtnFCvalidVal'); }


								} else {
									var d = new RegExp("^\\s*$");
									if (d.test(value)) {
										notfill=1;
									}
								}


							}



						}



					} else if (IPchlEev.notvalid == 0) {
//это только для старых браузеров - небольшая проверка валидности (точнее заполненности некоторых полей)
						if ((window.jqIPAcode(that).attr("required") != undefined)||(window.jqIPAcode(that).attr("pattern") != undefined)) {
							if(window.jqIPAcode(that).is("[type=checkbox]")) {
								if(!window.jqIPAcode(that).is(':checked')) {
//упрощенная проверка выполнена на старом браузере, нельзя отсылать каллхантер форму, но submit пропускаем дальше
alert('set1 IPchlEev.clhrsnd')
									IPchlEev.clhrsnd=1;
//этот флаг тоже ставим, что бы не было задержки отправки submit
									IPchlEev.alredyformsnd=1;
								}
							} else if(window.jqIPAcode(that).is("[type=radio]")) {
								if (!radiob.hasOwnProperty(window.jqIPAcode(that).attr('name'))) {
									radiob[window.jqIPAcode(that).attr('name')]=window.jqIPAcode(that).is(':checked');
								} else if (window.jqIPAcode(that).is(':checked')) {
									radiob[window.jqIPAcode(that).attr('name')]=window.jqIPAcode(that).is(':checked');
								}
							} else {
//								if(window.jqIPAcode(that).val().length < 1) {
								var value = window.jqIPAcode(that).val();
								var d = new RegExp("^\\s*$");
								if (d.test(value)) {
//упрощенная проверка выполнена на старом браузере, нельзя отсылать каллхантер форму, но submit пропускаем дальше
alert('set2 IPchlEev.clhrsnd')
									IPchlEev.clhrsnd=1;
//этот флаг тоже ставим, что бы не было задержки отправки submit
									IPchlEev.alredyformsnd=1;
								}
							}
						}
					}
				}
			}
			for (var key in radiob) {
				if (!radiob[key]) {
//упрощенная проверка выполнена на старом браузере, нельзя отсылать каллхантер форму, но submit пропускаем дальше
alert('set3 IPchlEev.clhrsnd')
					IPchlEev.clhrsnd=1;
//этот флаг тоже ставим, что бы не было задержки отправки submit
					IPchlEev.alredyformsnd=1;
//notfill сюда добавлено просто для напоминания что здесь обрабатываются не только старые браузеры, но и режим внутренней проверки полей ExtFormBtnFCvalidAttr
//					notfill=1;
					break;
				}
			}
			if (formempty||notfill) {
//не отправляем для пустых форм аналогично проверке для старых браузеров
//не отправляем для незаполненных обязательных полей в режиме самостоятельной валидации ExtFormBtnFCvalidAttr
alert('set4 IPchlEev.clhrsnd')
alert(formempty)
alert(notfill)
				IPchlEev.clhrsnd=1;
				IPchlEev.alredyformsnd=1;
			}
		}



		IPchlEev.form0 = form[0];

		IPchlEev.SBMFormFlg=0;

		var fonsubmitcode = form[0].onsubmit;
		var fsubmitcode = form[0].submit;
		var clickcode = button[0].click;
		var onclickcode = button[0].onclick;


		IPchlEev.DP = 0;
		IPchlEev.fs = 0;
		IPchlEev.aj = 0;


//добавлен обработчик команд history.back history.forward history.go window.location
//иначе запускается FSubmit, который не должен запускаться и не происходит переход а происходит submit
		IPchlEev.nosubmit = 0;
		if (!IPchlEev.sethistory) {
			IPchlEev.sethistory=1;
			if (window.history) {
				var hgo = window.history.go;
				if (typeof hgo == "function") {
//					window.history.go = function go(param) {
//заменяем на apply
					window.history.go = function() {
//						alert('history.go(..), native code');
//блокируем отправку submit
						IPchlEev.nosubmit = 1;
//нельзя отсылать каллхантер форму (если успели вовремя этот флаг выставить и вообще форма должна быть отправлена)
alert('set5 IPchlEev.clhrsnd')
						IPchlEev.clhrsnd=1;
//этот флаг тоже сбрасываем, если есть, что бы была задержка отправки submit
						IPchlEev.alredyformsnd=0;
//						return hgo.call(this,param);
						return hgo.apply(this,arguments);
					}
				}
				var hback = window.history.back;
				if (typeof hback == "function") {
					window.history.back = function back() {
//						alert('history.back(), native code');
//блокируем отправку submit
						IPchlEev.nosubmit = 1;
//нельзя отсылать каллхантер форму (если успели вовремя этот флаг выставить и вообще форма должна быть отправлена)
alert('set6 IPchlEev.clhrsnd')
						IPchlEev.clhrsnd=1;
//этот флаг тоже сбрасываем, если есть, что бы была задержка отправки submit
						IPchlEev.alredyformsnd=0;
						return hback.call(this);
					}
				}
				var hforward = window.history.forward;
				if (typeof hforward == "function") {
					window.history.forward = function forward() {
//						alert('history.forward(), native code');
//блокируем отправку submit
						IPchlEev.nosubmit = 1;
//нельзя отсылать каллхантер форму (если успели вовремя этот флаг выставить и вообще форма должна быть отправлена)
alert('set7 IPchlEev.clhrsnd')
						IPchlEev.clhrsnd=1;
//этот флаг тоже сбрасываем, если есть, что бы была задержка отправки submit
						IPchlEev.alredyformsnd=0;
						return hforward.call(this);
					}
				}
			}

//при смене window.location к сожалению никак нельзя это детектировать!
//перезаписать функции типа window.location.assign невозможно
//поэтому наш скрипт не будет работать при смене window.location!
//есть только window.location.watch для мозиллы (в других браузерах не сработает)
//ниже описано как сделать, но только с постоянным контролем в цикле изменения, иначе кроссброузерно никак
//http://stackoverflow.com/questions/1369241/jquery-tell-when-the-hash-changes

		}




		IPchlEev.ajtype=0;

		if (!IPchlEev.setaj) {
			IPchlEev.setaj=1;
			if(window.jQuery) {
//для обнаружения запросов ajax - jQuery используем тот который подключн на сайт
//Метод .ajaxSend() не сработает если при вызове $.ajax() или $.ajaxSetup() в настройке global стояло значение false.
				window.jQuery(document).ajaxSend(IPchlEev.ajh);
			}
			window.jqIPAcode(document).ajaxSend(IPchlEev.ajh);
		}





	if (!IPclhrData["SubmitFullBlocked"]) {
//просто не выполняем все обработчики, если полная блокировка submit, только выполняем проверку валидности заполнения форм


		if (!IPchlEev.ar) {
//неправильное поведение браузера если IPchlEev.ar=1 (то есть обработчики кликов выполняются раньше чем onclick)
//если еще не выполнялись обработчики кликов (именно так должно быть), то выполняем их
//если же уже выполнились обработчики кликов, то могли пройти там запросы submit() но мы уже с этим ничего не сделаем, точнее мы сюда уже не дойдем даже (если был submit())



//часть браузеров (опера хром, мозилла нет) будет выполнять обработчики submit по dispatchEvent(evtclick)
//поэтому подготавливаем прерывание, что бы этого не произошло
			form[0].submit = function submit() {
//				alert('submit() in event click, native code')
				if (IPchlEev.SBMFormFlg==1) {
//еще одна отложенная проблема - может возникать здесь submit после прохождения точки где устанавливается IPchlEev.SBMFormFlg=1
//то есть решение уже принято (не будет субмит) а оказывается что будет
//типовой вариант возникновения этой проблемы - подключена GA и есть поля required, сначала не заполняем поля и кликаем, выдаст ошибку заполнения, потом заполняем и кликаем
//submit возникнет на этапе перехвата 'submit() in event submit, native code', но добавлено так же для 'submit() in event click, native code'
					var ipsubmitspar = this._ipsubmitspar;
//PRI!!!!
					IPchlEev.SndF(null,null,window.jqIPAcode,ipsubmitspar.form,ipsubmitspar.button,11);
//					IPchlEev.SndF(null,window.jqIPAcode,ipsubmitspar.form,ipsubmitspar.button,11);
//					IPchlEev.SndF(window.jqIPAcode,ipsubmitspar.form,ipsubmitspar.button,11);
				} else {
					IPchlEev.fs = 1;
				}
				if (evtsubmitonclick.preventDefault) {
					IPchlEev.DPn = 1;
					evtsubmitonclick.preventDefault();
				} else {
					evtsubmitonclick.returnValue = false;
				}
				return false;
			}



//этого вообще не должно быть (выполнение onsubmit и других обработчиков submit при генерации событий кликов по dispatchEvent(evtclick)), мочим
			form[0].onsubmit = function onsubmit(event) {
//				alert('onsubmit in event click, native code')
				event = event || window.event //для ie8
				if (event.preventDefault) {
					IPchlEev.DPn = 1;
					event.preventDefault();
				} else {
					event.returnValue = false;
				}
				if (event.stopImmediatePropagation) {
					event.stopImmediatePropagation();
				}
				return false;
			}


//подготовили, но не запустили прерывание submit (часть браузеров его сами запустят при dispatchEvent(evtclick))
			var evtsubmitonclick = prepareTriggerEvent('submit','click');


/*
//это не кросс-броузерное решение
			var evtsubmitonclick = document.createEvent('Event');
			evtsubmitonclick.initEvent('submit', true, true);
        
//патчим preventDefault, что бы можно было определять, был вызов preventDefault или нет
			if (evtsubmitonclick.preventDefault) {
				var preventDefIP = evtsubmitonclick.preventDefault;
				evtsubmitonclick.preventDefault = function preventDefault() {
					if (IPchlEev.DPn) {
						IPchlEev.DPn = 0;
					} else {
//						alert('preventDefault submit in event click, native code')
						IPchlEev.DP = 1;
					}
					preventDefIP.call(this);
				}
			}
*/



			button[0].click = function click() {
//				alert('click() in event, native code')
				if (evtclick.preventDefault) {
					IPchlEev.DPn = 1;
					evtclick.preventDefault();
				} else {
					evtclick.returnValue = false;
				}
				return false;
			}

			if (typeof onclickcode == "function") {
				var onclickIP = onclickcode;
				button[0].onclick = function onclick(event) {
//					alert('onclick in event click, native code')
					event = event || window.event //для ie8
					var ret=onclickIP.call(this,event);
					if(event.preventDefault){
						if(ret === false){IPchlEev.DP = 1;}
					} else {
						if((ret === false)||(event.returnValue == false)){IPchlEev.DP = 1;}
					}
					return false;
				}

			} else {
				button[0].onclick = function onclick(event) {
//					alert('onclick in event click, native code')
					event = event || window.event //для ie8
					if(!event.preventDefault)
						if(event.returnValue == false){IPchlEev.DP = 1;}
					return false;
				}
			}


			var evtclick = prepareTriggerEvent('click','click');
			triggerEvent(button[0],evtclick);

/*        
//это не кросс-броузерное решение
			var evtclick = document.createEvent('Event');
			evtclick.initEvent('click', true, true);

//патчим preventDefault, что бы можно было определять, был вызов preventDefault или нет
			if (evtclick.preventDefault) {
				var preventDefIP2 = evtclick.preventDefault;
				evtclick.preventDefault = function preventDefault() {
					if (IPchlEev.DPn) {
						IPchlEev.DPn = 0;
					} else {
//						alert('preventDefault click in event click, native code')
						IPchlEev.DP = 1;
					}
					preventDefIP2.call(this);
				}
			}


			button[0].dispatchEvent(evtclick);
*/


                }


		if((IPchlEev.notvalid < 1)&&(IPchlEev.fs || (!IPchlEev.DP))) {

//если в обработчиках кликов был submit, то браузеры поступают следующим образом
//обязательно выполняют поcле этого обработчики submit и даже если в них был preventDefault, то все равно делают submit (так как он раньше в кликах должен был быть выполнен)


			form[0].submit = function submit() {
//				alert('submit() in event submit, native code')

				if (IPchlEev.SBMFormFlg==1) {
//еще одна отложенная проблема - может возникать здесь submit после прохождения точки где устанавливается IPchlEev.SBMFormFlg=1
//то есть решение уже принято (не будет субмит) а оказывается что будет
//типовой вариант возникновения этой проблемы - подключена GA и есть поля required, сначала не заполняем поля и кликаем, выдаст ошибку заполнения, потом заполняем и кликаем
//submit возникнет на этапе перехвата 'submit() in event submit, native code', но добавлено так же для 'submit() in event click, native code'
					var ipsubmitspar = this._ipsubmitspar;
//PRI!!!!
					IPchlEev.SndF(null,null,window.jqIPAcode,ipsubmitspar.form,ipsubmitspar.button,11);
//					IPchlEev.SndF(null,window.jqIPAcode,ipsubmitspar.form,ipsubmitspar.button,11);
//					IPchlEev.SndF(window.jqIPAcode,ipsubmitspar.form,ipsubmitspar.button,11);
				} else {
					IPchlEev.fs = 1;
				}
				if (evtsubmit.preventDefault) {
				IPchlEev.DPn = 1;
					evtsubmit.preventDefault();
				} else {
					evtsubmit.returnValue = false;
				}
				return false;
			}


		
			if (typeof fonsubmitcode == "function") {
				var onsubmitIP = fonsubmitcode;
				form[0].onsubmit = function onsubmit(event) {
//					alert('onsubmit in event submit, native code')
					event = event || window.event //для ie8
					var ret=onsubmitIP.call(this,event);
					if(event.preventDefault){
						if(ret === false){IPchlEev.DP = 1;}
					} else {
						if((ret === false)||(event.returnValue == false)){IPchlEev.DP = 1;}
					}
					if (event.preventDefault) {
						IPchlEev.DPn = 1;
						event.preventDefault();
					} else {        	
						event.returnValue = false;
					}
					return false;
				}

			} else {
				form[0].onsubmit = function onsubmit(event) {
//					alert('onsubmit in event submit, native code')
					event = event || window.event //для ie8
					if(!event.preventDefault)
						if(event.returnValue == false){IPchlEev.DP = 1;}
					return false;
				}
			}


			var evtsubmit = prepareTriggerEvent('submit','submit');
			triggerEvent(form[0],evtsubmit);

/*
//это не кросс-броузерное решение
			var evtsubmit = document.createEvent('Event');
			evtsubmit.initEvent('submit', true, true);

//патчим preventDefault, что бы можно было определять, был вызов preventDefault или нет
			if (evtsubmit.preventDefault) {
				var preventDefIP3 = evtsubmit.preventDefault;
				evtsubmit.preventDefault = function preventDefault() {
					if (IPchlEev.DPn) {
						IPchlEev.DPn = 0;
					} else {
//						alert('preventDefault submit in event submit, native code')
						IPchlEev.DP = 1;
					}
					preventDefIP3.call(this);
				}
			}


//не сработает dispatchEvent (сразу выполнится действие по умолчанию) только если где-то ошибка в коде обработчиков
			form[0].dispatchEvent(evtsubmit);
*/


		}

	}


//все замечательно, но есть одна проблема - submit() может выполнится с задержкой (при этом флаг preventDefault = 1)
//такое поведение возможно например в ga да и вообще где угодно (ajax - тоже могут написать с задержкой выполнения)
//в результате мы получаем неконтролируемый субмит или запрос ajax в любом месте (без отправки нашей формы)
//исправляем проблему здесь

		form[0]._ipsubmitspar={form:form, button:button};
		if (!form[0]._ipsubmits) {
			form[0]._ipsubmits = fsubmitcode;
//эту функцию можно добавлять один раз
//короче несколько дней думал и пришел к самому простому варианту, без лишних заморочек один раз перехватываем и все
//для первого клика полностью хватает, а если будут повторные клики, то если в submit находится не наша функия, то скорее всего ее загнали дальше в стек вызова (по типу как здесь сделано)

//можно еще сделать через prototype - form[0].constructor.prototype (тогда она точно попадет вглубь при подмене)
//решение пишут не кроссбраузерное но у меня в трех ьраузерах работало, поэтому можно совместить с прямой заменой submit на случай когда constructor.prototype не существует
//http://stackoverflow.com/questions/1999891/how-to-reliably-submit-an-html-form-with-javascript
//http://briancrescimanno.com/form-submit-method-doesnt-fire-submit-event-workaround/
//HTMLElement.prototype.click = function click() {
//	alert('firstclick()')
//}
//HTMLFormElement.prototype.submit = function submit() {
//	alert('firstsubmit()')
//}
//form[0].constructor.prototype.submit = function submit() {
//	alert('firstsubmit form0()')
//}

			form[0].submit = IPchlEev.submitfun;
		}



//изменить поведение ajaxSend обработчика на случай срабатывания с задержкой
		IPchlEev.ajtype=1;




		form[0].onsubmit = fonsubmitcode;

		button[0].click = clickcode;
		button[0].onclick = onclickcode;




//если есть вызов submit() где-либо в обработчиках - нужно его обязательно выполнить, несмотря ни на что (даже при наличии preventDefault)
//(то есть флаг IPchlEev.fs=1 перебивает флаг IPchlEev.DP = 1)
//исключение - только если не пройдена валидация (см выше)
//дальше нужно анализировать оба флага
						

/*
						
		if (IPchlEev.fs) {
			alert('submit() in event submit or click')
		} else {
			alert('no submit() in event submit or click')
		}
		if (IPchlEev.DP) {
			alert('DefaultPrevent')
		} else {
			alert('no DefaultPrevent')
		}
		if (IPchlEev.aj) {
			alert('ajaxSend')
		} else {
			alert('no ajaxSend')
		}
*/


		return IPchlEev.notvalid;
	}
