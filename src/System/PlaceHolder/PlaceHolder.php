<?php

namespace XT\Core\System\Placeholder;


class Placeholder
{
     protected $placeholder_id;
     protected $name;
     protected $description;

    public function exchangeArray($data)
    {
        if (isset($data['placeholder_id'])) $this->placeholder_id = $data['placeholder_id'];
        if (isset($data['name'])) $this->name = $data['name'];
        if (isset($data['description'])) $this->description = $data['description'];
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * @return mixed
     */
    public function getPlaceholderId()
    {
        return $this->placeholder_id;
    }

    /**
     * @param mixed $placeholder_id
     */
    public function setPlaceholderId($placeholder_id)
    {
        $this->placeholder_id = $placeholder_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }



}