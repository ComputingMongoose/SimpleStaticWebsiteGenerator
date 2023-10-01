<?php $sys->setTags(["topic2"]); ?>

<h1>Example 1 / Topic 2 / Page 1</h1>

<p>Other pages in Topic 2</p>

<ul>
<?php
$items=$sys->generateMenuItemsForTag("topic2");
foreach($items as $item){
	echo "<li><a href=\"${item['link']}\">${item['title']}</a></li>\n";
}
?>
</ul>
