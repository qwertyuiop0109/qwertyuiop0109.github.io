
var IpAid='uvencom.com';


var intepriceApermissions = {
	"ApprovedDomains":	['uvencom.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }