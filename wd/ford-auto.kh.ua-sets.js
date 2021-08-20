
var IpAid='ford-auto.kh.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['ford-auto.kh.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }