<?php
/*******************************************************************

Copyright 2010, Adrian Laurenzi

This file is part of ImpactProbe.

ImpactProbe is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
at your option) any later version.

ImpactProbe is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with ImpactProbe. If not, see <http://www.gnu.org/licenses/>.

*******************************************************************/
?>
<script type="text/javascript">
    function DeleteProject(id) {
        if(confirm("Are you sure you want to delete this project?\nWARNING: all files and data related to this project will be permanently deleted.")) {
            window.location = '<?= Url::base(TRUE).'params/delete/'?>'+id;
        }
    }
    function ShowWarning() {
        alert("You must deactivate this project before it can be deleted (click the 'stop' icon).");
    }
</script>

<? if(count($projects) > 0) { ?>
<p>
<a href="<?= Url::base(TRUE) ?>params/new" class="button_lg button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-document"></span>New Monitoring Project</a>
<a href="#" class="button_lg button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-wrench"></span>Settings</a>
</p>

<table width="600" border="0" cellspacing="0" cellpadding="5" style="border:1px solid #000;">
    <tr class="table_header">
        <td align="left"><b>Title</b></td>
        <td align="center"><b>Date Created</b></td>
        <td align="center">&nbsp;</td>
    </tr>
    <? foreach($projects as $project) { ?>
        <tr>
            <td align="left"><?= $project['project_title'] ?></td>
            <td align="center"><?= date(Kohana::config('myconf.date_format'), $project['date_created']) ?></td>
            <td align="center">
            <ul id="icons" class="ui-widget ui-helper-clearfix">
                <a href="<?= Url::base(TRUE).'results/view/'.$project['project_id'] ?>"><li class="button_hover ui-state-default ui-corner-all" title="Results"><span class="ui-icon ui-icon-signal"></span></li></a>
                <a href="<?= Url::base(TRUE).'gather/log/'.$project['project_id'] ?>"><li class="button_hover ui-state-default ui-corner-all" title="Gather log"><span class="ui-icon ui-icon-note"></span></li></a>
                <a href="<?= Url::base(TRUE).'params/modify/'.$project['project_id'] ?>"><li class="button_hover ui-state-default ui-corner-all" title="Edit parameters"><span class="ui-icon ui-icon-pencil"></span></li></a>
                
                <a href="<?= Url::base(TRUE).'home/project_change_state/'.$project['project_id'] ?>"><li class="button_hover ui-state-default ui-corner-all" title="<?= ($project['active']) ? 'Deactivate project' : 'Activate project' ?>"><span class="ui-icon <?= ($project['active']) ? 'ui-icon-stop' : 'ui-icon-play' ?>"></span></li></a>
                
                <a href="javascript:<?= ($project['active']) ? 'ShowWarning()' : 'DeleteProject('.$project['project_id'].')' ?>"><li class="button_hover ui-state-default ui-corner-all" title="Delete project"><span class="ui-icon ui-icon-trash"></span></li></a>
            </ul>
            </td>
        </tr>
    <? } ?>
</table>
<? } ?>

<p><a href="<?= Url::base(TRUE) ?>params/new" class="button_lg button_hover ui-state-default ui-corner-all"><span class="ui-icon ui-icon-document"></span>New Monitoring Project</a></p>