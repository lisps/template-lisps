<?php
/**
 * DokuWiki LISPS Template 2013
 *
 * @link     http://dokuwiki.org/template
 * @author   Anika Henke <anika@selfthinker.org>
 * @author   Clarence Lee <clarencedglee@gmail.com>
 * @author   lisps
 * @license  GPL 2 (http://www.gnu.org/licenses/gpl.html)
 */

if (!defined('DOKU_INC')) die(); /* must be run from within DokuWiki */

/**
 * returns lastmod of given page id
 */
 function template_getLastModOfGivenPageId($pageid){
	global $ID;
	global $INFO;
	
	// Backup id of current page
	$id_save = $ID;
	// Set global page id
    $ID = $pageid; 
	// get page infos
    $tmp_info = pageinfo(); 
	// save lastmod
    $lastmod = $tmp_info['lastmod'];
	// restore global page id
    $ID = $id_save;
	
	return $lastmod;
}
/**
 * Includes the rendered HTML of a given page
 *
 * This function is useful to populate sidebars or similar features in a
 * template
 */
function template_tpl_include_page($pageid, $print = true, $propagate = false, $rev = '') {
    if (!$pageid) return false;
    if ($propagate) $pageid = page_findnearest($pageid);

    global $TOC;
    $oldtoc = $TOC;
    $html   = p_wiki_xhtml($pageid, $rev, false);
    $TOC    = $oldtoc;

    if(!$print) return $html;
    echo $html;
    return $html;
}

//Sidebar with replace CONSTANTS
$search=array("_USERNAME_",
			  "_CLIENTNAME_",
			  "_PAGEID_");
$replace=array($INFO["userinfo"]["name"],
			   $_SERVER['REMOTE_USER'],
			   $ID);
if($_SERVER['REMOTE_USER'] && page_exists('user:'.$_SERVER['REMOTE_USER'].':sidebar')){
	$sidebar = template_tpl_include_page('user:'.$_SERVER['REMOTE_USER'].':sidebar', 0, 0, template_getLastModOfGivenPageId('user:'.$_SERVER['REMOTE_USER'].':sidebar'));
} else if($_SERVER['REMOTE_USER'] && page_exists('user:sidebar')){
	$sidebar = template_tpl_include_page('user:sidebar', 0, 0, template_getLastModOfGivenPageId('user:sidebar'));
} else {
	$sidebar = tpl_include_page('sidebar', 0, 0, '');
}
$sidebar=str_ireplace($search,$replace,$sidebar);

$hasSidebar = $sidebar?true:false;
$showSidebar = $hasSidebar && ($ACT=='show');
?><!DOCTYPE html>
<html lang="<?php echo $conf['lang'] ?>" dir="<?php echo $lang['direction'] ?>" class="no-js">
<head>
    <meta charset="utf-8" />
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /><![endif]-->
    <title><?php tpl_pagetitle() ?> [<?php echo strip_tags($conf['title']) ?>]</title>
    <script>(function(H){H.className=H.className.replace(/\bno-js\b/,'js')})(document.documentElement)</script>
    <?php tpl_metaheaders() ?>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php echo tpl_favicon(array('favicon', 'mobile')) ?>
    <?php tpl_includeFile('meta.html') ?>
</head>

<body>
    <!--[if lte IE 7 ]><div id="IE7"><![endif]--><!--[if IE 8 ]><div id="IE8"><![endif]-->
    <div id="dokuwiki__site"><div id="dokuwiki__top"
        class="dokuwiki site mode_<?php echo $ACT ?> <?php echo ($showSidebar) ? 'showSidebar' : '';
        ?> <?php echo ($hasSidebar) ? 'hasSidebar' : ''; ?>">

        <?php include('tpl_header.php') ?>

        <div class="wrapper group">

            <?php if($showSidebar): ?>
                <!-- ********** ASIDE ********** -->
                <div id="dokuwiki__aside"><div class="pad include group">
                    <h3 class="toggle"><?php echo $lang['sidebar'] ?></h3>
                    <div class="content">
                        <?php tpl_flush() ?>
                        <?php tpl_includeFile('sidebarheader.html') ?>
                        <?php 
							echo $sidebar;
						?>
                        <?php tpl_includeFile('sidebarfooter.html') ?>
                    </div>
                </div></div><!-- /aside -->
            <?php endif; ?>

            <!-- ********** CONTENT ********** -->
            <div id="dokuwiki__content"><div class="pad group">

                <div class="pageId"><span><?php echo hsc($ID) ?></span></div>

                <div class="page group">
                    <?php tpl_flush() ?>
                    <?php tpl_includeFile('pageheader.html') ?>
                    <!-- wikipage start -->
                    <?php tpl_content() ?>
                    <!-- wikipage stop -->
                    <?php tpl_includeFile('pagefooter.html') ?>
                </div>
				<!-- Display permalink -->
                <div class="docInfo">
					<?php echo '<a class="mainpermalink" href="' . DOKU_BASE . "doku.php?id=" . $ID.'&rev='.($INFO['rev']?$INFO['rev']:$INFO['lastmod']) .' ">Permalink</a> ';?>
					<?php tpl_pageinfo() ?>
				</div>

                <?php tpl_flush() ?>
            </div></div><!-- /content -->

            <hr class="a11y" />

            <!-- PAGE ACTIONS -->
            <div id="dokuwiki__pagetools">
                <h3 class="a11y"><?php echo $lang['page_tools']; ?></h3>
                <div class="tools">
                    <ul>
                        <?php
							tpl_action('login', 	1, 'li', 0, '<span>', '</span>');
							tpl_action('register', 	1, 'li', 0, '<span>', '</span>');
						?>
						<li><br><hr></li>
						<?php if($INFO['writable'] && ($ACT === 'edit' || $ACT ==='preview') ):?>
						<li onmouseup="jQuery('#edbtn__save').click();">
                            <a href="#" class="action save"><span><?php echo $lang['btn_save']; ?></span></a>
                        </li>
						<li><br><hr></li>
						<?php endif;?>
						<?php 
							
                            tpl_action('edit',      1, 'li', 0, '<span>', '</span>');
							tpl_action('revert',    1, 'li', 0, '<span>', '</span>');
							tpl_action('top',       1, 'li', 0, '<span>', '</span>');
						?>
						
						<li onmouseup="window.print()">
                            <a href="" class="action print"><span><?php echo tpl_getLang('btn_print'); ?></span></a>
                        </li>
						
						<?php
                            tpl_action('subscribe', 1, 'li', 0, '<span>', '</span>');
                            tpl_action('revisions', 1, 'li', 0, '<span>', '</span>');
							tpl_action('backlink',  1, 'li', 0, '<span>', '</span>');
							
						?>
						<li><br><hr></li>
						<?php  
							tpl_action('admin', 	1, 'li', 0, '<span>', '</span>');
							tpl_action('profile', 	1, 'li', 0, '<span>', '</span>');
							tpl_action('recent', 	1, 'li', 0, '<span>', '</span>');
							tpl_action('media', 	1, 'li', 0, '<span>', '</span>');
							tpl_action('index', 	1, 'li', 0, '<span>', '</span>');
                        ?>
                    </ul>
                </div>
            </div>
        </div><!-- /wrapper -->

        <?php include('tpl_footer.php') ?>
    </div>	
	<div class="watermark">
		<?php 
		if(isset($_SERVER['REMOTE_USER'])) {
			echo 'gedruckt von '.hsc($INFO['userinfo']['name']) ;
		} else {
			echo 'oeffentlich';
		}
		 
		?>
	</div>
	<div class="watermark-confidential">
		<?php
            // get watermark either out of the template images folder or data/media folder
            $logoSize = array();
            $logo = tpl_getMediaFile(array(':wiki:watermark.png',':watermark.png', 'images/watermark.png'), false, $logoSize);
            echo '<img src="'.$logo.'" '.$logoSize[3].' /> ';

        ?>
	</div>
	</div><!-- /site -->

    <div class="no"><?php tpl_indexerWebBug() /* provide DokuWiki housekeeping, required in all templates */ ?></div>
    <div id="screen__mode" class="no"></div><?php /* helper to detect CSS media query in script.js */ ?>
    <!--[if ( lte IE 7 | IE 8 ) ]></div><![endif]-->
</body>
</html>
