
var IpAid='liza-shop.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['liza-shop.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }