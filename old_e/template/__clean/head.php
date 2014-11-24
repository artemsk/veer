<? // <html>? ?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>{TITLE}</title>
<meta name="keywords" content="{KWORDS}" />
<meta name="description" content="{DESCRIPTIONS}" />
<link rel='stylesheet' type='text/css' href='{TEMPL_PATH}/css.css' />
<!--[if IE]>
<link rel='stylesheet' type='text/css' href='{TEMPL_PATH}/css_ie.css' />
<![endif]-->
<!--<link rel="search" href="" title="" type="application/opensearchdescription+xml" />-->
<link rel="shortcut icon" href="{IMG_PATH}/favicon.ico" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>
	!window.jQuery && document.write('<script src="{EXTPTH}/fancybox/jquery-1.4.3.min.js"><\/script>');
</script>
<script type="text/javascript">
        function show(elementname)
        {
            document.getElementById(elementname).style.display='block';
        }

        function hide(elementname)
        {
            document.getElementById(elementname).style.display='none';
        }

        function clearText(thefield){
            if (thefield.defaultValue==thefield.value)
                thefield.value = ""
        }

    function jumpToAnchor() {
   window.location = String(window.location).replace(/\#.*$/, "") + "#catlist";
}

    $.ajaxSetup ({ cache: false });

</script>
{TRACKING}{HEAD_CONTAINER}
</head>