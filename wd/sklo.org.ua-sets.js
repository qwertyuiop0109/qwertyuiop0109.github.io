
var IpAid='sklo.org.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['sklo.org.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }