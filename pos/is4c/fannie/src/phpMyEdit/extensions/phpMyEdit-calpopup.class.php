<?php

/*
 * phpMyEdit - instant MySQL table editor and code generator
 *
 * extensions/phpMyEdit-calpopup.class.php - phpMyEdit calendar popup extension
 * ____________________________________________________________
 *
 * Contribution of Adam Hammond <php@pixolet.co.uk>, London, UK
 * Copyright (c) 2003-2006 Platon Group, http://platon.sk/
 * All rights reserved.
 *
 * See README file for more information about this software.
 * See COPYING file for license information.
 *
 * Download the latest version from
 * http://platon.sk/projects/phpMyEdit/
 */
 
/* $Platon: phpMyEdit/extensions/phpMyEdit-calpopup.class.php,v 1.9 2006-01-22 21:44:17 nepto Exp $ */

/*
    OVERVIEW
    --------

	NOTE...This extension will not work with the CVS version of PME

    calPopup extends the standard phpMyEdit class to allow
    a calendar popup helper to be put on any text field.
    This extension uses the free jsCalendar code from
    http://dynarch.com/mishoo/calendar.epl website.
    
    REQUIREMENTS
    ------------

    The requirement is a properly installed jsCalendar script.
    All browsers supported by jsCalendar are supported by this
    extension.

    USAGE
    -----

    This extension enables the display of a popup calendar selection
    against selected fields.
    
    In order to use it, you should:

    1. Load the jsCalendar scripts in the <head>...</head> section of
       your phpMyEdit calling program, substituting the correct paths:

       <script type="text/javascript" src="js/calendar.js"></script>
       <script type="text/javascript" src="js/lang/calendar-en.js"></script>
       <script type="text/javascript" src="js/calendar-setup.js"></script>

    2. Choose your preferred jsCalendar CSS file (see jsCalendar
       documentation) and add the following in the <head>...</head>
       section of your phpMyEdit calling program, substituting the
       correct path:
        
        <link rel="stylesheet" type="text/css" media="screen"
                href="css/calendar-system.css">

       NOTE: To avoid an unwanted side effect in the CSS style
       produced by phpMyEditSetup.php, add a 'width:auto' property
       into the '.calendar table' entry in your selected jsCalendar
       style sheet.

    3. Call to phpMyEdit-calPopup.class.php instead
       of phpMyEdit.class.php.

       Example:

       require_once 'extensions/phpMyEdit-calpopup.class.php';
       new phpMyEdit_calpopup($opts);

    4. Add 'calendar' parameter to the field definitions where you
       want a calendar popup in your phpMyEdit calling program.

       Example:

       $opts['fdd']['col_name'] = array(
         'name'     => 'Column',
         'select'   => 'T',
         'options'  => 'ACPVD',
         'required' => true,
         'calendar' => true
       );

       This is will display a button next to the field which pops up
       a calendar when clicked. If that field has a 'strftimemask'
       parameter set, it will use this for the date format.
        
       For more advanced usage, you can set the 'calendar' parameter
       to an array of valid jsCalendar Calendar.setup options
       (see jSCalendar document for details). Note that not all
       of these options make sense to use in phpMyEdit, and some
       of them will actively break the function.
        
       Example:
        
       $opts['fdd']['col_name'] = array(
         'name'     => 'Column',
         'select'   => 'T',
         'options'  => 'ACPVD',
         'required' => true,
         'calendar' => array(
           'ifFormat'    => '%Y/%m/%d', // defaults to the ['strftimemask']
           'firstDay'    => 1,          // 0 = Sunday, 1 = Monday
           'singleClick' => true,       // single or double click to close
           'weekNumbers' => true,       // Show week numbers
           'showsTime'   => false,      // Show time as well as date
           'timeFormat'  => '24',       // 12 or 24 hour clock
		   'label'       => '...',      // button label (used by phpMyEdit)
           'date'        => '2003-12-19 10:00' // Initial date/time for popup
                                               // (see notes below)
         )
       );

    NOTES
    -----

    1. The popup will normally set the initial value to the current
       field value or to current date/time. 'date' option will always
       override this, even if there is a current date/time value
       in the field. If you want a default value only if the field
       is currently empty, use the phpMyEdit 'default' option.

    2. Only the options listed above may be set by the user, any other
       options will be ignored.

    SEARCH KEYWORD
    --------------

	Search for "CalPopup" string in this source code,
	to find all extension related modifications.
*/

require_once dirname(__FILE__).'/../phpMyEdit.class.php';

class phpMyEdit_calpopup extends phpMyEdit
{
	/* CalPopup mod start */

	/* Array for collecting list of fields with calendar popups */
	var $calendars;

	/* Array of valid options for passing to Calendar.setup */
	var $valid_opts = array(
			'button','ifFormat','singleClick','firstDay',
			'weekNumbers','showsTime','timeFormat','date'
			);

	/**
	 * Checks to see if the calendar parameter is set on the field
	 *
	 * @param	k			current field name
	 * @param	curval		current value of field (set to null for default)
	 *
	 * If the calendar parameter is set on the field, this function displays
	 * the button. It then pushes the Calendar.setup parameters into an array,
	 * including the user specified ones in the calling program is they exist.
	 * This array is then added to the $calendars array indexed by the field
	 * name. This allows for multiple fields with calendar popups.
	 */
	function CalPopup_helper($k, $curval) /* {{{ */
	{
		if (@$this->fdd[$k]['calendar']) {
			$cal_ar['ifFormat']    = '%Y-%m-%d %H:%M';
			$cal_ar['showsTime']   = true;
			$cal_ar['singleClick'] = false;
			if (isset($curval)) {
				if (substr($curval, 0, 4) != '0000')
					$cal_ar['date'] = $curval;
			}
			if (isset($this->fdd[$k]['strftimemask'])) {
				$cal_ar['ifFormat'] = $this->fdd[$k]['strftimemask'];
			}
			if (is_array($this->fdd[$k]['calendar'])) {
				foreach($this->fdd[$k]['calendar'] as $ck => $cv) {
					$cal_ar[$ck] = $cv;
				}
			}
			$cal_ar['button'] = 'pme_calpopup_button_'.$this->fds[$k];
			$this->calendars[$this->fds[$k]] = $cal_ar;
			$label = @$this->fdd[$k]['calendar']['label'];
			strlen($label) || $label = '...';
			echo '<button id="',$cal_ar['button'],'">',$label,'</button>';
		}
	} /* }}} */

	/* CalPopup mod end */

	function display_add_record() /* {{{ */
	{
		for ($tab = 0, $k = 0; $k < $this->num_fds; $k++) {
			if (isset($this->fdd[$k]['tab']) && $this->tabs_enabled() && $k > 0) {
				$tab++;
				echo '</table>',"\n";
				echo '</div>',"\n";
				echo '<div id="phpMyEdit_tab',$tab,'">',"\n";
				echo '<table class="',$this->getCSSclass('main'),'" summary="',$this->tb,'">',"\n";
			}
			if (! $this->displayed[$k]) {
				continue;
			}
			if ($this->hidden($k)) {
				echo $this->htmlHidden($this->fds[$k], $row["qf$k"]);
				continue;
			}
			$css_postfix    = @$this->fdd[$k]['css']['postfix'];
			$css_class_name = $this->getCSSclass('input', null, 'next', $css_postfix);
			echo '<tr class="',$this->getCSSclass('row', null, true, $css_postfix),'">',"\n";
			echo '<td class="',$this->getCSSclass('key', null, true, $css_postfix),'">',$this->fdd[$k]['name'],'</td>',"\n";
			echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'"';
			echo $this->getColAttributes($k),">\n";
			if ($this->col_has_values($k)) {
				$vals       = $this->set_values($k);
				$selected   = @$this->fdd[$k]['default'];
				$multiple   = $this->col_has_multiple_select($k);
				$readonly   = $this->readonly($k);
				$strip_tags = true;
				$escape     = true;
				echo $this->htmlSelect($this->fds[$k], $css_class_name, $vals, $selected,
						$multiple, $readonly, $strip_tags, $escape);
			} elseif (isset ($this->fdd[$k]['textarea'])) {
				echo '<textarea class="',$css_class_name,'" name="',$this->fds[$k],'"';
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
			} else {
				// Simple edit box required
				$size_ml_props = '';
				$maxlen = intval($this->fdd[$k]['maxlen']);
				$size   = isset($this->fdd[$k]['size']) ? $this->fdd[$k]['size'] : min($maxlen, 60);
				$size   && $size_ml_props .= ' size="'.$size.'"';
				$maxlen && $size_ml_props .= ' maxlength="'.$maxlen.'"';

				/* CalPopup mod start */
				if (@$this->fdd[$k]['calendar']) {
					$size_ml_props .= ' id="pme_calpopup_input_'.$this->fds[$k].'"';
				}
				/* CalPopup mod end */

				echo '<input class="',$css_class_name,'" type="text" ';
				echo ($this->readonly($k) ? 'disabled ' : ''),' name="',$this->fds[$k],'"';
				echo $size_ml_props,' value="';
				echo htmlspecialchars($this->fdd[$k]['default']),'">';

                /* CalPopup mod start */
				/* Call CalPopup helper function */
				$this->CalPopup_helper($k, null);
				/* CalPopup mod end */
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
		echo '<td class="',$this->getCSSclass('value', null, true, $css_postfix),'"';
		echo $this->getColAttributes($k),">\n";
		if ($this->col_has_values($k)) {
			$vals       = $this->set_values($k);
			$multiple   = $this->col_has_multiple_select($k);
			$readonly   = $this->readonly($k);
			$strip_tags = true;
			$escape     = true;
			echo $this->htmlSelect($this->fds[$k], $css_class_name, $vals, $row["qf$k"],
					$multiple, $readonly, $strip_tags, $escape);
		} elseif (isset($this->fdd[$k]['textarea'])) {
			echo '<textarea class="',$css_class_name,'" name="',$this->fds[$k],'"';
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
		} else {
			$size_ml_props = '';
			$maxlen = intval($this->fdd[$k]['maxlen']);
			$size   = isset($this->fdd[$k]['size']) ? $this->fdd[$k]['size'] : min($maxlen, 60);
			$size   && $size_ml_props .= ' size="'.$size.'"';
			$maxlen && $size_ml_props .= ' maxlength="'.$maxlen.'"';

			/* CalPopup mod start */
			if (@$this->fdd[$k]['calendar']) {
				$size_ml_props .= ' id="pme_calpopup_input_'.$this->fds[$k].'"';
			}
			/* CalPopup mod end */

			echo '<input class="',$css_class_name,'" type="text" ';
			echo ($this->readonly($k) ? 'disabled ' : ''),'name="',$this->fds[$k],'" value="';
			echo htmlspecialchars($row["qf$k"]),'" ',$size_ml_props,'>',"\n";

            /* CalPopup mod start */
			/* Call CalPopup helper function */
			$this->CalPopup_helper($k, htmlspecialchars($row["qf$k"]));
			/* CalPopup mod end */
		}
		echo '</td>',"\n";
	} /* }}} */

	function form_end() /* {{{ */
	{
		if ($this->display['form']) {
			echo '</form>',"\n";
			
			/* CalPopup mod start */
			
			/* Add script calls to the end of the form for all fields
			   with calendar popups. */
			if (isset($this->calendars)) {
				echo '<script type="text/javascript"><!--',"\n";
				foreach($this->calendars as $ck => $cv) {
					echo 'Calendar.setup({',"\n";
					foreach ($cv as $ck1 => $cv1) {
						if (in_array($ck1, $this->valid_opts)) {
							echo "\t",str_pad($ck1, 15),' : "',$cv1,'",',"\n";
						}
					}
					echo "\t",str_pad('inputField', 15),' : "pme_calpopup_input_',$ck,'"',"\n";
					echo '});',"\n";
				};
				echo '// --></script>',"\n";
			};

			/* CalPopup mod end */
		};
	} /* }}} */
}

?>
