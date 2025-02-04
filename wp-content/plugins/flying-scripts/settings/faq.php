<?php
function flying_scripts_view_faq() {
?>
    <h3>What are the ideal scripts to be included?</h3>
    <p>Any script that is not crucial for rendering the first view or above fold contents. 3rd party scripts like tracking scripts, chat plugins, etc are ideal.</p>

    <h3>What should I put in include keywords</h3>
    <p>Any keyword inside your inline script that uniquely identifies that script. For example "fbevents.js" for Facebook Pixel, "gtag" for Google Tag Manager, "customerchat.js" for Facebook Customer Chat plugin.</p>

    <h3>How is it different from <code>defer</code></h3>
    <p><code>defer</code> tells the browser to download the script when found and execute it when HTML parsing is complete. When you include a script in Flying Scripts, those scripts won't be executed until there is user interaction.</p>

    <h3>What is user interaction?</h3>
    <p>Events from the user like mouse hover, scroll, keyboard input, touch in a mobile device, etc.</p>

    <h3>What is timeout?</h3>
    <p>Even if there is no user interaction, scripts will be executed after the specified timeout.</p>
<?php
}