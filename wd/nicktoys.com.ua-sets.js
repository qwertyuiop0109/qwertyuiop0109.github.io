
var IpAid='nicktoys.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['nicktoys.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }