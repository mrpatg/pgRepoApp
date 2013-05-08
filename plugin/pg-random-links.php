<?php
/*
Plugin Name: Random Links Display Node Widget
Plugin URI: http://patrickg.net
Description: A node widget to display random links from a central repository.
Author: Patrick Godbey
Version: 1
Author URI: http://patrickg.net
*/


	$pg_random_links_db_version = "1.0";

	$table_name3 = $wpdb->prefix . "pg_random_links_repo";

function pg_random_links_install() {
	global $wpdb;
	global $pg_random_links_db_version;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
 
	add_option("pg_random_links_db_version", $pg_random_links_db_version);
	add_option("pg_random_links_repo_url", NULL);
	add_option("pg_random_links_repo_links", NULL);
	add_option("pg_random_links_repo_updated", NULL);
	add_option("pg_random_links_repo_count", NULL);
	add_option("pg_random_links_repo_throttle", NULL);
}

register_activation_hook(__FILE__,'pg_random_links_install');

add_action('admin_menu', 'pg_random_links_menu');

function pg_random_links_menu() {
	add_options_page('PG Random Links Node', 'PG Random Links Node', 'manage_options', 'pg_random_links_options', 'pg_random_links_options');
}


function get_repo_url(){
	$repourl = get_option('pg_random_links_repo_url');
	if($repourl){
		return $repourl;
	}else{
		return 'is not set.';
	}
}



function pg_update_repo_url($repourl){
	$repourl = update_option('pg_random_links_repo_url', $repourl);
	if($repourl){
		return $repourl;
	}else{
		return 'ERROR';
	}
}




function pg_update_repo_links($silent=TRUE){

	$repourl = get_option('pg_random_links_repo_url');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $repourl.'repo.php');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	$response = curl_exec($ch);
	curl_exec($ch);	

	$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	if($http_status == '200'){
		$update_links = update_option('pg_random_links_repo_links', $response);
	}


	if($update_links){
		date_default_timezone_set('America/Los_Angeles');
		$date = date('m/d/Y h:i:s a', time());
		update_option('pg_random_links_repo_updated', $date);
		if(!$silent){
			return "Updated on <em>".$date."</em>";
		}
	}else{
		if(!$silent){
			return "ERROR (http status code: $http_status )";
		}
	}
}

function date_diff($d1, $d2){
    $d1 = (is_string($d1) ? strtotime($d1) : $d1);
    $d2 = (is_string($d2) ? strtotime($d2) : $d2);

    $diff_secs = abs($d1 - $d2);
    $base_year = min(date("Y", $d1), date("Y", $d2));

    $diff = mktime(0, 0, $diff_secs, 1, 1, $base_year);
    return array(
        "years" => date("Y", $diff) - $base_year,
        "months_total" => (date("Y", $diff) - $base_year) * 12 + date("n", $diff) - 1,
        "months" => date("n", $diff) - 1,
        "days_total" => floor($diff_secs / (3600 * 24)),
        "days" => date("j", $diff) - 1,
        "hours_total" => floor($diff_secs / 3600),
        "hours" => date("G", $diff),
        "minutes_total" => floor($diff_secs / 60),
        "minutes" => (int) date("i", $diff),
        "seconds_total" => $diff_secs,
        "seconds" => (int) date("s", $diff)
    );
}

function pg_random_links_repo_throttle(){

// Compare throttle and Update date values. If current time is the value of the throttle more than current time or greater, return true.

		$throttle = get_option('pg_random_links_repo_throttle');
		$updatedate = get_option('pg_random_links_repo_updated');
		$date = date('m/d/Y h:i:s a', time());

		$a = date_diff($date, $updatedate);
		if($a['hours'] >= $throttle){
			return TRUE;
		}else{
			return FALSE;
		}
}

function pg_repo_health_check(){

	$repourl = get_option('pg_random_links_repo_url');

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $repourl.'repo.php?flag=health');
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3");
	$response = curl_exec($ch);
	curl_exec($ch);	

	$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	$jsonresponse = json_decode($response);	
	if($http_status == '200'){
		if($jsonresponse == 'OK'){
			return $jsonresponse.' (http status '.$http_status.')';
		}else{
			return 'Error - No repo found.';
		}
	}else if($http_status == '400'){
		return 'Error 400 - Bad Request';
	}else if($http_status == '404'){
		return 'Error 404 - Not Found';
	}else if($http_status == '500'){
		return 'Error 500 - Internal Error';
	}else if($http_status == '403'){
		return 'Error 403 - Forbidden';
	}else{
		return 'Health Check Failed: HTTP STATUS '.$http_status;
	}

}

function pg_random_links_options() {
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	if(isset($_POST['repothrottle'])){
		
		update_option('pg_random_links_repo_throttle', $_POST['repothrottle']);

	}

	if(isset($_POST['repourl'])){
		$pg_repo_update_msg = pg_update_repo_url($_POST['repourl']);	
	}

	if(isset($_POST['updatelinks'])){
		$silent = FALSE;
		$pg_repo_update_links_msg = pg_update_repo_links($silent);	
	}

	echo '<div class="wrap">';
	echo '<h2>Random Links Node Plugin Options</h2>';
	echo '<h3>Enter the Repository URL</h3>';
	echo '<p><form method="post" action="">';
	echo '<form method="POST" action="">
			    <ul>
			        <li><label for="repourl">Repo URL<span> *</span>: </label>
			        <input id="repourl" size="30" name="repourl" value="';
	echo get_option('pg_random_links_repo_url');
	echo '" /></li>    
					<li><input type="submit" class="button-secondary" value="Update Repo URL"></li>
			    </ul>
			</form>';
	echo '</p>';
	echo '<p class="repo-throttle">';
	echo '<form method="POST" name="updatethrottle" action="">
			    <ul>
			        <li><label for="repothrottle">Repo Throttle<span> *</span>: </label><br>
			        Update repo every <input id="repothrottle" size="2" maxlength="2" name="repothrottle" value="';
	echo get_option('pg_random_links_repo_throttle');
	echo '" /> hours</li>    
					<li><input type="submit" class="button-secondary" value="Update Repo Throttle"></li>
			    </ul>
			</form>';
	echo '</p>';
	echo '<p class="repo-url">';
	echo "The current Repository URL is <strong>".get_repo_url()."</strong>.";	
	echo '</p>';
	echo '<p class="repo-health">';
	echo "Current Repo status: <strong>".pg_repo_health_check()."</strong>.";
	echo '</p>';
	echo '<p><form method="post" name="updatelinks" action="">';	
	echo '<form method="POST" action="">
			    <ul>
					<li><input type="submit" class="button-secondary" value="Update Repo Links"></li>
					<input type="hidden" name="updatelinks" value="true">
			    </ul>
			</form>';
	echo '</p>';
	echo '<p class="repo-health">';
	echo "Repo Link Update status: <strong>".$pg_repo_update_links_msg."</strong>.";
	echo '</p>';
	echo '</p><hr>';
	echo '</div>';
}

function get_pg_random_links($numlinks) {

	$dbjson = get_option('pg_random_links_repo_links');

	$jsonarray = json_decode($dbjson);

	shuffle($jsonarray);

	$jsonarray = array_slice($jsonarray, 0, $numlinks);

	$json = json_encode($jsonarray);

	return $json;

}

// WIDGET TIME

class pg_random_links_widget extends WP_Widget {
    function pg_random_links_widget() {
        $widget_ops = array( 'classname' => 'widget_avatar', 'description' => __( "Display node widget with links from repo." ) );
        $this->WP_Widget('pg_random_links', __('PG Random Links Node'), $widget_ops);
    }

    function widget( $args, $instance ) {
        extract($args);
		if(pg_update_repo_throttle()){ pg_update_repo_links(); }
        $widget_title = apply_filters( 'widget_text', $instance['title'], $instance );
        $numlinks = apply_filters( 'widget_text', $instance['text'], $instance );
        echo $before_widget;

		echo '<h2 class="widgettitle">'.$widget_title.'</h2>';

		$linkdata = json_decode(get_pg_random_links($numlinks));
	//	$linkdata = get_pg_random_links($numlinks);
	//	print_r($linkdata);
		foreach($linkdata AS $links){
			$url = $links[0];
			$title = $links[1];
			echo '<a href="'.$url.'">'.$title.'</a><br />';
		}
        echo $after_widget;
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        if ( current_user_can('unfiltered_html') ){
            $instance['title'] =  $new_instance['title'];
            $instance['text'] =  $new_instance['text'];
        }else{
            $instance['title'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['title']) ) ); // wp_filter_post_kses() expects slashed
            $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		}
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'text' => '' , 'title' => '') );
        $text = format_to_edit($instance['text']);
        $title = format_to_edit($instance['title']);
?>
Widget Title<br/>
        <input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"><br/>
Number of links to display.<br/>
        <input type="text" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" value="<?php echo $text; ?>">
<?php
    }
}

function lbi_widgets_init() {
    register_widget( 'pg_random_links_widget' );
}
add_action( 'widgets_init', 'lbi_widgets_init' );

?>