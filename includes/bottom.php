<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>
        </div>
    </div>
    <script src="assets/static/js/components/dark.js"></script>
    <script src="assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="assets/extensions/jquery/jquery.min.js"></script>
    <?php
    if (isset($plugin_js)) {
    echo $plugin_js; }?>
    <script src="assets/compiled/js/app.js"></script>

    <?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/pagevars/'.$page_vars.'.php'; ?>
    

    <?php if (isset($page_js)) { echo $page_js; }?>
    <script src="assets/static/js/allpages.js"></script>

</body>

</html>