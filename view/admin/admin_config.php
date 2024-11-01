<?php
/**
 * Admin config view.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-hyves
 */
$err = false;
global $wpHyvesConsumerKey;
global $wpHyvesConsumerSecret;
?>
<div class="wrap">
	<div id="icon-hyves" class="icon32">
	<img src="<?php bloginfo ('wpurl') ?>/wp-content/plugins/wp-hyves/resources/images/icon_32.png"
	alt="hyves logo" /></div>
	<h2><?php _e('WP-Hyves Options','wp-hyves');?></h2>
	<p>
		<ul>
			<?php if(!function_exists('curl_init')): ?>
			<li><?php _e('CURL extension not installed:','wp-hyves'); ?></li>
			<li><?php _e('you must have CURL extension enabled in your php configuration','wp-hyves');?></li>
			<?php else: ?>
			<!-- found curl -->
			<?php endif; ?>

			<?php if(!is_writable(wpHyvesGetUploadPath(true))): $err = true; ?>
			<li><?php _e("Can't write to", 'wp-hyves');?> <?php echo wpHyvesGetUploadPath(true); ?></li>
			<li><?php _e('Make sure the directory is writable by:<br/> <pre>chmod 777 ', 'wp-hyves'); ?> <?php echo wpHyvesGetUploadPath(true); ?></li>
			<?php else: ?>
			<!-- cache is writable -->
			<?php endif; ?>

			<?php if($form->getKey() == '' && '' == $wpHyvesConsumerKey): ?>
			<li><?php _e('Get Hyves API Key here:', 'wp-hyves');?> <a href="http://www.hyves.nl/api/apply/" title="apply for hyves api key">http://www.hyves.nl/api/apply/</a></li>
			<?php else: ?>
			<!-- api key is set -->
			<?php endif; ?>
		</ul>
	</p>

	<?php if(!$err): ?>

	<?php if('' == $wpHyvesConsumerKey && '' == $wpHyvesConsumerSecret): ?>

	<form name="hyves_config_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" accept-charset="utf-8">
		<?= $form->htmlFormId(); ?>
		<table class="form-table" cellspacing="2" cellpadding="5" width="100%">
		<?php dl_load_admin_block('form-table-row',
			array(
			'input_key'=>'wphyves_key',
			'input_value'=>$form->getKey(),
			'input_description'=>__('Enter Consumer key', 'wp-hyves'),
			'label_name'=>__('Consumer Key', 'wp-hyves'))
		);
		?>
		<?php dl_load_admin_block('form-table-row',
			array(
			'input_key'=>'wphyves_secret',
			'input_value'=>$form->getSecret(),
			'input_description'=>__('Enter Consumer secret','wp-hyves'),
			'label_name'=>__('Consumer Secret', 'wp-hyves'))
		);
		?>
		</table>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes','wp-hyves'); ?>" class="button-primary" />
		</p>
	</form>
	<?php else: ?>
	<p>
		<strong>
			<?php _e('WP-Hyves is enabled', 'wp-hyves'); ?>.
		</strong>
	</p>
	<?php endif; ?>

	<?php if(isset($api) && '' != $form->getKey() && '' != $form->getSecret()) { $api->init(); } ?>

	<?php echo '<h3>' . __('Help', 'wp-hyves') . '</h3>'; ?>
	<p>
	<?php _e('If you cannot get this plugin to work <br/> please check your ip settings of the created Desktop Consumer at: <br/>', 'wp-hyves'); ?>
	 <a href="http://www.hyves.nl/api/apply/" title="apply for hyves api key">http://www.hyves.nl/api/apply/</a><br/>
	<strong><?php echo __('Copy IP: ') . '</strong>' . $_SERVER['SERVER_ADDR']; ?>
	</p>

	<?php endif; ?>
</div>

<?php include_once('blocks/footer.php'); ?>