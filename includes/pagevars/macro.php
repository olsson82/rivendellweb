<script>
        var HOST_URL = "<?php echo DIR; ?>";
        var CART_ID = "<?php echo $id; ?>";
        var TRAN_EDITCOMMAND = "<?= $ml->tr('EDITCOMMAND') ?>";
        var TRAN_REMOVECOMMAND = "<?= $ml->tr('REMOVECOMMAND') ?>";
        var TRAN_CLOSECOMMANDOWINDOW = "<?= $ml->tr('CLOSECOMMANDOWINDOW') ?>";
        var TRAN_COMMANDONOTPOSSIBLE = "<?= $ml->tr('COMMANDONOTPOSSIBLE') ?>";
        var TRAN_REMOVEMACROCOMMAND = "<?= $ml->tr('REMOVEMACROCOMMAND') ?>";
        var TRAN_CARTSAVED = "<?= $ml->tr('CARTSAVED') ?>";
        var TRAN_NORIGHTS = "<?= $ml->tr('NORIGHTS') ?>";
        var TRAN_YES = "<?= $ml->tr('YES') ?>";
        var TRAN_NO = "<?= $ml->tr('NO') ?>";
        var TRAN_OK = "<?= $ml->tr('OK') ?>";
        var TRAN_BUG = "<?= $ml->tr('BUG') ?>";
        var TRAN_NOTBEEMPTY = "<?= $ml->tr('NOTBEEMPTY') ?>";
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
        <?php if ($info->checkusrRights('DELETE_CARTS_PRIV')) { ?>
            var ALLOW_DEL = "1";
        <?php } else { ?>
            var ALLOW_DEL = "0";
        <?php } ?>
        <?php if ($info->checkusrRights('MODIFY_CARTS_PRIV')) { ?>
            var ALLOW_MOD = "1";
        <?php } else { ?>
            var ALLOW_MOD = "0";
        <?php } ?>
    </script>