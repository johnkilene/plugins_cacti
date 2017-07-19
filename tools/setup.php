<?php
/*******************************************************************************

    Author ......... Jimmy Conner
    Contact ........ jimmy@sqmail.org
    Home Site ...... http://cactiusers.org
    Program ........ Network Tools Utility
    Version ........ 0.3
    Purpose ........ Useful Network Tools for Cacti

    Modif FM for add snmp v2
    Modif MM for cacti version 1.0 and add snmp V3
*******************************************************************************/

function plugin_tools_install() {
    api_plugin_register_hook('tools', 'config_arrays', 'tools_config_arrays', 'setup.php');
    api_plugin_register_hook('tools', 'draw_navigation_text', 'tools_draw_navigation_text', 'setup.php');

}

function plugin_tools_uninstall() {

}

function plugin_tools_version() {
    global $config;
    $info = parse_ini_file($config['base_path'] . '/plugins/tools/INFO', true);
    return $info['info'];
}

function plugin_tools_check_config() {
    return true;
}

function plugin_tools_upgrade() {
    return false;
}



function plugin_init_tools() {
	global $plugin_hooks;
	$plugin_hooks['config_arrays']['tools'] = 'tools_config_arrays';
	$plugin_hooks['draw_navigation_text']['tools'] = 'tools_draw_navigation_text';
}

function tools_version () {
	return array( 'name' 	=> 'tools',
			'version' 	=> '0.3',
			'longname'	=> 'Network Tools',
			'author'	=> 'Jimmy Conner',
			'homepage'	=> 'http://cactiusers.org',
			'email'		=> 'jimmy@sqmail.org',
			'url'		=> 'http://cactiusers.org/cacti/versions.php'
			);
}

function tools_config_arrays () {
	global $user_auth_realms, $user_auth_realm_filenames, $menu;

	$user_auth_realm_filenames['tools.php'] = 8;
	$menu["Utilities"]['plugins/tools/tools.php'] = "Network Tools";

}
function tools_draw_navigation_text ($nav) {
	$nav["tools.php:"] = array("title" => "Network Tools", "mapping" => "index.php:", "url" => "tools.php", "level" => "1");
	$nav["tools.php:servicecheck"] = array("title" => "Network Tools", "mapping" => "index.php:", "url" => "tools.php", "level" => "1");
	$nav["tools.php:snmpwalk"] = array("title" => "Network Tools", "mapping" => "index.php:", "url" => "tools.php", "level" => "1");
	return $nav;
}

?>
