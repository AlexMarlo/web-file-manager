<body>
  <h2>Welcome, <?php echo Yii::app()->user->name; ?>!</h2>

  <script type="text/javascript" language="javascript">
    function go(value, value2, value3, homeurl)
    {
      if(value3=="1")
      {
        $("#pan1").empty();
        $("#pan1").load(homeurl + "/file/manager?path=" + escape(value) + "&path2=" + escape(value2) + "&panel=1");
      }
      else if(value3 == "2")
      {
        $("#pan2").empty();
        $("#pan2").load(homeurl + "/file/manager?path=" + escape(value) + "&path2=" + escape(value2) + "&panel=2");
      }

      $('#rb1').val(escape(value + '/'));
      $('#rb2').val(escape(value2 + '/'));
    }

    function refresh(p, p2, homeurl)
    {
      go(p, p2, '1', homeurl);
      go(p, p2, '2', homeurl);
    }
  </script>

  <table id="main">
    <tbody>
      <td id="pan1" width="400" height="300">
        <?php
        $this->renderPartial('_manager', array('tree' => $tree, 'p' => $p, 'len' => $len, 'panel' => '1', 'p2' => $p2, ));
        ?>
      </td>
      <td id="pan2" width="400" height="300">
        <?php
        $this->renderPartial('_manager', array('tree' => $tree2, 'p' => $p2, 'len' => $len2, 'panel' => '2', 'p2' => $p, ));
        ?>
      </td>
    </tbody>
  </table>
  <?php if (!Yii::app()->user->isGuest): ?>
    <script type="text/javascript" language="javascript"> 
      function del(homeurl)
      {
        var del1=$(".work1", $(".trSelected").get(0)).text();
        var del2=$(".work2", $(".trSelected").get(0)).text();

        if((del1 == "..") || (del1 == "../"))
          exit;

        if((del1 == ".") || (del1 == "./"))
          exit;

        if((del2 == "..") || (del2 == "../"))
          exit;

        if((del2 == ".") || (del2 == "./"))
          exit;

        var pd = $("#p1").val();
        var pd2 = $("#p2").val();
        
        if(del1 != '')
        {
            del1 = pd + '/' + del1;
            $.get(homeurl + "/file/delete", {path: escape(del1)},function(xml){refresh(pd, pd2, homeurl);});
        }
        else
          del1 = pd + '/' + del1;

        if(del2!='')
        {
            del2 = pd2 + '/' +del2;
            $.get(homeurl + "/file/delete", {path: escape(del2)}, function(xml){refresh(pd, pd2, homeurl);});
        }
        else
          del2 = pd2 + '/' + del2;
      }

      function cop(homeurl)
      {
        var pc = $("#p1").val();
        var pc2 = $("#p2").val();
        if(pc == pc2)
          exit;

        var cop1 = $(".work1", $(".trSelected").get(0)).text();
        var cop2 = $(".work2", $(".trSelected").get(0)).text();

        if((cop1 == "..")||(cop1 == "../"))
          exit;

        if((cop2 == "..")||(cop2 == "../"))
          exit;

        if((cop1 == ".")||(cop1 == "./"))
          exit;

        if((cop2 == ".")||(cop2 == "./"))
          exit;

        if((cop2=='')&&(cop1!=''))
        {
          cop2 = pc2 + '/' + cop1;
          cop1 = pc + '/' + cop1;
          $.get(homeurl+"/file/copy", {path: escape(cop1), pathTo: escape(cop2)}, function(xml){refresh(pc, pc2, homeurl);});
        }

        if((cop1 == '')&&(cop2 != ''))
        {
          cop1 = pc + '/' + cop2;
          cop2 = pc2 + '/' + cop2;
          $.get(homeurl + "/file/copy", {path: escape(cop2), pathTo: escape(cop1)}, function(xml){refresh(pc, pc2, homeurl)});
        }
      }

      function mov(homeurl)
      {
        var pm = $("#p1").val();
        var pm2 = $("#p2").val();
        if(pm == pm2)
          exit;
        
        var mov1 = $(".work1", $(".trSelected").get(0)).text();
        var mov2 = $(".work2", $(".trSelected").get(0)).text();
        
        if((mov1 == "..") || (mov1 == "../"))
          exit;

        if((mov2 == "..") || (mov2 == "../"))
          exit;

        if((mov1 == ".") || (mov1 == "./"))
          exit;

        if((mov2 == ".") || (mov2 == "./"))
          exit;

       
        if((mov2=='')&&(mov1!=''))
        {
          mov2 = pm2 + '/' + mov1;
          mov1 = pm + '/' + mov1;
          $.get(homeurl + "/file/move", {path: escape(mov1), pathTo:escape(mov2)}, function(xml){refresh(pm, pm2, homeurl)});
        }

        if((mov1 == '') && (mov2 != ''))
        {
          mov1 = pm + '/' + mov2;
          mov2 = pm2 + '/' + mov2;
          $.get(homeurl+"/file/move", {path: escape(mov2), pathTo: escape(mov1)}, function(xml){refresh(pm, pm2, homeurl)});
        }
      }

      function mkdir(evt,homeurl)
      {
        var mkfd1 = $("#mkfd1").val();
        var mkfd2 = $("#mkfd2").val();

        var pmkd = $("#p1").val();
        var pmkd2 = $("#p2").val();

        if(evt == 1 && mkfd1 != '')
        {
          mkfd1 = pmkd + '/' + mkfd1;
          $.get(homeurl + "/file/mkdir", {path: escape(mkfd1)}, function(xml){refresh(pmkd, pmkd2, homeurl)});
        };

        if(evt == 2 && mkfd2 != '')
        {
          mkfd2 = pmkd2 + '/' + mkfd2;
          $.get(homeurl+"/file/mkdir",{path: escape(mkfd2)}, function(xml){refresh(pmkd, pmkd2, homeurl)});
        }
      }

      function mkfile(evt, homeurl)
      {
        var mkfd1 = $("#mkfd1").val();
        var mkfd2 = $("#mkfd2").val();
            
        var pmkf = $("#p1").val();
        var pmkf2 = $("#p2").val();
        
        if(evt == 1 && mkfd1 != '')
        {
          mkfd1 = pmkf + '/' + mkfd1;
          $.get(homeurl + "/file/mkfile", {path: escape(mkfd1)}, function(xml){refresh(pmkf, pmkf2, homeurl)});
        }

        if(evt==2 && mkfd2!='')
        {
          mkfd2 = pmkf2 + '/' + mkfd2;
          $.get(homeurl + "/file/mkfile", {path: escape(mkfd2)}, function(xml){refresh(pmkf, pmkf2, homeurl)});
        }
      }

      function dow(homeurl)
      {
        var pd = $("#p1").val();
        var pd2 = $("#p2").val();

        var dow1 = $(".work1", $(".trSelected").get(0)).text();
        var dow2 = $(".work2", $(".trSelected").get(0)).text();

        if((dow1 == "..") || (dow1 == "../"))
          exit;

        if((dow2 == "..") || (dow2 == "../"))
          exit;

        if((dow1 == ".") || (dow1 == "./"))
          exit;

        if((dow2 == ".") || (dow2 == "./"))
          exit;

        if((dow2 == '') && (dow1 != ''))
        {
          dow1 = pd + '/' + dow1;
          location.href = homeurl + "/file/download?path=" + escape(dow1);
        }

        if((dow1 == '') && (dow2 != ''))
        {
          dow2 = pd2 + '/' + dow2;
          location.href = homeurl + "/file/download?path=" + escape(dow2);
        }
      }
    </script>

    <h3> Chuse action with first selected object:</h3>
    <table id="opMenu" width="200">
      <tbody>
        <td id="del" width="50"><button onclick="del('<?php echo Yii::app()->homeUrl; ?>');">Delete selected</button></td>
        <td id="cop" width="50"><button onclick="cop('<?php echo Yii::app()->homeUrl; ?>');">Copy selected</button></td>
        <td id="mov" width="50"><button onclick="mov('<?php echo Yii::app()->homeUrl; ?>');">Move selected</button></td>
        <td id="dow" width="50"><button onclick="dow('<?php echo Yii::app()->homeUrl; ?>');">Download selected</button></td>
      </tbody>
    </table>

    <table id="mkfdir">
      <tbody>
        <td width="400" align="left"><b>make file or dir in FIRST panel folder:</b><br>
          <input id="mkfd1" type="text" name="newd" size="20" maxlength="20">
          <input type="button" value="file" class="but" onclick="mkfile(1,'<?php echo Yii::app()->homeUrl; ?>')">
          <input type="button" value="dir" class="but" onclick="mkdir(1,'<?php echo Yii::app()->homeUrl; ?>')">
        </td>
        <td width="400" align="left"><b>make file or dir in SECOND panel folder:</b><br>
          <input id="mkfd2" type="text" name="newd" size="20" maxlength="20">
          <input type="button" value="file" class="but" onclick="mkfile(2,'<?php echo Yii::app()->homeUrl; ?>')">
          <input type="button" value="dir" class="but" onclick="mkdir(2,'<?php echo Yii::app()->homeUrl; ?>')">
        </td>
      </tbody>
    </table><br>

    <script type="text/javascript">
      $(document).ready(function(){
        $('.MultiFile').MultiFile({ 
            accept:'jpg|gif|bmp|png|doc|pdf|rtf|avi|mp3|wav|txt|docx|rar|zip|xml|docx', max:15, STRING: { 
            remove:'delete',
            file:'$file', 
            selected:'Chuse: $file', 
            denied:'not supported file format: $ext!', 
            duplicate:'duplicate file:\n$file!' 
          }
        });

        $("#loading").ajaxStart(function(){
          $(this).show();
        })
        .ajaxComplete(function(){
          $(this).hide();
        });

        $('#uploadForm').ajaxForm({
          beforeSubmit: function(a,f,o)
          {
            o.dataType = "html";
            //$('#uploadOutput').html('Submitting...');
          },
          success: function(data) {
            //var $out = $('#uploadOutput');
            //$out.html('Form success handler received: <strong>' + typeof data + '</strong>');
            if (typeof data == 'object' && data.nodeType)
              data = elementToString(data.documentElement, true);
            else if (typeof data == 'object')
              data = objToString(data);
            //$out.append('<div><pre>'+ data +'</pre></div>');
                var pdown =$("#p1").val();
                var pdown2=$("#p2").val();
                refresh(pdown,pdown2,$("#homeUrl").val());
          }
        });
      });
    </script>

    <form id="uploadForm" action="<?php echo Yii::app()->homeUrl; ?>/file/upload" method="post" enctype="multipart/form-data">
      <input name="MAX_FILE_SIZE" value="1000000" type="hidden"/>
      Upload files: <input name="fileToUpload[]" id="fileToUpload" class="MultiFile" type="file" />
      <input id="rb1" type="radio" name="answer" value="/" checked>Left panel
      <input id="rb2" type="radio" name="answer" value="/">Right panel<br />
      <input value="Upload" type="submit"/>
    </form>

    <img id="loading" src="<?php echo Yii::app()->request->baseUrl; ?>/loading.gif" style="display:none;"/>
    <div id="uploadOutput"></div>
    <input id="homeUrl" value="<?php echo Yii::app()->homeUrl; ?>" type="hidden"/>
  <?php endif; ?>

</body>
