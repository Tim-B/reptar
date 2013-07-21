<?php

    class ReptarTemplates {

        private static $postbitTemplate = '
            <ul class="reptar_rate" rel="{$reptar[\'uid\']}">
                <li class="negative" rel="-2"></li>
                <li class="neutral" rel="-1"></li>
                <li class="positive" rel="1"></li>
                <li class="outstanding" rel="2"></li>
            </ul>
        ';

        private static $css = '
            ul.reptar_rate {
                margin: 0;
                padding: 0;
            }
            .reptar_rate li {
                display: inline;
                width: 21px;
                height: 21px;
                margin: 5px 2px 5px 2px;
                float: left;
                display: block;
                filter: url("data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\'><filter id=\'grayscale\'><feColorMatrix type=\'matrix\' values=\'0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0.3333 0.3333 0.3333 0 0 0 0 0 1 0\'/></filter></svg>#grayscale"); /* Firefox 10+, Firefox on Android */
                filter: gray; /* IE6-9 */
                -webkit-filter: grayscale(100%);
                cursor: pointer;
            }
            .reptar_rate li:hover, .reptar_rate li.active {
                filter: none;
                -webkit-filter: grayscale(0%);
            }
            .reptar_rate .negative {
                background-image: url(images/smilies/sad.gif);
            }
            .reptar_rate .neutral {
                background-image: url(images/smilies/undecided.gif);
            }
            .reptar_rate .positive {
                background-image: url(images/smilies/smile.gif);
            }
            .reptar_rate .outstanding {
                background-image: url(images/smilies/biggrin.gif);
            }
        ';

        public static function insert() {
            global $db, $theme, $config;

            require_once MYBB_ROOT . $config['admin_dir'] . "/inc/functions_themes.php";
            $insert_array = array('title' => 'reptar_postbit_rating', 'template' => $db->escape_string(self::$postbitTemplate), 'sid' => '-1', 'version' => '', 'dateline' => TIME_NOW);

            $db->insert_query("templates", $insert_array);

            $insert_array = array('name' => 'reptar.css', 'tid' => '1', 'stylesheet' => $db->escape_string(self::$css), 'cachefile' => 'reptar.css', 'lastmodified' => TIME_NOW);

            $sid = (int)$db->insert_query("themestylesheets", $insert_array);

            $query = $db->simple_select("themes", "tid");
            while ($theme = $db->fetch_array($query)) {
                update_theme_stylesheet_list($theme['tid']);
            }
        }

        public static function remove() {
            global $db, $config;

            require_once MYBB_ROOT . $config['admin_dir'] . "/inc/functions_themes.php";
            $db->delete_query("templates", "title = 'reptar_postbit_rating'");
            $db->delete_query("themestylesheets", "name = 'reptar.css'");
            $query = $db->simple_select("themes", "tid");
            while ($theme = $db->fetch_array($query)) {
                update_theme_stylesheet_list($theme['tid']);
            }
        }

    }

?>
