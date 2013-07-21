<?php

    // Disallow direct access to this file for security reasons
    if (!defined("IN_MYBB")) {
        die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
    }

    require_once MYBB_ROOT . 'inc/reptar/ReptarCore.php';

    $plugins->add_hook("postbit", "postbit_hook");
    $plugins->add_hook("showthread_end", "showthreadend_hook");
    $plugins->add_hook("xmlhttp", "ajaxrate_hook");

    function reptar_info() {
        return array(
            "name" => "Reptar",
            "description" => "An advanced reputation extension for MyBB.",
            "website" => "https://github.com/Tim-B/reptar",
            "author" => "Tim B.",
            "authorsite" => "https://github.com/Tim-B/reptar",
            "version" => "1.0",
            "guid" => "",
            "compatibility" => "*"
        );
    }

    function reptar_install() {
        ReptarCore::instance()->install();
    }

    function reptar_activate() {
        ReptarCore::instance()->activate();
    }

    function reptar_uninstall() {
        ReptarCore::instance()->uninstall();
    }

    function reptar_deactivate() {
        ReptarCore::instance()->deactivate();
    }

    function reptar_is_installed() {
        return ReptarCore::instance()->isInstalled();
    }

    function postbit_hook(&$post) {
        return ReptarCore::instance()->postbit($post);
    }

    function showthreadend_hook() {
        return ReptarCore::instance()->showthreadEnd();
    }

    function ajaxrate_hook() {
        return ReptarCore::instance()->rate();
    }


?>