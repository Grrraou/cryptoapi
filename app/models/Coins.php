<?php

class Coins extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(column="id", type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(column="name", type="string", length=55, nullable=false)
     */
    public $name;

    /**
     *
     * @var string
     * @Column(column="code", type="string", length=10, nullable=false)
     */
    public $code;

    /**
     *
     * @var string
     * @Column(column="uri", type="string", length=255, nullable=false)
     */
    public $uri;
	
	/**
	 * Mise en forme json pour ApiController
	 */
	public function apiFormat($format = true)
	{
		$data = [
			'id'	=> $this->id,
			'name'	=> $this->name,
			'code'	=> $this->code,
			'uri' => $this->uri,
		];
		
		if ($format == true)
			return json_encode($data);
		else
			return $data;
	}

}
