<?php

namespace Kakaprodo\SystemAnalytic\Exception;

use Exception;

class SystemAnalyticException extends Exception
{
    /**
     * will contain additional data in the response
     * @var Array
     */
    public $data = [];

    /**
     * will contain the error status
     * default = 400
     * @var int
     */
    protected $status = null;

    protected $shouldThrow = true;

    public function __construct($message, $status = 400)
    {
        parent::__construct($message);
        $this->status = $this->status;
    }

    /**
     * Laravel property
     * This will be called by the framework
     */
    public function render()
    {
        return response()->json($this->buildData(), $this->status);
    }

    /**
     * Data that will be appended to the response
     */
    public function withData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set a status
     */
    public function withStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * arrange the response message
     */
    public function buildData()
    {
        $message = $this->getMessage();

        if ($this->data == []) {
            return [
                'success' => false,
                'message' => $message
            ];
        }

        return [
            'success' => false,
            'message' => $message,
            'data' => (object) $this->data
        ];
    }

    /**
     * Process the statement to mention whether an'execption can be thrown
     * 
     * note: validate only for the @die method
     */
    public function when($statement)
    {
        $this->shouldThrow = $statement;

        return $this;
    }


    /**
     * Throw the instance of this class
     */
    public function die()
    {
        if (!$this->shouldThrow) return;

        throw $this;
    }
}
