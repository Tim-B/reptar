<?php

    require_once('Reptar_TestBase.php');

    class Reptar_UnitTest extends Reptar_TestBase {

        public function tearDown() {

        }

        protected function generateMyBBInstance() {
            $mybb = $this->getMock('MyBB');
            $mybb->input = array();
            $mybb->user = array();
            $mybb->usergroups = array();
            return $mybb;
        }

        protected function generateDBInstance() {
            $db = $this->getMock('DB', array('simple_select', 'fetch_array', 'insert_query'));
            return $db;
        }

    }