
var intepriceIPL = 'viktoriabondarchuk.inteprice.com';
var intepriceCRT = '20.05.2015';

var intepriceApermissions = {
	"ApprovedDomains":	['viktoriabondarchuk.od.ua','viktoriabondarchuk.inteprice.com'],
	"GATargets":		false,
	"YMTargets":		false,
	"RingTarget":		false,
	"RingWindow":		false,
	"IPSHOPTargets":	false,
	"CallHunter":		true
};


if (typeof window.IPCHFEmail === "undefined") {
	window.IPCHFEmail='vikulya-od@mail.ru';
}

if (typeof window.intepriceCallHunterFolder === "undefined") {
	window.intepriceCallHunterFolder = '/files/callhunter/';
}

if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}

if (typeof window.intepriceCallHunterjQueryWait === "undefined") {
//	window.intepriceCallHunterjQueryWait=5;
}

if (typeof window.intepriceCallHunterOnlyEnabledPages === "undefined") {
//	window.intepriceCallHunterOnlyEnabledPages=true;
}


var intepriceAconfig = [{}];


if (typeof trackRingHitIA == 'function') { trackRingHitIA(); }
if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }
