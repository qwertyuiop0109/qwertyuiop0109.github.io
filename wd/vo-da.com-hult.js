function checkloadIPactive(a){a.intepriceCallHunterActive=!1;if(a.jqIPAcode&&(a.jqIPAcode("#clhtr_mob_banner-startphone").hide(),a.jqIPAcode("#clhtr_phone_div").hide(),!a.intepriceCHOKS)){var b=a.IPclhrDataUpdate&&Number(IPclhrDataUpdate.consultantOn)||a.IPclhrData&&Number(IPclhrData.consultantOn);b&&(a.intepriceCHOKS=1,1==b?a.jqIPAcode("<style>").prop("type","text/css").html("div#supportTrigger, div#supportTriggerMobile { display: none!important; visibility: hidden!important; }").appendTo("head"):2==b?a.jqIPAcode("<style>").prop("type","text/css").html("div#sh_button { display: none!important; visibility: hidden!important; }").appendTo("head"):3!=b&&4!=b||a.jqIPAcode("<style>").prop("type","text/css").html("div#jivo_chat_widget, div#jivo-iframe-container { display: none!important; visibility: hidden!important; }").appendTo("head"))}setTimeout(function(){checkloadIPactive(a)},2E3)}setTimeout(function(){checkloadIPactive(window)},700);

(function(d) { var s = d.createElement('script');
s.type ='text/javascript'; s.charset = 'utf-8';	s.async = true;
s.src = 'https://cdn.sale-storm.com/wd/invite/support.js?n=12';
var sc = d.getElementsByTagName('script')[0];
if (sc) sc.parentNode.insertBefore(s, sc);
else d.documentElement.firstChild.appendChild(s);
})(document);