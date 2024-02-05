
var IpAid='123.systems';


var intepriceApermissions = {
	"ApprovedDomains":	['123.systems'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }