
var IpAid='mh2.domira.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['mh2.domira.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }