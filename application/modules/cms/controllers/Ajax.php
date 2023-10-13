<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Created by DT Team.
 * AppName: NearbyStores
 */
class Ajax extends AJAX_Controller
{

    public function __construct()
    {
        parent::__construct();

    }


    public function re_order_menu()
    {

        $this->enableDemoMode();

        $params = array(
            "list" => RequestInput::post('list'),
        );

        $result = $this->mCMS->re_order_menu($params);
        echo json_encode($result);

    }

    public function removeMenu()
    {

        $this->enableDemoMode();

        $result = $this->mCMS->removeMenu(RequestInput::post('id'));
        echo json_encode($result);

    }

    public function addMenu()
    {

        $this->enableDemoMode();

        $params = array(
            "title" => RequestInput::post('title'),
            "parent_id" => RequestInput::post('parent_id'),
            "option" => RequestInput::post('option'),
            "page" => RequestInput::post('page'),
            "ex_url" => RequestInput::post('ex_url'),

        );
        $result = $this->mCMS->addMenu($params);
        echo json_encode($result);

    }

    public function updateMenu()
    {

        $this->enableDemoMode();

        $params = array(
            "id" => RequestInput::post('id'),
            "title" => RequestInput::post('title'),
            "parent_id" => RequestInput::post('parent_id'),
            "option" => RequestInput::post('option'),
            "page" => RequestInput::post('page'),
            "ex_url" => RequestInput::post('ex_url'),
        );
        $result = $this->mCMS->updateMenu($params);
        echo json_encode($result);

    }

    public function addPage()
    {

        $this->enableDemoMode();

        $params = array(
            "title" => RequestInput::post('title'),
            "slug" => RequestInput::post('slug'),
            "template" => RequestInput::post('template'),
            "content" => RequestInput::post('content', FALSE),
            "status" => RequestInput::post('status'),
        );


        $result = $this->mCMS->addPage($params);
        echo json_encode($result);

    }

    public function removePage()
    {

        $this->enableDemoMode();

        $result = $this->mCMS->removePage(
            RequestInput::post('id')
        );
        echo json_encode($result);

    }

    public function savePage()
    {

        $this->enableDemoMode();

        $params = array(
            "id" => RequestInput::post('id'),
            "title" => RequestInput::post('title'),
            "slug" => RequestInput::post('slug'),
            "template" => RequestInput::post('template'),
            "content" => RequestInput::post('content', FALSE),
            "status" => RequestInput::post('status'),
        );

        $result = $this->mCMS->savePage($params);
        echo json_encode($result);

    }

    public function uploadImage()
    {

        $errors = array();

        $r = array();

        if (empty($errors)) {

            $Upoader = new UploaderHelper($_FILES['image']);

            $r = $Upoader->start();

            if (empty($Upoader->getErrors())) {

                $id = $r['image'];
                $type = $r['type'];

                $user_id = intval(SessionManager::getData("id_user"));

                if ($user_id == 0) {
                    $user_id = 1;
                }

                $this->db->insert('image', array(
                    "image" => $id,
                    "type" => $type,
                    "user_id" => $user_id,
                ));
            }

            $er = $Upoader->getErrors();
            if (!empty($er)) {

                echo json_encode(array(
                    "success" => false,
                    "error" => $er,
                    "status" => 401
                ));
                return;
            }

        }


        echo json_encode(array(
            "data" => array(
                "link" => $r["full"]["url"]
            ),
            "success" => true,
            "status" => 200
        ));
        return;
    }

    public function file_uploader()
    {

        $attr = RequestInput::post("attr");

        if(isset($_FILES['files'])){
            foreach ($_FILES["files"]["error"] as $key => $error) {
                if ($error == UPLOAD_ERR_OK) {

                    $tmp_name = $_FILES["files"]["tmp_name"][$key];
                    $name = basename($_FILES["files"]["name"][$key]);

                    //create dir if needed
                    if(!is_dir(FCPATH."uploads/template")){
                        mkdir(FCPATH."uploads/template");
                    }



                    $tmp = explode('.', $name);
                    $file_extension = end($tmp);
                    $nameWithoutExt = preg_replace("#\.$file_extension#","",$name);
                    $nameWithoutExt = $this->parse_name($nameWithoutExt);

                    //move to the upload folder
                    move_uploaded_file($tmp_name, FCPATH."uploads/template/".$nameWithoutExt.".".$file_extension);

                    echo json_encode(array(Tags::SUCCESS=>1,
                        Tags::RESULT=> "uploads/template/".$nameWithoutExt.".".$file_extension,
                        "attr"=>$attr
                        ));
                    return;
                }
            }
        }
        echo json_encode(array(Tags::SUCCESS=>0,"attr"=>$attr,Tags::ERRORS=>array('err'=>$_FILES["files"]["error"])));
        return;

    }

    private function parse_name($str, $delimiter = '-'){
        $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));
        return $slug;
    }


}

/* End of file CategoryDB.php */