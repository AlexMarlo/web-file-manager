<?php
class FileController extends CController
{
  public $defaultAction='manager';

  public function filters()
  {
    return array(
      'accessControl',
    );
  }

  public function accessRules()
  {
    return array(
      array('allow',
        'actions'=>array('*'),
        'users'=>array('admin'),
      ),
      array('allow',
        'actions'=>array('manager'),
        'users'=>array('guest'),
      ),            
      array('deny',  // deny all users
        'users'=>array('guest'),
      ),
    );
  }

  private function unicodeUrlDecode($url, $encoding = "")
  {
    $encoding = 'CP1251';
    preg_match_all('/%u([[:xdigit:]]{4})/', $url, $a);

    foreach ($a[1] as $unicode)
    {
      $num = hexdec($unicode);
      $str = '';// UTF-16(32) number to UTF-8 string
      if ($num < 0x80)
      $str = chr($num);
      else if ($num < 0x800)
      $str = chr(0xc0 | (($num & 0x7c0) >> 6)) .
      chr(0x80 | ($num & 0x3f));
      else if ($num < 0x10000)
      $str = chr(0xe0 | (($num & 0xf000) >> 12)) .
      chr(0x80 | (($num & 0xfc0) >> 6)) .
      chr(0x80 | ($num & 0x3f));
      else
      $str = chr(0xf0 | (($num & 0x1c0000) >> 18)) .
      chr(0x80 | (($num & 0x3f000) >> 12)) .
      chr(0x80 | (($num & 0xfc0) >> 6)) .
      chr(0x80 | ($num & 0x3f));

      $str = iconv ("UTF-8", "$encoding", $str);
      $url = str_replace ('%u'.$unicode, $str, $url);
    }

    return urldecode ($url);
  }

  public function ajaxout($content)
  {
    return unhtmlentities(htmlentities($content,0,"windows-1251"));
  }

  public function unhtmlentities ($string)
  {
    $trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl = array_flip ($trans_tbl);
    return strtr ($string, $trans_tbl);
  }

  private function zipping($archive,$fPath)
  {
    $arch= new ZipArchive;
    $arch->open($Archive,ZipArchive::CREATE);
    $this->ItemInZip($arch,realpath(dirname($fPath)).'/',realpath($fPath).'/');
    $arch->close();
    /*
    $cmd = "`which zip` -P {$password} {$destFile} {$sourceFile}";
    exec($cmd);
    */
    return true;
  }

  private function itemInZip($arch,$fPath,$fInZip)
  {
    if(is_file($fInZip))
    {
      $arch->addFile($fInZip,str_replace($fPath, '', $fInZip));
    }
    elseif(is_dir($fInZip))
    {
      //$arch->addEmptyDir($FPath);
      if ($fPath[1]<>':')
        $arch->addEmptyDir(str_replace(dirname($fPath), '', $fPath));

      $scr = scandir($fInZip);
      array_shift($scr);
      array_shift($scr);

      foreach($scr as $i)
        $this->itemInZip($arch, $fPath, $fInZip . $i . (is_dir($fInZip.$i)? '/': ''));
    }
  }

  private function getRoot()
  {
    $sql = 'SELECT rootdir FROM rootdir;';

    $command = Yii :: app()->db->createCommand($sql);
    $dataReader = $command->query();

    foreach($dataReader as $row)
      $rd = "$row[rootdir]";

    if(file_exists($rd))
      return realpath($rd);
    else
      return dirname(__FILE__) . '/../../user_work_directories/default';
  }

  private function makeFile($fName)
  {
    if(!file_exists($fName))
    {
      $this->file = fopen($fName, 'w+');
      fclose($this->file);
    }
  }

  private function makeDir($dirName)
  {
    mkdir($dirName,0777);
  }

  private function copy($what,$where)
  {
    if(realpath($what)==realpath($this->getRoot())||realpath($what)==realpath(dirname(__FILE__).'\..\..\workDir\\'))
      exit;

    if(is_file($what) && !copy($what,$where))
      return(1);

    if(is_dir($what))
    {
      mkdir($where);
      $scr=scandir($what);
      array_shift($scr);
      array_shift($scr);
      foreach($scr as $i)
        $this->copy($what.'\\'.$i,$where.'\\'.$i);
    }

    return(0);
  }

  private function move($what,$where)
  {
    if(realpath($what)==realpath($this->getRoot())||realpath($what)==realpath(dirname(__FILE__).'\..\..\workDir\\'))
      exit;

    $this->copy($what,$where);
    $this->delete($what);
  }

  private function delete($what)
  {
    if(realpath($what)==realpath($this->getRoot())||realpath($what)==realpath(dirname(__FILE__).'\..\..\workDir\\'))
      exit;

    if(is_file($what) && !unlink($what))
      return(1);

    if(is_dir($what))
    {
      $scr=scandir($what);
      array_shift($scr);
      array_shift($scr);
      foreach($scr as $i)
        $this->delete($what.'\\'.$i);

      rmdir($what);
    }

    return(0);
  }

  private function download($what)
  {
    if(is_file($what))
    {
      header("Content-Length: ".filesize($what));
      header("Content-Disposition: attachment; filename=".$what); 
      header("Content-Type: application/x-force-download; name=\"".$what."\"");
      readfile($what);
    }
    else
    {
      if(is_dir($what))
      {
        $this->zipping($what.".zip",$what);
        //$pathToDownload=realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_POST['answer']));

        header("Content-Length: ".filesize($what.".zip"));
        header("Content-Disposition: attachment; filename=".$what.".zip"); 
        header("Content-Type: application/x-force-download; name=\"".$what.".zip"."\"");
        readfile($what.".zip");
        $this->delete($what.".zip");
      }
    }
  }

  private function dir($what)
  {
    $scr=scandir($what);
    //array_shift($scr);
    //array_shift($scr);
    return($scr);
  }

  public function actionManager()
  {
    $tmp = $this->getRoot();

    if (!isset($_GET['path']))
      $scandir=$tmp;
    else
    {
      //$scandir=iconv("UTF-8", "windows-1251", $_GET['path']);
      $scandir=$_GET['path'];
      $scandir=$this->unicodeUrlDecode($scandir);
      $scandir=$tmp.$scandir;
      $scandir=str_replace("/","\\",$scandir);
    }

    if (!isset($_GET['path2']))
      $scandir2=$tmp;
    else
    {
      $scandir2=$_GET['path2'];
      $scandir2=$this->unicodeUrlDecode($scandir2);
      $scandir2 = $tmp.$scandir2;
      $scandir2 = str_replace("/","\\",$scandir2);
    }

    $scandir = str_replace("\\", DIRECTORY_SEPARATOR, $scandir);
    $scandir2 = str_replace("\\", DIRECTORY_SEPARATOR, $scandir2);
    $tmp = str_replace("\\", DIRECTORY_SEPARATOR, $tmp);

    $scandir = realpath($scandir); 
    $scandir2 = realpath($scandir2);

    if(substr_count($scandir, $tmp) < 1)
      $scandir = $tmp;

    if(substr_count($scandir2, $tmp) < 1)
      $scandir2 = $tmp;

    $tree = $this->dir($scandir);
    $tree2 = $this->dir($scandir2);


    if(in_array('XMLHttpRequest', $_SERVER))
    {
      if($_GET['panel']=='1')
      {
        $this->renderPartial('_manager',array( 'tree'=>$tree,'p'=>$scandir,'len'=>strlen($tmp),'panel'=>'1','p2'=>$scandir2,));
      }
      else
      {
        if($_GET['panel']=='2')
        {
          $this->renderPartial('_manager',array( 'tree'=>$tree2,'p'=>$scandir2,'len'=>strlen($tmp),'panel'=>'2','p2'=>$scandir,));
        }
      }
    }
    else
    {
      $this->render('manager', array('tree' => $tree, 'p' => $scandir, 'len' => strlen($tmp),
                    'tree2' => $tree2,'p2' => $scandir2,'len2' => strlen($tmp), ));
    }
  }

    public function actionDelete()
    {
      if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
      {
        if($this->unicodeUrlDecode($_GET['path'])==''){exit;};            
        $this->delete(realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['path'])));
      }
    }

    public function actionCopy()
    {
      if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
      {
        $this->copy(
            realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['path'])),
            realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['pathTo'])));
      }
    }

    public function actionMkDir()
    {
      if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
      {
        if($this->unicodeUrlDecode($_GET['path'])=='')
          exit;

        $this->makeDir(realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['path'])));
      }
    }

    public function actionMkFile()
    {
      if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
      {
          if($this->unicodeUrlDecode($_GET['path'])=='')
            exit;

          $this->makeFile(realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['path'])));
      }
    }

    public function actionMove()
    {
      if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
      {
        $this->move(
            realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['path'])),
            realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['pathTo'])));
      }
    }

    public function actionUpload()
    {
      //if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
      {
        $pathToUpload=realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_POST['answer']));
        $error = "";
        $msg = "";
        $fileElementName = 'fileToUpload';
        $i = 0;
        $files_count = sizeof($_FILES[$fileElementName]["name"]);

        for ($i = 0; $i < $files_count-1; $i++)
        {
          if(!empty($_FILES[$fileElementName]['error'][$i]))
          {
            switch($_FILES[$fileElementName]['error'][$i])
            {
              case '1':
                  $error = 'size of uploaded file exceeds the specified parameter upload_max_filesize  in php.ini ';
                  break;
              case '2':
                  $error = 'size of uploaded file exceeds the specified parameter MAX_FILE_SIZE in HTML form ';
                  break;
              case '3':
                  $error = 'loaded only part of the file';
                  break;
              case '4':
                  $error = 'file was not loaded (in the form of user entered an incorrect path). ';
                  break;
              case '6':
                  $error = 'Bad temporary a directory';
                  break;
              case '7':
                  $error = 'error writing the file to disk';
                  break;
              case '8':
                  $error = 'File Download interrupted';
                  break;
              case '999':
              default:
                  $error = 'No error code avaiable';
            }
          }
          elseif(empty($_FILES[$fileElementName]['tmp_name'][$i]) || $_FILES[$fileElementName]['tmp_name'][$i] == 'none')
            $error = 'No file was uploaded..';
          else
          {
            if (file_exists($pathToUpload . $_FILES[$fileElementName]['name'][$i]))
              $error =$_FILES[$fileElementName]['name'][$i] . " óæå ñóùåñòâóåò. ";
            else
            {
              $msg .= " File Name: " . $_FILES[$fileElementName]['name'][$i]."<br/>";
              $msg .= " File Type: " . $_FILES[$fileElementName]['type'][$i]."<br/>";
              $msg .= " File Size: " . (@filesize($_FILES[$fileElementName]['tmp_name'][$i])/ 1024)."Kb";
              move_uploaded_file($_FILES[$fileElementName]['tmp_name'][$i], $pathToUpload . $_FILES[$fileElementName]['name'][$i]);
            }
            //for security reason, we force to remove all uploaded file
            @unlink($_FILES[$fileElementName][$i]);
          }
          //echo "{";
          //echo($error . "',\n");
          //echo  "msg: '" . $msg . "'\n";
          //echo "}";
        }//end cicle copy files
        //if($this->unicodeUrlDecode($_GET['path'])==''){exit;};            
        //$this->download(realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['path'])));
      }
  }
    
  public function actionDownload()
  {
  //if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
    if($this->unicodeUrlDecode($_GET['path'])=='')
      exit;

    $this->download(realpath($this->getRoot()).str_replace("/","\\",$this->unicodeUrlDecode($_GET['path'])));
  }
}
?>
