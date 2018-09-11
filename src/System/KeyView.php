<?php

namespace XT\Core\System;

class KeyView
{
    const HEADER                = 'header_layout';
    const NAVSIDEBAR            = 'nav_sidebar';
    const CONTENT               = 'content';
    const CONTENTASIDE          = 'content_aside';
    const FOOTER                = 'footer_layout';
    const PARTIAL_HEAD          = 'partial_head';
    const PARTIAL_FOOT          = 'partial_foot';

    const key_addview           = 'view';
    const key_head              = 'partial_head';
    const key_foot              = 'partial_foot';
    const key_block             = 'block';
    const key_partial           = 'partial';
    const key_value             = 'value';
    const prefix_html_start     = 'start_block_';
    const prefix_html_end       = 'end_html_';


    public static function listEventTemplate() {
         return [
             self::prefix_html_start.self::HEADER,
             self::prefix_html_end.self::HEADER,
             self::prefix_html_start.self::NAVSIDEBAR,
             self::prefix_html_end.self::NAVSIDEBAR,
             self::prefix_html_start.self::CONTENTASIDE,
             self::prefix_html_end.self::CONTENTASIDE,
             self::prefix_html_start.self::FOOTER,
             self::prefix_html_end.self::FOOTER,
         ];
    }
}