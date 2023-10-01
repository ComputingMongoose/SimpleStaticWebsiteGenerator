# SimpleStaticWebsiteGenerator
This is a PHP utility for generating a static HTML website from simple PHP files. It makes use of a user provided template that needs to call certain functions from this class. The PHP files may use additional methods for communicating with the generation system (for example for selecting items matching a certain tag). Examples are in the "examples" folder. My website at [https://ComputingMongoose.github.io](https://ComputingMongoose.github.io) is generated using this generator. The GitHub for my site is [here](https://github.com/ComputingMongoose/ComputingMongoose.github.io).

This utility file makes use of [SimpleSiteMapGenerator](https://github.com/ComputingMongoose/SimpleSiteMapGenerator) for producing site maps. A copy is included in this repo, but you should check for an updated version in its own GitHub.

## Methods for running the generator

### setSource($folder)
Sets the source folder containing PHP files.

### setDestination($folder)
Sets the destionation folder for the HTML output.

### setTemplate($template)
Sets the template PHP file. This will be used for generating each page. The template should call different methods for generating the menu and special links as needed.

### setMenu($menu)
Sets the menu to be used on all pages. Depending on the template, the menu may be generated automatically, without using this method, by selecting all pages with a certain tag.

### setBaseURL($url)
Sets the base URL for this website. This must be starting with http or https.

### runStep1()
Parses all pages to determine the available tags, page titles, page mappings, etc. This is required before running the next steps.

### runStep2()
This generates the HTML static files. It must be executed after _runStep1()_.

### generateSitemap()
Generates XML and TXT sitemaps. It must be executed after _runStep1()_.

## Methods for templates and pages

### getTitle()
Returns the title of the current page. 

### getContent()
Returns the HTML content of the current page. This is usually inserted inside a template element.

### getMenuHtml()
Returns an HTML representation of the menu. This is currently hardcoded to using _ul_ and _li_ elements with specific css classes.

### generateMenuItemsForTag($tag, $ignoreIndex=true, $ignoreCurrent=true)
Returns an array of menu items (title,link) corresponding to a specific tag. It may ignore the index page associated with the tag and/or the current page. This method may be useful also for pages.

### getFilesForTag($tag, $ignoreIndex=true, $ignoreCurrent=true)
Returns an array of files corresponding to a specific tag. It may ignore the index page associated with the tag and/or the current page. This method may be useful also for pages.

### getLink($file)
Returns an URL corresponding to a page specified by its associated PHP file.

### getResourceLink($file)
Returns an URL corresponding to a resource specified by its associated file (image, css, script).

### setTitle($title)
Forcefully sets the title associated with the current page. Usually the title is derived automatically by the system from h1 HTML tags.

### setTags($tags)
Sets the tags associated with the current page.


# Youtube

Checkout my YouTube channel for interesting videos: https://www.youtube.com/@ComputingMongoose/

# Website

Checkout my website: https://ComputingMongoose.github.io

