<?php

namespace okapi\views\devel\clogentry;

use okapi\core\Db;
use okapi\core\Exception\ParamMissing;
use okapi\core\Response\OkapiHttpResponse;

class View
{
    public static function call()
    {
        if (!isset($_GET['id'])) {
            throw new ParamMissing("id");
        }
        $tmp = Db::select_value("
            select data
            from okapi_clog
            where id='".Db::escape_string($_GET['id'])."'
        ");
        $data = unserialize(gzinflate($tmp));

        $response = new OkapiHttpResponse();
        $response->content_type = "application/json; charset=utf-8";
        $response->body = json_encode($data);
        return $response;
    }
}
