<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller {

    public function action_index()
    {
        $view = View::factory('template');
        $view->page_title = "Home";
        $view->page_content = View::factory('pages/home');
        
        $model_params = new Model_Params;
        $projects = $model_params->get_projects();
        
        $view->page_content->projects = $projects;
        $this->request->response = $view;
    }

}
