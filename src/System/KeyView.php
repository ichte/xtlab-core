<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 30-Aug-18
 * Time: 1:12 PM
 */

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
    const prefix_html_start     = 'start_block_html_';
    const prefix_html_end       = 'end_block_html_';
}