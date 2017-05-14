<?php
namespace Admin\Controller;

class TestController    extends AdminController {

    public function test_select(){
        $this->setDistrictSelectList(0, 0);
        $this->display();
    }

}

