<?php 
/*
	Small job preview template


	VSRIABLES:
	
	$post_id
	$btn_name
	$position_title
	$job_location
	$employment_type
	$permalink
	$preview_location
	$preview_employment_type
	$custom_message
*/


$info = '';
if( $job_location && $preview_location != 'on' ) {
	$info .= apply_filters( 'jobs/preview_details_jobLocation', '<span>'.$job_location . '</span>');
}
if( $employment_type && $preview_employment_type != 'on'  ) {
	$coma = '';
	if( $info != '' ) $coma = '<span class="job-preview-details-separator">, <span>';

	$coma = apply_filters( 'jobs/preview_details_separator', $coma );

	$info .= $coma; 
	$info .= apply_filters( 'jobs/preview_details_employmentType', '<span>' . $employment_type . '</span>');
}

$info = apply_filters( 'jobs/preview_details', $info, $post_id );

$custom_message = apply_filters( 'jobs/preview_custom_message', $custom_message, $post_id );

$custom_message_el = '';
if( $custom_message ){
	$custom_message_el = '<div class="job_custom_message">'.$custom_message.'</div>';
}

$aria_label = sprintf(__('View job: %s', 'job-postings'), $position_title);
$view_button = '<a href="'.$permalink.'" target="'.$target.'" class="apply-btn local" aria-label="'.esc_attr($aria_label).'">'.$btn_name.'</a>';
$view_button = apply_filters('job-postings/view_button', $view_button, $btn_name, $permalink, $target, $post_id);

$out .= '<article class="job-preview clearfix" role="article" aria-labelledby="job-title-'.$post_id.'">';

	$out .= '<div class="job-content">';
		$out .= '<h5><a href="'.$permalink.'" id="job-title-'.$post_id.'" target="'.$target.'"><span>'.$position_title.'</span></a></h5>';
		$out .= '<div class="job-additional-information" aria-label="'.esc_attr__('Additional job information', 'job-postings').'">';
			$out .= $info;
			if($custom_message_el) $out .= $custom_message_el;
		$out .= '</div>';
	$out .= '</div>';

	$out .= '<div class="job-cta">';
		$out .= $view_button;
	$out .= '</div>';
	
$out .= '</article>';

// 
// NB! No ooutput/write here. We return output ($out) from the function.
//