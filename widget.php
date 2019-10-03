<?php
// Register and load the widget
function wpb_load_widget() {
    register_widget( 'gitHub' );
}
add_action( 'widgets_init', 'wpb_load_widget' );


/**
 * Widget class
 * 
 */
class gitHub extends WP_Widget {

    private $fields = array('title' => '','url'=>'');
    private $descrField = array('title' => 'Title widget','url'=>'Url to get info');

    public function __construct() {//конструктор
        parent::__construct("gitHub", "Echo info on url GitHub", array("description" => "Echo info on url GitHub"));
    }

    /**
     * Work on frontend
     * 
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance) {
    	global $github_embed;
        //$instance = defaultValue($instance, $this->fields);
        echo $instance['title'];
        //echo $instance['url'];
        echo $github_embed->mechanicUrl($instance['url'],true)->html;
    }

    /**
     * Work on backend interface
     * 
     * @param array $instance
     */
    public function form($instance) {
        $this->genField($instance);
    }

    /**
     * Update Settings
     * 
     * @param array $newInstance
     * @param array $oldInstance
     * @return array
     */
    public function update($newInstance, $oldInstance) {//
        return $this->genField($newInstance, true);
    }

    /**
     * Gen fields in backend
     * 
     * @param array $instance
     * @param array $update
     * @return boolean
     */
    private function genField($instance, $update = false) {
        if ($update) {
            $ret = array();
            foreach ($this->fields as $k => $v) {
                $ret[$k] = $instance[$k];
            }
            return $ret;
        }

        foreach ($this->fields as $k => $v) {
            $tableId = $this->get_field_id($k);
            $tableName = $this->get_field_name($k);
            if (isset($instance[$k])) 
                $value = $instance[$k];
             else 
                $value = '';
            echo '<p><label>' . $this->descrField[$k] . '</label><br><input id="' . $tableId . '" type="text" name="' . $tableName . '" value="' . $value . '" placeholder="' . $v . '" class="widefat"></p>';
        }
    }

}
?>
