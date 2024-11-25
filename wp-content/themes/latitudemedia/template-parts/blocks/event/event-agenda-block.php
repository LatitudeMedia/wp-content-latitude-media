<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event agenda block', 'ltm') . '</h3>';
}
// Set defaults Event agenda block.
$options = wp_parse_args(
    array_merge($args, get_fields() ?? []),
    [
        'display' => false,
        'blockAttributes' => [],
    ]
);

extract($options);

if(!$display && !is_admin()) {
    return;
}


$blockAttrs = wp_kses_data(
  get_block_wrapper_attributes(
      [
          "class" => 'content-block agenda-section green',
          "id" => 'event-agenda-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="bordered-title green">Agenda</div>
        <div class="agenda-section-wrapper">
            <ul>
                <li>
                    <div class="agenda-data">
                        <div class="time">8:00 am - 9:00 am</div>
                        <h5>Registration and networking breakfast</h5>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">9:00 AM - 9:15 AM</div>
                        <h5>Welcome, and opening comments from Latitude Media</h5>
                        <p>Kicking off Transition-AI with an overview of our coverage of the space, key themes in
                            the market in 2024, and the plan for the conference</p>
                        <ul class="speakers">
                            <li>
                                <span class="name">Stephen Lacey</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">9:15 AM - 9:45 AM</div>
                        <h5>Keynote interview</h5>
                        <p></p>
                        <ul class="speakers">
                            <li>
                                <span class="name">Stephen Lacey</span>, <span class="company">Latitude Media</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">9:45 AM - 10:15 AM</div>
                        <h5>In conversation - Building an AI startup ecosystem for energy</h5>
                        <ul class="speakers">
                            <li>
                                <span class="name">Julia Lundin</span>, <span class="company">AES</span>
                            </li>
                            <li>
                                <span class="name">Abid Suddiqui</span>, <span class="company">AI Fund</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">10:15 AM - 10:45 AM</div>
                        <h5>Networking Break</h5>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">10:45 AM - 11:30 AM</div>
                        <h5>Panel session - Meeting the load growth challenge</h5>
                        <p>A panel discussion diving deep into the outlook for load growth driven by data center
                            demand from AI</p>
                        <ul class="speakers">
                            <li>
                                <span class="name">Jeremy Renshaw</span>, <span class="company">EPRI</span>
                            </li>
                            <li>
                                <span class="name">Page Crahan</span>, <span
                                        class="company">Google X - Tapestry</span>
                            </li>
                            <li>
                                <span class="name">Chris Shelton</span>, <span class="company">AES</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">11:30 AM - 12:00 PM</div>
                        <h5>Case Study - AI and the customer</h5>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">12:00 PM - 1:00 PM</div>
                        <h5>Lunch</h5>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">8:00 am - 9:00 am</div>
                        <h5>Registration and networking breakfast</h5>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">9:00 AM - 9:15 AM</div>
                        <h5>Welcome, and opening comments from Latitude Media</h5>
                        <p>Kicking off Transition-AI with an overview of our coverage of the space, key themes in
                            the market in 2024, and the plan for the conference</p>
                        <ul class="speakers">
                            <li>
                                <span class="name">Stephen Lacey</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">9:15 AM - 9:45 AM</div>
                        <h5>Keynote interview</h5>
                        <p></p>
                        <ul class="speakers">
                            <li>
                                <span class="name">Stephen Lacey</span>, <span class="company">Latitude Media</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">9:45 AM - 10:15 AM</div>
                        <h5>In conversation - Building an AI startup ecosystem for energy</h5>
                        <ul class="speakers">
                            <li>
                                <span class="name">Julia Lundin</span>, <span class="company">AES</span>
                            </li>
                            <li>
                                <span class="name">Abid Suddiqui</span>, <span class="company">AI Fund</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">10:15 AM - 10:45 AM</div>
                        <h5>Networking Break</h5>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">10:45 AM - 11:30 AM</div>
                        <h5>Panel session - Meeting the load growth challenge</h5>
                        <p>A panel discussion diving deep into the outlook for load growth driven by data center
                            demand from AI</p>
                        <ul class="speakers">
                            <li>
                                <span class="name">Jeremy Renshaw</span>, <span class="company">EPRI</span>
                            </li>
                            <li>
                                <span class="name">Page Crahan</span>, <span
                                        class="company">Google X - Tapestry</span>
                            </li>
                            <li>
                                <span class="name">Chris Shelton</span>, <span class="company">AES</span>
                            </li>
                        </ul>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">11:30 AM - 12:00 PM</div>
                        <h5>Case Study - AI and the customer</h5>
                    </div>
                </li>
                <li>
                    <div class="agenda-data">
                        <div class="time">12:00 PM - 1:00 PM</div>
                        <h5>Lunch</h5>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>