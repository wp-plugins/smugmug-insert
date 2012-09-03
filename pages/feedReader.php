<?php
error_reporting(E_ERROR);
ini_set('display_errors', 'ON');
$nsMrss = 'http://search.yahoo.com/mrss/';
$nsAtom = 'http://www.w3.org/2005/Atom';

$method = $_GET["action"];

if ($method == "list") {
	$username = $_GET["username"];
	if (!empty($username)) {
		$doc = new DOMDocument();
		$doc->load('http://smugmug.com/hack/feed.mg?Type=nickname&Data='.$username.'&format=rss200');
		$feedInfos = array();
		foreach ($doc->getElementsByTagName('item') as $node) {
		  $itemRSS = array ( 
		    'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
		    'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
		    'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
		    'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
		    );
		    
		  preg_match('/[a-z0-9\_]+$/i', $itemRSS['link'], $urlFilter);
		  preg_match('/(src)="([^"]*)"/i',$itemRSS['desc'], $picFilter);
		  $cleanedArray = array(
		    'albumName' => $itemRSS['title'], 
		    'albumId' => $urlFilter[0],
		    'thumbUrl' => $picFilter[2],
		    'timestamp' => strtotime($itemRSS['date']),
			'date' => date("d.m.Y",strtotime($itemRSS['date']))
		  );
		  array_push($feedInfos, $cleanedArray);
		}
		echo json_encode($feedInfos);
	} else {
		echo "error username is empty!";
	}
} else if ($method == "album") {
	$albumid = $_GET["albumid"];
	$thumb = $_GET["thumb"];
	$medium = $_GET["medium"];
	$start = !empty($_GET["start"]) && preg_match('/^[0-9]+$/i', $_GET["start"]) ? $_GET["start"] : 1;
	$pageCount = !empty($_GET["PageCount"]) && preg_match('/^[0-9]+$/i', $_GET["PageCount"]) ? $_GET["PageCount"] : 100;
	if (empty($medium))
		$medium = "L";
	if (empty($thumb))
		$thumb = "Ti";	
	if (!empty($albumid)) {
		$photo = new DOMDocument();
		$photo->load('http://smugmug.com/hack/feed.mg?Type=gallery&Data='.$albumid.'&format=rss200&start='.$start.'&PageCount='.$pageCount);
		$picInfos = array();
		$count = 1;
		
		foreach($photo->getElementsByTagNameNS($nsAtom, 'link') as $meta) {
			if (preg_match('/(\&start\=([0-9]+)\&PageCount\=([0-9]+))/i', $meta->getAttribute('href'), $pagingFilter) == 1) {
				$metaArr[$meta->getAttribute('rel')] = array('start' => $pagingFilter[2], 'pageCount' => $pagingFilter[3]);
			}
		}
	
		$curStart = $metaArr['self']['start'];
		$curPerPage = $metaArr['self']['pageCount'];
		$metaInfo = array('current' => $curStart, 'perPage' => $curPerPage);
		if (!empty($metaArr['next'])) {
			$metaInfo['next'] = ($curStart+$curPerPage);
		}
		if ($curStart > 1) {
			$metaInfo['previous'] = (($curStart-$curPerPage) <= 0 ? 1 : ($curStart-$curPerPage));
		}
		
		
		foreach ($photo->getElementsByTagName('item') as $node) {
			$picInfo = array('id' => $count++);
			foreach ($node->getElementsByTagNameNS($nsMrss, 'content') as $media) {
				$varW = $media->getAttribute('width');
				$varH = $media->getAttribute('height');
				$varUrl = $media->getAttribute('url');
				
				$sizeType = "O";
				if (preg_match('/\-([a-z0-9]+)\.(jpg|png|jpeg|tif)$/i', $varUrl, $sizeFilter) == 1) {
					$sizeType = $sizeFilter[1];
				} 
				switch($sizeType) {
					case $thumb:
						$picInfo['thumbUrl'] = $varUrl;
						$picInfo['thumbWidth'] = $varW;
						$picInfo['thumbHeight'] = $varH;
						break;
					case 'O':
						$picInfo['fullUrl'] = $varUrl;
						$picInfo['fullWidth'] = $varW;
						$picInfo['fullHeight'] = $varH;
						break;
					case $medium:
						$picInfo['mediumUrl'] = $varUrl;
						$picInfo['mediumWidth'] = $varW;
						$picInfo['mediumHeight'] = $varH;
						break;	
				}
			}
			array_push($picInfos, $picInfo);
		}
		$metaInfo['itemCount'] = count($picInfos);
		$metaInfo['items'] = $picInfos;
		echo json_encode($metaInfo);
	} else {
		echo "error albumid is empty!";
	}
} else {
	echo "wrong or missing action!";
}
?>