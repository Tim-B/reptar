<?php

    require_once('./inc/Reptar_UnitTest.php');

    function verify_post_check($code, $silent = false) {
        return $code == 'abc123';
    }


    class CoreTest extends Reptar_UnitTest {

        public function setUp() {
            parent::setUp();

            if (!defined('MYBB_ROOT')) {
                define('MYBB_ROOT', dirname(dirname(__FILE__)) . "/../src/");
            }
            require_once('../src/inc/reptar/ReptarCore.php');
        }

        public function testSingleton() {
            $core = ReptarCore::instance();
            $this->assertTrue($core instanceof ReptarCore);

            $core2 = ReptarCore::instance();

            $this->assertEquals($core, $core2);
        }

        /**
         * @runInSeparateProcess
         */
        public function testRate() {
            global $mybb, $lang, $charset, $db;

            $mybb = $this->generateMyBBInstance();
            $db = $this->generateDBInstance();
            $lang = $this->getMock('MyLanguage');
            $charset = 'charset';

            $mybb->input['action'] = 'reptar_rate';
            $mybb->input['uid'] = 10;
            $mybb->input['my_post_key'] = 'abc123';
            $mybb->input['value'] = 2;
            $mybb->user['uid'] = 5;
            $mybb->usergroup['cangivereputations'] = true;

            $db->expects($this->once())
                ->method('simple_select')
                ->will($this->returnValue(null));

            $db->expects($this->once())
                ->method('fetch_array')
                ->will($this->returnValue(null));

            $db->expects($this->once())
                ->method('insert_query')
                ->will($this->returnValue(null));

            $core = ReptarCore::instance();

            $core->rate();
        }

    }