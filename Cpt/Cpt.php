<?php

namespace Mindmycat\Cpt;

abstract class Cpt
{
    protected string $cpt_slug = 'new-mmc-cpt';

    protected function isPostTypeCorrect()
    {
        return isset( $_POST['post_type'] ) && $this->cpt_slug === $_POST['post_type'] ;
    }

    protected function isPostTypeCorrectByPostId( $post_id ) {

        return $this->cpt_slug == get_post_type( $post_id );
    }

    protected function isPostPublished( $post )
    {
        return $post->post_status == 'publish';
    }


   abstract protected function hasPermission($post_id);
}

