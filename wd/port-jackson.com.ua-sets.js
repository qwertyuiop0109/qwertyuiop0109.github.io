
var IpAid='port-jackson.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['port-jackson.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }