
var IpAid='frunze.hyundai.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['frunze.hyundai.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }