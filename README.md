This theme is derived from Underscores WordPress Theme, Copyright 2013 Automattic, Inc.
Underscores WordPress Theme is distributed under the terms of the GNU GPL

Interesting things in this theme:

* **fonts folder**:
  * **Alef** - site fonts (for backup. I actually use Google's Alef).
  * **icomoon** - icon fonts for top menu.

* **images folder**:  
The site admin can customize the theme for holidays and special events. 
This folder has the header backgrounds and the logos for each event/holiday.
* Images from freepik
  * [logo_yom_kipur.jpg](http://www.freepik.com/free-vector/open-book-icon_764538.htm)
  * [header_birthday_purple.jpg](http://www.freepik.com/free-vector/purple-spotlight-stage-vector_715699.htm)
  * [header_birthday_blue.jpg](http://www.freepik.com/index.php?goto=41&idd=596676)
  * [logo_birthday_5.png](http://www.freepik.com/free-vector/golden-badges-free-design_720792.htm)
  * [header_purim.jpg](http://www.freepik.com/free-photos-vectors/party)
  * [logo_shoa.png](http://www.freepik.com/free-vector/flame-icons_725081.htm)
* Images using CC BY-NC-SA 2.0 license:
  * [header_rosh_hashana.jpg](https://www.flickr.com/photos/forestfortrees/49115389)
* Images using CC BY-SA 3.0 license:
  * [header_sukot.jpg](https://en.wikipedia.org/wiki/Sukkot#/media/File:EtrogC.jpg)
  * [header_shoa.jpg](https://upload.wikimedia.org/wikipedia/commons/f/f3/Holocaust_memorial_in_%C3%9Ast%C3%AD_nad_Labem,_2012,_01.JPG)
* Images using CC BY 3.0 license:
  * [logo_memorial.png](http://www.flaticon.com/free-icon/flag-black-shape_25669)
  * [logo_independence.png](http://www.flaticon.com/free-icon/flag-black-shape_25669)
  * [header_jerusalem.jpg](http://commons.wikimedia.org/wiki/File:David%27s_Tower_1.jpg)
* Images using CC BY 2.5 license:
  * [logo_pesah.png](http://commons.wikimedia.org/wiki/File:Machine-made_Shmura_Matzo.jpg)

* **inc folder**:
  * **class-tgm-plugin-activation.php** - Recommends plugins, and includes 2 plugins which aren't in the WP repository: Demo Tax meta class (adds user-field in category) and tf-faq (enables users to submit questios to a set of proffesionals)
  * **comments-and-latest-comments.php** - Latest comments, and comments format.
  * **editor.php** - Puts the Excerpt meta-box above the editor.
  * **featured-image.php** - This theme was built for a site that's already up and running. The site wasn't wasn't using the featured image, instead, it either used a user-field, or the first image in the post.  
        The functions in this file take either the user field, or the first image in the post, and turn them into the post's featured image.
  * **helpers.php** - My added functions file.
  * **jewish-calendar.php** - Based on the WP calendar widget, this file creates a Hebrew calendar which features events from the MEETINGS category.
  * **short-excerpt.php** - Cuts the excerpt by character (i.s.o word, like the usual excerpt does), and makes sure not to cut a word in its middle.
  * **extras.php** - Came with the _s theme. I removed the functions we didn't use.
  * **template-tags.php** - Came with the _s theme. I removed the functions we didn't use and added showing the Hebrew date in addition to the Gregorian date, in kamoha_posted_on.
  * **toc.php** - Functions for table of contents in post.
  
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
  * Removed all post-format files because we don' use them in this theme.  
  * Files added or especially modified:
    * **404.php** - Calls a special page made for this. 
    * **archive.php** - Only used for categories and tags (we don't have author and date archives).
    * **front-page1.php** - Has a maintenance message. Was used when we uploaded the theme, where we needed the theme to be active, but still couldn't show the homepage.
    * **functions.php** - Came with _s theme, and I just modified a few functions. Any new functions, were added in the inc folder. 
    * **page-site_documentation.php** - Use image-map created on a screenshot of the site, and scripts (imagemapster, colorbox) to provide documentation about each part of the page. Work in progress.

* **Incorporated code**:
  * **Jewish calendar: ** [Ulrich and David Greve](http://www.david-greve.de/luach-code/jewish-php.html)
  * **Featured images creation:** [CSS Tricks](http://css-tricks.com/snippets/wordpress/get-the-first-image-from-a-post), [WordPress Popular Posts](https://github.com/crowdfavorite-mirrors/wp-wordpress-popular-posts/blob/master/wordpress-popular-posts.php), [get_attachment_id_from_url](https://philipnewcomer.net/2012/11/get-the-attachment-id-from-an-image-url-in-wordpress/)
  * **Tabs in homepage:** [CSS Tricks](http://css-tricks.com/functional-css-tabs-revisited/)
  * **Featured image column in admin post list:** [WPengineer](http://wpengineer.com/1960/display-post-thumbnail-post-page-overview/)
  * **Payment form shortcode:**: [GavickPro](https://www.gavick.com/blog/wordpress-tinymce-custom-buttons/),[WPSE](http://wordpress.stackexchange.com/questions/139163/add-custom-tinymce-4-button-usable-since-wordpress-3-9-beta1)
