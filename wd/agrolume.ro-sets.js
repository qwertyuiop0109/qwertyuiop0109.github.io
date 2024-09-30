
var IpAid='agrolume.ro';


var intepriceApermissions = {
	"ApprovedDomains":	['agrolume.ro'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }