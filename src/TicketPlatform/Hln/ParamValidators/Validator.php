<?php

namespace biqu\TicketPlatform\Hln\ParamValidators;

class Validator
{
    /**
     * 参数中的config类型
     * @var [type]
     */
    protected $config = [
        // 'old_or_new_url',
        // 'app_code',
        'cinema_id',
        // 'token_id',
        // 'token',
        'secretkey',
        // 'version',
        'channelCode'
        // 'partner_code',
        // 'partnerkey'
    ];
    protected $app = [];

    protected $params = [];

    protected $nullable = [];

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * 验证参数的正确性
     * wenqiang
     * 2017-03-16T17:34:50+0800
     * @return [type] [description]
     */
    public function auth()
    {
        $arr = [];

        foreach ($this->config as $val) {
            if (!in_array($val, array_keys($this->params['config']))) {
                throw new \Exception($val . '未传入', 599);
            }
            $arr['config'][$val] = $this->params['config'][$val];
        }

        if ($this->app) {
            foreach ($this->app as $val) {
                if (!in_array($val, array_keys($this->params['app']))) {
                    throw new \Exception($val . '未传入', 599);
                }
                $arr['app'][$val] = $this->params['app'][$val];
            }

            foreach ($arr['app'] as $key => $val) {
                if (!in_array($key, $this->nullable)) {
                    if (empty($val) && $val !== false && $val !== 0 && $val !== '0') {
                        throw new \Exception($key . '的值不可为空', 599);
                    }
                }
            }
        }
        return $arr;
    }
}