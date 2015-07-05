<?php

/**
 *
 * @package Fuel
 * @author Ory43
 * @since 2015/05/27
 *
 */
class Tasks_Base
{

    /**
     * タイトル
     *
     * @var string
     */
    protected $title = 'no title';

    public function run()
    {
        var_dump($this->title);
    }
}