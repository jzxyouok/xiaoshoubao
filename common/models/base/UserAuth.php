<?php

namespace common\models\base;

use kriss\modules\auth\models\Auth;

class UserAuth extends Auth
{
    const COMPANY = 'company';
    const COMPANY_VIEW = 'companyView';

    const CLUE = 'clue';
    const CLUE_VIEW = 'clueView';
    const CLUE_PICK = 'cluePick';
    const CLUE_DISTRIBUTE = 'clueDistribute';

    const USER_CLUE = 'userClue';
    const USER_CLUE_VIEW = 'userClueView';
    const USER_CLUE_PICK = 'userCluePick';
    const USER_CLUE_RETURN = 'userClueReturn';
    const USER_CLUE_RECORD = 'userClueRecord';

    const DEPARTMENT = 'department';
    const DEPARTMENT_VIEW = 'departmentView';
    const DEPARTMENT_CREATE = 'departmentCreate';
    const DEPARTMENT_UPDATE = 'departmentUpdate';
    const DEPARTMENT_DELETE = 'departmentDelete';

    const USER = 'user';
    const USER_VIEW = 'userView';
    const USER_CREATE = 'userCreate';
    const USER_UPDATE = 'userUpdate';
    const USER_RESET_PASSWORD = 'userResetPassword';
    const USER_CHANGE_STATUS = 'userChangeStatus';
    const USER_UPDATE_ROLE = 'userUpdateRole';

    public static function getMessageData()
    {
        $old = parent::getMessageData();

        $new = [
            self::COMPANY => '企业查询',
            self::COMPANY_VIEW => '查看企业查询',

            self::CLUE => '线索公海池',
            self::CLUE_VIEW => '查看线索公海池',
            self::CLUE_PICK => '领取线索（到公海）',
            self::CLUE_DISTRIBUTE => '分配线索',

            self::USER_CLUE => '线索跟踪',
            self::USER_CLUE_VIEW => '查看线索跟踪',
            self::USER_CLUE_PICK => '领取线索（到私海）',
            self::USER_CLUE_RETURN => '退回线索',
            self::USER_CLUE_RECORD => '跟进线索',

            self::DEPARTMENT => '部门管理',
            self::DEPARTMENT_VIEW => '查看部门管理',
            self::DEPARTMENT_CREATE => '新增部门管理',
            self::DEPARTMENT_UPDATE => '修改部门管理',
            self::DEPARTMENT_DELETE => '删除部门管理',

            self::USER => '帐号管理',
            self::USER_VIEW => '查看帐号管理',
            self::USER_CREATE => '新增帐号管理',
            self::USER_UPDATE => '修改帐号管理',
            self::USER_RESET_PASSWORD => '重置密码帐号管理',
            self::USER_CHANGE_STATUS => '修改状态帐号管理',
            self::USER_UPDATE_ROLE => '授权帐号管理',
        ];

        return $old + $new;
    }

    public static function initData()
    {
        $old = parent::initData();

        $new = [
            [
                'id' => 10010, 'name' => self::COMPANY,
                'children' => [
                    ['id' => 100101, 'name' => self::COMPANY_VIEW],
                ]
            ],
            [
                'id' => 10020, 'name' => self::CLUE,
                'children' => [
                    ['id' => 100201, 'name' => self::CLUE_VIEW],
                    ['id' => 100202, 'name' => self::CLUE_PICK],
                    ['id' => 100203, 'name' => self::CLUE_DISTRIBUTE],
                ]
            ],
            [
                'id' => 10030, 'name' => self::USER_CLUE,
                'children' => [
                    ['id' => 100301, 'name' => self::USER_CLUE_VIEW],
                    ['id' => 100302, 'name' => self::USER_CLUE_PICK],
                    ['id' => 100303, 'name' => self::USER_CLUE_RETURN],
                    ['id' => 100304, 'name' => self::USER_CLUE_RECORD],
                ]
            ],
            [
                'id' => 10910, 'name' => self::DEPARTMENT,
                'children' => [
                    ['id' => 109101, 'name' => self::DEPARTMENT_VIEW],
                    ['id' => 109102, 'name' => self::DEPARTMENT_CREATE],
                    ['id' => 109103, 'name' => self::DEPARTMENT_UPDATE],
                    ['id' => 109104, 'name' => self::DEPARTMENT_DELETE],
                ]
            ],
            [
                'id' => 10920, 'name' => self::USER,
                'children' => [
                    ['id' => 109201, 'name' => self::USER_VIEW],
                    ['id' => 109202, 'name' => self::USER_CREATE],
                    ['id' => 109203, 'name' => self::USER_UPDATE],
                    ['id' => 109204, 'name' => self::USER_RESET_PASSWORD],
                    ['id' => 109205, 'name' => self::USER_CHANGE_STATUS],
                    ['id' => 109206, 'name' => self::USER_UPDATE_ROLE],
                ]
            ],
        ];

        return array_merge($old, $new);
    }
}