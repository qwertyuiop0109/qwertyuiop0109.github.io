
var IpAid='haval.kh.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['haval.kh.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }