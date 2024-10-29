<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://catsplugins.com
 * @since      1.0.0
 *
 * @package    acf_form_builder
 * @subpackage acf_form_builder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    acf_form_builder
 * @subpackage acf_form_builder/public
 * @author     Nicholas To <togiang88@gmail.com>
 */
class Acf_Form_Builder_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $acf_form_builder    The ID of this plugin.
	 */
	private $acf_form_builder;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $acf_form_builder       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $acf_form_builder, $version ) {

		$this->acf_form_builder = $acf_form_builder;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Acf_Form_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Acf_Form_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->acf_form_builder, plugin_dir_url( __FILE__ ) . 'css/acf-form-builder-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Acf_Form_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Acf_Form_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->acf_form_builder, plugin_dir_url( __FILE__ ) . 'js/acf-form-builder-public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the shortcode for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function acf_fb_register_shortcode() {
		add_shortcode('cat_form', array($this, 'acf_fb_register_shortcode_atts'));
	}

	/**
	 * Register the shortcode attrs for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function acf_fb_register_shortcode_atts($atts, $content = null) {
		
		$atts = shortcode_atts(
			array(
				'group_id' => $atts['group_id'],
				),
			$atts);
		
		if ( isset($atts['group_id']) ) {
			$post_meta = get_post_meta($atts['group_id']);

			if (isset($post_meta['form_settings'])) {
				$form_settings = unserialize($post_meta['form_settings'][0]);
			}

			if ( 'form' == $form_settings['display'] ) {
				$this->process_shortcode_atts( $atts );
			} else {
				return;
			}
		} else {
			return;
		}
	}

	public function process_shortcode_atts( $atts )
	{		
		$order_id = wp_create_nonce();
		
		$group_keys[] = $atts['group_id'];

		// get fields data
		//$fields = $this->acf_fb_get_form_fields( $atts );
		
		add_action( 'wp_enqueue_scripts', array($this, 'acf_load_script'), 100 );

		wp_enqueue_script(
            'wp-color-picker',
            admin_url( 'js/color-picker.min.js' ),
            array( 'iris' ),
            false,
            1
        );

        $html_before_fields = "<input type='hidden' value='" .$atts['group_id'] . "' name='group_id'/>";

		// otherwise render the groups
        $this->acf_form(
            apply_filters('acf-frontend-form-params', array(            		
                    'post_id' => 'new_post',
                    'field_groups' => $group_keys,
                    'form' => true,
                    'submit_value' => 'Submit Form',
                    'updated_message' => '',
                    'html_before_fields' => $html_before_fields,
                )
            )
        );
	}

	public function acf_fb_get_form_fields( $atts ) 
	{	
		if ( !empty($atts['group_id']) ) 
		{				
			if ( function_exists('acf_get_fields') ) // acf pro
			{
				$_groups = call_user_func_array( 'acf_get_field_groups', $args = array() );
				foreach ($_groups as $key => $_group) {
					if ($_group['ID'] == $atts['group_id'])
						$_fields = call_user_func_array( 'acf_get_fields', $_group );
				}
			} else // acf free
			{
				$_fields = apply_filters( 'acf/field_group/get_fields', array(), $atts['group_id'] );
			}
			
			return $_fields;
		} 

		return;
	}	

	public function acf_form( $args )
	{
		$func = apply_filters('acf-frontend-render-form', 'acf_form', $args);
		call_user_func_array($func, func_get_args());
	}

	public function acf_load_script() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script(
            'acf_load_script',
            admin_url( 'js/iris.min.js' ),
            array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
            false,
            1
        );

        wp_enqueue_script(
            'wp-color-picker',
            admin_url( 'js/color-picker.min.js' ),
            array( 'acf_load_script' ),
            false,
            1
        );

        $colorpicker_l10n = array(
            'clear' => __( 'Clear' ),
            'defaultString' => __( 'Default' ),
            'pick' => __( 'Select Color' ),
            'current' => __( 'Current Color' ),
        );

        wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n ); 

    }

    // stub for handling a form submission
    protected function _process_submitted_form() {

    	$post_data = array();
    	$post_data[] = $_POST;

		$post_meta = get_post_meta($_POST['group_id']);

		$actions = unserialize($post_meta['form_settings'][0]);
        
        if (isset($post_data)) {          
    		
    		if ('custom_actions' == $actions["default_actions"]) {

    			if ( $actions["custom_actions"] != 'none') {
    			
	    			$function = $actions["custom_actions"];

	    			if (method_exists($this, $function)) {
	        			call_user_func_array(array($this, $function), $post_data);
	    			} 
	    			if (function_exists($function)) {
	        			call_user_func_array($function, $post_data);
	    			}
	        	}
    		} else {
    			
    			$function = 'acf_fb_default_action_' . $actions["custom_actions"];

    			if (function_exists($function))
	        		call_user_func_array($function, $post_data);
    		}
        } else {       	
        	do_action('acf_fb_custom_actions', array(&$name));
        }
    }

    // determine if an acf form was submitted
    protected function _form_submitted() {

        $pro = function_exists('acf_validate_save_post');
        // if this is acf pro, then validate the form, and pass
        if ($pro && isset($_POST['acf']) && acf_validate_save_post())
            return true;
        // if not pro, and the fields we submitted, pass
        if (!$pro && isset($_POST['fields'], $_POST['acf_settings']))
            return true;

        return false;
    }

    // determine when this group needs to load the acf_form_head function. should be overriden by child class for it's logic to run
    protected function _needs_form_head() {
        return false;
    }

    // on the checkout, load our checkout js
    protected function _enqueue_assets() {
        // reused vars
        $uri = plugin_dir_url(__FILE__) . 'assets/js/acf-form-builder-script.js';

        // queue up the checkout specific js, that handles the acf form validation
        wp_enqueue_script('acf-frontend-display', $uri, array('jquery'));
    }

    // when viewing a page, load the acf_form_head logic before page render
    public function load_acf_form_head() 
    {
        // if this is not the appropriate woocommerce page, then bail now
        //if (!$this->_needs_form_head())
            //return;

        // process the form handler if we still need to
        if ($this->_form_submitted())
            $this->_process_submitted_form();

        // load any script and style for this location specifically
        $this->_enqueue_assets();

        // otherwise load the acf logic
        acf_form_head();
    }

}
