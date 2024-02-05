
var IpAid='agrovita.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['agrovita.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }