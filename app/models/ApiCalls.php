<?php

class ApiCalls extends \Phalcon\Mvc\Model
{

	public $request = [];
	
	public function load($request)
	{
		$this->request = $request;
	}
	
	// Fonction qui vérifie si la requête est légitime
	public function check()
	{
		if (empty($this->request->get('key')) || $this->request->get('key') !== $this->getDI()->get('config')->api->key)
		{
			$response = new \Phalcon\Http\Response();
			$response->setStatusCode(403, "OK");
			$response->setContent("<html><head><title>403 forbiden</title></head><body>accès refusé</body></html>");
			$response->send();
			
			exit;
		}
	}

}
