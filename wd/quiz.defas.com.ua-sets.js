
var IpAid='quiz.defas.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['quiz.defas.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }