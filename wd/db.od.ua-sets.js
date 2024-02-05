
var IpAid='db.od.ua';


var intepriceIPL = 'bars.inteprice.com';
var intepriceCRT = '03.06.2015';


var intepriceApermissions = {
	"ApprovedDomains":	['db.od.ua','bars.od.ua','bars.inteprice.com'],
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

