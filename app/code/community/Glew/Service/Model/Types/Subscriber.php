<?php

class Glew_Service_Model_Types_Subscriber
{
    function parse($subscriber)
    {
        foreach ($subscriber->getData() as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }
}
