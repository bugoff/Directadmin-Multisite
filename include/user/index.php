<?php
require('/usr/local/directadmin/plugins/MultiSite/include/httpsocket.php');
require('/usr/local/directadmin/plugins/MultiSite/include/functions.php');

if (isset($_SERVER['QUERY_STRING']) { parse_str($_SERVER['QUERY_STRING'],$_GET); }
if (isset($_SERVER['POST']) { parse_str($_SERVER['POST'],$_POST); }

$params=NULL;
$params=$_GET+$_POST;
$error=NULL;

//$domain is the domain we want to link to our drupal
$domain=isset($params['domain']) ? $params['domain'] : NULL;
//$drupalsite is the domain where we want to symlink to
$drupalsite=isset($params['drupalsite']) ? $params['drupalsite'] : NULL;
//$action can be create or delete
$action=isset($params['action']) ? $params['action'] : NULL;

// Debug options

// echo $domain;
// echo is_null($domain);

// echo is_null($drupalsite);

// echo is_null($action);

$basedir="/home/{$_SERVER['USER']}";

// echo "$basedir<br>";

$drupaldir= "{$basedir}/domains/{$drupalsite}/public_html";

// echo "$drupaldir<br>";

$domaindir= "{$basedir}/domains/{$domain}/public_html";

// echo "$domaindir<br>";

$domaindir_old="{$domaindir}_old";

// echo "$domaindir_old<br>";

//echo is_null($drupalsite);

//echo "Na 1ste if, checkdir domaindir";
//echo checkdir($domaindir);
//echo is_null($error);
//echo is_null($action);


// Keuzepagina tonen
// domain= is niet gezet (ze hebben dus niet via directadmin op de MultiSite link geklikt)
if(is_null($domain ) && is_null($error) && is_null($action)) {
	echo "<B>Select your domain</B><br />";
	foreach(get_domains() as $key => $value){
			echo "<A HREF='?domain=${value}'>${value}</A><BR>";
	}	
} elseif(is_null($drupalsite) && checkdir($domaindir)===DIR && is_null($error) && is_null($action)) {
// Alles is ingevuld, we geven hun een lijst van beschikbare sites om hun drupal aan te linken.
		echo "<B>Select your drupal domain</B><br />";
		foreach(get_domains() as $key => $value){
			if($value != $domain) {
				echo "<A HREF='?domain=${domain}&drupalsite=${value}&action=create'>${value}</A><BR>";
			}	
		}
} elseif(checkdir($domaindir)==='LINK'  && is_null($error)) {
// Het domein die ze opgegeven hebben heeft al een symlink.
		echo "Link already exists, do you want to delete your link?<br />";
		echo "<A HREF='?domain=${domain}&action=delete'>Delete</A><BR />";
}
// Ze hebben alles ingevuld en nu gaan we nog een laatste keer alle input controleren voor we iets gaan doen.

if( $action === 'create' && check_domain($drupalsite) && check_domain($domain) && is_null($error)) {
		
	  if(checkdir($drupaldir) === DIR && checkdir($domaindir) === DIR){				
			if(rename($domaindir,$domaindir_old)) { 
				
				echo "Renamed old dir to ${domaindir_old}<br />";
				
				if(symlink($drupaldir, $domaindir)){
					 echo 'Creating symlink: OK<br />';					
				}
		}
	} elseif(checkdir($drupaldir)===false) {
		echo "Your drupal dir doesn't exist, please create your site first.";
		$error=TRUE;
	} elseif(checkdir($domaindir)===false) {
		echo "There is something wrong with the domain you are trying to link, check if your public_html dir exists.";
		$error=TRUE;
	} elseif(checkdir($drupaldir)===LINK) {
		echo "Your drupal dir is already a symlink.";
		$error=TRUE;
	} elseif(checkdir($domaindir)===LINK) {
		echo "Link already exists, do you want to delete your link?<br />";
		echo "<A HREF='?domain=${domain}&action=delete'>Delete</A><BR />";
		$error=TRUE;
	}
} elseif(!is_null($drupalsite) && !check_domain($drupalsite) && is_null($error)) {
	echo "You don't own ${drupalsite}, you can't link to sites you don't own.";
	$error=TRUE;
} elseif(!is_null($domain) && !check_domain($domain) && is_null($error))  {
	echo "You don't own ${domain}, you can't link domains you don't own";
	$error=TRUE;
} elseif(!is_null($action) && !isset($action) && is_null($error))  {
	echo "${domain} ook niet he";
	$error=TRUE;
}

if( $action === 'delete' && check_domain($domain) && checkdir($domaindir)===LINK && is_null($error)) {
	if(unlink($domaindir)) {
		echo "Unlink ${domaindir}: OK<br />";
		if(rename($domaindir_old,$domaindir)) {
		 echo "rename ${domaindir}: OK<br />";
		}
	} else {
		echo "unlink niet gelukt";
		$error=TRUE;
	}
} elseif ( $action === 'delete' && checkdir($domaindir) !== LINK && is_null($error)) { 
	echo "Domain is no symlink, cannot delete<br />";
	$error=TRUE;
} elseif ( $action === 'delete' && !check_domain($domain) && is_null($error)) {
	echo "You don't own this domain";
	$error=TRUE;
} 
echo "<A HREF='/CMD_PLUGINS/MultiSite/'>Go back</A><BR />";
?>
