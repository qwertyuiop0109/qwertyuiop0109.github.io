
var IpAid='gardinoff.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['gardinoff.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }