<?php

class Assets extends \Phalcon\Mvc\Model
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
     * @var double
     * @Column(column="value", type="double", length=10, nullable=false)
     */
    public $value;

    /**
     *
     * @var integer
     * @Column(column="coin", type="integer", length=11, nullable=false)
     */
    public $coin;

    /**
     *
     * @var integer
     * @Column(column="wallet", type="integer", length=11, nullable=false)
     */
    public $wallet;
	
	/**
	 * Mise en forme json pour ApiController
	 */
	public function apiFormat($format = true, $full = false)
	{
		$this->loadCoin();
		if ($full == false)
		{
			$data = [
				'id'	=> $this->id,
				'value'	=> $this->value + 0,
				'coin'	=> $this->coin,
				'wallet' => $this->wallet,
			];
		}
		else
		{
			$data = [
				'id'	=> $this->id,
				'value'	=> $this->value + 0,
				'coin'	=> $this->loadCoin(true),
				'wallet' => $this->loadWallet(true),
			];
		}
		
		if ($format == true)
			return json_encode($data);
		else
			return $data;
	}
	
	/**
	 * Charger l'objet coin associé
	 */
	public function loadCoin($format = false)
	{
		$coin = Coins::findFirst([
			'conditions' => "id = :id:",
			'bind' => ['id' => $this->coin],
		]);
		
		if ($format == false)
			return $coin;
		else
			return $coin->apiFormat(false);
	}
	
	/**
	 * Charger l'objet wallet associé
	 */
	public function loadWallet($format = false)
	{
		$wallet = Wallets::findFirst([
			'conditions' => "id = :id:",
			'bind' => ['id' => $this->wallet],
		]);
		
		if ($format == false)
			return $wallet;
		else
			return $wallet->apiFormat(false);
	}

}
