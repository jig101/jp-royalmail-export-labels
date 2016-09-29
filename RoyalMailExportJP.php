<?php
/*
Plugin Name: Royalmail_Export_orders
Description: This will allow you to export orders in csv format so they can be imported in the print system.
Plugin URI: http://www.jigerpatel.co.uk
Author: Jiger Patel
Author URI: http://www.jigerpatel.co.uk
Version: 1.0
License: GPL2
Text Domain: Text Domain
Domain Path: Domain Path
*/

/**
* 
*/
foreach ( glob( plugin_dir_path( __FILE__ ) . "inc/*.php" ) as $file ) {
    include_once $file;
}

if ( ! class_exists( 'WC_Royalmail_Export_orders' ) ) {

class WC_Royalmail_Export_orders
{
    
    function __construct()
    {
        # Add the scripts
        add_action( 'admin_enqueue_scripts', array($this,'addRMScripts') );
        # Add menu page
        add_action( 'admin_menu', array($this,'regRmJpMenuPage') );
        # Set the default Shipping provider to Royalmail
        add_filter( 'woocommerce_shipment_tracking_default_provider', array($this,'custom_woocommerce_shipment_tracking_default_provider'));
        # Download option (export CSV Todays processing orders.)
        add_action('wp_ajax_export_todays_orders_for_royalmail', array($this,'export_todays_orders_for_royalmail_function'));
        #Save Orders with tracking numbers function VIA Ajax
        add_action('wp_ajax_saveTrackDetails', array($this,'fun_save_track_details'));
    }

    public function custom_woocommerce_complete_order( $order_id ) 
    { 
        if(!$order_id) 
        {
        return;
        }
    $order = wc_get_order( $order_id );
    $order->update_status( 'completed' );
    }

    #
    # Function fun_save_track_details();
    #Save Orders with tracking numbers function VIA Ajax
    #    
    public function fun_save_track_details() {
            if ( !check_ajax_referer( 'j98usjkshdf98uewjndoidSFDFSGsduasjd33f', 'security' ) ){echo "Permision Denied";}else{
        $onumber = $_POST['onmubers'];
        $tnumber = $_POST['tnumbers'];
            for($i=0;$i<count($onumber);$i++)
            {
                $date = date_create();
                update_post_meta( $onumber[$i], '_tracking_number', $tnumber[$i] );
                update_post_meta( $onumber[$i], '_date_shipped', date_format($date, 'U')  );
                update_post_meta( $onumber[$i], '_custom_tracking_link', "https://www.royalmail.com/track-your-item" );
            }
            for($i=0;$i<count($onumber);$i++)
            {
            if($tnumber[$i]){
            $Order = new WC_Order( $onumber[$i] );
            $OrderId = $Order->id;
                if(!$OrderId){}else{
                    $this->custom_woocommerce_complete_order($OrderId);
                }
            }
            }





    echo "done";
    }
    die();
    }
    #
    # 
    # add the scripts create the nonce and make local copy for it to be used.
    #   
    Public function addRMScripts() {
        wp_enqueue_script('RMScript', (plugin_dir_url( __FILE__ ) . '/js/rm.js'), array('jquery'), false, true);
        wp_localize_script('RMScript', 'WP', array('ajaxurl' =>  admin_url('admin-ajax.php')
            ,'NONCE' => wp_create_nonce('AJXp7dfgdfg656fD4fg5q0adfgdfg54dfglzmxkcnjvhb')
            ,'RMNONCE' => wp_create_nonce('j98usjkshdf98uewjndoidSFDFSGsduasjd33f')
            ,'CNONCE' => wp_create_nonce('AJXp7dfgdfg6caiuhdc97yweuihfweiuhf89yzmxkcnjvhb')));
        wp_register_style( 'admin_Rmail_css', plugin_dir_url( __FILE__ ) . '/admin-Rmail.css', false, '1.0.0' );
        wp_enqueue_style( 'admin_Rmail_css');
    }
    #
    #
    #Create a custome wordpress Menu Page in the admin section.
    #
    public function regRmJpMenuPage(){
        add_menu_page( 'Export Orders', 'Export Orders', 'manage_options', 'exportorders', array($this,'runpageRExport'), plugins_url( 'Woo-Royalmail-Export/images/icon.png' ), 7 ); 

    }
    #
    #
    #
    #
    public function runpageRExport(){
        $j =  $this->getProcessingOderNumbers();
        if(!$j){ $j =  '<p style="color:red;">There are no orders that are marked as processing.</p>';}else{ ?>
        <div class='wrap'>
            <div class='rm-wrap'>
                <h1>JP Royal mail export tool for printing labels. (by Jiger Patel)</h1>
                <div id='message' class='updated fade below-h2'>
                    <p>Make sure you have marked the orders you require as 'preparing to dispatch'.</p>
                    <p>After orders are marked please use the link below to download the csv formatted file.</p>
                </div>
                <div id='message' class='updated fade below-h2'>
                    <div id='d-message' style='display:none;'>
                        <p style='text-align:center; width:100%;' > Please wait the download is been processed...</p>
                        <p style='text-align:center; width:100%;' > <img src='https://www.herbalhighswholesale.com/uk/wp-includes/js/thickbox/loadingAnimation.gif' alt='loading'></p>
                    </div>
                    <div id='success-msg' style='display:none;'>
                     <p style='text-align:center; width:100%; color:green;' >Download Done</p>
                    </div>
                    <div id='c-message'>
                        <h3>STEP 1....Download todays orders for printing</h3>
                        <p><a id='pRdown' class='myButtonjp'>Download the Orders in CSV file</a></p><br style='clear: both;'>
                    </div>
                </div>
            <div id='OrderBarcode' class='updated fade below-h2'>
                <div id='d-message' style='display:none;'>
                    <p style='text-align:center; width:100%;' > Please wait the barcodes are in saving process...</p>
                    <p style='text-align:center; width:100%;' > <img src='https://www.herbalhighswholesale.com/uk/wp-includes/js/thickbox/loadingAnimation.gif' alt='loading'></p>
                </div>
                <div class='orders-list'>
                    <h3>STEP 2....Enter the tracking numbers and mark the as complete</h3>
                    <table style='undefined;table-layout: fixed; width: 578px'>
                        <colgroqup><col style='width: 150px'><col style='width: 270px'><col style='width: 270px'></colgroup>
                          <tr>
                            <th>Order Numbers</th>
                            <th>Tracking Number</th>
                            <th style='text-align:left'>Status</th>
                            </tr>
                            <?php echo $j; ?>
                            <tr>
                            <td>
                            </td>
                            <td>
                                <p>
                                <div id='d-message' style='display:none;'>
                                    <p style='text-align:center; width:100%;' > Please wait track numbers are been saved...</p>
                                    <p style='text-align:center; width:100%;' > <img src='https://www.herbalhighswholesale.com/uk/wp-admin/images/wpspin_light.gif' alt='loading'></p>
                                </div>
                                <div id='success-msg' style='display:none;'>
                                    <p style='text-align:center; width:100%; color:green;' >Tracking numbers saved and Marked Complete</p>
                                </div>
                                    <a id='trkDown' class='saveTrackNumbers'>Save Tracking Numbers</a></p><br style='clear:both'>
                            </td>
                          </tr>
                    </table>
                </div>
            </div>
        </div>




        <?php
        }
    }
    #
    #
    #
    #
    private function getProcessingOderNumbers(){
        $r = '';global $woocommerce;  
        $ar = array('post_type' => 'shop_order','post_status' => 'publish','posts_per_page' => -1,
                'tax_query' => array(
                        array(
                            'taxonomy' => 'shop_order_status',
                            'field' => 'slug',
                            'terms' => array('processing')
                        )
                    )
        );            
        $lo = new WP_Query( $ar );
        while ( $lo->have_posts() ) : $lo->the_post();
            $order_id = $lo->post->ID;
            $order = new WC_Order($order_id);
            $g = $order->get_order_number( );
            $array[] = $order;
            $resw .= '<tr class="oRow">
            <td style="text-align:center;" class="OrderNumberInput">'.$g .'</td>
            <td><input rel="'.$order_id.'" class="actTrackingNumberInput" type="text" name="trackingnumber" style="width:100%;"></td>
            <td style="text-align:left;"><span id="res-'.$order_id.'"></span></td>
            </tr>';
        endwhile; 
        
        return $resw;
    }

    public function custom_woocommerce_shipment_tracking_default_provider( $provider ) {
        $provider = 'Royal Mail'; // Replace this with the name of the provider. See line 42 in the plugin for the full list.
    return $provider;
    }

    public function export_todays_orders_for_royalmail_function(){
        if ( !check_ajax_referer( 'AJXp7dfgdfg656fD4fg5q0adfgdfg54dfglzmxkcnjvhb', 'security' ) ){echo "Permision Denied";}else{
        //$array = array(1,2,3,4,5,6);
        //$response = json_encode($array);
        
        //return $response;
        global $woocommerce;  
        $args = array(
            'post_type'         =>  'shop_order',
            'post_status'       =>  'publish',
            'posts_per_page'    =>   -1,
            'tax_query'         =>  array(
                                        array(
                                            'taxonomy' => 'shop_order_status',
                                            'field' => 'slug',
                                            'terms' => array('processing')
                                        )
                                    )
        );
        
        $loop = new WP_Query( $args );
        $jArray = array();

        $firstlineArry = array();
        $firstlineArry[] = "keep";
        $firstlineArry[] = "mail service";
        $firstlineArry[] = "method reciprient";
        $firstlineArry[] = "address";
        $firstlineArry[] = "postalcode";
        $firstlineArry[] = "city";
        $firstlineArry[] = "country";
        $firstlineArry[] = "ref";
        $firstlineArry[] = "parcel count";
        $firstlineArry[] = "weight";
        $firstlineArry[] = "Service Format";
        $firstlineArry[] = "sign for";
        $jArray[] = $firstlineArry;

       while ( $loop->have_posts() ) : $loop->the_post();
         $order_id = $loop->post->ID;$order = new WC_Order($order_id);
            
            //$jArray[] = array();
            $loopline = array();
            //col1 data only SR1
            $loopline[] = "SR1";
            
            //col2 shipping method check and insert
            $itemShipMethod = $order->get_shipping_to_display(); 
            if (strpos($itemShipMethod,'9AM') !== false) { $itemShipMethod = ""; $itemShipMethod = "SD4"; }
            if (strpos($itemShipMethod,'1PM') !== false) { $itemShipMethod = ""; $itemShipMethod = "SD1"; }
            if (strpos($itemShipMethod,'1st Class') !== false) { $itemShipMethod = ""; $itemShipMethod = "CRL1"; }
            if (strpos($itemShipMethod,'Free') !== false) { $itemShipMethod = ""; $itemShipMethod = "CRL1"; }
            $loopline[] = $itemShipMethod;
            
            //col3 get and insert shipping name
            $line3 = $order->shipping_first_name . " " . $order->shipping_last_name;
            $loopline[] = $line3;

            //col4 get and insert shipping address
            $line4 = $order->shipping_address_1 . " " . $order->shipping_address_2;
            $loopline[] = $line4;

            //col5 get and insert postcode
            $line5 = $order->shipping_postcode;
            $loopline[] = $line5;
            
            //col6 get and insert city
            $line6 = $order->shipping_city;
            $loopline[] = $line6;

            //col7 get and insert country
            $line7 = $order->shipping_country;
            $loopline[] = $line7;

            //col8 get and insert city
            $line8 = $order->order_number;
            $loopline[] = "WC" . $line8 . "UK";
        
            $loopline[] = "1";
          
            //calculate the weight of product and tiems it by the quantity
            $weight = 0;
            if ( sizeof( $order->get_items() ) > 0 ) {
                        foreach( $order->get_items() as $item ) {
                            if ( $item['product_id'] > 0 ) {
                                $_product = $order->get_product_from_item( $item );
                                if ( ! $_product->is_virtual() ) {
                                    $weight += $_product->get_weight() * $item['qty'];
                                }
                            }
                        }
                    }
            //$weight = number_format($weight / 1000, 2);
            $weight = round($weight,0);
            if($weight === 0){ $weight = 1;}
            $loopline[] = $weight;
            
            //calculate Service Format and insert
            $l = ""; 
            if ($itemShipMethod === 'CRL1'){ $l = "P"; }else{ $l = "n"; }
            $loopline[] = $l;

            //calculate signed for and insert
             $r = ""; 
            if ($itemShipMethod === 'CRL1'){ $r = "REC"; }else{ $r = "N"; }
            $loopline[] = $r;   

            //add the row array to the toplevel array on each row.
            $jArray[] = $loopline;
            ?>
        <?php endwhile; ?>
        <?php 
        //var_dump($jArray);
        echo json_encode($jArray);
        die();
    }
    }



}}#End class and if statment 

#Register the class and create a new instance.
$GLOBALS['WC_Royalmail_Export_orders'] = new WC_Royalmail_Export_orders();
?>