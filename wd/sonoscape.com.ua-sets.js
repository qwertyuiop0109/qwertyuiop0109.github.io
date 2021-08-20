
var IpAid='sonoscape.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['sonoscape.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }