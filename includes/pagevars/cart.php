<script>
    var HOST_URL = "<?php echo DIR; ?>";
    var AV_LENG = "<?php echo $info->getCartInfo($id, "AVERAGE_LENGTH"); ?>";
    var FO_LENG = "<?php echo $info->getCartInfo($id, "FORCED_LENGTH"); ?>";
    var CART_ID = "<?php echo $id; ?>";
    var CUT_ORDER = "<?php if ($info->getCartInfo($_GET["id"], "USE_WEIGHTING") == 'Y') {
        echo "1";
    } else {
        echo "2";
    } ?>";

    var TRAN_PLAY = "<?= $ml->tr('PLAY') ?>";
    var TRAN_PAUSE = "<?= $ml->tr('PAUSE') ?>";
    var TRAN_RESUME = "<?= $ml->tr('RESUME') ?>";
    var TRAN_RECORD = "<?= $ml->tr('RECORD') ?>";
    var TRAN_UPLOAD = "<?= $ml->tr('UPLOAD') ?>";
    var TRAN_STOP = "<?= $ml->tr('STOP') ?>";
    var TRAN_SAVEREQ = "<?= $ml->tr('SAVERECORDING') ?>";
    var TRAN_DOWN_REQ = "<?= $ml->tr('DOWNLOADRECORDING') ?>";
    var TRAN_ORDER = "<?= $ml->tr('ORDER') ?>";
    var TRAN_WEIGHT = "<?= $ml->tr('WEIGHT') ?>";
    var TRAN_WT = "<?= $ml->tr('WT') ?>";
    var TRAN_ORD = "<?= $ml->tr('ORD') ?>";
    var TRAN_CUTINFOEDIT = "<?= $ml->tr('EDITCUTINFO') ?>";
    var TRAN_REMOVECUT = "<?= $ml->tr('REMOVECUT') ?>";
    var TRAN_EDITAUDIOMARKERS = "<?= $ml->tr('EDITAUDIOMARKERS') ?>";
    var TRAN_IMPORTAUDIO = "<?= $ml->tr('IMPORTAUDIO') ?>";
    var TRAN_EXPORTAUDIO = "<?= $ml->tr('EXPORTAUDIO') ?>";
    var TRAN_CLOSETHEWINDOW = "<?= $ml->tr('CLOSETHEWINDOW') ?>";
    var TRAN_CLOSECUTIMPORT = "<?= $ml->tr('CLOSECUTIMPORT') ?>";
    var TRAN_SELECTBITRATE = "<?= $ml->tr('EXPSELECTBITRATE') ?>";
    var TRAN_SELECTSAMPLERATE = "<?= $ml->tr('EXPSELECTSAMPRATE') ?>";
    var TRAN_EDITAUDIO = "<?= $ml->tr('WANTEDITAUDIOFILE') ?>";
    var TRAN_LOADINGAUDIO = "<?= $ml->tr('LOADINGAUDIO') ?>";
    var TRAN_CLOSEEDITMARKERWINDOW = "<?= $ml->tr('CLOSEEDITMARKERWINDOW') ?>";
    var TRAN_EDITSAVEAUDIOMARK = "<?= $ml->tr('WANTSAVEAUDIO') ?>";
    var TRAN_NOTPOSSSAVEMAKRES = "<?= $ml->tr('NOTPOSSSAVEMARKERS') ?>";
    var TRAN_CARTSAVED = "<?= $ml->tr('CARTSAVED') ?>";
    var TRAN_TALKSTART = "<?= $ml->tr('TALKSTART') ?>";
    var TRAN_TALKEND = "<?= $ml->tr('TALKEND') ?>";
    var TRAN_SEGUESTART = "<?= $ml->tr('SEGUESTART') ?>";
    var TRAN_SEGUEEND = "<?= $ml->tr('SEGUEEND') ?>";
    var TRAN_HOOKSTART = "<?= $ml->tr('HOOKSTART') ?>";
    var TRAN_HOOKEND = "<?= $ml->tr('HOOKEND') ?>";
    var TRAN_FADEUP = "<?= $ml->tr('FADEUP') ?>";
    var TRAN_FADEDOWN = "<?= $ml->tr('FADEDOWN') ?>";
    var TRAN_CUTSTART = "<?= $ml->tr('CUTSTART') ?>";
    var TRAN_CUTEND = "<?= $ml->tr('CUTEND') ?>";
    var TRAN_NORIGHTS = "<?= $ml->tr('NORIGHTS') ?>";
    var TRAN_YES = "<?= $ml->tr('YES') ?>";
    var TRAN_NO = "<?= $ml->tr('NO') ?>";
    var TRAN_OK = "<?= $ml->tr('OK') ?>";
    var TRAN_BUG = "<?= $ml->tr('BUG') ?>";
    var TRAN_NOTBEEMPTY = "<?= $ml->tr('NOTBEEMPTY') ?>";
    var TRAN_CLOSEAUDIOEXPORTWINDOW = "<?= $ml->tr('EXPORTWINDOWCLOSE') ?>";
    var TRAN_CLOSECUTINFOWINDOW = "<?= $ml->tr('CLOSECUTINFOWINDOW') ?>";
    var TRAN_ONLYDIGITS = "<?= $ml->tr('ONLYDIGITS') ?>";
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
    <?php if ($info->checkusrRights('EDIT_AUDIO_PRIV')) { ?>
        var ALLOW_AUDIO = "1";
    <?php } else { ?>
        var ALLOW_AUDIO = "0";
    <?php } ?>
    <?php if ($info->checkusrRights('CREATE_CARTS_PRIV')) { ?>
        var ALLOW_CREATE = "1";
    <?php } else { ?>
        var ALLOW_CREATE = "0";
    <?php } ?>
</script>