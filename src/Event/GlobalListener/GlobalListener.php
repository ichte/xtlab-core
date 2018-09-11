<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 10-Sep-18
 * Time: 1:40 PM
 */

namespace XT\Core\Event\GlobalListener;


class GlobalListener
{
    protected $classname;
    protected $event;
    protected $description = 'Listener Global';
    protected $id;
    protected $code;
    protected $active;
    protected $priority;


    public function exchangeArray($data)
    {
        if (isset($data['classname'])) $this->classname = $data['classname'];
        if (isset($data['event'])) $this->event = $data['event'];
        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['active'])) $this->active = $data['active'];
        if (isset($data['code'])) $this->code = $data['code'];
        if (isset($data['priority'])) $this->priority = $data['priority'];

    }
    public function getArrayCopy()
    {
        $ar = get_object_vars($this);
        return $ar;
    }

    /**
     * @return mixed
     */
    public function getClassname()
    {
        return $this->classname;
    }



    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }


    /**
     * @param mixed $classname
     */
    public function setClassname($classname)
    {
        $this->classname = $classname;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return mixed
     */
    public function getEvents()
    {
        $es = explode('|', $this->event);
        foreach ($es as $e) {
            if ($e == '')
                return false;
        }

        return $es;
    }




    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }


}