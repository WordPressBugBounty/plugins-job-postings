<?php

if( !class_exists('JobSearch') ){
    class JobSearch
    {
        public static function render_search(){

            $jobs_archive_page = get_option('jobs_archive_page_' . jfw_get_lang());
            $jobs_page_permalink = get_permalink( $jobs_archive_page );
    
            $category = isset($_GET['job-category']) ? sanitize_text_field($_GET['job-category']) : '';
            $search 	= isset($_GET['job-search']) ? sanitize_text_field($_GET['job-search']) : '';
    
            $search_id = 'job-search-input-' . wp_unique_id();
            $out = '';
            $out .= '<div class="jobs-search">';
                $out .= '<form method="GET" action="'.$jobs_page_permalink.'" role="search" aria-label="' . esc_attr__('Search for jobs', 'job-postings') . '">';
                    $out .= '<input type="hidden" value="'.esc_attr($category).'" name="job-category">';
                    $out .= '<label for="' . $search_id . '" class="sr-only">' . __('Search for jobs', 'job-postings') . '</label>';
                    $out .= '<input id="' . $search_id . '" class="job-search" type="text" placeholder="'.__('Vacancy Search', 'job-postings').'" value="'.esc_attr($search).'" name="job-search" aria-label="' . esc_attr__('Search for jobs', 'job-postings') . '">';
                    $out .= '<button type="submit" class="job-search-submit" aria-label="' . esc_attr__('Submit search', 'job-postings') . '">'.Job_Postings_Helper::getRawSvg('search.svg').'<span class="sr-only">' . __('Search', 'job-postings') . '</span></button>';
    
                $out .= '</form>';
            $out .= '</div>';
    
            return $out;
        }
    
        public static function do_job_search(){
            wp_enqueue_style('jp-front-styles');
            Job_Postings::customStyles();
            $out = '';
            $out .= '<div class="job-postings-shortcode-search">';
                $out .= self::render_search();
            $out .= '</div>';
            return $out;
        }
    }
}
