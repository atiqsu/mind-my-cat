<?php

namespace Mindmycat\Handler;

use Mindmycat\Config;
use Mindmycat\Model\Contract;
use Mindmycat\Model\Users;
use Mindmycat\Model\WooCom;

class Contract_View_Page_In_My_Acc {

    public function __construct() {
        add_action('woocommerce_account_view-contract_endpoint', [$this, 'render_view_contract_tab']);
    }

    public function render_view_contract_tab() {
        
        $contract_id = get_query_var( 'view-contract' );

        if ( empty($contract_id) ) {
            echo '<h1>Contract not found</h1>';
            return;
        }

        $contract = Contract::find( $contract_id );

        if ( empty($contract) ) {
            echo '<h1>Contract not found</h1>';
            return;
        }

        $current_user = wp_get_current_user();

        if ( $contract->owner_id == $current_user->ID ) {

            $this->get_owner_view( $contract, $current_user );

        } elseif ( $contract->sitter_id == $current_user->ID ) {
            
            $this->get_sitter_view( $contract, $current_user );

        } elseif ( Users::hasAdminRole($current_user) ) {

            echo '<h1>Hello Admin!</h1>';

        } else {
            echo '<h1>You are not authorized to view this contract</h1>';
            return;
        }
    }

    protected function get_owner_view( $contract, $current_user ) {

        $sitter = get_user_by( 'id', $contract->sitter_id );

        $order1 = WooCom::getOrder( $contract->order_id );
        $order2 = WooCom::getOrder( $contract->order_id2 );

        ?>
        <div class="grid_slot">
            <h2>Contract Details</h2>
            <p>Contract ID: <?php echo $contract->id; ?></p>
            <p>Contract Status: <span style="color: blue;"> <?php echo Config::getContractStatuses($contract->status); ?></span></p>

            <div class="flx-cont">
                <div class="grid_slot_inner boom-col">
                    <h2>Owner Details</h2>
                    <p>Name: <?php echo $current_user->user_nicename; ?></p>
                    <p>Email: <?php echo $current_user->user_email; ?></p>
                    <p>Phone: <?php echo $current_user->user_phone; ?></p>
                </div>

                <div class="grid_slot_inner boom-col">
                    <h2>Sitter Details</h2>
                    <p>Name: <?php echo $sitter->user_nicename; ?></p>
                    <p>Email: <?php echo $sitter->user_email; ?></p>
                    <p>Phone: <?php echo $sitter->user_phone; ?></p>
                </div>
            </div>
        </div>

        <?php

        if ( empty($order1) ) {

            ?>
            <div class="grid_slot">
                <h2>Order for pre-consultation fee not found</h2>
                <p>Order ID: <?php echo $contract->order_id; ?></p>
            </div>
            <?php

        } else {

            switch ( $contract->status ) {

                case Config::CONTRACT_STATUS_READY_FOR_PREVISIT_DEPOSIT:
                    ?>
                    <div class="grid_slot">
                        <h2>Cotract is ready for pre-visit fee deposit</h2>
                        <p>Please deposit the pre-visit fee to continue</p>
                        <p>Order ID: <?php echo $contract->order_id; ?></p>
                        <p> Order status: <u> <?php echo $order1->get_status(); ?> </u> </p>
                        <p> <a href="<?php echo $order1->get_checkout_payment_url(); ?>" target="_blank">Pay Now</a></p>
                    </div>
                    <?php
                    break;

                case Config::CONTRACT_STATUS_PREVISIT_FEE_DEPOSITED:
                        ?>
                        <div class="grid_slot">
                            <h2>You have deposited the pre-visit fee</h2>
                            <p>Waiting for you to schedule a date for previsit</p>

                            <p>
                                <input type="date" name="mmc_previsit_date" id="mmc_previsit_date" placeholder="Select Date">
                                <input type="hidden" name="mmc_contract_id" id="mmc_contract_id" value="<?php echo $contract->id; ?>">
                                <button id="mmc_schedule_previsit" class="btn-submit">Schedule Previsit</button>
                            </p>

                        </div>
                        <?php
                        break;

                case Config::CONTRACT_STATUS_PREVISIT_SCHEDULED:
                    ?>
                    <div class="grid_slot">
                        <h2>Pre-visit date is scheduled</h2>
                        <p>Pre-visit date: <?php echo $contract->previsit_date; ?></p>
                        <p>Waiting for the sitter to confirm or reject the date</p>
                    </div>
                    <?php
                    break;

                case Config::CONTRACT_STATUS_SITTER_REJECTED:
                    ?>
                    <div class="grid_slot">
                        <h2>Sitter has rejected the pre-visit date</h2>
                        <p>Please re-schedule a new date</p>

                        <p>
                            <input type="date" name="mmc_previsit_date" id="mmc_previsit_date" placeholder="Select Date">
                            <input type="hidden" name="mmc_contract_id" id="mmc_contract_id" value="<?php echo $contract->id; ?>">
                            <button id="mmc_schedule_previsit" class="btn-submit">Schedule Previsit</button>
                        </p>

                        </div>
                    <?php
                    break;

                case Config::CONTRACT_STATUS_SITTER_ACCEPTED:

                    $schedule = json_decode( $contract->schedule, true );

                    ?>
                    <div class="grid_slot">
                        <h2>Sitter has accepted the pre-visit date</h2>
                        <p>Pre-visit date: <?php echo $contract->previsit_date; ?></p>

                        <?php

                        if ( !empty($schedule) ) {

                            $last_date = '';

                            ?>
                            
                            <table class="calendar-table">
                                <tr>
                                    <th>Day</th>
                                    <th>Slots</th>
                                </tr>
                                <?php   
                                foreach ( $schedule as $day => $slots ) {
                                    ?>
                                    <tr>
                                        <td><?php echo $day; ?></td>
                                        <td><?php 
                                            foreach ( $slots as $slot ) {
                                                echo '<div class="grid_slot_inner">' . date('H:i A', $slot['start']) . ' - ' . date('H:i A', $slot['end']) . '</div>';
                                            }
                                        ?></td>
                                    </tr>
                                    <?php

                                    if ( $last_date < $day ) {

                                        $last_date = $day;
                                    }
                                }
                                ?>
                            </table>
                            <?php

                            $toady = date('Y-m-d');

                            if ( $last_date < $toady ) { ?>

                                <div class="grid_slot_inner_inner">
                                    <p>The service should have been ended by now. Please contact the sitter to start the service.</p>
                                </div>  

                                <?php
                            }
                        } ?>

                    </div>
                    <?php

                    break;  

                case Config::CONTRACT_STATUS_SESSION_STARTED:

                    $schedule = json_decode( $contract->schedule, true );

                    ?>
                    <div class="grid_slot">
                        <h2>Session is started</h2>
                    </div>
                    <?php
                    break;

                case Config::CONTRACT_STATUS_SESSION_ENDED:
                    ?>
                    <div class="grid_slot">
                        <h2>Session is ended</h2>
                    </div>
                    <?php
                    break;

                case Config::CONTRACT_STATUS_COMPLETED:
                    ?>
                    <div class="grid_slot">
                        <h2>Contract is completed</h2>
                    </div>
                    <?php
                    break;

                case Config::CONTRACT_STATUS_CANCELLED:
                    ?>
                    <div class="grid_slot">
                        <h2>Contract is cancelled</h2>
                    </div>
                    <?php
                    break;

                default:
                    ?>
                    <div class="grid_slot">
                        <h2>Pre-visit fee is already deposited</h2>
                        <p>Contract status is <?php echo Config::getContractStatuses($contract->status); ?></p>
                    </div>
                    <?php
                    break;
            }
        }
    }

    protected function get_sitter_view( $contract, $current_user ) {

        $owner = get_user_by( 'id', $contract->owner_id );
        $order1 = WooCom::getOrder( $contract->order_id );
        $order2 = WooCom::getOrder( $contract->order_id2 );

        ?>

        <div class="grid_slot">
            <h2>Contract Details</h2>
            <p>Contract ID: <?php echo $contract->id; ?></p>
            <p>Contract Status: <span style="color: blue;"> <?php echo Config::getContractStatuses($contract->status); ?></span></p>

            <div class="flx-cont">
                <div class="grid_slot_inner boom-col">
                    <h2>Owner Details</h2>
                    <p>Name: <?php echo $owner->user_nicename; ?></p>
                    <p>Email: <?php echo $owner->user_email; ?></p>
                    <p>Phone: <?php echo $owner->user_phone; ?></p>
                </div>

                <div class="grid_slot_inner boom-col">
                    <h2>Sitter Details</h2>
                    <p>Name: <?php echo $current_user->user_nicename; ?></p>
                    <p>Email: <?php echo $current_user->user_email; ?></p>
                    <p>Phone: <?php echo $current_user->user_phone; ?></p>
                </div>
            </div>
        </div>

        <?php

        switch ( $contract->status ) {

            case Config::CONTRACT_STATUS_READY_FOR_PREVISIT_DEPOSIT:    
                ?>
                <div class="grid_slot">
                    <p> User has not deposited the pre-visit fee yet. Please wait for the user to deposit the fee. </p>
                    <p> More details after client discussion</p>
                </div>
                <?php
                break;

            case Config::CONTRACT_STATUS_PREVISIT_FEE_DEPOSITED:
                ?>
                <div class="grid_slot">
                    <p> User has deposited the pre-visit fee. Please wait for the owner to schedule a date for previsit. </p>
                    <p> More details after client discussion</p>
                </div>

                <div class="grid_slot">
                    <h3> User has deposited the pre-visit fee.</h3>
                    <p> Waiting for the owner to schedule a date for previsit </p>
                </div>

                <?php
                break;

            case Config::CONTRACT_STATUS_PREVISIT_SCHEDULED:
                ?>
                <div class="grid_slot">
                    <h2> Previsit date scheduled! </h2>
                    <p> Previsit date: <?php echo $contract->previsit_date; ?> </p>

                    <input type="hidden" name="mmc_contract_id" id="mmc_contract_id" value="<?php echo $contract->id; ?>">
                    <button id="mmc_confirm_previsit_date" class="btn-submit">Confirm Date</button>
                    <button id="mmc_reject_previsit_date" class="btn-reject">Reject Date</button>
                </div>
                <?php
                break;

            case Config::CONTRACT_STATUS_SITTER_REJECTED:
                ?>
                <div class="grid_slot">
                    <p> You have rejected the previsit date. Please wait for the owner to schedule a new date. </p>
                </div>

                <div class="grid_slot">
                    <h3> You have rejected the previsit date! </h3>
                    <p> Please wait for the owner to schedule a new date </p>
                </div>
                <?php
                break;

            case Config::CONTRACT_STATUS_SITTER_ACCEPTED:
                
                $order1_status = WooCom::getOrderStatus( $contract->order_id );
                $order2_status = WooCom::getOrderStatus( $contract->order_id2 );

                ?>
                <div class="grid_slot">
                    <h3> You have accepted the previsit date! </h3>
                    <p> Pre-visit Date: <?php echo $contract->previsit_date; ?> </p>
                    <p> Pre-visit order status: <u> <?php echo $order1_status; ?> </u> </p>
                    <p> Post-visit order status: <u> <?php echo $order2_status; ?> </u> </p>
                </div>
                <?php

                if ( $order2_status == 'completed' ) {
                    ?>
                    <div class="grid_slot_inner">
                        <input type="hidden" name="mmc_contract_id" id="mmc_contract_id" value="<?php echo $contract->id; ?>">
                        <button id="mmc_session_start" class="btn-submit">Start session</button>
                    </div>
                    <?php

                } else {

                    ?>
                    <div class="grid_slot_inner">
                        <p> Post-visit order is not completed yet. </p>
                        <button disabled class="btn-submit">Start session</button>
                    </div>
                    <?php
                }
                
                break;

            case Config::CONTRACT_STATUS_SESSION_STARTED:

                $order1_status = WooCom::getOrderStatus( $contract->order_id );
                $order2_status = WooCom::getOrderStatus( $contract->order_id2 );

                $meta = json_decode( $contract->metadata, true );
                $session_started_on = $meta['session_started_on'];

                ?>
                <div class="grid_slot">
                    <h3> You have accepted the previsit date! </h3>
                    <p> Pre-visit Date: <?php echo $contract->previsit_date; ?> </p>
                    <p> Pre-visit order status: <u> <?php echo $order1_status; ?> </u> </p>
                    <p> Post-visit order status: <u> <?php echo $order2_status; ?> </u> </p>
                </div>

                <div class="grid_slot">
                    <h3> Session is started! </h3>
                    <p> started on: <?php echo date('Y-m-d H:i:s', $session_started_on); ?> </p>

                    <div class="grid_slot_inner">
                        <input type="hidden" name="mmc_contract_id" id="mmc_contract_id" value="<?php echo $contract->id; ?>">
                        <button id="mmc_session_end" class="btn-submit">End session</button>
                    </div>
                </div>
                <?php
                break;

            case Config::CONTRACT_STATUS_SESSION_ENDED:
                ?>
                <div class="grid_slot">
                    <p> Session is ended. Please wait for the owner to complete the contract. </p>
                </div>
                <?php
                break;

            case Config::CONTRACT_STATUS_COMPLETED:

                $order1_status = WooCom::getOrderStatus( $contract->order_id );
                $order2_status = WooCom::getOrderStatus( $contract->order_id2 );

                $meta = json_decode( $contract->metadata, true );
                $session_started_on = $meta['session_started_on'];
                $session_ended_on = $meta['session_ended_on']; 

                ?>
                <div class="grid_slot">
                    <h3> Contract is completed! </h3>
                    <p> Pre-visit Date: <?php echo $contract->previsit_date; ?> </p>
                    <p> Pre-visit order status: <u> <?php echo $order1_status; ?> </u> </p>
                    <p> Post-visit order status: <u> <?php echo $order2_status; ?> </u> </p>
                </div>

                <div class="grid_slot">
                    <p> Session started on: <?php echo date('Y-m-d H:i:s', $session_started_on); ?> </p>
                    <p> Session ended on: <?php echo date('Y-m-d H:i:s', $session_ended_on); ?> </p>
                </div>
                <?php
                break;

            case Config::CONTRACT_STATUS_CANCELLED:
                ?>
                <div class="grid_slot">
                    <p> Contract is cancelled. Please wait for the owner to confirm the cancellation. </p>
                </div>
                <?php
                break;

            default:
                ?>
                <div class="grid_slot"> 
                    <p> Contract status is <?php echo Config::getContractStatuses($contract->status); ?> </p>
                </div>
                <div class="grid_slot">
                    <h3> Not sure what is happening! </h3>
                    <p> contract status is <?php echo Config::getContractStatuses($contract->status); ?> </p>
                </div>
                <?php
                break;
        }
    }
}
