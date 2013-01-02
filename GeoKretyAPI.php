<?php
class GeoKretyApi
{
    function __construct($secid)
    {
    	$this->secid = $secid;
    }
    
	private function TakeUserGeokrets()
	{
	 $url = "http://geokrety.org/export2.php?secid=$this->secid&inventory=1";

	 return simplexml_load_file($url);
	}
	
	public function MakeGeokretList()
	{
     $krety = $this->TakeUserGeokrets();
     $lista = 'liczba geokretów u siebie: ' . count($krety->geokrety->geokret).'<br>';
     
     $lista .= '<table>';
	 foreach ($krety->geokrety->geokret as $kret)
	{
		$lista .= '<tr><td></td><td><a href="http://geokrety.org/konkret.php?id='.$kret->attributes()->id.'">'. $kret .'</a></td></tr>'; 
	}
	$lista .= '</table>';
	echo $lista;
	}

	public function MakeGeokretSelector()
	{
		$krety = $this->TakeUserGeokrets();
 
		$selector = '<table>';
		$MaxNr = 0;
		foreach ($krety->geokrety->geokret as $kret)
		   {
		   	$MaxNr++;
		   	$selector .= '<tr>
					        <td>
					          <a href="http://geokrety.org/konkret.php?id='.$kret->attributes()->id.'">'.$kret.'</a>
					        </td>
					        <td>
					          <select name="GeoKretIDAction'.$MaxNr.'[action]" ><option value="-1">'.tr('GKApi13').'</option><option value="0">'.tr('GKApi12').'</option><option value="5">'.tr('GKApi14').'</option></select>
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[nr]" value="'.$kret->attributes()->nr.'">
                              <input type="hidden" name="GeoKretIDAction'.$MaxNr.'[id]" value="'.$kret->attributes()->id.'">
                              		
                             </td>
					     </tr>';
		   }
		$selector .= '</table>';
		$selector .= '<input type="hidden" name=MaxNr value="'.$MaxNr.'">';
		return $selector;
	}
	
	
	/**
	 * Function logs Geokret on geokrety.org using GeoKretyApi.
	 * @author Łza
	 * @param array $GeokretyArray
	 * @return boolean
	 */
	public function LogGeokrety($GeokretyArray)
	{ 
	    $postdata = http_build_query($GeokretyArray);
	
		$opts = array('http' =>
				array(
						'method'  => 'POST',
						'header'  => 'Content-type: application/x-www-form-urlencoded',
						'content' => $postdata
				)
		);
		
		$context = stream_context_create($opts);
		$result = file_get_contents('http://geokrety.org/ruchy.php', false, $context);
		$resultarray = simplexml_load_string($result);

		if (!$resultarray) 
		{
			$Tablica = print_r($GeokretyArray, true);
			$message = "przechwycono Blad z GeoKretyApi\r\n \r\n Tablica Logowania Geokreta:\r\n\r\n $Tablica \r\n\r\n  geokrety.org zwrocily nastepujący wynik: \r\n \r\n $result ";
			
			$headers = 'From: GeoKretyAPI on opencaching.pl' . "\r\n" .
            'Reply-To: rt@opencaching.pl' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

			mail('rt@opencaching.pl', 'GeoKretyApi returned error', $message, $headers);
			return false;
		}

		elseif ($GeokretyArray['wpt'] == $resultarray->geokrety->geokret->attributes()->waypoint) return true;
		else return false;
	
	}

}


class DbPdoConnect
{
	function __construct()
	{
	 include 'lib/settings.inc.php';
	 $this->server   = $opt['db']['server'];
	 $this->name     = $opt['db']['name'];
	 $this->username = $opt['db']['username'];
	 $this->password = $opt['db']['password'];
	}

	public function DbPdoConnect($query)
	{
		$dbh = new PDO("mysql:host=".$this->server.";dbname=".$this->name,$this->username,$this->password);
		$dbh -> query ('SET NAMES utf8');
		$dbh -> query ('SET CHARACTER_SET utf8_unicode_ci');
		
		$STH = $dbh -> prepare($query);
		$STH -> execute();

		return $STH -> fetch();
	}
}

?>