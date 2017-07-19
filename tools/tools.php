<?php
/*
Modif F.mugnier:
- ajout choix version
- memo port version
- defaut snmp oid
ATTENTION: incompatibilitéE !
*/
/*
Modif Michael.M
- Compatible 1.0
- snmpwalk coche par defaut
- Ajout snmpv3 a tester
- Ajout lecture version et communaute (default device)
- Todo : return cli pour script
*/

$DEFAULT_OID = ".1.3.6.1.2.1.1";
chdir('../../');

include("./include/auth.php");
include_once('./include/global.php');
include_once("./lib/snmp.php");
include_once("./lib/utility.php");

$action = "";
if (isset_request_var('action')) {
	$action = get_request_var('action');
}

if (isset_request_var('host') && IsValidHost(get_request_var('host'))) {
	$host = get_request_var('host');
}else{
	$host = '127.0.0.1';
}

top_header();

show_tools ();

switch ($action) {
	case 'servicecheck':
		ShowServiceCheck();
		break;
	case 'snmpwalk':
		WalkHost();
		break;
	default:
		break;
}

bottom_footer();

function show_tools () {
	global $action, $host, $DEFAULT_OID;
	$community = read_config_option('snmp_community');
	$port = '161';
	$oid = $DEFAULT_OID;
	$version = read_config_option('snmp_ver');
	$authprot='sha';
	$authphrase='aes';
	$username='';
	$passphrase='';
	$password='';

	if ($action == "snmpwalk")
	{
	if (isset_request_var('community')) {
		$community = get_request_var('community'); }
	if (isset_request_var('port')) {
		$port = get_request_var('port'); }
	if (isset_request_var('oid')) {
		$oid = get_request_var('oid'); }
	if (isset_request_var('version')) {
		$version = get_request_var('version'); }
        if (isset_request_var('username')) {
            $username = get_request_var('username'); }
		if (isset_request_var('passphrase')) {
            $passphrase = get_request_var('passphrase'); }
        if (isset_request_var('password')) {
            $password = get_request_var('password'); }
        if (isset_request_var('authprot')) {
            $authprot = get_request_var('authprot'); }
        if (isset_request_var('authphrase')) {
            $authphrase = get_request_var('authphrase'); }
	}
	print "<center>";
	print "<br><br><br><table width=500 bgcolor=black cellpadding=1 cellspacing=0><tr><td>
	<table width='100%' bgcolor=white cellspacing=3><tr><td>";
	print "<form method=POST action=tools.php>";
	print "<table width='100%'><tr><td valign=center width=50>Host</td><td valign=center>\n";
	print "<input type=text size=20 value='$host' name=host id=host><br><br></td></tr>\n";
	print "<tr><td colspan=2><hr width='100%'></td></tr>";
//SNMP Walk
	print "<tr><td><input type=radio name=action value=snmpwalk checked=checked";
	if ($action == 'snmpwalk')
		print " checked";
	print "></td><td><h3>SNMP Walk</h3>";
	print "<table cellspacing=0 cellpadding=0 corder=0><tr><td>OID</td><td><input type=text size=30 value='$oid' name=oid id=oid></td></tr>";
	print "<tr><td>Community&nbsp;&nbsp;&nbsp;</td><td><input type=text size=30 value='$community' name=community id=community></td></tr>";
	print "<tr><td>Port&nbsp;&nbsp;&nbsp;</td><td><input type=text size=5 value='$port' name=port id=port>&nbsp;&nbsp;&nbsp;";
	print "<input type=radio name=version value=1 " . (($version==1)? "checked" : "") . ">V1";
	print "<input type=radio name=version value=2 " . (($version==2)? "checked" : "") . ">V2";
        print "<input type=radio name=version value=3 " . (($version==3)? "checked" : "") . ">V3</td></tr>";
	print "<tr><td>For version 3</td><tr>";
        print "<tr><td>Username&nbsp;&nbsp;&nbsp;</td><td><input type=text size=25 value='$username' name=username id=username></td></tr>";
	print "<tr><td>Password&nbsp;&nbsp;&nbsp;</td><td><input type=text size=25 value='$password' name=password id=password>&nbsp;&nbsp;&nbsp;";
        print "<input type=radio name=authprot value='sha' " . (($authprot=='sha')? "checked" : "") . ">SHA";
        print "<input type=radio name=authprot value='md5' " . (($authprot=='md5')? "checked" : "") . ">MD5</td></tr>";
        print "<tr><td>Passphrase&nbsp;&nbsp;&nbsp;</td><td><input type=text size=25 value='$passphrase' name=passphrase id=passphrase>&nbsp;&nbsp;&nbsp;";
        print "<input type=radio name=authphrase value='aes' " . (($authphrase=='aes')? "checked" : "") . ">AES";
        print "<input type=radio name=authphrase value='des' " . (($authphrase=='des')? "checked" : "") . ">DES</td></tr>";
	print "</tr></table>";
	print "</td></tr>";
	print "<tr><td colspan=2><hr width='100%'></td></tr>";

// Service Check
	print "<tr><td><input type=radio name=action value=servicecheck";
	if ($action == 'servicecheck')
		print " checked";
	print "></td><td><h3>Service Check</h3><input type=checkbox name=http checked>HTTP<input type=checkbox name=ftp checked>FTP<input type=checkbox name=pop3 checked>POP3<input type=checkbox name=smtp checked>SMTP</td></tr>";
	print "<tr><td colspan=2><hr width='100%'></td></tr>";
	print "<tr><td></td><td><input type=submit value=Submit></td></tr>";
	print "</table></form>";
	print "</td></tr></table></td></tr></table>";
	print "</center>";
}


function ShowServiceCheck () {


	if (isset_request_var('host') && IsValidHost(get_request_var('host'))) {
		$host = get_request_var('host');
	} else {
		$host = '127.0.0.1';
	}

	print "<br><br><br><center><table width=500 bgcolor=black cellpadding=1 cellspacing=0><tr><td><table width='100%' bgcolor=white><tr><td>";
	$results = array();
	$results[] = ServiceCheck($host, 'http');
	$results[] = ServiceCheck($host, 'ftp');
	$results[] = ServiceCheck($host, 'pop3');
	$results[] = ServiceCheck($host, 'smtp');

	print "<center><table><tr><td colspan=5><center><h3>Service Results for $host</h3></center></td></tr><tr><td></td><td><b><u>Service</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b><u>Port</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b><u>Result</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td><b><u>Time</u></b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
	foreach ($results as $r) {
		print "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>" . $r['service'] . "</td><td>" . $r['port'] . "</td><td>" . $r['result'] . "</td><td>" . $r['time'] . "</td></tr>";
	}
	print "</table></center>";
	print "</td></tr></table></td></tr></table>";

}

function IsValidHost($host) {
	if(preg_match("/^((([0-9]{1,3}\.){3}[0-9]{1,3})|([0-9a-z-.]{0,61})?\.[a-z]{2,4})$/i", $host))
		return 1;
	return 0;
}

function IsValidOID($oid) {
	if(preg_match("/^([0-9\.]{0,61})$/i", $oid)) 
		return 1;
	return 0;
}

function WalkHost () {
	global $config;
	$port = 161;
	$version = 2;

	if (isset_request_var('host') && IsValidHost(get_request_var('host'))) {
		$host = get_request_var('host');
	} else {
		$host = '127.0.0.1';
	}

	if (isset_request_var('port')) {
		$port = get_request_var('port'); }
		
	if (isset_request_var('version')) {
		$version = get_request_var('version'); }
		
    if (isset_request_var('username')) {
   		$username = get_request_var('username'); }

	if (isset_request_var('passphrase')) {
        $passphrase = get_request_var('passphrase'); }
			
    if (isset_request_var('password')) {
        $password = get_request_var('password'); }
			
    if (isset_request_var('authprot')) {
        $authprot = get_request_var('authprot'); }

	if (isset_request_var('authphrase')) {
        $authphrase = get_request_var('authphrase'); }
	
	if (isset_request_var('oid')) {
			$oid = get_request_var('oid');
	} else {
		$oid = ".1.3.6.1.2.1.1";
	}

	if (isset_request_var('community')) {
			$community = get_request_var('community');
	} else {
		$community = "public";
	}

	$host = strtolower($host);
// MODIF
	//	snmp_set_quick_print(0);
	$snmp_start = microtime (true);
	$a = '';
	$a = cacti_snmp_walk($host, $community, $oid, $version, $username, $password, $authprot, $passphrase ,$authphrase, "", $port, 1000);
	$response_time = round((microtime(true) - $snmp_start) * 1000);	// en millisec
// FIN MODIF
	print "<br><br><br><center><table bgcolor=black cellpadding=1 cellspacing=0><tr><td><table width='100%' bgcolor=white><tr><td>";

	print "<center><table border=1px><tr><td colspan=2 bgcolor=white><center><h3>SNMP Walk Results for $host</h3></center></td></tr>";
	foreach ($a as $val) {
		print "<tr><td>" . $val['oid'] . "</td><td>" . $val['value'] . "</td></tr>";
	}
	if (count($a) == 0)
		print "<tr><td>no snmp values returned</td></tr>";
	print "</table></center>";
	print "</td></tr></table></td></tr></table>";
// AJOUT	
	print "<p>Response time: $response_time ms</p>";
//	print "<p>Debug : $version $authprot $authphrase</p>";
}


function ServiceCheck($host, $service, $port='', $timeout = 10) {
	$host = strtolower($host);

	switch($service) {
		case 'http':
			$Request = "HEAD / HTTP/1.0\r\nUser-Agent: Service Check\r\nHost: $host\r\n\r\n";
			$OkResults = array("200\D+OK", "200\D+Document\s+Follows", "302", "301");
			if(!is_numeric($port)) $port = 80;
			break;

		case 'ftp':
			$OkResults = array("220");
			$Request = '';
			if(!is_numeric($port)) $port = 21;
			break;

		case 'smtp':
			$OkResults = array("220");
			$Request = '';
			if(!is_numeric($port)) $port = 25;
			break;

		case 'pop3':
			$OkResults = array("\\+OK");
			$Request = '';
			if(!is_numeric($port)) $port = 110;
			break;
	}

	list($MSec, $Sec) = explode(" ", microtime());
	$TimeBegin = (double) $MSec + (double) $Sec;
	$Socket = @fsockopen($host, $port, $error_number, $error, $timeout);

	list($MSec, $Sec) = explode(" ", microtime());
	$TimeEnd = (double) $MSec + (double) $Sec;
	$Time = number_format($TimeEnd - $TimeBegin, 3);
	// Check port

	if (is_resource($Socket)) {
		if ($Request != "") {
			fputs($Socket, $Request);
			stream_set_timeout($Socket, $timeout);
		}
		if (!feof($Socket)) {
			$Response = fgets($Socket, 4096);
			stream_set_timeout($Socket, $timeout);
		}
		$Result = "Failed";
		$Error  = $Response;

		foreach($OkResults as $exp_result) {
			if (preg_match("/$exp_result/",$Response)) {
				$Error = "";
				$Result = "Ok";
			}
		}
		fclose($Socket);
	 } else {
		$Result = "Failed";
		$Error = ((!$error) ? "Time out" : $error);
	 }

	 return array(
			'host'   => $host,
			'service'=> $service,
			'port'   => $port,
			'result' => $Result,
			'time'   => $Time,
			'error'  => $Error
		 );
}
?>
