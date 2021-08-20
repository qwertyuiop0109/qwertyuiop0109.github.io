
var IpAid='a-ces.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['a-ces.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }