<?php

namespace Restery\Eloquent\Contracts;


interface Resourceful
{
    /**
     * @return string
     */
    public static function getController();
}