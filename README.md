This theme is based on the underscores theme
Interesting in this theme:

* **fonts folder**:
  * **Alef** - site fonts (for backup. I actually use Google's Alef).
  * **icomoon** - icon fonts for top menu.

* **images folder**:
The site admin can customize the theme for holidays and special events. The customization changes the header background and the logo, to specific logos and backgrounds included in this folder.

* **inc folder**:
  * **class-tgm-plugin-activation.php** - recommends plugins, and includes 2 plugins which aren't in the WP repository: Demo Tax meta class (adds user-field in category), tf-faq (enables users to submit questios to a set of proffesionals)
  * **featured-image.php** - This theme is intended for a site that's already up and running, and hasn't used the featured image. Instead, it either used a user-field, or the first image in the post.
The functions in this file use either of those methods, and turn that image into  a featured image.
  * **helpers.php** - Main functions file.
  * **jewish-calendar.php** - Based on the WP calendar widget, this file creates a Hebrew calendar which features events from the MEETINGS category.
  * **short-excerpt.php** - Cuts the excerpt by character (i.s.o word, like the usual excerpt does), but makes sure not to cut a word in its middle.
  * **extras.php** - Came with the _s theme. I removed the functions we didn't use.
  * **template-tags.php** - Came with the _s theme. I removed the functions we didn't use and added showing the Hebrew date in addition to the Gregorian date, in kamoha_posted_on.
  
* **js folder**:
  * **customize-themes.js***: Used in Theme Customization.
  * **navigation.js**: Came with _s theme.
  * **script.js**: Scripts needed by this theme.
  * **skip-link-focus-fix.js**: Came with _s theme.
  
* **languages**:
  This theme is for a Hebrew site, so it has a Hebrew translation file.

* **site_documentation folder**:
  Screenshots used in site documentation. The site documentation is a page in the site with a dedicated page template, page-site_documentation.php.
  
* **root folder**:
  * Removed all post-format files because we don;y use them in this theme.  
  * Files added or especially modified:
    * **404.php**: Calls a special page made for this. 
    * **archive.php**: Only used for categories and tags (we don't have author and date archives).
    * **front-page1.php**: Has a maintenance message. Was used when we uploaded the theme, where we needed the theme to be active, but still couldn't show the homepage.
    * **functions.php**: Came with _s theme, and I just modified a few functions. Any new functions, were added in the inc folder.
    * **page-links_and_emergency.php**: Page template for links regarding the site content, and emergency numbers.
  