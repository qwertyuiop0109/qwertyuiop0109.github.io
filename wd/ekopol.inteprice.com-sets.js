
var intepriceIPL = 'ekopol.inteprice.com';
var intepriceCRT = '20.05.2015';

var intepriceApermissions = {
	"ApprovedDomains":	['ekopol.od.ua','ekopol.inteprice.com'],
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

if (typeof window.intepriceCallHunterOnlyEnabledPages === "undefined") {
//	window.intepriceCallHunterOnlyEnabledPages=true;
}

if (typeof window.IPCHFEmail === "undefined") {
	window.IPCHFEmail='7983170@ukr.net';
}


var intepriceAconfig = [{}];


if (typeof trackRingHitIA == 'function') { trackRingHitIA(); }
if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }

