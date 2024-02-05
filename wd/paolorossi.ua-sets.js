
var IpAid='paolorossi.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['paolorossi.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }