<?php

$db_user = 'root';
$db_pass = $db_user;
$db_name = 'a_mvcdbdoc';
$db_host = 'localhost';

$apidata_id = 1;

$db = new PDO('mysql:host='.$db_host.';dbname='.$db_name.';charset=utf8', $db_user, $db_pass, array(PDO::ATTR_EMULATE_PREPARES => false,  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$stmt = $db->query('SELECT * FROM apidata WHERE apidata_id = '.$apidata_id);
$apidata_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

class FolderCopy {


  public static function copyFolder($src, $dest) {

    $path = realpath($src);
    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);

      /** SplFileInfo $object*/
    foreach($objects as $name => $object)
    {
      $startsAt = substr(dirname($name), strlen($src));
      self::mkDir($dest.$startsAt);
      if(is_writable($dest.$startsAt) and $object->isFile())
      {
          copy((string)$name, $dest.$startsAt.DIRECTORY_SEPARATOR.basename($name));
      }
    }
  }

  private static function mkDir($folder, $perm=0777) {
    if(!is_dir($folder)) {
      mkdir($folder, $perm);
    }
  }

}

 function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }

function buildTree(array &$elements, $parentId = 0) {
    $branch = array();
    
    foreach ($elements as $key => $element) {        
        if ($element['apimenu_parent_id'] == $parentId) {
            $children = buildTree($elements, $element['apimenu_id']);
            if ($children) {
                $element['children'] = $children;
            }
            $branch[$element['apimenu_id']] = $element;
            //unset($elements[$element['apimenu_id']]);
        }
    }
    return $branch;
}

function array_depth(array $array) {
    $max_depth = 1;

    foreach ($array as $value) {
        if (is_array($value)) {
            $depth = array_depth($value) + 1;

            if ($depth > $max_depth) {
                $max_depth = $depth;
            }
        }
    }

    return $max_depth;
}


foreach($apidata_result as $ar)
{
	//var_dump($ar["apidata_id"]);
	//var_dump($ar["apidata_version"]);
	//var_dump($ar["apidata_name"]);

	$stmt1 = $db->query('SELECT * FROM apidoc WHERE apidoc_parent_id = '.$ar["apidata_id"]);
	$apidoc_result = $stmt1->fetchAll(PDO::FETCH_ASSOC);

	foreach ($apidoc_result as $apr)
	{
		//$apr["apidoc_version"]

		$stmtapimenu = $db->query('SELECT * FROM apimenu WHERE apimenu_doc_parent_id = '.$apr["apidoc_id"]);
		$apimenu_result = $stmtapimenu->fetchAll(PDO::FETCH_ASSOC);
                
                
	}	
}

$tree = buildTree($apimenu_result);

$render_section_menu = null;

$main_menu_build = '';
foreach ($tree as $tv)
{
    if (isset($tv['apimenu_name']))
    {
        if ($tv['apimenu_parent_id'] == null)
        {
            if (isset($tv['children']))
            {
                $children_menu_render = null;
                foreach ($tv['children'] as $menu_child)
                {
                    $children_menu_render .= '<li><a href="'.$menu_child['apimenu_menu_link'].'.htm">'.$menu_child['apimenu_name'].'</a></li>';
                    
                    $section_name = $menu_child['apimenu_menu_link'].'.htm';
                    
                    if (isset($menu_child["children"]))
                    {
                        foreach ($menu_child["children"] as $z => $cil)
                        {                            
                            $render_section_menu[$section_name][$z]['apimenu_menu_link'] = $cil['apimenu_menu_link'].'.htm';
                            $render_section_menu[$section_name][$z]['apimenu_menu_name'] = $cil['apimenu_menu_name'];
                            
                            $render_section_parent_menu[$menu_child['apimenu_parent_id']][$z]['apimenu_menu_link'] = $cil['apimenu_menu_link'].'.htm';
                            $render_section_parent_menu[$menu_child['apimenu_parent_id']][$z]['apimenu_menu_name'] = $cil['apimenu_menu_name'];
                        }
                    }
                }
                
                $main_menu_build .= '<li class="menu_inner_li">
                                <a href="#">'.$tv['apimenu_name'].' ￬</a>
                                <ul class="hidden">
                                    '.$children_menu_render.'
                                </ul>
                            </li>';
            }
            else
            {
                $main_menu_build .= '<li class="menu_inner_li"><div id="inner_menu_li"><a href="'.$tv['apimenu_menu_link'].'.htm">'.$tv['apimenu_name'].'</a></div></li>';
            }
        }
    }
}

$from_file = '../indexgen.htm';
$to_file = '../generated/index.htm';

$from_dir_gen = dirname(dirname(__FILE__)).'/';
$gen_dir = dirname(dirname(__FILE__)).'/generated';

rrmdir($gen_dir);
@rmdir($gen_dir);
mkdir($gen_dir);

@chmod($gen_dir, 0777);


FolderCopy::copyFolder($from_dir_gen.'css', $gen_dir.'/css');
FolderCopy::copyFolder($from_dir_gen.'assets', $gen_dir.'/assets');
FolderCopy::copyFolder($from_dir_gen.'js', $gen_dir.'/js');
FolderCopy::copyFolder($from_dir_gen.'pages', $gen_dir.'/pages');

//var_dump($tree);

$content_section_replace5 = '';

foreach ($apimenu_result as $apr)
{
    $from_page = '../indexgen.htm';
    $to_page = $gen_dir.'/'.$apr["apimenu_menu_link"].'.htm';
    $current_file = substr(strrchr($to_page, '/'), 1).'.htm';
    
    if (strlen($apr["apimenu_menu_link"]) > 0)
    {
        if (!copy($from_page, $to_page))
        {
            die('A file permission problem for copy. Operation stop.');
        }
                
        $file_content = file_get_contents($from_page);
        $content_main_menu_replace = str_replace("{{MENU}}", $main_menu_build, $file_content);
        
        $section_link = $apr["apimenu_menu_link"].'.htm';
        $section_parent_id = $apr["apimenu_parent_id"];
        


        $submenu_child = '';
        $child_menu_render = '';
        
        $is_submenu_exists = false;
        
        if (isset($render_section_menu[$section_link]))
        {
            $submenu_link_render = '';
            foreach ($render_section_menu[$section_link] as $submenu_child_data)
            {
                //$submenu_link_render .= '<li><a href="'.$submenu_child_data["apimenu_menu_link"].'">'.$submenu_child_data["apimenu_menu_name"].'</a></li>';
            }
            
            
            
            
            
            if ($submenu_link_render == null)
            {
                
                
                if (isset($render_section_menu[$section_link]))
                {
                    if ($render_section_menu[$section_link])
                    {
                        foreach ($render_section_menu[$section_link] as $submenu_child_data)
                        {    
                            $class_active = '';
                            
                            $submenu_link_render .= '<li>&nbsp;&nbsp;&nbsp;<a href="'.$submenu_child_data["apimenu_menu_link"].'">'.$submenu_child_data["apimenu_menu_name"].'</a></li>';
                        }
                    }                    
                }
            }
            
            $child_menu_render .= '
                            <li class="parent_active_menu">'.$apr["apimenu_name"].'</li>
                            '.$submenu_link_render;
            
            
            
        }
        else
        {     
            
            $child_menu_render2 = '';
            $submenu_link_render2 = '';
            
            
            
            if (isset($apr["apimenu_id"]))
            {
                $stmtapimenu = $db->query('SELECT * FROM apimenu WHERE apimenu_parent_id = '.$apr["apimenu_id"]);
                $apimenu_result = $stmtapimenu->fetchAll(PDO::FETCH_ASSOC);

                

                
                        $class_active = '';
                        foreach ($apimenu_result as $submenu_child_data)
                        {          
                            
                            
                            $submenu_link_render2 .= '<li><a href="'.$submenu_child_data["apimenu_menu_link"].'.htm">'.$submenu_child_data["apimenu_menu_name"].'</a></li>';
                        }

                        
                        
                        $child_menu_render2 .= '
                                        <li class="parent_active_menu">'.$apr["apimenu_name"].'</li>
                                        '.$submenu_link_render2;


                
            }
            
            $child_menu_render4 = '';
            if (isset($apr["apimenu_parent_id"]))
            {
                $stmtapimenu = $db->query('SELECT * FROM apimenu WHERE apimenu_parent_id = '.$apr["apimenu_parent_id"]);
                $apimenu_result = $stmtapimenu->fetchAll(PDO::FETCH_ASSOC);
                
                        foreach ($apimenu_result as $submenu_child_data)
                        {
                            $submenu_link_render2 .= '<li><a href="'.$submenu_child_data["apimenu_menu_link"].'.htm">'.$submenu_child_data["apimenu_menu_name"].'</a></li>';
                        }

                        $link_api = '<a href="'.$apr["apimenu_menu_link"].'.htm"> <-- ';
                        $link_api_end = '</a>';
                        
                        $child_menu_render4 .= '
                                        <li class="parent_active_menu">'.$link_api.$apr["apimenu_name"].$link_api_end.'</a></li>
                                        '.$submenu_link_render2;
                        
                        
                        


                
            }
            
            
            
            
            
        }

        
        //$content_section_replace = str_replace("{{MENU_SECTION}}", $child_menu_render, $content_main_menu_replace);
        $content_section_replace2 = str_replace("{{MENU_SECTION}}", $child_menu_render, $content_main_menu_replace);
        
        $content_section_replace3 = str_replace("{{INDEX_TITLE}}", $apr["apimenu_name"], $content_section_replace2);
        
        
        
        
        $content_section_replace4 = str_replace("{{MENU_EXTENDED}}", $child_menu_render4, $content_section_replace3);
        
        
        $sttxtdata = $db->query('SELECT * FROM txtdata WHERE txtdata_id = '.$apr["apimenu_id"]);
        $txtdataresult = $sttxtdata->fetch(PDO::FETCH_ASSOC);
        
        if ($txtdataresult['txtdata_id'] === $apr["apimenu_id"])
        {
            $txtdatacontent = str_replace("\n", "<br>", $txtdataresult['txtdata_content']);
            
            $content_section_replace5 = str_replace("{{MAIN_CONTENT_TEXT}}", $txtdatacontent, $content_section_replace4);
        }
/*
        $content_section_replace5 = preg_replace(
                '/\[url=(.+?)\](.+?)\[\/url\]/', 
                '<a href="\1">\2</a>', 
                $content_section_replace5);
  */      
        
        

        $content_section_replace5 = preg_replace(
                '/\[code\](.+?)\[\/code\]/', 
                '<pre class="prettyprint"><span class="codeinner"><code>\1</code></span></pre>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[codewarn\](.+?)\[\/codewarn\]/', 
                '<pre class="prettyprint"><span class="codeinner"><code class="warn">\1</code></span></pre>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[h1\](.+?)\[\/h1\]/', 
                '<div id="h1_inner"><h1>\1</h1></div>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[h2\](.+?)\[\/h2\]/', 
                '<div id="h2_inner"><h2>\1</h2></div>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[h3\](.+?)\[\/h3\]/', 
                '<div id="h3_inner"><h3>\1</h3></div>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[h4\](.+?)\[\/h4\]/', 
                '<div id="h4_inner"><h4>\1</h4></div>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[h5\](.+?)\[\/h5\]/', 
                '<div id="h5_inner"><h5>\1</h5></div>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[text\](.+?)\[\/text\]/', 
                '<div id="text_content"><p>\1</p></div>', 
                $content_section_replace5);

        $content_section_replace5 = preg_replace(
                '/\[content_info=(.+?)\](.+?)\[\/content_info\]/', 
                '<div id="content_info"><p>\1</p><div class="text">\2</div></div>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[img=\"(.+?)\"\]/', 
                '<div class="img-contain"><img src="\1" /></div>', 
                $content_section_replace5);
 
        $content_section_replace5 = preg_replace(
                '/\[imgcaptiontitle=\"(.+?)\"\ title=\"(.+?)\"\](.+?)\[\/imgcaptiontitle\]/', 
                '<div class="img-contain">
                        <div class="header_img_contain">\2</div>
                        <img src="\1" />
                        <div class="img_content"><p>\3</p></div>
                    </div>', 
                $content_section_replace5);
        
        $content_section_replace5 = preg_replace(
                '/\[imgcaption=\"(.+?)\"\](.+?)\[\/imgcaption\]/', 
                '<div class="img-contain">
                        <img src="\1" />
                        <div class="img_content"><p>\2</p></div>
                    </div>', 
                $content_section_replace5);
        

        
        
        
        file_put_contents($to_page, $content_section_replace5);
        
        
        
            
            
        

    }
    
    
    
}