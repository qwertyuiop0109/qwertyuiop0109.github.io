
var IpAid='cleverbook.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['cleverbook.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }