
var IpAid='kia-auto.kh.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['kia-auto.kh.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }