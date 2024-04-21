<script>
        var HOST_URL = "<?php echo DIR; ?>";
        var SERVICENAME = "<?php echo $_COOKIE['serviceName'] ?>";
        var TRAN_VOICETRACKER = "<?= $ml->tr('VOICETRACKER') ?>";
        var TRAN_REMOVELOG = "<?= $ml->tr('REMOVELOG') ?>";
        var TRAN_LOGNAMENOTEMPTY = "<?= $ml->tr('LOGNAMENOTEMPTY') ?>";
        var TRAN_CLOSEADDLOG = "<?= $ml->tr('CLOSEADDLOG') ?>";
        var TRAN_LOGBEINGEDIT = "<?= $ml->tr('LOGBEINGEDIT') ?>";
        var TRAN_YES = "<?= $ml->tr('YES') ?>";
        var TRAN_NO = "<?= $ml->tr('NO') ?>";
        var TRAN_OK = "<?= $ml->tr('OK') ?>";
        var TRAN_NOSPACEALLOWED = "<?= $ml->tr('NOSPACEALLOWED') ?>";
        var TRAN_DELETEMARKED = "<?= $ml->tr('DELMARKEDLOGS') ?>";
        var TRAN_MARKEDDELETED = "<?= $ml->tr('DELMARKEDLOGSDONE') ?>";
        var TRAN_DELMARKEDLOGSNOT = "<?= $ml->tr('DELMARKEDLOGSNOT') ?>";
        var TRAN_DELMARKEDLOGSNOTSEL = "<?= $ml->tr('DELMARKEDLOGSNOTSEL') ?>";  
        var TRAN_NORIGHTS = "<?= $ml->tr('NORIGHTS') ?>";
        var TRAN_BUG = "<?= $ml->tr('BUG') ?>";        
        var TRAN_TABLEFIRST = "<?= $ml->tr('TAFIRST') ?>";
        var TRAN_TABLELAST = "<?= $ml->tr('TALAST') ?>";
        var TRAN_TABLENEXT = "<?= $ml->tr('TANEXT') ?>";
        var TRAN_TABLEPREV = "<?= $ml->tr('TAPREVIUS') ?>";
        var TRAN_TABLESHOW = "<?= $ml->tr('TASHOW') ?>";
        var TRAN_TABLESELECTED = "<?= $ml->tr('SELECTED') ?>";
        var TRAN_TABLENODATA = "<?= $ml->tr('TABLENODATA') ?>";
        var TRAN_TABLESHOWS = "<?= $ml->tr('SHOWS') ?>";
        var TRAN_TABLETO = "<?= $ml->tr('TO') ?>";
        var TRAN_TABLETOTAL = "<?= $ml->tr('OFTOTAL') ?>";
        var TRAN_TABLEROWS = "<?= $ml->tr('ROWS') ?>";
        var TRAN_TABLEFILTERED = "<?= $ml->tr('FILTEREDFROM') ?>";
        var TRAN_TABLELOADING = "<?= $ml->tr('LOADING') ?>";
        var TRAN_TABLEWORKING = "<?= $ml->tr('WORKING') ?>";
        var TRAN_TABLESEARCH = "<?= $ml->tr('SEARCH') ?>";
        var TRAN_TABLENSORTRISE = "<?= $ml->tr('ENABLERISING') ?>";
        var TRAN_TABLENSORTFALL = "<?= $ml->tr('ENABLEFALLING') ?>";
        var TRAN_TABLENORESULTS = "<?= $ml->tr('NORESULTS') ?>";
        <?php if ($info->checkusrRights('DELETE_LOG_PRIV')) { ?>
			var ALLOW_DEL = "1";
		<?php } else { ?>
			var ALLOW_DEL = "0";
		<?php } ?>
		<?php if ($info->checkusrRights('CREATE_LOG_PRIV')) { ?>
			var ALLOW_ADD = "1";
		<?php } else { ?>
			var ALLOW_ADD = "0";
		<?php } ?>
		<?php if ($info->checkusrRights('VOICETRACK_LOG_PRIV')) { ?>
			var ALLOW_VOICE = "1";
		<?php } else { ?>
			var ALLOW_VOICE = "0";
		<?php } ?>
    </script>