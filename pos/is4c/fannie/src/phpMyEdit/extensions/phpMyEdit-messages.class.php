<?php

/*
 * phpMyEdit - instant MySQL table editor and code generator
 *
 * extensions/phpMyEdit-messages.class.php - phpMyEdit messages extension
 * ____________________________________________________________
 *
 * Developed by Ondrej Jombik <nepto@platon.sk>
 * Copyright (c) 2002-2006 Platon Group, http://platon.sk/
 * All rights reserved.
 *
 * See README file for more information about this software.
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://platon.sk/projects/phpMyEdit/
 */

/* $Platon: phpMyEdit/extensions/phpMyEdit-messages.class.php,v 1.15 2006-06-17 11:14:54 nepto Exp $ */

/* This extension is part of phpMyEzin: Content Management System project,
   where it handles discussion messages for particular articles. It depends on
   some phpMyEzin characteristics, thus extension should not and cannot be used
   outside this project. However there are planned some improvements for future
   to make this extension handle any kind of tree-structured data. */

require_once dirname(__FILE__).'/../phpMyEdit.class.php';

class phpMyEdit_messages extends phpMyEdit
{
	function phpMyEdit_messages($opts) /* {{{ */
	{
		$execute = 1;
		isset($opts['execute']) && $execute = $opts['execute'];
		$opts['execute'] = 0;
		parent::phpMyEdit($opts);
		$this->tb2         = $opts['tb2'];
		$this->format_date = $opts['format_date'];
		
		/* Preserved article ID in CGI environment. */
		/* TODO: change to $this->article_id or something like this */
		global $ezin_admin_article;
		$ezin_admin_article = $this->get_data_cgi_var('article_id');

		$execute && $this->execute();
	} /* }}} */

	function list_table() /* {{{ */
	{
		$ezin_admin_article_change_up = $this->get_sys_cgi_var('ezin_admin_article_change_up');
		$ezin_admin_article_change_down = $this->get_sys_cgi_var('ezin_admin_article_change_down');
		if (isset($ezin_admin_article_change_up)) {
			$ezin_admin_article = $this->get_sys_cgi_var('ezin_admin_article_up');
		} elseif (isset($ezin_admin_article_change_down)) {
			$ezin_admin_article = $this->get_sys_cgi_var('ezin_admin_article_down');
		}
		!isset($ezin_admin_article) && $ezin_admin_article = $this->get_data_cgi_var('article_id');
		$ezin_admin_article = intval($ezin_admin_article);
		$query = sprintf('SELECT article_id, count(id) AS messages FROM %s'
				.' GROUP BY article_id HAVING article_id = %d',
				$this->tb, intval($ezin_admin_article));
		if (($result = $this->myquery($query)) == false) {
			return false;
		}
		$row = @mysql_fetch_array($result, MYSQL_ASSOC);
		//$ezin_admin_article  = intval($row['article_id']);
		$ezin_admin_msgcount = intval($row['messages']);	
		@mysql_free_result($result);

		echo '<form class="',$this->getCSSclass('form');
		echo '" action="',$page_name,'" method="POST">',"\n";

		if ($this->nav_up() || $ezin_admin_article <= 0) {
			$this->message_nav_buttons($ezin_admin_article, $ezin_admin_msgcount, 'up');
			echo '<hr class="',$this->getCSSclass('hr', 'up'),'">',"\n";
		}

		if ($ezin_admin_article > 0) {
			echo '<table class="',$this->getCSSclass('main'),'" summary="',$this->tb,'">',"\n";
			echo '<tr class="',$this->getCSSclass('header'),'">',"\n";
			foreach (array('ID', 'Subject', ' ', 'Author', 'Date & Time', 'IP addresses') as $str) {
				echo '<th class="',$this->getCSSclass('header'),'">';
				echo Platon::htmlspecialchars2($str),'</th>',"\n";
			}
			echo '</tr>',"\n";
			echo '<tr class="',$this->getCSSclass('header'),'">',"\n";
			echo '<th class="',$this->getCSSclass('header'),'" colspan="6">';
			echo 'Valid messages</td></tr>',"\n";
			$message_ids = $this->message_process($ezin_admin_article, 0, 0);
			$count_message_ids = count($message_ids);
			if ($count_message_ids == 0) {
				echo '<tr class="',$this->getCSSclass('row', null, 'next'),'">',"\n";
				echo '<td class="',$this->getCSSclass('cell', null, true),'" colspan="6">',"\n";
				echo '<i>There are no valid messages for this article.</i>';
				echo '</td></tr>',"\n";
			}
			$query = sprintf('SELECT id, parent, article_id, author,'
					.' email, homepage, subject, datetime, ip'
					.' FROM %s WHERE article_id = %d ORDER BY datetime ASC',
					$this->tb, intval($ezin_admin_article));
			if (($result = $this->myquery($query)) == false) {
				return false;
			}
			$all_ids = array();
			$parents = array();
			for ($i = 0; ($row = @mysql_fetch_array($result, MYSQL_ASSOC)); $i++) {
				$all_ids[]           = $row['id'];
				$parents[$row['id']] = $row['parent'];
			}
			@mysql_free_result($result);
			$all_ids = array_diff($all_ids, $message_ids);
			echo '<tr class="',$this->getCSSclass('header'),'">',"\n";
			echo '<th class="',$this->getCSSclass('header'),'" colspan="6">';
			echo 'Invalid messages</td></tr>',"\n";
			if (count($all_ids) > 0) {
				/* To force buttons */
				$count_message_ids = -1;
				while (count($all_ids) > 0) {
					//echo "<p>all_ids: "; var_dump($all_ids);echo '<br>';
					$sub_ids = $this->message_process($ezin_admin_article,
							$parents[array_shift($all_ids)], 0, true);
					$all_ids = array_diff($all_ids, $sub_ids);
				}
			} else {
				echo '<tr class="',$this->getCSSclass('row', null, 'next'),'">',"\n";
				echo '<td class="',$this->getCSSclass('cell', null, true),'" colspan="6">',"\n";
				echo '<i>There are no invalid messages for this article.</i>';
				echo '</td></tr>',"\n";
			}
			echo '</table>';
		}
		if ($this->nav_down() && $ezin_admin_article > 0) {
			echo '<hr class="',$this->getCSSclass('hr', 'down'),'">',"\n";
			$this->message_nav_buttons($ezin_admin_article, $ezin_admin_msgcount, 'down');
		}
		echo $this->htmlHiddenData('article_id', $ezin_admin_article);
		echo '</form>',"\n";
	} /* }}} */

	function message_process($article_id, $id, $level = 0, $parent = true) /* {{{ */
	{
		$id    = intval($id);
		$level = intval($level);
		$query = sprintf('SELECT id, parent, article_id, author,'
				.' email, homepage, subject, datetime, ip'
				.' FROM %s WHERE %s = %d AND article_id = %d'
				.' ORDER BY datetime ASC', $this->tb,
				$parent == true ? 'parent' : 'id', intval($id), intval($article_id));
		if (($result = $this->myquery($query)) == false) {
			return false;
		}

		$ar     = array();
		$ar_out = array();
		for ($i = 0; ($row = @mysql_fetch_array($result, MYSQL_ASSOC)); $i++) {
			$ar[$i]   = $row;	
			$ar_out[] = $row['id'];
		}
		$checked = ! $level && $parent ? ' checked' : '';
		for ($i = 0; $i < count($ar); $i++) {
			echo '<tr class="',$this->getCSSclass('row', null, 'next'),'">',"\n";
			$css_class_name  = $this->getCSSclass('cell', null, true);
			$css_class_name2 = $this->getCSSclass('navigation', null, true);
			echo '<td class="',$css_class_name,'">',$ar[$i]['id'],'</td>',"\n";
			echo '<td class="',$css_class_name,'">';
			for ($j = 0; $j < $level; $j++) {
				echo '&nbsp;&nbsp;&nbsp;';
			}
			echo htmlspecialchars($ar[$i]['subject']);
			echo '</td>',"\n";
			echo '<td class="',$css_class_name2,'">';
			echo '<input',$checked,' class="',$css_class_name2,'"';
			echo ' type="radio" ','name="',$this->cgi['prefix']['sys'],'rec"';
			echo ' value="',$ar[$i]['id'],'" class="link"></td>',"\n";
			echo '<td class="',$css_class_name,'">',htmlspecialchars($ar[$i]['author']),  '</td>';
			echo '<td class="',$css_class_name,'">',htmlspecialchars($ar[$i]['datetime']),'</td>';
			// TODO: do resolving
			echo '<td class="',$css_class_name,'"><small>';
			// this shoud be global IP-adress-deliminator
			$output = false;
			$ar_ip  = preg_split('|([ ]*[ \\/,;]+[ ]*)|', $ar[$i]['ip'], -1, PREG_SPLIT_DELIM_CAPTURE);
			foreach ($ar_ip as $ip) {
				if (strlen($output) > 0) {
					$output = true;
				}
				$ip = htmlspecialchars($ip);
				if (preg_match('/^(\d{1,3}\.){3}\d{1,3}$/', $ip)) {
					echo '<a class="',$css_class_name,'" target="_blank" href="http://',$ip,'">';
					echo '<small>',$ip,'</small></a>';
				} else {
					echo $ip;
				}
			}
			if (! $output) {
				echo '&nbsp;';
			}
			echo '</small></td>',"\n";
			echo '</tr>',"\n";
			if ($parent) {
				$ar_out = array_merge($ar_out, $this->message_process(
							$article_id, $ar[$i]['id'], $level + 1));
			}
			strlen($checked) && $checked = '';
		}
		return $ar_out;
	} /* }}} */

	function message_nav_buttons($article_id, $messages_count, $position) /* {{{ */
	{
		echo '<table class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<tr class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<td class="',$this->getCSSclass('buttons', $position),'">',"\n";
		$this->print_article_select($article_id, 0, $position);
		echo '</td>',"\n";
		echo '<td class="',$this->getCSSclass('buttons2', $position),'">',"\n";
		if ($article_id > 0) {
			if ($this->add_enabled()) {
				echo $this->htmlSubmit('operation', 'Add', $this->getCSSclass('add', $position), false, false);
			}
			if ($this->view_enabled()) {
				echo '&nbsp;';
				echo $this->htmlSubmit('operation', 'View', $this->getCSSclass('view', $position),
						false, $messages_count <= 0);
			}
			if ($this->change_enabled()) {
				echo '&nbsp;';
				echo $this->htmlSubmit('operation', 'Change', $this->getCSSclass('change', $position),
						false, $messages_count <= 0);
			}
			if ($this->delete_enabled()) {
				echo '&nbsp;';
				echo $this->htmlSubmit('operation', 'Delete', $this->getCSSclass('delete', $position),
						false, $messages_count <= 0);
			}
		}
		echo '</td></tr></table>',"\n";
	} /* }}} */

	function display_record_buttons() /* {{{ */
	{
		echo '<table class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<tr class="',$this->getCSSclass('navigation', $position),'">',"\n";
		echo '<td class="',$this->getCSSclass('buttons', $position),'">',"\n";
		$this->print_article_select($article_id, 1, $position);
		echo '</td>',"\n";
		if (strlen(@$this->message) > 0) {
			echo '<td class="',$this->getCSSclass('message', $position),'">',$this->message,'</td>',"\n";
		}
		echo '<td class="',$this->getCSSclass('buttons2', $position),'">',"\n";
		if ($this->change_operation()) {
			echo $this->htmlSubmit('savechange', 'Save', $this->getCSSclass('save', $position), true), '&nbsp;';
			echo $this->htmlSubmit('morechange', 'Apply', $this->getCSSclass('more', $position), true), '&nbsp;';
			echo $this->htmlSubmit('cancelchange', 'Cancel', $this->getCSSclass('cancel', $position), false);
		} elseif ($this->add_operation()) {
			echo $this->htmlSubmit('saveadd', 'Save', $this->getCSSclass('save', $position), true), '&nbsp;';
			echo $this->htmlSubmit('moreadd', 'More', $this->getCSSclass('more', $position), true), '&nbsp;';
			echo $this->htmlSubmit('canceladd', 'Cancel', $this->getCSSclass('cancel', $position), false);
		} elseif ($this->delete_operation()) {
			echo $this->htmlSubmit('savedelete', 'Delete', $this->getCSSclass('save', $position), false), '&nbsp;';
			echo $this->htmlSubmit('canceldelete', 'Cancel', $this->getCSSclass('cancel', $position), false);
		} elseif ($this->view_operation()) {
			if ($this->change_enabled()) {
				echo $this->htmlSubmit('operation', 'Change', $this->getCSSclass('save', $position), false), '&nbsp;';
			}
			echo $this->htmlSubmit('cancelview', 'Cancel', $this->getCSSclass('cancel', $position), false);
		}
		// Message is now written here
		echo '</td>',"\n";
		echo '</tr></table>',"\n";
	} /* }}} */

	function print_article_select($selected_id, $disabled, $position) /* {{{ */
	{
		if ($selected_id <= 0) {
			$rec = intval($this->get_sys_cgi_var('rec'));
			if ($rec > 0) {
				$query  = sprintf('SELECT article_id FROM %s WHERE id = %d',
						$this->tb, $rec);
				$result = $this->myquery($query);
				if ($result != false) {
					$row = @mysql_fetch_array($result, MYSQL_NUM);
					$selected_id = $row[0];
				}
				@mysql_free_result($result);
			}
		}
		static $articles = null;
		if ($articles == null) {
			$articles = array();
			$query = 'SELECT id, title, atitle, UNIX_TIMESTAMP(datetime) AS date'
				.' FROM '.$this->tb2
				.' ORDER BY date DESC';
			if (($result = $this->myquery($query)) == false) {
				return false;
			}
			for ($k = 0; ($row = @mysql_fetch_array($result, MYSQL_ASSOC)); $k++) {
				$articles[] = $row;
			}
			@mysql_free_result($result);
		}
		echo '<select',($disabled ? ' disabled' : ''),' name="';
		echo $this->cgi['prefix']['sys'].'ezin_admin_article_',$position,'" size="1">',"\n";
		echo '<option value="0">-- Choose article --</option>',"\n";
		foreach ($articles as $row) {
			$row['title'] = empty($row['title']) ? $row['atitle'] : $row['title'];
			$row['title'] = Platon::pretty_substr(strip_tags($row['title']), 40);
			echo '<option'.($selected_id == $row['id'] ? ' selected' : '');
			echo ' value="',$row['id'],'">',$row['title'];
			if ($row['date'] > 0) {
				printf(' [%d] (%s)', $row['id'], date($this->format_date, $row['date']));
			}
			echo '</option>',"\n";	
		}
		echo '</select>',"\n";
		if (! $disabled) {
			echo $this->htmlSubmit('ezin_admin_article_change_'.$position, ' &gt; ', $this->get_sys_cgi_var('change', $position)), '&nbsp;', "\n";
		}
		return true;
	} /* }}} */

}

/* Modeline for ViM {{{
 * vim:set ts=4:
 * vim600:fdm=marker fdl=0 fdc=0:
 * }}} */

?>
