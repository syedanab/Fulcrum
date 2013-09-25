<?php

/*
Plugin Name: Google Analytics API Top Posts
Plugin Repository: https://github.com/syedanab/Fulcrum
Note: This plugin shows the 10 most popular posts in a new page using Google Analytics API to fetch data from your analytics account.
Author: Syed Anab Imam
*/

/*
  Main Class
 */
 class googleAnalyticsAPITopPosts extends WP_Widget{
 	
	public function __construct(){
		$GATP_url = get_bloginfo('siteurl');
		$options = array(
			'description' => 'fetch most viewed pages in a month from Google Analytics API account and display it in a new page ',
			'name'        => 'Google Analytics API most visited'
		);
		
		parent::__construct('Google_Analytics_API_Top_Posts','',$options);
	}
	
/*
  Form that displays widget Admin
 */
	public function form($instance){
		extract($instance);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input 
				class="widefat" 
				id="<?php echo $this->get_field_id('title'); ?>" 
				name="<?php echo $this->get_field_name('title'); ?>" 
				value="<?php if (isset($title)) echo esc_attr($title) ; ?>"
			>
			<p><?php echo "Thank you for using this plugin! Please set your Google API information in Settings section."; ?></p>
		</p>
		<?php
	}
	
	/*
	  Widget update
	 */
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	
	/*
	  Displays info in sidebar
	 */
	public function widget($args,$instance){
		extract($args);
		extract($instance);
		
		$title = apply_filters('widget_title',$title);
		if (empty($title)) $title = 'Top Posts';
		echo $before_widget;
			echo $before_title . $title . $after_title;
		GoogleAnalyticsAPITopPosts_view();
		echo $after_widget;
	}
	
 }
/**************************************************************************************/

/*
  Register widget
 */
 
 add_action('widgets_init', mk_register_gatp);
 
 function mk_register_gatp(){
 	register_widget('googleAnalyticsAPITopPosts');
 }

  // GoogleAnalyticsAPItopPosts Class - END

 /**************************************************************************************/
/*
  Setting info of Google Analytics API 
 */

function GoogleAnalyticsAPITopPosts_options(){
	if($_POST['Submit'] == "Save"){
		
		
		update_option('GoogleAnalyticsAPIPopularPosts_username', $_POST['GoogleAnalyticsAPIPopularPosts_username']);
		if($_POST['GoogleAnalyticsAPIPopularPosts_password'])
			update_option('GoogleAnalyticsAPIPopularPosts_password', $_POST['GoogleAnalyticsAPIPopularPosts_password']);
		update_option('GoogleAnalyticsAPIPopularPosts_profileID' , $_POST['GoogleAnalyticsAPIPopularPosts_profileID']);
	}
?>
	<div class="wrap">
			<div class="icon32" id="icon-options-general"><br/></div>
			<h2 style="margin-top:0px">Google Analytics API Top Posts <hr>
				<?php echo "Set Your Google Analytics API settings"; ?>
			</h2>
			<p> <strong>By: Syed </strong></p>
			
			<form method="post">
				<table class="form-table">
					<tbody>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsAPITopPosts_username"><?php echo "Google Account Email:"; ?></label></th>
						<td>
							<input type="text" class="regular-text" value="<?php echo get_option('GoogleAnalyticsTopPosts_username'); ?>" name="GoogleAnalyticsTopPosts_username"/><br />
							<span class="setting-description"><?php echo "Please fill the email address you use to login to your Google Analytics API."; ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsAPITopPosts_password"><?php echo "Google Account Password:"; ?></label></th>
						<td>
							<input type="password" class="regular-text" value="<?php echo get_option('GoogleAnalyticsAPITopPosts_password'); ?>" name="GoogleAnalyticsAPITopPosts_password"/><br />
							<span class="setting-description"><?php echo "Please fill password correct"; ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="GoogleAnalyticsAPITopPosts_profileID"><?php echo "Google Analytics Profile ID:"; ?></label></th>
						<td>
							<input type="text" class="regular-text" value="<?php echo get_option('GoogleAnalyticsAPITopPosts_profileID'); ?>" name="GoogleAnalyticsAPITopPosts_profileID"/><br />
							<span class="setting-description"><?php echo "Check for your Profile ID. <br />(UA-xxxxxxx-xx is not the Profile ID, The profile ID of your account can be found in the URL of your reports. For instance, if you select a profile from an account and view your reports, you may encounter a URL string that looks like this:<br />https://www.google.com/analytics/reporting/?reset=1&id=123456&pdr=00000000-00000000<br />The profile ID is the number that comes right after the &id parameter. So, in this case, your profile ID would be 123456.)"; ?></span>
						</td>
					</tr>
					</tbody>
				</table>
				<div style="float:right;">
					<p><?php echo "Thank you for using this plugin! Hopefully a cherishing experience!
					<br />Author: Syed Anab Imam"; ?></p>
				</div>
				<p class="submit">
					<input type="submit" value="Save" class="button-primary" name="Submit"/>
				</p>
			</form>
<?php
}

/* 
  ADD ADMIN SETTINGS MENU
 */
function GoogleAnalyticsAPITopPosts_menu() {
    add_options_page('Google Analytics API Top Posts', 'Google Analytics API Top Posts', 9, __FILE__, 'GoogleAnalyticsAPITopPosts_options');
}
add_action('admin_menu', 'GoogleAnalyticsAPITopPosts_menu');


/**************************************************************************************/
/*
  Page OUTPUT
 */

function GoogleAnalyticsAPITopPosts_view(){
	/*
	  Start the time and expiry session 
	 */
	//$now = mktime(date("H"), date("i"), date("s"), date("m")  , date("d"), date("Y"));
	//$expire = $now + (60 * 60);
	$GATP_usr = get_option('GoogleAnalyticsAPIPopularPosts_username');
	$GATP_pwd = get_option('GoogleAnalyticsAPIPopularPosts_password');
	$GATP_pID = get_option('GoogleAnalyticsAPIPopularPosts_profileID');
	$GAPP_mRs = 10;
	$GAPP_SDs = 30;
	
	//Set the time for displaying the blog posts
		$todays_year = date("Y");
		$todays_month = date("m");
		$todays_day = date("d");
		$date = "$todays_year-$todays_month-$todays_day";
		$newdate = strtotime ( "-$GAPP_SDs day" , strtotime ( $date ) ) ;
		$newdate = date ( 'Y-m-d' , $newdate );
		$From = $newdate;
		
	define('ga_email', $GATP_usr);
	define('ga_password', $GATP_pwd);
	define('ga_profile_id', $GATP_pID);
	if(!ga_email || !ga_password || !ga_profile_id) {
		$output = "<b>Google Analytics API Popular Posts Error :</b><br />Please enter your account details in the options page.";
		return $output;
	}
	//link to Google Analytics API and use it
	$GATP_filter_fixed = 'ga:pagePath=~^/';
	require 'gapi.class.php';
	$ga = new gapi(ga_email, ga_password);
	$ga->requestReportData(ga_profile_id, array('hostname', 'pagePath'), array('visits'), array('-visits'), $filter=$GATP_filter_fixed.$GATP_filter, $start_date=$From, $end_date=$date, $start_index=1, $max_results=$GATP_mRs);


	foreach($ga->getResults() as $result) :
		$getHostname = $result->getHostname();
		$getPagepath = $result->getPagepath();
		$postPagepath = 'http://'.$getHostname.$getPagepath;
		$getPostID = url_to_postid($postPagepath);
		if ($getPostID <= 0) {
			$titleStr = $postPagepath;
			$output .= '<ul>'."\n";
			$output .= '<li>'."\n";
			$output .= '<div class="popular_post"><a href='.$postPagepath.'>'.$titleStr.'</a></div>'."\n";
			$output .= '</li>'."\n";
			$output .= '</ul>'."\n";
		}
		else {
			$titleStr = get_the_title($getPostID);
			$post = get_post($getPostID);
			$dateStr = mysql2date('Y-m-d', $post->post_date);
			$contentStr = strip_tags(mb_substr($post->post_content, 0, 60));
			$output .= '<ul>'."\n";
			$output .= '<li>'."\n";
			$output .= '<div class="popular_post"><a href='.$postPagepath.'>'.$titleStr.'</a><br />'."\n";
			$output .= '<div class="popular_post_date">'.$dateStr.'<br /></div>'."\n";
			$output .= '<div class="popular_post_contents">'.$contentStr.' ...'.'</div>'."\n";
			$output .= '</div>'."\n";
			$output .= '</li>'."\n";
			$output .= '</ul>'."\n";
		}
	endforeach
	?>
<?php
	return $output;
}
/**************************************************************************************/
/*
  Loads CSS
 */
function loadCSS() {
	$style = get_template_directory().'/google-analytics-API-top-posts.css';
	 if (is_file($style)) {
		$style = get_bloginfo('stylesheet_directory').'/google-analytics-API-top-posts.css';
	} else {
		$url = WP_PLUGIN_URL.'/'.dirname(plugin_basename(__FILE__));
		$style = $url.'/google-analytics-API-top-posts.css';
	}
	echo "<!--Google Analytics API Popular Posts plugin-->\n";
	echo '<link rel="stylesheet" type="text/css" media="all" href="'.$style.'">';
}
add_action('wp_head', 'loadCSS');


/**************************************************************************************/
/*
  Adding new page
 */

 /* Runs when plugin is activated */
register_activation_hook(__FILE__,'my_plugin_install'); 

/* Runs when plugin is deactivated or is in deactivation mode*/
register_deactivation_hook( __FILE__, 'my_plugin_remove' );

 function my_plugin_install() {

    global $wpdb;

    $the_page_title = 'TOP 10 Popular Posts';
    $the_page_name = 'TOP 10 Popular Posts';

    // the menu entry
    delete_option("my_plugin_page_title");
    add_option("my_plugin_page_title", $the_page_title, '', 'yes');
    // the slug
    delete_option("my_plugin_page_name");
    add_option("my_plugin_page_name", $the_page_name, '', 'yes');
    // the id
    delete_option("my_plugin_page_id");
    add_option("my_plugin_page_id", '0', '', 'yes');

    $the_page = get_page_by_title( $the_page_title );

    if ( ! $the_page ) {

        // Create post object
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = GoogleAnalyticsAPITopPosts_view();
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncategorised'
		
        // Insert the post into the database
        $the_page_id = wp_insert_post( $_p );

    }
    else {
        // the plugin may have been previously active and the page may trash

        $the_page_id = $the_page->ID;
        $_p = array();
        $_p['post_title'] = $the_page_title;
        $_p['post_content'] = GoogleAnalyticsAPITopPosts_view();
        $_p['post_status'] = 'publish';
        $_p['post_type'] = 'page';
        $_p['comment_status'] = 'closed';
        $_p['ping_status'] = 'closed';
        $_p['post_category'] = array(1); // the default 'Uncategorised'
		// Insert the post into the database
        $the_page_id = wp_insert_post( $_p );
        //verifies that the page is not trashed
        $the_page->post_status = 'publish';
        $the_page_id = wp_update_post( $the_page );

    }

    delete_option( 'my_plugin_page_id' );
    add_option( 'my_plugin_page_id', $the_page_id );

}

function my_plugin_remove() {

    global $wpdb;

    $the_page_title = get_option( "my_plugin_page_title" );
    $the_page_name = get_option( "my_plugin_page_name" );

    // ID of the webpage
    $the_page_id = get_option( 'my_plugin_page_id' );
    if( $the_page_id ) {

        wp_delete_post( $the_page_id ); // Page will trash, not delete

    }

    delete_option("my_plugin_page_title");
    delete_option("my_plugin_page_name");
    delete_option("my_plugin_page_id");

}

/*
 Parser for Queries
 */
function my_plugin_query_parser( $q ) {

$the_page_name = get_option( "my_plugin_page_name" );
$the_page_id = get_option( 'my_plugin_page_id' );

$qv = $q->query_vars;

// permalinks
if( !$q->did_permalink AND ( isset( $q->query_vars['page_id'] ) ) AND ( intval($q->query_vars['page_id']) == $the_page_id ) ) {

$q->set('my_plugin_page_is_called', TRUE );
return $q;

}
elseif( isset( $q->query_vars['pagename'] ) AND ( ($q->query_vars['pagename'] == $the_page_name) OR ($_pos_found = strpos($q->query_vars['pagename'],$the_page_name.'/') === 0) ) ) {

$q->set('my_plugin_page_is_called', TRUE );
return $q;

}
else {

$q->set('my_plugin_page_is_called', FALSE );
return $q;

}

}
add_filter( 'parse_query', 'my_plugin_query_parser' );

 /**************************************************************************************/
 /*
  Additional shortcode to display posts on whole page
  */
function GATP_shortcode() {
	$output = GoogleAnalyticsAPITopPosts_view();
	return $output;
}
add_shortcode('GATP_VIEW', 'GATP_shortcode');

?>
