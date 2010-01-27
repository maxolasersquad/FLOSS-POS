<?php

/*
 * phpMyEdit - instant MySQL table editor and code generator
 *
 * extensions/phpMyEdit-htmlarea.class.php - phpMyEdit htmlArea extension
 * ____________________________________________________________
 *
 * Contribution of Ezudin Kurtowich <ekurtovic@ieee.org>, Sarajevo
 * Copyright (c) 2003-2006 Platon Group, http://platon.sk/
 * All rights reserved.
 *
 * See README file for more information about this software.
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://platon.sk/projects/phpMyEdit/
 */

/* $Platon: phpMyEdit/extensions/phpMyEdit-htmlarea.class.php,v 1.10 2006-01-22 21:44:18 nepto Exp $ */

/*
    OVERVIEW
    --------

	NOTE...This extension will not work with the CVS version of PME. It has
	been replaced by the mce_cal extension.
	
    htmlArea is a free WYSIWYG textarea replacement from
    http://www.interactivetools.com/ website.

    REQUIREMENTS
    ------------
   
    The extension requires a properly installed htmlArea script
    as described on the http://www.interactivetools.com/ site.

    USAGE
    -----

    This extension enables WYSIWYG editing of a textarea field.
    In order to use it, you should:
    
    1. Load htmlArea script in the <head>...</head> section of your
       phpMyEdit calling program as described in the htmlarea manual.
    
       NOTE: To avoid an unwanted side effect in css style produced
       by phpMyEditSetup.php, delete 'table-width:100%' property.

    2. Call to phpMyEdit-htmlarea.class.php instead
       of phpMyEdit.class.php.

       Example:

       require_once 'extensions/phpMyEdit-htmlarea.class.php';
       new phpMyEdit_htmlarea($opts);

    3. Add 'html'=>true parameter to the textarea field definition
       in your phpMyEdit calling program.

       Example:
    
       $opts['fdd']['col_name'] = array(
         'name'     => 'Column',
         'select'   => 'T',
         'options'  => 'ACPVD',
         'required' => true,
         'textarea' => array(
           'html' => true,
           'rows' => 11,
           'cols' => 81)
       );

    SEARCH KEYWORD
    --------------

	Search for "htmlArea" string in this source code,
	to find all extension related modifications.
*/

require_once dirname(__FILE__).'/../phpMyEdit.class.php';

class phpMyEdit_htmlarea extends phpMyEdit
{

	/*
	 * Display functions overriding
	 */

	function display_add_record() /* {{{ */
	{
		for ($k = 0; $k < $this->num_fds; $k++) {
			if ($this->hidden($k)) {
				echo $this->htmlHidden($this->fds[$k], $row["qf$k"]);
				continue;
			}
			if (! $this->displayed[$k]) {
				continue;
			}
			$css_postfix    = @$this->fdd[$k]['css']['postfix'];
			$css_class_name = $this->getCSSclass('input', null, 'next', $css_postfix);
			echo '<tr class="',$this->getCSSclass('row', null, true, $css_postfix),'">',"\n";
			echo '<td class="',$this->getCSSclass('key', null, true, $css_postfix),'">',$this->fdd[$k]['name'],'</td>',"\n";
			echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'">'."\n";
			if ($this->col_has_values($k)) {
				$vals     = $this->set_values($k);
				$selected = @$this->fdd[$k]['default'];
				$multiple = $this->fdd[$k]['select'] == 'M' && ! $this->fdd[$k]['values']['table'];
				$readonly = $this->readonly($k);
				echo $this->htmlSelect($this->fds[$k], $css_class_name, $vals, $selected, $multiple,$readonly);
			} elseif (isset ($this->fdd[$k]['textarea'])) {
				echo '<textarea class="',$css_class_name,'" name="'.$this->fds[$k].'"';
				echo ($this->readonly($k) ? ' disabled' : '');
				if (intval($this->fdd[$k]['textarea']['rows']) > 0) {
					echo ' rows="',$this->fdd[$k]['textarea']['rows'],'"';
				}
				if (intval($this->fdd[$k]['textarea']['cols']) > 0) {
					echo ' cols="',$this->fdd[$k]['textarea']['cols'],'"';
				}
				if (isset($this->fdd[$k]['textarea']['wrap'])) {
					echo ' wrap="',$this->fdd[$k]['textarea']['wrap'],'"';
				} else {
					echo ' wrap="virtual"';
				}
				echo '>',htmlspecialchars($this->fdd[$k]['default']),'</textarea>',"\n";

                // EK htmlArea code modification is here
                if (isset($this->fdd[$k]['textarea']['html'])) {
                    echo '<script type="text/javascript" language="javascript1.2"><!--',"\n";
					echo 'editor_generate("',$this->fds[$k],'");',"\n";
					echo '// --></script>';
  				}
			} else {
				// Simple edit box required
				$size_ml_props = '';
				$maxlen = intval($this->fdd[$k]['maxlen']);
				//$maxlen > 0 || $maxlen = 300;
				$size   = min($maxlen, 60);
				$size   && $size_ml_props .= ' size="'.$size.'"';
				$maxlen && $size_ml_props .= ' maxlength="'.$maxlen.'"';
				echo '<input class="',$css_class_name,'" type="text" ';
				echo ($this->readonly($k) ? 'disabled ' : ''),' name="',$this->fds[$k],'"';
				echo $size_ml_props,' value="';
				echo htmlspecialchars($this->fdd[$k]['default']),'">';
			}
			echo '</td>',"\n";
			if ($this->guidance) {
				$css_class_name = $this->getCSSclass('help', null, true, $css_postfix);
				$cell_value     = $this->fdd[$k]['help'] ? $this->fdd[$k]['help'] : '&nbsp;';
				echo '<td class="',$css_class_name,'">',$cell_value,'</td>',"\n";
			}
			echo '</tr>',"\n";
		}
	} /* }}} */

	function display_change_field($row, $k) /* {{{ */
	{
		$css_postfix    = @$this->fdd[$k]['css']['postfix'];
		$css_class_name = $this->getCSSclass('input', null, true, $css_postfix);
		echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'">',"\n";
		if ($this->col_has_values($k)) {
			$vals     = $this->set_values($k);
			$multiple = $this->fdd[$k]['select'] == 'M' && ! $this->fdd[$k]['values']['table'];
			$readonly = $this->readonly($k);
			echo $this->htmlSelect($this->fds[$k], $css_class_name, $vals, $row["qf$k"], $multiple, $readonly);
		} elseif (isset($this->fdd[$k]['textarea'])) {
			echo '<textarea class="',$css_class_name,'" name="'.$this->fds[$k].'"';
			echo ($this->readonly($k) ? ' disabled' : '');
			if (intval($this->fdd[$k]['textarea']['rows']) > 0) {
				echo ' rows="',$this->fdd[$k]['textarea']['rows'],'"';
			}
			if (intval($this->fdd[$k]['textarea']['cols']) > 0) {
				echo ' cols="',$this->fdd[$k]['textarea']['cols'],'"';
			}
			if (isset($this->fdd[$k]['textarea']['wrap'])) {
				echo ' wrap="',$this->fdd[$k]['textarea']['wrap'],'"';
			} else {
				echo ' wrap="virtual"';
			}
			echo '>',htmlspecialchars($row["qf$k"]),'</textarea>',"\n";

			// EK htmlArea code modification is here
			if (isset($this->fdd[$k]['textarea']['html'])) {
				echo '<script type="text/javascript" language="javascript1.2"><!--',"\n";
				echo 'editor_generate("',$this->fds[$k],'");',"\n";
				echo '// --></script>';
			}
		} else {
			$size_ml_props = '';
			$maxlen = intval($this->fdd[$k]['maxlen']);
			//$maxlen > 0 || $maxlen = 300;
			$size   = min($maxlen, 60);
			$size   && $size_ml_props .= ' size="'.$size.'"';
			$maxlen && $size_ml_props .= ' maxlength="'.$maxlen.'"';
			echo '<input class="',$css_class_name,'" type="text" ';
			echo ($this->readonly($k) ? 'disabled ' : ''),'name="',$this->fds[$k],'" value="';
			echo htmlspecialchars($row["qf$k"]),'" ',$size_ml_props,'>',"\n";
		}
		echo '</td>',"\n";
	} /* }}} */

}

/* Modeline for ViM {{{
 * vim:set ts=4:
 * vim600:fdm=marker fdl=0 fdc=0:
 * }}} */

?>
