
var IpAid='gerchikco.com';


var intepriceApermissions = {
	"ApprovedDomains":	['gerchikco.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }