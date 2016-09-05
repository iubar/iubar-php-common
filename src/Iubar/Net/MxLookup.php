<?php

namespace Iubar\Net;

class MxLookup {
	var $dns_socket = NULL;
	var $QNAME = "";
	var $dns_packet= NULL;
	var $ANCOUNT = 0;
	var $cIx = 0;
	var $dns_repl_domain;
	var $arrMX = array();

	function __construct($domain, $dns="8.8.8.8") { 
	
	// Google DNS 
// 		The Google Public DNS IP addresses (IPv4) are as follows:		
// 		8.8.8.8
// 		8.8.4.4
// 		The Google Public DNS IPv6 addresses are as follows:		
// 		2001:4860:4860::8888
// 		2001:4860:4860::8844
		
	// OpenDns.com Pv4 addresses
// 		208.67.222.222 (resolver1.opendns.com)
// 		208.67.220.220 (resolver2.opendns.com)
// 		208.67.222.220 [8]
// 		208.67.220.222 [8]
// 		IPv6 addresses[9]
// 		2620:0:ccc::2
// 		2620:0:ccd::2
		
		$this->QNAME($domain);
		$this->pack_dns_packet();
		$dns_socket = fsockopen("udp://$dns", 53);

		fwrite($dns_socket,$this->dns_packet,strlen($this->dns_packet));
		$this->dns_reply  = fread($dns_socket,1);
		$bytes = stream_get_meta_data($dns_socket);
		$this->dns_reply .= fread($dns_socket,$bytes['unread_bytes']);
		fclose($dns_socket);
		$this->cIx=6;
		$this->ANCOUNT   = $this->gord(2);
		$this->cIx+=4;
		$this->parse_data($this->dns_repl_domain);
		$this->cIx+=7;

		for($ic=1;$ic<=$this->ANCOUNT;$ic++)
		{
		$QTYPE = ord($this->gdi($this->cIx));
		if($QTYPE!==15){print("[MX Record not returned]"); die();}
           $this->cIx+=9;
		$mxPref = ord($this->gdi($this->cIx));
		$this->parse_data($curmx);
		$this->arrMX[] = array("MX_Pref" => $mxPref, "MX" => $curmx);
		$this->cIx+=3;
		}
		}

		function parse_data(&$retval)
		{
		$arName = array();
		$byte = ord($this->gdi($this->cIx));
		while($byte!==0)
			{
				if($byte==192) //compressed
				{
				$tmpIx = $this->cIx;
				$this->cIx = ord($this->gdi($cIx));
					$tmpName = $retval;
					$this->parse_data($tmpName);
						$retval=$retval.".".$tmpName;
						$this->cIx = $tmpIx+1;
						return;
					}
					$retval="";
				$bCount = $byte;
				for($b=0;$b<$bCount;$b++)
				{
				$retval .= $this->gdi($this->cIx);
					}
					$arName[]=$retval;
		$byte = ord($this->gdi($this->cIx));
		}
		$retval=join(".",$arName);
		}

		function gdi(&$cIx,$bytes=1)
			{
				$this->cIx++;
				return(substr($this->dns_reply, $this->cIx-1, $bytes));
		}

		function QNAME($domain)
		{
			$dot_pos = 0; $temp = "";
			while($dot_pos=strpos($domain,"."))
		{
		$temp   = substr($domain,0,$dot_pos);
		$domain = substr($domain,$dot_pos+1);
		$this->QNAME .= chr(strlen($temp)).$temp;
}
$this->QNAME .= chr(strlen($domain)).$domain.chr(0);
}

function gord($ln=1)
{
$reply="";
for($i=0;$i<$ln;$i++){
	$reply.=ord(substr($this->dns_reply,$this->cIx,1));
	$this->cIx++;
}

return $reply;
}

function pack_dns_packet()
{
$this->dns_packet = chr(0).chr(1).
chr(1).chr(0).
chr(0).chr(1).
chr(0).chr(0).
chr(0).chr(0).
chr(0).chr(0).
$this->QNAME.
chr(0).chr(15).
	chr(0).chr(1);
}

} // end class



// if (realpath(__FILE__) == realpath($_SERVER['DOCUMENT_ROOT'].$_SERVER['SCRIPT_NAME'])){
// 	/* Exampe of use: */
// 	$mx = new MxLookup("php.net");
// 	print $mx->ANCOUNT." MX Records\n";
// 	print "Records returned for ".$mx->dns_repl_domain.":\n<pre>";
// 	print_r($mx->arrMX);
// 	if(count($mx->arrMX)>0){
// 		echo "OK" . PHP_EOL;
// 	}else{
// 		echo "KO" . PHP_EOL;
// 	}
// }

?>