
var IpAid='lcc.biz.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['lcc.biz.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }