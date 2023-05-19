
var IpAid='master-tools.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['master-tools.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }