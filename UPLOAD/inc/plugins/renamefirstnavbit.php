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

function renamefirstnavbit_run()
{
	global $mybb, $navbits;
	if ($mybb->settings['renamefirstnavbit_enable'] == 1 && !empty($mybb->settings['renamefirstnavbit_content']))
	{
		$navbits = array();
		$navbits[0]['url'] = $mybb->settings['bburl'];
		$navbits[0]['name'] = $mybb->settings['renamefirstnavbit_content'];
	}
}
$plugins->add_hook("global_end", "renamefirstnavbit_run");
