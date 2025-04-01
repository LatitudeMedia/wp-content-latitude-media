/**
 * Helps display dfp ads
 *
 */
( function() {

	window.googletag = window.googletag || {cmd: []};
	googletag.cmd.push(function() {
		var pathParts = window.location.pathname.substr(1).split('/');
		var dfpAdsSpots = document.querySelectorAll('[id^="div-gpt"]');
		var wpDfpAdsSettings = window.wpDfpAdsSettings || [];
		var slotsToDefine = [];
		if( dfpAdsSpots.length
			&& typeof wpDfpAdsSettings.slots !== 'undefined'
			&& typeof wpDfpAdsSettings.slots.slot !== 'undefined' ) {
			dfpAdsSpots.forEach(function(spot) {
				var findSlot = wpDfpAdsSettings.slots.slot.find(function(slot) {
					return slot.spot_id === spot.id;
				});

				if(findSlot) {
					slotsToDefine.push(findSlot);
				}
			});
		}

		if(slotsToDefine) {
			slotsToDefine.forEach(function(slot) {
				var mapSizes = [];
				var adsSizes = [];
				slot.size_mapping.forEach(function(sizes) {
					if(!sizes.screen_size) {
						var baseSizes = sizes.ad_size_dynamic.map(function(size) {
							return size.split('x').map(function(e) { return Number(e)});
						});
						if(baseSizes.length) {
							adsSizes = adsSizes.concat(baseSizes);
						}
					}
					else {
						mapSizes.push({
							screen: sizes.screen_size.split('x').map(function(e) { return Number(e)}),
							ad: 	sizes.ad_size_dynamic.map(function(size) {
								return size.split('x').map(function(e) { return Number(e)});
							})
						});
					}
				});

				if(mapSizes) {
					var adMapping = googletag.sizeMapping();
					mapSizes.forEach( function(breakpoint) {
						adMapping.addSize(breakpoint.screen, breakpoint.ad);
					});
					var builtMapping = adMapping.build();

					googletag.defineSlot(slot.dfp_path, adsSizes, slot.spot_id)
						.defineSizeMapping(builtMapping)
						.addService(googletag.pubads());

				}
				else {
					googletag.defineSlot(slot.dfp_path, adsSizes, slot.spot_id).addService(googletag.pubads());
				}
			})
		}

		if( typeof wpDfpAdsSettings.targeting !== 'undefined' ) {
			Object.entries(wpDfpAdsSettings.targeting).forEach(([key, value]) => {
				googletag.pubads().setTargeting(key, value.toString())
			});
		}

		// targeting
		googletag.pubads().enableSingleRequest();
		googletag.pubads().collapseEmptyDivs();
		googletag.enableServices();

		window.dispatchEvent(new CustomEvent('gpt-slots-ready', {
			ready: true
		}));
	});

})();