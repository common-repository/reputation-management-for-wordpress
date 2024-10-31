<?php

include("../../../wp-load.php");

	$opt = get_option("lbog_");
	$the_key = $opt[key];
	if(empty($_GET[upage])) {
		$upage = 0;
	} else {
		$upage = $_GET[upage];
	}
	$dpage = $upage + 1;
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
		$array2 = technorati_update(trim($key),$opt[jumlah],$upage);
		if($array2) {
			foreach($array2 as $data) {
				echo "<li><a class='rsswidget' href='".$data->link."' title='".stripslashes(strip_tags($data->title))."'>".$data->title."</a>&nbsp;<span>(Technorati)"."\n";
				echo '<div class="rssSummary">'.stripslashes($data->description)."</div></li>"."\n";
			}
		}
		
		if(!$array or !array2) echo "<li>There are no results for this keyphrase</li>";
		echo "</ul></div>";
	}

?>
