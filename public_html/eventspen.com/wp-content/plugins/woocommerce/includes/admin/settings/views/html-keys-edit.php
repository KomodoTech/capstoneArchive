<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div id="key-fields" class="settings-panel">
	<h2><?php _e( 'Key details', 'woocommerce' ); ?></h2>

	<input type="hidden" id="key_id" value="<?php echo esc_attr( $key_id ); ?>" />

	<table id="api-keys-options" class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="key_description"><?php _e( 'Description', 'woocommerce' ); ?></label>
					<?php echo wc_help_tip( __( 'Friendly name for identifying this key.', 'woocommerce' ) ); ?>
				</th>
				<td class="forminp">
					<input id="key_description" type="text" class="input-text regular-input" value="<?php echo esc_attr( $key_data['description'] ); ?>" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="key_user"><?php _e( 'User', 'woocommerce' ); ?></label>
					<?php echo wc_help_tip( __( 'Owner of these keys.', 'woocommerce' ) ); ?>
				</th>
				<td class="forminp">
					<?php
						$curent_user_id = get_current_user_id();
						$user_id        = ! empty( $key_data['user_id'] ) ? absint( $key_data['user_id'] ) : $curent_user_id;
						$user           = get_user_by( 'id', $user_id );
						/* translators: 1: user display name 2: user ID 3: user email */
						$user_string    = sprintf(
							esc_html__( '%1$s (#%2$s &ndash; %3$s)', 'woocommerce' ),
							$user->display_name,
							absint( $user->ID ),
							$user->user_email
						);
					?>
					<input type="hidden" class="wc-customer-search" id="key_user" data-placeholder="<?php esc_attr_e( 'Search for a customer&hellip;', 'woocommerce' ); ?>" data-selected="<?php echo esc_attr( $user_string ); ?>" value="<?php echo esc_attr( $user_id ); ?>" data-allow_clear="true" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="key_permissions"><?php _e( 'Permissions', 'woocommerce' ); ?></label>
					<?php echo wc_help_tip( __( 'Select the access type of these keys.', 'woocommerce' ) ); ?>
				</th>
				<td class="forminp">
					<select id="key_permissions" class="wc-enhanced-select">
						<?php
							$permissions = array(
								'read'       => __( 'Read', 'woocommerce' ),
								'write'      => __( 'Write', 'woocommerce' ),
								'read_write' => __( 'Read/Write', 'woocommerce' ),
							);

							foreach ( $permissions as $permission_id => $permission_name ) : ?>
							<option value="<?php echo esc_attr( $permission_id ); ?>" <?php selected( $key_data['permissions'], $permission_id, true ); ?>><?php echo esc_html( $permission_name ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<?php if ( 0 !== $key_id ) : ?>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<?php _e( 'Consumer key ending in', 'woocommerce' ); ?>
					</th>
					<td class="forminp">
						<code>&hellip;<?php echo esc_html( $key_data['truncated_key'] ); ?></code>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<?php _e( 'Last access', 'woocommerce' ); ?>
					</th>
					<td class="forminp">
						<span><?php
							if ( ! empty( $key_data['last_access'] ) ) {
								/* translators: 1: last access date 2: last access time */
								$date = sprintf( __( '%1$s at %2$s', 'woocommerce' ), date_i18n( wc_date_format(), strtotime( $key_data['last_access'] ) ), date_i18n( wc_time_format(), strtotime( $key_data['last_access'] ) ) );

								echo apply_filters( 'woocommerce_api_key_last_access_datetime', $date, $key_data['last_access'] );
							} else {
								_e( 'Unknown', 'woocommerce' );
							}
						?></span>
					</td>
				</tr>
			<?php endif ?>
		</tbody>
	</table>

	<?php do_action( 'woocommerce_admin_key_fields', $key_data ); ?>

	<?php
		if ( 0 == $key_id ) {
			submit_button( __( 'Generate API key', 'woocommerce' ), 'primary', 'update_api_key' );
		} else {
			?>
			<p class="submit">
				<?php submit_button( __( 'Save changes', 'woocommerce' ), 'primary', 'update_api_key', false ); ?>
				<a style="color: #a00; text-decoration: none; margin-left: 10px;" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'revoke-key' => $key_id ), admin_url( 'admin.php?page=wc-settings&tab=api&section=keys' ) ), 'revoke' ) ); ?>"><?php _e( 'Revoke key', 'woocommerce' ); ?></a>
			</p>
			<?php
		}
	?>
</div>

<script type="text/template" id="tmpl-api-keys-template">
	<p id="copy-error"></p>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Consumer key', 'woocommerce' ); ?>
				</th>
				<td class="forminp">
					<input id="key_consumer_key" type="text" value="{{ data.consumer_key }}" size="55" readonly="readonly"> <button type="button" class="button-secondary copy-key" data-tip="<?php esc_attr_e( 'Copied!', 'woocommerce' ); ?>"><?php _e( 'Copy', 'woocommerce' ); ?></button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'Consumer secret', 'woocommerce' ); ?>
				</th>
				<td class="forminp">
					<input id="key_consumer_secret" type="text" value="{{ data.consumer_secret }}" size="55" readonly="readonly"> <button type="button" class="button-secondary copy-secret" data-tip="<?php esc_attr_e( 'Copied!', 'woocommerce' ); ?>"><?php _e( 'Copy', 'woocommerce' ); ?></button>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<?php _e( 'QRCode', 'woocommerce' ); ?>
				</th>
				<td class="forminp">
					<div id="keys-qrcode"></div>
				</td>
			</tr>
		</tbody>
	</table>
</script>
