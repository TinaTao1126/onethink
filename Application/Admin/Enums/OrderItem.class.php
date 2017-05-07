<?php

namespace Admin\Enums;

class OrderItem {
    public static  $ITEM_TYPE =array(
        array(
            "id"=>1,
    		"name"=>'洗车',
        ),
        array(
    	   "id"=>2,
            "name"=>'快修',
         ),
    	array(
    	   "id"=>3,
    	    "name"=>"保养"
        ),
        array(
    	   "id"=>4,
            "name"=>'装饰美容'
        )
    );
    
    public static  $ALL_ITEM_TYPE_ID =array(
    		1,2,3,4
    );
    
    public static $ITEM_TYPE_1 = 1;
    
    public static $ITEM_TYPE_2 = 2;
    
    public static $ITEM_TYPE_3 = 3;
    
    public static $ITEM_TYPE_4 = 4;
	
}

