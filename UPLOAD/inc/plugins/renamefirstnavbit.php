<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}

function renamefirstnavbit_info()
{
    return array(
        "name"          => "Rename First Navbit",
        "description"   => "Give the first forum navbit a new name.",
        "website"       => "https://github.com/SvePu/MyBB-Rename-First-Navbit",
        "author"        => "SvePu",
        "authorsite"    => "https://github.com/SvePu",
        "version"       => "1.2",
        "codename"      => "renamefirstnavbit",
        "compatibility" => "18*"
    );
}

function renamefirstnavbit_activate()
{
	global $db;
	$query_add = $db->simple_select("settinggroups", "COUNT(*) as rows");
	$rows = $db->fetch_field($query_add, "rows");
	$renamefirstnavbit_group = array(
		"name" 			=>	"renamefirstnavbit",
		"title" 		=>	"Rename First Navbit Settings",
		"description" 	=>	"",
		"disporder"		=> 	$rows+1,
		"isdefault" 	=>  0
	);

	$gid = $db->insert_query("settinggroups", $renamefirstnavbit_group);

	$setting_array = array(
		'renamefirstnavbit_enable' => array(
			'title'			=> "Enable plugin?",
			'description'  	=> "If you want to activate plugin functions - choose YES",
			'optionscode'  	=> 'yesno',
			'value'        	=> '1',
			'disporder'		=> 1
		),
		'renamefirstnavbit_content' => array(
			"title"			=> "Name of first navbit",
			"description" 	=> "Here you can enter in the new name of your first forum navbit.",
			'optionscode'  	=> 'text',
			'value'        	=> 'Home',
			"disporder"		=> 2
		),
		'renamefirstnavbit_hide' => array(
			"title"			=> "Hide Navigation on Index Page",
			"description" 	=> "Choose YES to hide the navigation on index page.<br/>If you only want hide the navigation on index page without changing the default navbit name, leave the above text field blank.",
			'optionscode'  	=> 'yesno',
			'value'        	=> '0',
			"disporder"		=> 3
		)
	);

	foreach($setting_array as $name => $setting)
	{
		$setting['name'] = $name;
		$setting['gid'] = $gid;

		$db->insert_query('settings', $setting);
	}
	rebuild_settings();
}

function renamefirstnavbit_deactivate()
{
	global $db, $mybb;

	$result = $db->simple_select('settinggroups', 'gid', "name = 'renamefirstnavbit'", array('limit' => 1));
	$group = $db->fetch_array($result);

	if(!empty($group['gid']))
	{
		$db->delete_query('settinggroups', "gid='{$group['gid']}'");
		$db->delete_query('settings', "gid='{$group['gid']}'");
		rebuild_settings();
	}
}

function renamefirstnavbit_global_end()
{
	global $mybb, $navbits;
	if ($mybb->settings['renamefirstnavbit_enable'] == 1)
	{
		if(!empty($mybb->settings['renamefirstnavbit_content']))
		{
			$navbits = array();
			$navbits[0]['url'] = $mybb->settings['bburl'];
			$navbits[0]['name'] = $mybb->settings['renamefirstnavbit_content'];
		}

		if($mybb->settings['renamefirstnavbit_hide'] == 1 && strpos('index.php', THIS_SCRIPT) !== false)
		{
			$navbits = array();
			$navbits[0]['url'] = "";
			$navbits[0]['name'] = "";
		}
	}
}
$plugins->add_hook("global_end", "renamefirstnavbit_global_end");

function renamefirstnavbit_pre_parse_page(&$contents)
{
	global $mybb;
	if ($mybb->settings['renamefirstnavbit_enable'] == 1 && $mybb->settings['renamefirstnavbit_hide'] == 1 && strpos('index.php', THIS_SCRIPT) !== false)
	{
		$contents = str_replace('<navigation>', "", $contents);
	}
}
$plugins->add_hook("pre_parse_page", "renamefirstnavbit_pre_parse_page");
