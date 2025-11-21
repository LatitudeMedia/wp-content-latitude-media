/**
 * Helps display dfp ads - FIXED VERSION
 */
(function () {
  window.googletag = window.googletag || { cmd: [] };

  googletag.cmd.push(function () {
    var pathParts = window.location.pathname.substr(1).split("/");

    var dfpAdsSpots = document.querySelectorAll('[id^="div-gpt"]');
    var wpDfpAdsSettings = window.wpDfpAdsSettings || [];
    var slotsToDefine = [];

    if (
      dfpAdsSpots.length &&
      typeof wpDfpAdsSettings.slots !== "undefined" &&
      typeof wpDfpAdsSettings.slots.slot !== "undefined"
    ) {
      dfpAdsSpots.forEach(function (spot) {
        var findSlot = wpDfpAdsSettings.slots.slot.find(function (slot) {
          return slot.spot_id === spot.id;
        });

        if (findSlot) {
          slotsToDefine.push(findSlot);
        }
      });
    }

    if (slotsToDefine) {
      slotsToDefine.forEach(function (slot) {
        var mapSizes = [];
        var adsSizes = [];

        slot.size_mapping.forEach(function (sizes) {
          var currentAdSizes = sizes.ad_size_dynamic.map(function (size) {
            return size.split("x").map(function (e) {
              return Number(e);
            });
          });

          if (currentAdSizes.length) {
            adsSizes = adsSizes.concat(currentAdSizes);
          }

          if (sizes.screen_size) {
            mapSizes.push({
              screen: sizes.screen_size.split("x").map(function (e) {
                return Number(e);
              }),
              ad: currentAdSizes,
            });
          }
        });

        if (mapSizes.length > 0) {
          var adMapping = googletag.sizeMapping();
          mapSizes.forEach(function (breakpoint) {
            adMapping.addSize(breakpoint.screen, breakpoint.ad);
          });
          var builtMapping = adMapping.build();

          googletag
            .defineSlot(slot.dfp_path, adsSizes, slot.spot_id)
            .defineSizeMapping(builtMapping)
            .addService(googletag.pubads());
        } else {
          googletag
            .defineSlot(slot.dfp_path, adsSizes, slot.spot_id)
            .addService(googletag.pubads());
        }
      });
    }

    googletag.pubads().enableSingleRequest();
    googletag.pubads().collapseEmptyDivs();
    // googletag.pubads().setTargeting('articletitle', pathParts[0] ? pathParts[0]: 'home-page').setTargeting('cat_target', [wpDfpAdsSettings.categories]);
    googletag.enableServices();

    window.dispatchEvent(
      new CustomEvent("gpt-slots-ready", {
        ready: true,
      })
    );
  });
})();
