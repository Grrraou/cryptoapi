<?php

class IndexController extends ControllerBase
{

    public function indexAction()
    {
		$fiat_req = json_decode(file_get_contents('http://' . $_SERVER['HTTP_HOST'] . '/api/getFiat'));
		
		$this->view->total_usd = $fiat_req->total_usd;
		$this->view->total_eur = $fiat_req->total_eur;
    }

}

