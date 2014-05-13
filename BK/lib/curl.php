<?php
class CurlException extends Exception
{
}

class CurlUtils
{
    const TIMEOUT = 60;

    public static function post_file($url, $post_data, $mode = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CurlUtils::TIMEOUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
       
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

        $ret = curl_exec($ch);

        //if any network problem encounted, return the same format of error message as studio api
        $info = curl_getinfo($ch);

        CurlUtils::write_debug_info('POST', $post_data, $ret, $info);

        CurlUtils::convert_error($ret, $ch, $mode);
        return $ret;
    }

    public static function post($url, $post_data, $mode = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CurlUtils::TIMEOUT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($ch);
       
        //if any network problem encounted, return the same format of error message as studio api
        $info = curl_getinfo($ch);
		if($info && $info["content_type"]=="text/xml"){
			$ret = json_encode(simplexml_load_string($ret));
		}
       
        CurlUtils::write_debug_info('POST', $url, $ret, $post_data);
       
        CurlUtils::convert_error($ret, $ch, $mode);
        return $ret;
    }

    public static function delete($url, $data = null, $mode = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CurlUtils::TIMEOUT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8"));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($ch);
        //if any network problem encounted, return the same format of error message as studio api
        $info = curl_getinfo($ch);

        CurlUtils::write_debug_info('DELETE', $url, $ret, $info);
       
        CurlUtils::convert_error($ret, $ch, $mode);
        return $ret;
    }
   
    public static function put($url, $put_data, $mode = null)
    {
        $fh = fopen('php://memory', 'rw');
        fwrite($fh, $put_data);
        rewind($fh);
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, $fh);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CurlUtils::TIMEOUT);
        curl_setopt($ch, CURLOPT_INFILESIZE, strlen($put_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($ch);
        //if any network problem encounted, return the same format of error message as studio api
        $info = curl_getinfo($ch);

        CurlUtils::write_debug_info('PUT', $url, $ret, $info);

        CurlUtils::convert_error($ret, $ch, $mode);

        return $ret;
    }
   
    public static function get($url, $mode = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CurlUtils::TIMEOUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($ch);

        //if any network problem encounted, return the same format of error message as studio api
        $info = curl_getinfo($ch);

        CurlUtils::write_debug_info('GET', $url, $ret, $info);

        CurlUtils::convert_error($ret, $ch, $mode);

        return $ret;
    }
	
	public static function public_api_get($url){
		$ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CurlUtils::TIMEOUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $ret = curl_exec($ch);

        //if any network problem encounted, return the same format of error message as studio api
        $info = curl_getinfo($ch);
       
        //CurlUtils::write_debug_info('GET', $url, $ret, $info);
		
		return $ret;
	}
   
    public static function encoder_get($auth_url, $auth_token, $url, $mode = null){      
        $ch = curl_init();
       
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CurlUtils::TIMEOUT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/text; charset=utf-8"));
        curl_setopt($ch, CURLOPT_COOKIE, $auth_token);
       
        $ret = json_decode(curl_exec($ch));
        $info = curl_getinfo($ch);

        CurlUtils::write_debug_info('GET', $url, $ret, $info);
       
        CurlUtils::convert_error($ret, $ch, $mode);

        curl_close($ch);
        return $ret;
    }

    private static function convert_error(&$ret, $ch, $mode)
    {
        $info = curl_getinfo($ch);

        if($mode == "EXP_MODE")
        {
            if ($ret == null || curl_errno($ch) != 0 || $info["http_code"] == 404)
            {
                throw new CurlException("curl error with curl errno ".curl_errno($ch)." http code ".$info["http_code"]);
            }
        }
        else
        {
            if ($ret == null || curl_errno($ch) != 0 || $info["http_code"] == 404)
            {
              $error_message = new stdClass();
              $error_message->message = "服务器内部错误";
              $error_message->errors[] = array("10002" => "获取信息失败");
              $ret = json_encode($error_message);
            }
        }
    }

    private static function write_debug_info($http_type, $url, $ret, $info){
         $ret_obj = @json_decode($ret);
         
         if ($ret_obj != null){
           $ret = $ret_obj;
         }
         
         //if the return is not a valid json, we output the orignal message.
         if (function_exists("get_instance")){
           $ci = &get_instance();
           $debug_file = $ci->config->item('debug_file');
           if (is_file($debug_file) && is_writable($debug_file)){
             $ci->load->library('TVie_logger', array('log_file' => $debug_file));
             
             $ci->tvie_logger->log('debug', "type: $http_type\nurl is: {0}\nreturn value is: {1}\ncurl_state is:{2}", $url, $ret,
             $info);
          }
        }
    }
}