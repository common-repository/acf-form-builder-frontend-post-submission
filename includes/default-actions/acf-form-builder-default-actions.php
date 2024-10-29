<?php

function acf_fb_default_action_post($data, $wp_post_type=null) {

	if ( is_null($data) )
		return;

	// detect post type you want to create, ex: post, page
	$_wp_post_type = isset($wp_post_type) ? $wp_post_type : 'post';

	if (isset($data['acf'])) // acf pro version
		$fields = $data['acf'];
	else // acf free version
		$fields = $data['fields'];

	$fields_object = array();
	foreach ($fields as $key => $field) {
		$fields_object[$key] = get_field_object($key);	
	}

	$post_data = array();
	foreach ($fields_object as $key => $value) {
		$post_data[$key]['name'] = $value['name'];
		$post_data[$key]['field_type'] = $value['type'];
		$post_data[$key]['value'] = $fields[$key];
		$post_data[$key]['post_type'] = $value['form_field_type'];
	}

	$prepare = array();
	foreach ($post_data as $key => $value) {
		if ('title'==$value['post_type']) $prepare['title'] = $value['value'];
		if ('content'==$value['post_type']) $prepare['content'] = $value['value'];
		if ('feature-image'==$value['post_type']) $prepare['feature-image'] = $value['value'];
		if ('custom'==$value['post_type']) $prepare['custom'][$key] = $value['value'];
	}


	$new_post = array(
	  'post_title'    => wp_strip_all_tags( $prepare['title'] ),
	  'post_content'  => $prepare['content'],
	  'post_status'   => 'publish',
	  'post_type'     => 'post',
	  'post_author'   => 1,
	  'meta_input'    => $prepare['custom']
	);

	// create post and get post_id
	$post_id = wp_insert_post( $new_post, $wp_error );

	// set feture image
	set_post_thumbnail( $post_id, $prepare['feature-image'] );

	// update custom field: [type = text]
	foreach ($prepare['custom'] as $key => $value) {
		update_field( $key, $value, $post_id );
	}
}

