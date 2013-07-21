<?php

    require_once MYBB_ROOT . 'inc/reptar/SettingsFactory.php';
    require_once MYBB_ROOT . 'inc/reptar/ReptarTemplates.php';
    require_once MYBB_ROOT . "/inc/adminfunctions_templates.php";

    class ReptarCore {

        private static $instance = null;

        private $threadUIDs = array();

        private $uid = null;

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
                ->setOption(SettingsFactory::TYPE_BOOLEAN)
                ->setValue(false);

            $settingsFactory->commitSettings();
            SettingsFactory::setupTables();
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
            SettingsFactory::removeTables();
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
            global $templates, $mybb;
            if (!$mybb->usergroup['cangivereputations']) {
                return;
            }
            $reptar = array();
            $reptar['uid'] = $post['uid'];
            eval('$post[\'reptar_rate\']  = "' . $templates->get('reptar_postbit_rating') . '";');
        }

        public function showthreadEnd() {

        }

        public function rate() {
            global $mybb, $lang, $charset;
            if ($mybb->input['action'] == "reptar_rate") {

                $this->uid = (int)$mybb->user['uid'];

                header("Content-type: text/plain; charset={$charset}");

                if (!$mybb->usergroup['cangivereputations']) {
                    $this->ajaxError($lang->add_no_permission);
                }

                if (!verify_post_check($mybb->input['my_post_key'], true)) {
                    $this->ajaxError($lang->invalid_post_code);
                }

                $targetID = $mybb->input['uid'];
                $value = (int)$mybb->input['value'];
                $target = $this->getRepRecord($targetID);
                if ($target == null) {
                    $this->insertRating($targetID, $value);
                } else {
                   $this->updateRating($target['reptar_id'], $value);
                }
            }
        }

        private function getRepRecord($target_uid) {
            global $db;
            $target_uid = (int)$target_uid;
            $result = $db->simple_select('reptar_ratings', '*', 'target_uid = ' . $target_uid . ' AND uid = ' . $this->uid);
            return $db->fetch_array($result);
        }

        private function insertRating($target_uid, $rating) {
            global $db;
            $db->insert_query(
                'reptar_ratings',
                array(
                    'uid' => $this->uid,
                    'target_uid' => $target_uid,
                    'rating' => $rating,
                    'rate_time' => time(),
                )
            );

        }

        private function updateRating($rep_id, $rating) {
            global $db;
            $db->update_query(
                'reptar_ratings',
                array(
                    'rating' => $rating,
                    'rate_time' => time(),
                ),
                'reptar_id = ' . $rep_id
            );
        }

        private function ajaxError($message) {
            global $charset;

            // Send our headers.
            header("Content-type: text/xml; charset={$charset}");

            // Send the error message.
            echo "<error>" . $message . "</error>";

            // Exit
            exit;
        }
    }