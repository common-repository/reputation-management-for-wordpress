<?php
/*
Plugin Name: ViperRep
Plugin URI: http://www.viperchill.com/wordpress-plugins/
Description: ViperRep is a reputation management plugin for Wordpress. It allows you to track mentions of your brand, your products, or even your own name, around the web. It adds a simple widget to your Wordpress Dashboard so you can see the data quickly and easily.
Author: Glen Allsopp
Version: 1.1
Author URI: http://www.viperchill.com
*/

add_action("admin_head", "latest_blog_head");

function latest_blog_head() {
	$req = $_SERVER['REQUEST_URI'];
	$sb = substr($req,strlen($req)-1,1);
$pos = strpos($req, "index.php");
	if($sb == "/" or $sb == "n" or $pos) {
	?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#updatemore a').click(function() {
		var url = $(this).attr('href');
		$plugin_dir = str_replace("/".basename(__FILE__),"",plugin_basename(__FILE__));
		$('#update_ct').html('<div id="loading">Loading...</div><br>');
		$('#update_ct').load('<?php echo WP_PLUGIN_URL.'/'.$plugin_dir.'/more.php?upage='?>'+url);
		$('#backto').show();
		if(url==0) {
			$('#backto').hide();
			$("#morea").attr('href',1);
		} else {
			$(this).attr('href',parseFloat(url)+1);
		}
		return false;
	});
});
</script>
<style>
#loading { top: 0; left: 0; color: 000000; background-color: #FFFFCC; padding: 5px 10px; font: 12px Arial; width:450px; }
#backto { display:inline; }
</style>
    <?
	}
}

function latest_blog_update() {
	$opt = get_option("lbog_");
	$the_key = $opt[key];
	if(empty($_GET[upage])) {
		$upage = 0;
	} else {
		$upage = $_GET[upage];
	}
	$dpage = $upage + 1;
	echo '<div style="" class="inside">'."\n";
	echo '<div style="" class="htmltop">'.$opt[htmltop].'</div><br>';
	echo '<div style="text-align:right" id="updatemore">x<a href="1" id="morea">Show More</a><div style="display:none" id="backto"> (<a href="0">Show Latest</a>)</div></div><br>';
	echo '<div id="update_ct">';
	$exp = explode(",",$the_key);
	if(!is_array($exp)) {
		echo "There is no key defined.";
		return;
	}
	foreach($exp as $key) {
		echo '<div class="rss-widget"><ul>';
		echo "<h4>".stripslashes($key)."</h4><br>"."\n";
		$array = googleblogsearch_update(trim($key),$opt[jumlah],$upage);
		if($array) {
			foreach($array as $data) {
				echo "<li><a class='rsswidget' href='".$data->link."' title='".stripslashes(strip_tags($data->title))."'>".$data->title."</a>&nbsp;<span>(Google)"."\n";
				echo '<div class="rssSummary">'.stripslashes($data->description)."</div></li>"."\n";
			}
		}	
		/*$array2 = technorati_update(trim($key),$opt[jumlah],$upage);
		if($array2) {
			foreach($array2 as $data) {
				echo "<li><a class='rsswidget' href='".$data->link."' title='".stripslashes(strip_tags($data->title))."'>".$data->title."</a>&nbsp;<span>(Technorati)"."\n";
				echo '<div class="rssSummary">'.stripslashes($data->description)."</div></li>"."\n";
			}
		}*/
		
		if(!$array) echo "<li>There are no results for this keyphrase</li>";
		echo "</ul></div>";
	}
	echo '</div>';
	echo '<div style="" class="htmltop">'.$opt[htmlbottom].'</div>';
	echo "</div>";
} 

function latest_blog_update_add_dashboard_widgets() {
	wp_add_dashboard_widget('latest_blog_update', 'Reputation Management by <a href="http://www.viperchill.com/wordpress-plugins/">ViperRep</a>', 'latest_blog_update');
} 

function technorati_update($q,$c = 10,$page = 0) {
	$pages = $page + 1;
	if($string = file_get_contents("http://ahlul.web.id/public/technorati2rss/technorati.php?page=".$pages."&q=".urlencode($q))) {
		$xml = simplexml_load_string($string);
		if(empty($xml)) return array();
		$ar = $xml->channel->item;
		$i = 0;
		foreach($ar as $key => $array) {
			if($i<$c) $data[] = $array;			
			$i++;
		}
		return $data;
	} else {
		return array();
	}
}

function googleblogsearch_update($q,$c = 10,$page = 0) {
	$page = $page * 10;
	$string = file_get_contents("http://blogsearch.google.com/blogsearch_feeds?hl=en&scoring=d&ie=utf-8&num=10&output=rss&start=".$page."&q=".urlencode($q));
	if($string) {
		$xml = simplexml_load_string($string);
		if(empty($xml)) return array();
		$ar = $xml->channel->item;
		$i = 0;
		foreach($ar as $key => $array) {
			if($i<$c) $data[] = $array;			
			$i++;
		}
		return $data;
	} else {
		return array();
	}
}

function last_blog_options() {
	
?>
	<style>
		/*Common Viper Products */
		
			#ViperFeed_main_container {
				width: 700px;
				border: 1px solid #DCDCDC;
				margin: 10px;
				padding: 10px;
			}
			
			.ViperFeed_input,.ViperFeed_label {
				margin: 3px;
				display: block;
				width: 95%;
			}
			
			.ViperFeed_form_element {
				float: left;
				width: 50%;
			}
			
			.ViperFeed_label {
				font-weight: bold;
				margin-top: 20px;
			}
			
			.ViperFeed_input {
				margin-left: 20px;
			}
		
		/* End Common Viper Products */
	</style>
	<div id="ViperFeed_main_container">
		<?php
			$plugin_dir = str_replace("/".basename(__FILE__),"",plugin_basename(__FILE__));
			echo file_get_contents("http://www.viperchill.com/rss/plugin_header.php?plugin=".$plugin_dir);
			
			extract($_POST);
			if($_POST[Submit]) {
				if(!add_option("lbog_",$lbog_)) {
					update_option("lbog_",$lbog_);
					echo "<div id='message' class='updated'><p>Your settings have been saved. Go to the <a href='index.php'>dashboard</a> to view them.</p></div>";
				}
			}
			$lbog_ = get_option("lbog_");
			
		?>
		<form method="post" action="">
			<div class="ViperFeed_label">
				Keyphrases you want to monitor:
			</div>
			<div class="ViperFeed_input">
				For multiple words, put the term in quotes. Separate multiple keyphrases with a comma.
				<input type="text" name="lbog_[key]" value="<?php echo htmlspecialchars(stripslashes($lbog_['key'])); ?>" size="80" />
			</div>
			<div class="ViperFeed_label">
				Results per keyphrase:
			</div>
			<div class="ViperFeed_input">
				<input type="text" name="lbog_[jumlah]" value="<?php echo $lbog_[jumlah]; ?>" size="10" />
			</div>
			<div class="ViperFeed_label">
				Top HTML/Text
			</div>
			<div class="ViperFeed_input">
				<textarea name="lbog_[htmltop]" cols="50" rows="6"><?php echo htmlspecialchars(stripslashes($lbog_[htmltop])); ?></textarea>
			</div>
			<div class="ViperFeed_label">
				Bottom HTML/Text
			</div>
			<div class="ViperFeed_input">
				<textarea name="lbog_[htmlbottom]" cols="50" rows="6"><?php echo htmlspecialchars(stripslashes($lbog_[htmlbottom])); ?></textarea>
			</div>
			<p class="submit">
				<input name="Submit" class="button-primary" value="Save Changes" type="submit">
			</p>
		</form>
	</div>
<?php
	
}


function last_blog_update_menu() {
  add_options_page('ViperRep', 'ViperRep', 5, __FILE__, 'last_blog_options');
}

add_action('admin_menu', 'last_blog_update_menu');
add_action('wp_dashboard_setup', 'latest_blog_update_add_dashboard_widgets' );

 ?>
