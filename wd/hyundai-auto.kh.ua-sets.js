
var IpAid='frunze.hyundai.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['hyundai-auto.kh.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }