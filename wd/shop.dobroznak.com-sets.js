
var IpAid='shop.dobroznak.com';


var intepriceApermissions = {
	"ApprovedDomains":	['shop.dobroznak.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }