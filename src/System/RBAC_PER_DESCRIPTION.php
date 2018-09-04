<?php
namespace XT\Core\System;


class RBAC_PER_DESCRIPTION
{
    public static $desctiption = [
        RBAC_PERMISSION::ADMIN_INDEX => 'Admin Page',
        RBAC_PERMISSION::BASIC_SETTING => 'Basic Setting Page',
        RBAC_PERMISSION::DB_ALTER => 'Alter tables data (database)',
        RBAC_PERMISSION::LAYOUT_EDIT => ''
    ];

    public static $rolepermission = [
        RBAC_ROLE::Administrator => [
            RBAC_PERMISSION::DB_ALTER
        ] ,



        RBAC_ROLE::Moderator => [
            RBAC_PERMISSION::LAYOUT_EDIT
        ] ,



        RBAC_ROLE::Editor => [
            RBAC_PERMISSION::ADMIN_INDEX,
            RBAC_PERMISSION::BASIC_SETTING
        ] ,


        RBAC_ROLE::Member => [

        ] ,

        RBAC_ROLE::Banned => [

        ],
    ];

    public static $rolehiearchy = [
        RBAC_ROLE::Administrator => [
            RBAC_ROLE::Moderator
        ],

        RBAC_ROLE::Moderator => [
            RBAC_ROLE::Editor
        ],

        RBAC_ROLE::Editor => [
            RBAC_ROLE::Member
        ]

    ];
}
