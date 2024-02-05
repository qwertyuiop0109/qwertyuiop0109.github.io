
var IpAid='vinkod.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['vinkod.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }