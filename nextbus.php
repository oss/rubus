<?php 
	ini_set('display_errors', 0);
	//error_reporting(E_ALL|E_STRICT);
	require_once('simplexml.php');
    require_once('textio.php');

    if(!empty($_REQUEST['Body']))
    {
        $_GET['s'] = $_REQUEST['Body'];
        $_REQUEST['s'] = $_REQUEST['Body'];
    }

    if(!empty($_REQUEST['From']))
    {
        $_REQUEST['q'] = $_REQUEST['From'];
        $_GET['q'] = $_REQUEST['From'];
    }

	function nice_name($str)
	{
		$arr = explode(' ', $str);
		$i = 0;
		$rv = '';
		foreach($arr as $val)
		{
			if($i == 0) $rv .= $val.' ';
			else $rv .= $val[0];
			$i++; 
		}
		return strtoupper($rv);
	}

	function strip_start($haystack, $needle){
		$haystack = strtolower($haystack);
		$needle = strtolower($needle);
		$pos = strpos($haystack, $needle);

		if($pos !== FALSE)
		{
			$haystack = substr($haystack, $pos+1+strlen($needle));
		}
		return $haystack;
	}

	$stopIds = Array();
	$_GET['s'] = strip_start($_GET['s'], 'rubus');
	$_GET['s'] = strip_start($_GET['s'], 'ru bus');

    # if we don't have a term here, let's assume it's help.
    
	if(!$_GET["s"] || $_GET["s"] == "") $_GET["s"] = "help";
	$word = trim(strtolower($_GET["s"]));
	$original = trim(strtolower($_GET["s"]));

	ob_start();
	for(;;) 
	{
		switch($word)
		{
			case "qua":
				if(strpos($original, "drop") !== FALSE) 
					$stopIds[] = 1027;
				else
					$stopIds[] = 1030;
				break;
			case "bus":
				if(strpos($original, "sui") !== FALSE)
					$stopIds[] = 1007;
				else
					$stopIds[] = 1056;
				break;
			case "lib":
				if(strpos($original, "t") !== FALSE)
					$stopIds[] = 1053;
				else
					$stopIds[] = 1006;
				break;
			case "liv":
				if(strpos($original, "com") !== FALSE)
					$stopIds[] = 1057;
				else if(strpos($original, "p") !== FALSE)
					$stopIds[] = 1028;
				else
					$stopIds[] = 1029;
				break;
			case "col":
				if(strpos($original, "h") !== FALSE)
					$stopIds[] = 1052;
				else if(strpos($original, "y") !== FALSE)
					$stopIds[] = 1042;
				else
					$stopIds[] = 1062;
				break;
			case "q":
			case "quad":
			case "quads":
				$stopIds[] = 1030;
				break;

			case "wrhouses":
			case "wrh":
			case "whouse":
			case "who":
			case "warehouses":
			case "warehouse":
			case "war":
				$stopIds[] = 1031;
				break;

			case "cedar":
			case "cedar lane":
			case "cedas lane":
			case "cedar ln":
			case "ln":
			case "clane":
			case "cla":
			case "lan":
			case "capts":
			case "cap":
			case "ced":
			case "clapts":
				$stopIds[] = 1026;
				break;

			case "ttop":
			case "tto":
			case "treetop":
			case "tree":
			case "tre":
			case "tree apts":
			case "ttapts":
			case "tta":
				$stopIds[] = 1032;
				break;

			case "harrison":
			case "hson":
			case "hso":
			case "hson ave":
			case "harri":
			case "harr":
			case "har":
			case "have":
			case "hav":
				$stopIds[] = 1025;
				break;

			case "tstation":
			case "tst":
			case "train":
			case "train station":
			case "tra":
			case "trn":
				$stopIds[] = 1060;
				$stopIds[] = 1064;
				$stopIds[] = 1065;
				break;

			case "zimmerli":
			case "zim":
			case "zimm":
			case "zimmli":
				$stopIds[] = 1069;
				$stopIds[] = 1017;
				break;

			case "nursing school":
			case "nursing":
			case "nurse":
			case "nurs":
			case "nsc":
			case "nur":
				$stopIds[] = 1026;
				break;

			case "colony":
			case "colony house":
			case "chouse":
			case "cho":
				$stopIds[] = 1042;
				break;

			case "quaddrop":
			case "quadsdrop":
			case "quads drop":
			case "qdrop":
			case "quad drop":
			case "dropoff":
			case "dro":
				$stopIds[] = 1027;
				break;

			case "hlth cntr":
			case "health":
			case "hlth":
			case "hlt":
			case "hcn":
			case "hce":
			case "hea":
			case "hcntr":
			case "hcenter":
			case "health center":
			case "lhc":
				$stopIds[] = 1034;
				break;

			case "labor":
			case "sears":
			case "sea":
			case "labor ed":
			case "lbuild":
			case "labor education":
			case "labor education":
			case "lab":
				$stopIds[] = 1049;
				break;

			case "liberty":
			case "libty":
			case "lty":
			case "lbty":
			case "lbrty":
			case "lbr":
			case "lbt":
			case "lbu":
				$stopIds[] = 1053;
				break;

			case "rockoff":
			case "rock":
			case "roff":
			case "rof":
			case "roc":
				$stopIds[] = 1043;
				break;
			case "cto":
			case "c t":
			case "c-t":
				$stopIds[] = 1037;
				break;

			case "patterson":
			case "paterson":
			case "bk":
			case "bki":
			case "bur":
			case "burger":
			case "bking":
			case "king":
			case "kin":
			case "burger king":
			case "king":
			case "p st":
			case "pat":
			case "pson":
			case "pso":
				$stopIds[] = 1054;
				$stopIds[] = 1067;
				break;

			case "public":
			case "public safety":
			case "saf":
			case "pubsafety":
			case "pub safety":
			case "psafety":
			case "psa":
			case "pub":
			case "psb":
				$stopIds[] = 1044;
				break;

			case "jameson":
			case "james":
			case "jam":
			case "cabaret":
			case "caba":
			case "cab":
			case "ctheatre":
			case "cth":
				$stopIds[] = 1045;
				break;

			case "passion":
			case "pas":
			case "puddle":
			case "pud":
			case "ppuddle":
			case "ppu":
			case "pass":
			case "pudd":
			case "passion puddle":
			case "redoak":
			case "red oak":
			case "roa":
			case "red":
			case "oak":
				$stopIds[] = 1011;
				break;

			case "foodsci":
			case "food":
			case "foo":
			case "fsci":
			case "fsb":
			case "fsc":
				$stopIds[] = 1012;
				break;

			case "biel":
			case "beil":
			case "bei":
			case "bie":
			case "broad":
			case "biel road":
			case "bielroad":
				$stopIds[] = 1048;
				break;

			case "lipmann":
			case "lipman":
			case "lman":
			case "lma":
			case "lip":
			case "lmann":
			case "lhall":
			case "lha":
				$stopIds[] = 1047;
				break;

			case "gibbons":
			case "gib":
			case "gibb":
			case "gbons":
			case "gbbons":
			case "gbb":
			case "gbo":
			case "new gibbons":
			case "newgibbons":
			case "new":
				$stopIds[] = 1051;
				break;

			case "katz":
			case "katzenbach":
			case "kat":
			case "kba":
			case "kbach":
			case "kac":
				$stopIds[] = 1014;
				break;

			case "hend":
			case "henderson":
			case "hapts":
			case "hap":
			case "hen":
				$stopIds[] = 1050;
				break;

			case "chall":
			case "c":
			case "cha":
			case "dou":
			case "dcc":
			case "college hall":
				$stopIds[] = 1015;
				$stopIds[] = 1052;
				break;

			case "lot100":
			case "lot 100":
			case "l100":
			case "lot":
			case "livingston commuter lot":
			case "liv comm lot":
				$stopIds[] = 1057;
				break;

			case "beck":
			case "bec":
			case "rac":
			case "bhall":
			case "bha":
			case "beck hall":
			case "pla":
			case "lpa":
			case "lpl":
				$stopIds[] = 1028;
				break;


			case "lsc":
			case "lcc":
			case "livingston student center":
			case "livingston":
				$stopIds[] = 1029;
				break;

			case "rsc":
			case "rcc":
			case "rutgers student center":
			case "rut":
			case "college ave":
			case "college":
			case "student center":
			case "brower":
			case "bro":
			case "cac":
			case "ccc":
				$stopIds[] = 1062;
				break;

			case "sac":
			case "cas":
			case "geo":
			case "student activities center":
			case "stu":
			case "act":
			case "fre":
				$stopIds[] = 1001;
				break;

			case "scott hall":
			case "sha":
			case "sco":
			case "sec":
			case "grease trucks":
			case "tru":
			case "gre":
			case "scott":
			case "sctt hall":
			case "sctt":
			case "sct":
			case "s":
			case "murray":
			case "mur":
				$stopIds[] = 1055;
				break;

			case "vis":
			case "vis cntr":
			case "visitor":
			case "visitor center":
			case "vc":
			case "vcn":
			case "vcntr":
			case "vsc":
				$stopIds[] = 1002;
				break;

			case "sonny":
			case "son":
			case "werblin":
			case "werb":
			case "wer":
			case "wlin":
			case "wli":
			case "wrblin":
			case "wblin":
			case "wbl":
			case "wrb":
				$stopIds[] = 1003;
				$stopIds[] = 1022;
				break;

			case "hill center":
			case "hill":
			case "hil":
			case "ser":
			case "h":
				$stopIds[] = 1004;
				$stopIds[] = 1066;
				break;

			case "ar":
			case "arc":
			case "all":
			case "ali":
			case "sciences building":
			case "science buildings":
			case "science building":
			case "science":
			case "science":
			case "sci":
				$stopIds[] = 1036;
				$stopIds[] = 1005;
				$stopIds[] = 1041;
				break;

			case "busch suites":
			case "suites":
			case "sui":
			case "bs":
			case "bsu":
			case "bsuites":
				$stopIds[] = 1007;
				break;	

			case "buell":
			case "bue":
			case "bapts":
			case "bap":
			case "buell apts":
				$stopIds[] = 1009;
				break;

			case "libofsci":
			case "lsm":
			case "l.s":
				$stopIds[] = 1006;
				break;	

			case "davidson":
			case "david":
			case "dvdsn":
			case "dvdson":
			case "dvd":
			case "dav":
				$stopIds[] = 1023;
				break;

			case "bsc": 
			case "bcc": 
			case "busch": 
			case "bush": 
			case "b": 
				$stopIds[] = 1056;
				$stopIds[] = 1008;
				break;

			case "stadium":
			case "stad":
			case "sta":
			case "stdium":
			case "stdum":
			case "std":
			case "wes":
				$stopIds[] = 1024;
				break;
			
			case "qgjmt":
				echo "Call me at 973-862-7118 to receive $500 <br />";
				exit;
			case "help":
			default:
		}
        # if we found stop ids. we're done here.
		if(count($stopIds)) break;

        # if the word is equal to the first 3 letters we couldn't find a matching stop.
		if($word == substr($word, 0, 3)) 
		{
			echo "Usage: 'RUBUS [stopname]'\n";
			echo "Think of a word that represents a stop, it will prolly work\n";
			echo "More info: eden/~vverna/nextbus";
			break;
		}
		$word = substr($word, 0, 3);
	}

	if(count($stopIds))
	{
		$counter = 0;
        # for each stopID under this stop.
		foreach($stopIds as $i) 
		{
			$url = "http://webservices.nextbus.com/service/publicXMLFeed?a=rutgers&command=predictions&stopId=".$i;
			$xml = new XMLParser(file_get_contents($url));
			$xml->Parse();

			$predictions = $xml->document->tagChildren;
			if($counter === 0)
				echo $predictions[0]->tagAttrs['stoptitle']."\n";
			foreach($predictions as $item)
			{
				if(count($item->tagChildren) > 0)
				{
					$prediction = $item->tagChildren[0]->tagChildren;
					if($prediction)
						echo strtoupper($item->tagAttrs['routetag']);


					if(count($stopIds) > 1)
						echo ' '.nice_name($item->tagChildren[0]->tagAttrs['title']);

					$num_predictions = 0;
					foreach($prediction as $x)
					{
						if($num_predictions++ == 3)
							break;
						if(trim($x->tagAttrs['minutes']))
							echo " " . $x->tagAttrs['minutes'];
					}
					if($prediction)
						echo "\n";
				}
			}
			$counter++;	
		}
	}
	else
	{
		$stopIds[] = -1;
	}

	$echoed_data = ob_get_clean();
    // we don't need this. We just respond via Twiml.
    //if(!empty($_REQUEST['From']))
        //send_message($_REQUEST['From'], $echoed_data);
?>
<Response>
    <Sms> <?php echo $echoed_data; ?> </Sms>
</Response>
