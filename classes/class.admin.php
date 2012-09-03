<?php if (!defined('SMUGINS_VERSION')) exit('No direct script access allowed');
/**
 * SmugMug Insert Admin
 */
class SMUGINS_Admin {
	private $version = NULL;
	
	public $option_array = array(
		'user_id_1'=>'',
		'user_id_2'=>'',
		'user_id_3'=>'',
		'user_id_4'=>'',
		'user_id_5'=>'',
		'thumbnail_size'=>'Ti',
		'full_size' => 'L',
		'lightbox'=>'',
		'css_class'=>'smugins'
	);
	public $image_sizes = array(
		"Ti" => "100px cropped",
		"Th" => "150px cropped",
		"S" => "max 400px",
		"M" => "max 600px",
		"L" => "max 800px",
		"XL" => "max 1024px",
		"X2" => "max 1280px",
		"X3" => "max 1600px"
	);
	
	public $libraries = array(
		"" => "None",
		"lightbox" => "Lightbox",
		"prettyPhoto" => "prettyPhoto",
		"shadowbox" => "Shadowbox"
	);
	
	function __construct() {
		$this->version = SMUGINS_VERSION;
		$this->set_data();
	}
	
	function set_data() {
		$active = get_option('smugins_active');
		if ($active){
			$db_options = get_option('smugins_option');
			foreach ((array)$this->option_array as $key => $val){
				if (isset($db_options[$key])){
					$this->option_array[$key] = $db_options[$key];
				}
			}
		}
	}
	
	function smugins_activate(){
		add_option( 'smugins_active', 1 );
		add_option( 'smugins_option', $this->option_array );
	}
	
	function smugins_deactivate(){
		delete_option( 'smugins_active' );
		delete_option( 'smugins_option' );
	}
	
	function smugins_admin_menu(){
		$settings_page = add_menu_page('Smugmug Insert Plugin Settings', 'SmugIns', 'administrator', __FILE__, array($this,'smugins_admin_page'),SMUGINS_PLUGIN_URL.'/script/images/smugmug_16.png');
		add_action( "admin_print_styles-$settings_page", array( $this, 'load_admin_js_css' ) );
	}
	
	function smugins_admin_page(){
		include( SMUGINS_PLUGIN_DIR.'/pages/settings.php' );
	}
	
	function load_admin_js_css(){
		wp_enqueue_style( 'smugins-style', SMUGINS_PLUGIN_URL.'/script/dialog.css', false, $this->version, 'screen'); 
	}
	
	function update_options($data){
		foreach($this->option_array as $key => $val){
			if (isset($data[$key])){
				$this->option_array[$key] = stripslashes(trim($data[$key]));
			}
		}
		update_option( 'smugins_option', $this->option_array );
	}
	
	function smugins_media_button($context){
		global $post_ID, $temp_ID;
		$uploading_iframe_ID = (int) (0 == $post_ID ? $temp_ID : $post_ID);
		$media_upload_iframe_src = "media-upload.php?post_id=$uploading_iframe_ID";
		
		$smugins_media_iframe_src = apply_filters('smugins_media_iframe_src', "$media_upload_iframe_src&amp;type=".md5('smugins')."&amp;tab=type");
		$smugins_media_title = __('SmugMugInsert', 'smugins');
		
		echo "
		<a href='{$smugins_media_iframe_src}&amp;TB_iframe=1&amp;height=500&amp;width=800' class='thickbox' title='$smugins_media_title'>
		<img src='".SMUGINS_PLUGIN_URL.'/script/images/smugmug_16.png'."' alt='$smugins_media_title' />
		</a>";
	}
	
	function add_style() {
		global $is_IE;
		wp_enqueue_style('media');
		wp_enqueue_style('smugins', SMUGINS_PLUGIN_URL.'/script/dialog.css',array(),$this->version,'all');
	}

}