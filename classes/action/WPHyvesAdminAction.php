<?php
/**
 * WPHyvesAdminAction.
 * @author Dave Ligthart <info@daveligthart.com>
 * @version 0.1
 * @package wp-hyves
 */
class WPHyvesAdminAction extends WPHyvesWPPlugin{

	var $wpHyvesApi = null;
	var $curPostId = 0;
	var $wpHyvesConsumerKey = '';
	var $wpHyvesConsumerSecret = '';

	/**
	 * Constructor.
	 */
	public function __construct($plugin_name, $plugin_base, $key = '', $secret = '') {
		$this->WPHyvesAdminAction($plugin_name, $plugin_base, $key, $secret);
	}

	/**
	 * Constructor.
	 * @param string $plugin_name
	 * @param string $plugin_base
	 * @param string $key consumer key hyves api
	 * @param string $secret consumer secret hyves api
	 * @access public
	 */
	function WPHyvesAdminAction($plugin_name, $plugin_base, $key = '', $secret = ''){
		$this->plugin_name = $plugin_name;
		$this->plugin_base = $plugin_base;
		$this->wpHyvesConsumerKey = $key;
		$this->wpHyvesConsumerSecret = $secret;

		/**
		 * Handle wordpress actions.
		 */
		$this->add_action('activate_'.trim($_GET['plugin']) ,'activate'); //plugin activation.
		$this->add_action('admin_head'); // header rendering.
		$this->add_action('publish_post');
		$this->add_action('admin_menu'); // menu rendering.
		$this->add_action('init');
		
		switch(@$_REQUEST['action']) {		
			case 'import_friends':
				$this->add_action('admin_notices', 'display_import_friends_message');
				break;
			case '':
				break;
		}
	}

	/**
	 * Render admin views.
	 * Called by admin_menu.
	 * @access private
	 */
	function renderView() {
		$sub = $this->getAction();
		$url = $this->getActionUrl();

		// Display submenu
		$this->render_admin('admin_submenu', array ('url' => $url, 'sub' => $sub));

		/**
		 * Show view.
		 */
		switch($sub){
			default:
			case 'main':
				$this->admin_start();
				break;
		}
	}
	/**
	 * Import Users.
	 */
	function renderImportUsersView() {
		$this->render_admin('admin_import_users', array("plugin_name"=>$this->plugin_name,'api'=>$this->wpHyvesApi));
	}

	/**
	 * Activate plugin.
	 * @access private
	 */
	function activate() {

	}

	/**
	 * Init.
	 * Runs before headers are sent.
	 * @access protected
	 */
	function init() {
		$this->adminConfigForm = new WPHyvesAdminConfigForm();

		/* @see config.php */
		if('' != $this->wpHyvesConsumerKey && '' != $this->wpHyvesConsumerSecret) {
			$this->adminConfigForm->setKey($this->wpHyvesConsumerKey);
			$this->adminConfigForm->setSecret($this->wpHyvesConsumerSecret);
			$this->adminConfigForm->saveOptions(true);
		}

		$key = $this->adminConfigForm->getKey();
		$secret = $this->adminConfigForm->getSecret();

		$this->wpHyvesApi = new WPHyvesAPI($key, $secret);
		$this->wpHyvesApi->auth();
	}

	/**
	 * Render header.
	 * @access private
	 */
	function admin_head(){
		$this->render_admin('admin_head', array("plugin_name"=>$this->plugin_name));
	}

	/**
	 * Create menu entry for admin.
	 * @return	void
	 * @access private
	 */
	function admin_menu(){
		if (function_exists('add_options_page')) {
			add_options_page(__('WP-Hyves', 'wphyves'),
			__('WP-Hyves', 'wphyves'),
			10,
			basename ($this->dir()),
			array (&$this, 'renderView')
			);
		}
		
		//parent, page_title, menu_title, access_level/capability, file, [function]);
		if(function_exists('add_submenu_page')) {
			add_submenu_page('users.php', __('Import Hyves','wp-hyves'), __('Import Hyves','wp-hyves'), 10, basename($this->dir()), array (&$this, 'renderImportUsersView'));
		}

		$this->add_metabox();
	}

	/**
	 * Display the configuration settings.
	 * @access protected
	 */
	function admin_start(){
		$adminConfigAction = new WPHyvesAdminConfigAction($this->plugin_name, $this->plugin_base, $this->wpHyvesApi);
		$adminConfigAction->render();
	}

	/**
	 * Display the help page.
	 * @return void
	 * @access private
	 */
	function admin_help(){
		$this->render_admin('admin_help', array("plugin_name"=>$this->plugin_name));
	}

	/**
	 * Display wp-stats chart in dashboard.
	 * @return void
	 * @access private
	 */
	function admin_dashboard() {
		$this->render_admin('admin_dashboard', array("plugin_name"=>$this->plugin_name));
	}

	/**
	 * Publish post to Hyves Blog.
	 * @param integer $id Id of post
	 * @access private
	 */
	function publish_post($id) {
		$this->curPostId = $id;
	}

	/**
	 * Add post metabox.
	 */
	function addpost_metabox() {
		if($_REQUEST['post'] > 0) {
			?>
<p class="sub"><img
	src="<?php bloginfo ('wpurl') ?>/wp-content/plugins/wp-hyves/resources/images/icon_24.png"
	alt="hyves logo" style="float: right;" /></p>
<a
	href="<?php echo wpHyvesGetScriptUrl().'&action=authorize_and_post&id='.addslashes($_REQUEST['post']); ?>"
	title="publish to hyves"> <input id="publish" class="button-primary"
	type="button" value="Publish to Hyves" name="wphyves_publish_post" /> </a>

			<?php
		} else {
			_e('Waiting for Post to be published.', 'wp-hyves');
		}
	}

	/**
	 * Add metabox.
	 */
	function add_metabox() {
		if(function_exists('add_meta_box')){
			add_meta_box('wphyves_add_post_div', __('WP-Hyves - Add Post', 'wp-hyves'), array(&$this, 'addpost_metabox'), 'post', 'side');
		}
	}
	
	/**
	 * Display message importing friends.
	 * @return unknown_type
	 */
	function display_import_friends_message() {
		$this->display_message( __('Importing Friends! Please wait..', 'wp-hyves'));
	}
	
	/**
	 * Display message importing scraps.
	 * @return unknown_type
	 */
	function display_import_scraps_message() {
		$this->display_message( __('Importing Scraps! Please wait..', 'wp-hyves'));
	}
}
?>