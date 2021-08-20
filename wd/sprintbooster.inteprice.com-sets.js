
var intepriceIPL = 'sprintbooster.inteprice.com';
var intepriceCRT = '03.07.2015';

var IPCHFEmail = 'sale@atlastuning.com';

var intepriceApermissions = {
	"ApprovedDomains":	['sprintbooster.com.ua','sprintbooster.inteprice.com'],
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

var intepriceAconfig = [{}];

if (typeof trackRingHitIA == 'function') { trackRingHitIA(); }
if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }

