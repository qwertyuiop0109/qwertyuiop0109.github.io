
var IpAid='opt.odejonka.com';


var intepriceApermissions = {
	"ApprovedDomains":	['opt.odejonka.com'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }