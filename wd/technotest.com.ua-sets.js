
var IpAid='technotest.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['technotest.com.ua'],
	"GATargets":		false,
	"YMTargets":		false,
	"RingTarget":		false,
	"RingWindow":		false,
	"IPSHOPTargets":	false,
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


var intepriceAconfig = [{}];


if (typeof trackRingHitIA == 'function') { trackRingHitIA(); }
if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }