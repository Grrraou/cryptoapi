<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class CoinsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for coins
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Coins', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $coins = Coins::find($parameters);
        if (count($coins) == 0) {
            $this->flash->notice("The search did not find any coins");

            $this->dispatcher->forward([
                "controller" => "coins",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $coins,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a coin
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $coin = Coins::findFirstByid($id);
            if (!$coin) {
                $this->flash->error("coin was not found");

                $this->dispatcher->forward([
                    'controller' => "coins",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $coin->id;

            $this->tag->setDefault("id", $coin->id);
            $this->tag->setDefault("name", $coin->name);
            $this->tag->setDefault("code", $coin->code);
            $this->tag->setDefault("uri", $coin->uri);
            
        }
    }

    /**
     * Creates a new coin
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "coins",
                'action' => 'index'
            ]);

            return;
        }

        $coin = new Coins();
        $coin->name = $this->request->getPost("name");
        $coin->code = $this->request->getPost("code");
        $coin->uri = $this->request->getPost("uri");
        

        if (!$coin->save()) {
            foreach ($coin->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "coins",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("coin was created successfully");

        $this->dispatcher->forward([
            'controller' => "coins",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a coin edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "coins",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $coin = Coins::findFirstByid($id);

        if (!$coin) {
            $this->flash->error("coin does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "coins",
                'action' => 'index'
            ]);

            return;
        }

        $coin->name = $this->request->getPost("name");
        $coin->code = $this->request->getPost("code");
        $coin->uri = $this->request->getPost("uri");
        

        if (!$coin->save()) {

            foreach ($coin->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "coins",
                'action' => 'edit',
                'params' => [$coin->id]
            ]);

            return;
        }

        $this->flash->success("coin was updated successfully");

        $this->dispatcher->forward([
            'controller' => "coins",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a coin
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $coin = Coins::findFirstByid($id);
        if (!$coin) {
            $this->flash->error("coin was not found");

            $this->dispatcher->forward([
                'controller' => "coins",
                'action' => 'index'
            ]);

            return;
        }

        if (!$coin->delete()) {

            foreach ($coin->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "coins",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("coin was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "coins",
            'action' => "index"
        ]);
    }

	/**
     * Creates a new coin
     */
    public function testAction()
    {
       echo "test";
    }
	
}
