<?php
/*
Plugin Name: London 2012 olympics
Plugin URI: http://cssoftdevlabs.com/Dev004/london
Description: Wordpress plugin London 2012
Author: london olympics
Version: 1.0.1
Author URI: http://cssoftdevlabs.com/
*/
$siteurl = get_option('siteurl');
define('OLY_FOLDER', dirname(plugin_basename(__FILE__)));
define('OLY_URL', $siteurl.'/wp-content/plugins/' . OLY_FOLDER);
define('OLY_FILE_PATH', dirname(__FILE__));
define('OLY_DIR_NAME', basename(OLY_FILE_PATH));

register_activation_hook(__FILE__,'oly_install');
register_deactivation_hook(__FILE__ , 'oly_uninstall' );
register_uninstall_hook(__FILE__ , 'oly_delete');
function oly_install()
{
    
}
function oly_uninstall()
{
    
}
function oly_delete()
{
  unlink(OLY_FOLDER);
}
add_action('admin_menu','OLY_admin_menu');

function OLY_admin_menu() { 
	add_menu_page(
		"london_2012",
		"olympics",
		8,
		__FILE__,
		"oly_admin_menu_list",
		OLY_URL."/images/logo.png"
	); 
	//add_submenu_page(__FILE__,'olypics','Site list','8','list-site','oly_admin_list_site');
           
}
function oly_admin_menu_list()
{
	echo "<br><br><br>put <strong>[oly_london_2012]</strong> shortcode on page for Medal tally<br>";
        echo "alse use widget  for Medal tally<br>";
}
//Add ShortCode for "front end listing"
add_shortcode("oly_london_2012","oly_site_listing_shortcode");
function oly_site_listing_shortcode() 
{ 
	require_once('libs/nusoap.php');
        $client = new nusoap_client('http://cssoftdevlabs.com/Dev004/iws/server.php?wsdl', true);

   
      	
         $err = $client->getError();
	if ($err) {
    	

    	echo '<h2>temporarily unavailable</h2>';
    	

	}
        $result = $client->call('london', array('name' => 'Aman'));
        $xmlstr =  $client->response;

	$element = new SimpleXMLElement( $result);

        //print_r($element);
      
      echo '<table width="100%" border="0" >';
      echo '<tr><td>';
     $i=1;
	foreach($element->row as $row)
        {
            
            if($i==1)
              {
                echo '<table width="100%" border="0" >';
                echo '<tr><td colspan="4">'.$row->ad.'</td></tr>';
                echo '<tr><td width="32%">&nbsp;</td><td><img src="'.OLY_URL.'/images/medal_gold.png" alt="G"></td><td><img src="'.OLY_URL.'/images/medal_silver.png" alt="S"></td><td><img src="'.OLY_URL.'/images/medal_bronze.png" alt="B"></td></tr></table></td></tr><tr><td><div style="height:188px;overflow-y:scroll;"><table width="100%" border="0" >';
              }
              else
              {
                 if($row->gold == 0 && $row->silver == 0 && $row->bronze == 0){
					 continue;
				 }else{
                 echo '<tr><td width="35%">'.$row->country_code.'&nbsp;<img src="'.OLY_URL.'/flag/'.$row->flag.'" alt=""></td><td>'.$row->gold.'</td><td>'.$row->silver.'</td><td>'.$row->bronze.'</td></tr>';
                 }
              }
           $i++;  
        }
        echo '</table></div></td></tr></table>';
}



class LondonWidget extends WP_Widget
{
  function LondonWidget()
  {
    $widget_ops = array('classname' => 'LondonWidget', 'description' => 'Displays Medal in olympics ');
    $this->WP_Widget('LondonWidget', 'London 2012 Olympics', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    // WIDGET CODE GOES HERE
    oly_site_listing_shortcode();
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("LondonWidget");') );


?>
