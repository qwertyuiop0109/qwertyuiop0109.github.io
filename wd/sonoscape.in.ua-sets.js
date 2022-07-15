
var IpAid='sonoscape.in.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['sonoscape.in.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }