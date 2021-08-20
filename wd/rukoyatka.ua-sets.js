
var IpAid='rukoyatka.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['rukoyatka.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }