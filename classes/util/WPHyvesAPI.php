<?php
/**
 * WPHyvesAPI. Connect to hyves.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.5
 * @package wp-hyves
 */
include_once(dirname(__FILE__) . '/GenusApis-PHP5-1.0.1/GenusApis.php');

include_once(dirname(__FILE__) . '/WPHyvesUtils.php');

define('HYVES_API_URL', 'http://data.hyves-api.nl');
define('HYVES_API_URL_BASE', 'http://www.hyves.nl/api/');
define('HYVES_API_URL_AUTH', 'http://www.hyves.nl/api/authorize/');
define("HA_VERSION", "1.2.1");

class WPHyvesAPI {
	/** @var api base url */
	var $hyves_api_base_url;
	/** @var api url**/
	var $hyves_api_url;
	/** @var hyves auth url */
	var $hyves_auth_url;
	/** @var hyves key **/
	var $hyves_cons_key;
	/** @var hyves secret **/
	var $hyves_cons_secret;
	/** @var consumer */
	var $oOAuthConsumer = null;
	/** @var wp-admin script url */
	var $script_url;
	/** @var api class */
	var $api = null;
	/** @var userid current logged in user */
	var $userid;
	/** @var local token public key */
	var $local_token;

	/**
	 * Constructor.
	 * @param string $key
	 * @param string $secret
	 */
	public function __construct($key, $secret) {
		$this->WPHyvesAPI($key, $secret);
	}

	/**
	 * Constructor.
	 * @param string $key
	 * @param string $secret
	 * @access public
	 */
	function WPHyvesAPI($key, $secret) {

		$this->hyves_cons_key = $key;

		$this->hyves_cons_secret = $secret;

		$this->hyves_api_url = HYVES_API_URL;

		$this->hyves_api_base_url = HYVES_API_URL_BASE;

		$this->hyves_auth_url = HYVES_API_URL_AUTH;

		$this->script_url = wpHyvesGetScriptUrl();

		$this->oOAuthConsumer = new OAuthConsumer($this->hyves_cons_key, $this->hyves_cons_secret);

		session_start(); //required by hyves api.

		// Set memory limit.
		ini_set('memory_limit','150M');
	}

	/**
	 * Authorize before headers are sent.
	 * @param array $args
	 * @access public
	 */
	function auth($args = array()) {
		extract($args, EXTR_SKIP);

		session_start();

		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "default";

		try {
			$this->api = new GenusApis($this->oOAuthConsumer, HA_VERSION);

			// Authorize by menu.
			if('authorize' == $action) {
				$this->authorize($this->script_url."&action=authorized",
				array('friends.get',
				'users.getScraps', 
				'users.getLoggedin', 
				'users.get', 
				'media.getAlbums', 
				'media.get', 
				'blogs.create', 
				'wwws.create'));
			}
			elseif('authorized' == $action && isset($_REQUEST['oauth_token'])) {
				// Authorized page, hyves will redirect to this page (callback).
				$oauth_token = $_REQUEST['oauth_token'];
				$oRequestToken = getRequestTokenFromSession($oauth_token);
				$oAccessToken = $this->api->retrieveAccesstoken($oRequestToken);
				$this->userid = $oAccessToken->getUserid();
				$this->local_token = md5($oAccessToken->getKey());
				$_SESSION['localtoken_'.$this->local_token] = serialize($oAccessToken);
			}
			elseif('authorize_and_post' == $action && '' != $_REQUEST['id']) { // Post to blog.
				$this->authorizeAndPost();
			}
			elseif('authorized_and_post' == $action && isset($_REQUEST['oauth_token']) && '' != $_REQUEST['id']) {
				$this->authorizedAndPost();
			}
			elseif('authorize_and_post_www' == $action) { // Post www.
				$this->authorizeAndPostWWW();
			}
			elseif('authorized_and_post_www' == $action) {
				$this->authorizedAndPostWWW();
			}
			elseif('authorize_import_users' == $action) {
				$this->authorizeAndImportUsers();
			}
			elseif('authorized_import_users' == $action) {
				$this->authorizedAndImportUsers();
			}
			elseif('authorize_and_get_wwws' == $action) {
				$this->authorizeAndImportWWWs();
			}
			elseif('authorized_and_get_wwws' == $action) {
				$this->authorizedAndImportWWWs();
			}
			elseif('authorize_and_get_tips' == $action) {
				$this->authorizeAndImportTips();
			}
			elseif('authorized_and_get_tips' == $action) {
				$this->authorizedAndImportTips();
			}
			elseif('authorize_and_get_scraps' == $action) {
				$this->authorizeAndGetScraps();
			}
			elseif('authorized_and_get_scraps' == $action) {
				$this->authorizedAndGetScraps();
			}
		}
		catch(GeneralException $e) {
			// Retry post if hyves delays.
			if($e->getCode() == 0 && 'authorize_and_post' == $action || 'authorized_and_post' == $action) {
				$this->authorizeAndPost();
			} // Retry post www.
			elseif($e->getCode() == 0 && 'authorize_and_post_www' == $action || 'authorized_and_post_www' == $action) {
				$this->authorizeAndPostWWW();
			} // Retry import users.
			elseif($e->getCode() == 0 && 'authorize_import_users' == $action || 'authorized_import_users' == $action) {
				$this->authorizeAndImportUsers();
			}
			elseif($e->getCode() == 0 && 'authorize_and_get_wwws' == $action || 'authorized_and_get_wwws' == $action) {
				$this->authorizeAndImportWWWs();
			}
			elseif($e->getCode() == 0 && 'authorize_and_get_tips' == $action || 'authorized_and_get_tips' == $action) {
				$this->authorizeAndImportTips();
			}elseif($e->getCode() == 0 && 'authorize_and_get_scraps' == $action || 'authorized_and_get_scraps' == $action) {
				$this->authorizeAndGetScraps();
			}
		}
		catch(HyvesApiException $e) {
			$this->err($e->getCode() . ' message: ' . $e->getMessage());
			//$this->err('Key: ' .$this->hyves_cons_key . ' Secret: ' . $this->hyves_cons_secret);
			exit;
		}
	}

	/**
	 * Authorize and post to blog.
	 * @access protected
	 */
	function authorizeAndPost() {
		$this->authorize(wpHyvesGetScriptUrl()."&action=authorized_and_post&id=" . $_REQUEST['id'], array('blogs.create'));
	}

	/**
	 * Authorized to post.
	 * @access protected
	 */
	function authorizedAndPost() {
		$postid = $_REQUEST['id'];
		$this->authorized(wpHyvesGetScriptUrl(). '&action=add_post&id=' . $postid);
	}

	/**
	 * Authorize to post www.
	 * @access protected
	 */
	function authorizeAndPostWWW() {
		$this->authorize(wpHyvesGetScriptUrl()."&action=authorized_and_post_www&what=" . $_REQUEST['what'] . '&where=' . $_REQUEST['where'], array('wwws.create'));
	}

	/**
	 * Authorized to post www.
	 * @access protected
	 */
	function authorizedAndPostWWW() {
		$what = $_REQUEST['what'];
		$where = $_REQUEST['where'];
		$this->authorized(wpHyvesGetScriptUrl(). '&action=add_www&what=' . $what . '&where=' . $where);
	}

	/**
	 * Authorize to get wwws.
	 */
	function authorizeAndImportWWWs() {
		$this->authorize($this->script_url . '&action=authorized_and_get_wwws', array('wwws.getByUser'));
	}

	/**
	 * Authorized and get wwws.
	 */
	function authorizedAndImportWWWs() {
		$this->authorized(wpHyvesGetScriptUrl() . '&action=get_wwws');
	}

	/**
	 * Authorize to import users.
	 */
	function authorizeAndImportUsers() {
		$this->authorize(wpHyvesGetScriptUrl()."&action=authorized_import_users", array('friends.get', 'users.get', 'media.get'));
	}

	/**
	 * When authorized import users.
	 */
	function authorizedAndImportUsers() {
		$this->authorized(wpHyvesImportUsersScriptUrl(). '&action=import_friends');
	}

	/**
	 * Authorize and Import tips
	 */
	function authorizeAndImportTips() {
		$this->authorize(wpHyvesGetScriptUrl()  . '&action=authorized_and_get_tips', array('tips.getByUser'));
	}

	/**
	 * When authorized Import tips.
	 */
	function authorizedAndImportTips() {
		$this->authorized(wpHyvesGetScriptUrl() . '&action=get_tips');
	}

	/**
	 * @return unknown_type
	 */
	function authorizeAndGetScraps() {
		$this->authorize(wpHyvesGetScriptUrl() . '&action=authorized_and_get_scraps', array('users.getScraps', 'users.getLoggedin', 'users.get', 'media.get'));
	}

	/**
	 * @return unknown_type
	 */
	function authorizedAndGetScraps() {
		$this->authorized(wpHyvesGetScriptUrl() . '&action=import_scraps');
	}

	/**
	 * Authorize.
	 * @param string $redirect url
	 * @param array $methods allowed methods to access
	 * @access protected
	 */
	function authorize($redirect, $methods = array()) {
		// Create request token and authorize it (causes redirect).
		$oRequestToken = $this->api->retrieveRequesttoken($methods);
		$_SESSION['requesttoken_'.$oRequestToken->getKey()] = serialize($oRequestToken);
		$this->api->redirectToAuthorizeUrl($oRequestToken, $redirect);
		exit;
	}
	/**
	 * Authorized.
	 */
	function authorized($redirect = '') {
		if('' != $redirect) {
			$oauth_token = $_REQUEST['oauth_token'];
			$oRequestToken = getRequestTokenFromSession($oauth_token);
			$oAccessToken = $this->api->retrieveAccesstoken($oRequestToken);
			$local_token = md5($oAccessToken->getKey());
			$_SESSION['localtoken_'.$local_token] = serialize($oAccessToken);
			header('location: '. $redirect . '&local_token=' . $local_token);
			exit;
		}
	}

	/**
	 *
	 */
	function getResults($method = '', $args = array()) {
		$oXml = null;
		if('' != $method && null != $this->api) {
			$local_token = $_REQUEST['local_token'];
			$oAccessToken = getAccessTokenFromSession($local_token);
			$oXml = $this->api->doMethod($method, $args , $oAccessToken);
		}
		return $oXml;
	}

	/**
	 * Init api. After headers are sent.
	 * @access public.
	 */
	function init() {
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "default";
		switch($action) {
			case 'err':
			case 'default':
				$this->showMain();
				break;
			case 'authorized':
				$this->showAuthorized();
				break;
			case 'import_friends':
				$this->importFriends();
				break;
			case 'import_blog':
				$this->importBlog();
				break;
			case 'import_scraps':
				$this->importScraps();
				break;
			case 'posts':
				$this->showPosts();
				break;
			case 'add_post':
				$this->addPost();
				break;
			case 'add_www':
				$this->addWWW();
				break;
			case 'get_wwws':
				$this->importFriendWWWs();
				break;
			case 'get_tips':
				$this->importFriendTips();
				break;
			case 'invalidsession':
				_e( 'session invalid, please retry', 'wp-hyves');
				break;
		}
	}

	/**
	 * Add blog post.
	 * @return blog post url
	 * @access private
	 */
	function addBlogPost($title, $body, $publish = 'default') {
		$local_token = $_REQUEST['local_token'];
		$oAccessToken = getAccessTokenFromSession($local_token);

		$title = strip_tags($title);

		preg_match_all('|<a.*?href="(.*?)".*?>.*?</a>|i', $body, $matches);

		// Replace hyperlinks.
		foreach($matches[0] as $link) {
			preg_match_all('|href="(.*?)".*?>(.*?)</a>|i', $link , $link_matches);
			//print_r($link_matches);
			$body = str_replace($link,"[url={$link_matches[1][0]}]{$link_matches[2][0]}[/url]", $body);
		}

		$body = strip_tags($body); // remove html tags.
		$body .= ' - [url=http://wordpress.org/extend/plugins/wp-hyves/]Sent by WP-Hyves[/url] ';
		$oXml = $this->api->doMethod('blogs.create', array('title'=>$title, 'body'=>$body, 'visibility'=>$publish), $oAccessToken);

		echo '<h3>';
		_e('Post added successfully!', 'wp-hyves');
		echo '</h3>';

		_e('View it here:', 'wp-hyves');
		echo '<br/>';
		echo '<a href="'.$oXml->blog->url.'" target="_blank" title="Blog">';
		echo $oXml->blog->url;
		echo '</a>';
		echo '</p>';

		echo '<p>';
		echo '<a href="'.wpHyvesGetScriptUrl().'" target="_self" title="return">';
		_e('return to menu', 'wp-hyves');
		echo '</a>';

		//print_r($oXml);
		return $oXml->blog->url;
	}

	/**
	 * Add post.
	 * @access private
	 */
	function addPost(){
		$id = addslashes($_REQUEST['id']);
		if($id > 0) {
			$p = get_post($id);
			$title = $p->post_title;
			$body = $p->post_content;
			$hyves_post_url = $this->addBlogPost($title, $body);

			update_post_meta($id, 'hyves_added_post', 1);
			update_post_meta($id, 'hyves_post_url', $hyves_post_url);
		}
	}

	/**
	 * Add www.
	 * @param string $what
	 * @param string $where
	 * @param string $publish visibility
	 * @access public
	 */
	function addWWW($what = '', $where = '', $publish = 'default') {
		if('' == $what) {
			$what = $_REQUEST['what'];
		}

		if('' == $where) {
			$where = $_REQUEST['where'];
		}

		$oXml = $this->getResults('wwws.create',
		array('emotion'=>$what, 'where'=>$where, 'visibility'=>$publish));

		if('' != $oXml->www->wwwid) {
			echo '<h3>';
			_e('WWW added successfully!', 'wp-hyves');
			echo '</h3>';

			echo '<p>' . $what . '@' . $where . '</p>';
		} else {
			_e('Failed to add WWW', 'wp-hyves');
		}
	}

	/**
	 * Import blog posts from hyves.
	 * @access private
	 */
	function importBlog() {

	}

	/**
	 * @return unknown_type
	 */
	function importBlogs() {

	}

	/**
	 * @return unknown_type
	 */
	function importGadgets() {

	}

	/**
	 * @return unknown_type
	 */
	function importMediaAlbums() {

	}

	/**
	 * @return unknown_type
	 */
	function importRespects() {

	}

	/**
	 * @return unknown_type
	 */
	function importScraps() {
		echo '<h3>' . __('Importing Scraps', 'wp-hyves') . '</h3>';

		echo '<p>' . __('Please wait..', 'wp-hyves') . '</h3>';

		//ob_start();

		$user = $this->getCurrentUser();

		$userId = $user->user->userid;
			
		/*echo "<pre>";
		 print_r($user);
		 echo "</pre>";
		 */
		
		// Load scraps from Hyves.
		$oXml = $this->getResults('users.getScraps', array('target_userid'=>$userId));

		if(null != $oXml) {
				
			$scraps = array();

			// Get single scrap.
			foreach($oXml->scrap as $scrap) {
				
				// Get scrap sender.
				$aUser = $this->getUser($scrap->userid);
				
				// Get user thumbnail.
				$media = $this->getMedia($aUser->user->mediaid);
				
				// Avatar filename.
				$avatar_filename = '';
				
				// Cache image.
				if($media != null) {
					
					// Cache thumbnail.
					wpHyvesCacheImage($media->media->icon_medium->src, $aUser->user->userid . '-medium.jpg');
					
					// Set avatar filename.
					$avatar_filename = wpHyvesGetUploadPath(false, true).  $aUser->user->userid  . '-medium.jpg';
				}
								
				// Set scrap.
				$scraps[] = array('scrapid' => ''.$scrap->scrapid,
				'userid' => '' . $scrap->userid,
				'body' => ''.$scrap->body,
				'name' => '' . $aUser->user->displayname,
				'avatar' => '' . $avatar_filename 
				);
			}

			// Save scraps.
			if(is_array($scraps)) {
				$this->saveOption('wphyves_scraps', serialize($scraps));
			}
		}
		
		/*
		$ret = unserialize($this->loadOption('wphyves_scraps'));
		echo "<pre>";
		print_r($ret);
		echo "</pre>";
		*/

		//ob_end_clean();

		echo '<p>' . __('Importing finished.', 'wp-hyves') . '</p>';
		echo '<a href="index.php" title="Dashboard">';
		echo __('return to dashboard', 'wp-hyves');
		echo '</a>';
	}

	/**
	 * Import friend tips.
	 * @return unknown_type
	 */
	function importFriendTips() {
		echo '<h3>' . __('Importing Friend Tips', 'wp-hyves') . '</h3>';

		echo '<p>' . __('Please wait..', 'wp-hyves') . '</h3>';

		//ob_start();
		$blogusers = get_users_of_blog();
		$usernames = array();
		foreach($blogusers as $bu) {
			if(null != $bu && '' != $bu->user_id) {
				$id = get_usermeta($bu->user_id, 'hyves_userid');
				if('' != $id) {
					$oXml = $this->getResults('tips.getByUser', array('userid'=>$id));
					if(null != $oXml) {
						update_usermeta($bu->user_id, 'hyves_tip', addslashes($oXml->tip->body));
					}
				}
			}
		}
		//ob_end_clean();

		echo '<p>' . __('Importing finished.', 'wp-hyves') . '</p>';

		echo '<a href="index.php" title="Dashboard">';
		echo __('return to dashboard', 'wp-hyves');
		echo '</a>';
	}

	/**
	 * Import Friend WWWs.
	 * @access protected
	 */
	function importFriendWWWs() {

		echo '<h3>' . __('Importing Friend WWWs', 'wp-hyves') . '</h3>';

		echo '<p>' . __('Please wait..', 'wp-hyves') . '</h3>';

		ob_start();
		$blogusers = get_users_of_blog();
		$usernames = array();
		foreach($blogusers as $bu) {
			if(null != $bu && '' != $bu->user_id) {
				$id = get_usermeta($bu->user_id, 'hyves_userid');
				if('' != $id) {
					$oXml = $this->getResults('wwws.getByUser', array('userid'=>$id));

					$wwwId = $oXml->www->wwwid;
					$what = $oXml->www->emotion;
					$where = $oXml->www->where;
					$when_ts = $oXml->www->created;

					update_usermeta($bu->user_id, 'hyves_www', $what. '@' . $where);
				}
			}
		}
		ob_end_clean();

		echo '<p>' . __('Importing finished.', 'wp-hyves') . '</p>';

		echo '<a href="index.php" title="Dashboard">';
		echo __('return to dashboard', 'wp-hyves');
		echo '</a>';
	}

	/**
	 * Import Hyves friends.
	 * @access private.
	 */
	function importFriends() {
		$oXml = $this->getResults('friends.get');

		// Get current users.
		$blogusers = get_users_of_blog();

		$usernames = array();

		foreach($blogusers as $bu) {
			$usernames[] = $bu->user_login;
		}

		//echo '<p><h3>'. __('Importing Friends! Please wait..', 'wp-hyves') .'</h3>' . "\n";

		$default_password = wp_generate_password();

		echo '<p>';
		_e('Default password for added users:', 'wp-hyves');
		echo '<pre>'.$default_password.'</pre>';
		echo '<p>' . __('Send this password to all users manually.', 'wp-hyves') . '</p>';
		echo '</p>';

		//echo $default_password;
		//print_r($oXml);

		$usersCollection = array();

		$added_users = false;

		foreach($oXml->userid as $id) {

			$user = $this->getUser($id);

			$media = $this->getMedia($user->user->mediaid);

			if($user != null) {

				// Cache image.
				if($media != null) {
					wpHyvesCacheImage($media->media->icon_medium->src, $id . '-medium.jpg');
				}

				// Get hyves name to use as login.
				$hyves_name = '';

				preg_match_all('|http://(.*?).hyves.nl|i', $user->user->url, $matches);

				foreach ( $matches[1] as $n ){
					$hyves_name = $n;
				}

				//print_r($user);
				if($user->user->nickname != '') {
					if('' == $hyves_name) {
						$hyves_name = $user->user->nickname;
					}

					// add new if not exists.
					if(!in_array($hyves_name, $usernames)) {

						$new_userid = dl_create_user($hyves_name, $default_password, $user->user->url,  $hyves_name . '@hyves');

						if('' != $new_userid) {

							$hyves_userid = (string)$user->user->userid;
							$fn = (string)$user->user->firstname;
							$ln = (string)$user->user->lastname;
							//	$birthday = (string)$user->user->birthday->year . '-' . (string)$this->user->birthday->month . '-' . (string)$this->user->birthday->day;
							$avatar_filename = wpHyvesGetUploadPath(false, true). $hyves_userid . '-medium.jpg';

							update_usermeta($new_userid, 'hyves_userid', $hyves_userid);
							update_usermeta($new_userid, 'first_name', $fn);
							update_usermeta($new_userid, 'last_name', $ln);
							update_usermeta($new_userid, 'hyves_pw', $default_password);
							//update_usermeta($new_userid, 'avatar', $avatar_filename);
							//	update_usermeta($new_userid, 'hyves_birthday', $birthday);

							$added_users = true;

							$usersCollection[$hyves_name]['img_html'] = '<img src="' . $avatar_filename . '" alt="'.$media->media->title.'" />';
							$usersCollection[$hyves_name]['firstname'] = $fn;
							$usersCollection[$hyves_name]['lastname'] = $ln;
							$usersCollection[$hyves_name]['password'] = $default_password;
							$usersCollection[$hyves_name]['username'] = $hyves_name;


							//echo $avatar_filename;

						}
					}
				}
				//	print_r($media);
			}
		}

		//	dl_load_admin_block('list-users', array('users'=>$usersCollection));

		//echo ' <p><strong>'. __('Finished importing.','wp-hyves') . '</strong></p></p>';
	}

	/**
	 * Get current logged in user.
	 * @return user object
	 */
	function getCurrentUser() {
		$user = $this->getResults('users.getLoggedin');
		return $user;
	}

	/**
	 * Get user by id.
	 * @param string $userId
	 * @return user object
	 */
	function getUser($userId) {
		$user = $this->getResults('users.get', array('userid' => $userId));
		return $user;
	}

	/**
	 * Get media by id.
	 * @param string $mediaId
	 * @return media object
	 */
	function getMedia($mediaId) {
		$media = $this->getResults('media.get', array('mediaid' => $mediaId));
		return $media;
	}

	/**
	 * Show authorized.
	 * @access protected
	 */
	function showAuthorized() {
		echo '<h3>' . __('Menu', 'wp-hyves') . '</h3>';
		echo '<ul>';
		echo '<li>';
		echo "<a href=\"".$this->script_url."&action=import_friends&local_token=".$this->local_token."\">Import Friends</a>";
		echo '</li>';
		echo '<li>';
		echo "<a href=\"".$this->script_url."&action=posts&local_token=".$this->local_token."\">Add post</a>";
		echo '</li>';
		echo '</ul>';
	}

	/**
	 * @param unknown_type $msg
	 * @return unknown_type
	 */
	function err($msg) {
		echo  $msg;
	}

	/**
	 * Show menu.
	 * @access protected
	 */
	function showMain() {

	}

	/**
	 * Show posts.
	 * @access protected
	 */
	function showPosts() {
		$local_token = $_REQUEST['local_token'];
		$oAccessToken = getAccessTokenFromSession($local_token);
		$lastposts = get_posts('numberposts=15');

		echo '<h3>'. __('Select post to publish to Hyves', 'wp-hyves') . '</h3>';
		echo '<ol>';
		foreach($lastposts as $post):
		$is_added =  get_post_meta($post->ID, 'hyves_added_post');
		if(!$is_added):
		?>
<li><a
	href="<?php echo wpHyvesGetScriptUrl(); ?>&action=add_post&local_token=<?php echo $local_token;?>&id=<?php echo $post->ID; ?>">
		<?php echo $post->post_title; ?> </a></li>
		<?php	endif; endforeach;
		echo '</ol>';
	}

	/**
	 * Save single option.
	 * @param unknown_type $key
	 * @param unknown_type $value
	 * @return unknown_type
	 */
	function saveOption($key, $value) {
		if($key != '') {
			if(!update_option($key, $value)){
				if(!add_option($key, $value)){
					//echo "failed to add option!";
				}
			}
		}
	}

	/**
	 * Load option.
	 * @param String $key option key
	 * @return String option value
	 */
	function loadOption($key){
		return stripslashes(get_option($key));
	}
}
?>