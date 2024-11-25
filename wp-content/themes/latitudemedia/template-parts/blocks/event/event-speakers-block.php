<?php
if (is_admin()) {
    echo '<h3 style="text-align: center;">' . __('Event speakers block', 'ltm') . '</h3>';
}
// Set defaults Event speakers block.
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
          "class" => 'content-block our-team-section',
          "id" => 'event-speakers-block' . ($options['blockAttributes']['anchor'] ? ' ' . $options['blockAttributes']['anchor'] : ''),
      ]
  )
);

?>

<div <?php echo $blockAttrs; ?>
>
    <div class="container-narrow">
        <div class="our-team-section-wrapper">
            <div class="bordered-title green">featured speakers</div>
            <ul class="team">
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker1.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Peter Freed</a>
                        <div class="occupation">Former Meta + Near Horizon Group</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker2.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Julia Lundin</a>
                        <div class="occupation">AES</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker3.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Chris Shelton</a>
                        <div class="occupation">AES</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker4.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Abid Suddiqui</a>
                        <div class="occupation">AI Fund</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker5.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Chase Lochmiller</a>
                        <div class="occupation">Crusoe</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker6.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Helena Fu</a>
                        <div class="occupation">DOE</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker7.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Charles Yang</a>
                        <div class="occupation">DOE</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker8.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Kevin Jones</a>
                        <div class="occupation">Dominion Energy</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker9.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Jeremy Renshaw</a>
                        <div class="occupation">EPRI</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker10.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Sayles Braga</a>
                        <div class="occupation">Sidewalk Infrastructure Partners</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker11.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Sean Murphy</a>
                        <div class="occupation">PingThings</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker12.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Josh Wong</a>
                        <div class="occupation">ThinkLabs AI</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker13.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Stephen Lacey</a>
                        <div class="occupation">Latitude Media</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
                <li>
                    <div class="image-folder green">
                        <a href="#">
                            <img alt="member" src="client/assets/images/speaker14.png">
                        </a>
                    </div>
                    <div class="content-folder">
                        <a class="name green" href="#">Maeve Allsup</a>
                        <div class="occupation">Latitude Media</div>
                        <a class="more-link" href="#">Read more</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>

