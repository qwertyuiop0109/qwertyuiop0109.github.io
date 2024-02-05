
var IpAid='kiaparts.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['kiaparts.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }