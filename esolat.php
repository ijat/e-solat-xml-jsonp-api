<?php 
 /*
    e-Solat XML/JSON/JSONP API - A simple API to fetch Malaysia's prayers time from e-solat.gov.my.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$version = "0.05 rev 1 (Last-Modified: 02/02/2017)";
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
  $format = $_GET['format'];
  $e_ver = $_GET['ver'];
  $call = $_GET['callback'];

  if ($e_ver == 1) {
	$txt = file_get_contents('http://www2.e-solat.gov.my/solat.php?kod='.$kod);
  }
  else {
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
          'imsak' => ($format==12 ? strtoupper(date("g:i a", strtotime($matches[1][0]))) : $matches[1][0]),
          'subuh' => ($format==12 ? strtoupper(date("g:i a", strtotime($matches[1][1]))) : $matches[1][1]),
          'syuruk'=> ($format==12 ? strtoupper(date("g:i a", strtotime($matches[1][2]))): $matches[1][2]),
          'zohor'=> ($format==12 ? strtoupper(date("g:i a", strtotime($matches[1][3]))) : $matches[1][3]),
          'asar'=> ($format==12 ? strtoupper(date("g:i a", strtotime($matches[1][4]))) : $matches[1][4]),
          'maghrib'=> ($format==12 ? strtoupper(date("g:i a", strtotime($matches[1][5]))) : $matches[1][5]),
          'isyak' => ($format==12 ? strtoupper(date("g:i a", strtotime($matches[1][6]))) : $matches[1][6]),
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
      echo "<span style=\"font-family: Tahoma;font-size:13px;\">Ijat.my e-Solat API v".$version."<br/>----------------------------------------------------------------<br/><br/>Type unsupported. Only xml, json or jsonp type are supported.</span>";
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
?>
