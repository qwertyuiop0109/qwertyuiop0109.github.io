
var IpAid='mydom.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['mydom.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }