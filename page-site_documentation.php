<?php
/*
  Template Name: דף הסברים על האתר
 */
?>
<!DOCTYPE html>
<html lang="he-IL" dir="rtl">
    <head>
        <meta name="generator"
              content="HTML Tidy for HTML5 (experimental) for Windows https://github.com/w3c/tidy-html5/tree/c63cc39" />
        <meta charset="UTF-8" />
        <title>מסמך הסבר לתבנית החדשה של כמוך</title>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/site_documentation/colorbox.css">
        <style>
            body{width: 1250px; margin: 0 auto;background: url('http://www.kamoha.org.il/wp-content/themes/kamoha_2015/images/pattern.png');}
            img{display: block;}
            #explenation{    
                background-color: #839db9;
                box-shadow: 0 2px 8px 0 #aaa;
                font-size: 20px;
                padding: 0 15px;
            }
            /*design for elements hover: במקום הירוק + אדום המזעזע :)
            background-color: rgba(131,157,182,0.7);
            border: 3px solid #3d6998;*/

            #cboxLoadedContent {
                margin-bottom: 28px;
                padding: 0 15px;
            }

            #cboxClose{
                bottom: auto;
                right: auto;
                top: 0;
                left: 0;
            }

            .section-title {
                font-size: 22px;
                font-weight: bold;
                color: #3d6998;
            }

            .key {
                font-weight: bold;
                margin-top: 29px;
            }
        </style>
    </head>
    <body>
        <div class="content">
            <section id="explenation" style="text-align:center;">
                <h1>עבודה על התבנית החדשה</h1>
                <h2>להלן תצלום מסך של האתר. מעבר עכבר על חלקי התצלום יאירו את החלקים שלו, ולחיצה על חלק תוביל להסברים עליו</h2>
            </section>
            <img id="homepage-img" src="<?php echo get_template_directory_uri(); ?>/site_documentation/kamoha_home_ss.jpg" usemap="#map-homepage-img" alt="" /> 
            <map name="map-homepage-img" id="map-homepage-img">
                <area class="cboxElement" alt="" title="" href="#top-menu" shape="rect" coords="21,18,746,76" style="outline:none;" target="_self"     />
                <area class="cboxElement" alt="" title="" href="#logo" shape="rect" coords="1013,18,1170,138" style="outline:none;" target="_self"     />
                <area class="cboxElement" alt="" title="" href="#search" shape="rect" coords="50,94,311,159" style="outline:none;" target="_self"     />
                <area class="cboxElement" alt="" title="" href="#topics-menu" shape="rect" coords="3,183,1250,252" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#first-newest-post" shape="rect" coords="390,297,1191,547" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#newest-posts" shape="rect" coords="388,623,1191,1149" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#load-more" shape="rect" coords="367,1170,1186,1223" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#updates" shape="rect" coords="28,286,347,790" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#countdown" shape="rect" coords="44,811,325,921" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#ask-rabbi-link" shape="rect" coords="55,993,318,1064" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#latest-comments" shape="rect" coords="33,1830,324,2262" style="outline:none;" target="_self"     />
                <area  alt="" title="" href="#tag-cloud" shape="rect" coords="47,2333,329,2650" style="outline:none;" target="_self"     />
            </map>

            <div style="display:none"><!-- here is content that shows in modals-->
                <article id="top-menu" class="section">
                    <h4 id="top-menu-title" class="section-title">תפריט עליון</h4>
                    <dl>
                        <dt class="key">איך הוא נראה:</dt>
                        <dd><img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-1.png" alt=""></dd>
                        <dt class="key">הגדרה:</dt>  
                        <dd> התפריט העליון ביותר בדף. הפריטים בו קשורים לעמותה. </dd>
                        <dt class="key">היכן מגדירים אותו:</dt> 
                        <dd> ניתן להגדיר אותו, ולהוסיף לו פריטים, בממשק הניהול. בתפריט הימני בוחרים <strong>עיצוב</strong> ובתת התפריט בוחרים <strong>תפריטים</strong>.
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-exp-1.png" alt="">
                            <br>
                            כדי לערוך אותו, יש לבחור אותו מהתפריט הנפתח:
                            <img src ="<?php echo get_template_directory_uri(); ?>/site_documentation/header-exp-2.png" alt="">
                        </dd>
                        <dt class="key">איך משנים טקסט:</dt>  
                        <dd> אפשר לשנות באחד משני אופנים: אם הפריט הוא מדור או עמוד, אז כשמשנים את שם המדור או את שם העמוד, השם בתפריט ישתנה גם הוא. אם לא רוצים לשנות את שם המדור או העמוד אלא רק את המלל בתפריט, אפשר לעשות זאת בתפריט עצמו </dd>
                        <dt class="key">התנהגות בגרסת נייד:</dt>  
                        <dd>רוב הפריטים מוסתרים. מופיעים רק האיקונים של אודות, צור קשר, ורבנים תומכים
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-11.png" alt="">
                        </dd>
                        <dt class="key">הערות תכנותיות:</dt>  
                        <dd>האיקונים אינם תמונה אלא font-icon שנלקח מאתר icomoon.io</dd>
                    </dl>  
                </article>
                <article id="logo" class="section">
                    <h4 id="logo-title" class="section-title">סמליל</h4>
                    <dl>
                        <dt class="key">איך הוא נראה:</dt>
                        <dd><img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-2.png" alt=""></dd>
                        <dt class="key">הגדרה:</dt>  
                        <dd> סמליל העמותה. </dd>
                        <dt class="key">היכן מגדירים אותו:</dt> 
                        <dd> הוא מוגדר באופן תכנותי. לעת עתה אין שליטה בו מתוך ממשק הניהול. יתכן שבעתיד ניתן יהיה לשנות אותו לסמלילים מותאמים לארועים מיוחדים כגון חגים.
                        </dd>
                        <dt class="key">איך משנים טקסט:</dt>  
                        <dd>הסמליל מסתיר את שם האתר. אם רוצים לשנות את שם האתר, עושים זאת בממשק הניהול: בתפריט הימני בוחרים הגדרות, ובתת התפריט בוחרים כללי. במסך שנפתח, משנים את <strong>כותרת האתר</strong>. </dd>
                        <dt class="key">התנהגות בגרסת נייד:</dt>  
                        <dd>עובר לאמצע במקום להיות בצד ימין, וקטן עם הקטנת הרזולוציה
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-22.png" alt="">
                        </dd>
                    </dl> 
                </article>
                <article id="search" class="section">
                    <h4 id="search-title" class="section-title">חיפוש</h4>
                    <dl>
                        <dt class="key">איך הוא נראה:</dt>
                        <dd><img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-3.png" alt=""></dd>			  
                        <dt class="key">הגדרה:</dt>  
                        <dd> שדה חיפוש. </dd>
                        <dt class="key">היכן מגדירים אותו:</dt> 
                        <dd> הוא מוגדר באופן תכנותי. אין שליטה בו מתוך ממשק הניהול.
                        </dd>
                        <dt class="key">איך משנים טקסט:</dt>  
                        <dd> 
                            אפשר לשנות את המילה חיפוש עם 3 הנקודות שאחריה, לאיזו מילה שרוצים. אפשר גם למחוק לגמרי את התרגום, במקרה שרוצים שתיבת החיפוש תהיה ריקה.
                            כדי לשנות את הטקסט הנ"ל, בממשק הניהול, בוחרים בתפריט הימני את כלים -> לוקליזציה.
                            <br>
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-12.png" alt=""> 
                            <br>
                            במסך שנפתח, בוחרים ערכות עיצוב:
                            <br>
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-13.png" alt=""> 
                            <br>
                            במסך שנפתח, בוחרים לערוך את ערכת העיצוב kamoha_2015:
                            <br>
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-14.png" alt=""> 
                            <br>
                            ואז מחפשים את המילה search. רצוי להדליק את הצ'קבוקס "רישיות האות לא משנה". בוחרים את התרגום שכתוב לידו Placeholder, ולוחצים על כפתור העריכה.
                            <br>
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-15.png" alt=""> 
                            <br>
                            כותבים בחלון שנפתח מה שרוצים, ולוחצים על שמירה:
                            <br>
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-16.png" alt=""> 
                            <br>
                            חשוב: כשמסיימים, חובה ללחוץ על הכפתור "צור קובץ mo", אחרת השינויים לא ייראו באתר!
                            <br>
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-17.png" alt=""> 
                        </dd>
                        <dt class="key">התנהגות בגרסת נייד:</dt>  
                        <dd> מופיע בתוך תפריט שלושת הפסים
                        </dd>
                        <dt class="key">הערות תכנותיות:</dt>  
                        <dd> האיקון של זכוכית המגדלת הוא לא תמונה, אלא font-icon מהאתר icomoon.io 
                        </dd>
                    </dl>   
                </article>
                <article id="topics-menu" class="section">
                    <h4 id="topics-menu-title" class="section-title">תפריט נושאים</h4>
                    <dl>
                        <dt class="key">איך הוא נראה:</dt>
                        <dd><img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-4.png" alt=""></dd>			  
                        <dt class="key">הגדרה:</dt>  
                        <dd> תפריט של המדורים השונים באתר, ותתי מדוריהם אם ישנם </dd>
                        <dt class="key">היכן מגדירים אותו:</dt> 
                        <dd> ניתן להגדיר אותו, ולהוסיף לו פריטים, בממשק הניהול. בתפריט הימני בוחרים <strong>עיצוב</strong> ובתת התפריט בוחרים <strong>תפריטים</strong>.
                            <br>
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-exp-1.png" alt="">
                            <br>
                            כדי לערוך אותו, יש לבחור אותו מהתפריט הנפתח (תפריט מרכזי):
                            <img src ="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-41.png" alt="">
                            <br>
                            <strong>שים לב: </strong> בתפריט נצאים רק המדורים הראשיים. תתי המדורים נוצרים דינמית ע"י התבנית.
                        <dt class="key">איך משנים טקסט:</dt>  
                        <dd> אפשר לשנות באחד משני אופנים: אם הפריט הוא מדור או עמוד, אז כשמשנים את שם המדור או את שם העמוד, השם בתפריט ישתנה גם הוא. אם לא רוצים לשנות את שם המדור או העמוד אלא רק את המלל בתפריט, אפשר לעשות זאת בתפריט עצמו. 
                            <br>
                            האפשרות השניה (שינוי שם הפריט בתפריט) אפשרית רק לגבי המדורים הראשיים, מפני שהם מוגדרים פיזית בתפריט. לתתי המדורים תקפה רק האפשרות הראשונה (לשנות את שם המדור עצמו) מפני שהם מתווספים לתפריט באופן דינמי בעת הצגת האתר.
                        </dd>
                        <dt class="key">איך משנים סדר הופעה:</dt>  
                        <dd> עבור המדורים הראשיים, הנמצאים בניהול התפריט, גרירה שלהם ברחבי התפריט וחיצה על כפתור שמור משנה את מיקומם. עבור תתי המדורים, שאינם נראים בניהול התפריט, שינוי סדר ההופעה נעשה במסך my category order הנמצא תחת הפריט פוסטים בממשק הניהול. ע"מ לערוך תתי מדורים, יש לבחור את המדור מתוך הרשימה הנפתחת, ללחוץ על כפתור ה-Order Subcategories , וברשימה המתגלה יש לגרור את תתי המדורים למיקום הרצוי, ולבסוף ללחוץ על Click to order categories:
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-46.png" alt="">
                        </dd>
                        <dt class="key">התנהגות בגרסת נייד:</dt>  
                        <dd>
                            החל מרוחב מסך של 750 פיקסלים, משתמשים בתוסף Responsive Menu, שאת הגדרות התפריט בו אפשר לנהל מתוך מסך הניהול הנפתח כשלוחצים על הפריט בתפריט הימני בממשק הניהול
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-43.png" alt="">
                            <br>
                            התוסף הזה מציג את התפריט כ-3 פסים:
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-42.png" alt="">
                            <br>
                            שכאשר הוא נלחץ, נפתח כך:
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-44.png" alt="">
                            <br>
                            <strong>שים לב: </strong> בגרסת הנייד נעשה שימוש בתפריט אחר – לא התפריט המרכזי, אלא תפריט ששמו "מותאם לנייד":
                            <img src="<?php echo get_template_directory_uri(); ?>/site_documentation/header-elm-45.png" alt="">
                        </dd>
                        <dt class="key">הערות תכנותיות:</dt>  
                        <dd>הקוד שמייצר את תתי התפריטים יושב על ה-hook של wp_get_nav_menu_items.
                            <br>
                            כמו"כ, ישנם כמה פריטים בתפריט המרכזי המקבלים class מיוחד ע"מ לקבל עיצוב מיוחד. כך זה לגבי הבלוגים וכך זה לגבי הסוגיות.
                        </dd>                       
                    </dl>   

                </article>
                <article id="first-newest-post" class="section">
                    <h4 id="first-newest-post-title" class="section-title">ראשי-ראשי</h4>
                </article>
                <article id="newest-posts" class="section">
                    <h4 id="newest-posts-title" class="section-title">רשומות חדשות ראשי</h4>
                </article>
                <article id="load-more" class="section">
                    <h4 id="load-more-title" class="section-title">טען עוד</h4>
                </article>
                <article id="updates" class="section">
                    <h4 id="updates-title" class="section-title">עדכונים</h4>
                </article>
                <article id="countdown" class="section">
                    <h4 id="countdown-title" class="section-title">שעון עצר</h4>
                </article>
                <article id="ask-rabbi-link" class="section">
                    <h4 id="ask-rabbi-link-title" class="section-title">שאל את הרב</h4>
                </article>
                <article id="latest-comments" class="section">
                    <h4 id="latest-comments-title" class="section-title">תגוביות אחרונות</h4>
                </article>
                <article id="tag-cloud" class="section">
                    <h4 id="tag-cloud-title" class="section-title">ענן תגיות</h4>
                </article>
            </div>
            <!-- image mapster taken from here: http://www.outsharked.com/imagemapster-->
            <script src="<?php echo get_template_directory_uri(); ?>/site_documentation/jquery-2.1.1.min.js"></script> 
            <script src="<?php echo get_template_directory_uri(); ?>/site_documentation/jquery.imagemapster.min.js"></script> 
            <script src="<?php echo get_template_directory_uri(); ?>/site_documentation/jquery.colorbox-min.js"></script> 
            <script>
                $(document).ready(function () {
                    // imagemapster:
                    var img = $('img');
                    img.mapster({
                        fillOpacity: 0.7,
                        strokeColor: '3d6998',
                        strokeWidth: 3,
                        render_highlight: {
                            fillColor: '839DB6',
                            stroke: true
                        },
                        render_select: {
                            fill: false,
                            stroke: false
                        },
                        fadeInterval: 50
                    });
                    // colorbox:
                    $('area').colorbox({inline: true, width: "50%"});
                });
            </script>
        </div>
    </body>
</html>
