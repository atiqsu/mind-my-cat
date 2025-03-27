<?php

namespace Mindmycat\Short_Code;

use Mindmycat\Handler\Handle_Search_Result;
use Mindmycat\Helper;

class Search_Result 
{

    public function init()
    {
        add_shortcode( 'pet_sitter_search_result', [$this, 'callback'] );
    }

    public function callback()
    {

        if ( empty( $_GET['filter_idd'] ) ) {

            return;
        }

        $rIdd = $_GET['filter_idd'];

        $user_requirement = get_option($rIdd, []);

        if ( empty($user_requirement) ) {

            return '<h3>User requirement form not found... please go back and submit the requirement form</h3>';
        }


        ob_start();

        if(!empty($_GET['sitter_id'])) : ?>

            <div id="hiring-sitter-root"></div>  <?php 

        else : ?>

            <div class="mmc-search-results">  
            
            <?php 

            $currUri = Helper::get_current_page_url( );
            $sitters = Handle_Search_Result::search_pet_sitter($user_requirement);

            if(empty($sitters)) :  ?>

                <h3>No sitter found with given requirement</h3>

                <a href="<?php echo Helper::remove_filter_param($currUri, 'filter_idd'); ?>">Go back and submit the requirement form</a>

                <?php

            else :     

                foreach($sitters as $sitter) { ?>

                    <div class="profile_grid">
                        <img src="<?php echo esc_url($sitter['img']) ?>" alt="<?php echo $sitter['name'] ?>" width="100"/>
                        <br>
                        <strong><?php echo $sitter['name'] ?></strong>
                        <br>

                        <a href="<?php echo $currUri . '?filter_idd=' . $rIdd . '&sitter_id=' . $sitter['user_id'] ?>">View</a>

                    </div>

                    <?php
                }  

            endif; ?>
            </div>

        <?php

        endif;  
        

        return ob_get_clean();
    }
}





