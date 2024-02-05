
//-- Google Analytics counter --

  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga_IpA');


  ga_IpA('create', 'UA-54940309-1', { 'cookieDomain': 'auto', 'siteSpeedSampleRate': 50 });

  ga_IpA('require', 'linkid', 'linkid.js');
  ga_IpA('require', 'displayfeatures');
  ga_IpA('send', 'pageview');


//-- add --
//var ga = ga_IpA;
  window.GAtrackingId;
  window.GAclientId;
  window.GAuserId;
  window.GAlibloaded=0;
  ga_IpA(function(tracker) {
    window.GAlibloaded=1;
	window.GAtrackingId = tracker.get('trackingId');
	window.GAclientId = tracker.get('clientId');
	window.GAuserId = tracker.get('userId');
	if (typeof window.trackRingHitIA == 'function') { window.trackRingHitIA(); }
  });
//-- /add --

//-- /Google Analytics counter --





//-- Yandex.Metrika counter --

(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter575119 = new Ya.Metrika({id:575119,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";


//-- add --
    window.YAcounter=0;
    s.onreadystatechange = s.onload = function(){
    var state = s.readyState;
	if ((!state)||(state&&((state=='loaded')||(state=='complete')))) {
		for (var key in window) {
			if ( (typeof(window[key]) == 'object') && (key.toString().search('yaCounter') != -1) && (typeof(window[key].reachGoal) == 'function') ) {
				s.onreadystatechange = s.onload = null;
				window.YAcounter=window[key];
				break;
			}
		}
		if (typeof window.trackRingHitIA == 'function') { window.trackRingHitIA(); }
	}
    }
//-- /add --


    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");

//-- /Yandex.Metrika counter --

