<?php
/**
 * Created by PhpStorm.
 * User: Dao Xuan Thu
 * Date: 01-Sep-18
 * Time: 10:00 PM
 */

namespace XT\Core\Admin\Common;


class Log
{
    protected $log_id;
    protected $date;
    protected $type;
    protected $event;

    
    /**
     * @return mixed
     */
    public function getLogId()
    {
        return $this->log_id;
    }

    /**
     * @param mixed $log_id
     */
    public function setLogId($log_id)
    {
        $this->log_id = $log_id;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }


}