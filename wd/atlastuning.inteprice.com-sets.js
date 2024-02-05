
//var intepriceIPL = 'atlastuning.inteprice.com';
var intepriceIPL = 'sprintbooster.inteprice.com';
var intepriceCRT = '03.07.2015';


//история изменений
//6.10 (my), 7.10 (atlas) - установка новых скриптов сбора аналитики
//19.10 - замена ga на ga_IpA (убраны лишние события от Callbackhunter)
//20.10 - MObileRing замена 1-го касания на 3-пальцевое

var IPCHFEmail = 'sale@atlastuning.com';

var intepriceApermissions = {
	"ApprovedDomains":	['atlastuning.com','atlastuning.inteprice.com'],
	"GATargets":		true,
	"YMTargets":		true,
	"RingTarget":		true,
	"RingWindow":		false,
	"IPSHOPTargets":	false,
	"CallHunter":		true
};

if (typeof window.intepriceCallHunterFolder === "undefined") {
	window.intepriceCallHunterFolder = '/files/callhunter/';
}

if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof window.intepriceCallHunterOnlyEnabledPages === "undefined") {
//	window.intepriceCallHunterOnlyEnabledPages=true;
}



var intepriceAconfig = [
	{
		"TargetName":		"BOX-ATLAS-REQUEST-TARGET",
		"TargetTagClass":	"BOX-ATLAS-REQUEST-TARGET",
		"TargetEvent":		"LeftFormSubmit",
		"TargetType":		"EndPage",
		"TargetTags":		['form'],
		"TargetTagName":	"",
		"TargetTagId":		"",
		"TargetTagHref":	"",
		"TargetTagAction":	"",
		"TargetDomains":	[],
		"AddClass":		"",
		"ClickEmulation":	1,
		"HiddenSubmit":		1,
		"Opacity":		1,
		"OpacityMRing":		0.2,
		"KeyCodeDRing":		27,
		"TimeoutMRing":		2000,
		"TimeoutDRing":		0,
		"RingCookieDays":	7,
		"RingIfHitNames":	[],
		"yaParamsTrue":		0,
		"Test":			0,
		"TargetNote":		"Запрос модуля",
		"TargetPage":		"http://www.atlastuning.com/box-atlas-request.html"
        },
	{
		"TargetName":		"CONTACTS_TARGET",
		"TargetTagClass":	"CONTACTS_TARGET",
		"TargetEvent":		"LeftFormSubmit",
		"TargetType":		"EndPage",
		"TargetTags":		['form'],
		"TargetTagName":	"",
		"TargetTagId":		"",
		"TargetTagHref":	"",
		"TargetTagAction":	"",
		"TargetDomains":	[],
		"AddClass":		"",
		"ClickEmulation":	1,
		"HiddenSubmit":		1,
		"Opacity":		1,
		"OpacityMRing":		0.2,
		"KeyCodeDRing":		27,
		"TimeoutMRing":		2000,
		"TimeoutDRing":		0,
		"RingCookieDays":	7,
		"RingIfHitNames":	[],
		"yaParamsTrue":		0,
		"Test":			0,
		"TargetNote":		"Запрос со страницы контактов",
		"TargetPage":		"http://www.atlastuning.com/contacts.html"
        },
	{
		"TargetName":		"CONTACTS-SKYPE-TARGET",
		"TargetTagClass":	"CONTACTS-SKYPE-TARGET",
		"TargetEvent":		"LeftClick",
		"TargetType":		"Event",
		"TargetTags":		[],
		"TargetTagName":	"",
		"TargetTagId":		"",
		"TargetTagHref":	"",
		"TargetTagAction":	"",
		"TargetDomains":	[],
		"AddClass":		"",
		"ClickEmulation":	1,
		"HiddenSubmit":		1,
		"Opacity":		1,
		"OpacityMRing":		0.2,
		"KeyCodeDRing":		27,
		"TimeoutMRing":		2000,
		"TimeoutDRing":		0,
		"RingCookieDays":	7,
		"RingIfHitNames":	[],
		"yaParamsTrue":		0,
		"Test":			0,
		"TargetNote":		"Контакт чере skype",
		"TargetPage":		"http://www.atlastuning.com/contacts.html"
        },
	{
		"TargetName":		"CONTACTS-EMAIL-TARGET",
		"TargetTagClass":	"CONTACTS-EMAIL-TARGET",
		"TargetEvent":		"LeftClick",
		"TargetType":		"Event",
		"TargetTags":		[],
		"TargetTagName":	"",
		"TargetTagId":		"",
		"TargetTagHref":	"",
		"TargetTagAction":	"",
		"TargetDomains":	[],
		"AddClass":		"",
		"ClickEmulation":	1,
		"HiddenSubmit":		1,
		"Opacity":		1,
		"OpacityMRing":		0.2,
		"KeyCodeDRing":		27,
		"TimeoutMRing":		2000,
		"TimeoutDRing":		0,
		"RingCookieDays":	7,
		"RingIfHitNames":	[],
		"yaParamsTrue":		0,
		"Test":			0,
		"TargetNote":		"Прямой контакт на email",
		"TargetPage":		"http://www.atlastuning.com/contacts.html"
        },
		{
		"TargetName":		"MOBILE_RING_TARGET",
		"TargetTagClass":	"",
		"TargetEvent":		"MobileRing",
		"TargetType":		"Event",
		"TargetTags":		[],
		"TargetTagName":	"",
		"TargetTagId":		"",
		"TargetTagHref":	"",
		"TargetTagAction":	"",
		"TargetDomains":	[],
		"AddClass":		"",
		"ClickEmulation":	1,
		"HiddenSubmit":		1,
		"Opacity":		1,
		"OpacityMRing":		0.2,
		"KeyCodeDRing":		27,
		"TimeoutMRing":		2000,
		"TimeoutDRing":		0,
		"RingCookieDays":	7,
		"RingIfHitNames":	[],
		"yaParamsTrue":		0,
		"Test":			0,
		"TargetNote":		"Звонок, планшет/мобильный",
		"TargetPage":		"все страницы"
        },
	{
		"TargetName":		"RING_TARGET",
		"TargetTagClass":	"",
		"TargetEvent":		"DesktopRing",
		"TargetType":		"Event",
		"TargetTags":		[],
		"TargetTagName":	"",
		"TargetTagId":		"",
		"TargetTagHref":	"",
		"TargetTagAction":	"",
		"TargetDomains":	[],
		"AddClass":		"",
		"ClickEmulation":	1,
		"HiddenSubmit":		1,
		"Opacity":		1,
		"OpacityMRing":		0.2,
		"KeyCodeDRing":		27,
		"TimeoutMRing":		2000,
		"TimeoutDRing":		0,
		"RingCookieDays":	7,
		"RingIfHitNames":	[],
		"yaParamsTrue":		0,
		"Test":			0,
		"TargetNote":		"Звонок, настольный компьютер",
		"TargetPage":		"все страницы"
        },
	{
		"TargetName":		"SEND_EMAIL_TARGET",
		"TargetTagClass":	"",
		"TargetEvent":		"Hit",
		"TargetType":		"Event",
		"TargetTags":		[],
		"TargetTagName":	"",
		"TargetTagId":		"",
		"TargetTagHref":	"",
		"TargetTagAction":	"",
		"TargetDomains":	[],
		"AddClass":		"",
		"ClickEmulation":	1,
		"HiddenSubmit":		1,
		"Opacity":		1,
		"OpacityMRing":		0.2,
		"KeyCodeDRing":		27,
		"TimeoutMRing":		2000,
		"TimeoutDRing":		0,
		"RingCookieDays":	7,
		"RingIfHitNames":	[],
		"yaParamsTrue":		0,
		"Test":			0,
		"TargetNote":		"Письмо отправлено",
		"TargetPage":		"со всех страниц успешная отправка формы"
        }
];


if (typeof trackRingHitIA == 'function') { trackRingHitIA(); }
if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }
