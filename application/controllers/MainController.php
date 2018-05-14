<?php
namespace application\controllers;


use application\core\Controller;
use application\lib\Pagination;


class MainController extends Controller {

    public function indexAction() {
        $count = $this->model->cardCount();
        $pagination = new Pagination($this->route, $count, 3);
        $res = $this->model->cardList($this->route);


        $vars = [
            'pagination' => $pagination->get(),
            'list' => $res
        ];
        $link = ['/public/styles/main.css'];

        $this->view->render('main', $vars,$link);
    }

    public function createTaskAction()
    {
        if(isset($_POST['create']) && !empty($_FILES['file']['name']))
        {

            $file = $_FILES['file']['tmp_name'];
            $newfile = __DIR__.'\public\images\\'.$_FILES['file']['name'];
            $newfile = str_replace('\application\controllers', '', $newfile);
            if (!copy($file, $newfile)) {
                echo "не удалось скопировать $file...\n";
            }
            $_POST['img'] = $_FILES['file']['name'];
            $this->model->taskAdd($_POST);
            $this->view->redirect('');
        }elseif (isset($_POST['create']) && empty($_FILES['file']['name']))
        {
            $this->model->taskAdd($_POST);
        }
        $link = ['/public/styles/main.css', '/public/styles/createTask.css'];

        $this->view->render('Create Task', [], $link);
    }

}