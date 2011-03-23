<p></p>
<?php
    $lroot=substr_replace($p,"",0,$len);
    $lroot=str_replace("\\","/",$lroot);

    $lroot2=substr_replace($p2,"",0,$len);
    $lroot2=str_replace("\\","/",$lroot2);
        
    if($panel=='1')
    {
      echo(" <input id=\"p1\" value=\"$lroot\" type=\"hidden\"/>");
      echo("<table id=\"stats\" class=\"stats\">");
    }
    else
    {
      if($panel=='2')
      {
        echo(" <input id=\"p2\" value=\"$lroot\" type=\"hidden\"/>");
        echo("<table id=\"stats2\" class=\"stats\">");
      }
    }    
?>
  <tbody>
  <thead>    
    <tr>
      <th width="130">Name</th>
      <th width="50">Size</th>
      <th width="20">Type</th>
            <th width="100">Date last change</th>
    </tr>
  </thead>   
<?php
    foreach($tree as $i){
    $tmp=$p.'\\'.$i;
    
    if($panel=='1')
    {
      echo ("<tr id=\"df\" ondblclick=\"go('$lroot/$i','$lroot2',$panel,'".Yii::app()->homeUrl."');\">");
      echo ("<td id=\"work\" class=\"work1\">$i</td>");
    }
    else
    {
      if($panel=='2')
      {
        echo ("<tr ondblclick=\"return go('$lroot2','$lroot/$i',$panel,'".Yii::app()->homeUrl."');\">");
        echo ("<td  id=\"work\" class=\"work2\">$i</td>");
      }
    }

    if($i!='..' && $i!='.')
    {
      $fS = stat($tmp);
      if (!is_dir($tmp))
      {
          
          echo("<td>$fS[size] byte</td>");
      }
      else
      {
          echo ("<td></td>");
      }

      if (is_dir($tmp)){echo ("<td>Dir</td>");};
      if (is_file($tmp)){echo ("<td id=\"fil\">File</td>");};
      $t=getdate($fS["mtime"]);
      echo ("<td>".date("d.m.Y H:i:s.",filemtime($tmp))."</td>");
    }
    else
    {
      echo ("<td></td>");
      echo ("<td></td>");
      echo ("<td></td>");
    }

      echo ('</tr>');
    }
?>
  </tbody></table>
<script type="text/javascript">
  $('.stats').flexigrid();    
</script>    
    
