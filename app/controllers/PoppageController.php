<?php

class  PopPageController extends ControllerBase
{
    public function initialize()
    {
        parent::initialize();

        $this->view->setTemplateAfter("pop");
    }

    public function ReminderFeeAction()
    {
        $number = $this->request->get("Number");
        $s = Press::find(array("CustomerNumber=$number"));
        $data = array();
        foreach($s as $c){
            $data[] = $c->dump();
        }
        $this->ajax->flushData($data);
    }

    /**
     * @param $id
     * @param $number
     * @param $year
     */
    public function CancelAction()
    {
        $number = $this->request->get("Number");
        $s = Charge::find("CustomerNumber=$number");
        $data = array();
        foreach($s as $c){
            $data[] = $c->dump();
        }
        $this->ajax->flushData($data);
        exit;
    }



    /**
     * 修改菜单
     */
    public function ModifyUserAction()
    {
        $id = $this->request->get("ID");
        $user = Menu::findFirst($id);

        $data = $user->dump();
        $data["Group"] = $user->Type;

        $des = Device::find();
        foreach($des as $d){
            $data["$d->ID"] = 0;
        }

        $devices = DeviceMenu::find("MenuID=$id");
        foreach($devices as $dev){
            $data["$dev->DeviceID"] = 1;
        }
        $this->ajax->flushData($data);

    }
}
