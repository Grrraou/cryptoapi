<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Http\Request;


class ApiController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
       $this->view->disable();
       echo "Group Controller; IndexAction; ID $id";
    }
	
    /**
     * Fonction pour récupérer un seul coin via son id, son code ou son nom dans cet ordre de prioritée.
     */	
	public function coinsGetAction()
	{
		$api = new ApiCalls;
		$api->load($this->request);
		$api->check(); // On vérifie la clé api
		
		$this->view->disable();
		
		$conditions = "";
		$bind = [];
		$data = [];
		
		$id = (!empty($this->request->get('id'))) ? intval($this->request->get('id')) : 0;
		$code = (!empty($this->request->get('code'))) ? $this->request->get('code') : "";
		$name = (!empty($this->request->get('name'))) ? $this->request->get('name') : "";
		
		if ($id !== 0)
		{
			$conditions = "id = :id:";
			$bind['id'] = $id;
		}
		else if ($code !== "")
		{
			$conditions = "code = :code:";
			$bind['code'] = $code;
		}
		else if ($name !== "")
		{
			$conditions = "name = :name:";
			$bind['name'] = $name;
		}
		
		$coin = (!empty($conditions)) ? Coins::findFirst([
			'conditions' => $conditions,
			'bind' => $bind,
		]) : "";
		
		echo (!empty($coin)) ? $coin->apiFormat() : json_encode(['error' => 'no result']);
		
	}
	
	/**
     * Fonction de recherche des coins
     */
	public function coinsSearchAction()
	{
		$api = new ApiCalls;
		$api->load($this->request);
		$api->check(); // On vérifie la clé api
		
		$this->view->disable();
		
		$conditions = "";
		$bind = [];
		$data = [];
		
		// Si on ne précise pas (&andor=or), les filtres se cumulent.
		// Il faut faire plusieurs requetes pour mélanger les AND et les OR (avec l'argument IN par exemple)
		$andor = (!empty($this->request->get('andor') && $this->request->get('andor') == "or")) ? " OR " : " AND ";
		
		// Inclusion d'une liste d'id
		$includes = (!empty($this->request->get('in'))) ? explode(',', $this->request->get('in')) : [];
		if (!empty($includes))
		{
			$conditions .= ($conditions != "") ? $andor : "";
			$conditions .= " id IN (";
			
			$i = 0;
			foreach ($includes as $include)
			{
				$conditions .= ":in" . $i . ":";
				$bind['in' . $i] = $include;
				$i++;
				$conditions .= ($i != count($includes)) ? "," : "";
			}
			$conditions .= ")";
		}
		
		$id = (!empty($this->request->get('id'))) ? intval($this->request->get('id')) : 0;
		// Si id_comp n'est pas valable on considère que c'est un égal
		$id_comp = "=";
		if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "more")
			$id_comp = ">";
		else if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "moreeq")
			$id_comp = ">=";
		else if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "less")
			$id_comp = "<";
		else if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "lesseq")
			$id_comp = "<=";
		
		if ($id !== 0)
		{
			$conditions .= ($conditions != "") ? $andor : "";
			$conditions .= "id " . $id_comp . " :id:";
			$bind['id'] = $id;
		}
		
		// code et name sont censés être unique donc la comparaison est forcément LIKE (sinon forcement un resultat -> coinsGet)
		$code = (!empty($this->request->get('code'))) ? $this->request->get('code') : "";
		if ($code != "")
		{
			$conditions .= ($conditions != "") ? $andor : "";
			$conditions .= "code LIKE :code:";
			$bind['code'] = "%" . $code . "%";
		}
		$name = (!empty($this->request->get('name'))) ? $this->request->get('name') : "";
		if ($name != "")
		{
			$conditions .= ($conditions != "") ? $andor : "";
			$conditions .= "name LIKE :name:";
			$bind['name'] = "%" . $name . "%";
		}
		
		// Limiter le nombre de résultats
		$limit = (!empty($this->request->get('limit')) && $this->request->get('limit') < $this->config->api->maxSearchResult) 
						? intval($this->request->get('limit')) 
						: $this->config->api->maxSearchResult;
		
		// all=all sert a tout récupérer en ignorant les filtres
		if (!empty($this->request->get('all')) && $this->request->get('all') == "all")
		{
			$conditions = "1=1";
			$bind = [];
		}
		
		$coins = (!empty($conditions)) ? Coins::find([
			'conditions' => $conditions,
			'bind' => $bind,
			'limit' => $limit,
		]) : [];
		
		foreach ($coins as $coin) {
			$data[] = [
				'id'	=> $coin->id,
				'name'	=> $coin->name,
				'code'	=> $coin->code,
				'uri'	=> $coin->uri,
			];
		}
		
		echo json_encode($data);
	}
	
	/**
     * Fonction pour récupérer un seul wallet via son id ou son nom dans cet ordre de prioritée.
     */	
	public function walletsGetAction()
	{
		$api = new ApiCalls;
		$api->load($this->request);
		$api->check(); // On vérifie la clé api
		
		$this->view->disable();
		
		$conditions = "";
		$bind = [];
		$data = [];
		
		$id = (!empty($this->request->get('id'))) ? intval($this->request->get('id')) : 0;
		$name = (!empty($this->request->get('name'))) ? $this->request->get('name') : "";
		
		if ($id !== 0)
		{
			$conditions = "id = :id:";
			$bind['id'] = $id;
		}
		else if ($name !== "")
		{
			$conditions = "name = :name:";
			$bind['name'] = $name;
		}
		
		$wallet = (!empty($conditions)) ? Wallets::findFirst([
			'conditions' => $conditions,
			'bind' => $bind,
		]) : "";
		
		echo (!empty($wallet)) ? $wallet->apiFormat() : json_encode(['error' => 'no result']);
		
	}
	
	/**
     * Fonction de recherche des wallets
     */
	public function walletsSearchAction()
	{
		$api = new ApiCalls;
		$api->load($this->request);
		$api->check(); // On vérifie la clé api
		
		$this->view->disable();
		
		$conditions = "";
		$bind = [];
		$data = [];
		
		// Si on ne précise pas (&andor=or), les filtres se cumulent.
		// Il faut faire plusieurs requetes pour mélanger les AND et les OR (avec l'argument IN par exemple)
		$andor = (!empty($this->request->get('andor') && $this->request->get('andor') == "or")) ? " OR " : " AND ";
		
		// Inclusion d'une liste d'id
		$includes = (!empty($this->request->get('in'))) ? explode(',', $this->request->get('in')) : [];
		if (!empty($includes))
		{
			$conditions .= ($conditions != "") ? $andor : "";
			$conditions .= " id IN (";
			
			$i = 0;
			foreach ($includes as $include)
			{
				$conditions .= ":in" . $i . ":";
				$bind['in' . $i] = $include;
				$i++;
				$conditions .= ($i != count($includes)) ? "," : "";
			}
			$conditions .= ")";
		}
		
		$id = (!empty($this->request->get('id'))) ? intval($this->request->get('id')) : 0;
		// Si id_comp n'est pas valable on considère que c'est un égal
		$id_comp = "=";
		if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "more")
			$id_comp = ">";
		else if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "moreeq")
			$id_comp = ">=";
		else if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "less")
			$id_comp = "<";
		else if (!empty($this->request->get('id_comp')) && $this->request->get('id_comp') == "lesseq")
			$id_comp = "<=";
		
		if ($id !== 0)
		{
			$conditions .= ($conditions != "") ? $andor : "";
			$conditions .= "id " . $id_comp . " :id:";
			$bind['id'] = $id;
		}
		
		// name est censé être unique donc la comparaison est forcément LIKE (sinon forcement un resultat -> walletsGet)
		$name = (!empty($this->request->get('name'))) ? $this->request->get('name') : "";
		if ($name != "")
		{
			$conditions .= ($conditions != "") ? $andor : "";
			$conditions .= "name LIKE :name:";
			$bind['name'] = "%" . $name . "%";
		}
		
		// Limiter le nombre de résultats
		$limit = (!empty($this->request->get('limit')) && $this->request->get('limit') < $this->config->api->maxSearchResult) 
						? intval($this->request->get('limit')) 
						: $this->config->api->maxSearchResult;
		
		// all=all sert a tout récupérer en ignorant les filtres
		if (!empty($this->request->get('all')) && $this->request->get('all') == "all")
		{
			$conditions = "1=1";
			$bind = [];
		}
		
		$wallets = (!empty($conditions)) ? Wallets::find([
			'conditions' => $conditions,
			'bind' => $bind,
			'limit' => $limit,
		]) : [];
		
		foreach ($wallets as $wallet) {
			$data[] = [
				'id'	=> $wallet->id,
				'name'	=> $wallet->name,
				'url'	=> $wallet->url,
			];
		}
		
		echo json_encode($data);
	}
	
	/**
     * Fonction pour récupérer un seul asset via son id.
     */	
	public function assetsGetAction()
	{
		$api = new ApiCalls;
		$api->load($this->request);
		$api->check(); // On vérifie la clé api
		
		$this->view->disable();
		
		$conditions = "";
		$bind = [];
		$data = [];
		
		$id = (!empty($this->request->get('id'))) ? intval($this->request->get('id')) : 0;
		$full = (!empty($this->request->get('full')) && $this->request->get('full') == "full") ? true : false;
		
		if ($id !== 0)
		{
			$conditions = "id = :id:";
			$bind['id'] = $id;
		}
		
		$asset = (!empty($conditions)) ? Assets::findFirst([
			'conditions' => $conditions,
			'bind' => $bind,
		]) : "";
		
		echo (!empty($asset)) ? $asset->apiFormat(true, $full) : json_encode(['error' => 'no result']);
		
	}
	
	/**
     * Récupérer la valeur totale cumulée en fiat des assets
     */	
	public function GetFiatAction()
	{
		$this->view->disable();
		
		$assets = Assets::find();
		$total_usd = 0;
		$total_eur = 0;
		
		foreach ($assets as $asset)
		{
			$coin = Coins::findFirst([
				'conditions' => 'id = :coin:',
				'bind' => ['coin' => $asset->coin],
			]);
			$total_usd += $coin->usd_value * $asset->value;
			$total_eur += $coin->eur_value * $asset->value;
		}
		
		echo json_encode(['total_usd' => $total_usd, 'total_eur' => $total_eur]);
	}

}
