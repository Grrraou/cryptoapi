<?php

class Wallets extends \Phalcon\Mvc\Model
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
     * @Column(column="url", type="string", length=255, nullable=false)
     */
    public $url;
	
	/**
	 * Mise en forme json pour ApiController
	 */
	public function apiFormat($format = true)
	{
		$data = [
			'id'	=> $this->id,
			'name'	=> $this->name,
			'url' => $this->url,
		];
		
		if ($format == true)
			return json_encode($data);
		else
			return $data;
	}

}
