This theme is based on the underscores theme.  
Interesting things in this theme:

* **fonts folder**:
  * **Alef** - site fonts (for backup. I actually use Google's Alef).
  * **icomoon** - icon fonts for top menu.

* **images folder**:  
The site admin can customize the theme for holidays and special events. 
This folder has the header backgrounds and the logos for each event/holiday.

* **inc folder**:
  * **class-tgm-plugin-activation.php** - recommends plugins, and includes 2 plugins which aren't in the WP repository: Demo Tax meta class (adds user-field in category) and tf-faq (enables users to submit questios to a set of proffesionals)
  * **featured-image.php** - This theme was built for a site that's already up and running. The site wasn't wasn't using the featured image, instead, it either used a user-field, or the first image in the post.  
        The functions in this file take either the user field, or the first image in the post, and turn them into the post's featured image.
  * **helpers.php** - My added functions file.
  * **jewish-calendar.php** - Based on the WP calendar widget, this file creates a Hebrew calendar which features events from the MEETINGS category.
  * **short-excerpt.php** - Cuts the excerpt by character (i.s.o word, like the usual excerpt does), and makes sure not to cut a word in its middle.
  * **extras.php** - Came with the _s theme. I removed the functions we didn't use.
  * **template-tags.php** - Came with the _s theme. I removed the functions we didn't use and added showing the Hebrew date in addition to the Gregorian date, in kamoha_posted_on.
  
* **js folder**:
  * **customize-themes.js** - Used in Theme Customization.
  * **navigation.js** - Came with _s theme.
  * **script.js** - Scripts needed by this theme.
  * **skip-link-focus-fix.js** - Came with _s theme.
  
* **languages folder**:  
  This theme is for a Hebrew site, so it has a Hebrew translation file.

* **site_documentation folder**:  
  Has screenshots used in site documentation. The site documentation is a page which uses a dedicated page template - page-site_documentation.php.
  
* **root folder**:
  * Removed all post-format files because we don;y use them in this theme.  
  * Files added or especially modified:
    * **404.php** - Calls a special page made for this. 
    * **archive.php** - Only used for categories and tags (we don't have author and date archives).
    * **front-page1.php** - Has a maintenance message. Was used when we uploaded the theme, where we needed the theme to be active, but still couldn't show the homepage.
    * **functions.php** - Came with _s theme, and I just modified a few functions. Any new functions, were added in the inc folder. 
    * **page-site_documentation.php** - Use image-map created on a screenshot of the site, and scripts (imagemapster, colorbox) to provide documentation about each part of the page. Work in progress.