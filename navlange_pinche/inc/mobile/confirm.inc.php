<?php
global $_W,$_GPC;
$op = !empty($_GPC['op']) ? $_GPC['op'] : 'index';
if($op == 'index') {
    $travel = pdo_get('navlange_pinche_travel',array('id'=>$_GPC['id']));
    $member = pdo_fetchall("SELECT member.id as id, owner.car_series as car_series,owner.name as owner_name,owner.tel as owner_tel, pin.passenger_count as passenger_count,pin.price as price FROM " . tablename('navlange_pinche_member') . " AS member LEFT JOIN " . tablename('navlange_pinche_pin') . " AS pin ON member.pin_id=pin.id LEFT JOIN " . tablename('navlange_pinche_owner') . " AS owner ON pin.owner_id=owner.id WHERE member.travel_id=" . $_GPC['id']);
} else if ($op == 'confirm') {
    if($_GPC['pay_mode'] == '9') {
        pdo_update('navlange_pinche_travel',array('status'=>'1'),array('id'=>$_GPC['travel_id']));
        pdo_update('navlange_pinche_member',array('status'=>'1','price'=>$_GPC['to_pay'],'pay_mode'=>$_GPC['pay_mode']),array('id'=>$_GPC['member_id']));
        pdo_delete('navlange_pinche_member',array('travel_id'=>$_GPC['travel_id'],'status'=>'0'));
        $this->pin_success_notify($_GPC['travel_id']);
        message(error(0,''),'','ajax');
    } else if ($_GPC['pay_mode'] == '0') {
        $credit2 = $_W['member']['credit2'];
        if($credit2 < $_GPC['to_pay']) {
            message(error(1,'余额不足'),'','ajax');
        } else {
            mc_credit_update($_W['member']['uid'], 'credit2', -$_GPC['to_pay'], array($_W['member']['uid'], '余额付款',$_W['current_module']['name']));
            pdo_update('navlange_pinche_travel',array('status'=>'1'),array('id'=>$_GPC['travel_id']));
            pdo_update('navlange_pinche_member',array('status'=>'1','pay_mode'=>$_GPC['pay_mode']),array('id'=>$_GPC['member_id']));
            pdo_delete('navlange_pinche_member',array('travel_id'=>$_GPC['travel_id'],'status'=>'0'));
            $this->pin_success_notify($_GPC['travel_id']);
            message(error(0,''),'','ajax');
        }
    } else if ($_GPC['pay_mode'] == '1') {
        $member = pdo_get('navlange_pinche_member',array('id'=>$_GPC['member_id']));
        $pay_conf = pdo_get('navlange_pay_conf',array('uniacid'=>$_W['uniacid']));
        if($pay_conf['debug'] == '1') {
            $_GPC['to_pay'] = 0.01;
        }
        $params = array(
            'tid' => $member['pay_tid'],      //充值模块中的订单号，此号码用于业务模块中区分订单，交易的识别码
            'ordersn' => $member['sn'],  //收银台中显示的订单号
            'title' => '拼车付款：' . $_GPC['to_pay'] . '元',          //收银台中显示的标题
            'fee' => $_GPC['to_pay'],      //收银台中显示需要支付的金额,只能大于 0
            'user' => $_W['openid'],     //付款用户, 付款的用户名(选填项)
            'module' => $this->module['name'],
        );
        $log = pdo_get('core_paylog', array('uniacid' => $_W['uniacid'], 'module' => $this->module['name'], 'tid' => $params['tid']));
        //在pay方法中，要检测是否已经生成了paylog订单记录，如果没有需要插入一条订单数据
        //未调用系统pay方法的，可以将此代码放至自己的pay方法中，进行漏洞修复
        if (empty($log)) {
            $log = array(
                'uniacid' => $_W['uniacid'],
                'acid' => $_W['acid'],
                'openid' => $_W['openid'],
                'module' => $this->module['name'], //模块名称，请保证$this可用
                'tid' => $params['tid'],
                'fee' => $params['fee'],
                'card_fee' => $params['fee'],
                'status' => '0',
                'is_usecard' => '0',
            );
            pdo_insert('core_paylog', $log);
        }
        $reply['params'] = base64_encode(json_encode($params));
        message(error(2,$reply),'','ajax');
    }
}
$conf = pdo_get('navlange_pinche_conf',array('uniacid'=>$_W['uniacid']));
include $this->template('confirm');
?>