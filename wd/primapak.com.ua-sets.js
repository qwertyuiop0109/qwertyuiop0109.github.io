
var IpAid='primapak.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['primapak.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }