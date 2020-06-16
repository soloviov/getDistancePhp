<?php


class Point
{
    public $name = '';
    public $latitude = 0;
    public $longitude = 0;

    public function __construct(String $name = '', Float $latitude = 0, Float $longitude = 0)
    {
        $this->name = trim($name);
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }


}
