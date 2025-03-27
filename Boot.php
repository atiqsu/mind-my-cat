<?php

namespace Mindmycat;

use Mindmycat\Cpt\Services as Cpt_Services;
use Mindmycat\Model\Services;
use Mindmycat\Handler\Admin_Menu;
use Mindmycat\Handler\Ajax_Confirm_Previsit_Date;
use Mindmycat\Handler\Ajax_Get_Contract_Info;
use Mindmycat\Handler\Ajax_Get_Sitter_Info;
use Mindmycat\Handler\Ajax_Previsit_Fee_Deposit;
use Mindmycat\Handler\Ajax_Reject_Previsit_Date;
use Mindmycat\Handler\Ajax_Save_Owner_Requested_Schedule;
use Mindmycat\Handler\Ajax_Set_Previsit_Date;
use Mindmycat\Handler\Handle_Hiring;
use Mindmycat\Handler\Handle_Search_Submit;
use Mindmycat\Handler\Sitter_Finder;
use Mindmycat\Handler\Woocom_Handler;
use Mindmycat\Short_Code\Search_Filter;
use Mindmycat\Model\Taxonomy;
use Mindmycat\Short_Code\Contract_Details;
use Mindmycat\Short_Code\Search_Result;
use Mindmycat\Handler\Ajax_Session_Start;
use Mindmycat\Handler\Ajax_Session_End;

class Boot
{

    protected string $path;

    public function __construct(string $path)
    {

        do_action( 'mind_my_cat/before_loaded', $this );

        $this->path = $path;

        add_action( 'init', [$this, 'i18n'] );
        add_action( 'init', [$this, 'late_hooks'] );
        add_action( 'plugins_loaded', [$this, 'loaded'] );
        add_action( 'wp_enqueue_scripts', [$this, 'frontend_assets'] );
        add_action( 'admin_enqueue_scripts', [$this, 'admin_assets'] );

        Helper::set_plugin_url($path);

    }

    public function i18n(): void
    {
        load_plugin_textdomain(
            'mind-my-cat',
            false,
            plugin_dir_path(plugin_basename($this->path)) . '/languages/'
        );
    }

    public function loaded(): void
    {

        # CPTs
        (new Cpt_Services)->init();

        # All ajax handlers
        new Handle_Search_Submit;
        new Ajax_Get_Sitter_Info;
        new Ajax_Save_Owner_Requested_Schedule;
        new Ajax_Previsit_Fee_Deposit;
        new Ajax_Get_Contract_Info;
        new Ajax_Set_Previsit_Date;
        new Ajax_Confirm_Previsit_Date;
        new Ajax_Reject_Previsit_Date;
        new Ajax_Session_Start;
        new Ajax_Session_End;


        new Admin_Menu;
        //new Sitter_Finder;
        //new Service_Wanted;
        //(new Handle_Hiring)->init();



        // check if it is a AJAX/Heartbit Call....
        do_action( 'mind_my_cat/loaded', $this );
    }

    public function frontend_assets()
    {
        wp_enqueue_style( 'mmc_front_style', plugins_url( 'assets/style.css', $this->path ) );
	    wp_enqueue_script( 'mmc_front_script', plugins_url( 'assets/front.js', $this->path ), ['jquery'], '1.0.0', true );



        wp_localize_script( 'mmc_front_script', 'mmcFrontObj', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'mmc_front_script' ),
            'services' => Services::getAllServiceInfo(),  // todo - trim later
            'locations' => Taxonomy::getAllLocationAndPluck(),
            'rest' => [
                'url' => get_rest_url(),
                'nonce' => wp_create_nonce( 'wp_rest' ),
            ],
            'mmcUser' => [
                'isLoggedIn' => is_user_logged_in(),
                'userRole' => is_user_logged_in() ? wp_get_current_user()->roles[0] : '',
                'userIdd' => is_user_logged_in() ? wp_get_current_user()->ID : ''
            ]
        ] );

        $this->enq_react_asset();

    }

    public function enq_react_asset()
    {
        $asset_file = plugin_dir_path( __FILE__ ) . 'build/index.asset.php';

        if ( ! file_exists( $asset_file ) ) {
            return;
        }
    
        $asset = include $asset_file;

        wp_enqueue_style(
            'mmc_react_ast',
            plugins_url( 'build/style-index.css', __FILE__ ),
            array(),
            $asset['version']
        );
    
        wp_enqueue_script(
            'mmc_react_ast',
            plugins_url( 'build/index.js', __FILE__ ),
            $asset['dependencies'],
            $asset['version'],
            array(
                'in_footer' => true,
            )
        );
    }

    public function admin_assets()
    {
        wp_enqueue_script( 'mmc_admin_script', plugins_url( 'assets/admin.js', $this->path ), [], '1.0.0', true );
    }

    public function late_hooks()
    {

        # shortcodes ....... 
       (new Search_Filter)->init();
       (new Search_Result)->init();
//        (new Contract_Details)->init();
//
//
//        Woocom_Handler::add_endpoint();
//        new Woocom_Handler;
    }
}

