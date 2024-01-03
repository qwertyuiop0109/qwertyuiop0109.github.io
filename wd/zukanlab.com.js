var pagecur=window.location.href.toString().split(window.location.host)[1];
if (pagecur.indexOf('wrk=12') >= 0) {

	function docready(f){/in/.test(document.readyState)?setTimeout(docready,9,f):f()}

//без docready не всегда передаются конверсии в аналитикс, и не работает SSCHunterAdwordstrack (так как не успевает до того подгрузится SSCHunterAdwordstrack())
	docready(function(){



//передаем в ads Другое
		try {


			if (Math.random() < 0.7) {
			//if (1) {

				setTimeout(function() {

					var AdwConvTime = {
					google_conversion_id: 612145020,
					google_conversion_language: "en",
					google_conversion_format: "3",
					google_conversion_color: "ffffff",
					google_conversion_label: "LZpHCM6pgdsBEPyu8qMC",
					google_remarketing_only: false
						};
					console.log(typeof(window['google_trackConversion']));
					SSCHunterAdwordstrack(AdwConvTime);
					console.log('SSCHunterAdwordstrack Ok');


				}, 10);


			}
		} catch (e) {
			console.log('SSCHunterAdwordstrack Error, name: ' + e.name + ", message: " + e.message);
		}


//передаем в ads Запрос цены
		try {

			//if (Math.random() < 0.5) {
			if (1) {

				setTimeout(function() {

					var AdwConvTime = {
					google_conversion_id: 612145020,
					google_conversion_language: "en",
					google_conversion_format: "3",
					google_conversion_color: "ffffff",
					google_conversion_label: "VglsCIaz-doBEPyu8qMC",
					google_remarketing_only: false
						};
					console.log(typeof(window['google_trackConversion']));
					SSCHunterAdwordstrack(AdwConvTime);
					console.log('SSCHunterAdwordstrack2 Ok');


				}, 1000);


			}
		} catch (e) {
			console.log('SSCHunterAdwordstrack2 Error, name: ' + e.name + ", message: " + e.message);
		}





	});     


}
