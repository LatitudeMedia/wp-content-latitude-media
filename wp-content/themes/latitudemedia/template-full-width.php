<?php
/**
 * Template Name: Full width template
 *
 */

get_header();

$hide_title = get_field('hide_title', get_the_ID());
if( !$hide_title ) {
    printf('<div class="container-narrow"><h1>%s</h1></div>', get_the_title() );
}

the_content();
?>
    <div class="image-text-section company-search-block pink">
        <div class="container-narrow">
            <div class="bordered-title">Amplify your company’s reach</div>
            <div class="image-text-section-wrapper">
                <div class="image-folder">
                    <a href="#">
                        <img src="client/assets/images/company_search.png" alt="img">
                    </a>
                </div>
                <div class="content-folder">
                    <p>
                        We’re a team of producers and journalists with strong roots in clean energy and audio storytelling. We go deeper on the trends that matter most to the industry. <strong>We don’t just make podcasts; we make entertaining audio stories that resonate.</strong></p>
                </div>
            </div>
        </div>
    </div>

    <div class="downloads-counter-section">
        <div class="container-narrow">
            <div class="downloads-counter-section-wrapper">
                <div class="description">
                    <h2>Downloads</h2>
                    <p>Each month, our download numbers reflect the growing success of our content with our B2B audience.</p>
                </div>
                <div class="digits">
                    <div class="left">
                        <div class="digit">2.1M</div>
                        <div class="caption">Total downloads recorded</div>
                    </div>
                    <div class="right">
                        <div class="digit">80K</div>
                        <div class="caption">Average monthly downloads</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="image-text-section tall-it-block pink">
        <div class="container-narrow">
            <div class="image-text-section-wrapper">
                <div class="image-folder">
                    <a href="#">
                        <img src="client/assets/images/audience.png" alt="img">
                    </a>
                </div>
                <div class="content-folder">
                    <h2>Audience</h2>
                    <h4>Highly engaged leaders across: </h4>
                    <ul>
                        <li>Clean energy + Energy storage</li>
                        <li>Utility + IPP</li>
                        <li>Project development + EPC + Installer</li>
                        <li>Technology vendor + Manufacturer</li>
                        <li>Finance + Investing</li>
                        <li>Electrified transportation</li>
                        <li>Corporate</li>
                        <li>Government + Policy + Regulatory</li>
                        <li>Consulting + Market research</li>
                        <li>Academia</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="image-text-section tall-it-block reverted pink">
        <div class="container-narrow">
            <div class="image-text-section-wrapper">
                <div class="content-folder">
                    <h2>Previous sponsors</h2>
                    <p>Podcast advertising has increased brand awareness and thought leadership for our sponsors across the clean energy and climate tech landscape.</p>
                </div>
                <div class="image-folder">
                    <a href="#">
                        <img src="client/assets/images/logos.png" alt="img">
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="wide-form-section">
        <div class="container-narrow">
            <div class="wide-form-section-wrapper">
                <h2>Learn how to engage with our podcast listeners</h2>
                <p>Learn more about podcast advertising opportunities with Latitude Media.</p>
                <form action="">
                    <div class="fieldset double">
                        <div class="left">
                            <label for="first-name">First Name*</label>
                            <input type="text" id="first-name">
                        </div>
                        <div class="right">
                            <label for="last-name">Last Name*</label>
                            <input type="text" id="last-name">
                        </div>
                    </div>
                    <div class="fieldset double">
                        <div class="left">
                            <label for="work-email">Work Email*</label>
                            <input type="email" id="work-email">
                        </div>
                        <div class="right">
                            <label for="phone">Phone Number*</label>
                            <input type="text" id="phone-number">
                        </div>
                    </div>
                    <div class="fieldset double">
                        <div class="left">
                            <label for="company">Company Name*</label>
                            <input type="text" id="company">
                        </div>
                        <div class="right">
                            <label for="job">Job Title**</label>
                            <input type="text" id="job">
                        </div>
                    </div>
                    <input type="submit" value="Submit">
                </form>
            </div>
        </div>
    </div>

    <div class="listeners-section">
        <div class="container-narrow">
            <div class="listeners-section-wrapper">
                <h2>What listeners are saying</h2>
                <ul>
                    <li>
                        <div class="percent">95%</div>
                        <p>Of listeners say our podcasts help <strong>keep them informed</strong> about broad trends within their industry.</p>
                    </li>
                    <li>
                        <div class="percent">72%</div>
                        <p>Of listeners say our podcasts <strong>provide important insights</strong> that inform their day-to-day decisions.</p>
                    </li>
                    <li>
                        <div class="percent">63%</div>
                        <p>Of listeners say our podcasts help them <strong>discover potential partners, suppliers, and competitors.</strong></p>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="podcasts-sponsorship-section">
        <div class="container-narrow">
            <div class="bordered-title">
                Podcasts Available for Sponsorship
            </div>
            <div class="podcasts-sponsorship-section-wrapper">
                <div class="image-folder">
                    <a href="#">
                        <img src="client/assets/images/podcast_img12.png" alt="img">
                    </a>
                </div>
                <div class="content-folder">
                    <h4>Catalyst</h4>
                    <div class="top-info">
                        <p>LAUNCHED: November 2021</p>
                        <p>HOST: Shayle Kann</p>
                    </div>
                    <div class="bottom-info">
                        <p>SAMPLE EPISODES:</p>
                        <ul>
                            <li>What’s really happening in the EV market?</li>
                            <li>Financing first-of-a-kind climate assets</li>
                            <li>How is U.S. industrial policy affecting actual climate tech investment?</li>
                        </ul>
                    </div>
                    <a href="#" class="button">Read listener reviews</a>
                </div>
            </div>
        </div>
    </div>

    <div class="adv-options-section">
        <div class="container-narrow">
            <div class="bordered-title">Advertising Options</div>
            <div class="adv-options-section-wrapper">
                <ul>
                    <li>
                        <div class="title">Standard podcast ads</div>
                        <p>Position your company as a thought leader with pre- and mid-roll ads that frame your company around its work and innovations in the industry.</p>
                        <a href="#" class="listen-link">Listen to sample</a>
                    </li>
                    <li>
                        <div class="title">Drop-in episodes</div>
                        <p>Spotlight a company executive with a dedicated interview with Stephen Lacey to discuss industry trends or a specific topic relevant to your company’s work.</p>
                        <a href="#" class="listen-link">Listen to sample</a>
                    </li>
                    <li>
                        <div class="title">Serialized campaign</div>
                        <p>Tell a story to our audience through customer stories and/or executive interviews through pre-roll and mid-roll ads over the course of several podcast episodes.</p>
                        <a href="#" class="listen-link">Listen to sample</a>
                    </li>
                    <li>
                        <div class="title">Custom post-roll campaign</div>
                        <p>Delve into insights or trends seen by your company with Stephen Lacey in a 4-5 minute feature hosted in an episode’s post-roll feed, enticing listeners to stay turned with a pre-roll sound bite at the beginning of the following episode.</p>
                        <a href="#" class="listen-link">Listen to sample</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-cta-line orange">
        <div class="container-narrow">
            <div class="text-cta-line-wrapper">
                <h5>Looking for something more custom? Reach out to our team at <a href="#">Latitude Studios</a> to learn more.</h5>
            </div>
        </div>
    </div>

    <div class="grey-cta-block">
        <div class="container-narrow">
            <div class="grey-cta-block-wrapper">
                <h2>Get in front of the Latitude Media podcast audience.</h2>
                <a href="#" class="cta-button pink">Contact Us</a>
            </div>
        </div>
    </div>

<?php
get_footer();
