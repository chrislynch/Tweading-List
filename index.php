<html>
<head>
	<title>tweadingList</title>
</head>
<body>
<h3>tweadingList: Book recommendations based on your tweets</h3>
<form action="index.php">
	<input type="text" name="twitterUser" value="">
	<select name="cloudSize">
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
<?php

/* 
 * Load last 100 tweets for the specific user. Break down into tweets themselves, we only need the text (title)
 */ 
if (isset($_GET['twitterUser'])) {

	$twitterUser = $_GET['twitterUser'];
	$tweets = file_get_contents("http://search.twitter.com/search.atom?lang=en&q=$twitterUser&rpp=100");
	$tweetsXML = simplexml_load_string($tweets);
	$tweetsArray = get_object_vars($tweetsXML);
	if (!isset($tweetsArray['entry'])){
		print '<p>Sorry, we couldn\'t find enough tweets to build a cloud for that username.</p>';
	} else {

		$tweetsArray = $tweetsArray['entry'];
		// print '<pre>' . print_r($tweetsArray,TRUE) . '</pre>';
		
		
		/*
		 * Break down the tweets, looking for proper nouns and strings of proper nouns
		 */
		// Create an array to hold our search terms.
		// Create temporary storage for variables
		$searchTerms = array();
		$previousWord = '';
		
		foreach($tweetsArray as $tweet){
			// Explode each tweet into words and reset markers.
			$previousWord = '';
			$tweetWords = explode(' ',$tweet->title);
			
			foreach($tweetWords as $tweetWord){
				// $searchTerms[$tweetWord] = substr($tweetWord,0,1);
				$tweetWord = trim($tweetWord);
				if (substr($tweetWord,0,1) <> '@' && substr($tweetWord,0,1) <> '#' && strlen($tweetWord) > 3){
					if (substr($tweetWord,0,1) === strtoupper(substr($tweetWord,0,1)) ){
						// Upper case first character. Therefore a proper noun
						if (key_exists($tweetWord, $searchTerms)){ $searchTerms[$tweetWord] ++; } else {$searchTerms[$tweetWord] = 1;}
						unset($searchTerms[$previousWord]);
						if (strlen($previousWord) > 0){
							$previousWord = trim($previousWord . ' ' . $tweetWord);
							if (key_exists($previousWord, $searchTerms)){ $searchTerms[$previousWord] ++; } else {$searchTerms[$previousWord] = 1;}
						} else {
							$previousWord = trim($previousWord . ' ' . $tweetWord);
						}	
					} else {
						if (strlen($previousWord) > 0) {
							$previousWord = '';
						}
					}	
				}
			}
		}
		arsort($searchTerms);
		
		/*
		 * Use the top 10 words to search for books
		 */
		$bookCount = 5;
		$googleSleep = 1;
		print '<table>';
		foreach($searchTerms as $searchTerm => $score){
			// Search for a book
			$searchTerm = urlencode($searchTerm);
			$books = file_get_contents("http://books.google.com/books/feeds/volumes?q=$searchTerm&start-index=1&max-results=1"); 
			$booksXML = simplexml_load_string($books);
			$booksArray = get_object_vars($booksXML);
			
			if (isset($booksArray['entry'])){
				$book = $booksArray['entry'];
		
				/*
			 	* Output book data
			 	*/
				
				print '<tr><td>';
				
				foreach($book->link as $linkData){
					$links = get_object_vars($linkData);
					$links = $links['@attributes'];
					$link_type = array_reverse(explode('/',$links['rel']));
					$link_type = $link_type['0'];
					if ($link_type == 'thumbnail'){
						print '<img src="' . $links['href'] . '">'; 
					}
				}
				
				print '</td><td>';		
				print $book->title . '<br>';
				print '</td><td><ul>';
						
				foreach($book->link as $linkData){
					$links = get_object_vars($linkData);
					$links = $links['@attributes'];
					if ($links['type'] == 'text/html'){
						$link_type = array_reverse(explode('/',$links['rel']));
						$link_type = $link_type['0'];
						print '<li><a href="' . $links['href'] . '">' . $link_type . '</a></li>';
					}
				}
				print '</ul></td></tr>';
				// print '<pre>'  . print_r($book,TRUE) . '</pre><br>';
				
				// Decrement book counter
				$bookCount --;
				if ($bookCount <= 0) {break;}
			}
			
			// Sleep (if required to support Google API
			sleep($googleSleep);
		}
		
		ksort($searchTerms);
		print '<tr><tr><td colspan="2"><h4>Your cloud</h4>';
		foreach($searchTerms as $searchTerm => $score){
			$fontsize = -3 + $score;
			if ($fontsize > 0) { $fontsize = '+' . $fontsize;}
			print '<font size="' . $fontsize . '">' . $searchTerm . '</font>&nbsp;';
		}
		print '</td></tr>';
		
		print '</table>';
		
	}
	
}


function my_print_r($data,$return = FALSE){
	$result = '<pre>' . print_r($data,TRUE) . '</pre><br>';
	if (!$return) { print $result; } 
	return $result; 
}
?>
</body>
</html>