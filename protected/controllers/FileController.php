<?php

Yii::import('application.components.service.FSService');

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

  public function actionManager()
  {
    $tmp = $this->getRoot();

    if (!isset($_GET['path']))
      $scandir = $tmp;
    else
    {
      //$scandir=iconv("UTF-8", "windows-1251", $_GET['path']);
      $scandir = $_GET['path'];
      $scandir = FSService::unicodeUrlDecode($scandir);
      $scandir = $tmp.$scandir;
      //$scandir = str_replace( "/", DIRECTORY_SEPARATOR, $scandir);
    }

    if (!isset( $_GET['path2']))
      $scandir2 = $tmp;
    else
    {
      $scandir2 = $_GET['path2'];
      $scandir2 = FSService::unicodeUrlDecode($scandir2);
      $scandir2 = $tmp . $scandir2;
      //$scandir2 = str_replace( "/", DIRECTORY_SEPARATOR,$scandir2);
    }

    $scandir = str_replace( "\\", DIRECTORY_SEPARATOR, $scandir);
    $scandir2 = str_replace( "\\", DIRECTORY_SEPARATOR, $scandir2);
    $tmp = str_replace( "\\", DIRECTORY_SEPARATOR, $tmp);

    $scandir = realpath($scandir); 
    $scandir2 = realpath($scandir2);

    if( substr_count( $scandir, $tmp) < 1)
      $scandir = $tmp;

    if( substr_count( $scandir2, $tmp) < 1)
      $scandir2 = $tmp;

    $tree = FSService::dir( $scandir);
    $tree2 = FSService::dir( $scandir2);


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
      if( FSService::unicodeUrlDecode($_GET['path'])=='')
        exit;
      FSService::delete( realpath($this->getRoot()).str_replace( "/", DIRECTORY_SEPARATOR, FSService::unicodeUrlDecode( $_GET['path'])));
    }

    public function actionCopy()
    {
      FSService::copy(
            realpath($this->getRoot()).str_replace( "/", DIRECTORY_SEPARATOR, FSService::unicodeUrlDecode( $_GET['path'])),
            realpath($this->getRoot()).str_replace( "/", DIRECTORY_SEPARATOR, FSService::unicodeUrlDecode( $_GET['pathTo']))
          );
    }

    public function actionMkDir()
    {
      if( trim($_GET['path']) == '')
        exit;

      FSService::makeDir( $_GET['path']);
    }

    public function actionMkFile()
    {
      if( FSService::unicodeUrlDecode( $_GET['path']) == '')
        exit;

      FSService::makeFile( realpath( $this->getRoot()) . str_replace( "/", DIRECTORY_SEPARATOR, FSService::unicodeUrlDecode($_GET['path'])));
    }

    public function actionMove()
    {
      FSService::move(
          realpath( $this->getRoot()) . str_replace( "/", DIRECTORY_SEPARATOR, FSService::unicodeUrlDecode($_GET['path'])),
          realpath( $this->getRoot()) . str_replace( "/", DIRECTORY_SEPARATOR, FSService::unicodeUrlDecode($_GET['pathTo']))
        );
    }

    public function actionUpload()
    {
        $pathToUpload = realpath( $this->getRoot()) . str_replace( "/", DIRECTORY_SEPARATOR, FSService::unicodeUrlDecode($_POST['answer']));
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
        //$this->download(realpath($this->getRoot()).str_replace( "/", DIRECTORY_SEPARATOR, $this->unicodeUrlDecode($_GET['path'])));
  }
    
  public function actionDownload()
  {
    if( FSService::unicodeUrlDecode($_GET['path']) == '')
      exit;

    FSService::download( FSService::unicodeUrlDecode($_GET['path']));
  }
}
?>
