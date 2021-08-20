
var IpAid='virnyidruk.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['virnyidruk.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }