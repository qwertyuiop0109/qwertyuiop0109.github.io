
var IpAid='loft-glass.com';


var intepriceApermissions = {
	"ApprovedDomains":	['loft-glass.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }