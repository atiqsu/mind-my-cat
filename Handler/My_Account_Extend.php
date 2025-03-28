<?php

namespace Mindmycat\Handler;

class My_Account_Extend {

    public function rewrite() { 

        add_rewrite_endpoint('pet-bookings', EP_ROOT | EP_PAGES | EP_PERMALINK);
        add_rewrite_endpoint( 'view-contract', EP_ROOT | EP_PAGES | EP_PERMALINK );

        return $this;
    }

    public function extend() {
        new Woo_Thank_You;
        new Pet_Booking_Tab_In_My_Acc;
        new Contract_View_Page_In_My_Acc;
    }
}
