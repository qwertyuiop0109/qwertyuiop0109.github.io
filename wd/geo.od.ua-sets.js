
var IpAid='geo.od.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['geo.od.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }