<?php

/**
 * Created by IntelliJ IDEA.
 * User: gpapkala
 * Date: 28.11.2017
 * Time: 14:01
 */
class Meveto_OAuth_Activator
{

    public static function activate()
    {
        add_rewrite_endpoint('meveto', EP_ROOT);
    }
}
