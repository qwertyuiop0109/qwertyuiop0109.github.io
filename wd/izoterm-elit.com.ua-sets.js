

var intepriceIPL = 'izoterm-elit.inteprice.com';
var intepriceCRT = '16.07.2015';


var intepriceApermissions = {
	"ApprovedDomains":	['izoterm-elit.com.ua'],
	"GATargets":		false,
	"YMTargets":		false,
	"RingTarget":		false,
	"RingWindow":		false,
	"IPSHOPTargets":	false,
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterFolder === "undefined") {
	window.intepriceCallHunterFolder = 'http://izoterm-elit.com.ua/callhunter/';
}


if (typeof window.IPCHFEmail === "undefined") {
	window.IPCHFEmail='izoterm-elit@mail.ru';
}


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


var intepriceAconfig = [{}];


if (typeof trackRingHitIA == 'function') { trackRingHitIA(); }
if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }
