<div class="wpallimport-collapsed">
	<div class="wpallimport-content-section">
		<div class="wpallimport-collapsed-header">			
			<h3><?php _e('User Account Info','wp_all_import_user_add_on');?></h3>	
		</div>		
		<div class="wpallimport-collapsed-content" style="padding: 0;">
			<div class="wpallimport-collapsed-content-inner wpallimport-user-data">
				<table class="form-table pmui-account-fields">
				<input type="hidden" name="pmui[import_users]" value="1"/>
					<tr class="pmui-half">
						<td class="pmui-half-left">
							<div class="input">
								<p style="margin-bottom:5px;"><?php _e('<b>First Name</b>', 'wp_all_import_user_add_on');?></p>
								<input name="pmui[first_name]" type="text" class="widefat rad4" style="width:100%;margin-bottom:5px;" value="<?php echo esc_attr($post['pmui']['first_name']) ?>"/>				
							</div>
						</td>
						<td class="pmui-half-right">
							<div class="input">
								<p style="margin-bottom:5px;"><?php _e('<b>Last Name</b>', 'wp_all_import_user_add_on');?></p>
								<input name="pmui[last_name]" type="text" class="widefat rad4" style="width:100%;margin-bottom:5px;" value="<?php echo esc_attr($post['pmui']['last_name']) ?>"/>				
							</div>
						</td>
					</tr>
					<tr class="pmui-half">
						<td class="pmui-half-left">
							<div class="input">
								<p style="margin-bottom:5px;">
								
								<?php

								if ( $post['wizard_type'] != 'matching' ) {
									_e('<b>Username (required)</b>', 'wp_all_import_user_add_on');
								} else {
									_e('<b>Username</b>', 'wp_all_import_user_add_on');
								}
								
								?>
								
								<a class="wpallimport-help" href="#help" title="<?php _e('A string that contains the user\'s username for logging in.', 'wp_all_import_user_add_on'); ?>">?</a></p>
								<input name="pmui[login]" type="text" class="widefat rad4" style="width:100%;margin-bottom:5px;" value="<?php echo esc_attr($post['pmui']['login']) ?>"/>				
							</div>
						</td>
						<td class="pmui-half-right">
							<div class="input">
								<p style="margin-bottom:5px;">

								<?php 

								if ( $post['wizard_type'] != 'matching' ) {
									_e('<b>Email (required)</b>', 'wp_all_import_user_add_on');
								} else {
									_e('<b>Email</b>', 'wp_all_import_user_add_on');
								}
								
								?>
								
								</p>
								<input name="pmui[email]" type="text" class="widefat rad4" style="width:100%;margin-bottom:5px;" value="<?php echo esc_attr($post['pmui']['email']); ?>"/>
							</div>
						</td>
					</tr>
					<tr class="pmui-full">
						<td colspan="2">
							<div class="input">
								<p style="margin-bottom:5px;"><?php _e('<b>Password</b>', 'wp_all_import_user_add_on');?></p>
								<input name="pmui[pass]" type="text" class="widefat rad4" style="width:100%;margin-bottom:5px;" value="<?php echo esc_attr($post['pmui']['pass']); ?>"/>
								<div class="input" style="margin:3px;">
									<input type="hidden" name="is_hashed_wordpress_password" value="0" />
									<input type="checkbox" id="is_hashed_wordpress_password" name="is_hashed_wordpress_password" value="1" <?php echo $post['is_hashed_wordpress_password'] ? 'checked="checked"' : '' ?> class="fix_checkbox"/>
									<label for="is_hashed_wordpress_password"><?php _e('This is a hashed password from another WordPress site','wp_all_import_plugin');?> </label>						
									<a href="#help" class="wpallimport-help" title="<?php _e('WordPress encrypts passwords and stores them as a hashed value in the database. Disable this feature if you are importing a plaintext password. Enable this feature if you are importing hashed passwords that were exported from another WordPress install.', 'wp_all_import_plugin') ?>" style="position: relative; top: 0px;">?</a>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>