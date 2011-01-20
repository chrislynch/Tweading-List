<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<title>Tweading List: Book Recommendations from your Tweets</title>
<meta name="description" content="Tweading List analyses your tweets to provide book recommendations">
<meta name="keywords" content="twitter, book recommendations, twitter games">


<link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>

<div id="wrapper">
	<div id="subpage_left">
		<img src="images/logo.png"></img><br><br>
		<h1>Welcome to tweading List</h1>
		<p>tweading List provides book recommendations based on an analysis of your tweets.
		To get started, just enter your Twitter account name (or someone elses!) and the number of tweets to analyse into the form below.</p>
		
		<strong>Go, recommend!</strong>
		<form action="index.php">
			Twitter Account Name: <input type="text" name="twitterUser" value="">&nbsp;
			No. of Tweets: <select name="cloudSize">
				<option value="5">5</option>
				<option value="10">10</option>
				<option value="15">15</option>
				<option value="20">20</option>
				<option value="25" selected="selected">25</option>
				<option value="50">50</option>
				<option value="75">75</option>
				<option value="100">100</option>
			</select>
			<input type="submit">
		</form>
		<p></p>
<?php

/* 
 * Load last 100 tweets for the specific user. Break down into tweets themselves, we only need the text (title)
 */ 
if (isset($_GET['twitterUser'])) {
	include 'tweadinglist.php';
	$books = tweadingList();
	if(sizeof($books)> 0 ){
		print '<h2>Your Recommendations</h2>';
		print '<ul>';
		foreach($books as $book){
			if (isset($book['img'])){
				print '<a href="' . $book['info'] . '"><img src="' . $book['img'] . '" title="' . $book['title'] . '" alt="' . $book['title'] . '"></a>&nbsp;&nbsp;&nbsp;';	
			}
			
		}
		print '</ul>';	
	
?>
		<p></p>
		<h2>How does it work?</h2>
		
		<p>tweading List uses the Twitter Search API to grab your most recent tweets
		and then scans them for proper nouns and other keywords. Once your cloud is built,
		we use the Google Book Search API to try and find books that you might like.</p>
		<p>It's random, but we think that at least one in five to one in ten of the recommendations are good!</p>
		<p>If you like tweadingList, you can <a href="https://github.com/chrislynch/Tweading-List">get your own copy of the code from github</a></p>
	
<?php 
	}
}
?>	

</div>
	<div id="subpage_right">
	<br><br>
	<h1>Support tweadingList</h1><br>
	<p>Like tweadingList? Buy a copy of the latest graphic novel from its creator, Chris Lynch</p>
	<iframe src="http://rcm-uk.amazon.co.uk/e/cm?t=chrislynch&o=2&p=8&l=as1&asins=1905692374&fc1=000000&IS2=1&lt1=_blank&m=amazon&lc1=0000FF&bc1=000000&bg1=FFFFFF&f=ifr" style="width:120px;height:240px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>
	</div>

	<div class="divider"></div>
	<!-- 
	<div id="more">
		<div class="twitter">
			<div class="headline">
				<h4>App Name on Twitter</h4>
				<a href="" class="follow fader">Follow us</a>
			</div>
			<p><a href="">about 6 hours ago</a> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sedd do eiusmod 
				tempor incididunt ut labore et dolore magna aliqua.</p>
				
			<p><a href="">about 6 hours ago</a> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sedd do eiusmod 
				tempor incididunt ut labore et dolore magna aliqua.</p>
		</div>
		
		<div class="quote">
			<div class="headline">
				<h4>What others are saying</h4>
			</div>
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut.</p>
			<span class="quote_by"><a href="">Some company name</a></span>
		</div>
	</div>
	 -->
	<div id="footer">
		<div class="alignleft"><p>Copyright © 2010 <a href="http://www.engine4.net" title="Chris Lynch">Chris Lynch.</a>  All Rights reserved.</p></div>
		<div class="alignright">
			<a href="http://www.planetofthepenguins.com" title="Read the Blog">Read the Blog</a>
			<span class="divider"></span>
			<a href="http://www.gravit-e.co.uk" title="Web Development">Web Development</a>
			<span class="divider"></span>
			<a href="http://www.twitlogo.com/" title="http://www.twitlogo.com/">Logo by twitlogo.com</a>
		</div>
	</div>
</div>

</body>
</html>
