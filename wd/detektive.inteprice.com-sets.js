
var intepriceApermissions = {
	"ApprovedDomains":	['private-detective.od.ua','detektive.odessa.ua','detektive.inteprice.com'],
	"GATargets":		false,
	"YMTargets":		false,
	"RingTarget":		false,
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

if (typeof window.IPCHFEmail === "undefined") {
	window.IPCHFEmail='detektive_26@mail.ru';
}


var intepriceAconfig = [{}];


if (typeof trackRingHitIA == 'function') { trackRingHitIA(); }
if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }
