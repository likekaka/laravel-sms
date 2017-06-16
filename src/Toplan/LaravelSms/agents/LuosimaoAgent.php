<?php
namespace Toplan\Sms;

class LuosimaoAgent extends Agent
{
    public function sendSms($tempId, $to, Array $data, $content)
    {
        $this->sendContentSms($to, $content);
    }


    public function sendContentSms($to, $content)
    {
        //因为Luosimao的签名必须放在最后，所以发送前需要检测签名位置
        //如果不是在最后，则调整至最后
        $content = trim($content);
        if ($content && !preg_match('/】$/', $content)) {
            preg_match('/【([0-9a-zA-Z\W]+)】/', $content, $matches);
            $content = str_replace($matches[0], '', $content) . $matches[0];
        }

        if (strpos($to, ",") === false) {
            $url = 'https://sms-api.luosimao.com/v1/send.json';
            $optData = [
                'mobile'  => $to,
                'message' => $content,
            ];
        } else {
            $url = 'https://sms-api.luosimao.com/v1/send_batch.json';
            $optData = [
                'mobile_list'  => $to,
                'message' => $content,
            ];
        }

        $res = $this->LuosimaoCurl($url, $optData, $this->apikey);

        $data = json_decode($res, true);
        if ($data['error'] == 0) {
            $this->result['success'] = true;
        }
        $this->result['info'] = $this->currentAgentName . ':' . $data['msg'] . "({$data['error']})";
        $this->result['code'] = $data['error'];
    }

    protected function LuosimaoCurl($url, $optData, $apikey)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-' . $apikey);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $optData);

        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
    
    public function sendTemplateSms($tempId, $to, Array $data)
    {
        return null;
    }

    public function voiceVerify($to, $code)
    {
        $url = 'https://voice-api.luosimao.com/v1/verify.json';
        $apikey = $this->voiceApikey;
        $optData = [
            'mobile' => $to,
            'code' => $code
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "$url");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD  , 'api:key-' . $apikey);

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $optData);

        $res = curl_exec( $ch );
        curl_close( $ch );

        $data = json_decode($res, true);
        if ($data['error'] == 0) {
            $this->result['success'] = true;
        }
        $this->result['info'] = $this->currentAgentName . ':' . $data['msg'] . "({$data['error']})";
        $this->result['code'] = $data['error'];
        return $this->result;
    }
}
