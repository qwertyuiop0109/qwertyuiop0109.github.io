
var IpAid='chystoshka.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['chystoshka.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }