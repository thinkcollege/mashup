<?php $searchTerm = $_REQUEST["searchTerm"] ? $_REQUEST["searchTerm"] : ""; header("Content-Type:text/html; charset=UTF-8");
$nytpage = $_REQUEST["nytpage"] ? $_REQUEST["nytpage"] : "0";
$pagenext = $_REQUEST["pagenext"] ? $_REQUEST["pagenext"] : "";
$cleanTerm = $_REQUEST["searchTerm"] ? urlencode($searchTerm) : ""; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" ><head><?php 
 ?><link rel="stylesheet" type="text/css" href="styles.css" />
	<script type="text/javascript" src="jquery-1.6.min.js"></script>
	<script type="text/javascript" src="scripts.js"></script>

</head><body><h1>Library of Congress Images/New York Times Article Archive</h1><div id="searchBox"><form action="" method="get"><label for="searchTerm">Search Term: </label><input type="text" id="searchTerm" name="searchTerm" /><input type="submit" value="Submit" /></form></div><div class="clearfix"><div id="libCon"><h2>Library of Congress Image Collections</h2><?php 


   $ch = curl_init();

   // set the url to fetch
 if ($_REQUEST["pagenext"]) { curl_setopt($ch, CURLOPT_URL, 'http://www.loc.gov/pictures/search/?q=' . $cleanTerm . '&fo=json&sp=' . $pagenext); } else { curl_setopt($ch, CURLOPT_URL, 'http://www.loc.gov/pictures/search/?q=' . $cleanTerm . '&fo=json'); }

   // output content, not headers
   curl_setopt($ch, CURLOPT_HEADER, 0);

   // return the value instead of printing the response to browser
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

   // use a user agent to mimic a browser
   curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
$locjson = curl_exec($ch); ;
 curl_close($ch);
// decode JSON
$locoutput = json_decode($locjson, true);
// pagination of LOC records
if($locoutput['pages']['page_list']) { echo '<ul class="floatEm clearfix">'; foreach($locoutput['pages']['page_list'] as $pageList) { $pagenum = $pageList['number']; $pageurl = $pageList['url']; echo '<li><a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenum . '&nytpage=' . $nytpage . '">' . $pagenum . '</a></li>'; } echo '</ul>';}
// elements of record
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
	

   $chii = curl_init();

   // set the url to fetch
 curl_setopt($chii, CURLOPT_URL, 'http://api.nytimes.com/svc/search/v1/article?query=title:' . $cleanTerm . '&offset=' . $nytpage . '&api-key=bfb03a1d975ff053253f4895ebd3639b:5:59463337'); 

   // output content, not headers
   curl_setopt($chii, CURLOPT_HEADER, 0);

   // return the value instead of printing the response to browser
   curl_setopt($chii, CURLOPT_RETURNTRANSFER, 1);

   // use a user agent to mimic a browser
   curl_setopt($chii, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
$nytjson = curl_exec($chii); ;
 curl_close($chii);	
	
// get NYT data
?></div><div id="nYt"><h2>New York Times Article Archives, 1981-2012</h2><?php
$nytoutput = json_decode($nytjson, true); 

$nytpages = ceil($nytoutput['total']/10);  

// pagination for NYT records
$range = 3;
$currentpage = $nytpage;

if ($currentpage > 0) {
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=0"><<</a> ';
   $prevpage = ($currentpage - 1);
   // show < link to go back to 1 page
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . $prevpage . '"><</a> ';
} 

// loop to show links to range of pages around current page
for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++) {
   if (($x > 0) && ($x <= ($nytpages-1))) {
      // if we're on current page...
      if ($x == $currentpage) {
         echo " [<strong>" . ($x +1) . "</strong>] ";
      } else {
         echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . $x . '">' . ($x + 1) . '</a> ';
      }
   }
} 
                       
if ($currentpage != ($nytpages - 1)) {
   $nextpage = $currentpage + 1; 
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . $nextpage . '">></a> ';
   echo ' <a href="./?searchTerm=' . $searchTerm . '&pagenext=' . $pagenext . '&nytpage=' . ($nytpages - 1) . '">>></a> ';
} 
// end NYT pagination
// get NYT record elements



foreach($nytoutput as $key => $value) { if ($key == 'results') { foreach($value as $subkey => $subvalue) { $timestamp = strtotime($subvalue['date']);
$makedate = date("M d Y", $timestamp);
echo '<h3><a href="' . $subvalue['url'] . '" target="_blank">' . $subvalue['title']. '</a></h3><p class="artDate">' . $makedate . '</p>	<dl class="accordionR">

<dt><a href="">Intro Text</a></dt>
<dd>' . $subvalue['body'] . '</dd></dl>'; }
} 
} ?></div></div>

</body></html>
