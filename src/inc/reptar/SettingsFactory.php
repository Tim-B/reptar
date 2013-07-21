<?php

    class SettingsFactory {

        private static $groupName = 'reptar';
        private $gid;
        private $title = 'Reptar';
        private $description = 'Advanced Reputation System';
        private $default = 'no';
        private $order = 1;
        private $settings = array();
        public static $booleanType = 'yesno';
        public static $textType = 'text';

        public function __construct() {
            global $db;
            $group = array(
                'gid' => 'NULL',
                'name' => self::$groupName,
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
                $query .= $setting->getQuery(self::$groupName, $this->gid, $count++) . ', ';
            }
            $query = substr($query, 0, -2);
            $query .= ';';
            $db->query($query);
        }

        public static function removeSettings() {
            global $db;
            $result = $db->simple_select('settinggroups', 'gid', 'name=\'' . self::$groupName . '\'');
            $gid = $db->fetch_field($result, 'gid');
            $db->delete_query('settings', 'gid=' . $gid);
            $db->delete_query('settinggroups', 'gid=' . $gid);
        }

        public static function settingsGroupExists() {
            global $db;
            $result = $db->simple_select('settinggroups', 'gid', 'name=\'' . self::$groupName . '\'');
            $gid = $db->fetch_field($result, 'gid');
            if ($gid == null) {
                return false;
            } else {
                return true;
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