<?php

namespace biqu\TicketPlatform\Mtx;

use SoapClient;
use biqu\TicketPlatform\DataFormat;

class Transform
{
    /**
     * 注册会员卡
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T14:55:59+0800
     *
     * @param    DataFormat  $data
     * @return   DataFormat
     */
    public function cardRegister(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            if ($arr['ResultCode'] == 1) {
                throw new \Exception('1.cardRegister%' . $arr['ResultMsg'], 602);
            } else {
                throw new \Exception($arr['ResultCode'], 602);
            }
        }

        return $data->setData([
            'result_code' => 0,
            'message'     => '注册会员卡成功',
            'card_number' => $arr['AccountNo']
        ]);
    }

    /**
     * 登录会员卡
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T17:01:38+0800
     *
     * @param    DataFormat     $data
     * @param    integer        $cardType
     * @return   DataFormat
     */
    public function cardInfo(DataFormat $data, $cardType, $cardId)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 602);
        }

        return $data->setData([
            'result_code'       => 0,
            'message'           => '操作成功',
            'card_id'           => $cardId,
            'type'              => $cardType,
            'level_no'          => $arr['AccLevelCode'],
            'level_name'        => $arr['AccLevelName'],
            'member_name'       => $arr['MemberName'],
            'phone'             => $arr['PhoneNumber'],
            'balance'           => $arr['AccBalance'],
            'score'             => $arr['AccIntegral'],
            'expiration_time'   => $arr['ExpirationTime'],
            'open_cinema'       => '',
        ]);
    }

    /**
     * 会员卡交易记录
     * wenqiang
     * 2017-03-18T16:46:26+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardTransRecord(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            if ($arr['ResultCode'] == 1) {
                throw new \Exception('1.cardTransRecord', 602);
            } else {
                throw new \Exception($arr['ResultCode'], 602);
            }
        }

        $info = array_get($arr, 'CardTraceRecords.CardTraceRecord');

        if (is_null($info)) {
            return $data->setData([
                    'result_code'   => 0,
                    'message'       => '操作成功',
                    'record'        => []
                ]);
        }

        $ret = [];
        if ($this->isIndexArr($info)) {
            foreach ($info as $key => $val) {
                $ret[] = [
                    'trace_no'          =>   $val['TraceNo'],
                    'trace_type_no'     =>   $val['TraceTypeNo'],
                    'trace_type_name'   =>   $val['TraceTypeName'],
                    'old_price'         =>   $val['OldPrice'],
                    'price'             =>   $val['Price'],
                    'trace_date'        =>   $val['TraceDate'],
                    'trace_time'        =>   $val['TraceTime'],
                    'old_score'         =>   $val['OldSocre'],
                    'score'             =>   $val['Score'],
                    'trace_price'       =>   $val['TracePrice'],
                    'user_code'         =>   $val['UserCode'],
                    'gift_cod'          =>   $val['GiftCod'],
                    'feature_date'      =>   $val['FeatureDate'],
                    'feature_time'      =>   $val['FeatureTime'],
                    'feature_no'        =>   $val['FeatureNo'],
                    'film_no'           =>   $val['FilmNo'],
                    'cinema_name'       =>   $val['CinemaName'],
                    'ticket_num'        =>   $val['TicketNum'],
                    'trace_memo'        =>   $val['TraceMemo'],
                    'acc_level_name'    =>   $val['AccLevelName'],
                    'acc_level_code'    =>   $val['AccLevelCode'],
                ];
            }
        } else {
            $ret[] = [
                    'trace_no'          =>   $info['TraceNo'],
                    'trace_type_no'     =>   $info['TraceTypeNo'],
                    'trace_type_name'   =>   $info['TraceTypeName'],
                    'old_price'         =>   $info['OldPrice'],
                    'price'             =>   $info['Price'],
                    'trace_date'        =>   $info['TraceDate'],
                    'trace_time'        =>   $info['TraceTime'],
                    'old_score'         =>   $info['OldSocre'],
                    'score'             =>   $info['Score'],
                    'trace_price'       =>   $info['TracePrice'],
                    'user_code'         =>   $info['UserCode'],
                    'gift_cod'          =>   $info['GiftCod'],
                    'feature_date'      =>   $info['FeatureDate'],
                    'feature_time'      =>   $info['FeatureTime'],
                    'feature_no'        =>   $info['FeatureNo'],
                    'film_no'           =>   $info['FilmNo'],
                    'cinema_name'       =>   $info['CinemaName'],
                    'ticket_num'        =>   $info['TicketNum'],
                    'trace_memo'        =>   $info['TraceMemo'],
                    'acc_level_name'    =>   $info['AccLevelName'],
                    'acc_level_code'    =>   $info['AccLevelCode'],
                ];
        }

        return $data->setData([
            'result_code'   => $arr['ResultCode'],
            'message'       => '操作成功',
            'record'        => $ret
        ]);
    }

    /**
     * 会员卡充值
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-10T20:12:29+0800
     *
     * @param    DataFormat $data
     * @return   DataFormat
     */
    public function cardRecharge(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            if ($arr['ResultCode'] == 1) {
                throw new \Exception('1.cardRecharge', 602);
            } else {
                throw new \Exception($arr['ResultCode'], 602);
            }
        }

        return $data->setData([
            'result_code'   =>0,
            'message'       => '会员卡充值成功',
            'balance'       => $arr['Balance'],
            'score'         => $arr['Score'],
            'desc'          => $arr['ResultMsg']
        ]);
    }

    /**
     * 会员卡退费
     * wenqiang
     * 2017-03-17T17:36:16+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function cardRefund(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            if ($arr['ResultCode']  == 1) {
                throw new \Exception('1.cardRefund', 602);
            } else {
                throw new \Exception($arr['ResultCode'], 602);
            }
        }

        return $data->setData([
            'result_code'       =>  $arr['ResultCode'],
            'message'           => '会员卡退费成功',
            'trace_no_center'   =>  $arr['TraceNoCenter'],
            'balance'           =>  $arr['Balance']
        ]);
    }

    /**
     * 会员卡对应的排期折扣价
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-12T14:50:55+0800
     *
     * @param    DataFormat     $data
     * @param    float          $standPrice
     * @return   DataFormat
     */
    public function cardScheduleDiscount(DataFormat $data, $standPrice)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            if ($arr['ResultCode'] == 1) {
                throw new \Exception('1.discount', 602);
            } else {
                throw new \Exception($arr['ResultCode'], 602);
            }
        }

        if ($arr['DiscountType'] == 0) {
            $price = $standPrice * floatval($arr['Price']) / 10;
        } elseif ($arr['DiscountType'] == 1) {
            $price = $arr['Price'];
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       => '操作成功',
            'price'         => $price,
            'discount_type' => $arr['DiscountType']
        ]);
    }

    /**
     * 会员卡所有类型
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-12T15:41:54+0800
     *
     * @param    DataFormat $data
     * @return   DataFormat
     */
    public function cardType(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 602);
        }

        $lists = [];
        foreach ($arr['MemberTypes']['MemberType'] as $d) {
            if (is_array($d)) {
                $lists[] = [
                    'no'    => $d['MemberType'],
                    'name'  => $d['MemberTypeName']
                ];
            } else {
                $lists[] = [
                    'no'    => $arr['MemberTypes']['MemberType']['MemberType'],
                    'name'  => $arr['MemberTypes']['MemberType']['MemberTypeName']
                ];

                break;
            }
        }

        return $data->setData([
                'result_code'   =>  0,
                'message'       => '操作成功',
                'lists'         =>  $lists,
            ]);
    }

    /**
     * 获取所有排期
     * wenqiang
     * 2017-03-11T15:04:49+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getAllSchedule(DataFormat $data)
    {
        $tmp = [];
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0 ) {
            if ($arr['ResultCode'] == 1) {
                throw new \Exception('1.getAllSchedule', 600);
            } else {
                throw new \Exception($arr['ResultCode'], 600);
            }
        }

        $array = $arr['CinemaPlans']['CinemaPlan'];

        if (!$this->isIndexArr($array)) {
            return $data->setData([
                'result_code'   => 0,
                'message'       => '操作成功',
                'cinema_plans'  => []
            ]);
        }

        foreach ($array as $key => $val) {
            $startTime = $array[$key]['FeatureDate'] . ' ' . $array[$key]['FeatureTime'];
            $endTime = $array[$key]['FeatureDate'] . ' ' . $array[$key]['TotalTime'];

            if ($endTime < $startTime) {
                $endTime = date('Y-m-d H:i:s', strtotime("$endTime 1 day"));
            }

            $tmp[$key]['hall_name']     =   $val['HallName'];
            $tmp[$key]['hall_no']       =   $val['HallNo'];
            $tmp[$key]['film_name']     =   $val['FilmName'];
            $tmp[$key]['film_no']       =   $val['FilmNo'];
            $tmp[$key]['start_time']    =   $startTime;
            $tmp[$key]['end_time']      =   $endTime;
            $tmp[$key]['app_price']     =   $val['AppPric'];
            $tmp[$key]['stand_price']   =   $val['StandPric'];
            $tmp[$key]['protect_price'] =   $val['ProtectPrice'];
            $tmp[$key]['schedule_no']   =   $val['FeatureAppNo'];
            $tmp[$key]['feature_no']    =   $val['FeatureNo'];
            $tmp[$key]['film_language'] =   $val['CopyLanguage'];
            $tmp[$key]['film_type']     =   $val['CopyType'];
            $tmp[$key]['set_status']    =   $val['SetClose'];
        }

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'cinema_plans'  => $tmp
            ]);
    }

    /**
     * 获取对应排期下的座位图状态
     * wenqiang
     * 2017-03-11T15:17:47+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getSeatInfo(DataFormat $siteState, DataFormat $allSeat)
    {
        $siteArr = $siteState->xmlToArray();
        $allSeatArr = $allSeat->toArray();

        if ($siteArr['ResultCode'] != 0) {
            if ($siteArr['ResultCode'] == 1) {
                throw new \Exception('1.getSeatInfo', 601);
            } else {
                throw new \Exception($siteArr['ResultCode'], 601);
            }
        }

        $array = array_get($siteArr, 'PlanSiteStates.PlanSiteState');

        $maxRow = max(array_column($array, 'GraphRow'));
        $maxCol = max(array_column($array, 'GraphCol'));
        $minCol = min(array_column($array, 'GraphCol'));

        $douleSeat = [];
        $seatMap = [];

        foreach ($allSeatArr['hallSeats'] as $key => $val) {
            if ($val['leftCount'] == 1 && $val['rightCount'] == 0) {
                $douleSeat[$val['seatNo']] = 'R';
            } elseif ($val['leftCount'] == 0 && $val['rightCount'] == 1) {
                $douleSeat[$val['seatNo']] = 'L';
            }
        }

        foreach ($array as $d) {
            $seatMap[$d['GraphRow'].'-'.$d['GraphCol']] = $d;
        }

        $seatData = [];
        for ($row = 1; $row <= $maxRow; $row++) {
            for ($col= 1; $col <= $maxCol; $col++) {
                if ($col >= $minCol) {
                    $seatData[$row - 1][$col - $minCol] = [
                        'seat_no'   => '',
                        'state'     => -2,
                        'row'       => $row,
                        'col'       => $col,
                        'type'      => 'N',
                        'code'      => '',
                        'name'      => '',
                        'true_state'    => -2
                    ];

                    if (array_key_exists($row.'-'.$col, $seatMap)) {
                        $seatData[$row - 1][$col - $minCol]['seat_no'] = $seatMap[$row.'-'.$col]['SeatNo'];
                        $seatData[$row - 1][$col - $minCol]['row'] = intval($seatMap[$row.'-'.$col]['SeatRow']);
                        $seatData[$row - 1][$col - $minCol]['col'] = intval($seatMap[$row.'-'.$col]['SeatCol']);
                        $seatData[$row - 1][$col - $minCol]['type']= array_get($douleSeat, $seatMap[$row.'-'.$col]['SeatNo']);
                        $seatData[$row - 1][$col - $minCol]['code']= $seatMap[$row.'-'.$col]['SeatPieceNo'];
                        $seatData[$row - 1][$col - $minCol]['name']= $seatMap[$row.'-'.$col]['SeatPieceName'];
                        $seatData[$row-1][$col - $minCol]['true_state'] = intval($seatMap[$row.'-'.$col]['SeatState']);
                        if (intval($seatMap[$row.'-'.$col]['SeatState']) != 0) {
                            $seatData[$row - 1][$col - $minCol]['state'] = 1;
                        } else {
                            $seatData[$row-1][$col - $minCol]['state'] = 0;
                        }
                    }
                }
            }
        }

        return $siteState->setData([
                'result_code'   =>  0,
                'message'       => '操作成功',
                'seat_data'     =>  $seatData
            ]);
    }

    /**
     * 锁座并下单
     * wenqiang
     * 2017-03-11T15:25:04+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function lockSeat(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0 ) {
            if ($arr['ResultCode'] == 1) {
                throw new \Exception('1.lockSeat', 600);
            } else {
                throw new \Exception($arr['ResultCode'], 600);
            }
        }

        return $data->setData([
            'result_code'   =>  0,
            'message'       => '锁座成功',
            'order_no'      => $arr['OrderNo']
        ]);
    }

    /**
     * 解锁座位
     * wenqiang
     * 2017-03-11T15:40:06+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function unlockSeat(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 601);
        }

        return $data->setData([
            'result_code' => $arr['ResultCode'],
            'message'       => '解锁座位成功',
        ]);
    }


    /**
     * 常规买票
     * wenqiang
     * 2017-03-10T21:28:07+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function ticketPlaceOrder(DataFormat $data, $groundTradeNo)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'] . '%' . $groundTradeNo, 601);
        }

        if (is_null($groundTradeNo)) {
            return $data->setData([
                'result_code'   => 0,
                'message'       => '操作成功',
                'order_no'      => $arr['OrderNo'],
                'valid_code'    => $arr['ValidCode'],
                'verify_code'   => '',
            ]);
        } else {
            return $data->setData([
                'result_code'       => 0,
                'message'           => '操作成功',
                'order_no'          => $arr['OrderNo'],
                'valid_code'        => $arr['ValidCode'],
                'verify_code'       => '',
                'ground_trade_no'   => $groundTradeNo,
            ]);
        }

    }

    /**
     * 退票
     * wenqiang
     * 2017-03-11T15:48:09+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function refundOrder(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 601);
        }

        return $data->setData([
            'result_code'   => $arr['ResultCode'],
            'message'       => '退票成功',
        ]);
    }

    /**
     * 获取电影票信息
     * wenqiang
     * 2017-03-11T17:53:29+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getOrderInfo(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 603);
        }

        $seatInfo = [];
        if (!$this->isIndexArr($arr['SeatInfos']['SeatInfo'])) {
            $seatInfo['col'] = $arr['SeatInfos']['SeatInfo']['SeatCol'];
            $seatInfo['row'] = $arr['SeatInfos']['SeatInfo']['SeatRow'];
        } else {
             foreach ($arr['SeatInfos']['SeatInfo'] as $key => $val) {
                $seatInfo[$key]['row'] = $val['SeatRow'];
                $seatInfo[$key]['col'] = $val['SeatCol'];
            }
        }

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'order_no'      => $arr['OrderNo'],
            'film_name'     => $arr['FilmName'],
            'schedule_time' => $arr['FeatureDate'] . ' ' . $arr['FeatureTime'],
            'hall_name'     => $arr['HallName'],
            'seat_info'     => $seatInfo
        ]);
    }

    /**
     * 修改订单价格
     * wenqiang
     * 2017-03-17T15:35:27+0800
     * @return [type] [description]
     */
    public function modifyOrderPrice(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 603);
        }

        return $data->setData([
            'result_code'   => $arr['ResultCode'],
            'message'       => '操作成功',
        ]);
    }

    /**
     * 获取订单售票状态
     * wenqiang
     * 2017-03-11T15:55:34+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function getOrderStatus(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 603);
        }

        return $data->setData([
            'result_code'   => 0,
            'message'       => '操作成功',
            'order_no'      => $arr['OrderNo'],
            'valid_code'    => $arr['ValidCode'],
            'order_status'  => $arr['OrderStatus']
        ]);
    }

    /**
     * 合作商打票
     * wenqiang
     * 2017-03-11T16:23:00+0800
     * @param  DataFormat $data [description]
     * @return [type]           [description]
     */
    public function printTicket(DataFormat $data)
    {
        $arr = $data->xmlToArray();

        if ($arr['ResultCode'] != 0) {
            throw new \Exception($arr['ResultCode'], 603);
        }

        $seatInfo = array_get($arr, 'SeatInfos.SeatInfo');
        $printData = [];

        if ($this->isIndexArr($seatInfo)) {
            foreach ($seatInfo as $key => $val) {
                $printData[] = [
                    'qr_code'       =>  $val['TicketNo'],
                    'ticket_no'     =>  $val['TicketNo2'],
                    'seat_no'       =>  '',
                    'seat_code'     =>  '',
                    'seat_row'      =>  $val['SeatRow'],
                    'seat_col'      =>  $val['SeatCol'],
                    'cpn_name'      =>  $val['CpnName'],
                    'ticket_price'  =>  $val['StPrice'],
                    'service'       =>  $val['PayPrice'],
                    'sell_ticket_time'  =>  '',
                ];
            }
        } else {
            $printData[0] = [
                'qr_code'       =>  $seatInfo['TicketNo'],
                'ticket_no'     =>  $seatInfo['TicketNo2'],
                'seat_no'       =>  '',
                'seat_code'     =>  '',
                'seat_row'      =>  $seatInfo['SeatRow'],
                'seat_col'      =>  $seatInfo['SeatCol'],
                'cpn_name'      =>  $seatInfo['CpnName'],
                'ticket_price'  =>  $seatInfo['StPrice'],
                'service'       =>  $seatInfo['PayPrice'],
                'sell_ticket_time'  =>  '',
            ];
        }

        return $data->setData([
            'result_code'   =>  $arr['ResultCode'],
            'message'       =>  '操作成功',
            'ticket_plat'   =>  'mtx',
            'print_type'    =>  $arr['PrintType'],
            'order_no'      =>  $arr['OrderNo'],
            'order_date'    =>  $arr['OrderDate'] . ' ' . $arr['OrderTime'],
            'ticket_num'    =>  count($printData),
            'ticket_type'   =>  $arr['TicketKindName'],
            'film_no'       =>  '',
            'film_name'     =>  $arr['FilmName'],
            'hall_no'       =>  '',
            'hall_name'     =>  $arr['HallName'],
            'feature_no'    =>  '',
            'feature_time'  =>  $arr['FeatureDate'] . ' ' . $arr['FeatureTime'],
            'print_data'    =>  $printData,
        ]);
    }



    /**
     * 判断是否是索引数组
     * wenqiang
     * 2017-03-13T15:32:18+0800
     * @param  [type]  $value [description]
     * @return boolean        [description]
     */
    public function isIndexArr($value) {
        if (is_array($value)) {
            $keys = array_keys($value);
            return $keys === array_keys($keys);
        }
        return false;
    }
}