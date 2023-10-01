<?php

require_once "../../SimpleStaticWebsiteGenerator.php";

$sys=new SimpleStaticWebsiteGenerator();

echo "Static website generator from simple PHP files\n\n";

$sys->setSource("content");
$sys->setDestination("html_out");
$sys->setTemplate("template/template.php");
$sys->setBaseURL("https://examplewebsite");

echo "Step 1. Processing content\n";
$sys->runStep1();
echo "DONE\n";

$sys->setMenu([
	["title"=>"Topic 1","tag"=>"topic1","link"=>"content/page1.php","items"=>["TAG:topic1"]],
	["title"=>"Topic 2","tag"=>"topic2","link"=>"content/t2_page1.php","items"=>["TAG:topic2"]],
]);


echo "\n\n";
echo "Step 2. Generating static content\n";
$sys->runStep2();
echo "DONE\n";

echo "\n\n";
echo "Step 3. Generating sitemap\n";
$sys->generateSitemap();
echo "DONE\n";
