<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Gather extends Controller {

    public function action_index()
    {
        print "Testing\n";
    }

    public function action_test()
    {
        //EX COMMAND: php /path/to/kohana/index.php --uri=gather/test --user=test1 --pass=test2
        
        // Get the values of `user` and `pass`
        $params = CLI::options('user', 'pass'); // $params['user'] and $params['pass']
        
        mkdir("/home/adrian/Documents/GSoC_2010/src/".$params['user']);
    }
}