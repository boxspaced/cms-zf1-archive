<?php

use Boxspaced\EntityManager\Mapper\Conditions\Conditions;
use Boxspaced\EntityManager\Entity\AbstractEntity;

return [
    'strict' => false,
    'db' => [
        'driver' => 'Pdo_MySql',
        'database' => DB_NAME,
        'username' => DB_USERNAME,
        'password' => DB_PASSWORD,
        'hostname' => DB_HOST,
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
        ],
    ],
    'types' => [
        'App_Domain_User' => [
            'mapper' => [
                'params' => [
                    'table' => 'user',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'type' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'username' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'email' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'password' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'lastLogin' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'thisLogin' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'activated' => [
                        'type' => AbstractEntity::TYPE_BOOL,
                    ],
                    'everBeenActivated' => [
                        'type' => AbstractEntity::TYPE_BOOL,
                    ],
                    'registeredTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                ],
            ],
        ],
        'App_Domain_ProvisionalLocation' => [
            'mapper' => [
                'params' => [
                    'table' => 'provisional_location',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'to' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'beneathMenuItemId' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                ],
            ],
        ],
        'App_Domain_Module' => [
            'mapper' => [
                'params' => [
                    'table' => 'module',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'enabled' => [
                        'type' => AbstractEntity::TYPE_BOOL,
                    ],
                    'routeController' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'routeAction' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                ],
                'children' => [
                    'routes' => [
                        'type' => 'App_Domain_Route',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('module.id')->eq($id);
                        },
                    ],
                    'pages' => [
                        'type' => 'App_Domain_ModulePage',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentModule.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_Route' => [
            'mapper' => [
                'params' => [
                    'table' => 'route',
                    'columns' => [
                        'module' => 'module_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'slug' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'identifier' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'module' => [
                        'type' => 'App_Domain_Module',
                    ],
                ],
            ],
        ],
        'App_Domain_Menu' => [
            'mapper' => [
                'params' => [
                    'table' => 'menu',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'primary' => [
                        'type' => AbstractEntity::TYPE_BOOL,
                    ],
                ],
                'children' => [
                    'items' => [
                        'type' => 'App_Domain_MenuItem',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentMenuItem')->isNull()
                                ->field('menu.id')->eq($id)
                                ->order('orderBy', Conditions::ORDER_ASC);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_MenuItem' => [
            'mapper' => [
                'params' => [
                    'table' => 'menu_item',
                    'columns' => [
                        'menu' => 'menu_id',
                        'parentMenuItem' => 'parent_menu_item_id',
                        'route' => 'route_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'orderBy' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'navText' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'external' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'menu' => [
                        'type' => 'App_Domain_Menu',
                    ],
                    'parentMenuItem' => [
                        'type' => 'App_Domain_MenuItem',
                    ],
                    'route' => [
                        'type' => 'App_Domain_Route',
                    ],
                ],
                'children' => [
                    'items' => [
                        'type' => 'App_Domain_MenuItem',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentMenuItem.id')->eq($id)
                                ->order('orderBy', Conditions::ORDER_ASC);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_Block' => [
            'mapper' => [
                'params' => [
                    'table' => 'block',
                    'columns' => [
                        'versionOf' => 'version_of_id',
                        'type' => 'type_id',
                        'author' => 'author_id',
                        'template' => 'template_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'liveFrom' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'expiresEnd' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'workflowStage' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'status' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'authoredTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'lastModifiedTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'publishedTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'rollbackStopPoint' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'versionOf' => [
                        'type' => 'App_Domain_Block',
                    ],
                    'type' => [
                        'type' => 'App_Domain_BlockType',
                    ],
                    'author' => [
                        'type' => 'App_Domain_User',
                    ],
                    'template' => [
                        'type' => 'App_Domain_BlockTemplate',
                    ],
                ],
                'children' => [
                    'fields' => [
                        'type' => 'App_Domain_BlockField',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentBlock.id')->eq($id);
                        },
                    ],
                    'notes' => [
                        'type' => 'App_Domain_BlockNote',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentBlock.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_BlockType' => [
            'mapper' => [
                'params' => [
                    'table' => 'block_type',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'icon' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                ],
                'children' => [
                    'templates' => [
                        'type' => 'App_Domain_BlockTemplate',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('forType.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_BlockField' => [
            'mapper' => [
                'params' => [
                    'table' => 'block_field',
                    'columns' => [
                        'parentBlock' => 'block_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'value' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'parentBlock' => [
                        'type' => 'App_Domain_Block',
                    ],
                ],
            ],
        ],
        'App_Domain_BlockNote' => [
            'mapper' => [
                'params' => [
                    'table' => 'block_note',
                    'columns' => [
                        'parentBlock' => 'block_id',
                        'user' => 'user_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'text' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'createdTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'parentBlock' => [
                        'type' => 'App_Domain_Block',
                    ],
                    'user' => [
                        'type' => 'App_Domain_User',
                    ],
                ],
            ],
        ],
        'App_Domain_BlockTemplate' => [
            'mapper' => [
                'params' => [
                    'table' => 'block_template',
                    'columns' => [
                        'forType' => 'for_type_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'viewScript' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'forType' => [
                        'type' => 'App_Domain_BlockType',
                    ],
                ],
            ],
        ],
        'App_Domain_Item' => [
            'mapper' => [
                'params' => [
                    'table' => 'item',
                    'columns' => [
                        'versionOf' => 'version_of_id',
                        'type' => 'type_id',
                        'author' => 'author_id',
                        'provisionalLocation' => 'provisional_location_id',
                        'route' => 'route_id',
                        'template' => 'template_id',
                        'teaserTemplate' => 'teaser_template_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'colourScheme' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'navText' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'metaKeywords' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'metaDescription' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'title' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'publishedTo' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'liveFrom' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'expiresEnd' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'workflowStage' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'status' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'authoredTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'lastModifiedTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'publishedTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'rollbackStopPoint' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'versionOf' => [
                        'type' => 'App_Domain_Item',
                    ],
                    'type' => [
                        'type' => 'App_Domain_ItemType',
                    ],
                    'author' => [
                        'type' => 'App_Domain_User',
                    ],
                    'provisionalLocation' => [
                        'type' => 'App_Domain_ProvisionalLocation',
                    ],
                    'route' => [
                        'type' => 'App_Domain_Route',
                    ],
                    'template' => [
                        'type' => 'App_Domain_ItemTemplate',
                    ],
                    'teaserTemplate' => [
                        'type' => 'App_Domain_ItemTeaserTemplate',
                    ],
                ],
                'children' => [
                    'fields' => [
                        'type' => 'App_Domain_ItemField',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentItem.id')->eq($id);
                        },
                    ],
                    'parts' => [
                        'type' => 'App_Domain_ItemPart',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentItem.id')->eq($id);
                        },
                    ],
                    'notes' => [
                        'type' => 'App_Domain_ItemNote',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentItem.id')->eq($id);
                        },
                    ],
                    'freeBlocks' => [
                        'type' => 'App_Domain_ItemFreeBlock',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentItem.id')->eq($id);
                        },
                    ],
                    'blockSequences' => [
                        'type' => 'App_Domain_ItemBlockSequence',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentItem.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_ItemType' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_type',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'icon' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'multipleParts' => [
                        'type' => AbstractEntity::TYPE_BOOL,
                    ],
                ],
                'children' => [
                    'templates' => [
                        'type' => 'App_Domain_ItemTemplate',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('forType.id')->eq($id);
                        },
                    ],
                    'teaserTemplates' => [
                        'type' => 'App_Domain_ItemTeaserTemplate',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('forType.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_ItemField' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_field',
                    'columns' => [
                        'parentItem' => 'item_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'value' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'parentItem' => [
                        'type' => 'App_Domain_Item',
                    ],
                ],
            ],
        ],
        'App_Domain_ItemPart' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_part',
                    'columns' => [
                        'parentItem' => 'item_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'orderBy' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'parentItem' => [
                        'type' => 'App_Domain_Item',
                    ],
                ],
                'children' => [
                    'fields' => [
                        'type' => 'App_Domain_ItemPartField',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentPart.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_ItemPartField' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_part_field',
                    'columns' => [
                        'parentPart' => 'part_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'value' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'parentPart' => [
                        'type' => 'App_Domain_ItemPart',
                    ],
                ],
            ],
        ],
        'App_Domain_ItemNote' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_note',
                    'columns' => [
                        'parentItem' => 'item_id',
                        'user' => 'user_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'text' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'createdTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'parentItem' => [
                        'type' => 'App_Domain_Item',
                    ],
                    'user' => [
                        'type' => 'App_Domain_User',
                    ],
                ],
            ],
        ],
        'App_Domain_ItemTemplate' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_template',
                    'columns' => [
                        'forType' => 'for_type_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'viewScript' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'forType' => [
                        'type' => 'App_Domain_ItemType',
                    ],
                ],
                'children' => [
                    'blocks' => [
                        'type' => 'App_Domain_ItemTemplateBlock',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentTemplate.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_ItemTeaserTemplate' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_teaser_template',
                    'columns' => [
                        'forType' => 'for_type_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'viewScript' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'forType' => [
                        'type' => 'App_Domain_ItemType',
                    ],
                ],
            ],
        ],
        'App_Domain_ItemTemplateBlock' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_template_block',
                    'columns' => [
                        'parentTemplate' => 'template_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'adminLabel' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'sequence' => [
                        'type' => AbstractEntity::TYPE_BOOL,
                    ],
                    'parentTemplate' => [
                        'type' => 'App_Domain_ItemTemplate',
                    ],
                ],
            ],
        ],
        'App_Domain_ItemFreeBlock' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_free_block',
                    'columns' => [
                        'parentItem' => 'item_id',
                        'templateBlock' => 'template_block_id',
                        'block' => 'block_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'parentItem' => [
                        'type' => 'App_Domain_Item',
                    ],
                    'templateBlock' => [
                        'type' => 'App_Domain_ItemTemplateBlock',
                    ],
                    'block' => [
                        'type' => 'App_Domain_Block',
                    ],
                ],
            ],
        ],
        'App_Domain_ItemBlockSequence' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_block_sequence',
                    'columns' => [
                        'parentItem' => 'item_id',
                        'templateBlock' => 'template_block_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'parentItem' => [
                        'type' => 'App_Domain_Item',
                    ],
                    'templateBlock' => [
                        'type' => 'App_Domain_ItemTemplateBlock',
                    ],
                ],
                'children' => [
                    'blocks' => [
                        'type' => 'App_Domain_ItemBlockSequenceBlock',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentBlockSequence.id')->eq($id)
                                ->order('orderBy', Conditions::ORDER_ASC);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_ItemBlockSequenceBlock' => [
            'mapper' => [
                'params' => [
                    'table' => 'item_block_sequence_block',
                    'columns' => [
                        'parentBlockSequence' => 'block_sequence_id',
                        'block' => 'block_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'orderBy' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'parentBlockSequence' => [
                        'type' => 'App_Domain_ItemBlockSequence',
                    ],
                    'block' => [
                        'type' => 'App_Domain_Block',
                    ],
                ],
            ],
        ],
        'App_Domain_Course' => [
            'mapper' => [
                'params' => [
                    'table' => 'course',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'category' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'title' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'code' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'day' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'startDate' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'time' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'numWeeks' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'hoursPerWeek' => [
                        'type' => AbstractEntity::TYPE_FLOAT,
                    ],
                    'venue' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'fee' => [
                        'type' => AbstractEntity::TYPE_FLOAT,
                    ],
                    'concession' => [
                        'type' => AbstractEntity::TYPE_FLOAT,
                    ],
                    'dayTime' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                ],
            ],
        ],
        'App_Domain_WhatsOn' => [
            'mapper' => [
                'params' => [
                    'table' => 'whats_on',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'category' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'activity' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'dayTime' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'venue' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'age' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'specificDate' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                ],
            ],
        ],
        'App_Domain_DigitalGalleryImage' => [
            'mapper' => [
                'params' => [
                    'table' => 'digital_gallery_image',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'keywords' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'title' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'description' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'imageNo' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'credit' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'copyright' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'price' => [
                        'type' => AbstractEntity::TYPE_FLOAT,
                    ],
                    'imageName' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                ],
                'children' => [
                    'categories' => [
                        'type' => 'App_Domain_DigitalGalleryImageCategory',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('image.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_DigitalGalleryCategory' => [
            'mapper' => [
                'params' => [
                    'table' => 'digital_gallery_category',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'type' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'text' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                ],
            ],
        ],
        'App_Domain_DigitalGalleryImageCategory' => [
            'mapper' => [
                'params' => [
                    'table' => 'digital_gallery_image_category',
                    'columns' => [
                        'image' => 'image_id',
                        'category' => 'category_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'image' => [
                        'type' => 'App_Domain_DigitalGalleryImage',
                    ],
                    'category' => [
                        'type' => 'App_Domain_DigitalGalleryCategory',
                    ],
                ],
            ],
        ],
        'App_Domain_DigitalGalleryOrder' => [
            'mapper' => [
                'params' => [
                    'table' => 'digital_gallery_order',
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'dayPhone' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'email' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'message' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'createdTime' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'code' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                ],
                'children' => [
                    'items' => [
                        'type' => 'App_Domain_DigitalGalleryOrderItem',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('order.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_DigitalGalleryOrderItem' => [
            'mapper' => [
                'params' => [
                    'table' => 'digital_gallery_order_item',
                    'columns' => [
                        'order' => 'order_id',
                        'image' => 'image_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'order' => [
                        'type' => 'App_Domain_DigitalGalleryOrder',
                    ],
                    'image' => [
                        'type' => 'App_Domain_DigitalGalleryImage',
                    ],
                ],
            ],
        ],
        'App_Domain_ModulePage' => [
            'mapper' => [
                'params' => [
                    'table' => 'module_page',
                    'columns' => [
                        'parentModule' => 'module_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'parentModule' => [
                        'type' => 'App_Domain_Module',
                    ],
                ],
                'children' => [
                    'freeBlocks' => [
                        'type' => 'App_Domain_ModulePageFreeBlock',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentModulePage.id')->eq($id);
                        },
                    ],
                    'blockSequences' => [
                        'type' => 'App_Domain_ModulePageBlockSequence',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentModulePage.id')->eq($id);
                        },
                    ],
                    'blocks' => [
                        'type' => 'App_Domain_ModulePageBlock',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentModulePage.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_ModulePageBlock' => [
            'mapper' => [
                'params' => [
                    'table' => 'module_page_block',
                    'columns' => [
                        'parentModulePage' => 'module_page_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'name' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'adminLabel' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'sequence' => [
                        'type' => AbstractEntity::TYPE_BOOL,
                    ],
                    'parentModulePage' => [
                        'type' => 'App_Domain_ModulePage',
                    ],
                ],
            ],
        ],
        'App_Domain_ModulePageFreeBlock' => [
            'mapper' => [
                'params' => [
                    'table' => 'module_page_free_block',
                    'columns' => [
                        'parentModulePage' => 'module_page_id',
                        'modulePageBlock' => 'module_page_block_id',
                        'block' => 'block_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'parentModulePage' => [
                        'type' => 'App_Domain_ModulePage',
                    ],
                    'modulePageBlock' => [
                        'type' => 'App_Domain_ModulePageBlock',
                    ],
                    'block' => [
                        'type' => 'App_Domain_Block',
                    ],
                ],
            ],
        ],
        'App_Domain_ModulePageBlockSequence' => [
            'mapper' => [
                'params' => [
                    'table' => 'module_page_block_sequence',
                    'columns' => [
                        'parentModulePage' => 'module_page_id',
                        'modulePageBlock' => 'module_page_block_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'parentModulePage' => [
                        'type' => 'App_Domain_ModulePage',
                    ],
                    'modulePageBlock' => [
                        'type' => 'App_Domain_ModulePageBlock',
                    ],
                ],
                'children' => [
                    'blocks' => [
                        'type' => 'App_Domain_ModulePageBlockSequenceBlock',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('parentBlockSequence.id')->eq($id)
                                ->order('orderBy', Conditions::ORDER_ASC);
                        }
                    ],
                ],
            ],
        ],
        'App_Domain_ModulePageBlockSequenceBlock' => [
            'mapper' => [
                'params' => [
                    'table' => 'module_page_block_sequence_block',
                    'columns' => [
                        'parentBlockSequence' => 'block_sequence_id',
                        'block' => 'block_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'orderBy' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'parentBlockSequence' => [
                        'type' => 'App_Domain_ModulePageBlockSequence',
                    ],
                    'block' => [
                        'type' => 'App_Domain_Block',
                    ],
                ],
            ],
        ],
        'App_Domain_HelpdeskTicket' => [
            'mapper' => [
                'params' => [
                    'table' => 'helpdesk_ticket',
                    'columns' => [
                        'user' => 'user_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'status' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'subject' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'issue' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'createdAt' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'user' => [
                        'type' => 'App_Domain_User',
                    ],
                ],
                'children' => [
                    'comments' => [
                        'type' => 'App_Domain_HelpdeskTicketComment',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('ticket.id')->eq($id);
                        },
                    ],
                    'attachments' => [
                        'type' => 'App_Domain_HelpdeskTicketAttachment',
                        'conditions' => function ($id) {
                            return (new Conditions())
                                ->field('ticket.id')->eq($id);
                        },
                    ],
                ],
            ],
        ],
        'App_Domain_HelpdeskTicketComment' => [
            'mapper' => [
                'params' => [
                    'table' => 'helpdesk_ticket_comment',
                    'columns' => [
                        'ticket' => 'ticket_id',
                        'user' => 'user_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'comment' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'createdAt' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'ticket' => [
                        'type' => 'App_Domain_HelpdeskTicket',
                    ],
                    'user' => [
                        'type' => 'App_Domain_User',
                    ],
                ],
            ],
        ],
        'App_Domain_HelpdeskTicketAttachment' => [
            'mapper' => [
                'params' => [
                    'table' => 'helpdesk_ticket_attachment',
                    'columns' => [
                        'ticket' => 'ticket_id',
                        'user' => 'user_id',
                    ],
                ],
            ],
            'entity' => [
                'fields' => [
                    'id' => [
                        'type' => AbstractEntity::TYPE_INT,
                    ],
                    'fileName' => [
                        'type' => AbstractEntity::TYPE_STRING,
                    ],
                    'createdAt' => [
                        'type' => AbstractEntity::TYPE_DATETIME,
                    ],
                    'ticket' => [
                        'type' => 'App_Domain_HelpdeskTicket',
                    ],
                    'user' => [
                        'type' => 'App_Domain_User',
                    ],
                ],
            ],
        ],
    ],
];
