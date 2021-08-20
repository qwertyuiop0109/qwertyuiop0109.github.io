
var IpAid='k-systems.km.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['k-systems.km.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }