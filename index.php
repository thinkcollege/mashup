<?php $searchTerm = $_REQUEST["searchTerm"] ? $_REQUEST["searchTerm"] : ""; header("Content-Type:text/html; charset=UTF-8");
$nytpage = $_REQUEST["nytpage"] ? $_REQUEST["nytpage"] : "0";
$pagenext = $_REQUEST["pagenext"] ? $_REQUEST["pagenext"] : "";
$cleanTerm = $_REQUEST["searchTerm"] ? urlencode($searchTerm) : ""; ?>
<html><head><?php 
 ?><link rel="stylesheet" type="text/css" href="styles.css" />
	<script type="text/javascript" src="jquery-1.6.min.js"></script>
	<script type="text/javascript" src="scripts.js"></script>

</head><body><h1>Library of Congress Images/New York Times Article Archive</h1><div id="searchBox"><form action="" method="get"><label for="searchTerm">Search Term: </label><input type="text" id="searchTerm" name="searchTerm" /><input type="submit" value="Submit" /></form></div><div class="clearfix"><div id="libCon"><h2>Library of Congress Image Collections</h2><?php 


	
	$locjson = $_REQUEST["pagenext"] ? file_get_contents( 
    'http://www.loc.gov/pictures/search/?q=' . $cleanTerm . '&fo=json&sp=' . $pagenext) : file_get_contents( 
    'http://www.loc.gov/pictures/search/?q=' . $cleanTerm . '&fo=json');
$locoutput = json_decode($locjson, true);
if($locoutput['pages']['page_list']) { echo '<ul class="floatEm clearfix">'; foreach($locoutput['pages']['page_list'] as $pageList) { $pagenum = $pageList['number']; $pageurl = $pageList['url']; echo '<li><a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenum . '&nytpage=' . $nytpage . '">' . $pagenum . '</a></li>'; } echo '</ul>';}
foreach($locoutput['results'] as $key => $value)
{ 

	echo '<h3>'. $value['title']. '</h3><div class="viewDiv"><a href="' . $value['image']['full'] . '"><img class="hideFull" src="' . $value['image']['full'] . '" /></a><a class="preview" href="' . $value['image']['full'] . '"><img src="' . $value['image']['thumb'] . '" /></a></div><a href="' . $value['links']['item'] . '" target="_blank"><strong>Full Item Record</strong></a><br />'; 
	?>
	<dl class="accordionL">

<dt><a href="">Subject Headings</a></dt>
<dd>
	<?php
		foreach($value['subjects'] as $subject) echo '<a href="./?searchTerm=' . $subject . '">' . $subject . '</a><br />';
	echo '</dd></dl>';	
	}
	
?></div><div id="nYt"><h2>New York Times Article Archives, 1981-2012</h2><?php $nytjson = file_get_contents( 'http://api.nytimes.com/svc/search/v1/article?query=title:' . $cleanTerm . '&offset=' . $nytpage . '&api-key=bfb03a1d975ff053253f4895ebd3639b:5:59463337');
$nytoutput = json_decode($nytjson, true); 

$nytpages = ceil($nytoutput['total']/10);  
/******  build the pagination links ******/
// range of num links to show
$range = 3;
$currentpage = $nytpage;
// if not on page 1, don't show back links
if ($currentpage > 0) {
   // show << link to go back to page 1
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=0"><<</a> ';
   // get previous page num
   $prevpage = ($currentpage - 1);
   // show < link to go back to 1 page
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . $prevpage . '"><</a> ';
} // end if 

// loop to show links to range of pages around current page
for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
   // if it's a valid page number...
   if (($x > 0) && ($x <= ($nytpages-1))) {
      // if we're on current page...
      if ($x == $currentpage) {
         // 'highlight' it but don't make a link
         echo " [<strong>" . ($x +1) . "</strong>] ";
      // if not current page...
      } else {
         // make it a link
         echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . $x . '">' . ($x + 1) . '</a> ';
      } // end else
   } // end if 
} // end for
                 
// if not on last page, show forward and last page links        
if ($currentpage != ($nytpages - 1)) {
   // get next page
   $nextpage = $currentpage + 1;
    // echo forward link for next page 
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . $nextpage . '">></a> ';
   // echo forward link for lastpage
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . ($nytpages - 1) . '">>></a> ';
} // end if
/****** end build pagination links ******/



foreach($nytoutput as $key => $value) { if ($key == 'results') { foreach($value as $subkey => $subvalue) { $timestamp = strtotime($subvalue['date']);
$makedate = date("M d Y", $timestamp);
echo '<h3><a href="' . $subvalue['url'] . '" target="_blank">' . $subvalue['title']. '</a></h3><p class="artDate">' . $makedate . '</p>	<dl class="accordionR">

<dt><a href="">Intro Text</a></dt>
<dd>' . $subvalue['body'] . '</dd></dl>'; }
} 
} ?></div></div>

</body></html>
