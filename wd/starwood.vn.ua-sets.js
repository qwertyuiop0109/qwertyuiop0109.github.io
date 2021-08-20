
var IpAid='starwood.vn.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['starwood.vn.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }