
var IpAid='bipauto.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['bipauto.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }