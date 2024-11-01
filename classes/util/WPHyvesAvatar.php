<?php
/**
 * Avatars.
 * @author dligthart
 * @version 0.1
 * @package wp-hyves
 */
function wphyves_avatar($url, $email) {
	$result = $url;
	$user = get_user_by_email($email);
	if($user) {
		$id = $user->ID;
		$hyves_id = get_usermeta($id, 'hyves_userid');
		if('' != $hyves_id) {
			$url = wpHyvesGetUploadPath(false, true). $hyves_id . '-medium.jpg';
			$result = '<img class="avatar avatar-32 photo" height="32" width="32" src="'.$url.'" alt=""/>';
		}
	}
	return $result;

}
?>