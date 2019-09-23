<?php 
//Here is the doc scrapped by PhantomJS. We are going to parse it.
$result = file_get_contents('file.txt');

//echo $result;

///////////////////////////////////////////////////
//0. Get their fucking KEY (random string generated that blocks the scrap. They may have other protections)
///////////////////////////////////////////////////

$explo_key = explode('<app-root ng-version="7.2.15"><div id="wrapper"><navbar-component _nghost-',$result) ;
$explo_key_second_part = explode('-',$explo_key[1]) ;
$current_key = $explo_key_second_part[0];
echo "current anti-scrap key is : ".$current_key."\n";

$explo_line = explode('tr _ngcontent-'.$current_key.'-c7',$result) ;

foreach ($explo_line as $result_line) {
	//echo $result_line."\n\n\n\n\n";

///////////////////////////////////////////////////
//1. Defining card name
///////////////////////////////////////////////////

$explo_name = explode('<a _ngcontent-'.$current_key.'-c7="" href="',$result_line) ;

	if (preg_match("#^/prints/#", $explo_name[1])){
		$explo2 = explode('">', $explo_name[1]);
		$card_name_first_part = $explo2[1];
		$card_name_second_cut = explode('</a>',$card_name_first_part);
		$card_name = $card_name_second_cut[0];
		echo $card_name."\n";
	}


///////////////////////////////////////////////////
//2. Defining card set
///////////////////////////////////////////////////
$explo_set = explode('</i><a _ngcontent-'.$current_key.'-c7="" href="',$result_line) ;
		//echo $explo_set[1]."111111111\n\n\n\n";
		//echo $explo_set[0]."0000000\n\n\n\n";

	if (preg_match("#^/sets/#", $explo_set[2])){
		$explo2 = explode('">', $explo_set[2]);
		$card_set_first_part = $explo2[1];
		$card_set_second_cut = explode('</a>',$card_set_first_part);
		$card_set = $card_set_second_cut[0];
		echo $card_set."\n";
	}


///////////////////////////////////////////////////
//3. Defining new price
///////////////////////////////////////////////////

	$explo_new_price = explode('-c7="" class="text-right"> $',$result_line) ;
	$explo = explode(' </td><td _', $explo_new_price[1]);
	$new_card_price = $explo[0];
	echo "New price is: ".$new_card_price."\n";


///////////////////////////////////////////////////
//4. Defining old price
///////////////////////////////////////////////////

	$explo_new_price = explode('-c7="" class="text-right"> $',$result_line) ;
	$explo = explode(' </td><td _', $explo_new_price[2]);
	$old_card_price = $explo[0];
	echo "Old price is: ".$old_card_price."\n";

///////////////////////////////////////////////////
//5. Getting the variation
///////////////////////////////////////////////////	

	$explo_variation = explode('c7="" class="text-right alert-danger"><!---->',$result_line);
	if(!isset($explo_variation[1])){
			$explo_variation = explode('alert-success"><!----><span _ngcontent-mcq-c7="">+</span>',$result_line) ;
	}

	$explo = explode('% </td>', $explo_variation[1]);
	$card_variation = $explo[0];
	$card_variation = preg_replace("/,+/", "", $card_variation);
	echo "Variation is: ".$card_variation."\n";
	

/// DB recording
	try
	{
	// windows
	$bdd = new PDO('mysql:host=localhost;dbname=mtgstocks;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

	// mac
	// $bdd = new PDO('mysql:host=localhost;dbname=mkm_users_scrap;charset=utf8', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

	// pour aller au serveur 
	//$bdd = new PDO('mysql:host=51.255.196.228;dbname=mkm_users_scrap;charset=utf8', '', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

	// depuis le serveur 
	//$bdd = new PDO('mysql:host=localhost;dbname=mkm_users_scrap;charset=utf8', '', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

	}
	catch(Exception $e)
	{
	    die('Erreur : '.$e->getMessage());
	}


	try
	{
	$pdoStatement = $bdd->prepare('INSERT INTO prices (card_name, set_name, new_price, old_price, variation, day_data) VALUES(?,?,?,?,?,NOW())');

	$pdoStatement->bindParam(1, $card_name, PDO::PARAM_STR);
	$pdoStatement->bindParam(2, $card_set, PDO::PARAM_STR);
	$pdoStatement->bindParam(3, $new_card_price);
	$pdoStatement->bindParam(4, $old_card_price);
	$pdoStatement->bindParam(5, $card_variation);
	$pdoStatement->execute();
	}
	catch(Exception $e)
	{
	    echo('Erreur : '.$e->getMessage());
	}
}

?>