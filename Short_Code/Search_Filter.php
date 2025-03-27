<?php

namespace Mindmycat\Short_Code;

use Mindmycat\Helper;
use Mindmycat\Model\Page;
use Mindmycat\Model\Services;
use Mindmycat\Model\Taxonomy;

class Search_Filter 
{

    public function init()
    {
        add_shortcode( 'pet_sitter_search_filter', [$this, 'callback'] );
    }

    public function callback()
    {

        $all_service = Services::getAllServiceInfoWithPricing();
        $locations = Taxonomy::getAllLocationAndPluck();

        $form_submitter = $_GET['filter_idd'] ?? false;

        ob_start();

        ?>
        <div id="search-filters-root"></div>

        <?php


        if($form_submitter) {

            echo '<div class="user_filter_form">';
            echo '<h2>Search Result</h2>';
            echo '</div>';

            $submitted = get_option($form_submitter);

            ?>
            
            <div class="flx-cont">
                <div class="boom-col">

                    <form class="boom-form">

                        <div class="inp-cnt">
                            <label>Location:</label>
                            <input type="text" name="service_area" value="<?php echo $submitted['service_area']; ?>" readonly >
                        </div>

                        <div class="inp-cnt">
                            <label>Date:</label>
                            <label>
                                <input type="date" name="start_date" value="<?php echo $submitted['start_date']; ?>" readonly>
                            </label>
                            <label style="max-width: 30px;text-align: center;"> To </label>
                            <label>
                                <input type="date" name="end_date" value="<?php echo $submitted['end_date']; ?>" readonly>
                            </label>
                        </div>

                        <div class="inp-cnt">
                            <label>Service:</label>
                            <input type="text" name="service_area" value="<?php echo $all_service[$submitted['services']]['post_title']; ?>" readonly >
                        </div>

                        <?php

                        $pet_numbers = $submitted['pet_numbers'];

                        foreach($pet_numbers as $slug => $number) {  ?>

                            <div class="inp-cnt">
                                <input type="text" name="pet_number" value="<?php echo $number; ?>" readonly >
                                <label><?php echo $submitted['pet_titles'][$slug]['name']; ?>(s)</label>
                            </div>

                            <?php   
                        } ?>


                        <div class="inp-cnt">
                            <label>Instructions:</label>
                            <input type="text"  value="<?php echo $submitted['pet_condition']; ?>" readonly >
                        </div>
                        
                        <div class="inp-cnt">
                            <label>Duration (per day):</label>
                            <input type="text"  value="<?php echo Services::getDurationFromPricingIndex($submitted['duration'], $all_service[$submitted['services']]['_pricing']['prices']); ?>" readonly >
                        </div>
                        
                        <!-- todo - Edit icon will be here -->

                    </form>

                </div>

            </div>

            <script>

                jQuery(document).ready(function($){
                    console.log('hii');
                });
            </script>


            <?php

        } else { ?>
            

        <div class="user_filter_form">

            <form id="requirement_form" style="display: block;">

                <label>Location:
                    <input type="text" name="service_area" id="service_area" list="service_location_list" >

                    <datalist id="service_location_list"> 
                        <?php Helper::print_datalist($locations); ?>
                    </datalist>
                </label>

                <label> Start & end date: </label>
                <div class="inp-cnt">
                    <label>
                        <input type="date" name="start_date" required placeholder="Start date">
                    </label>
                    <label style="max-width: 30px;text-align: center;"> To </label>
                    <label>
                        <input type="date" name="end_date" required placeholder="End date">
                    </label>
                </div>


                <label for="service_select">Select Service:</label>
                <select name="service_select" id="service_selected">
                    <option value="">-- Select Service --</option>

                    <?php Helper::print_service_list($all_service); ?>
                </select>


                <div style="padding: 10px 05px; margin: 5px 0;border: dashed 1px grey" id="result_box">
                    <span>How many pets</span>
                    <br>

                    <div id="pet_number_section"></div>

                    <div>
                        <label>Any specific condition ?</label>
                        <input type="text" name="pet_condition" />
                    </div>
                    <br>

                    <label for="service_select"> Duration( per day):
                        
                        <select name="duration_per_day" id="duration_per_day">
                            <option value="">-- Select Duration --</option>
                        </select>
                    </label>

                </div>  

                <button type="button" id="save_requirement" class="btn-submit"> Search </button>

            </form>

        </div>

        <script>

            let allServiceData = <?php echo (empty($all_service) ? '{}' : json_encode($all_service)) ?>;
            let findSitterPageUri = '<?php echo Page::get_find_sitter_page_url(); ?>';
            let currentPageUri = '<?php echo Page::get_current_page_url(''); ?>';
            
            let petList = [];
            let timeInfo = {};


            jQuery(document).ready(function($){

                $('#service_selected').on('change', function() {

                    var selectedService = $(this).val();

                    petList = updatePetListOnServiceChange(selectedService, allServiceData, $);

                    timeInfo = updateDurationOnServiceChange(selectedService, allServiceData, $);
                });


                $('#save_requirement').click(function(evt) {

                    evt.preventDefault();

                    submitBtnDisabler(true, evt, $);


                    let serviceArea = $('input[name="service_area"]').val();

                    let startDate = $('input[name="start_date"]').val();
                    let endDate = $('input[name="end_date"]').val();
                    let services = $('#service_selected').val() || '';
                    let timeDuration = $('#duration_per_day').val();
                    let hasCondition = $('input[name="has_condition"]').is(":checked");

                    let condition = $('input[name="pet_condition"]').val();

                    if(services === '') {

                        alert('Please select a service');
                        return;
                    }


                    if(startDate == '' || endDate == '') {

                        alert('Please select a date range');
                        return;
                    }

                    //console.log(serviceArea, startDate, endDate, services, timeDuration, hasCondition, condition);

                    if(serviceArea == '') {

                        alert('Please select a location');
                        return;
                    }
                    

                    let petNumbers = {};
                    let petTitles = {};
                    let userReqIdd = generateUserRequestString();
                    let serviceTitle = allServiceData[services]['post_title'];

                    if(petList.length) {

                        petList.forEach(pet => {

                            petNumbers[pet['slug']] = $('input[name="pet_number_' + pet['slug'] + '"]').val() || 0;

                            petTitles[pet['slug']] = {
                                name: pet['name'],
                                slug: pet['slug']
                            }
                        });
                    }

                    petNumbers['others'] = $('input[name="pet_number_others"]').val() || 0;

                    petTitles['others'] = {
                                name: 'Other',
                                slug: 'others'
                            };


                    $.post(
                            '<?php echo admin_url('admin-ajax.php'); ?>',
                            { action: 'mmc_search_submit',  
                                data: {
                                    service_area: serviceArea, 
                                    start_date: startDate, 
                                    end_date: endDate, 
                                    pet_numbers: petNumbers, 
                                    services: services,
                                    duration: timeDuration,
                                    has_condition: hasCondition,
                                    pet_condition: condition,
                                    pet_titles: petTitles,
                                    req_idd: userReqIdd,
                                    duration_in_minutes: timeInfo[timeDuration],
                                    service_title: serviceTitle,
                                } 
                            },
                            function(response, status) {

                                if(status === 'success') {

                                window.location.href = currentPageUri + '=' + userReqIdd; // todo - edgecases

                                } else {
                                    console.log(response);
                                }

                                submitBtnDisabler(false, evt, $);
                            }
                        );
                });
            });


        </script>  <?php

        } ?>



        <div id="search-results"></div>

        
        <?php

        return ob_get_clean();

    }
}
