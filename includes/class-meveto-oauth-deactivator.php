<?php

/**
 * Created by IntelliJ IDEA.
 * User: gpapkala
 * Date: 28.11.2017
 * Time: 14:01
 */
class Meveto_OAuth_Deactivator
{

    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
