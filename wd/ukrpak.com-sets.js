
var IpAid='ukrpak.com';


var intepriceApermissions = {
	"ApprovedDomains":	['ukrpak.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }