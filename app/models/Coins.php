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
     *
     * @var double
     * @Column(column="value", type="double", length=10, nullable=false)
     */
    public $usd_value;
	
	/**
     *
     * @var double
     * @Column(column="value", type="double", length=10, nullable=false)
     */
    public $eur_value;
	
	/**
	 * Mise en forme json pour ApiController
	 */
	public function apiFormat($format = true)
	{
		$data = [
			'id'		=> $this->id,
			'name'		=> $this->name,
			'code'		=> $this->code,
			'uri' 		=> $this->uri,
			'usd_value'	=> $this->usd_value,
			'eur_value'	=> $this->eur_value,
		];
		
		if ($format == true)
			return json_encode($data);
		else
			return $data;
	}

}
