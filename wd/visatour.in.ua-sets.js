
var IpAid='visatour.in.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['visatour.in.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }