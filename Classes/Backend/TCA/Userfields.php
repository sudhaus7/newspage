<?php
/**
 * Created by PhpStorm.
 * User: frank
 * Date: 08/03/16
 * Time: 12:09
 */

namespace SUDHAUS7\Sudhaus7Newspage\Backend\TCA;

use SUDHAUS7\Thememanager\Tools\Compiler;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use SUDHAUS7\Sudhaus7Base\Tools\Globals;

class Userfields
{
    public function tagIcons($PA, &$fObj)
    {
        $id = sha1($PA['itemFormElName']);
        $formField = '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"><link href="/typo3conf/ext/sudhaus7_newspage/Resources/Public/Css/socicons.css" rel="stylesheet"><link href="/typo3conf/ext/sudhaus7_newspage/Resources/Public/Css/Logos-LK.css" rel="stylesheet"><link href="/typo3conf/ext/sudhaus7_newspage/Resources/Public/Css/font-awesome.min.css" rel="stylesheet">';
        $formField .= '<style type="text/css">

#block-'.$id.' {
    width: 80%;
    height: 140px;
   
    overflow: hidden;
    position: absolute;
    left: 19px;
    top: 22px;
    background-color: #eee;
    border: 1px solid #333;
    z-index: 99999;
}

#block-'.$id.' input {
    position: absolute;
    width: calc(100% - 30px);
    height: 30px;
    top:0;
    left:0;
    background-color:rgba(0,0,0,0);
    color:#000;
    border-bottom: 1px solid #000;
    border-left: none;
    border-right: none;
    border-top: none;
    margin-left: 5px;
    
}
#block-'.$id.' > div {
    overflow-x: hidden;
    overflow-y: auto;
    margin: 40px 0 0 0;
    height: 100px;
}
#block-'.$id.' > div >  ul {
    list-style-type: none;
        margin:  0;
    padding: 5px;
    
    
}


#block-'.$id.' ul li {
    list-style-type: none;
    float: left;
    width: 100%;
    
     margin: 2px;
    padding: 4px;
    height: 35px;
}
#block-' . $id . ' ul li.clickable {

    
   
    width: 35px;
    cursor: pointer;
    
}
#block-'.$id.' ul li:first-child {
    border: 1px solid black;
}
#block-'.$id.' ul li.active {
    border: 1px solid red;
}
#block-' . $id . ' ul li [class^="socicon-"]:before, i.fa  {
    font-size: 20px;
}

#close-' . $id . ' {
    width: 25px;
    float:right;

}

</style>';

        $formField .= '<script type="text/javascript">
TYPO3.jQuery(document).ready(function(){
 
    TYPO3.jQuery("#block-' . $id . ' ul li.clickable").click(function() {

        TYPO3.jQuery("#block-'.$id.' ul li.active").removeClass("active");

        TYPO3.jQuery(this).addClass("active");
        var text = TYPO3.jQuery(this).html();

        TYPO3.jQuery("#field-'.$id.'").val(text).change();
        TYPO3.jQuery("#button-'.$id.'").html(text.length > 0 ? text : "&nbsp;");
        TYPO3.jQuery("#block-'.$id.'").toggle();
    });
    TYPO3.jQuery("#button-'.$id.'").click(function(e) {
    
        console.log("click");
    
        e.preventDefault();
        e.stopPropagation();
        TYPO3.jQuery("#block-'.$id.'").toggle();
    });
    TYPO3.jQuery("#search-'.$id.'").on("keyup",function(e) {
        
        var val = TYPO3.jQuery(this).val();
        if (val.length > 0) {
            var re = new RegExp(val,"i");
            TYPO3.jQuery("#block-' . $id . ' li.clickable").hide().each(function(i,li) {
                var title = TYPO3.jQuery(li).attr("title");
                //console.log(title);
                if (title.match(re)) {
                    TYPO3.jQuery(li).show();
                }
            });
        } else {
         TYPO3.jQuery("#block-' . $id . ' li").show();
        }
        
    });
    
    TYPO3.jQuery("#close-' . $id . '").on("click",function(e) {
        e.preventDefault();
        e.stopPropagation();
        TYPO3.jQuery("#block-' . $id . ' li").show();
        TYPO3.jQuery("#search-' . $id . '").val("");
        TYPO3.jQuery("#block-' . $id . '").toggle();
    });

});
</script>';
        $formField  .= '<div style="padding: 5px; position: relative;">';

        $formField .= '<input type="hidden" id="field-'.$id.'" name="' . $PA['itemFormElName'] . '"';
        $formField .= ' value="' . htmlspecialchars($PA['itemFormElValue']) . '"';
        $formField .= ' onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '"';
        $formField .= $PA['onFocus'];
        $formField .= ' />';

        $default = !empty($PA['itemFormElValue']) ? $PA['itemFormElValue'] : '&nbsp;';

        $formField .= '<button  id="button-'.$id.'" class="material-icons">'.$default.'</button>';

        $file = dirname(dirname(dirname(dirname(__FILE__)))).'/Resources/Public/font/material-icons/MaterialIcons-Regular.ijmap';
        $buf = file_get_contents($file);
        $json = json_decode($buf, true);


        $formField .= '<div id="block-' . $id . '" style="display:none;"><input type="search" name="" placeholder="Stichwortsuche" value="" id="search-' . $id . '"/><button id="close-' . $id . '">X</button>';
        $formField .= '<div><ul>';


        $active = empty($PA['itemFormElValue']) ? ' class="active clickable"' : ' class="clickable"';
        $formField .= '<li title="Kein Icon" '.$active.'><i class="material-icons"></i></li>';

        // $formField .= '<li>Baukasten Icons</li>';

        // $val = '<i class="emh-kreuz">+</i>';
        $active = $PA['itemFormElValue'] == $val ? ' class="active clickable"' : ' class="clickable"';

        $lkicons = ['elkwue','kirche1','kirche2','kirche3','kirche4','kirche5','kirche6','kirche7'];

        $formField .= '<li>Landeskirche Icons</li>';

        foreach ($lkicons as $lkicon) {
            $val = '<i class="lkicon-' . strtolower($lkicon) . '"></i>';
            $active = $PA['itemFormElValue'] == $val ? ' class="active clickable"' : ' class="clickable"';
            $formField .= '<li title="' . ucfirst($lkicon) . '" ' . $active . '>' . $val . '</li>';
        }

        $formField .= '<li>Material Icons (standard Icons)</li>';

        foreach ($json['icons'] as $k=>$a) {
            $val = '<i class="material-icons">' . str_replace(' ', '_', strtolower($a['name'])) . '</i>';
            $active = $PA['itemFormElValue'] == $val ? ' class="active clickable"' : ' class="clickable"';
            $formField .= '<li title="' . $a['name'] . '" ' . $active . '>' . $val . '</li>';
        }

        $formField .= '<li>Social Icons</li>';

        $socicion = array(
            'modelmayhem','mixcloud','drupal','swarm','istock','yammer','ello','stackoverflow','persona','triplej','houzz','rss','paypal','odnoklassniki','airbnb','periscope','outlook','coderwall','tripadvisor','appnet','goodreads','tripit','lanyrd','slideshare','buffer','disqus','vkontakte','whatsapp','patreon','storehouse','pocket','mail','blogger','technorati','reddit','dribbble','stumbleupon','digg','envato','behance','delicious','deviantart','forrst','play','zerply','wikipedia','apple','flattr','github','renren','friendfeed','newsvine','identica','bebo','zynga','steam','xbox','windows','qq','douban','meetup','playstation','android','snapchat','twitter','facebook','googleplus','pinterest','foursquare','yahoo','skype','yelp','feedburner','linkedin','viadeo','xing','myspace','soundcloud','spotify','grooveshark','lastfm','youtube','vimeo','dailymotion','vine','flickr','500px','wordpress','tumblr','twitch','8tracks','amazon','icq','smugmug','ravelry','weibo','baidu','angellist','ebay','imdb','stayfriends','residentadvisor','google','yandex','sharethis','bandcamp','itunes','deezer','telegram','openid','amplement','viber','zomato','draugiem','endomodo','filmweb','stackexchange','wykop','teamspeak','teamviewer','ventrilo','younow','raidcall','mumble','medium','bebee','hitbox','reverbnation','formulr','instagram','battlenet','chrome','discord','issuu','macos','firefox','opera','keybase','alliance','livejournal','googlephotos','horde','etsy','zapier','google-scholar','researchgate','wechat','strava','line','lyft','uber','songkick','viewbug','googlegroups','quora','diablo','blizzard','hearthstone','heroes','overwatch','warcraft','starcraft','beam','curse','player','streamjar','nintendo','hellocoton',
        );

        foreach ($socicion as $v) {
            $val = '<i class="socicon-' . strtolower($v) . '"></i>';
            $active = $PA['itemFormElValue'] == $val ? ' class="active clickable"' : ' class="clickable"';
            $formField .= '<li title="' . ucfirst($v) . '" ' . $active . '>' . $val . '</li>';
        }

        $formField .= '<li>Font Awesome Icons</li>';


        $fontawesome = array(
            "glass",
            "music",
            "search",
            "envelope-o",
            "heart",
            "star",
            "star-o",
            "user",
            "film",
            "th-large",
            "th",
            "th-list",
            "check",
            "remove",
            "close",
            "times",
            "search-plus",
            "search-minus",
            "power-off",
            "signal",
            "gear",
            "cog",
            "trash-o",
            "home",
            "file-o",
            "clock-o",
            "road",
            "download",
            "arrow-circle-o-down",
            "arrow-circle-o-up",
            "inbox",
            "play-circle-o",
            "rotate-right",
            "repeat",
            "refresh",
            "list-alt",
            "lock",
            "flag",
            "headphones",
            "volume-off",
            "volume-down",
            "volume-up",
            "qrcode",
            "barcode",
            "tag",
            "tags",
            "book",
            "bookmark",
            "print",
            "camera",
            "font",
            "bold",
            "italic",
            "text-height",
            "text-width",
            "align-left",
            "align-center",
            "align-right",
            "align-justify",
            "list",
            "dedent",
            "outdent",
            "indent",
            "video-camera",
            "photo",
            "image",
            "picture-o",
            "pencil",
            "map-marker",
            "adjust",
            "tint",
            "edit",
            "pencil-square-o",
            "share-square-o",
            "check-square-o",
            "arrows",
            "step-backward",
            "fast-backward",
            "backward",
            "play",
            "pause",
            "stop",
            "forward",
            "fast-forward",
            "step-forward",
            "eject",
            "chevron-left",
            "chevron-right",
            "plus-circle",
            "minus-circle",
            "times-circle",
            "check-circle",
            "question-circle",
            "info-circle",
            "crosshairs",
            "times-circle-o",
            "check-circle-o",
            "ban",
            "arrow-left",
            "arrow-right",
            "arrow-up",
            "arrow-down",
            "mail-forward",
            "share",
            "expand",
            "compress",
            "plus",
            "minus",
            "asterisk",
            "exclamation-circle",
            "gift",
            "leaf",
            "fire",
            "eye",
            "eye-slash",
            "warning",
            "exclamation-triangle",
            "plane",
            "calendar",
            "random",
            "comment",
            "magnet",
            "chevron-up",
            "chevron-down",
            "retweet",
            "shopping-cart",
            "folder",
            "folder-open",
            "arrows-v",
            "arrows-h",
            "bar-chart-o",
            "bar-chart",
            "twitter-square",
            "facebook-square",
            "camera-retro",
            "key",
            "gears",
            "cogs",
            "comments",
            "thumbs-o-up",
            "thumbs-o-down",
            "star-half",
            "heart-o",
            "sign-out",
            "linkedin-square",
            "thumb-tack",
            "external-link",
            "sign-in",
            "trophy",
            "github-square",
            "upload",
            "lemon-o",
            "phone",
            "square-o",
            "bookmark-o",
            "phone-square",
            "twitter",
            "facebook-f",
            "facebook",
            "github",
            "unlock",
            "credit-card",
            "feed",
            "rss",
            "hdd-o",
            "bullhorn",
            "bell",
            "certificate",
            "hand-o-right",
            "hand-o-left",
            "hand-o-up",
            "hand-o-down",
            "arrow-circle-left",
            "arrow-circle-right",
            "arrow-circle-up",
            "arrow-circle-down",
            "globe",
            "wrench",
            "tasks",
            "filter",
            "briefcase",
            "arrows-alt",
            "group",
            "users",
            "chain",
            "link",
            "cloud",
            "flask",
            "cut",
            "scissors",
            "copy",
            "files-o",
            "paperclip",
            "save",
            "floppy-o",
            "square",
            "navicon",
            "reorder",
            "bars",
            "list-ul",
            "list-ol",
            "strikethrough",
            "underline",
            "table",
            "magic",
            "truck",
            "pinterest",
            "pinterest-square",
            "google-plus-square",
            "google-plus",
            "money",
            "caret-down",
            "caret-up",
            "caret-left",
            "caret-right",
            "columns",
            "unsorted",
            "sort",
            "sort-down",
            "sort-desc",
            "sort-up",
            "sort-asc",
            "envelope",
            "linkedin",
            "rotate-left",
            "undo",
            "legal",
            "gavel",
            "dashboard",
            "tachometer",
            "comment-o",
            "comments-o",
            "flash",
            "bolt",
            "sitemap",
            "umbrella",
            "paste",
            "clipboard",
            "lightbulb-o",
            "exchange",
            "cloud-download",
            "cloud-upload",
            "user-md",
            "stethoscope",
            "suitcase",
            "bell-o",
            "coffee",
            "cutlery",
            "file-text-o",
            "building-o",
            "hospital-o",
            "ambulance",
            "medkit",
            "fighter-jet",
            "beer",
            "h-square",
            "plus-square",
            "angle-double-left",
            "angle-double-right",
            "angle-double-up",
            "angle-double-down",
            "angle-left",
            "angle-right",
            "angle-up",
            "angle-down",
            "desktop",
            "laptop",
            "tablet",
            "mobile-phone",
            "mobile",
            "circle-o",
            "quote-left",
            "quote-right",
            "spinner",
            "circle",
            "mail-reply",
            "reply",
            "github-alt",
            "folder-o",
            "folder-open-o",
            "smile-o",
            "frown-o",
            "meh-o",
            "gamepad",
            "keyboard-o",
            "flag-o",
            "flag-checkered",
            "terminal",
            "code",
            "mail-reply-all",
            "reply-all",
            "star-half-empty",
            "star-half-full",
            "star-half-o",
            "location-arrow",
            "crop",
            "code-fork",
            "unlink",
            "chain-broken",
            "question",
            "info",
            "exclamation",
            "superscript",
            "subscript",
            "eraser",
            "puzzle-piece",
            "microphone",
            "microphone-slash",
            "shield",
            "calendar-o",
            "fire-extinguisher",
            "rocket",
            "maxcdn",
            "chevron-circle-left",
            "chevron-circle-right",
            "chevron-circle-up",
            "chevron-circle-down",
            "html5",
            "css3",
            "anchor",
            "unlock-alt",
            "bullseye",
            "ellipsis-h",
            "ellipsis-v",
            "rss-square",
            "play-circle",
            "ticket",
            "minus-square",
            "minus-square-o",
            "level-up",
            "level-down",
            "check-square",
            "pencil-square",
            "external-link-square",
            "share-square",
            "compass",
            "toggle-down",
            "caret-square-o-down",
            "toggle-up",
            "caret-square-o-up",
            "toggle-right",
            "caret-square-o-right",
            "euro",
            "eur",
            "gbp",
            "dollar",
            "usd",
            "rupee",
            "inr",
            "cny",
            "rmb",
            "yen",
            "jpy",
            "ruble",
            "rouble",
            "rub",
            "won",
            "krw",
            "bitcoin",
            "btc",
            "file",
            "file-text",
            "sort-alpha-asc",
            "sort-alpha-desc",
            "sort-amount-asc",
            "sort-amount-desc",
            "sort-numeric-asc",
            "sort-numeric-desc",
            "thumbs-up",
            "thumbs-down",
            "youtube-square",
            "youtube",
            "xing",
            "xing-square",
            "youtube-play",
            "dropbox",
            "stack-overflow",
            "instagram",
            "flickr",
            "adn",
            "bitbucket",
            "bitbucket-square",
            "tumblr",
            "tumblr-square",
            "long-arrow-down",
            "long-arrow-up",
            "long-arrow-left",
            "long-arrow-right",
            "apple",
            "windows",
            "android",
            "linux",
            "dribbble",
            "skype",
            "foursquare",
            "trello",
            "female",
            "male",
            "gittip",
            "gratipay",
            "sun-o",
            "moon-o",
            "archive",
            "bug",
            "vk",
            "weibo",
            "renren",
            "pagelines",
            "stack-exchange",
            "arrow-circle-o-right",
            "arrow-circle-o-left",
            "toggle-left",
            "caret-square-o-left",
            "dot-circle-o",
            "wheelchair",
            "vimeo-square",
            "turkish-lira",
            "try",
            "plus-square-o",
            "space-shuttle",
            "slack",
            "envelope-square",
            "wordpress",
            "openid",
            "institution",
            "bank",
            "university",
            "mortar-board",
            "graduation-cap",
            "yahoo",
            "google",
            "reddit",
            "reddit-square",
            "stumbleupon-circle",
            "stumbleupon",
            "delicious",
            "digg",
            "pied-piper-pp",
            "pied-piper-alt",
            "drupal",
            "joomla",
            "language",
            "fax",
            "building",
            "child",
            "paw",
            "spoon",
            "cube",
            "cubes",
            "behance",
            "behance-square",
            "steam",
            "steam-square",
            "recycle",
            "automobile",
            "car",
            "cab",
            "taxi",
            "tree",
            "spotify",
            "deviantart",
            "soundcloud",
            "database",
            "file-pdf-o",
            "file-word-o",
            "file-excel-o",
            "file-powerpoint-o",
            "file-photo-o",
            "file-picture-o",
            "file-image-o",
            "file-zip-o",
            "file-archive-o",
            "file-sound-o",
            "file-audio-o",
            "file-movie-o",
            "file-video-o",
            "file-code-o",
            "vine",
            "codepen",
            "jsfiddle",
            "life-bouy",
            "life-buoy",
            "life-saver",
            "support",
            "life-ring",
            "circle-o-notch",
            "ra",
            "resistance",
            "rebel",
            "ge",
            "empire",
            "git-square",
            "git",
            "y-combinator-square",
            "yc-square",
            "hacker-news",
            "tencent-weibo",
            "qq",
            "wechat",
            "weixin",
            "send",
            "paper-plane",
            "send-o",
            "paper-plane-o",
            "history",
            "circle-thin",
            "header",
            "paragraph",
            "sliders",
            "share-alt",
            "share-alt-square",
            "bomb",
            "soccer-ball-o",
            "futbol-o",
            "tty",
            "binoculars",
            "plug",
            "slideshare",
            "twitch",
            "yelp",
            "newspaper-o",
            "wifi",
            "calculator",
            "paypal",
            "google-wallet",
            "cc-visa",
            "cc-mastercard",
            "cc-discover",
            "cc-amex",
            "cc-paypal",
            "cc-stripe",
            "bell-slash",
            "bell-slash-o",
            "trash",
            "copyright",
            "at",
            "eyedropper",
            "paint-brush",
            "birthday-cake",
            "area-chart",
            "pie-chart",
            "line-chart",
            "lastfm",
            "lastfm-square",
            "toggle-off",
            "toggle-on",
            "bicycle",
            "bus",
            "ioxhost",
            "angellist",
            "cc",
            "shekel",
            "sheqel",
            "ils",
            "meanpath",
            "buysellads",
            "connectdevelop",
            "dashcube",
            "forumbee",
            "leanpub",
            "sellsy",
            "shirtsinbulk",
            "simplybuilt",
            "skyatlas",
            "cart-plus",
            "cart-arrow-down",
            "diamond",
            "ship",
            "user-secret",
            "motorcycle",
            "street-view",
            "heartbeat",
            "venus",
            "mars",
            "mercury",
            "intersex",
            "transgender",
            "transgender-alt",
            "venus-double",
            "mars-double",
            "venus-mars",
            "mars-stroke",
            "mars-stroke-v",
            "mars-stroke-h",
            "neuter",
            "genderless",
            "facebook-official",
            "pinterest-p",
            "whatsapp",
            "server",
            "user-plus",
            "user-times",
            "hotel",
            "bed",
            "viacoin",
            "train",
            "subway",
            "medium",
            "yc",
            "y-combinator",
            "optin-monster",
            "opencart",
            "expeditedssl",
            "battery-4",
            "battery-full",
            "battery-3",
            "battery-three-quarters",
            "battery-2",
            "battery-half",
            "battery-1",
            "battery-quarter",
            "battery-0",
            "battery-empty",
            "mouse-pointer",
            "i-cursor",
            "object-group",
            "object-ungroup",
            "sticky-note",
            "sticky-note-o",
            "cc-jcb",
            "cc-diners-club",
            "clone",
            "balance-scale",
            "hourglass-o",
            "hourglass-1",
            "hourglass-start",
            "hourglass-2",
            "hourglass-half",
            "hourglass-3",
            "hourglass-end",
            "hourglass",
            "hand-grab-o",
            "hand-rock-o",
            "hand-stop-o",
            "hand-paper-o",
            "hand-scissors-o",
            "hand-lizard-o",
            "hand-spock-o",
            "hand-pointer-o",
            "hand-peace-o",
            "trademark",
            "registered",
            "creative-commons",
            "gg",
            "gg-circle",
            "tripadvisor",
            "odnoklassniki",
            "odnoklassniki-square",
            "get-pocket",
            "wikipedia-w",
            "safari",
            "chrome",
            "firefox",
            "opera",
            "internet-explorer",
            "tv",
            "television",
            "contao",
            "500px",
            "amazon",
            "calendar-plus-o",
            "calendar-minus-o",
            "calendar-times-o",
            "calendar-check-o",
            "industry",
            "map-pin",
            "map-signs",
            "map-o",
            "map",
            "commenting",
            "commenting-o",
            "houzz",
            "vimeo",
            "black-tie",
            "fonticons",
            "reddit-alien",
            "edge",
            "credit-card-alt",
            "codiepie",
            "modx",
            "fort-awesome",
            "usb",
            "product-hunt",
            "mixcloud",
            "scribd",
            "pause-circle",
            "pause-circle-o",
            "stop-circle",
            "stop-circle-o",
            "shopping-bag",
            "shopping-basket",
            "hashtag",
            "bluetooth",
            "bluetooth-b",
            "percent",
            "gitlab",
            "wpbeginner",
            "wpforms",
            "envira",
            "universal-access",
            "wheelchair-alt",
            "question-circle-o",
            "blind",
            "audio-description",
            "volume-control-phone",
            "braille",
            "assistive-listening-systems",
            "asl-interpreting",
            "american-sign-language-interpreting",
            "deafness",
            "hard-of-hearing",
            "deaf",
            "glide",
            "glide-g",
            "signing",
            "sign-language",
            "low-vision",
            "viadeo",
            "viadeo-square",
            "snapchat",
            "snapchat-ghost",
            "snapchat-square",
            "pied-piper",
            "first-order",
            "yoast",
            "themeisle",
            "google-plus-circle",
            "google-plus-official",
        );

        foreach ($fontawesome as $v) {
            $val = '<i class="fa fa-' . strtolower($v) . '"></i>';
            $active = $PA['itemFormElValue'] == $val ? ' class="active clickable"' : ' class="clickable"';
            $formField .= '<li title="' . ucfirst($v) . '" ' . $active . '>' . $val . '</li>';
        }


        $formField .= '</ul></div>';
        $formField .= '</div>';
        $formField .= '</div>';
        return $formField;
    }
}
