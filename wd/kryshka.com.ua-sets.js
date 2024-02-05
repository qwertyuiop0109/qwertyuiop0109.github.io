
var IpAid='kryshka.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['kryshka.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }