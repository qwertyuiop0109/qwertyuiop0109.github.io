
var IpAid='ortoland.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['ortoland.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }