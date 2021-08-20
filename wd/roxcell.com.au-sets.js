
var IpAid='roxcell.com.au';


var intepriceApermissions = {
	"ApprovedDomains":	['roxcell.com.au'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }