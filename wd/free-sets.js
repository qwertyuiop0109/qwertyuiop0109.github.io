//этот файл уже не нужен, не используется

var intepriceApermissions = {
	"ApprovedDomains":	[],
	"GATargets":		false,
	"YMTargets":		false,
	"RingTarget":		false,
	"RingWindow":		false,
	"IPSHOPTargets":	false,
	"CallHunter":		true
};

if (typeof window.intepriceCallHunterFolder === "undefined") {
//	window.intepriceCallHunterFolder = '/files/callhunter/';
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

//function checkloadIPactive() {
	//window.intepriceAnalyticsActive = false;
	//window.intepriceCallHunterActive = false;
	//setTimeout(function() { checkloadIPactive(); }, 2000);
//}
//checkloadIPactive();
