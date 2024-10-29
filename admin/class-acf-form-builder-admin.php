<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://catsplugins.com
 * @since      1.0.0
 *
 * @package    acf_form_builder
 * @subpackage acf_form_builder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    acf_form_builder
 * @subpackage acf_form_builder/admin
 * @author     Nicholas To <togiang88@gmail.com>
 */

class Acf_Form_Builder_Admin {

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
	 * @param      string    $acf_form_builder       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $acf_form_builder, $version ) {

		$this->acf_form_builder = $acf_form_builder;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->acf_form_builder, plugin_dir_url( __FILE__ ) . 'css/acf-form-builder-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->acf_form_builder, plugin_dir_url( __FILE__ ) . 'js/acf-form-builder-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * display action fields.
	 *
	 * @since    1.0.0
	 */
	public function acf_fb_display_action_fields() {
		
		// get current admin screen, or null
	    $screen = get_current_screen();

	    // verify admin screen object
	    if (is_object($screen)) {

	    	if ('acf' == $screen->post_type || 'acf-field-group' == $screen->post_type) {

				wp_enqueue_script( 'acf_fb_display_action_fields', plugin_dir_url( __FILE__ ) . 'js/acf-form-builder-get-fields.js', array( 'jquery' ), $this->version, false );

	            wp_localize_script(
	                'acf_fb_display_action_fields',
	                'acf_fb_meta_box_obj',
	                [
	                    'url' => admin_url('admin-ajax.php'),
	                ]
	            );
	        }
	    }
	}


	/**
	 * handle ajax return
	 *
	 * @since    1.0.0
	 */
	public function acf_fb_meta_box_ajax_handler() {

	    if (isset($_POST['acf_fb_field_value'])) {
	        
	        switch ($_POST['acf_fb_field_value']) {	            
	            case 'form':
	            	$this->acf_fb_add_action_select_field();	            
	                break;
	            
	            case 'custom_actions': 
					$this->acf_fb_add_custom_action_select_field($custom_actions='custom_actions');				
	            	break;

	            case 'create_post': 
					$this->acf_fb_add_custom_action_select_field($custom_actions='create_post');				
	            	break;
	            
	            case 'non_form':
	            default:
	            	echo 'hide_element';
	                break;
	        }
	    }
	    // ajax handlers must die
	    die;
	}

	/**
	 * add custom metabox
	 */	
	public function acf_fb_add_custom_box() {
		//count forms
		global $post;
		$post_type = $post->post_type;
		if ('acf-field-group' == $post_type ) {$post_type = 'acf-field-group';} else {$post_type = 'acf';}
		$post_args = array('posts_per_page' => 10, 'post_type' => $post_type, 'exclude' => $post->ID, );
		$other_posts = get_posts($post_args);
		foreach ($other_posts as $other_post) {
   			$get_post_meta = get_post_meta($other_post->ID);
			$form_settings = unserialize($get_post_meta['form_settings'][0]);
   			if (in_array('form', $form_settings)) {$forms_count_result = 2;break;} else {$forms_count_result = 0;}
 		 }
		if ($forms_count_result < 1) {

        add_meta_box(
            'acf_fb_form_settings',           				// Unique ID
            'Form Builder Settings',  					// Box title
            array($this, 'acf_fb_main_custom_box_html'),  	// Content callback, must be of type callable
            array('acf', 'acf-field-group'),                   						// Post type
            'normal',
            'high'
        );

        } else {

        add_meta_box(
            'acf_fb_form_settings',           				// Unique ID
            'Form Builder Settings',  					// Box title
            array($this, 'acf_fb_custom_error_box_html'),  	// Content callback, must be of type callable
            array('acf', 'acf-field-group'),                   						// Post type
            'normal',
            'high'
        );

        }
     	 
	}
	public function acf_fb_custom_error_box_html()
	{
		echo '<style>p.acf-cta-action a {color: #fff;font-size: 18px;font-weight: 800;}</style>';
		echo '<p><strong>ACF Form Builder Notice:</strong> You have exceeded the maximum forms that you can use in FREE version. Please update to PRO version to remove the limitations.</p><p style="background: #ff2828;padding: 10px;text-align: center;color: #FFFFFF;"><strong><a style="font-size: 18px;font-weight: 800;color: #fff;" href="https://www.wpwiseguys.com/recommends/acf-form-builder-pro-dashboard/">UPDATE TO PRO VERSION NOW!</a><p></strong>';
	}

	public function acf_fb_main_custom_box_html() {
		global $post;

		$post_type = $post->post_type;
		
		$post_meta = get_post_meta($post->ID);

		if (isset($post_meta['form_settings'])) {
			$form_settings = unserialize($post_meta['form_settings'][0]);
		}
        ?>
        <style type="text/css">
        #acf_fb_form_settings .inside {
        	margin: 0;
        	padding: 0;
        }
        #acf_fb_form_settings .inside table tbody tr td div table tbody tr td {
        	padding: 4px;
        }
        </style>

        <?php if ( 'acf-field-group' == $post_type ) : // acf pro ?>
        <input type="hidden" id="acf_fb_is_pro" value="<?php echo $post_type; ?>">
    	<div class="acf-field">
    		<div class="acf-label">
				<label>Shortcode</label>
				<p>Copy and paste somewhere after you updated the field group</p>
			</div> 
			<div class="acf-input">
				<div class="rule-groups">
					<p>[cat_form group_id="<?php echo $post->ID; ?>"]</p>
				</div>
			</div>  
		</div>     	
    	<div class="acf-field show_form">
    		<div class="acf-label">
				<label>Form Display</label>
				<p>Choose 'This is a form' if you want to get a shortcode and paste its somewhere you want the form to display</p>
			</div>
			<div class="acf-input">
				<div class="rule-groups">
		    	<?php 								
					// create field
					acf_render_field(array(
						'type'		=> 'radio',
						'prefix'	=> "form_settings",
						'name'		=> 'display',
						'value'		=> isset($form_settings['display']) ? $form_settings['display'] : 'non_form',
						'choices'	=> array(
	                        'non_form' => 'This is not a form (default)',
	                        'form' => 'This is a form',
	                    ),
						'class'		=> 'show_form'
					));
		    	?>
		    	</div>
		    </div>
    	</div>
    	<?php if ( 'non_form' != $form_settings['display'] && isset($form_settings['display'])) : ?>
			<?php $this->acf_fb_add_action_select_field($form_settings, $post_type); ?>
		<?php endif; ?>
    	<script type="text/javascript">
		if( typeof acf !== 'undefined' ) {
				
			acf.postbox.render({
				'id': 'acf_fb_form_settings',
				'label': 'left'
			});	

		}
		</script>
        <?php elseif ( 'acf' == $post_type ) : // acf free ?>
        <input type="hidden" id="acf_fb_is_pro" value="<?php echo $post_type; ?>">
        <table class="acf_input widefat" id="acf_form_settings">
			<tbody>
		        <tr class="show_form">
					<td class="label">
						<label for="post_type">Shortcode</label>
						<p class="description">Copy and paste somewhere after you updated the field group</p>
					</td>
		            <td>
		            	<p>[cat_form group_id="<?php echo $post->ID; ?>"]</p>
		            </td>		        	
		        </tr>
				<tr class="show_form">
					<td class="label">
						<label for="post_type">Form Display</label>
						<p class="description">Choose 'This is a form' if you want to get a shortcode and paste its somewhere you want the form to display</p>
					</td>
		            <td>
		                <?php
		                do_action('acf/create_field', array(
		                    'type' => 'radio',
		                    'name' => 'form_settings[display]',
		                    'value' => isset($form_settings['display']) ? $form_settings['display'] : 'non_form',
		                    'choices' => array(
		                        'non_form' => 'This is not a form (default)',
		                        'form' => 'This is a form',
		                    ),
		                    //'layout' => 'horizontal',
		                ));
		                ?>
		            </td>
		        </tr> 
		    <?php if ( 'non_form' != $form_settings['display'] && isset($form_settings['display'])) : ?>
				 <?php $this->acf_fb_add_action_select_field($form_settings, $post_type); ?>
		    <?php endif; ?>
	        </tbody>
        </table> 
    	<?php endif; ?>

        <?php

}

	public function acf_fb_add_action_select_field($form_settings=null, $post_type=null) {

		global $post;

		$custom_actions = isset($form_settings['default_actions']) ? $form_settings['default_actions'] : 'create_post';

		if (is_null($post_type)) $post_type = $_POST['acf_fb_is_pro'];

		?>
        <?php if ( 'acf-field-group' == $post_type ) : // acf pro ?>    	
    	<div class="acf-field" id="hide-form">
    		<div class="acf-label">
				<label>Actions</label>
				<p>Choose an action you want to do after the form is submitted</p>
			</div>
			<div class="acf-input">
				<div class="">
					<div class="">
						<table class="acf-table -clear" id="acf_form_show_custom">
							<tbody>
								<tr>
						            <td class="show_custom" style="width: 50%">
						                <?php 								
											// create field
											acf_render_field(array(
												'type'		=> 'select',
												'prefix'	=> "form_settings",
												'name'		=> 'default_actions',
												'value'		=> isset($form_settings['default_actions']) ? $form_settings['default_actions'] : 'create_post',
												'choices'	=> array(
						                        	'create_post' => 'Create POST',
						                        	'custom_actions' => 'Custom Actions',
							                    )
											));
								    	?>
						            </td>
						            <td class="custom_actions">
						            <?php 
						            	$this->acf_fb_add_custom_action_select_field($custom_actions, $post_type);
						                ?>
						            </td>					      
					            </tr>
				            </tbody>
			            </table>
		    		</div>
		    	</div>
		    </div>
    	</div>
		<?php elseif ( 'acf' == $post_type ) : // acf free ?>
		<tr>
			<td class="label">
				<label for="post_type">Actions</label>
				<p class="description">Choose an action you want to do after the form is submitted</p>
			</td>
			<td>	
				<div>					
					<table class="acf_input widefat" id="acf_form_show_custom">
						<tbody>
							<tr>
					            <td class="show_custom" style="width: 50%">
					                <?php
					                do_action('acf/create_field', array(
					                    'type' => 'select',
					                    'name' => 'form_settings[default_actions]',
					                    'value' => isset($form_settings['default_actions']) ? $form_settings['default_actions'] : 'create_post',
					                    'choices' => array(
				                        	'create_post' => 'Create POST',
				                        	'custom_actions' => 'Custom Actions'
					                    ),
					                    'layout' => 'horizontal'
					                ));
					                ?>
					            </td>
					            <td class="custom_actions">
					            <?php 
					            	$this->acf_fb_add_custom_action_select_field($custom_actions, $post_type);
					                ?>
					            </td>					      
				            </tr>
			            </tbody>
		            </table>	
	            </div>		            
	        </td>
        </tr>
        <?php endif; ?>
		<?php
	}

	public function acf_fb_add_custom_action_select_field($custom_actions=null, $post_type=null) {

		global $post;
		
		$post_meta = get_post_meta($post->ID);

		if (is_null($post_type)) $post_type = $_POST['acf_fb_is_pro'];

		if (isset($post_meta['form_settings'])) {
			$form_settings = unserialize($post_meta['form_settings'][0]);
		}

		$custom_fields = array();

		add_filter('acf_fb_custom_actions_hook', array($this,'acf_fb_get_custom_actions'), 999, 2);

		if ('custom_actions' == $custom_actions) {
			$custom_fields = apply_filters('acf_fb_custom_actions_hook', $actions);
		} elseif ('create_post' == $custom_actions) {
			$custom_fields = get_post_types();		
		}
		
		if ( 'acf-field-group' == $post_type ) : // acf pro 
			// create field
			acf_render_field(array(
				'type'		=> 'select',
				'prefix'	=> "form_settings",
				'name'		=> 'custom_actions',
				'value'		=> isset($form_settings['custom_actions']) ? $form_settings['custom_actions'] : 1,
				'choices'	=> $custom_fields,
				'layout' => 'horizontal'
                )
			);
		elseif ('acf' == $post_type) : //acf free
	        do_action('acf/create_field', array(
	            'type' => 'select',
	            'name' => 'form_settings[custom_actions]',
	            'value' => isset($form_settings['custom_actions']) ? $form_settings['custom_actions'] : 1,
	            'choices' => $custom_fields,
	            'layout' => 'horizontal',
	        )); 
    	endif;

	}

	public function acf_fb_save_form_settings_meta() {//echo '<pre>'; var_dump($_POST); die;

		$error = false;

		$post_id = $_POST['post_ID'];

		if (isset($_POST['acf_field_group'])) $locations = $_POST['acf_field_group']['location'];
		else $locations = $_POST['location'];

	    // check if post type is acf and have form_settings in $post data
	    if ( ('acf' == $_POST['post_type'] || 'acf-field-group' == $_POST['post_type']) && isset($_POST['form_settings']) ) {

    		$is_post = false;

	    	if ( 'create_post' == $_POST['form_settings']['default_actions'] ) {
	    		foreach ($locations as $key => $rules) {
	    			foreach ($rules as $key => $rule) {
	    				$default_action = $_POST['form_settings']['custom_actions'];
	    				if ($default_action == $rule['value']) {
							$is_post = true;
	    				}
	    			}
	    		}

	    		if (!$is_post)
	    			$error = new WP_Error('Error_Location', 'Location error: Please select Post Type is equal to post type if you choose Create Post action');
	    	} 
	    }

	    if ($error) {
	        set_transient("acf_fb_save_post_errors_{$post_id}", $error, 45);

	        return false; 
	    }

		if ( isset($_POST['form_settings'])) {
			update_post_meta( $_POST['post_ID'], 'form_settings', $_POST['form_settings'] );
		}
	    return true;
	}

	public function acf_fb_handle_error_before_save() {

		global $post;

		$post_id = $post->ID;

		if ( $error = get_transient( "acf_fb_save_post_errors_{$post_id}" ) ) {

		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('table#acf_location tbody tr td div.location-groups').append('<p style="color:red; margin-top: 10px">Please select Post Type is equal to post</p>');
				jQuery('div#acf-field-group-locations div.inside div.acf-field div.acf-input').append('<p style="color:red; margin-top: 10px">Please select Post Type is equal to post type in your Create Post\'s select box action</p>');
				jQuery('#message').attr('style', 'display:none');
			});
		</script>
		    <div class="error">
		        <p><?php echo $error->get_error_message(); ?></p>
		    </div>
		<?php
		    delete_transient("acf_fb_save_post_errors_{$post_id}");
		}

	}

	function acf_fb_get_custom_actions($args) {
		
		global $wp_filter;

		$functions = array();

		// get all callbacks func hooked to the tag 'acf_fb_custom_actions' hook
		$acf_fb_custom_actions = $wp_filter['acf_fb_custom_actions']->callbacks;

		$replace_arr = array(
			'acf_fb_custom_actions_' => '', // remove default custom function refix
			'_' => ' ');					// replace "_" letter to " " letter

		// convert function name
		foreach ($acf_fb_custom_actions as $priority => $callbacks) {
			foreach ($callbacks as $key => $callback) {
				if (is_array($callback['function'])) {
					$name = ucwords( strtr( $callback['function'][1], $replace_arr ));					
					$functions[$callback['function'][1]] = $name;
				} else {
					$name = ucwords( strtr( $callback['function'], $replace_arr ));
					$functions[$callback['function']] = $name;
				}
			}
			
		}

		return $functions;
	}
}