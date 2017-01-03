<?php global $lwp_options, $Listing; ?>
<div id="email_fancybox_form" class="" style="display: none">
	<?php if ( ! isset( $lwp_options['email_friend_form_shortcode'] ) || empty( $lwp_options['email_friend_form_shortcode'] ) ) { ?>
		<h3><?php _e( "Email", "listings" ); ?></h3>

		<form name="email_friend" method="post" class="ajax_form">
			<table>
				<tr>
					<td><label for="friend_form_name"><?php _e( "Sender's name", "listings" ); ?></label>:</td>
					<td><input type="text" name="name" id="friend_form_name"></td>
				</tr>
				<tr>
					<td><label for="friend_form_email"><?php _e( "Sender's email", "listings" ); ?></label>:</td>
					<td><input type="text" name="email" id="friend_form_email"></td>
				</tr>
				<tr>
					<td><label for="friend_form_friend_email"><?php _e( "Recipient's email", "listings" ); ?></label>:</td>
					<td><input type="text" name="friends_email" id="friend_form_friend_email"></td>
				</tr>
				<tr>
					<td colspan="2"><label for="friend_form_message"><?php _e( "Message", "listings" ); ?></label>:<br>
						<textarea name="message" class="fancybox_textarea" id="friend_form_message"></textarea></td>
				</tr>
				<?php
				if ( $lwp_options['recaptcha_enabled'] == 1 && isset( $lwp_options['recaptcha_public_key'] ) && ! empty( $lwp_options['recaptcha_public_key'] ) ) {
					echo "<tr><td colspan='2'>" . __( "reCAPTCHA", "listings" ) . ": <br><div id='email_fancybox_form_recaptcha' class='recaptcha_holder' style=\"transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;\"></div></td></tr>";
				} ?>
				<tr>
					<td colspan="2"><input type="submit" value="<?php _e( "Submit", "listings" ); ?>"> <i
							class="fa fa-refresh fa-spin loading_icon_form"></i></td>
				</tr>
			</table>
		</form>
	<?php } else {
		echo do_shortcode( $lwp_options['email_friend_form_shortcode'] );
	} ?>
</div>

<div id="trade_fancybox_form" class="" style="display: none">
	<?php if ( ! isset( $lwp_options['tradein_form_shortcode'] ) || empty( $lwp_options['tradein_form_shortcode'] ) ) { ?>
		<h3><?php _e( "Trade-In", "listings" ); ?></h3>

		<form name="trade_in" method="post" class="ajax_form">
			<div class="container">
				<div class="row">

					<div class="col-md-6">
						<h4><?php _e( "Contact Information", "listings" ); ?></h4>

						<div class="row">
							<div class="col-md-6">
								<?php _e( "First Name", "listings" ); ?><br><input type="text" name="first_name"><br>
								<?php _e( "Work Phone", "listings" ); ?><br><input type="text" name="work_phone"><br>
								<?php _e( "Email", "listings" ); ?><br><input type="text" name="email">
							</div>

							<div class="col-md-6">
								<?php _e( "Last Name", "listings" ); ?><br><input type="text" name="last_name"><br>
								<?php _e( "Phone", "listings" ); ?><br><input type="text" name="phone"><br>
								<?php _e( "Preferred Contact", "listings" ); ?><br> <span class="styled_input"> <input
										type="radio" name="contact_method" value="<?php _e( 'email', 'listings' ); ?>"
										id="email"> <label for="email"><?php _e( "Email", "listings" ); ?></label>  <input
										type="radio" name="contact_method" value="<?php _e( 'phone', 'listings' ); ?>"
										id="phone"> <label for="phone"><?php _e( "Phone", "listings" ); ?></label> </span>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<?php _e( "Comments", "listings" ); ?><br><textarea name="comments"
								                                                    style="width: 89%;"
								                                                    rows="5"></textarea>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<h4><?php _e( "Options", "listings" ); ?></h4>

						<?php
						$options = $Listing->get_single_listing_category( "options" );
						$options = ( isset( $options['terms'] ) && ! empty( $options['terms'] ) ? $options['terms'] : array() );
						?>

						<select name="options" multiple style="height: 200px;" data-update="false">
							<?php

							if ( empty( $options ) ) {
								echo "<option value='" . __( "Not available", "listings" ) . "'>N/A</option>";
							} else {

								array_multisort( array_map( 'strtolower', $options ), $options );

								foreach ( $options as $option ) {
									echo "<option value='" . $option . "'>" . $option . "</option>";
								}
							}

							?>
						</select>
					</div>

				</div>
			</div>

			<div style="clear:both;"></div>

			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<h4><?php _e( "Vehicle Information", "listings" ); ?></h4>

						<div class="">
							<div class="row">

								<div class="col-md-6">
									<?php _e( "Year", "listings" ); ?><br><input type="text" name="year"><br>
									<?php _e( "Model", "listings" ); ?><br><input type="text" name="model"><br>
									<?php _e( "VIN", "listings" ); ?><br><input type="text" name="vin"><br>
									<?php _e( "Engine", "listings" ); ?><br><input type="text" name="engine"><br>
									<?php _e( "Transmission", "listings" ); ?><br><select name="transmission" class="css-dropdowns"
									                                                      data-update="false">
										<option
											value="<?php _e( "Automatic", "listings" ); ?>"><?php _e( "Automatic", "listings" ); ?></option>
										<option
											value="<?php _e( "Manual", "listings" ); ?>"><?php _e( "Manual", "listings" ); ?></option>
									</select>
								</div>

								<div class="col-md-6">
									<?php _e( "Make", "listings" ); ?><br><input type="text" name="make"><br>
									<?php _e( "Exterior Colour", "listings" ); ?><br><input type="text" name="exterior_colour"><br>
									<?php _e( "Kilometres", "listings" ); ?><br><input type="text" name="kilometres"><br>
									<?php _e( "Doors", "listings" ); ?><br><select name="doors" class="css-dropdowns"
									                                               data-update="false">
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
									</select><br>
									<?php _e( "Drivetrain", "listings" ); ?><br><select name="drivetrain" class="css-dropdowns"
									                                                    data-update="false">
										<option value="<?php _e( "2WD", "listings" ); ?>"><?php _e( "2WD", "listings" ); ?></option>
										<option value="<?php _e( "4WD", "listings" ); ?>"><?php _e( "4WD", "listings" ); ?></option>
										<option value="<?php _e( "AWD", "listings" ); ?>"><?php _e( "AWD", "listings" ); ?></option>
									</select>
								</div>

							</div>
						</div>
					</div>

					<div class="col-md-6">
						<h4><?php _e( "Vehicle Rating", "listings" ); ?></h4>

						<div class="">
							<div class="row">

								<div class="col-md-6">
									<?php _e( "Body (dents, dings, rust, rot, damage)", "listings" ); ?><br><select
										name="body_rating" class="css-dropdowns" data-update="false">
										<option value="10">10 - <?php _e( "best", "listings" ); ?></option>
										<option value="9">9</option>
										<option value="8">8</option>
										<option value="7">7</option>
										<option value="6">6</option>
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1 - <?php _e( "worst", "listings" ); ?></option>
									</select><br>
									<?php _e( "Engine (running condition, burns oil, knocking)", "listings" ); ?><br><select
										name="engine_rating" class="css-dropdowns" data-update="false">
										<option value="10">10 - <?php _e( "best", "listings" ); ?></option>
										<option value="9">9</option>
										<option value="8">8</option>
										<option value="7">7</option>
										<option value="6">6</option>
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1 - <?php _e( "worst", "listings" ); ?></option>
									</select><br>
									<?php _e( "Glass (chips, scratches, cracks, pitted)", "listings" ); ?><br><select
										name="glass_rating" class="css-dropdowns" data-update="false">
										<option value="10">10 - <?php _e( "best", "listings" ); ?></option>
										<option value="9">9</option>
										<option value="8">8</option>
										<option value="7">7</option>
										<option value="6">6</option>
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1 - worst</option>
									</select><br>
									<?php _e( "Exhaust (rusted, leaking, noisy)", "listings" ); ?><br><select
										name="exhaust_rating" class="css-dropdowns" data-update="false">
										<option value="10">10 - <?php _e( "best", "listings" ); ?></option>
										<option value="9">9</option>
										<option value="8">8</option>
										<option value="7">7</option>
										<option value="6">6</option>
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1 - <?php _e( "worst", "listings" ); ?></option>
									</select>
								</div>

								<div class="col-md-6">
									<?php _e( "Tires (tread wear, mismatched)", "listings" ); ?><br><select name="tire_rating"
									                                                                        class="css-dropdowns"
									                                                                        data-update="false">
										<option value="10">10 - <?php _e( "best", "listings" ); ?></option>
										<option value="9">9</option>
										<option value="8">8</option>
										<option value="7">7</option>
										<option value="6">6</option>
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1 - <?php _e( "worst", "listings" ); ?></option>
									</select><br>
									<?php _e( "Transmission / Clutch (slipping, hard shift, grinds)", "listings" ); ?><br><select
										name="transmission_rating" class="css-dropdowns" data-update="false">
										<option value="10">10 - <?php _e( "best", "listings" ); ?></option>
										<option value="9">9</option>
										<option value="8">8</option>
										<option value="7">7</option>
										<option value="6">6</option>
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1 - <?php _e( "worst", "listings" ); ?></option>
									</select><br>
									<?php _e( "Interior (rips, tears, burns, faded/worn, stains)", "listings" ); ?><br><select
										name="interior_rating" class="css-dropdowns" data-update="false">
										<option value="10">10 - <?php _e( "best", "listings" ); ?></option>
										<option value="9">9</option>
										<option value="8">8</option>
										<option value="7">7</option>
										<option value="6">6</option>
										<option value="5">5</option>
										<option value="4">4</option>
										<option value="3">3</option>
										<option value="2">2</option>
										<option value="1">1 - <?php _e( "worst", "listings" ); ?></option>
									</select>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="container">
				<div class="row">
					<div class="col-md-6">
						<h4><?php _e( "Vehicle History", "listings" ); ?></h4>

						<?php _e( "Was it ever a lease or rental return?", "listings" ); ?> <br><select
							name="rental_return" class="css-dropdowns" data-update="false">
							<option value="<?php _e( "Yes", "listings" ); ?>"><?php _e( "Yes", "listings" ); ?></option>
							<option value="<?php _e( "No", "listings" ); ?>"><?php _e( "No", "listings" ); ?></option>
						</select><br>

						<?php _e( "Is the odometer operational and accurate?", "listings" ); ?> <br><select
							name="odometer_accurate" class="css-dropdowns" data-update="false">
							<option value="<?php _e( "Yes", "listings" ); ?>"><?php _e( "Yes", "listings" ); ?></option>
							<option value="<?php _e( "No", "listings" ); ?>"><?php _e( "No", "listings" ); ?></option>
						</select><br>

						<?php _e( "Detailed service records available?", "listings" ); ?> <br><select
							name="service_records" class="css-dropdowns" data-update="false">
							<option value="<?php _e( "Yes", "listings" ); ?>"><?php _e( "Yes", "listings" ); ?></option>
							<option value="<?php _e( "No", "listings" ); ?>"><?php _e( "No", "listings" ); ?></option>
						</select><br>
					</div>
					<div class="col-md-6">
						<h4><?php _e( "Title History", "listings" ); ?></h4>

						<?php _e( "Is there a lienholder?", "listings" ); ?> <br><input type="text" name="lienholder"><br>

						<?php _e( "Who holds this title?", "listings" ); ?> <br><input type="text" name="titleholder">
					</div>
				</div>
			</div>


			<div class="container">
				<div class="row">
					<div class="col-lg-12">
						<h4><?php _e( "Vehicle Assessment", "listings" ); ?></h4>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">

						<?php _e( "Does all equipment and accessories work correctly?", "listings" ); ?><br><textarea
							name="equipment" rows="5" style="width: 89%;"></textarea><br>

						<?php _e( "Did you buy the vehicle new?", "listings" ); ?><br><textarea name="vehiclenew"
						                                                                        rows="5"
						                                                                        style="width: 89%;"></textarea><br>

						<?php _e( "Has the vehicle ever been in any accidents? Cost of repairs?", "listings" ); ?>
						<br><textarea name="accidents" rows="5" style="width: 89%;"></textarea>
					</div>

					<div class="col-md-6">
						<?php _e( "Is there existing damage on the vehicle? Where?", "listings" ); ?><br><textarea
							name="damage" rows="5" style="width: 89%;"></textarea><br>

						<?php _e( "Has the vehicle ever had paint work performed?", "listings" ); ?><br><textarea
							name="paint" rows="5" style="width: 89%;"></textarea><br>

						<?php _e( "Is the title designated 'Salvage' or 'Reconstructed'? Any other?", "listings" ); ?>
						<br><textarea name="salvage" rows="5" style="width: 89%;"></textarea>
					</div>
				</div>
			</div>
			<?php

			if ( $lwp_options['recaptcha_enabled'] == 1 && isset( $lwp_options['recaptcha_public_key'] ) && ! empty( $lwp_options['recaptcha_public_key'] ) ) {
				echo __( "reCAPTCHA", "listings" ) . ": <br><div id='trade_fancybox_form_recaptcha' class='recaptcha_holder' style=\"transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;\"></div>";
			}

			?>

			<input type="submit" value="<?php _e( "Submit", "listings" ); ?>"> <i
				class="fa fa-refresh fa-spin loading_icon_form"></i>

		</form>
	<?php } else {
		echo do_shortcode( $lwp_options['tradein_form_shortcode'] );
	} ?>
</div>

<div id="offer_fancybox_form" class="" style="display: none">
	<?php if ( ! isset( $lwp_options['make_offer_form_shortcode'] ) || empty( $lwp_options['make_offer_form_shortcode'] ) ) { ?>
		<h3><?php _e( "Make an Offer", "listings" ); ?></h3>

		<form name="make_offer" method="post" class="ajax_form">
			<table>
				<tr>
					<td><?php _e( "Name", "listings" ); ?>:</td>
					<td><input type="text" name="name"></td>
				</tr>
				<tr>
					<td><?php _e( "Preferred Contact", "listings" ); ?>:</td>
					<td><span class="styled_input"> <input type="radio" name="contact_method"
					                                       value="<?php _e( "email", "listings" ); ?>" id="offer_email"><label
								for="offer_email"><?php _e( "Email", "listings" ); ?></label>  <input type="radio"
					                                                                                  name="contact_method"
					                                                                                  value="<?php _e( "phone", "listings" ); ?>"
					                                                                                  id="offer_phone"> <label
								for="offer_phone"><?php _e( "Phone", "listings" ); ?></label> </span></td>
				</tr>
				<tr>
					<td><?php _e( "Email", "listings" ); ?>:</td>
					<td><input type="text" name="email"></td>
				</tr>
				<tr>
					<td><?php _e( "Phone", "listings" ); ?>:</td>
					<td><input type="text" name="phone"></td>
				</tr>
				<tr>
					<td><?php _e( "Offered Price", "listings" ); ?>:</td>
					<td><input type="text" name="offered_price"></td>
				</tr>
				<tr>
					<td><?php _e( "Financing Required", "listings" ); ?>:</td>
					<td><select name="financing_required" class="css-dropdowns" data-update="false">
							<option value="<?php _e( "Yes", "listings" ); ?>"><?php _e( "Yes", "listings" ); ?></option>
							<option value="<?php _e( "No", "listings" ); ?>"><?php _e( "No", "listings" ); ?></option>
						</select></td>
				</tr>
				<tr>
					<td colspan="2"><?php _e( "Other Comments/Conditions", "listings" ); ?>:<br>
						<textarea name="other_comments" class="fancybox_textarea"></textarea></td>
				</tr>
				<?php

				if ( $lwp_options['recaptcha_enabled'] == 1 && isset( $lwp_options['recaptcha_public_key'] ) && ! empty( $lwp_options['recaptcha_public_key'] ) ) {
					echo "<tr><td colspan='2'>" . __( "reCAPTCHA", "listings" ) . ": <br><div id='offer_fancybox_form_recaptcha' class='recaptcha_holder' style=\"transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;\"></div></td></tr>";
				}

				?>
				<tr>
					<td colspan="2"><input type="submit" value="<?php _e( "Submit", "listings" ); ?>"> <i
							class="fa fa-refresh fa-spin loading_icon_form"></i></td>
				</tr>
			</table>
		</form>
	<?php } else {
		echo do_shortcode( $lwp_options['make_offer_form_shortcode'] );
	} ?>
</div>

<div id="schedule_fancybox_form" class="" style="display: none">
	<?php if ( ! isset( $lwp_options['schedule_test_drive_form_shortcode'] ) || empty( $lwp_options['schedule_test_drive_form_shortcode'] ) ) { ?>
		<h3><?php _e( "Schedule Test Drive", "listings" ); ?></h3>

		<form name="schedule" method="post" class="ajax_form">
			<table>
				<tr>
					<td><?php _e( "Name", "listings" ); ?>:</td>
					<td><input type="text" name="name"></td>
				</tr>
				<tr>
					<td><?php _e( "Preferred Contact", "listings" ); ?>:</td>
					<td><span class="styled_input"> <input type="radio" name="contact_method"
					                                       value="<?php _e( "Email", "listings" ); ?>"
					                                       id="schedule_email"><label
								for="schedule_email"><?php _e( "Email", "listings" ); ?></label>  <input type="radio"
					                                                                                     name="contact_method"
					                                                                                     value="<?php _e( "Phone", "listings" ); ?>"
					                                                                                     id="schedule_phone"> <label
								for="schedule_phone"><?php _e( "Phone", "listings" ); ?></label> </span></td>
				</tr>
				<tr>
					<td><?php _e( "Email", "listings" ); ?>:</td>
					<td><input type="text" name="email"></td>
				</tr>
				<tr>
					<td><?php _e( "Phone", "listings" ); ?>:</td>
					<td><input type="text" name="phone"></td>
				</tr>
				<tr>
					<td><?php _e( "Best Day", "listings" ); ?>:</td>
					<td><input type="text" name="best_day"></td>
				</tr>
				<tr>
					<td><?php _e( "Best Time", "listings" ); ?>:</td>
					<td><input type="text" name="best_time"></td>
				</tr>
				<?php

				if ( $lwp_options['recaptcha_enabled'] == 1 && isset( $lwp_options['recaptcha_public_key'] ) && ! empty( $lwp_options['recaptcha_public_key'] ) ) {
					echo "<tr><td colspan='2'>" . __( "reCAPTCHA", "listings" ) . ": <br><div id='schedule_fancybox_form_recaptcha' class='recaptcha_holder' style=\"transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;\"></div></td></tr>";
				}

				?>
				<tr>
					<td colspan="2"><input type="submit" value="<?php _e( "Submit", "listings" ); ?>"> <i
							class="fa fa-refresh fa-spin loading_icon_form"></i></td>
				</tr>
			</table>
		</form>
	<?php } else {
		echo do_shortcode( $lwp_options['schedule_test_drive_form_shortcode'] );
	} ?>
</div>

<div id="request_fancybox_form" class="" style="display: none">
	<?php if ( ! isset( $lwp_options['request_info_form_shortcode'] ) || empty( $lwp_options['request_info_form_shortcode'] ) ) { ?>
		<h3><?php _e( "Request More Info", "listings" ); ?></h3>

		<form name="request_info" method="post" class="ajax_form">
			<table>
				<tr>
					<td><?php _e( "Name", "listings" ); ?>:</td>
					<td><input type="text" name="name"></td>
				</tr>
				<tr>
					<td><?php _e( "Preferred Contact", "listings" ); ?>:</td>
					<td><span class="styled_input"><input type="radio" name="contact_method"
					                                      value="<?php _e( "Email", "listings" ); ?>"
					                                      id="request_more_email"><label
								for="request_more_email"><?php _e( "Email", "listings" ); ?></label>  <input
								type="radio" name="contact_method" value="<?php _e( "Phone", "listings" ); ?>"
								id="request_more_phone"> <label
								for="request_more_phone"><?php _e( "Phone", "listings" ); ?></label></span></td>
				</tr>
				<tr>
					<td><?php _e( "Email", "listings" ); ?>:</td>
					<td><input type="text" name="email"></td>
				</tr>
				<tr>
					<td><?php _e( "Phone", "listings" ); ?>:</td>
					<td><input type="text" name="phone"></td>
				</tr>
				<tr>
					<td colspan="2"><?php _e("Questions/Comments", "listings"); ?>:<br>
						<textarea name="comments" class="fancybox_textarea"></textarea></td>
				</tr>
				<?php

				if ( $lwp_options['recaptcha_enabled'] == 1 && isset( $lwp_options['recaptcha_public_key'] ) && ! empty( $lwp_options['recaptcha_public_key'] ) ) {
					echo "<tr><td colspan='2'>" . __( "reCAPTCHA", "listings" ) . ": <br><div id='request_fancybox_form_recaptcha' class='recaptcha_holder' style=\"transform:scale(0.77);-webkit-transform:scale(0.77);transform-origin:0 0;-webkit-transform-origin:0 0;\"></div></td></tr>";
				}

				?>
				<tr>
					<td colspan="2"><input type="submit" value="<?php _e( "Submit", "listings" ); ?>"> <i
							class="fa fa-refresh fa-spin loading_icon_form"></i></td>
				</tr>
			</table>
		</form>
	<?php } else {
		echo do_shortcode( $lwp_options['request_info_form_shortcode'] );
	} ?>
</div>