<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta charset="utf-8" />
        <title>Dashboard - PantauAsap</title>

        <meta name="description" content="overview &amp; stats" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <!-- bootstrap & fontawesome -->
        <?php echo css_asset('bootstrap.css', 'ace'); ?>
        <?php echo css_asset('font-awesome.css', 'ace'); ?>

        <!-- page specific plugin styles -->

        <!-- text fonts -->
        <?php echo css_asset('ace-fonts.css', 'ace'); ?>

        <!-- ace styles -->
        <?php echo css_asset('ace.css', 'ace', array('class' => "ace-main-stylesheet", 'id' => "main-ace-style")); ?>

        <!--[if lte IE 9]>
        <?php echo css_asset('ace-part2.css', 'ace', array('class' => "ace-main-stylesheet")); ?>
        <![endif]-->

        <!--[if lte IE 9]>
        <?php echo css_asset('ace-ie.css', 'ace'); ?>
        <![endif]-->
        <!-- inline styles related to this page -->

        <!--[if !IE]> -->
        <script type="text/javascript">
            window.jQuery || document.write("<script src='<?php echo js_asset_url('jquery.js', 'ace'); ?>'>" + "<" + "/script>");
            var base_url = '<?php echo base_url(); ?>';</script>

        <!-- ace settings handler -->
        <?php echo js_asset('ace-extra.js', 'ace'); ?>

        <!-- HTML5shiv and Respond.js for IE8 to support HTML5 elements and media queries -->

        <!--[if lte IE 8]>
        <?php echo js_asset('html5shiv.js', 'ace'); ?>
        <?php echo js_asset('respond.js', 'ace'); ?>
        <![endif]-->
    </head>

    <body class="no-skin">
        <!-- #section:basics/navbar.layout -->
        <div id="navbar" class="navbar navbar-default">
            <script type="text/javascript">
                try {
                    ace.settings.check('navbar', 'fixed')
                } catch (e) {
                }
            </script>

            <div class="navbar-container" id="navbar-container">
                <!-- #section:basics/sidebar.mobile.toggle -->
                <button type="button" class="navbar-toggle menu-toggler pull-left" id="menu-toggler" data-target="#sidebar">
                    <span class="sr-only">Toggle sidebar</span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>
                </button>

                <!-- /section:basics/sidebar.mobile.toggle -->
                <div class="navbar-header pull-left">
                    <!-- #section:basics/navbar.layout.brand -->
                    <a href="#" class="navbar-brand">
                        <small>
                            <i class="fa fa-cloud"></i>
                            Pantau Asap
                        </small>
                    </a>

                    <!-- /section:basics/navbar.layout.brand -->

                    <!-- #section:basics/navbar.toggle -->

                    <!-- /section:basics/navbar.toggle -->
                </div>               

                <!-- /section:basics/navbar.dropdown -->
            </div><!-- /.navbar-container -->
        </div>

        <!-- /section:basics/navbar.layout -->
        <div class="main-container" id="main-container">
            <script type="text/javascript">
                try {
                    ace.settings.check('main-container', 'fixed')
                } catch (e) {
                }
            </script>

            <!-- #section:basics/sidebar -->
            <div id="sidebar" class="sidebar responsive">
                <script type="text/javascript">
                    try {
                        ace.settings.check('sidebar', 'fixed')
                    } catch (e) {
                    }
                </script>

                <div class="sidebar-shortcuts" id="sidebar-shortcuts">

                </div><!-- /.sidebar-shortcuts -->

                <ul class="nav nav-list">
                    <li class="<?php if ($page == 1) echo 'active'; ?>">
                        <a href="<?php echo site_url(); ?>">
                            <i class="menu-icon fa fa-map-o"></i>
                            <span class="menu-text"> Map </span>
                        </a>

                        <b class="arrow"></b>
                    </li>
                    
                    <li class="<?php if ($page == 2) echo 'active'; ?>">
                        <a href="<?php echo site_url('history'); ?>">
                            <i class="menu-icon fa fa-history"></i>
                            <span class="menu-text"> History </span>
                        </a>

                        <b class="arrow"></b>
                    </li>

                    <li class="<?php if ($page == 3) echo 'active'; ?>">
                        <a href="<?php echo site_url('about'); ?>">
                            <i class="menu-icon fa fa-tag"></i>
                            <span class="menu-text"> About </span>
                        </a>

                        <b class="arrow"></b>
                    </li>
                </ul><!-- /.nav-list -->

                <!-- #section:basics/sidebar.layout.minimize -->
                <div class="sidebar-toggle sidebar-collapse" id="sidebar-collapse">
                    <i class="ace-icon fa fa-angle-double-left" data-icon1="ace-icon fa fa-angle-double-left" data-icon2="ace-icon fa fa-angle-double-right"></i>
                </div>

                <!-- /section:basics/sidebar.layout.minimize -->
                <script type="text/javascript">
                    try {
                        ace.settings.check('sidebar', 'collapsed')
                    } catch (e) {
                    }
                </script>
            </div>

            <!-- /section:basics/sidebar -->
            <div class="main-content">
                <div class="main-content-inner">                    
                    <div class="page-content">
                        <!-- #section:settings.box -->

                        <!-- /section:settings.box -->

                        <div class="row">
                            <div class="col-xs-12">
                                <!-- PAGE CONTENT BEGINS -->
                                <?php echo $_content; ?>
                                <!-- PAGE CONTENT ENDS -->
                            </div><!-- /.col -->
                        </div><!-- /.row -->
                    </div><!-- /.page-content -->
                </div>
            </div><!-- /.main-content -->

            <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
                <i class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
            </a>
        </div><!-- /.main-container -->

        <!-- basic scripts -->



        <!-- <![endif]-->

        <!--[if IE]>
<script type="text/javascript">
window.jQuery || document.write("<script src='../assets/js/jquery1x.js'>"+"<"+"/script>");
</script>
<![endif]-->
        <script type="text/javascript">
            if ('ontouchstart' in document.documentElement)
                document.write("<script src='../assets/js/jquery.mobile.custom.js'>" + "<" + "/script>");</script>
        <?php echo js_asset('bootstrap.js', 'ace'); ?>

        <!-- page specific plugin scripts -->


        <?php echo js_asset('jquery-ui.custom.js', 'ace'); ?>
        <?php echo js_asset('jquery.ui.touch-punch.js', 'ace'); ?>

        <!-- ace scripts -->
        <?php echo js_asset('ace/elements.scroller.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.colorpicker.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.fileinput.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.typeahead.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.wysiwyg.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.spinner.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.treeview.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.wizard.js', 'ace'); ?>
        <?php echo js_asset('ace/elements.aside.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.ajax-content.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.touch-drag.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.sidebar.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.sidebar-scroll-1.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.submenu-hover.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.widget-box.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.settings.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.settings-rtl.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.settings-skin.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.widget-on-reload.js', 'ace'); ?>
        <?php echo js_asset('ace/ace.searchbox-autocomplete.js', 'ace'); ?>
    </body>
</html>
