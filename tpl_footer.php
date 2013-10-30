<?php
/**
 * Template footer, included in the main and detail files
 */

// must be run from within DokuWiki
if (!defined('DOKU_INC')) die();
?>

<!-- ********** FOOTER ********** -->
<div id="dokuwiki__footer"><div class="pad">
    <?php tpl_license(''); // license text ?>
</div>   
<?php
    if ($_SERVER['REMOTE_USER']) { 
		echo '<div class="pad">';
        tpl_userinfo(); /* 'Logged in as ...' */ 
		echo '</div>';
    }
?>
</div><!-- /footer -->
       
<?php tpl_includeFile('footer.html') ?>
