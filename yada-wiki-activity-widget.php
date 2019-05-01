<?php
/**
 * Plugin Name: Yada Wiki Activity Widget
 * Plugin URI:  https://github.com/ngagnon1/yada-wiki-activity-widget
 * Description: This plugin provides an activity widget for yada wiki
 * Version:     1
 * Author:      Nathan Gagnon
 * Text Domain: yada-wiki-activity-widget
 * Domain Path: /languages
 *
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class yadawiki_activity_widget extends WP_Widget {

  function __construct() {
    parent::__construct(
      'yadawiki_activity_widget', 
      'Yada Wiki Activity'
    );
  }

	function form( $instance ) {
		if( $instance) {
			$title 		= esc_attr($instance['title']);
			$num_posts 		= $instance['num_posts'];
		} else {
			$title 		= '';
			$num_posts 	= '';
		}

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'yada_wiki_domain'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('num_posts'); ?>"><?php _e('Number of Posts:', 'yada_wiki_domain'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('num_posts'); ?>" name="<?php echo $this->get_field_name('num_posts'); ?>" type="text" value="<?php echo $num_posts; ?>" />
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance 				= $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['num_posts'] 		= strip_tags($new_instance['num_posts']);
		return $instance;
	}
  

  function widget( $args, $instance ) {

    $num_posts = 8;
    $title = 'Latest Changes';

    if( $instance ){
      $title = trim($instance['title'])? $instance['title']: $title;
      $num_posts = is_numeric( $instance['num_posts'] ) && $instance['num_posts'] > 0? $instance['num_posts']: $num_posts;
    }

    $post_args = array( 
      'offset' => 0,
      'post_type' => 'yada_wiki', 
      'orderby' => 'post_modified',
      'order' => 'DESC',
      'posts_per_page' => $num_posts,
      'post_status' => 'publish'
    );   
    $list = get_posts( $post_args );
    if( isset($args['before_widget']) ) {
      echo $args['before_widget'];
    }
    echo '<div class="widget-text yadawiki_toc_widget_box">';
    echo '<div class="widget-title"><h1 class="widget-title">'.$title.'</h1></div>';
    echo '<div class="widget_links">';
    $yw_widget_content = '<ul class="widget_links ul">';
    foreach ( $list as $item ) {
      $date_a = new DateTime($item->post_modified_gmt);
      $date_b = new DateTime;
      $interval = date_diff($date_a,$date_b);
      $mins = $interval->format('%i');
      $hours = $interval->format('%h');
      $days = $interval->format('%d');
      $months = $interval->format('%m');
      $years = $interval->format('%y');
      $modified = "";
      $quantity = "";
      $interval = "";
      if( $years ){
        $quantity = $years;
        $interval = 'year';
      }
      else if( $months ){
        $quantity = $months;
        $interval = 'month';
      }
      else if( $days ){
        $quantity = $days;
        $interval = 'day';
      }
      else if( $hours ){
        $quantity = $hours;
        $interval = 'hour';
      }
      else if( $mins ){
        $quantity = $mins;
        $interval = 'minute';
      }
      else{
        $modified = "just now";
      }

      if( $interval ){
        if( $quantity>1 )
          $interval .= 's';
        $modified = "$quantity $interval ago";
      }

      $yw_widget_content = $yw_widget_content.'<li class="widget_links li"><a href="'.get_post_permalink($item->ID).'">'.$item->post_title.'</a> ('.$modified.')</li>';
    }
    $yw_widget_content = $yw_widget_content.'</ul>';
    echo $yw_widget_content;
    echo '</div>';
    echo '</div>';
    if( isset($args['after_widget']) ) {
      echo $args['after_widget'];
    }
  }

}

add_action( 'widgets_init', function(){
  register_widget( 'yadawiki_activity_widget' );
});

