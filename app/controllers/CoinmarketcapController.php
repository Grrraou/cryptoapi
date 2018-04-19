<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Http\Request;


class CoinmarketcapController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
       $this->view->disable();
    }

	/*
	 * Mettre à jour la valeur
	 */
	public function updateAction($code = "BTC", $currency = "all")
	{
		$this->view->disable();
		
		$coin = Coins::findFirst([
			'conditions' => 'code = :code:',
			'bind' => ['code' => $code],
		]);
		
		if (is_a($coin, "coins"))
		{
			$cmc_currency = (strtoupper($currency) == "ALL" || strtoupper($currency) == "EUR") ? "EUR" : "USD";
			$cmc_url = "https://api.coinmarketcap.com/v1/ticker/" . $coin->name . "/?convert=" . $cmc_currency;
			$cmc = json_decode(file_get_contents($cmc_url))[0];
			
			if (!empty($cmc->price_usd))
			{
				$coin->usd_value = ($currency == "eur" || $currency == "all") ? $cmc->price_usd : $coin->usd_value;	
				$coin->eur_value = (!empty($cmc->price_eur) && ($currency == "all" || $currency == "eur"))
								? $cmc->price_eur 
								: $coin->eur_value;
				$coin->update();
				echo strtoupper($code) . " mis à jour !";
			}
			else
			{
				echo "Probleme de connection à CoinMarketCap";
			}
		}
		else
		{
			echo "Code inconnu";
		}
	}
	
	/*
	 * Mettre toutes les valeurs à jour
	 */
	public function updateAllAction()
	{
		$this->view->disable();
		
		$conditions = "";
		$bind = [];
		
		if (!empty($this->request->get('exept')))
		{
			$exepts = explode(',', $this->request->get('exept'));
			$conditions = "code NOT IN (";
			
			$i = 1;
			foreach ($exepts as $exept)
			{
				$bind["exept" . $i] = $exept;
				$conditions .= ":exept" . $i . ":";
				$conditions .= ($i < count($exepts)) ? "," : "";
				$i++;
			}
			$conditions .= ")";
		}
		else
		{
			$conditions = "1+1";
		}
		
		$cmc_url = "https://api.coinmarketcap.com/v1/ticker/?convert=EUR";
		$cmc = json_decode(file_get_contents($cmc_url));
		
		$coins_cmc = [];
		foreach ($cmc as $cm)
		{
			$coins_cmc[$cm->symbol] = $cm;
		}
		
		$coins = Coins::find([
			'conditions' => $conditions,
			'bind' => $bind,
		]);
		
		foreach ($coins as $coin)
		{
			$coin->usd_value = (!empty($coins_cmc[$coin->code])) ? $coins_cmc[$coin->code]->price_usd : $coin->usd_value;
			$coin->eur_value = (!empty($coins_cmc[$coin->code])) ? $coins_cmc[$coin->code]->price_eur : $coin->eur_value;
			$coin->update();
			echo strtoupper($coin->code) . " mis à jour<br>";
		}
	}
	
}
