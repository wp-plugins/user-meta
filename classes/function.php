<?php


//Some common function to share over the plugin
if (!class_exists('userMetaFunction')){
    class userMetaFunction{

        //Create input field 
        function createInput($name, $value='', $type='', $config=array(), $options=array()){
        
            $id = isset($config['id']) ? $config['id'] : $name;
            $class = isset($config['class']) ? $config['class'] : '';
            $style = isset($config['style']) ? $config['style'] : '';
            
            if(!$type OR $type == 'text' OR  $type == 'textbox'){
                $input = "<input type='text' name='$name' id='$id' class='$class' value='$value' />";
            }elseif($type == 'select' OR $type == 'dropdown'){
                if(isset($options)){
                    $input = "<select name='$id' id='$name' class='$class'>";
                    foreach($options as $key => $val){
                        $key = ($config['have_key']) ? $key : $val;                         
                        $input .= ($val == $value) ? "<option value='$key' selected='true'>$val</option>" : "<option value='$key'>$val</option>";
                    }
                    $input .= "</select>";
                    $input;
                }
            }elseif($type == 'file'){
                $input = "<input type='file' name='$name' id='$id' class='$class' value='$value' />";
                $form_name = isset($config['form_name']) ? $config['form_name'] : '';
                
            	/*?><script type="text/javascript">
            		var form = document.getElementById($form_name);
            		form.encoding = 'multipart/form-data';
            		form.setAttribute('enctype', 'multipart/form-data');
            	</script><?php  */               
                                
            }   
            
            return $input;
        }
        
        //Upload file. return uploaded array(file,url,type,error) on success
        function fileUpload($name, $mimes = array()){           
            if ( !empty( $_FILES[$name]['name'] ) ){
                if(!$mimes){
        			$mimes = array(
        				'jpg|jpeg|jpe' => 'image/jpeg',
        				'gif' => 'image/gif',
        				'png' => 'image/png',
        				'bmp' => 'image/bmp',
        				'tif|tiff' => 'image/tiff'
        			);                    
                }
    		
    			// front end (theme my profile etc) support
    			if ( ! function_exists( 'wp_handle_upload' ) )
    				require_once( ABSPATH . 'wp-admin/includes/file.php' );
    		
    			return wp_handle_upload( $_FILES[$name], array( 'mimes' => $mimes, 'test_form' => false ) );                      
            }              
        }
        
        
        function donation(){
            global $userMeta;
            ?>            
            <p>If you find this plugins useful, please consider making a donation to keep the coffee brewing.</p>
            <p><a href="http://khaledsaikat.com/donate-now/"><img src="<?php echo $userMeta->pluginUrl().'/images/donate.gif'; ?>"/></a></p>
            <p>Thanks for your support, <a href="http://khaledsaikat.com">Khaled Saikat</a></p>
            <?php                      
        }        

        
    }
}


?>