<?php

namespace Mindmycat\Model;

use Mindmycat\Pages\Front_Page;

class Page
{

    public static function get_find_sitter_page_url()
    {
        $page_id = Front_Page::get_finder_page_id();

        return get_permalink( $page_id );
    }

    public static function get_user_filter($rIdd)
    {
        return get_transitent($rIdd);
    }

    public static function get_current_page_url($arg = '', $query = 'filter_idd') 
    {

        $uri = home_url( $_SERVER['REQUEST_URI'] );

        $uri = add_query_arg( $query, $arg, $uri );

        return $uri;
    }
}

