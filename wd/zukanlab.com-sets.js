
var IpAid='zukanlab.com';


var intepriceApermissions = {
	"ApprovedDomains":	['zukanlab.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }