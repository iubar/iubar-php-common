<?php

// html_entities_demo();
// xml_entities_demo();

// // REF: http://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references

// // Nota inoltre la differenza tra "%20" e "+":

// // - multipart/form-data uses MIME encoding
// // - application/x-www-form-urlencoded uses '+' 
// // - properly encoded URIs use %20

// function html_entities_demo(){
// 	$text = "?email=pippo@email.it&oggetto=dov'è il sole ?&valuta=€";
// 	echo "string: " . $text . PHP_EOL;
// 	echo PHP_EOL . PHP_EOL;
// 	echo "urlencode(): " . urlencode($text);
// 	echo PHP_EOL . PHP_EOL;
// 	echo "htmlentities(): " . htmlentities($text);
// 	echo PHP_EOL . PHP_EOL;
// 	echo "htmlentities(HTML5): " . htmlentities($text, ENT_QUOTES | ENT_HTML5, "UTF-8");
// 	echo PHP_EOL . PHP_EOL;
// 	echo "htmlentities(ENT_HTML401): " . htmlentities($text, ENT_QUOTES | ENT_HTML401, "UTF-8");	
// 	echo PHP_EOL . PHP_EOL;
// 	echo "htmlentities(ENT_XHTML): " . htmlentities($text, ENT_QUOTES | ENT_XHTML, "UTF-8");
// 	echo PHP_EOL . PHP_EOL;

// 	$data = array('email'=>"pippo@email.it",
// 			"oggetto"=>"dov'è il sole ?",
// 			"valuta"=>"€");
// 	echo "http_build_query(): " . http_build_query($data) . PHP_EOL;		// http_build_query() è la soluzione migliore !!!!
// 	echo PHP_EOL . PHP_EOL;
// }

// function xml_entities_demo(){
// 	$text = "Test &amp; <b> and encode è fatto </b> :)";
// 	echo "string: " . $text . PHP_EOL;
// 	echo "xml_entities2(): " . xml_entities2($text);
// 	echo PHP_EOL . PHP_EOL;
// 	echo "xml_entities(): " . xml_entities($text);
// 	echo PHP_EOL . PHP_EOL;
// 	echo "htmlspecialchars(): " . htmlspecialchars($text, ENT_QUOTES, 'UTF-8');  // solo 5 conversioni 
// 														// htmlspecialchars() è la soluzione migliore !!!!
// 	echo PHP_EOL . PHP_EOL;
// }

// function xml_entities2($string) {
// 	// XML only has 5 entities. Parsing into html entities breaks on certain characters because it creates an unencoded & in the entity itself.
// 	return str_replace(array("&", "<", ">", "\"", "'"),
// 			array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;"), $string);
// }

// function xml_entities($string) {
// 	// Simple function that escapes with the five "predefined entities" that are in XML:

// 	// Usage example Demo:
// 	// $text = "Test &amp; <b> and encode </b> :)";
// 	// echo xml_entities($text);
// 	// Output:
// 	// Test &amp;amp; &lt;b&gt; and encode &lt;/b&gt; :)

// 	return strtr(
// 			$string,
// 			array(
// 					"<" => "&lt;",
// 					">" => "&gt;",
// 					'"' => "&quot;",
// 					"'" => "&apos;",
// 					"&" => "&amp;",
// 			)
// 	);
// }



?>