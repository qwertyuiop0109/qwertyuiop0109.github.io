
var IpAid='omnitrade.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['omnitrade.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }