<?php

 /**
 * Elementor BiP Agenda Widget.
 *
 * Elementor widget that inserts an embbedable agenda schedule
 *
 * @since 1.1.1
 */
class Elementor_Agenda_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'agenda';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return 'Agenda';
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'fas fa-calendar-week';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return [ 'bip' ];
	}

	/**
	 * Register widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */



	protected function _register_controls() {

        $this->start_controls_section(
			'content_section', [
				'label' => 'Agenda',
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
        );


		$options = [];

		$posts = get_posts( [
				'post_type'  => 'agenda'
		] );

		foreach ( $posts as $post ) {
			$options[ $post->ID ] = $post->post_title;
		}

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'plugin-name' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .title' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_control(
			'posts',
			[
				'label' => __( 'Select Speakers', 'your-plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple' => true,
				'default' => 'all',
				'options' => $options  
			]
		);


        
        $this->end_controls_section();

	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */	


	protected function render() {

        $settings = $this->get_settings_for_display();

        ob_start();

		$myarray = $settings['posts'];

		$args = array( 'post_type' => 'agenda', 'posts_per_page' => 10, 'post__in' => $myarray, 'orderby'=>'post__in' );
		$loop = new WP_Query( $args );

			while ( $loop->have_posts() ) : $loop->the_post();

				if( have_rows('agenda_section') ):

					echo '<div class="row main-icon-list-2 p-5 bg-dark-grey">';

					while ( have_rows('agenda_section') ) : the_row();

						$unique    = uniqid();
						
						// Get Sub Fields
						$event_title = get_sub_field('event_title');
						$event_time = get_sub_field('event_time');
						$event_description = get_sub_field('event_description');
						$event_link = get_sub_field('event_link');
						$start_date = get_sub_field( 'start_time' );
						$end_date   =  get_sub_field( 'end_time' );
						$event_location   =  get_sub_field( 'event_location' );
						$speakers = get_sub_field('speakers');
						$speaker_posts = $speakers[0]["speaker_select"];

						 ?>

						<div class="col-sm-12 bg-white mb-3 speaker-container border shadow-sm">
				
							<div class="row">
									
								<div class="col-sm-1 py-3 bg-highlight border-right highlight-colour">
									<div class="font-weight-bold">
										<?php echo $event_time; ?> 
									</div>
								</div>

								<div class="col-sm-10 py-3">

									<!--Title for Speaker-->

									<div style="font-size: 18px; font-weight: bold;">
										<?php if ($event_description) { ?>
										<a class="i-btn" data-toggle="modal" data-target="#exampleModal-<?php echo $unique ?>" style="cursor: pointer;">	
											<span class="text-dark small font-weight-bold"><?php echo $event_title; ?></span>
										</a>
										<?php } else { ?>
											<span class="text-dark small font-weight-bold"><?php echo $event_title; ?></span>
										<?php } ?>
									</div>	

									<!--Speakers-->
									<?php if( $speaker_posts ): ?> <span class="small font-weight-bold highlight-color">Speakers</span><?php endif; ?>
									<div class="row">
										<?php if( $speaker_posts ): ?>
											<?php foreach( $speaker_posts as $speaker_post ): 

												$speaker_image = get_field( 'speaker_image', $speaker_post->ID );
												$speaker_name = get_field( 'speaker_name', $speaker_post->ID );
												$speaker_title = get_field( 'speaker_title', $speaker_post->ID );
												$speaker_company_name = get_field( 'speaker_company_name', $speaker_post->ID );
												$speaker_bio = get_field( 'speaker_bio', $speaker_post->ID );

												if(!empty($speaker_image['url'])){ ?>
													<div class="col-sm-1">
													<?php if(!empty($speaker_bio)) { ?>
													<span class="small font-weight-bold highlight-color" data-toggle="modal" data-target="#exampleModal-<?php echo $unique ?>" style="cursor: pointer;">
														<div class="image-container" style="height: 50px; position: relative;">
															<img class="speakerPhoto" style="max-height: 50px; text-align: center; display: block; margin:auto;top: 50%; position: relative; -ms-transform: translateY(-50%); transform: translateY(-50%);" 
															src="<?php echo esc_url($speaker_image['url']); ?>" alt="<?php echo $speaker_title; ?>" >
														</div>
													</span> 
													<?php } else { ?>
														<div class="image-container" style="height: 50px; position: relative;">
															<img class="speakerPhoto" style="max-height: 50px; text-align: center; display: block; margin:auto;top: 50%; position: relative; -ms-transform: translateY(-50%); transform: translateY(-50%);" 
															src="<?php echo esc_url($speaker_image['url']); ?>" alt="<?php echo $speaker_title; ?>"  >
														</div>
													<?php	}  ?>
													</div>
													<div class="col-sm-5 pt-2">
														<span class="speaker-name"><?php echo esc_html( $speaker_name ); ?></span>
														<p class="mb-0 font-weight-normal small"><?php echo esc_html( $speaker_title ); ?></p>
														<p class="mb-0 font-weight-light text-muted small"><?php echo esc_html( $speaker_company_name ); ?></p>
													</div>
												<?php } else { ?>
													<div class="col-sm-6 pt-2">
														<span class="speaker-name"><?php echo esc_html( $speaker_name ); ?></span>
														<p class="mb-0 font-weight-normal small"><?php echo esc_html( $speaker_title ); ?></p>
														<p class="mb-0 font-weight-light text-muted small"><?php echo esc_html( $speaker_company_name ); ?></p>
													</div>
												<?php } ?>
												<?php 
												endforeach; 
											endif;  ?>
									</div>
				
									<!-- Read more trigger modal -->
									<?php if ($event_description) { ?>
									<span class="small font-weight-bold highlight-color" data-toggle="modal" data-target="#exampleModal-<?php echo $unique ?>" style="cursor: pointer;">
										Read More <i class="fas fa-arrow-right"></i>
									</span>
									<?php } ?>
								</div>

								<!--Calendar Link-->			
								<!-- <div class="col-sm-1 py-3 mt-auto ml-auto">
									<a href="#" title="Add to Calendar" class="calendar-add"><i class="fas fa-calendar-alt" alt="Add to Calendar" title="Add to Calendar"></i></a>
								</div>
								
								<form method="post" action="?ics=true">

									<input type="hidden" name="start_date" value="<?php echo $start_date; ?>">

									<input type="hidden" name="end_date" value="<?php echo $end_date; ?>">

									<input type="hidden" name="location" value="<?php echo $event_location; ?>">

									<input type="hidden" name="summary" value="<?php echo $event_title; ?>">

									<input type="submit" value="Add to Calendar">

								</form>

								<script>
									/**
									* Create and download a file on click
									* @params {string} filename - The name of the file with the ending
									* @params {string} filebody - The contents of the file
									*/
									function download(filename, fileBody) {
										var element = document.createElement('a');
										element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(fileBody));
										element.setAttribute('download', filename);

										element.style.display = 'none';
										document.body.appendChild(element);

										element.click();

										document.body.removeChild(element);
									}


									/**
									* Returns a date/time in ICS format
									* @params {Object} dateTime - A date object you want to get the ICS format for.
									* @returns {string} String with the date in ICS format
									*/
									function convertToICSDate(dateTime) {
										const year = dateTime.getFullYear().toString();
										const month = (dateTime.getMonth() + 1) < 10 ? "0" + (dateTime.getMonth() + 1).toString() : (dateTime.getMonth() + 1).toString();
										const day = dateTime.getDate() < 10 ? "0" + dateTime.getDate().toString() : dateTime.getDate().toString();
										const hours = dateTime.getHours() < 10 ? "0" + dateTime.getHours().toString() : dateTime.getHours().toString();
										const minutes = dateTime.getMinutes() < 10 ? "0" +dateTime.getMinutes().toString() : dateTime.getMinutes().toString();

										return year + month + day + "T" + hours + minutes + "00";
									}


									/**
									* Creates and downloads an ICS file
									* @params {string} timeZone - In the format America/New_York
									* @params {object} startTime - Vaild JS Date object in the event timezone
									* @params {object} endTime - Vaild JS Date object in the event timezone
									* @params {string} title
									* @params {string} description
									* @params {string} venueName
									* @params {string} address
									* @params {string} city
									* @params {string} state
									*/
									function createDownloadICSFile(timezone, startTime, endTime, title, description, venueName, address, city, state) {
									const icsBody = 'BEGIN:VCALENDAR\n' +
									'VERSION:2.0\n' +
									'PRODID:Calendar\n' +
									'CALSCALE:GREGORIAN\n' +
									'METHOD:PUBLISH\n' +
									'BEGIN:VTIMEZONE\n' +
									'TZID:' + timezone + '\n' +
									'END:VTIMEZONE\n' +
									'BEGIN:VEVENT\n' +
									'SUMMARY:' + title + '\n' +
									'UID:@Default\n' +
									'SEQUENCE:0\n' +
									'STATUS:CONFIRMED\n' +
									'TRANSP:TRANSPARENT\n' +
									'DTSTART;TZID=' + timezone + ':' + convertToICSDate(startTime) + '\n' +
									'DTEND;TZID=' + timezone + ':' + convertToICSDate(endTime)+ '\n' +
									'DTSTAMP:'+ convertToICSDate(new Date()) + '\n' +
									'LOCATION:' + venueName + '\\n' + address + ', ' + city + ', ' + state + '\n' +
									'DESCRIPTION:' + description + '\n' +
									'END:VEVENT\n' +
									'END:VCALENDAR\n';

									download(title + '.ics', icsBody);
									}


									document.getElementById('downloadICS').addEventListener('click', () => {
									createDownloadICSFile(
										'America/Los_Angeles',
										new Date('Jan 1, 2020 08:00 PST'),
										new Date('Jan 4, 2020 17:00 PST'),
										'Example Event',
										'This is the event description',
										'Washington State Convention Center',
										'705 Pike St',
										'Seattle',
										'WA'
									);  
									});
								</script> -->

								<!-- Modal -->
								<div class="modal fade" id="exampleModal-<?php echo $unique ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel-<?php echo $unique; ?>" aria-hidden="true">
									<div class="modal-dialog mt-5" role="document">
										<div class="modal-content">
											<div class="modal-header">
											<h5 class="modal-title" id="exampleModalLabel-<?php echo $unique; ?>"><?php echo $event_title ?> - <?php echo $event_time ?></h5>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body">
												<p><?php echo $event_description; ?></p>
												<!--Speakers-->
												<?php if( $speaker_posts ): ?> <span class="small font-weight-bold highlight-color">Speakers</span><?php endif; ?>
												<div class="row">
												<?php if( $speaker_posts ): ?>
													<?php foreach( $speaker_posts as $speaker_post ): 
														// $permalink = get_permalink( $speaker_post->ID );
														// $speaker_name = get_the_title( $speaker_post->ID );
														$speaker_image = get_field( 'speaker_image', $speaker_post->ID );
														$speaker_name = get_field( 'speaker_name', $speaker_post->ID );
														$speaker_title = get_field( 'speaker_title', $speaker_post->ID );
														$speaker_company_name = get_field( 'speaker_company_name', $speaker_post->ID );
														$speaker_bio = get_field( 'speaker_bio', $speaker_post->ID );
														if(!empty($speaker_image['url'])){ ?>
															<div class="col-sm-2">
															<?php if(!empty($speaker_bio)) { ?>
															<span class="small font-weight-bold highlight-color" data-toggle="modal" data-target="#exampleModal-<?php echo $unique ?>" style="cursor: pointer;">
																<div class="image-container" style="height: 50px; position: relative;">
																	<img class="speakerPhoto" alt="<?php echo $speaker_name; ?>" style="max-height: 50px; text-align: center; display: block; margin:auto;top: 50%; position: relative; -ms-transform: translateY(-50%); transform: translateY(-50%);" 
																	src="<?php echo esc_url($speaker_image['url']); ?>" >
																</div>
															</span> 
															<?php } else { ?>
																<div class="image-container" style="height: 50px; position: relative;">
																	<img class="speakerPhoto" style="max-height: 50px; text-align: center; display: block; margin:auto;top: 50%; position: relative; -ms-transform: translateY(-50%); transform: translateY(-50%);" 
																	src="<?php echo esc_url($speaker_image['url']); ?>" alt="<?php echo $speaker_name ?>"  >
																</div>
															<?php	}  ?>
															</div>
														<?php } ?>
														<div class="col-sm-4 pt-2">
															<span class="speaker-name"><?php echo esc_html( $speaker_name ); ?></span>
															<p class="mb-0 font-weight-normal small"><?php echo esc_html( $speaker_title ); ?></p>
															<p class="mb-0 font-weight-light text-muted small"><?php echo esc_html( $speaker_company_name ); ?></p>
														</div>
														<?php 
														endforeach; 
													endif;  ?>
											</div>
											</div>
											<div class="modal-footer">
												<a class="text-white" href="<?php echo $event_link ?>" target="_blank"><button type="button" class="btn bg-highlight">Learn More</a>
												<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
											</div>
										</div>
									</div>
								</div>


							</div>

						</div>
					<?php 
					endwhile;
				endif;	
			endwhile; 
		wp_reset_query();
		?>

		</div>

        <!--Enqueue later-->
        <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>

			<!--
				!
				!
				!
				ELEMENTOR STARTING POINT
				IMPORTANT
				Use for background color 
				THIS USES ELEMENTOR COLOR PICKER
				WE CURRENT DONT HAVE A REPEATER SO THE NEXT BIT OF CODE WITH $settings['list'] WILL NOT WORK!!
				!
				!
				!
			-->
				<!-- <h2 style="color: <?php echo $settings['title_color'] ?> ">
					Background color pull in <small>(read description on code) </small>- <?php echo $settings['title_color']; ?>
				</h2>
				
				<?php     
					// $show_title = $settings['posts'];
					// // echo $show_title; 
					// echo get_the_title( $show_title );
				?> -->
	

			<!--
			!
			!
			!
			IMPORTANT
			Use for background color 
			THIS USES ELEMENTOR COLOR PICKER
			WE CURRENT DONT HAVE A REPEATER SO THE NEXT BIT OF CODE WITH $settings['list'] WILL NOT WORK!!
			!
			!
			!
		-->

		

	
        <!--Add into seprate file once done-->

        <style>

			.row.main-icon-list-2 {
				overflow: hidden;
			}

			.circle {
				height: 10px;
				width: 10px;
				border-radius: 50%;
				border: 10px solid;
				position: relative;
				border-color: #cccccc;
			}

			.circle:before {
				content: "";
				display: block;
				position: absolute;
				z-index: 1;
				top: 100%;
				left: 50%;
				border: 1px solid #cccccc;
				border-width: 0 0 0 1px;
				width: 1px;
				height: 1110px;
			}

            .speaker-container .image-container {
                top: 14px;
                position: relative;
                width: 50px;
                margin: 0 auto;
                overflow: hidden;
                border-radius: 50%;
                margin-bottom: 25px;
            }

            .speaker-container img {
                min-height: 100%!important;
                margin: auto;
                top: 50%;
                position: relative;
                -ms-transform: translateY(-50%);
                width: 100%;
                display: block;
                transform: translateY(-50%);
                object-fit: cover;
            }


            .speaker-container img:hover {
                display: block;
                -o-filter: none;
                -ms-filter: none;
                -moz-filter: none;
                -webkit-filter: none;
                filter: none;
                transition: all .3s ease;
                -moz-transition: all .3s ease;
                -webkit-transition: all .3s ease;
                -ms-transition: all .3s ease;
                -o-transition: all .3s ease;
                transform: translateY(-50%);
                object-fit: cover;
            }

			i.fas.fa-info-circle {
				color: #907d6f;
				font-size: 18px;
				margin-left: 2px!important;
			}

			.speaker-name *{
				font-weight: bold;
				margin-bottom: 0px;
				font-size: 14px;
			}

			.bg-dark-grey {
				background-color: #e0e0e0;
			}

			.calendar-add, .calendar-add:hover, .calendar-add:active {
				color: white;
    			background-color: <?php echo $settings['title_color']?>;
    			padding: 10px;
			}

			.highlight-color {
				color: <?php echo $settings['title_color']?>;
			}

			.bg-highlight {
				background-color: <?php echo $settings['title_color']?>;
				color: white;
			}

			.btn:hover {
				background-color: <?php echo $settings['title_color']?>;
			}

			@media (max-width: 600px) {
				.circle {
					display: none;
				}
			}

        </style>
    
    <?php 

	
    $output_string = ob_get_contents();
    ob_end_clean();

    echo $output_string;

    }

}