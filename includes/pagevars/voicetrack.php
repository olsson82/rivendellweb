<script>
    var HOST_URL = "<?php echo DIR; ?>";
    var VT_GROUP = "<?php echo $vtGroup; ?>";
    var VT_USERNAME = "<?php echo $username; ?>";
    var TRAN_PLAY = "<?= $ml->tr('PLAY') ?>";
    var TRAN_PAUSE = "<?= $ml->tr('PAUSE') ?>";
    var TRAN_RESUME = "<?= $ml->tr('RESUME') ?>";
    var TRAN_RECORD = "<?= $ml->tr('RECORD') ?>";
    var TRAN_UPLOAD = "<?= $ml->tr('UPLOAD') ?>";
    var TRAN_STOP = "<?= $ml->tr('STOP') ?>";
    var TRAN_SAVEREQ = "<?= $ml->tr('SAVERECORDING') ?>";
    var TRAN_DOWN_REQ = "<?= $ml->tr('DOWNLOADRECORDING') ?>";
    var TRAN_VOICETRACKER = "<?= $ml->tr('VOICETRACKER') ?>";
    var TRAN_MULTITRACKEDITOR = "<?= $ml->tr('MULTITRACKEDITOR') ?>";
    var TRAN_LOADINGAUDIO = "<?= $ml->tr('LOADINGAUDIO') ?>";
    var TRAN_CLOSEUPLOADWINDOWVOICE = "<?= $ml->tr('CLOSEUPLOADWINDOWVOICE') ?>";
    var TRAN_CLOSERECORDWINDOWVOICE = "<?= $ml->tr('CLOSERECORDWINDOWVOICE') ?>";
    var TRAN_CLOSETHEWINDOW = "<?= $ml->tr('CLOSETHEWINDOW') ?>";
    var TRAN_YES = "<?= $ml->tr('YES') ?>";
    var TRAN_NO = "<?= $ml->tr('NO') ?>";
    var TRAN_OK = "<?= $ml->tr('OK') ?>";
    var TRAN_BUG = "<?= $ml->tr('BUG') ?>";
    var TRAN_SELECTNORESULTS = "<?= $ml->tr('NORESULTSFOUNDSELECT') ?>";
    var TRAN_SELECTNOOPTIONS = "<?= $ml->tr('NOOPTIONSSELECT') ?>";
    var TRAN_SELECTPRESSSELECT = "<?= $ml->tr('PRESSSELECTSELECT') ?>";
    var TRAN_NORIGHTS = "<?= $ml->tr('NORIGHTS') ?>";
    <?php if ($json_sett["multitrack"] == '1') { ?>
        var ALLOW_MULTITRACK = "1";
    <?php } else { ?>
        var ALLOW_MULTITRACK = "0";
    <?php } ?>
    
    <?php if ($info->checkusrRights('CREATE_CARTS_PRIV')) { ?>
            var ALLOW_CREATE = "1";
        <?php } else { ?>
            var ALLOW_CREATE = "0";
        <?php } ?>               
</script>