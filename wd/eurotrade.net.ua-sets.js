
var IpAid='eurotrade.net.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['eurotrade.net.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }