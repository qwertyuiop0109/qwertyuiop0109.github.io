
var IpAid='art-potolok.od.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['art-potolok.od.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }