<?php
include_once APP . 'Model/WorkflowModules/WorkflowBaseModule.php';

class Module_shadow_attribute_after_save extends WorkflowBaseTriggerModule
{
    public $id = 'shadow-attribute-after-save';
    public $scope = 'attribute';
    public $name = 'Shadow Attribute After Save';
    public $description = 'This trigger is called after a Shadow Attribute has been saved in the database';
    public $icon = 'cube';
    public $inputs = 0;
    public $outputs = 1;
    public $blocking = false;
    public $misp_core_format = true;
    public $trigger_overhead = self::OVERHEAD_HIGH;

    public function __construct()
    {
        parent::__construct();
    }
}
