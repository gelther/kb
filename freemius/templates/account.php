<?php
	/**
	 * @package     Freemius
	 * @copyright   Copyright (c) 2015, Freemius, Inc.
	 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 * @since       1.0.3
	 */

	$slug = $VARS['slug'];
	/**
	 * @var Freemius $fs
	 */
	$fs = fs($slug);

	/**
	 * @var FS_Plugin_Tag $update
	 */
	$update = $fs->get_update();

	$is_paying = $fs->is_paying();
	$user = $fs->get_user();
	$site = $fs->get_site();
	$name = $user->get_name();
	$license = $fs->_get_license();
	$subscription = $fs->_get_subscription();
	$plan = $fs->get_plan();
	$is_active_subscription = (is_object($subscription) && $subscription->is_active());
?>

	<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<a href="<?php $fs->get_account_url() ?>" class="nav-tab nav-tab-active"><?php _e('Account', 'freemius') ?></a>
		<?php if ( $fs->_has_addons() ) : ?>
			<a href="<?php echo $fs->_get_admin_page_url('addons') ?>" class="nav-tab"><?php _e('Add Ons', 'freemius') ?></a>
		<?php endif ?>
		<?php if ($fs->is_not_paying() && $fs->has_paid_plan()) : ?>
			<a href="<?php echo $fs->get_upgrade_url() ?>" class="nav-tab"><?php _e('Upgrade', 'freemius') ?></a>
			<?php if (!$fs->is_trial_utilized() && $fs->has_trial_plan()) : ?>
				<a href="<?php echo $fs->get_trial_url() ?>" class="nav-tab"><?php _e('Free Trial', 'freemius') ?></a>
			<?php endif ?>
		<?php endif ?>
	</h2>
	<div id="poststuff">
	<div id="fs_account">
	<div class="has-sidebar has-right-sidebar">
	<div class="has-sidebar-content">
	<div class="postbox">
		<h3><?php _e('Account Details', 'freemius') ?></h3>
		<div class="fs-header-actions">
			<ul>
				<li>
					<form action="<?php echo $fs->_get_admin_page_url('account') ?>" method="POST">
						<input type="hidden" name="fs_action" value="delete_account">
						<?php wp_nonce_field('delete_account') ?>
						<a href="#" onclick="if (confirm('<?php
							if ($is_active_subscription) {
								echo esc_attr(sprintf( __( 'Deleting the account will automatically deactivate your %s plan license so you can use it on other sites. If you want to terminate the recurring payments as well, click the "Cancel" button, and first "Downgrade" your account. Are you sure you would like to continue with the deletion?', 'freemius' ), $plan->title ));
							}else {
								_e( 'Deletion is not temporary. Only delete if you no longer want to use this plugin anymore. Are you sure you would like to continue with the deletion?', 'freemius' );
							}
							?>'))  this.parentNode.submit(); return false;"><?php _e('Delete Account', 'freemius') ?></a>
					</form>
				</li>
				<?php if ($is_paying) : ?>
					<li>
						&nbsp;•&nbsp;
						<form action="<?php echo $fs->_get_admin_page_url('account') ?>" method="POST">
							<input type="hidden" name="fs_action" value="deactivate_license">
							<?php wp_nonce_field('deactivate_license') ?>
							<a href="#" onclick="if (confirm('<?php _e('Deactivating your license will block all premium features, but will enable you to activate the license on another site. Are you sure you want to proceed?', 'freemius') ?>')) this.parentNode.submit(); return false;"><?php _e('Deactivate License', 'freemius') ?></a>
						</form>
					</li>
					<?php if (!$license->is_lifetime() &&
					          $is_active_subscription) : ?>
					<li>
						&nbsp;•&nbsp;
						<form action="<?php echo $fs->_get_admin_page_url('account') ?>" method="POST">
							<input type="hidden" name="fs_action" value="downgrade_account">
							<?php wp_nonce_field('downgrade_account') ?>
							<a href="#" onclick="if (confirm('<?php printf(__('Downgrading your plan will immediately stop all future recurring payments and your %s plan license will expire in %s.', 'freemius'), $plan->title, human_time_diff( time(), strtotime( $license->expiration ) )) ?> <?php if (!$license->is_block_features) {
								printf(__( 'You can still enjoy all %s features but you will not have access to plugin updates and support.', 'freemius' ), $plan->title);
							}else {
								printf(__( 'Once your license expire you can still use the Free version but you will NOT have access to the %s features.', 'freemius'), $plan->title);
							}?> <?php _e(' Are you sure you want to proceed?', 'freemius') ?>')) this.parentNode.submit(); return false;"><?php _e('Downgrade', 'freemius') ?></a>
						</form>
					</li>
					<?php endif ?>
					<li>
						&nbsp;•&nbsp;
						<a href="<?php echo $fs->get_upgrade_url() ?>"><?php _e('Change Plan', 'freemius') ?></a>
					</li>
				<?php endif ?>
			</ul>
		</div>
		<div class="inside">
			<table id="fs_account_details" cellspacing="0" class="fs-key-value-table">
				<?php
					$profile = array();
					$profile[] = array('id' => 'user_name', 'title' => __('Name', 'freemius'), 'value' => $name);
//					if (isset($user->email) && false !== strpos($user->email, '@'))
						$profile[] = array('id' => 'email', 'title' => __('Email', 'freemius'), 'value' => $user->email);
					if (is_numeric($user->id))
						$profile[] = array('id' => 'user_id', 'title' => __('User ID', 'freemius'), 'value' => $user->id);

					$profile[] = array('id' => 'site_id', 'title' => __('Site ID', 'freemius'), 'value' => is_string($site->id) ? $site->id : 'No ID');

					$profile[] = array('id' => 'site_public_key', 'title' => __('Public Key', 'freemius'), 'value' => $site->public_key);

					$profile[] = array('id' => 'site_secret_key', 'title' => __('Secret Key', 'freemius'), 'value' => ((is_string($site->secret_key)) ? $site->secret_key : __('No Secret', 'freemius')));

					if ($fs->is_trial()){
						$trial_plan = $fs->get_trial_plan();

						$profile[] = array( 'id'    => 'plan',
						                    'title' => __( 'Plan', 'freemius' ),
						                    'value' => (is_string( $trial_plan->name ) ?
							                    strtoupper( $trial_plan->title ) . ' ' :
							                    '') . __('TRIAL', 'freemius')
						);
					}else {
						$profile[] = array( 'id'    => 'plan',
						                    'title' => __( 'Plan', 'freemius' ),
						                    'value' => is_string( $site->plan->name ) ?
							                    strtoupper( $site->plan->title ) :
							                    __('FREE', 'freemius')
						);
					}

					$profile[] = array('id' => 'version', 'title' => __('Version', 'freemius'), 'value' => $fs->get_plugin_version());
				?>
				<?php $odd = true; foreach ($profile as $p) : ?>
					<?php
					if ('plan' === $p['id'] && !$fs->has_paid_plan()) {
						// If plugin don't have any paid plans, there's no reason
						// to show current plan.
						continue;
					}
					?>
					<tr class="fs-field-<?php echo $p['id'] ?><?php if ($odd) :?> alternate<?php endif ?>">
						<td>
							<nobr><?php echo $p['title'] ?>:</nobr>
						</td>
						<td>
							<code><?php echo htmlspecialchars($p['value']) ?></code>
							<?php if ('email' === $p['id'] && !$user->is_verified()) : ?>
								<label><?php _e('not verified', 'freemius') ?></label>
							<?php endif ?>
							<?php if ( 'plan' === $p['id'] ) : ?>
								<?php if ($fs->is_trial()) : ?>
								<label><?php printf( __('Expires in %s', 'freemius'), human_time_diff( time(), strtotime( $site->trial_ends ) )) ?></label>
								<?php elseif (is_object($license) && !$license->is_lifetime()) : ?>
									<?php if (!$is_active_subscription && !$license->is_first_payment_pending()) : ?>
									<label><?php printf( __('Expires in %s', 'freemius'), human_time_diff( time(), strtotime( $license->expiration ) )) ?></label>
									<?php elseif ($is_active_subscription && !$subscription->is_first_payment_pending()) : ?>
										<label><?php printf( __('Auto renews in %s', 'freemius'), human_time_diff( time(), strtotime( $subscription->next_payment ) )) ?></label>
									<?php endif ?>
								<?php endif ?>
							<?php endif ?>

						</td>
						<td class="fs-right">
							<?php if ('email' === $p['id'] && !$user->is_verified()) : ?>
								<form action="<?php echo $fs->_get_admin_page_url('account') ?>" method="POST">
									<input type="hidden" name="fs_action" value="verify_email">
									<?php wp_nonce_field('verify_email') ?>
									<input type="submit" class="button button-small" value="<?php _e('Verify Email', 'freemius') ?>">
								</form>
							<?php endif ?>
							<?php if ('plan' === $p['id']) : ?>
								<div class="button-group">
										<?php $license = $fs->is_not_paying() ? $fs->_get_available_premium_license() : false ?>
										<?php if (false !== $license && ($license->left() > 0 || ($site->is_localhost() && $license->is_free_localhost))) : ?>
											<?php $premium_plan = $fs->_get_plan_by_id($license->plan_id) ?>
											<form action="<?php echo $fs->_get_admin_page_url('account') ?>" method="POST">
												<input type="hidden" name="fs_action" value="activate_license">
												<?php wp_nonce_field('activate_license') ?>
												<input type="submit" class="button button-primary" value="<?php printf( __('Activate %s Plan', 'freemius'), $premium_plan->title, ($site->is_localhost() && $license->is_free_localhost) ? '[localhost]' : (1 < $license->left() ? $license->left() . ' left' : '' )) ?> ">
											</form>
										<?php else : ?>
											<form action="<?php echo $fs->_get_admin_page_url('account') ?>" method="POST" class="button-group">
												<input type="submit" class="button" value="<?php _e('Sync License', 'freemius') ?>">
												<input type="hidden" name="fs_action" value="<?php echo $slug ?>_sync_license">
												<?php wp_nonce_field($slug . '_sync_license') ?>
												<a href="<?php echo $fs->get_upgrade_url() ?>" class="button<?php if (!$is_paying) echo ' button-primary' ?> button-upgrade"><?php (!$is_paying) ? _ex('Upgrade', 'verb', 'freemius') : _e('Change Plan', 'freemius') ?></a>
											</form>
										<?php endif ?>
								</div>
							<?php elseif ('version' === $p['id']) : ?>
								<div class="button-group">
									<?php if ( $is_paying ) : ?>
										<?php if (!$fs->is_allowed_to_install()) : ?>
											<a target="_blank" class="button button-primary" href="<?php echo $fs->_get_latest_download_local_url() ?>"><?php echo sprintf( __('Download %1s Version', 'freemius'), $site->plan->title) . (is_object($update) ? ' [' . $update->version . ']' : '') ?></a>
										<?php elseif ( is_object($update) ) : ?>
											<a class="button button-primary" href="<?php echo wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $fs->get_plugin_basename()), 'upgrade-plugin_' . $fs->get_plugin_basename()) ?>"><?php echo sprintf( __('Install Update Now [%1s]', 'freemius'), $update->version ) ?></a>
										<?php endif ?>
									<?php endif; ?>
								</div>
							<?php elseif (/*in_array($p['id'], array('site_secret_key', 'site_id', 'site_public_key')) ||*/ (is_string($user->secret_key) && in_array($p['id'], array('email', 'user_name'))) ) : ?>
								<form action="<?php echo $fs->_get_admin_page_url('account') ?>" method="POST" onsubmit="var val = prompt('<?php printf( __('What is your %s?', 'freemius'), $p['title'] ) ?>', '<?php echo $p['value'] ?>'); if (null == val || '' === val) return false; jQuery('input[name=fs_<?php echo $p['id'] ?>_<?php echo $slug ?>]').val(val); return true;">
									<input type="hidden" name="fs_action" value="update_<?php echo $p['id'] ?>">
									<input type="hidden" name="fs_<?php echo $p['id'] ?>_<?php echo $slug ?>" value="">
									<?php wp_nonce_field('update_' . $p['id']) ?>
									<input type="submit" class="button button-small" value="<?php _ex('Edit', 'verb', 'freemius') ?>">
								</form>
							<?php endif ?>
						</td>
					</tr>
					<?php $odd = !$odd; endforeach ?>
			</table>
		</div>
	</div>
	<?php
		$account_addons = $fs->get_account_addons();
		if (!is_array($account_addons))
			$account_addons = array();

		$installed_addons = $fs->get_installed_addons();
		$installed_addons_ids = array();
		foreach ($installed_addons as $fs_addon)
		{
			$installed_addons_ids[] = $fs_addon->get_id();
		}

		$addons_to_show = array_unique(array_merge($installed_addons_ids, $account_addons));
	?>
	<?php if (0 < count($addons_to_show)) : ?>
	<div class="postbox">
		<table id="fs_addons" class="widefat">
			<thead>
			<tr>
				<th></th>
				<th><?php _ex('Version', 'plugin version', 'freemius') ?></th>
				<th><?php _e('Plan', 'freemius') ?></th>
				<th><?php _ex('Expiration', 'expiration date', 'freemius') ?></th>
				<th></th>
				<?php if (defined('WP_FS__DEV_MODE') && WP_FS__DEV_MODE) : ?>
					<th></th>
				<?php endif ?>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($addons_to_show as $addon_id) : ?>
				<?php
				$addon = $fs->get_addon($addon_id);
				$is_addon_activated = $fs->is_addon_activated($addon->slug);

				$fs_addon = $is_addon_activated ? fs($addon->slug) : false;
				?>
				<tr>
					<td>
						<?php echo $addon->title ?>
					</td>
					<?php if ($is_addon_activated) : ?>
						<?php // Add-on Installed ?>
						<?php $addon_site = $fs_addon->get_site(); ?>
						<td><?php echo $fs_addon->get_plugin_version() ?></td>
						<td><?php echo is_string($addon_site->plan->name) ? strtoupper($addon_site->plan->title) : 'FREE' ?></td>
						<?php
						$current_license = $fs_addon->_get_license();
						$is_current_license_expired = is_object($current_license) && $current_license->is_expired();
						?>
						<?php if ( $fs_addon->is_not_paying() ) : ?>
							<?php if ($is_current_license_expired) : ?>
								<td><?php _e('Expired', 'freemius') ?></td>
							<?php endif ?>
							<?php $premium_license = $fs_addon->_get_available_premium_license() ?>
							<td<?php if (!$is_current_license_expired) echo ' colspan="2"' ?>>
								<?php if (is_object($premium_license) && !$premium_license->is_utilized()) : ?>
									<?php $site = $fs_addon->get_site() ?>
									<?php fs_ui_action_button(
										$slug, 'account',
										'activate_license',
										sprintf( __('Activate %s Plan', 'freemius'), $fs_addon->get_plan_title(), ($site->is_localhost() && $premium_license->is_free_localhost) ? '[localhost]' : (1 < $premium_license->left() ? $premium_license->left() . ' left' : '' )),
										array('plugin_id' => $addon_id)
									) ?>
								<?php else : ?>
									<div class="button-group">
										<?php fs_ui_action_button(
											$slug, 'account',
											$slug . '_sync_license',
											__('Sync License', 'freemius'),
											array('plugin_id' => $addon_id),
											false
										) ?>
										<?php echo sprintf( '<a href="%s" class="thickbox button button-primary" aria-label="%s" data-title="%s">%s</a>',
											esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&parent_plugin_id=' . $fs->get_id() . '&plugin=' . $addon->slug .
											                            '&TB_iframe=true&width=600&height=550' ) ),
											esc_attr( sprintf( __( 'More information about %s' ), $addon->title ) ),
											esc_attr( $addon->title ),
											_x('Upgrade', 'verb', 'freemius')
										); ?>
									</div>
								<?php endif ?>
							</td>
						<?php else : ?>
							<?php if (is_object($current_license)) : ?>
								<td><?php
										if ($current_license->is_lifetime()){
											_e('No expiration', 'freemius');
										} else if ($current_license->is_expired()) {
											_e('Expired', 'freemius');
										} else {
											echo sprintf(
												__('In %s', 'freemius'),
												human_time_diff( time(), strtotime( $current_license->expiration ) )
											);
										}
									?></td>
								<td>
									<?php fs_ui_action_button(
										$slug, 'account',
										'deactivate_license',
										__('Deactivate License', 'freemius'),
										array('plugin_id' => $addon_id),
										false
									) ?>
								</td>
							<?php endif ?>
						<?php endif ?>
					<?php else : ?>
						<?php // Add-on NOT Installed
						?>
						<td colspan="4">
							<?php if ($fs->is_addon_installed($addon->slug)) : ?>
								<?php $addon_file = $fs->get_addon_basename($addon->slug) ?>
								<a class="button button-primary" href="<?php echo wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $addon_file, 'activate-plugin_' . $addon_file) ?>" title="<?php esc_attr__('Activate this add-on') ?>" class="edit"><?php _e('Activate', 'freemius') ?></a>
							<?php else : ?>
								<?php if ($fs->is_allowed_to_install()) : ?>
									<a class="button button-primary" href="<?php echo wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . $addon->slug), 'install-plugin_' . $addon->slug) ?>"><?php _e('Install Now', 'freemius') ?></a>
								<?php else : ?>
									<a target="_blank" class="button button-primary" href="<?php echo $fs->_get_latest_download_local_url($addon_id) ?>"><?php echo __('Download Latest', 'freemius') ?></a>
								<?php endif ?>
							<?php endif ?>
						</td>
					<?php endif ?>
					<?php if (defined('WP_FS__DEV_MODE') && WP_FS__DEV_MODE) : ?>
						<td>
							<?php
								if ($is_addon_activated)
									fs_ui_action_button(
										$slug, 'account',
										'delete_account',
										_x('Delete', 'verb', WP_FS__SLUG),
										array('plugin_id' => $addon_id),
										false
									);
							?>
						</td>
					<?php endif ?>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<?php endif ?>

	<?php $fs->do_action( 'after_account_details' ) ?>
	</div>
	</div>
	</div>
	</div>
	</div>
<?php fs_require_template('powered-by.php') ?>