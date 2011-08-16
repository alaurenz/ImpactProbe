<p class="pagination">
    <ul id="icons" class="ui-widget ui-helper-clearfix">
	<?php if ($first_page !== FALSE): ?>
                <li class="button_hover ui-state-default ui-corner-all" title="<?php echo __('First') ?>" onclick="GoToPage(<?php echo $first_page ?>)" rel="first"><span class="ui-icon ui-icon-arrowstop-1-w"></span></li>
	<?php else: ?>
		<li class="ui-state-default ui-corner-all" style="background:#FFFFFF;"><span class="ui-icon ui-icon-arrowstop-1-w"></span></li>
	<?php endif ?>

	<?php if ($previous_page !== FALSE): ?>
                <li class="button_hover ui-state-default ui-corner-all" title="<?php echo __('Previous') ?>" onclick="GoToPage(<?php echo $previous_page ?>)" rel="prev"><span class="ui-icon ui-icon-arrow-1-w"></span></li>
	<?php else: ?>
		<li class="ui-state-default ui-corner-all" style="background:#FFFFFF;"><span class="ui-icon ui-icon-arrow-1-w"></span></li>
	<?php endif ?>

	<?php for ($i = 1; $i <= $total_pages; $i++): ?>

		<?php if ($i == $current_page): ?>
			<li class="ui-state-default ui-corner-all" style="background:#FFFFFF;"><span style="font-size:12px; color:#000000">&nbsp;<strong><?php echo $i ?></strong>&nbsp;</span></li>
		<?php else: ?>
                        <li class="button_hover ui-state-default ui-corner-all" title="Page <?php echo $i ?>" onclick="GoToPage(<?php echo $i ?>)"><span style="font-size:12px;">&nbsp;<?php echo $i ?>&nbsp;</span></li>
		<?php endif ?>

	<?php endfor ?>

	<?php if ($next_page !== FALSE): ?>
		<li class="button_hover ui-state-default ui-corner-all" title="<?php echo __('Next') ?>" onclick="GoToPage(<?php echo $next_page ?>)" rel="next"><span class="ui-icon ui-icon-arrow-1-e"></span></li>
	<?php else: ?>
		<li class="ui-state-default ui-corner-all" style="background:#FFFFFF;"><span class="ui-icon ui-icon-arrow-1-e"></span></li>
	<?php endif ?>

	<?php if ($last_page !== FALSE): ?>
	        <li class="button_hover ui-state-default ui-corner-all" title="<?php echo __('Last') ?>" onclick="GoToPage(<?php echo $last_page ?>)" rel="last"><span class="ui-icon ui-icon-arrowstop-1-e"></span></li>
	<?php else: ?>
		<li class="ui-state-default ui-corner-all" style="background:#FFFFFF;"><span class="ui-icon ui-icon-arrowstop-1-e"></span></li>
	<?php endif ?>
    </ul>
</p><!-- .pagination -->