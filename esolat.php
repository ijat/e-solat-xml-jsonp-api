<?php 
 /*
 e-Solat XML/JSON/JSONP API
 Created by Ijat @ Ijat.my (Reizn.com)

 This work is licensed under the Creative Commons Attribution-NonCommercial-ShareAlike 3.0 Unported License. 
 To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-sa/3.0/ 
 or send a letter to Creative Commons, 444 Castro Street, Suite 900, Mountain View, California, 94041, USA.
 */

$version = "0.04 (Last-Modified: 13/02/2014)";
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
date_default_timezone_set('Asia/Kuala_Lumpur');

if (empty($_GET['type'])) {
  echo "<span style=\"font-family: Tahoma;font-size:13px;\"><span style=\"font-weight:bold;font-size:15px;\">Ijat.my E-Solat API v".$version."</span><br/><br/>Error. Invalid syntax.</span>";
  die();
}
else {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Max-Age: 3628800');
  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

  $type = $_GET['type'];
  $kod = $_GET['kod'];
  $e_ver = $_GET['ver'];
  $call = $_GET['callback'];

  if (!$e_ver or $e_ver == 1) {
	$txt = file_get_contents('http://www2.e-solat.gov.my/solat.php?kod='.$kod);
  }
  else if ($e_ver == 2) {
	$txt = file_get_contents("http://www.e-solat.gov.my/web/my_waktusolat/mod_waktusolatget.php?negeri=;;;;;".$kod);
  }
  # 
  #else if ($e_ver == 3) {
  #	$txt = file_get_contents('http://www.e-solat.gov.my/web/waktusolatluarnegara/mod_waktusolatget.php?negeri='.$kod);
  #  }
  
  if (strlen($txt) < 500) {
    echo "<span style=\"font-family: Tahoma;font-size:13px;\"><span style=\"font-weight:bold;font-size:15px;\">Ijat.my E-Solat API v".$version."</span><br/><br/>Unable to fetch data from esolat server. Wrong code?</span>";
    die();
  }

  if ($txt === false) {
    echo "<span style=\"font-family: Tahoma;font-size:13px;\"><span style=\"font-weight:bold;font-size:15px;\">Ijat.my E-Solat API v".$version."</span><br/><br/>Error. Please try again.</span>";
    die();
  }
  else {
  
	if (!$e_ver or $e_ver == 1) {
		$tempat = get_string_between($txt,'<font color=#005257 size=3><b>','</b></font>');
		$tempat = ucwords(strtolower($tempat));
		$hdate = get_string_between($txt,'<br>','</font> </td>');
		$re1='.*?';
		$re2='((?:(?:[0-1][0-9])|(?:[2][0-3])|(?:[0-9])):(?:[0-5][0-9])(?::[0-5][0-9])?(?:\\s?(?:am|AM|pm|PM))?)';
		preg_match_all ("/".$re1.$re2."/is", $txt, $matches);
		$a_date = date('d/m/Y');
	}
	else if ($e_ver == 2) {
		$tempat = GetBetween('<b>','</b></font>',$txt);
		$tempat = ucwords(strtolower($tempat));
		preg_match_all("/(\d+:\d+)/", $txt, $matches);
		preg_match_all("/(\d+\s\w+\s\w+\s\d+)/", $txt, $a_hd);
		$hdate = $a_hd[0][0];
	}
    
	$array = array(
          'tempat' => $tempat,
          'doy' => date("z") + 1,
          'hdate' => $hdate,
          'kod' => $kod,
          'imsak' => $matches[1][0],
          'subuh' => $matches[1][1],
          'syuruk'=> $matches[1][2],
          'zohor'=> $matches[1][3],
          'asar'=> $matches[1][4],
          'maghrib'=> $matches[1][5],
          'isyak' => $matches[1][6],
          'version' => $version,
          'by' => 'Ijat [Ijat.my]',
    );

      $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ijat.my/>');

      $node = $xml->addChild('esolat');
      foreach($array as $key=>$value){
          $node->addChild(str_replace(' ','_',$key), trim($value));
      }

      $dom = new DOMDocument('1.0');
      $dom->preserveWhiteSpace = false;
      $dom->formatOutput = true;
      $dom->loadXML($xml->asXML());
      $thexml = $dom->saveXML(); 

    if ($type == 'xml') {
      header('Content-Type: text/xml');
      echo $thexml;
    }
    elseif ($type == 'json' Or $type == 'jsonp') {
      $fileContents = str_replace(array("\n", "\r", "\t"), '', $thexml);
      $fileContents = trim(str_replace('"', "'", $fileContents));
      $simpleXml = simplexml_load_string($fileContents);
      $data = json_encode($simpleXml);

      if ($type == 'json') {
        header('Content-Type: application/json; charset=utf8');
        echo $data;
      }
      elseif ($type == 'jsonp') {
        if ($call) {
          header('Content-Type: text/javascript; charset=utf8');
          echo $call . '(' . $data . ');';
        }
        else {
          header('Content-Type: application/json; charset=utf8');
          echo $data;
        }
      }

    }
    else {
      echo "<span style=\"font-family: Tahoma;font-size:13px;\">Reizn.com e-Solat API v".$version."<br/>----------------------------------------------------------------<br/><br/>Type unsupported. Only xml, json or jsonp type are supported.</span>";
      die();
    }
  }

}

function GetBetween($var1="",$var2="",$pool){
	$temp1 = strpos($pool,$var1)+strlen($var1);
	$result = substr($pool,$temp1,strlen($pool));
	$dd=strpos($result,$var2);
	if($dd == 0){
	$dd = strlen($result);
	}
	return substr($result,0,$dd);
}

/*function HijriDate($txt) {
	  $re1='(\\d+)';  # Integer Number 1
	  $re2='(\\s+)';  # White Space 1
	  $re3='((?:[a-z][a-z]+))'; # Word 1
	  $re4='(\\s+)';  # White Space 2
	  $re5='((?:(?:[1]{1}\\d{1}\\d{1}\\d{1})|(?:[2]{1}\\d{3})))(?![\\d])';  # Year 1
	  if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5."/is", $txt, $matches))
	  {
		  $int1=$matches[1][0];
		  $ws1=$matches[2][0];
		  $word1=$matches[3][0];
		  $ws2=$matches[4][0];
		  $year1=$matches[5][0];
		  return "$int1 $ws1 $word1 $ws2 $year1";
	  }
	  else {return date("d/m/Y");}
}

function printVar($var) {
    echo '<pre>';
    var_dump($var); 
    echo '</pre>';
} */
?>