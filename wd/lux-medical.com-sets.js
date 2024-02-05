
var IpAid='lux-medical.com';


var intepriceApermissions = {
	"ApprovedDomains":	['lux-medical.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }