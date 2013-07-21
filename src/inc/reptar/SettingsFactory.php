<?php

    class SettingsFactory {

        const GROUP_NAME = 'reptar';
        private $gid;
        private $title = 'Reptar';
        private $description = 'Advanced Reputation System';
        private $default = 'no';
        private $order = 1;
        private $settings = array();
        const TYPE_BOOLEAN = 'yesno';
        const TYPE_TEXT = 'text';

        public function __construct() {
            global $db;
            $group = array(
                'gid' => 'NULL',
                'name' => self::GROUP_NAME,
                'title' => $this->title,
                'description' => $this->description,
                'disporder' => $this->order,
                'isdefault' => $this->order,
            );
            $db->insert_query('settinggroups', $group);
            $this->gid = $db->insert_id();
        }

        public function addSetting() {
            $setting = new Setting();
            $this->settings[] = $setting;
            return $setting;
        }

        public function commitSettings() {
            global $db;
            $count = 0;
            $query = 'INSERT INTO ' . TABLE_PREFIX . 'settings (';
            $query .= 'name, title, description, optionscode, value, disporder, gid, isdefault ) ';
            $query .= 'VALUES ';
            foreach ($this->settings as $setting) {
                $query .= $setting->getQuery(self::GROUP_NAME, $this->gid, $count++) . ', ';
            }
            $query = substr($query, 0, -2);
            $query .= ';';
            $db->query($query);
        }

        public static function removeSettings() {
            global $db;
            $result = $db->simple_select('settinggroups', 'gid', 'name=\'' . self::GROUP_NAME . '\'');
            $gid = $db->fetch_field($result, 'gid');
            if ($gid != null) {
                $db->delete_query('settings', 'gid=' . $gid);
                $db->delete_query('settinggroups', 'gid=' . $gid);
            }
        }

        public static function settingsGroupExists() {
            global $db;
            $result = $db->simple_select('settinggroups', 'gid', 'name=\'' . self::GROUP_NAME . '\'');
            $gid = $db->fetch_field($result, 'gid');
            if ($gid == null) {
                return false;
            } else {
                return true;
            }
        }

        public static function setupTables() {
            global $db;
            $query = 'CREATE TABLE ' . TABLE_PREFIX . 'reptar_ratings (
            `reptar_id` int(10) UNSIGNED NOT NULL auto_increment,
            `uid` int(10) UNSIGNED NOT NULL,
            `target_uid` int(10) UNSIGNED NOT NULL,
            `rate_time` int(10) UNSIGNED NOT NULL,
            `rating` smallint(5) UNSIGNED NOT NULL,
            PRIMARY KEY  (`reptar_id`)
            ) ENGINE=MyISAM';
            $db->write_query($query);
        }

        public static function removeTables() {
            global $db;

            if ($db->table_exists('reptar_ratings')) {
                $db->drop_table('reptar_ratings');
            }
        }

    }

    class Setting {

        private $name;
        private $title;
        private $description;
        private $option;
        private $value;

        public function setName($name) {
            $this->name = $name;
            return $this;
        }

        public function setTitle($title) {
            $this->title = $title;
            return $this;
        }

        public function setDescription($description) {
            $this->description = $description;
            return $this;
        }

        public function setOption($option) {
            $this->option = $option;
            return $this;
        }

        public function setValue($value) {
            $this->value = $value;
            return $this;
        }

        public function getQuery($prefix, $gid, $order) {
            $output = '(';
            $output .= $this->prepValue($prefix . '_' . $this->name);
            $output .= $this->prepValue($this->title);
            $output .= $this->prepValue($this->description);
            $output .= $this->prepValue($this->option);
            $output .= $this->prepValue($this->value);
            $output .= $this->prepValue($order);
            $output .= $this->prepValue($gid);
            $output .= $this->prepValue(1, true) . ')';
            return $output;
        }

        private function prepValue($value, $last = false) {
            $return = '\'' . htmlspecialchars($value) . '\'';
            if (!$last) {
                $return .= ', ';
            }
            return $return;
        }

    }