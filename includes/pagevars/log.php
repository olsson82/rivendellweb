<script>
        var HOST_URL = "<?php echo DIR; ?>";
        var LOG_ID = "<?php echo $id; ?>";
        var USERNAME = "<?php echo $username; ?>";
        var LOCK_CODE = "<?php echo $lockguid; ?>";
        var TRAN_MOVEUP = "<?= $ml->tr('MOVEUP') ?>";
        var TRAN_MOVEDOWN = "<?= $ml->tr('MOVEDOWN') ?>";
        var TRAN_EDIT = "<?= $ml->tr('EDIT') ?>";
        var TRAN_STOP = "<?= $ml->tr('STOP') ?>";
        var TRAN_PLAY = "<?= $ml->tr('PLAY') ?>";
        var TRAN_SEGUE = "<?= $ml->tr('SEGUE') ?>";
        var TRAN_MARKERLABEL = "<?= $ml->tr('MARKER') ?>";
        var TRAN_TRACKLABEL = "<?= $ml->tr('TRACK') ?>";
        var TRAN_LOGCHAIN = "<?= $ml->tr('LOGCHAIN') ?>";
        var TRAN_ADDCART = "<?= $ml->tr('ADDCART') ?>";
        var TRAN_ADDVOICETRACK = "<?= $ml->tr('ADDVOICETRACK') ?>";
        var TRAN_ADDMARKER = "<?= $ml->tr('ADDMARKER') ?>";
        var TRAN_ADDLOGCHAIN = "<?= $ml->tr('ADDLOGCHAIN') ?>";
        var TRAN_DELLOGLINE = "<?= $ml->tr('DELLOGLINE') ?>";
        var TRAN_CLOSETHEWINDOW = "<?= $ml->tr('CLOSETHEWINDOW') ?>";
        var TRAN_ADDCHAINTEXT = "<?= $ml->tr('ADDLOGCHAIN') ?>";
        var TRAN_ADDCARTTEXT = "<?= $ml->tr('ADDCART') ?>";
        var TRAN_ADDMARKERTEXT = "<?= $ml->tr('ADDMARKER') ?>";
        var TRAN_ADDVOICETEXT = "<?= $ml->tr('ADDVOICETRACK') ?>";
        var TRAN_NOTCORRECTTIMEFORM = "<?= $ml->tr('NOTCORRECTTIMEFORM') ?>";
        var TRAN_NOTBEEMPTY = "<?= $ml->tr('NOTBEEMPTY') ?>";
        var TRAN_EDITLOGCHAINTEXT = "<?= $ml->tr('EDITLOGCHAIN') ?>";
        var TRAN_EDITVOICETEXT = "<?= $ml->tr('EDITVOICETRACK') ?>";
        var TRAN_EDITMARKERTEXT = "<?= $ml->tr('EDITMARKER') ?>";
        var TRAN_EDITCARTTEXT = "<?= $ml->tr('EDITCART') ?>";
        var TRAN_DATEFORMAT = "<?= $ml->tr('DATEFORMAT') ?>";
        var TRAN_LOGHASSAVED = "<?= $ml->tr('LOGHASSAVED') ?>";
        var TRAN_NOTPOSSIBLESAVELOG = "<?= $ml->tr('NOTPOSSIBLESAVELOG') ?>";
        var TRAN_CLOSELOGLOCKPAGE = "<?= $ml->tr('CLOSELOGLOCKPAGE') ?>";
        var TRAN_NORIGHTS = "<?= $ml->tr('NORIGHTS') ?>";
        var TRAN_YES = "<?= $ml->tr('YES') ?>";
        var TRAN_NO = "<?= $ml->tr('NO') ?>";
        var TRAN_OK = "<?= $ml->tr('OK') ?>";
        var TRAN_BUG = "<?= $ml->tr('BUG') ?>";
        var TRAN_SUN = "<?= $ml->tr('SUN') ?>";
        var TRAN_MON = "<?= $ml->tr('MON') ?>";
        var TRAN_TUE = "<?= $ml->tr('TUE') ?>";
        var TRAN_WED = "<?= $ml->tr('WED') ?>";
        var TRAN_THU = "<?= $ml->tr('THU') ?>";
        var TRAN_FRI = "<?= $ml->tr('FRI') ?>";
        var TRAN_SAT = "<?= $ml->tr('SAT') ?>";
        var TRAN_SUND = "<?= $ml->tr('SUNDAY') ?>";
        var TRAN_MOND = "<?= $ml->tr('MONDAY') ?>";
        var TRAN_TUED = "<?= $ml->tr('TUESDAY') ?>";
        var TRAN_WEDD = "<?= $ml->tr('WEDNESDAY') ?>";
        var TRAN_THUD = "<?= $ml->tr('THURSDAY') ?>";
        var TRAN_FRID = "<?= $ml->tr('FRIDAY') ?>";
        var TRAN_SATD = "<?= $ml->tr('SATURDAY') ?>";
        var TRAN_JAN = "<?= $ml->tr('JAN') ?>";
        var TRAN_FEB = "<?= $ml->tr('FEB') ?>";
        var TRAN_MAR = "<?= $ml->tr('MAR') ?>";
        var TRAN_APR = "<?= $ml->tr('APR') ?>";
        var TRAN_MAY = "<?= $ml->tr('MAY') ?>";
        var TRAN_JUN = "<?= $ml->tr('JUN') ?>";
        var TRAN_JUL = "<?= $ml->tr('JUL') ?>";
        var TRAN_AUG = "<?= $ml->tr('AUG') ?>";
        var TRAN_SEP = "<?= $ml->tr('SEP') ?>";
        var TRAN_OCT = "<?= $ml->tr('OCT') ?>";
        var TRAN_NOV = "<?= $ml->tr('NOV') ?>";
        var TRAN_DEC = "<?= $ml->tr('DEC') ?>";
        var TRAN_JANM = "<?= $ml->tr('JANUARY') ?>";
        var TRAN_FEBM = "<?= $ml->tr('FEBRUARY') ?>";
        var TRAN_MARM = "<?= $ml->tr('MARCH') ?>";
        var TRAN_APRM = "<?= $ml->tr('APRIL') ?>";
        var TRAN_MAYM = "<?= $ml->tr('MAYY') ?>";
        var TRAN_JUNM = "<?= $ml->tr('JUNE') ?>";
        var TRAN_JULM = "<?= $ml->tr('JULY') ?>";
        var TRAN_AUGM = "<?= $ml->tr('AUGUST') ?>";
        var TRAN_SEPM = "<?= $ml->tr('SEPTEMBER') ?>";
        var TRAN_OCTM = "<?= $ml->tr('OCTOBER') ?>";
        var TRAN_NOVM = "<?= $ml->tr('NOVEMBER') ?>";
        var TRAN_DECM = "<?= $ml->tr('DECEMBER') ?>";
        var TRAN_TO = "<?= $ml->tr('TO') ?>";
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
        var TRAN_SELECTNORESULTS = "<?= $ml->tr('NORESULTSFOUNDSELECT') ?>";
        var TRAN_SELECTNOOPTIONS = "<?= $ml->tr('NOOPTIONSSELECT') ?>";
        var TRAN_SELECTPRESSSELECT = "<?= $ml->tr('PRESSSELECTSELECT') ?>";
        <?php if ($info->checkusrRights('ADDTO_LOG_PRIV')) { ?>
			var ALLOW_ADDTO = "1";
		<?php } else { ?>
			var ALLOW_ADDTO = "0";
		<?php } ?>
        <?php if ($info->checkusrRights('ARRANGE_LOG_PRIV')) { ?>
			var ALLOW_ARRANGE = "1";
		<?php } else { ?>
			var ALLOW_ARRANGE = "0";
		<?php } ?>
        <?php if ($info->checkusrRights('REMOVEFROM_LOG_PRIV')) { ?>
			var ALLOW_REMOVEFROM = "1";
		<?php } else { ?>
			var ALLOW_REMOVEFROM = "0";
		<?php } ?>
    </script>