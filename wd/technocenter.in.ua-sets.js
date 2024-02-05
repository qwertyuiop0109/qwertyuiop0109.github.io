
var IpAid='technocenter.in.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['technocenter.in.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }