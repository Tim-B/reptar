<?php

    require_once MYBB_ROOT . 'inc/reptar/SettingsFactory.php';
    require_once MYBB_ROOT . 'inc/reptar/ReptarTemplates.php';
    require_once MYBB_ROOT . "/inc/adminfunctions_templates.php";

    class ReptarCore {

        private static $instance = null;

        private $threadUIDs = array();

        private function __construct() {

        }

        public static function instance() {
            if (self::$instance == null) {
                self::$instance = new ReptarCore();
            }
            return self::$instance;
        }

        public function install() {
            $settingsFactory = new SettingsFactory();

            $settingsFactory->addSetting()
                ->setName('active')
                ->setTitle('Reptar enabled')
                ->setDescription('Reptar will only function when set to true')
                ->setOption(SettingsFactory::$booleanType)
                ->setValue(false);

            $settingsFactory->commitSettings();
            ReptarTemplates::insert();
            rebuild_settings();
        }

        public function activate() {
            find_replace_templatesets("postbit", "#" . preg_quote('{$post[\'user_details\']}') . "#i", '{$post[\'user_details\']}{$post[\'reptar_rate\']}');
            find_replace_templatesets("postbit_classic", "#" . preg_quote('{$post[\'user_details\']}') . "#i", '{$post[\'user_details\']}{$post[\'reptar_rate\']}');
            find_replace_templatesets("headerinclude", "#" . preg_quote('{$stylesheets}') . "#i", '<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/reptar.js"></script>{$stylesheets}');
        }

        public function uninstall() {
            SettingsFactory::removeSettings();
            ReptarTemplates::remove();
        }

        public function deactivate() {
            find_replace_templatesets("postbit", "#" . preg_quote('{$post[\'reptar_rate\']}') . "#i", '', 0);
            find_replace_templatesets("postbit_classic", "#" . preg_quote('{$post[\'reptar_rate\']}') . "#i", '', 0);
            find_replace_templatesets("headerinclude", "#" . preg_quote('<script type="text/javascript" src="{$mybb->settings[\'bburl\']}/jscripts/reptar.js"></script>') . "#i", '', 0);
        }

        public function isInstalled() {
            return SettingsFactory::settingsGroupExists();
        }

        public function postbit(&$post) {
            if (!in_array($post['uid'], $this->threadUIDs)) {
                $this->threadUIDs[] = $post['uid'];
            }
            global $templates;
            $reptar = array();
            $reptar['uid'] = $post['uid'];
            eval('$post[\'reptar_rate\']  = "' . $templates->get('reptar_postbit_rating') . '";');
        }

        public function showthreadEnd() {

        }
    }