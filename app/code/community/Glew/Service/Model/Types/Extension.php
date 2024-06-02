<?php

class Glew_Service_Model_Types_Extension
{
    function parse($extension, $attr)
    {
        $this->name = $extension;
        $this->active = (string) $attr->active;
        $this->version = (string) $attr->version;
        return $this;
    }
}
