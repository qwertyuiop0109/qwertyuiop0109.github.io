
var IpAid='look-and-buy.com';


var intepriceApermissions = {
	"ApprovedDomains":	['look-and-buy.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }