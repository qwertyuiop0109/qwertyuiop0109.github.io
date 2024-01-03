
var IpAid='mebelrim.com';


var intepriceApermissions = {
	"ApprovedDomains":	['mebelrim.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }