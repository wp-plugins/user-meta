<?php


//Some common function to share over the plugin
if (!class_exists('userMetaFunction')){
    class userMetaFunction{

        //Create input field 
        function createInput($name, $value='', $type='', $css=array(), $options=array()){
        
            $id = isset($css['id']) ? $css['id'] : $name;
            $class = isset($css['class']) ? $css['class'] : '';
            $style = isset($css['style']) ? $css['style'] : '';
            
            if(!$type OR $type == 'text' OR  $type == 'textbox'){
                return $input = "<input type='text' name='$name' id='$id' class='$class' value='$value' />";
            }elseif($type == 'select' OR $type == 'dropdown'){
                if(isset($options)){
                    $input = "<select name='$id' id='$name' class='$class'>";
                    foreach($options as $key => $val){
                        $input .= ($val == $value) ? "<option value='$val' selected='true'>$val</option>" : "<option value='$val'>$val</option>";
                    }
                    $input .= "</select>";
                    return $input;
                }
            }   
        }

        
    }
}


?>