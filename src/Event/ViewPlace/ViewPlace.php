<?php

namespace XT\Core\Event\ViewPlace;


class ViewPlace
{
    protected $Controller;
    protected $Action;
    protected $Event;
    protected $Class;
    protected $id;
    protected $active;


    public function exchangeArray($data)
    {
        if (isset($data['Controller'])) $this->Controller = $data['Controller'];
        if (isset($data['Action'])) $this->Action = $data['Action'];
        if (isset($data['Event'])) $this->Event = $data['Event'];
        if (isset($data['Class'])) $this->Class = $data['Class'];
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['active'])) $this->active = $data['active'];
        if (isset($data['ControllerAction'])) {
            $ar = explode('-',$data['ControllerAction']);
            $this->setController($ar[0]);
            $this->setAction($ar[1]);

        }
    }
    public function getArrayCopy()
    {
        $ar = get_object_vars($this);
        $ar['ControllerAction'] = $this->Controller.'-'.$this->getAction();
        return $ar;
    }


    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->Controller;
    }

    /**
     * @param mixed $Controller
     */
    public function setController($Controller)
    {
        $this->Controller = $Controller;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->Action;
    }

    /**
     * @param mixed $Action
     */
    public function setAction($Action)
    {
        $this->Action = $Action;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->Event;
    }

    /**
     * @param mixed $Event
     */
    public function setEventView($Event)
    {
        $this->Event = $Event;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->Class;
    }

    /**
     * @param mixed $Class
     */
    public function setClass($Class)
    {
        $this->Class = $Class;
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