
var IpAid='akonit.net';


var intepriceApermissions = {
	"ApprovedDomains":	['akonit.net'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }