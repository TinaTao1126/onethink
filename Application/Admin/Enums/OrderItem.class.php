<?php

namespace Admin\Enums;

class OrderItem {
    public static  $ITEM_TYPE =array(
        1=>array(
            "id"=>1,
    		"name"=>'洗车',
            "en_name"=>'cleaning_amount'
        ),
        2=>array(
    	   "id"=>2,
            "name"=>'快修',
            "en_name"=>'repair_amount'
         ),
    	3=>array(
    	   "id"=>3,
    	    "name"=>"保养",
    	    "en_name"=>'maintain_amount'
        ),
        4=>array(
    	   "id"=>4,
            "name"=>'装饰美容',
            "en_name"=>'beauty_amount'
        )
    );
    
    public static $ITEM_TYPE_1 = 1;
    
    public static $ITEM_TYPE_2 = 2;
    
    public static $ITEM_TYPE_3 = 3;
    
    public static $ITEM_TYPE_4 = 4;
	
}

