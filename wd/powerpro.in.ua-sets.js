
var IpAid='powerpro.in.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['powerpro.in.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }