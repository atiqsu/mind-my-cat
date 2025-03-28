<?php

namespace Mindmycat\Handler;

use Mindmycat\Config;
use Mindmycat\Helper;
use Mindmycat\Model\Contract;

class Woo_Thank_You {

    public function __construct() {
        add_action('woocommerce_thankyou', [$this, 'append_instruction'], 10, 1 );
    }

    public function append_instruction($order_id) {
        
        $order = wc_get_order( $order_id );
    
        if ( $order ) {

            $contract = Contract::getContractByOrderId( $order_id);

            if ( empty($contract) ) {
    
                echo 'contract not found';

                return;
                
            } 
            
            Contract::update(['status' => Config::CONTRACT_STATUS_PREVISIT_FEE_DEPOSITED], ['id' => $contract->id]);
    
            
            $contract_url = Helper::get_view_contract_url($contract->id);

            echo '<div class="my-custom-content">';
            echo '<h2> Pet booking is successful!</h2>';
            echo '<p>Here is some additional information or instructions:</p>';
            echo '<ul>';
            echo '<li>We will process your order shortly.</li>';
            echo '<li>Please check detail from here <a href="'. esc_url($contract_url) .'">My Orders</a>.</li>';
            echo '</ul>';
            echo '<p>You will be redirected to your orders page in <span id="countdown">20</span> seconds...</p>';
            echo '</div>';

            
            // Add JavaScript for countdown and redirect
            echo '<script>
                function startCountdown() {
                    let timeLeft = 20;
                    const countdownElement = document.getElementById("countdown");
                    
                    const countdownInterval = setInterval(function() {
                        timeLeft--;
                        countdownElement.textContent = timeLeft;
                        
                        if (timeLeft <= 0) {
                            clearInterval(countdownInterval);
                            window.location.href = "' . esc_url($contract_url) . '";
                        }
                    }, 1000);
                }
                
                // Start countdown when page loads
                window.addEventListener("load", startCountdown);
            </script>';
        }
    }
}
