
var IpAid='ledkontur.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['ledkontur.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }