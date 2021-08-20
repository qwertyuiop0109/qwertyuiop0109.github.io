
var IpAid='schaefer.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['schaefer.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }