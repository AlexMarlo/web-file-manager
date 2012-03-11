<?php

class FSService
{
  public static function unicodeUrlDecode( $url, $encoding = 'CP1251')
  {
    if( PHP_EOL == "\r\n") // WINDOWS
      $encoding = 'CP1251';
    elseif( PHP_EOL == "\n") // LINUX/UNIX
      $encoding = 'UTF-8';
    elseif( PHP_EOL == "\r\n") // MAC OS
      $encoding = 'UTF-8';

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

      $str = iconv("UTF-8", "$encoding", $str);
      $url = str_replace ('%u'.$unicode, $str, $url);
    }

    return urldecode ($url);
  }

  public static function zipping( $archive, $fPath)
  {
    $arch = new ZipArchive;
    $arch->open( $Archive, ZipArchive::CREATE);
    FSService::itemInZip( $arch, realpath(dirname($fPath)) . '/', realpath($fPath) . '/');
    FSService::close();

    return true;
  }

  public static function itemInZip( $arch, $fPath, $fInZip)
  {
    if( is_file( $fInZip))
    {
      $arch->addFile( $fInZip, str_replace( $fPath, '', $fInZip));
    }
    elseif( is_dir($fInZip))
    {
      if ($fPath[1]<>':')
        $arch->addEmptyDir(str_replace(dirname($fPath), '', $fPath));

      $scr = scandir( $fInZip);
      
      FSService::deleteDotsDirFromArray( $scr);

      foreach($scr as $i)
        FSService::itemInZip( $arch, $fPath, $fInZip . $i . (is_dir($fInZip.$i)? '/': ''));
    }
  }

  public static function makeFile( $fName)
  {
    if( !file_exists($fName))
    {
      $file = fopen($fName, 'w+');
      fclose( $file);
      return true;
    }
    return false;
  }

  public static function makeDir( $dirName)
  {
    try
    {
      return mkdir( $dirName, 0777);
    }
    catch( Exception $e)
    {
      return false;
    }
  }

  public static function copy( $what, $where)
  {
    if( is_file( $what) && !copy( $what, $where))
      return true;

    if( is_dir( $what))
    {
      mkdir( $where);
      $scr = scandir( $what);

      FSService::deleteDotsDirFromArray( $scr);

      foreach($scr as $i)
        FSService::copy( $what . DIRECTORY_SEPARATOR . $i, $where . DIRECTORY_SEPARATOR . $i);
      
      return true;
    }

    return false;
  }

  public static function move( $what, $where)
  {
    FSService::copy( $what, $where);
    FSService::delete( $what);
  }

  public static function delete( $what)
  {
    if( is_file( $what) && !unlink( $what))
      return true;

    if(is_dir( $what))
    {
      $scr = scandir( $what);

      FSService::deleteDotsDirFromArray( $scr);

      foreach($scr as $i)
        FSService::delete( $what . DIRECTORY_SEPARATOR . $i);

      rmdir( $what);
      
      return true;;
    }

    return false;
  }

  public static function download( $what)
  {
    echo $what;
    if( is_file( $what))
    {
    echo 1;
      header( "Content-Length: " . filesize( $what));
      header( "Content-Disposition: attachment; filename=" . $what); 
      header( "Content-Type: application/x-force-download; name=\"" . $what."\"");
      readfile( $what);
    }
    else
    {
      if( is_dir( $what))
      {
        FSService::zipping( $what . ".zip", $what);

        header( "Content-Length: " . filesize( $what . ".zip"));
        header( "Content-Disposition: attachment; filename=" . $what . ".zip"); 
        header( "Content-Type: application/x-force-download; name=\"" . $what . ".zip"."\"");
        readfile( $what . ".zip");
        FSService::delete( $what . ".zip");
      }
    }
  }

  public static function dir( $what, $withoutDots = false)
  {
    $scr = scandir( $what);

    if( $withoutDots)
      FSService::deleteDotsDirFromArray( $scr);

    return( $scr);
  }
  
  private static function deleteDotsDirFromArray( $dirArray)
  {
    if( !is_array( $dirArray))
      return false;

    unset( $dirArray[ array_search( '.', $dirArray)]);
    unset( $dirArray[ array_search( '..', $dirArray)]);

    return true;
  }
}
