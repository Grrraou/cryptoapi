<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class WalletsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for wallets
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Wallets', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $wallets = Wallets::find($parameters);
        if (count($wallets) == 0) {
            $this->flash->notice("The search did not find any wallets");

            $this->dispatcher->forward([
                "controller" => "wallets",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $wallets,
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
     * Edits a wallet
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $wallet = Wallets::findFirstByid($id);
            if (!$wallet) {
                $this->flash->error("wallet was not found");

                $this->dispatcher->forward([
                    'controller' => "wallets",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $wallet->id;

            $this->tag->setDefault("id", $wallet->id);
            $this->tag->setDefault("name", $wallet->name);
            $this->tag->setDefault("url", $wallet->url);
            
        }
    }

    /**
     * Creates a new wallet
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "wallets",
                'action' => 'index'
            ]);

            return;
        }

        $wallet = new Wallets();
        $wallet->name = $this->request->getPost("name");
        $wallet->url = $this->request->getPost("url");
        

        if (!$wallet->save()) {
            foreach ($wallet->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "wallets",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("wallet was created successfully");

        $this->dispatcher->forward([
            'controller' => "wallets",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a wallet edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "wallets",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $wallet = Wallets::findFirstByid($id);

        if (!$wallet) {
            $this->flash->error("wallet does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "wallets",
                'action' => 'index'
            ]);

            return;
        }

        $wallet->name = $this->request->getPost("name");
        $wallet->url = $this->request->getPost("url");
        

        if (!$wallet->save()) {

            foreach ($wallet->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "wallets",
                'action' => 'edit',
                'params' => [$wallet->id]
            ]);

            return;
        }

        $this->flash->success("wallet was updated successfully");

        $this->dispatcher->forward([
            'controller' => "wallets",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a wallet
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $wallet = Wallets::findFirstByid($id);
        if (!$wallet) {
            $this->flash->error("wallet was not found");

            $this->dispatcher->forward([
                'controller' => "wallets",
                'action' => 'index'
            ]);

            return;
        }

        if (!$wallet->delete()) {

            foreach ($wallet->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "wallets",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("wallet was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "wallets",
            'action' => "index"
        ]);
    }

}
