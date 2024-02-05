
var IpAid='i-hobby.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['i-hobby.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }