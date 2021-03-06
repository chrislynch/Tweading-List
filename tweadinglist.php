<?php

function tweadingList(){
	$twitterUser = $_GET['twitterUser'];
	$tweets = file_get_contents("http://search.twitter.com/search.atom?lang=en&q=$twitterUser&rpp=100");
	$tweetsXML = simplexml_load_string($tweets);
	$tweetsArray = get_object_vars($tweetsXML);
	
	$return = array();
	
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
		$bookCount = 8;
		$googleSleep = 250;
		// print '<h2>Your Recommendations</h2><table>';
		foreach($searchTerms as $searchTerm => $score){
			// Search for a book
			$searchTerm = urlencode($searchTerm);
			$books = file_get_contents("http://books.google.com/books/feeds/volumes?q=$searchTerm&start-index=1&max-results=1"); 
			$booksXML = simplexml_load_string($books);
			$booksArray = get_object_vars($booksXML);
			
			if (isset($booksArray['entry'])){
				$book = $booksArray['entry'];
				$returnbook = array();
				/*
			 	* Output book data
			 	*/
				
				// print '<tr><td>';
				
				foreach($book->link as $linkData){
					
					$links = get_object_vars($linkData);
					$links = $links['@attributes'];
					$link_type = array_reverse(explode('/',$links['rel']));
					$link_type = $link_type['0'];
					if ($link_type == 'thumbnail'){
						// print '<img src="' . $links['href'] . '">';
						$returnbook['img'] = $links['href']; 
					}
				}
				
				// print '</td><td><h4>';		
				// print $book->title . '<br>';
				// print '</h4></td><td><ul>';
				$returnbook['title'] = $book->title;
						
				foreach($book->link as $linkData){
					$links = get_object_vars($linkData);
					$links = $links['@attributes'];
					if ($links['type'] == 'text/html'){
						$link_type = array_reverse(explode('/',$links['rel']));
						$link_type = $link_type['0'];
						// print '<li><a href="' . $links['href'] . '">' . $link_type . '</a></li>';
						$returnbook[$link_type] = $links['href'];
					}
				}
				// print '</ul></td></tr>';
				// print '<pre>'  . print_r($book,TRUE) . '</pre><br>';
				
				// Decrement book counter
				$bookCount --;
				if ($bookCount <= 0) {break;}
				$return[] = $returnbook;
			}
			
			// Sleep (if required to support Google API
			usleep($googleSleep);
		}
		
		ksort($searchTerms);
		/*
		print '<tr><tr><td colspan="2"><h4>Your cloud</h4>';
		foreach($searchTerms as $searchTerm => $score){
			$fontsize = -3 + $score;
			if ($fontsize > 0) { $fontsize = '+' . $fontsize;}
			print '<font size="' . $fontsize . '">' . $searchTerm . '</font>&nbsp;';
		}
		print '</td></tr>';
		
		print '</table>';
		*/
	}
	return $return;
}

function my_print_r($data,$return = FALSE){
	$result = '<pre>' . print_r($data,TRUE) . '</pre><br>';
	if (!$return) { print $result; } 
	return $result; 
}

?>