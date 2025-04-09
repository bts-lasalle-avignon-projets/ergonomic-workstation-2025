<?php
class Partage extends Controller
{
    private $viewmodel;

	public function __construct($action, $request)
	{
		parent::__construct($action, $request);
		$this->viewmodel = new PartageModel();
	}

    public function export()
    {
        if (NO_LOGIN) {
            $json = $this->viewmodel->export();
    
            if (defined('DEBUG') && DEBUG === true) {
                $this->display(['json' => $json]);
            } else {
                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="processus_export.json"');
                header('Content-Length: ' . strlen($json));
                echo $json;
                exit;
            }
    
        } else {
            if (!isset($_SESSION['is_logged_in'])) {
                header('Location: ' . URL_PATH . 'processus');
                exit;
            }
        }
    
    }

}