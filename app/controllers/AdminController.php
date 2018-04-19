<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Http\Request;


class AdminController extends ControllerBase
{
	/**
	 * Index action
	 */
	public function indexAction()
	{
	   $this->view->disable();

		if (!$this->session->has('user'))
		{
			$this->response->redirect('/admin/login');
			$this->view->disable();
		}
		else
		{
			echo "admin";
		}
	}
	
	public function loginAction()
	{
		if ($this->request->isPost())
		{
			$user = $this->request->getPost('user');
			$password = $this->request->getPost('password');
			
			$user = Users::findFirst([
				'conditions' => 'name = :name: AND password = :password:',
				'bind' => ['name' => $user, 'password' => $password],
			]);
			
			if (!empty($user))
			{
				$this->session->user = $user;
				$this->response->redirect('/admin');
			}
			else
			{
				$this->view->error = "Identifiants incorrects";
			}
			$this->view->test = $user;
		}
		// $this->view->test = "test";
		
	}

}
