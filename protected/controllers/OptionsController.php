<?php
class OptionsController extends CController
{

        private function unicodeUrlDecode($url, $encoding = "")
        {
            $encoding = 'CP1251';
            preg_match_all('/%u([[:xdigit:]]{4})/', $url, $a);
            foreach ($a[1] as $unicode){
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
        public function ajaxout($content){
            return unhtmlentities(htmlentities($content,0,"windows-1251"));
            }
        public function unhtmlentities ($string){
            $trans_tbl = get_html_translation_table (HTML_ENTITIES);
            $trans_tbl = array_flip ($trans_tbl);
            return strtr ($string, $trans_tbl);
        }    
        
    public $defaultAction='options';
    
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
        'actions'=>array('options','getrootdir','setrootdir',),
        'users'=>array('admin'),
      ),
      array('deny',  // deny all users
        'users'=>array('*'),
      ),
    );
  }    
    
    private function getRoot()
    {
        $sql='Select rootdir From rootdir';
        $command=Yii::app()->db->createCommand($sql);
        $dataReader=$command->query();
        foreach($dataReader as $row)
            $rd="$row[rootdir]";

        return $rd;
    }

    private function setRoot($root)
    {
        $sql="UPDATE rootdir SET rootdir='".$root."' WHERE id=\"1\";";
        $command = Yii::app()->db->createCommand($sql);
        $command->execute();
    }

    public function actionOptions(){
        $this->render('options',array('rootdir'=>$this->getRoot()));        
    }

    public function actionGetRootDir(){
            echo($this->getRoot());
            //echo($dataReader[rootdir]);
    }

    public function actionSetRootDir(){
        //if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')
        //{        
            $this->setRoot($this->unicodeUrlDecode($_GET['newrootdir']));
            $this->renderPartial('_options',array('rootdir'=>$this->getRoot()));
            //$dataReader=$command->query();
            
        //}
    }
}
?>
