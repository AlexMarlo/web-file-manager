<body>
	<script type="text/javascript" language="javascript"> 
        $(document).ready(function(){
                $("#loading").ajaxStart(function(){
                $(this).show();
                })
                .ajaxComplete(function(){
                $(this).hide();});
            }
        );    		
		function go(homeurl){
            //alert(homeurl);
            $("#rootdir").empty();
            $("#rootdir").load(homeurl+"/options/setrootdir?newrootdir="+escape($("#newrootdir").val()));
        };
	</script> 

<h2>
	Welcome, <?php echo Yii::app()->user->name; ?>!
</h2>
<div id="rootdir">
<?php $this->renderPartial('_options', array('rootdir' => $rootdir));?>
</div>
<br/>
    	<input id="newrootdir" name="newrootdir" value="" type="text"/>
        <button onclick="go('<?php echo Yii::app()->homeUrl; ?>')">Set new root directory</button>
        <img id="loading" src="<?php echo Yii::app()->request->baseUrl; ?>/loading.gif" style="display:none;"/>   

<input id="homeUrl" value="<?php echo Yii::app()->homeUrl; ?>" type="hidden"/> 
</body>