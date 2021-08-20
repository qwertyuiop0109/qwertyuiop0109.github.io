
var IpAid='papyrus.kiev.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['papyrus.kiev.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }