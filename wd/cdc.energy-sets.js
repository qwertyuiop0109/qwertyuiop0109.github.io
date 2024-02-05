
var IpAid='cdc.energy';


var intepriceApermissions = {
	"ApprovedDomains":	['cdc.energy'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }