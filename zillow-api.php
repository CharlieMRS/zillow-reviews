<?php

/*

Plugin Name: Zillow API
Description: Pull reviews from Zillow into carousel slider. Shortcode usage - [clakc_reviews zillow_id="X1-ZWz19jxbl4l897_98wrk" screenname="Jeremy J Marks" count="8"]
Author: Charlie Meers
Version: 1.1

*/

add_action('wp_enqueue_scripts', 'mrsc_reviews_enqueue_scripts');

function mrsc_reviews_enqueue_scripts() {
	wp_enqueue_script('slick-script', plugins_url( 'slick/slick.min.js', __FILE__ ), array('jquery'), null, true); 
	wp_enqueue_script('slick-initialize-script', plugins_url( 'slick/main.js', __FILE__ ), array('slick-script'), null, true);
	wp_enqueue_style('slick-theme-style', plugins_url( 'slick/slick-theme.css', __FILE__ ), null, true); 
	wp_enqueue_style('slick-style', plugins_url( 'slick/slick.css', __FILE__ ), null, true);  

}








add_shortcode( 'clakc_reviews', 'clakc_reviews' );

function clakc_reviews( $atts ) {
	

	$output = '';
	ob_start();
	
	
	$atts = shortcode_atts( array( 'zillow_id' => '', 'screenname' => '', 'count' => '' ), $atts, 'clakc_reviews' );
		
	
	
	//$originator = $atts['originator'];
	//$count = $atts['count'];
	$data = array(
		  'zws-id'=> $atts['zillow_id'],
		  'screenname'=> $atts['screenname'],
		  'count'=>$atts['count']
		  );
		  
	$fields =  http_build_query($data);
	//$fields =  'zws-id=X1-ZWz19jxbl4l897_98wrk&screenname=Jeremy%20J%20Marks&count=7';
	$query_1 = 'http://www.zillow.com/webservice/ProReviews.htm?' . $fields;
	//echo $fields;
	//echo $query_1;
	
	//this is temp. workaround for api daily limit imposed by zillow
	//$query_temp = 'temp-reviews.xml';
	
	
	$xml = simplexml_load_file($query_1);
	$response_code = $xml->message->code;
	//endif;
	if($response_code == 7) :
		$xml = simplexml_load_file('https://communitylendingofamerica.com/wp-content/plugins/zillow-api/temp-reviews.xml');
	endif;
	//echo '<pre>'; print_r($xml); echo '</pre>';
	
	echo '<div class="rating-carousel">';
	
		for ( $c = 0; $c < $data['count']; $c++) {
			
			$reviewer = $xml->response->result->proReviews->review[$c]->reviewer;
			$rating = $xml->response->result->proReviews->review[$c]->rating;
			$description = $xml->response->result->proReviews->review[$c]->description;
			
			echo '<div>';
				echo '<h4 class="rating">';
					for ( $i = 0; $i < $rating; $i++) {
						echo do_shortcode( '[x_icon type="star"]' );
					}
				echo '</h4>';
				echo '<p>'. $description . '</p>';
				echo '<p> &ndash; ' . $reviewer . '</p>';
			echo '</div>';
		}
		
	echo '</div>';
	
	
	$output = ob_get_contents();
	
	ob_end_clean();
	
	return $output;

}