<HTML>
<HEAD>
<TITLE><?= $_GET['title'] ?></TITLE>
<link rel="stylesheet" href="../css/main.css" type="text/css">
<link rel="stylesheet" href="../css/dark-hive/jquery-ui-1.8.14.custom.css" type="text/css">

<script src="../js/jquery-1.5.1.min.js" type="text/javascript"></script>
<script src="../js/jquery-ui-1.8.14.custom.min.js" type="text/javascript"></script>
<script src="../js/jquery-ui-global.js" type="text/javascript"></script>
</HEAD>
<BODY>

<script type="text/javascript">
$(document).ready(function(){
    $("#tutorials_menu").hover(
    function () {
        $(this).animate({
            height: "240px"
        }, 500 );
    }, 
    function () {
        $(this).animate({
            height: "35px"
        }, 500 );
    });
});
</script>

<div id="header">
    <div style="position:relative; float:left; padding:10px; font-size:16px;"><b><?= $_GET['title'] ?></b> [<a href="javascript:window.close()">close window</a>]</div>
    
    <div id="tutorials_menu">
        <div class="icon"><img src="../images/info_icon.png" width="25" height="25" alt="Tutorials" border="0"></div>
        <div class="title"><span style="font-size:14px;"><b>Show tutorial menu</b></span></div>
        
        <div class="links">
            <b>Data collection</b>
            <ol>
                <li><a href="index.php?title=Tutorial 1: Starting a new project&filename=starting_a_new_project">Starting a new project</a></a></li>
                <li><a href="index.php?title=Tutorial 2: Modifying an existing project&filename=modifying_an_existing_project">Modifying an existing project</a></li>
                <li><a href="index.php?title=Tutorial 3: Data collection log&filename=data_collection_log">Data collection log</a></li>
            </ol>
            <b>Data analysis</b>
            <ol start="4">
                <li><a href="index.php?title=Tutorial 4: Basic data analysis&filename=basic_results_view">Basic</a></li>
                <li><a href="index.php?title=Tutorial 5: Clustering and data relevancy optimization&filename=clustering_and_data_relevancy_optimization">Clustering and data relevancy optimization</a></li>
                <li><a href="index.php?title=Tutorial 6: Trendline&filename=trendline">Trendline</a></li>
            </ol>
        </div>
        <div style="clear:both;"></div>
    </div>

</div>


<div id="container">
    <div style="z-index:1; position:absolute; top:37px; width:962px; height:566px; border:2px solid #000;">
    <OBJECT CLASSID="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" WIDTH="962" HEIGHT="566" CODEBASE="http://active.macromedia.com/flash5/cabs/swflash.cab#version=7,0,0,0">
    <PARAM NAME=movie VALUE="<?= $_GET['filename'] ?>.swf">
    <PARAM NAME=play VALUE=true>
    <PARAM NAME=loop VALUE=false>
    <PARAM NAME=wmode VALUE=transparent>
    <PARAM NAME=quality VALUE=low>
    <EMBED SRC="<?= $_GET['filename'] ?>.swf" WIDTH=962 HEIGHT=566 quality=low loop=false wmode=transparent TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash">
    </EMBED>
    </OBJECT></center>
    <SCRIPT src='<?= $_GET['filename'] ?>.js'></script>
    
    </div>

</div>

</BODY>
</HTML>
