<?php

/* 
For an up-to-date version of this script check the GitHub at:
https://github.com/ComputingMongoose/SimpleStaticWebsiteGenerator
*/


require_once "SimpleSiteMapGenerator.php"; // From https://github.com/ComputingMongoose/SimpleSiteMapGenerator

class SimpleStaticWebsiteGenerator {
	var $currentPath;
	var $currentContent;
	var $currentTags;
	var $currentTitle;
	var $allTags;
	var $step;
	var $source;
	var $destination;
	var $dstMapping;
	var $baseMapping;
	var $template;
	var $tagMapping;
	var $titles;
	var $menu;
	var $baseURL;
	
	public function __construct(){
		$this->allTags=[];
		$this->currentTags=[];
		$this->currentPath="";
		$this->currentContent="";
		$this->step=0;
		$this->source="";
		$this->destination="";
		$this->dstMapping=[];
		$this->template="";
		$this->tagMapping=[];
		$this->titles=[];
		$this->menu=[];
		$this->baseMapping=[];
		$this->currentTitle=false;
		$this->baseURL="";
	}
	
	private function _startsWith( $haystack, $needle ) {
		 $length = strlen( $needle );
		 return substr( $haystack, 0, $length ) === $needle;
	}
	
	private function _endsWith( $haystack, $needle ) {
		$length = strlen( $needle );
		if( !$length ) {
			return true;
		}
		return substr( $haystack, -$length ) === $needle;
	}

	public function setCurrentFile($path){
		$this->currentPath=$path;
		$this->currentTags=[];
		$this->currentTitle=false;
	}
	
	public function setBaseURL($url){
		$this->baseURL=$url;
	}
	
	public function processFile($path){
		$this->setCurrentFile($path);
		echo $this->step.": $path   ";
		
		if($this->step==1){
			ob_start();
			global $sys;
			{ include "$path"; }
			$content=ob_get_contents();
			ob_end_clean();
			
			$err=0;
			
			// Find tags
			$dstTag="_generic";
			if(count($this->currentTags)==0){
				$err=1;
				echo "WARN: No tags\n";
			}else $dstTag=$this->currentTags[0];
			
			// Find destination mapping based on tags
			$folder=$this->destination;
			if(strlen($dstTag)>0){
				$folder.="/".$dstTag;
				$this->baseMapping[$path]="../";
			}else{
				$this->baseMapping[$path]="";
			}
			if(!is_dir($folder)){
				mkdir($folder);
				if(!is_dir($folder)){
					die("Cannot create destination folder [$folder]\n");
				}
			}
			$dst=$folder."/".basename($path,".php").".html";
			$this->dstMapping[$path]=$dst;
			
			// Find title
			if($this->currentTitle!==false){
				$this->titles[$path]=$this->currentTitle;
			}else{
				$r=preg_match_all("|<h1>([^<]*)</h1>|i",$content,$matches);
				if($r===0 || $r===false){
					$err=1;
					echo "WARN: No title\n";
					$this->titles[$path]="Unknown";
				}else{
					$this->titles[$path]=$matches[1][0];
				}
			}
			
			if($err==0)echo "OK\n";
		}else if($this->step==2){
			// Generate the content
			ob_start();
			global $sys;
			{ include "$path"; }
			$this->currentContent=ob_get_contents();
			ob_end_clean();
			
			// Include the template
			ob_start();
			{ include $this->template; }
			$fdata=ob_get_contents();
			ob_end_clean();
			
			// Produce final file
			echo " ==> ".$this->dstMapping[$path]."\n";
			file_put_contents($this->dstMapping[$path],$fdata);
		}
	}

	public function processFolder($dir){
		if (is_dir($dir)) {
			if ($dh = opendir($dir)) {
				while (($file = readdir($dh)) !== false) {
					$path="$dir/$file";
					if($file=="." || $file=="..")continue;
					if(is_dir($path))$this->processFolder($path);
					else if($this->_endsWith($file,".php") && !$this->_startsWith($file,"common"))
						$this->processFile($path);
				}
				closedir($dh);
			}
		}
	}
	
	public function runStep1(){
		$this->step=1;
		$this->processFolder($this->source);
	}
	
	public function runStep2(){
		$this->step=2;
		$this->processFolder($this->source);
	}

	public function setSource($dir){
		if(!is_dir($dir)){
			die("Invalid source folder [$dir]\n");
		}
		$this->source=$dir;
	}

	public function setDestination($dir){
		if(!is_dir($dir)){
			echo "WARN: Destination [$dir] does not exist. Attempting to create it.\n";
			mkdir($dir);
			if(!is_dir($dir)){
				die("Invalid destination folder [$dir]\n");
			}
		}
		$this->destination=$dir;
	}
		
	public function setTemplate($template){
		if(!is_file($template)){
			die("Template not found [$template]\n");
		}
		$this->template=$template;
	}
	
	public function setTagMapping($mapping){
		$this->tagMapping=$mapping;
	}
	
	public function setMenu($menu){
		$this->menu=$menu;
	}
	
	public function generateSitemap(){
		$pages=array_values($this->dstMapping);
		$sgen=new SimpleSiteMapGenerator();
		$sgen->setBaseURL($this->baseURL);
		$sgen->setDestinationPath($this->destination);
		$sgen->generateSiteMapXML($pages);
		$sgen->generateSiteMapTXT($pages);
	}
	
/*********** Methods for template ****************/	
	public function getContent(){
		return $this->currentContent;
	}
	
	public function getTitle(){
		return $this->titles[$this->currentPath];
	}
	
	public function getMenuHtml(){
		$currentTag=basename(dirname($this->currentPath));
		foreach($this->menu as $m){
			$cls="";
			if($m['tag']==$currentTag)$cls='class="currentmenu"';
			echo "<li $cls><a href=\"".$this->getLink($m['link'])."\">".$m['title']."</a></li>\n";
			if($m['tag']==$currentTag){
				echo '<li class="submenu currentmenu">';
				echo '<ul>';
				foreach($m['items'] as $item){
					if(is_string($item) && $this->_startsWith($item,"TAG:")){
						$itemdata=$this->generateMenuItemsForTag(substr($item,4),true,false);
						foreach($itemdata as $citem){
							echo "<li><a href=\"${citem['link']}\">${citem['title']}</a></li>\n";
						}
					}else{
						echo "<li><a href=\"${item['link']}\">${item['title']}</a></li>\n";
					}
				}
				echo '</ul>';
				echo "</li>\n";
				
			}
				
		}	
	}
	
/*********** Methods for posts *******************/
	public function setTitle($title){
		$this->currentTitle=$title;
	}
	
	public function setTags($tags){
		if(!is_array($tags)){
			die("Invalid call to setTags\n");
		}
		$this->currentTags=$tags;
		foreach($tags as $t){
			if(!isset($this->allTags[$t]))$this->allTags[$t]=[];
			$this->allTags[$t][$this->currentPath]=true;
		}
	}

	public function generateMenuItemsForTag($tag, $ignoreIndex=true, $ignoreCurrent=true){
		if(!isset($this->allTags[$tag])){
			if($this->step==1)return [];
			die("Unknown tag [$tag]\n");
		}
		
		$files=$this->getFilesForTag($tag, $ignoreIndex, $ignoreCurrent);
		$items=[];
		foreach($files as $c){
			$items[]=[
				"title"=>$this->titles[$c],
				"link"=>$this->getLink($c)
			];
		}
		return $items;
	}

	public function getFilesForTag($tag, $ignoreIndex=true, $ignoreCurrent=true){
		if(!isset($this->allTags[$tag]))
			die("Unknown tag [$tag]\n");

		$ret=[];
		foreach($this->allTags[$tag] as $c=>$t){
			if($ignoreIndex && strcasecmp(basename($c,".php"),"index_".$tag)==0)continue;
			if($ignoreCurrent && $c==$this->currentPath)continue;
			$ret[]=$c;
		}
		return $ret;
	}


	public function getLink($file){
		if(strpos($file,"/")===false){
			$p=dirname($this->currentPath)."/".$file;
		}else $p=$file;
		
		if(!isset($this->dstMapping[$p]))return "#";
		
		$link=substr($this->dstMapping[$p],strlen($this->destination));
		if(!isset($this->baseMapping[$this->currentPath]) || strlen($this->baseMapping[$this->currentPath])==0)$link=ltrim($link,"/");
		else $link=$this->baseMapping[$this->currentPath].ltrim($link,"/");

		return $link;
	}

	public function getResourceLink($file){
		if(!isset($this->baseMapping[$this->currentPath]))return "$file";
		return $this->baseMapping[$this->currentPath].$file;
	}
}

