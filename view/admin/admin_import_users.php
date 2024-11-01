<?php
/**
 * Import Users view.
 * @author dligthart
 * @version 0.1
 * @package wp-hyves
 */
?>
<div id="wrap" class="wrap">

<div id="icon-hyves" class="icon32">
<img src="<?php bloginfo ('wpurl') ?>/wp-content/plugins/wp-hyves/resources/images/icon_32.png"
	alt="hyves logo" /></div>
<h2><?php _e('Import Hyves', 'wp-hyves'); ?></h2>

<br/>
<form id="wphyves_import_friends_form"
	name="wphyves_import_friends_form" method="post"
	action="<?php echo wpHyvesGetScriptUrl().'&action=authorize_import_users'; ?>">
<input type="submit" name="Submit"
	value="<?php _e('Import Friends','wp-hyves'); ?>"
	class="button-primary" /></form>

<p><?php
$form = new WPHyvesAdminConfigForm();
if(isset($api) && '' != $form->getKey() && '' != $form->getSecret()) { $api->init(); }
?></p>

<?php
// Get current users.
$usersCollection = array();
$blogusers = get_users_of_blog();
$i = 0;
if(is_array($blogusers) && count($blogusers) > 0) {
	foreach($blogusers as $bu) {
		//print_r($bu);
		$id = get_usermeta($bu->user_id, 'hyves_userid');
		$fn = get_usermeta($bu->user_id, 'first_name');
		$ln = get_usermeta($bu->user_id, 'last_name');
		if($id != ''){ 

			$usersCollection[$bu->display_name]['firstname'] = $fn;
			$usersCollection[$bu->display_name]['lastname'] = $ln;
			$usersCollection[$bu->display_name]['username'] = $bu->display_name;
			$usersCollection[$bu->display_name]['img_html'] = '<a href="'. get_bloginfo('wpurl') .'/wp-admin/user-edit.php?user_id='.$bu->user_id.'" title="'. $fn . ' ' . $ln .' profile" />';
			$usersCollection[$bu->display_name]['img_html'] .= '<img src="'.wpHyvesGetUploadPath(false, true) . $id . '-medium.jpg" alt="'. $fn . ' ' . $ln . '" width="48" height="48" />';
			$usersCollection[$bu->display_name]['img_html'] .= '</a>&nbsp;';

			$i++;
		}
	}
}

dl_load_admin_block('list-users', array('users'=>$usersCollection));

?></div>
