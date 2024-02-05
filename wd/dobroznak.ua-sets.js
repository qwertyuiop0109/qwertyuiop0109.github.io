
var IpAid='dobroznak.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['dobroznak.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }