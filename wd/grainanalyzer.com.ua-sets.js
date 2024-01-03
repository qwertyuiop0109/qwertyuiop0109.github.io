
var IpAid='grainanalyzer.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['grainanalyzer.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }