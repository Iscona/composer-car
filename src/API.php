<?php

namespace biqu;

use biqu\APIInterface;
use ReflectionClass;
use biqu\ErrorMessage;
use biqu\TicketPlatform\ApiParamManager;

class API
{
    /**
     * 动态调用
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T22:03:23+0800
     *
     * @param    string     $name
     * @param    array      $arguments
     * @return   [type]
     */
    public function __call($name, $arguments)
    {
        try {
            $params = new ApiParamManager($name, $arguments[0]);
        } catch (\Exception $e) {
            return [
                'result_code'   =>  $e->getCode(),
                'message'       =>  $e->getMessage(),
                'debug'         =>  $this->getDebug($e, [$e->getCode()]),
            ];
        }

        $platform = (new ReflectionClass(
            $this->platformMaps(
                $arguments[0]['platform'])))->newInstance($params);

        if ($platform instanceof APIInterface) {
                try {
                    return call_user_func_array([$platform, $name], [])->toArray();
                } catch (\Exception $e) {
                        $arr = explode('%', $e->getMessage());
                        // \Log::info(['code' => $e->getCode(),
                        //             'message' => $e->getMessage()]);
                        $message = $this->extractMessage($arguments[0]['platform'], $arr[0]);
                        $debug = $this->getDebug($e, $arr);

                        return [
                            'result_code'   =>  $e->getCode(),      //自定义状态码
                            'message'       =>  $message,           //售票平台返回错误信息
                            'debug'         =>  $debug
                        ];
                }
        } else {
            throw new \Exception('没有实现标准接口', 500);
        }
    }

    /**
     * 标识符和对应售票平台接口类关联
     * description
     *
     * @author 朱其鹏
     * @datetime 2017-03-09T22:16:38+0800
     *
     * @param    string $platform 平台标识符
     * @return   string
     */
    protected function platformMaps($platform)
    {
        $maps = [
            'mtx'   => \biqu\TicketPlatform\Mtx\UnifiedAPI::class,
            'm1905' => \biqu\TicketPlatform\M1905\UnifiedAPI::class,
            'fhjy'  => \biqu\TicketPlatform\Fhjy\UnifiedAPI::class,
            'dx'    => \biqu\TicketPlatform\Dx\UnifiedAPI::class,
            'btsmtx'=> \biqu\TicketPlatform\Btsmtx\UnifiedAPI::class,
            'hln'   => \biqu\TicketPlatform\Hln\UnifiedAPI::class,//火炼鸟
            'btshln'=> \biqu\TicketPlatform\Btshln\UnifiedAPI::class,//火炼鸟
            'btsm1905'=>\biqu\TicketPlatform\Bts1905\UnifiedAPI::class,
            'btsfhjy'=>\biqu\TicketPlatform\Btsfhjy\UnifiedAPI::class,
        ];

        if (array_key_exists($platform, $maps)) {
            return $maps[$platform];
        } else {
            throw new \Exception('请求对应的售票系统不存在', 400);
        }
    }

    /**
     * 提取提示信息
     * wenqiang
     * 2017-05-02T12:04:27+0800
     * @param  [type] $platform [description]
     * @param  [type] $code     [description]
     * @return [type]           [description]
     */
    protected function extractMessage($platform, $code)
    {
        $constant = strtoupper($platform) . 'MESSAGE';

        define('MTXMESSAGE',    ErrorMessage::MTXMESSAGE);
        define('M1905MESSAGE',  ErrorMessage::M1905MESSAGE);
        define('FHJYMESSAGE',   ErrorMessage::FHJYMESSAGE);
        define('DXMESSAGE',     ErrorMessage::DXMESSAGE);
        define('BTSMTXMESSAGE', ErrorMessage::BTSMTXMESSAGE);
        define('HLNMESSAGE',    ErrorMessage::HLNMESSAGE);
        define('BTSHLNMESSAGE', ErrorMessage::BTSHLNMESSAGE);
        define('BTS1905MESSAGE',ErrorMessage::BTS1905MESSAGE);
        define('BTSFHJYMESSAGE',ErrorMessage::BTSFHJYMESSAGE);

        $message = constant($constant);

        return $message[$code] ??
                (preg_match('/^errorRaw_/', $code) ?
                str_replace('errorRaw_', '', $code) :
                $message['-1']);
    }

    /**
     * 组合错误调试信息
     * wenqiang
     * 2017-05-02T15:28:26+0800
     * @param  [type] $e   [description]
     * @param  [type] $arr [description]
     * @return [type]      [description]
     */
    protected function getDebug($e, $arr)
    {
        return [
            'line'  => $e->getLine(),
            'code'  => $arr[0],
            'file'  => $e->getFile() . (isset($arr[1]) ? "\n message:".  $arr[1] : ''),
            'other' => isset($arr[1]) ? $arr[1] : '',
            'trace' => $e->getTraceAsString()
        ];

        return "line: " . $e->getLine() .
            "\n code: " . $arr[0] .
            "\n  file: " . $e->getFile() .
            (isset($arr[1]) ? "\n message:".  $arr[1] : '') .
            "\n trace :\n" . $e->getTraceAsString();
    }
}