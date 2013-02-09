<?php
//PermBox by Polarbear541
//Released under the LGPL Licence (http://www.gnu.org/licenses/lgpl.html)
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("showthread_start", "permbox_showthread");
$plugins->add_hook("forumdisplay_end", "permbox_showforum");

function permbox_info()
{
	global $lang;
	$lang->load('permbox');
	return array(
		"name"			=> $lang->permbox,
		"description"	=> $lang->permbox_desc,
		"author"		=> "Polarbear541",
		"version"		=> "1.0",
		"compatibility" => "16*",
		"guid" 			=> "08d3f1bcb9aade7fbdb98ffc23fa2011"
	);
}

function permbox_install()
{
	global $db, $lang;
	$lang->load('permbox');
	//Insert setting group
	$permbox_group = array(
		'name'  => 'permbox',
		'title'      => $lang->permbox_sgroup,
		'description'    => $lang->permbox_sgroupdesc,
		'disporder'    => "1",
		'isdefault'  => "0",
	);
	
	$db->insert_query('settinggroups', $permbox_group);
	$gid = $db->insert_id(); 
	
	//Insert setting then rebuild	
	$setting_one = array(
		'name'			=> 'permbox_forum_onoff',
		'title'			=> $lang->permbox_sforumbox,
		'description'	=> $lang->permbox_sforumboxdesc,
		'optionscode'	=> 'yesno',
		'value'			=> '1',
		'disporder'		=> 1,
		'gid'			=> $gid,
	);
	$db->insert_query('settings', $setting_one);
	
	//Insert setting then rebuild	
	$setting_two = array(
		'name'			=> 'permbox_thread_onoff',
		'title'			=> $lang->permbox_sthreadbox,
		'description'	=> $lang->permbox_sthreadboxdesc,
		'optionscode'	=> 'yesno',
		'value'			=> '1',
		'disporder'		=> 2,
		'gid'			=> $gid,
	);
	$db->insert_query('settings', $setting_two);
	
	rebuild_settings();
	
	//Insert Template Group
	$templategone = array (
		'prefix'	=> 'permbox',
		'title'		=> 'Permission Box'
	);
	$db->insert_query('templategroups', $templategone);
	$gid = $db->insert_id(); 
	//Insert Main Templates
	$templateone = array(
		'title'		=> 'permbox_thread',
		'template' => $db->escape_string('<div style="width:275px;" id="permbox">
		<table cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" border="0" class="tborder"> 
		<tr> <td class="thead" align="center" colspan="2"><strong>{$lang->permbox_title_thread}</strong></td> </tr> 
		<tr> <td class="trow1"> You <b>{$canpostreply}</b> {$lang->permbox_creply} {$lang->permbox_inthread} </td> </tr> 
		<tr> <td class="trow2"> You <b>{$canedit}</b> {$lang->permbox_cedit} {$lang->permbox_inthread} </td> </tr>
		<tr> <td class="trow1"> You <b>{$candelete}</b> {$lang->permbox_cdelete} {$lang->permbox_inthread} </td> </tr>
		<tr> <td class="trow2"> You <b>{$canvote}</b> {$lang->permbox_cvote} {$lang->permbox_inthread} </td> </tr>
		<tr> <td class="trow2"> You <b>{$canhtml}</b> {$lang->permbox_chtml} {$lang->permbox_inthread} </td> </tr>
		<tr> <td class="trow2"> You <b>{$canmycode}</b> {$lang->permbox_cmycode} {$lang->permbox_inthread} </td> </tr>
		<tr> <td class="trow2"> You <b>{$cansmilies}</b> {$lang->permbox_csmilies} {$lang->permbox_inthread} </td> </tr>
		<tr> <td class="trow2"> You <b>{$canimg}</b> {$lang->permbox_cimgcode} {$lang->permbox_inthread} </td> </tr>
		<tr> <td class="trow2"> You <b>{$canvideo}</b> {$lang->permbox_cvideocode} {$lang->permbox_inthread} </td> </tr>
		</table> </div>'),
		'sid'		=> -2,
		'version'	=> '160',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query('templates', $templateone);
	$templatetwo = array(
		'title'		=> 'permbox_forum',
		'template' => $db->escape_string('<div style="width:275px;" id="permbox">
		<table cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" border="0" class="tborder"> 
		<tr> <td class="thead" align="center" colspan="2"><strong>{$lang->permbox_title_forum}</strong></td> </tr> 
		<tr> <td class="trow1"> You <b>{$canpostthread}</b> {$lang->permbox_cthread} {$lang->permbox_inforum} </td> </tr>
		<tr> <td class="trow2"> You <b>{$canpostreply}</b> {$lang->permbox_creply} {$lang->permbox_inforum} </td> </tr> 
		<tr> <td class="trow1"> You <b>{$canedit}</b> {$lang->permbox_cedit} {$lang->permbox_inforum} </td> </tr>
		<tr> <td class="trow2"> You <b>{$candelete}</b> {$lang->permbox_cdelete} {$lang->permbox_inforum} </td> </tr>
		<tr> <td class="trow1"> You <b>{$canvote}</b> {$lang->permbox_cvote} {$lang->permbox_inforum} </td> </tr>
		</table> </div>'),
		'sid'		=> -2,
		'version'	=> '160',
		'dateline'	=> TIME_NOW
	);
	$db->insert_query('templates', $templatetwo);
}

function permbox_uninstall()
{
	global $db;
	//Remove and rebuild settings
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('permbox_forum_onoff')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN ('permbox_thread_onoff')");
	$db->query("DELETE FROM ".TABLE_PREFIX."settinggroups WHERE name='permbox'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templategroups WHERE prefix='permbox'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='permbox_thread'");
	$db->query("DELETE FROM ".TABLE_PREFIX."templates WHERE title='permbox_forum'");
	rebuild_settings(); 
}

function permbox_is_installed()
{
	global $mybb;
	if(isset($mybb->settings['permbox_forum_onoff']) && isset($mybb->settings['permbox_thread_onoff']))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function permbox_activate() //When plugin installed
{
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets("showthread",'#'.preg_quote('{$usersbrowsing}').'#','{$permbox}{$usersbrowsing}');
	find_replace_templatesets("forumdisplay",'#'.preg_quote('{$threadslist}').'#','{$threadslist}{$permbox}');
}

function permbox_deactivate() //When plugin uninstalled
{
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';
	find_replace_templatesets("showthread",'#'.preg_quote('{$permbox}{$usersbrowsing}').'#','{$usersbrowsing}');
	find_replace_templatesets("forumdisplay",'#'.preg_quote('{$threadslist}{$permbox}').'#','{$threadslist}');
}

function permbox_showthread() //Show permission box in thread
{
	global $mybb, $templates, $permbox, $lang, $thread, $forum;
	if($mybb->settings['permbox_thread_onoff'] == 1)
	{
		$lang->load('permbox');
		$perms = forum_permissions($thread['fid']);
		
		//Reply
		if($perms['canpostreplys'] == 1)
		{
			$canpostreply = $lang->permbox_can;
		}
		else
		{
			$canpostreply = $lang->permbox_cannot;
		}
		
		//Edit
		if($perms['caneditposts'] == 1)
		{
			$canedit = $lang->permbox_can;
		}
		else
		{
			$canedit = $lang->permbox_cannot;
		}
		
		//Delete
		if($perms['candeleteposts'] == 1)
		{
			$candelete = $lang->permbox_can;
		}
		else
		{
			$candelete = $lang->permbox_cannot;
		}
		
		//Vote
		if($perms['canvotepolls'] == 1)
		{
			$canvote = $lang->permbox_can;
		}
		else
		{
			$canvote = $lang->permbox_cannot;
		}

		//HTML
		if($forum['allowhtml'] == 1)
		{
			$canhtml = $lang->permbox_can;
		}
		else
		{
			$canhtml = $lang->permbox_cannot;
		}

		//MyCode
		if($forum['allowmycode'] == 1)
		{
			$canmycode = $lang->permbox_can;
		}
		else
		{
			$canmycode = $lang->permbox_cannot;
		}

		//Smilies
		if($forum['allowsmilies'] == 1)
		{
			$cansmilies = $lang->permbox_can;
		}
		else
		{
			$cansmilies = $lang->permbox_cannot;
		}

		//IMG MyCode
		if($forum['allowimgcode'] == 1)
		{
			$canimg = $lang->permbox_can;
		}
		else
		{
			$canimg = $lang->permbox_cannot;
		}

		//Video MyCode
		if($forum['allowvideocode'] == 1)
		{
			$canvideo = $lang->permbox_can;
		}
		else
		{
			$canvideo = $lang->permbox_cannot;
		}
		
		eval("\$permbox = \"".$templates->get('permbox_thread')."\";");
	}
}

function permbox_showforum() //Show permission box in thread
{
	global $mybb, $templates, $permbox, $lang, $fid;
	if($mybb->settings['permbox_forum_onoff'] == 1)
	{
		$lang->load('permbox');
		$perms = forum_permissions($fid);
		//Thread
		if($perms['canpostthreads'] == 1)
		{
			$canpostthread = $lang->permbox_can;
		}
		else
		{
			$canpostthread = $lang->permbox_cannot;
		}
		
		//Reply
		if($perms['canpostreplys'] == 1)
		{
			$canpostreply = $lang->permbox_can;
		}
		else
		{
			$canpostreply = $lang->permbox_cannot;
		}
		
		//Edit
		if($perms['caneditposts'] == 1)
		{
			$canedit = $lang->permbox_can;
		}
		else
		{
			$canedit = $lang->permbox_cannot;
		}
		
		//Delete
		if($perms['candeleteposts'] == 1)
		{
			$candelete = $lang->permbox_can;
		}
		else
		{
			$candelete = $lang->permbox_cannot;
		}
		
		//Vote
		if($perms['canvotepolls'] == 1)
		{
			$canvote = $lang->permbox_can;
		}
		else
		{
			$canvote = $lang->permbox_cannot;
		}
		
		eval("\$permbox = \"".$templates->get('permbox_forum')."\";");
	}
}
?>