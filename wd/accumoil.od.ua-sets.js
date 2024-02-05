
var IpAid='accumoil.od.ua';


var intepriceApermissions = {
	"ApprovedDomains":	['accumoil.od.ua','accumoil.inteprice.com','market.kharkov.ua'],
	"CallHunter":		true
};


if (typeof window.intepriceCallHunterIni === "undefined") {
	window.intepriceCallHunterIni='config.js';
}


if (typeof intepriceCallHunterInit == 'function') { intepriceCallHunterInit(); }