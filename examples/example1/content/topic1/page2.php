<?php $sys->setTags(["topic1"]); ?>

<h1>Example 1 / Topic 1 / Page 2</h1>

<p>Other pages in Topic 1</p>

<ul>
<?php
$items=$sys->generateMenuItemsForTag("topic1");
foreach($items as $item){
	echo "<li><a href=\"${item['link']}\">${item['title']}</a></li>\n";
}
?>
</ul>
