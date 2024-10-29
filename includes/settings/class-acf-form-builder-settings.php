<?php

class Settings 
{
	public function __construct() {

		// once all plugins are loaded, figure out if we need to stub any functions
        add_action('plugins_loaded', array($this, 'initialize_functions'));
        add_action('acf/render_field_settings', array($this, 'add_field_display_label_pro'));
        add_action('acf/create_field_options', array($this, 'add_field_display_label'));
	}

    // determind which functions to use, based on what is available
    public function initialize_functions() {
    }

    // NON-PRO ONLY: add a field to the admin interface, that decides whether this field's label gets displayed on the frontend or not
    public function add_field_display_label($field) {
        ?>
        <tr class="field_display_label">
            <td class="label"><label>Form field type</label>
            <td>
                <?php
                do_action('acf/create_field', array(
                    'type' => 'radio',
                    'name' => 'fields[' . $field['name'] . '][form_field_type]',
                    'value' => isset($field['form_field_type']) ? $field['form_field_type'] : 'custom',
                    'choices' => array(
                        'custom' => 'Custom field',
                        'title' => 'Post title',
                        'content' => 'Post content',
                        'feature-image' => 'Featured Image',
                    ),
                    'layout' => 'horizontal',
                ));
                ?>
            </td>
        </tr>
        <?php
    }

    // PRO ONLY: add a field to the admin interface, that decides whether this field's label gets displayed on the frontend or not
    public function add_field_display_label_pro($field) {
        // required
        acf_render_field_wrap(array(
            'label' => 'Form field type',
            'type' => 'radio',
            'name' => 'form_field_type',
            'prefix' => $field['prefix'],
            'value' => isset($field['form_field_type']) ? $field['form_field_type'] : 'custom',
            'choices' => array(
                'custom' => 'Custom field',
                'title' => 'Post title',
                'content' => 'Post content',
                'feature-image' => 'Featured Image',
            ),
            'layout' => 'horizontal',
            'class' => 'field-display_location'
        ), 'tr');
    }
}
new Settings();