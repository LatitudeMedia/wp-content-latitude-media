<?php

function flying_scripts_inject_js() {

  $timeout = esc_attr(get_option('flying_scripts_timeout'));
    ?>
<script type="text/javascript" id="flying-scripts">const loadScriptsTimer=setTimeout(loadScripts,<?php echo $timeout ?>*1000);const userInteractionEvents=['click', 'mousemove', 'keydown', 'touchstart', 'touchmove', 'wheel'];userInteractionEvents.forEach(function(event){window.addEventListener(event,triggerScriptLoader,{passive:!0})});function triggerScriptLoader(){loadScripts();clearTimeout(loadScriptsTimer);userInteractionEvents.forEach(function(event){window.removeEventListener(event,triggerScriptLoader,{passive:!0})})}
function loadScripts(){document.querySelectorAll("script[data-type='lazy']").forEach(function(elem){elem.setAttribute("src",elem.getAttribute("data-src"))})}</script>
    <?php
}

add_action( 'wp_print_footer_scripts', 'flying_scripts_inject_js');