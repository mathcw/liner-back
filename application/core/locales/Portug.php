<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
* 
*/
class Portug
{
	public static $c = [
        '消息通知'=>'消息通知',
        '平台公告'=>'平台公告',
        '公告管理'=>'公告管理',
        '新增'=>'新增',
        '权限管理'=>'权限管理',
        '编辑'=>'编辑',
        '名称'=>'名称',
        '范围'=>'范围',
        '公司管理'=>'公司管理',
        '修改'=>'修改',
        '删除'=>'删除',
        '启停'=>'启停',
        '设置领导'=>'设置领导',
        '公司名称'=>'公司名称',
        '公司领导'=>'公司领导',
        '创建人'=>'创建人',
        '启停状态'=>'启停状态',
        '部门管理'=>'部门管理',
        '公司'=>'公司',
        '部门名称'=>'部门名称',
        '部门领导'=>'部门领导',
        '员工管理'=>'员工管理',
        '设置'=>'设置',
        '创建公司'=>'创建公司',
        '创建部门'=>'创建部门',
        '公司全称'=>'公司全称',
        '人员姓名'=>'人员姓名',
        '人员编号'=>'人员编号',
        '性别'=>'性别',
        '手机号'=>'手机号',
        '用户名'=>'用户名',
        '账号状态'=>'账号状态',
        '备注'=>'备注',
        '所在城市'=>'所在城市',
        '供应商编号'=>'供应商编号',
        '品牌名称'=>'品牌名称',
        '旗下部门'=>'旗下部门',
        '绑定分类'=>'绑定分类',
        '账号管理'=>'账号管理',
        '城市'=>'城市',
        '供应商公司'=>'供应商公司',
        '供应商部门'=>'供应商部门',
        '员工姓名'=>'员工姓名',
        '零售商编号'=>'零售商编号',
        '旗下注册用户'=>'旗下注册用户',
        '审核'=>'审核',
        '注册时间'=>'注册时间',
        '审核人'=>'审核人',
        '维护'=>'维护',
        '登陆日期'=>'登陆日期',
        '审核公司'=>'审核公司',
        '审核部门'=>'审核部门',
        '登陆时间'=>'登陆时间',
        '产品审核'=>'产品审核',
        '产品维护'=>'产品维护',
        '产品导航'=>'产品导航',
        '类型'=>'类型',
        '一级栏目'=>'一级栏目',
        '二级栏目'=>'二级栏目',
        '主题设置'=>'主题设置',
        '签证类型'=>'签证类型',
        '跟团游'=>'跟团游',
        '提交'=>'提交',
        '取消'=>'取消',
        '开团'=>'开团',
        '大交通'=>'大交通',
        '单订房'=>'单订房',
        '单签证'=>'单签证',
        '调价'=>'调价',
        '出团日期'=>'出团日期',
        '回团日期'=>'回团日期',
        '产品编号'=>'产品编号',
        '产品名称'=>'产品名称',
        '同行价'=>'同行价',
        '直客价'=>'直客价',
        '总位'=>'总位',
        '实报'=>'实报',
        '占位'=>'占位',
        '剩余'=>'剩余',
        '团态'=>'团态',
        '出发日期'=>'出发日期',
        '返程日期'=>'返程日期',
        '出发城市'=>'出发城市',
        '抵达城市'=>'抵达城市',
        '航班车次'=>'航班车次',
        '占位管理'=>'占位管理',
        '时限'=>'时限',
        '实报管理'=>'实报管理',
        '确认'=>'确认',
        '拒回'=>'拒回',
        '变更管理'=>'变更管理',
        '名单'=>'名单',
        '费用'=>'费用',
        '对账管理'=>'对账管理',
        '对账'=>'对账',
        '撤回'=>'撤回',
        '审核产品'=>'审核产品',
        '维护产品'=>'维护产品',
        '新增权限'=>'新增权限',
        '编辑权限'=>'编辑权限',
        '新增跟团游'=>'新增跟团游',
        '修改跟团游'=>'修改跟团游',
        '新增大交通'=>'新增大交通',
        '修改大交通'=>'修改大交通',
        '新增单订房'=>'新增单订房',
        '修改单订房'=>'修改单订房',
        '新增单签证'=>'新增单签证',
        '修改单签证'=>'修改单签证',
        '天数节点'=>'天数节点',
        '出发机场/车站'=>'出发机场/车站',
        '抵达机场/车站'=>'抵达机场/车站',
        '出发时间'=>'出发时间',
        '到达时间'=>'到达时间',
        '是否+1'=>'是否+1',
        '添加'=>'添加',
        '房型管理'=>'房型管理',
        '房型名称'=>'房型名称',
        '床型/数量'=>'床型',
        '面积'=>'面积',
        'WIFI'=>'WIFI',
        '入住人数'=>'入住人数',
        '可否加床'=>'可否加床',
        '产品信息'=>'产品信息',
        '天数晚数'=>'天数晚数',
        '出境/国内'=>'出境',
        '大类'=>'大类',
        '小类'=>'小类',
        '在售中'=>'在售中',
        '已过期'=>'已过期',
        '发布人'=>'发布人',
        '团期价格'=>'团期价格',
        '计划总位'=>'计划总位',
        '库存剩余'=>'库存剩余',
        '成团人数'=>'成团人数',
        '基准同行价'=>'基准同行价',
        '建议直客价'=>'建议直客价',
        '价格备注'=>'价格备注',
        '批量新增'=>'批量新增',
        '批量填充'=>'批量填充',
        '更多价格'=>'更多价格',
        '票种类型'=>'票种类型',
        '出抵城市'=>'出抵城市',
        '酒店名称'=>'酒店名称',
        '酒店星级'=>'酒店星级',
        '所在国家'=>'所在国家',
        '房型'=>'房型',
        '起售日期'=>'起售日期',
        '截止日期'=>'截止日期',
        '库存'=>'库存',
        '是否含早'=>'是否含早',
        '签证国家'=>'签证国家',
        '库存信息'=>'库存信息',
        '基准价格'=>'基准价格',
        '价格备注说明'=>'价格备注说明',
        '其他价格'=>'其他价格',
        '价格类型'=>'价格类型',
        '价格说明'=>'价格说明',
        '原价'=>'原价',
        '现同行价'=>'现同行价',
        '现直客价'=>'现直客价',
        '日志'=>'日志',
        '修改字段'=>'修改字段',
        '上次更新'=>'上次更新',
        '本次调整'=>'本次调整',
        '行政管理'  => '行政管理',
        '会员中心'  => '会员中心',
        '供应商管理'=> '供应商管理',
        '零售商管理'=> '零售商管理',
        '产品管理' =>  '产品管理',
        '业务配置' =>  '业务配置',
        '系统设置' =>  '系统设置',
        '店铺管理' =>  '店铺管理',
        '产品中心' =>  '产品中心',
        '订单管理' =>  '订单管理',
        '数据管理' =>  '数据管理',
		//动作
		'MISS'   => 'MISS',
		'UPLOAD' => 'UPLOAD',
		'DEL'    => 'DEL',
		'SAVE' => 'SAVE',
        'EXEC' => 'EXEC',
		//状态
		'SUC' => 'SUC',
		'FAI' => 'FAI',
		'ERR' => 'ERR',
		'DISABLED' => 'DISABLED',
		//名词
        'VERIFY_CODE' => 'VERIFY_CODE',
		'PV' => 'PV',
		'PARAM' => 'PARAM',
		'LIMIT' => 'LIMIT',
		'TYPE' => 'TYPE',
		'PIC' => 'PIC',
		'FILE' => 'FILE',
		'REC' => 'REC',
        'TOTAL' => 'TOTAL',
        
        'DUPLICATE' => 'DUPLICATE',
        'INCOMPLETE' => 'INCOMPLETE',
        'NO_DEFINED' => 'NO_DEFINED',
		'NO_DATA' => 'NO_DATA',
		'NO_PV' => 'NO_PV',
		'EXIST' => 'EXIST',
		'NOT_EXIST' => 'NOT_EXIST',
        'NOT_CFG' => 'NOT_CFG',
		'USING' => 'USING',
        'NO_CHANGE' => 'NO_CHANGE',

		'ACNT_OR_PW_ERR' => 'ACNT_OR_PW_ERR',
		'ACNT_DISABLED' => 'ACNT_DISABLED',

        'FLOW_NOT_SPECIFY' => 'FLOW_NOT_SPECIFY',
        'APPROVAL' => 'APPROVAL',
        'FLOW_NOT_ALLOW' => 'FLOW_NOT_ALLOW',
		'FLOW_WAIT' => 'FLOW_WAIT',
        'FLOW_APPROVED' => 'FLOW_APPROVED',
        'FLOW_REJECT' => 'FLOW_REJECT',
        'FLOW_REVOKE' => 'FLOW_REVOKE',

		//注册
		'SYS_INFO' => 'SYS_INFO',
		'SYS_CHANGED' => 'SYS_CHANGED',
		'SYS_BIND_IP' => 'SYS_BIND_IP',
		'SYS_BIND_IP_ERR' => 'SYS_BIND_IP_ERR',
		'USER_INFO' => 'USER_INFO',
		'REACH_MAX_USER' => 'REACH_MAX_USER',
        'USER_TYPE'=>  'USER_TYPE',
        'MOBILE'=>     'MOBILE',
        'PASSWORD'=>   'PASSWORD',
        'CAPTCHA'=>    'CAPTCHA'
	];
}