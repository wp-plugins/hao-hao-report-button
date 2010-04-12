<?php
/*
Plugin Name: Hao Hao Report Button
Version: 1.0
Plugin URI: http://www.haohaoreport.com/add-hao-hao-button-wordpress#wp-plugin
Author: Dao By Design
Author URI: http://www.daobydesign.com
Description: A configurable plugin to add an HHR vote/submit button to your posts.
*/ 

register_activation_hook(__FILE__, 'hhr_activation_hook');

function hhr_activation_hook() {
	return hhr_restore_config(False);
}

// restore built-in defaults, optionally overwriting existing values
function hhr_restore_config($force=False) {
	
	// Enabled or Not
	if ($force or !is_string(get_option('hhr_enabled')))
		update_option('hhr_enabled', 'yes');

	// Conditionals
	if ($force or !is_array(get_option('hhr_conditionals')))
		update_option('hhr_conditionals', array(
			'is_home' => False,
			'is_single' => True,
			'is_page' => True,
			'is_category' => False,
			'is_date' => False,
			'is_search' => False,
		));
	
	// Button Position
	if ($force or !is_string(get_option('hhr_position')))
		update_option('hhr_position', 'bottom');

	// Button Style
	if ($force or !is_string(get_option('hhr_style')))
		update_option('hhr_style', 'none');
	if ($force or !is_string(get_option('hhr_style_custom')))
		update_option('hhr_style_custom', '');

}

add_action('admin_menu', 'hhr_button_admin_menu');
function hhr_button_admin_menu() {
	add_submenu_page('options-general.php', 'HHR Button Options', 'HHR Button', 8, 'hhr_button', 'hhr_button_menu');
}

function hhr_button_menu() {
	if($_REQUEST['clear']) {
		hhr_restore_config(True);
		echo '<div id="message" class="error fade"><p>The settings have been reset to their defaults.</p></div>';
	} elseif ($_REQUEST['save']) {
		// update enabled
		update_option('hhr_enabled', mysql_escape_string($_REQUEST['hhr_enabled']));		
	
		// update conditional displays
		$conditionals = Array();
		if (!$_REQUEST['conditionals'])
			$_REQUEST['conditionals'] = Array();
		foreach(get_option('hhr_conditionals') as $condition=>$toggled)
			$conditionals[$condition] = array_key_exists($condition, $_REQUEST['conditionals']);
		update_option('hhr_conditionals', $conditionals);

		// update button position
		update_option('hhr_position', mysql_escape_string($_REQUEST['hhr_position']));
		
		// update button styles
		update_option('hhr_style', mysql_escape_string($_REQUEST['hhr_style']));
		update_option('hhr_style_custom',$_REQUEST['hhr_style_custom']);
	
		echo '<div id="message" class="updated fade"><p>The changes have been saved.</p></div>';
	}

	// Load the options for display in the form.
	$hhr_enabled = get_option('hhr_enabled');
	$conditionals = get_option('hhr_conditionals');
	$hhr_position = get_option('hhr_position');
	$hhr_style = get_option('hhr_style');
	$hhr_style_custom = get_option('hhr_style_custom');
	?>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" id="hhr_form" name="hhr_form">
		<div class="wrap" id="hhr_options">
			<h2>HHR Button Activation</h2>
			<fieldset>
				<h3>Enable / Disable Button</h3>
				<p>Enable the Hao Hao Report submit/vote button?&nbsp;&nbsp;<input type="radio" name="hhr_enabled" value="yes" <?php if($hhr_enabled=='yes'){ echo "CHECKED"; } ?>>Yes&nbsp;&nbsp;<input type="radio" name="hhr_enabled" value="no" <?php if($hhr_enabled=='no'){ echo "CHECKED"; } ?>>No
				</p>
			</fieldset>
			
			<fieldset id="hhr_conditionals">
				<h3>HHR Button Display Conditions</h3>
				<p>Select the areas of your site you want the HHR Button to be display on.<br />
				Note that putting the button on large lists of post summaries is usually unnecessary.</p>
				<ul style="list-style-type: none">
					<li><input type="checkbox" name="conditionals[is_single]"<?php echo ($conditionals['is_single']) ? ' checked="checked"' : ''; ?> /> Individual Posts</li>
					<li><input type="checkbox" name="conditionals[is_page]"<?php echo ($conditionals['is_page']) ? ' checked="checked"' : ''; ?> /> Individual Pages</li>
					<li><input type="checkbox" name="conditionals[is_home]"<?php echo ($conditionals['is_home']) ? ' checked="checked"' : ''; ?> /> Front page of the blog</li>
					<li><input type="checkbox" name="conditionals[is_category]"<?php echo ($conditionals['is_category']) ? ' checked="checked"' : ''; ?> /> Category archives</li>
					<li><input type="checkbox" name="conditionals[is_date]"<?php echo ($conditionals['is_date']) ? ' checked="checked"' : ''; ?> /> Date-based archives</li>
					<li><input type="checkbox" name="conditionals[is_search]"<?php echo ($conditionals['is_search']) ? ' checked="checked"' : ''; ?> /> Search results</li>
				</ul>
				<p><em>By default full Posts and Pages are enabled. This is best in most situations.</em></p>
			</fieldset>

			<filedset id="hhr_position">
				<h3>HHR Button Position</h3>
				<p>With this setting you can choose whether you want the button to generate at the top or bottom of your content.</p>
				<ul style="list-style-type: none">
					<li><input type="radio" name="hhr_position" value="bottom" <?php if($hhr_position=='bottom'){ echo "CHECKED"; } ?>>Bottom</li>
					<li><input type="radio" name="hhr_position" value="top" <?php if($hhr_position=='top'){ echo "CHECKED"; } ?>>Top</li>
				</ul>
			</fieldset>

			<filedset id="hhr_style">
				<h3>HHR Button Style</h3>
				<p>With the style set as none, the button can be styled by using the ".hhr_button" CSS class selector in your theme's style.css file. The easier method is simply using the "Left", "Right", or "Custom" options below. For "Custom", simply insert the CSS styles in the box below (<em>see the copy/paste examples below</em>).</p>
				<ul style="list-style-type: none">
					<li><input type="radio" name="hhr_style" value="none" <?php if($hhr_style=='none'){ echo "CHECKED"; } ?>>None</li>
					<li><input type="radio" name="hhr_style" value="left" <?php if($hhr_style=='left'){ echo "CHECKED"; } ?>>Left</li>
					<li><input type="radio" name="hhr_style" value="right" <?php if($hhr_style=='right'){ echo "CHECKED"; } ?>>Right</li>
					<li><input type="radio" name="hhr_style" value="custom" <?php if($hhr_style=='custom'){ echo "CHECKED"; } ?>>Custom (use box below)</li>
					<li><textarea cols="40" rows="3" name="hhr_style_custom"><?php echo $hhr_style_custom; ?></textarea></li>
				</ul>
				<p><strong>Custom Style Examples (note, you don't need to add the class/ID selector).</strong></p>
				<p><strong>Example 1:</strong> <em>float:right;margin:0px 0px 5px 10px;</em></p>
				<p><strong>Example 2:</strong> <em>float:right;margin:0px 0px 5px 10px;padding:5px;border:1px solid #ccc !important;background-color:#eee;</em></p>
			</fieldset>
		
			<p class="submit">
				<input name="save" id="save" style='width:100px' value="Save Changes" type="submit" />
				<input name="clear" id="reset" style='width:100px' value="Reset Options" type="submit" />
			</p>
		</div>
	</form>
	
<?php
}

// Hook the_content to output html if we should display on any page
$hhr_contitionals = get_option('hhr_conditionals');
$hhr_enabled = get_option('hhr_enabled');
if (is_array($hhr_contitionals) and in_array(true, $hhr_contitionals) and $hhr_enabled == 'yes') {
	add_filter('the_content', 'hhr_display_hook');
	add_filter('the_excerpt', 'hhr_display_hook');
	
	function hhr_display_hook($content='') {
		$conditionals = get_option('hhr_conditionals');
		$hhr_position = get_option('hhr_position');
		if ((is_home()     and $conditionals['is_home']) or
		    (is_single()   and $conditionals['is_single']) or
		    (is_page()     and $conditionals['is_page']) or
		    (is_category() and $conditionals['is_category']) or
		    (is_date()     and $conditionals['is_date']) or
		    (is_search()   and $conditionals['is_search']) or
		     0)
			
			if ($hhr_position == 'bottom') {
				$content .= hhr_button_html();
			} elseif ($hhr_position == 'top') {
				$content = hhr_button_html().$content;
			}
		return $content;
	}
}


function hhr_button_html() {
	$outputter = '';
	global $wp_query; 
	$post = $wp_query->post;
	$id = $post->ID;
	$postlink = get_permalink($id);
	$title = urlencode($post->post_title);
	if ($post->post_excerpt) {
		$hhr_excerpt = substr(strip_tags($post->post_excerpt),0, 300);
	} else {
		$hhr_excerpt = substr(strip_tags($post->post_content),0, 300);
	}
	$hhr_excerpt = str_replace(array('\r\n', '\r', '\n'),'',$hhr_excerpt);
	$hhr_excerpt = str_replace(array(chr(10),chr(13)),' ',$hhr_excerpt);

	$outputter .= '<script type="text/javascript">'.chr(10);
	$outputter .= 'hhr_url = "' . $postlink . '";'.chr(10);
	$outputter .= 'hhr_title = "' . htmlentities(strip_tags($post->post_title),ENT_QUOTES,'UTF-8',false) . '";'.chr(10);
	$outputter .= 'hhr_body = "' . htmlentities($hhr_excerpt,ENT_QUOTES,'UTF-8',false) . '";'.chr(10);
	$hhrtags = get_the_tags($id);
	if ($hhrtags) {
		foreach($hhrtags as $hhrtag) {
			$thetags .= $hhrtag->name . ', '; 
		}
		$thetags = htmlentities(substr($thetags, 0,strlen($thetags)-2),ENT_QUOTES,'UTF-8',false);
	}
	$outputter .= 'hhr_tags = "' . $thetags . '";'.chr(10);
	$outputter .= '</script>'.chr(10);
	$outputter .= '<script src="http://www.haohaoreport.com/sites/all/modules/drigg_external/js/button.js" type="text/javascript"></script>';

  	return $outputter;
}

function hhr_button_style() {
	$hhr_style_type = get_option('hhr_style');
	if ($hhr_style_type == 'left') :
		$hhr_style = 'float:left;margin:0px 10px 5px 0px;';
	elseif ($hhr_style_type == 'right') :
		$hhr_style = 'float:right;margin:0px 0px 5px 10px;';
	elseif ($hhr_style_type == 'custom') :
		$hhr_style = get_option('hhr_style_custom');
	endif;
	return $hhr_style;

}

// Sort out style attributes.
if (get_option('hhr_style') != 'none') { $button_style = hhr_button_style(); }

// Hook wp_head to add css
if (get_option('hhr_style') != 'none') {
	add_action('wp_head', 'hhr_wp_head');
	function hhr_wp_head($button_style) {
		$button_style = hhr_button_style();
		echo '<style media="screen" type="text/css">'.chr(10);
		echo '.hhr_button {'.$button_style.'}'.chr(10);
		echo '</style>'.chr(10);
	}
}

?>