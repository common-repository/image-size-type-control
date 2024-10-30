<?php
/*
Plugin Name: Image Size & Type Control
Description: Image Size & Type Control
Version: 1.0
Author: WPWhale
Author URI: https://wpfactory.com/author/wpwhale/
Text Domain: image-size-limiter
Domain Path: /langs
Copyright: © 2020 WPWhale
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;

require_once ('imsl-options.php');

if ( ! class_exists( 'IMSL_Size_Limit' ) ) :

	class IMSL_Size_Limit {
		
		public $option;
		
		public function __construct()  {  
				$this->option = get_option('imsl_options');

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'add_plugin_links') );
				add_filter('wp_handle_upload_prefilter', array($this, 'error_message'));
		}  

		public function add_plugin_links( $links ) {
			return array_merge(
				array(
					'settings' => '<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/options-media.php?settings-updated=true#imsl-limit">Settings</a>'
				),
				$links
			);
		}

		public function get_limit() {
			$option = $this->option;

			if ( isset($option['img_upload_limit']) ){
				$limit = $option['img_upload_limit'];
			} else {
				$limit = $this->imsl_limit();
			}

			return $limit;
		}
		
		public function is_max_image_restrict()
		{
			$option = $this->option;

			$restrict = false;

			if ( isset($option['img_upload_max_dimension_limit_checkbox']) ){
				$limit = $option['img_upload_max_dimension_limit_checkbox'];

				if ($limit=='yes')
				{
					$restrict = true;
				}
			}
			return $restrict;
		}
		
		public function is_min_image_restrict()
		{
			$option = $this->option;

			$restrict = false;

			if ( isset($option['img_upload_min_dimension_limit_checkbox']) ){
				$limit = $option['img_upload_min_dimension_limit_checkbox'];

				if ($limit=='yes')
				{
					$restrict = true;
				}
			}
			return $restrict;
		}

		public function is_ext_restrict_allowed()
		{
			$option = $this->option;

			$restrict = false;

			if ( isset($option['img_upload_allowed_ext_limit_checkbox']) ){
				$limit = $option['img_upload_allowed_ext_limit_checkbox'];

				if ($limit=='yes')
				{
					$restrict = true;
				}
			}
			return $restrict;
		}

		public function is_ext_restrict_disallowed()
		{
			$option = $this->option;

			$restrict = false;

			if ( isset($option['img_upload_disallowed_ext_limit_checkbox']) ){
				$limit = $option['img_upload_disallowed_ext_limit_checkbox'];

				if ($limit=='yes')
				{
					$restrict = true;
				}
			}
			return $restrict;
		}
		
		public function ext_disallowed()
		{
			$option = $this->option;

			$value = '';

			if ( isset($option['img_upload_disallowed_ext_limit']) ){
				$value = $option['img_upload_disallowed_ext_limit'];
			}
			return $value;
		}
		
		public function ext_allowed()
		{
			$option = $this->option;

			$value = '';

			if ( isset($option['img_upload_allowed_ext_limit']) ){
				$value = $option['img_upload_allowed_ext_limit'];
			}
			return $value;
		}
		
		public function max_height()
		{
			$option = $this->option;

			$value = 300;

			if ( isset($option['img_upload_max_dimension_height_limit']) ){
				$value = $option['img_upload_max_dimension_height_limit'];
			}
			return $value;
		}
		
		public function min_height()
		{
			$option = $this->option;

			$value = 300;

			if ( isset($option['img_upload_min_dimension_height_limit']) ){
				$value = $option['img_upload_min_dimension_height_limit'];
			}
			return $value;
		}
		
		public function max_width()
		{
			$option = $this->option;

			$value = 300;

			if ( isset($option['img_upload_max_dimension_width_limit']) ){
				$value = $option['img_upload_max_dimension_width_limit'];
			}
			return $value;
		}
		
		public function min_width()
		{
			$option = $this->option;

			$value = 300;

			if ( isset($option['img_upload_min_dimension_width_limit']) ){
				$value = $option['img_upload_min_dimension_width_limit'];
			}
			return $value;
		}

		public function output_limit() {
			$limit = $this->get_limit();
			$limit_output = $limit;
			$mblimit = $limit / 1000;


			if ( $limit >= 1000 ) {
				$limit_output = $mblimit;
			}

			return $limit_output;
		}

		public function imsl_limit() {
			$output = wp_max_upload_size();
			$output = round($output);
			$output = $output / 1000000; //convert to megabytes
			$output = round($output);
			$output = $output * 1000; // convert to kilobytes

			return $output;

		}

		public function limit_unit() {
			$limit = $this->get_limit();

			if ( $limit < 1000 ) {
				return 'KB';
			}
			else {
				return 'MB';
			}

		}
		
		public function get_extension($filename)
		{
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			return $ext;
		}

		public function error_message($file) {
			$size = $file['size'];
			$name = $file['name'];
			$size = $size / 1024;
			$type = $file['type'];
			$is_image = strpos($type, 'image');
			$limit = $this->get_limit();
			$limit_output = $this->output_limit();
			$unit = $this->limit_unit();
			
			$disallowed = $this->is_ext_restrict_disallowed();
			$allowed = $this->is_ext_restrict_allowed();

			$ext_disallowed = $this->ext_disallowed();
			$ext_allowed = $this->ext_allowed();

			$maxdm = $this->is_max_image_restrict();
			$mindm = $this->is_min_image_restrict();

			$min_height = $this->min_height();
			$min_width = $this->min_width();
			$max_height = $this->max_height();
			$max_width = $this->max_width();

			

		  if ( ( $size > $limit ) && ($is_image !== false) ) {
			 $file['error'] = __('Image files must be smaller than ', 'image-size-type-control') .$limit_output.$unit;
			 if (IMSL_DEBUG) {
				$file['error'] .= '[ filesize = '.$size.', limit ='.$limit.' ]. ';
			 }
			 return $file;
		  }
		  
		  if( $is_image !== false ) {
			  $image = getimagesize($file['tmp_name']);
			  $image_width = $image[0];
			  $image_height = $image[1];

			  if ($maxdm) {
				  if ( $image_width > $max_width || $image_height > $max_height ) {
						// add in the field 'error' of the $file array the message 
						$file['error'] =  __(' Maximum image dimension ( ', 'image-size-type-control') . $max_width . __(' by ', 'image-size-type-control') . $max_height . __(' pixels ) not match. ', 'image-size-type-control'); 
						return $file;
				  }
			  }
			  
			  if ($mindm) {
				  if ( $image_width < $min_width || $image_height < $min_height ) {
						// add in the field 'error' of the $file array the message 
						$file['error'] =  __(' Minimum image dimension ( ', 'image-size-type-control') . $min_width . __(' by', 'image-size-type-control') . $min_height . __(' pixels ) not match. ', 'image-size-type-control'); 
						return $file;
				  }
			  }
		  }
		  
		  if ( $name ) {
			 $ext = $this->get_extension($name);
			 if ($allowed) {
				 $arrallow = $this->get_ext_arr( $ext_allowed );
					if ( !in_array( $ext, $arrallow ) ) {
						$file['error'] =  __(' File extension not allowed, ( Allowed extension ', 'image-size-type-control') . $ext_allowed . ' ) ';  
						return $file;
					}
			 }
			 
			 if ( $disallowed ) {
				 $arrdisallow = $this->get_ext_arr( $ext_disallowed );
					if ( in_array( $ext, $arrdisallow ) ) {
						$file['error'] =  __('File extension not allowed, ( Disallowed extension ', 'image-size-type-control') . $ext_disallowed . ' ) ';  
						return $file;
					}
			 }
		  }

		  return $file;
		}
		
		public function get_ext_arr( $str ) {
			if (empty($str)) {
				return array();
			}
			return explode(',', $str);
		}
		public function load_styles() {
			$limit = $this->get_limit();

			$limit_output = $this->output_limit();
			$mblimit = $limit / 1000;
			$wplimit = $this->imsl_limit();
			$unit = $this->limit_unit();
			
			


			?>
			<!-- .Custom Max Upload Size -->
			<style type="text/css">
			.after-file-upload {
				display: none;
			}
			<?php /*if ( $limit < $wplimit ) : ?>
			.upload-flash-bypass:after {
				content: 'Maximum image size (Image size limiter): <?php echo $limit_output . $unit; ?>.';
				display: block;
				margin: 15px 0;
			}
			<?php endif;*/ ?>

			</style>
			<!-- END Custom Max Upload Size -->
			<?php
		}
		
		public function load_script()
		{
			$limit = $this->get_limit();
			$limit_output = $this->output_limit();
			
			$disallowed = $this->is_ext_restrict_disallowed();
			$allowed = $this->is_ext_restrict_allowed();

			$ext_disallowed = $this->ext_disallowed();
			$ext_allowed = $this->ext_allowed();

			$maxdm = $this->is_max_image_restrict();
			$mindm = $this->is_min_image_restrict();

			$min_height = $this->min_height();
			$min_width = $this->min_width();
			$max_height = $this->max_height();
			$max_width = $this->max_width();
			
			$mblimit = $limit / 1000;
			$wplimit = $this->imsl_limit();
			$unit = $this->limit_unit();
			
			$html  = '<strong>' . __('Image Size & Type Control', 'image-size-type-control') . '</strong>';

			if ( $limit < $wplimit ) :
			$html  .= '<br>' . __('Maximum image size: ', 'image-size-type-control') . $limit_output . $unit;
			endif;

			if ($maxdm) :
			$html .= '<br>' . __('Maximum Image dimension: ', 'image-size-type-control') . $max_width . __(' by ', 'image-size-type-control') . $max_height . __(' pixels', 'image-size-type-control');
			endif;

			if ($mindm) :
			$html .= '<br>' . __('Minimum Image dimension: ', 'image-size-type-control') . $min_width . __(' by ', 'image-size-type-control') . $min_height . __(' pixels', 'image-size-type-control');
			endif;

			if ($allowed) :
			$html .= '<br>' . __('Allowed Image Extension: ', 'image-size-type-control') . $ext_allowed;
			endif;

			if ($disallowed) :
			$html .= '<br>' . __('Disallowed Image Extension: ', 'image-size-type-control') . $ext_disallowed;
			endif;

			?>
			<script>
			jQuery(document).ready( function(){ 
				jQuery(".upload-flash-bypass").html('<?php echo $html; ?>');
			});
			</script>
			<?php
		}


	}

endif;
$IMSL_Size_Limit = new IMSL_Size_Limit;
add_action('admin_head', array($IMSL_Size_Limit, 'load_styles'));
add_action( 'admin_footer', array($IMSL_Size_Limit, 'load_script'));