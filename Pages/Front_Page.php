<?php

namespace Mindmycat\Pages;


class Front_Page 
{

	const PAGE_OPTION_KEY = 'find-pet-sitter-page-id';
    public $page_slug = 'find-pet-sitter-and-hire';


	public function create_or_update_page() {

		if ( $this->page_exists_by_slug() ) {

			return $this->change_status_to_publish();
		}

		return $this->create();
	}

	protected function page_exists_by_slug() {

		return get_page_by_path( $this->page_slug );
	}

	protected function change_status_to_publish() {

		$page_exists = get_page_by_path( $this->page_slug );

		if ( $page_exists ) {

			$page_data = array(
				'ID'          => $page_exists->ID,
				'post_status' => 'publish',
			);

			wp_update_post( $page_data );
		}

	}

	public function change_status_to_draft() {

		$page_exists = get_page_by_path( $this->page_slug );

		if ( $page_exists ) {

			$page_data = array(
				'ID'          => $page_exists->ID,
				'post_status' => 'draft',
			);

			wp_update_post( $page_data );
		}
	}


	protected function create() {

        $content = "[pet_sitter_search_filter] \n\n [pet_sitter_search_result]";
	    
        $page_data = array(
			'post_type'   => 'page',
			'post_status' => 'publish',
            'post_title'   => esc_html__('Find pet sitter and hire', 'petcare-service'),
			'post_name'   => $this->page_slug ,
			'post_content' => $content,
			'meta_input'  => array(
				'_wp_page_template' => 'default', 
			),
		);

		add_post_type_support( 'page', 'editor' );

		$page_id = wp_insert_post( $page_data );

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			
			update_option( self::PAGE_OPTION_KEY, $page_id );
		}

		return $page_id;

    }

	public static function get_finder_page_id() {
        return get_option( self::PAGE_OPTION_KEY );
    }

    public static function update_finder_page_id( $page_id ) {
        update_option( self::PAGE_OPTION_KEY, $page_id );
    }
}