<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class AssetsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for assets
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Assets', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $assets = Assets::find($parameters);
        if (count($assets) == 0) {
            $this->flash->notice("The search did not find any assets");

            $this->dispatcher->forward([
                "controller" => "assets",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $assets,
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
     * Edits a asset
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $asset = Assets::findFirstByid($id);
            if (!$asset) {
                $this->flash->error("asset was not found");

                $this->dispatcher->forward([
                    'controller' => "assets",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $asset->id;

            $this->tag->setDefault("id", $asset->id);
            $this->tag->setDefault("value", $asset->value);
            $this->tag->setDefault("coin", $asset->coin);
            $this->tag->setDefault("wallet", $asset->wallet);
            
        }
    }

    /**
     * Creates a new asset
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "assets",
                'action' => 'index'
            ]);

            return;
        }

        $asset = new Assets();
        $asset->value = $this->request->getPost("value");
        $asset->coin = $this->request->getPost("coin");
        $asset->wallet = $this->request->getPost("wallet");
        

        if (!$asset->save()) {
            foreach ($asset->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "assets",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("asset was created successfully");

        $this->dispatcher->forward([
            'controller' => "assets",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a asset edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "assets",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $asset = Assets::findFirstByid($id);

        if (!$asset) {
            $this->flash->error("asset does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "assets",
                'action' => 'index'
            ]);

            return;
        }

        $asset->value = $this->request->getPost("value");
        $asset->coin = $this->request->getPost("coin");
        $asset->wallet = $this->request->getPost("wallet");
        

        if (!$asset->save()) {

            foreach ($asset->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "assets",
                'action' => 'edit',
                'params' => [$asset->id]
            ]);

            return;
        }

        $this->flash->success("asset was updated successfully");

        $this->dispatcher->forward([
            'controller' => "assets",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a asset
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $asset = Assets::findFirstByid($id);
        if (!$asset) {
            $this->flash->error("asset was not found");

            $this->dispatcher->forward([
                'controller' => "assets",
                'action' => 'index'
            ]);

            return;
        }

        if (!$asset->delete()) {

            foreach ($asset->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "assets",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("asset was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "assets",
            'action' => "index"
        ]);
    }

}
