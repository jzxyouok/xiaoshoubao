<?php

namespace common\models\base;

use kriss\modules\auth\models\Auth;

class AdminAuth extends Auth
{
    const COMPANY = 'company';
    const COMPANY_VIEW = 'companyView';
    const COMPANY_CREATE = 'companyCreate';
    const COMPANY_UPDATE = 'companyUpdate';
    const COMPANY_DELETE = 'companyDelete';
    const COMPANY_CHANGE_STATUS = 'companyChangeStatus';
    const COMPANY_SYNC = 'companySync';

    const COMPANY_UPLOAD = 'companyUpload';
    const COMPANY_UPLOAD_VIEW = 'companyUploadView';
    const COMPANY_UPLOAD_IMPORT = 'companyUploadImport';
    const COMPANY_UPLOAD_UPDATE = 'companyUploadUpdate';
    const COMPANY_UPLOAD_DELETE = 'companyUploadDelete';
    const COMPANY_UPLOAD_EMPTY = 'companyUploadEmpty';
    const COMPANY_UPLOAD_SYNC = 'companyUploadSync';

    const JOB_SCHEDULE = 'jobSchedule';
    const JOB_SCHEDULE_VIEW = 'jobScheduleView';

    const TEAM = 'team';
    const TEAM_VIEW = 'teamView';
    const TEAM_CREATE = 'teamCreate';
    const TEAM_UPDATE = 'teamUpdate';
    const TEAM_RESET_PASSWORD = 'teamResetPassword';
    const TEAM_CHANGE_STATUS = 'teamChangeStatus';

    const ADMIN = 'admin';
    const ADMIN_VIEW = 'adminView';
    const ADMIN_CREATE = 'adminCreate';
    const ADMIN_UPDATE = 'adminUpdate';
    const ADMIN_RESET_PASSWORD = 'adminResetPassword';
    const ADMIN_CHANGE_STATUS = 'adminChangeStatus';
    const ADMIN_UPDATE_ROLE = 'adminUpdateRole';

    public static function getMessageData()
    {
        $old = parent::getMessageData();

        $new = [
            self::COMPANY => '企业管理',
            self::COMPANY_VIEW => '查看企业',
            self::COMPANY_CREATE => '新增企业',
            self::COMPANY_UPDATE => '修改企业',
            self::COMPANY_DELETE => '删除企业',
            self::COMPANY_CHANGE_STATUS => '修改企业状态',
            self::COMPANY_SYNC => '同步到查询库',

            self::COMPANY_UPLOAD => '企业上传管理',
            self::COMPANY_UPLOAD_VIEW => '查看企业上传',
            self::COMPANY_UPLOAD_IMPORT => '导入临时企业',
            self::COMPANY_UPLOAD_UPDATE => '修改临时企业',
            self::COMPANY_UPLOAD_DELETE => '删除临时企业',
            self::COMPANY_UPLOAD_EMPTY => '清空临时企业',
            self::COMPANY_UPLOAD_SYNC => '同步到正式库',

            self::JOB_SCHEDULE => '任务管理',
            self::JOB_SCHEDULE_VIEW => '查看任务',

            self::TEAM => '团队管理',
            self::TEAM_VIEW => '查看团队',
            self::TEAM_CREATE => '新增团队',
            self::TEAM_UPDATE => '修改团队',
            self::TEAM_RESET_PASSWORD => '重置团队管理员密码',
            self::TEAM_CHANGE_STATUS => '修改团队状态',

            self::ADMIN => '管理员管理',
            self::ADMIN_VIEW => '查看管理员',
            self::ADMIN_CREATE => '新增管理员',
            self::ADMIN_UPDATE => '修改管理员',
            self::ADMIN_RESET_PASSWORD => '重置密码管理员',
            self::ADMIN_CHANGE_STATUS => '修改管理员状态',
            self::ADMIN_UPDATE_ROLE => '授权管理员',
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
                    ['id' => 100102, 'name' => self::COMPANY_CREATE],
                    ['id' => 100103, 'name' => self::COMPANY_UPDATE],
                    ['id' => 100104, 'name' => self::COMPANY_DELETE],
                    ['id' => 100105, 'name' => self::COMPANY_CHANGE_STATUS],
                    ['id' => 100106, 'name' => self::COMPANY_SYNC],
                ]
            ],
            [
                'id' => 10011, 'name' => self::COMPANY_UPLOAD,
                'children' => [
                    ['id' => 100111, 'name' => self::COMPANY_UPLOAD_VIEW],
                    ['id' => 100112, 'name' => self::COMPANY_UPLOAD_IMPORT],
                    ['id' => 100113, 'name' => self::COMPANY_UPLOAD_UPDATE],
                    ['id' => 100114, 'name' => self::COMPANY_UPLOAD_DELETE],
                    ['id' => 100115, 'name' => self::COMPANY_UPLOAD_EMPTY],
                    ['id' => 100116, 'name' => self::COMPANY_UPLOAD_SYNC],
                ]
            ],
            [
                'id' => 10015, 'name' => self::JOB_SCHEDULE,
                'children' => [
                    ['id' => 100151, 'name' => self::JOB_SCHEDULE_VIEW],
                ]
            ],
            [
                'id' => 10020, 'name' => self::TEAM,
                'children' => [
                    ['id' => 100201, 'name' => self::TEAM_VIEW],
                    ['id' => 100202, 'name' => self::TEAM_CREATE],
                    ['id' => 100203, 'name' => self::TEAM_UPDATE],
                    ['id' => 100204, 'name' => self::TEAM_RESET_PASSWORD],
                    ['id' => 100205, 'name' => self::TEAM_CHANGE_STATUS],
                ]
            ],
            [
                'id' => 10990, 'name' => self::ADMIN,
                'children' => [
                    ['id' => 109901, 'name' => self::ADMIN_VIEW],
                    ['id' => 109902, 'name' => self::ADMIN_CREATE],
                    ['id' => 109903, 'name' => self::ADMIN_UPDATE],
                    ['id' => 109904, 'name' => self::ADMIN_RESET_PASSWORD],
                    ['id' => 109905, 'name' => self::ADMIN_CHANGE_STATUS],
                    ['id' => 109906, 'name' => self::ADMIN_UPDATE_ROLE],
                ]
            ],
        ];

        return array_merge($old, $new);
    }
}