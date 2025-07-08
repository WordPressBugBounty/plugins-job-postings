<?php 

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class JobGetUploadedFile {

    public function __construct(){
        add_action( 'init', array($this, 'init_internal') );
        add_action( 'query_vars', array($this, 'query_vars') );
        add_action( 'template_redirect', array($this, 'handle_download_request') );
    }

    public function init_internal(){
        add_rewrite_rule( 'job-postings-get-file/([^/]*)/?', 'index.php?job_postings_get_file=$matches[1]', 'top' );
    }

    public function query_vars( $query_vars ){
        $query_vars[] = 'job_postings_get_file';
        return $query_vars;
    }

    public function handle_download_request() {
        global $wp_query;
        
        if ( isset( $wp_query->query_vars['job_postings_get_file'] ) ) {
            
            if ( ! is_user_logged_in() ) {
                auth_redirect();
            }
            
            $this->do_query( $wp_query->query_vars['job_postings_get_file'] );
            exit();
        }
    }

    public function do_query( $filename ){

            // Secure file directory
            $filedir = apply_filters('job-postings/uploaded-files-path', JOBPOSTINGSFILESDIR);
            
            // Sanitize filename to prevent directory traversal
            $filename = basename(sanitize_file_name($filename)); // Remove any directory components and sanitize filename
            
            $file = $filedir . $filename;
            $file = urldecode($file);
            
            // Verify the file is within allowed directory
            $real_file = realpath($file);
            $real_dir = realpath($filedir);
            
            if (!$filedir || !$real_file || !$real_dir || 
                strpos($real_file, $real_dir) !== 0 || 
                !is_file($real_file)) {
                status_header(404);
                die('404 &#8212; File not found.');
            }

            $mime = wp_check_filetype($file);
            if( false === $mime[ 'type' ] && function_exists( 'mime_content_type' ) )
                $mime[ 'type' ] = mime_content_type( $file );

            if( $mime[ 'type' ] )
                $mimetype = $mime[ 'type' ];
            else
                $mimetype = 'image/' . substr( $file, strrpos( $file, '.' ) + 1 );

            header( 'Content-Type: ' . $mimetype ); // always send this
            if ( false === strpos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS' ) )
                header( 'Content-Length: ' . filesize( $file ) );

            $last_modified = gmdate( 'D, d M Y H:i:s', filemtime( $file ) );
            $etag = '"' . md5( $last_modified ) . '"';
            header( "Last-Modified: $last_modified GMT" );
            header( 'ETag: ' . $etag );
            header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 100000000 ) . ' GMT' );

            // Support for Conditional GET
            $client_etag = isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) ? stripslashes( $_SERVER['HTTP_IF_NONE_MATCH'] ) : false;

            if( ! isset( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) )
                $_SERVER['HTTP_IF_MODIFIED_SINCE'] = false;

            $client_last_modified = trim( $_SERVER['HTTP_IF_MODIFIED_SINCE'] );
            // If string is empty, return 0. If not, attempt to parse into a timestamp
            $client_modified_timestamp = $client_last_modified ? strtotime( $client_last_modified ) : 0;

            // Make a timestamp for our most recent modification...
            $modified_timestamp = strtotime($last_modified);

            if ( ( $client_last_modified && $client_etag )
                ? ( ( $client_modified_timestamp >= $modified_timestamp) && ( $client_etag == $etag ) )
                : ( ( $client_modified_timestamp >= $modified_timestamp) || ( $client_etag == $etag ) )
                ) {
                status_header( 304 );
                exit;
            }

            // If we made it this far, just serve the file
            readfile( $file );


        exit();
    }

}
new JobGetUploadedFile();
