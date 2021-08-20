
var IpAid='foodlogistic.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['foodlogistic.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }