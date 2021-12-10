
var IpAid='healthytrends.com.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['healthytrends.com.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }