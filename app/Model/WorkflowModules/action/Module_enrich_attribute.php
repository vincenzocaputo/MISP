<?php
include_once APP . 'Model/WorkflowModules/WorkflowBaseModule.php';

class Module_enrich_attribute extends WorkflowBaseActionModule
{
    public $id = 'enrich-attribute';
    public $name = 'Enrich Attribute';
    public $description = 'Enrich an Attribute with the provided module.';
    public $icon = 'asterisk';
    public $inputs = 1;
    public $outputs = 1;
    public $expect_misp_core_format = true;
    public $params = [];

    private $Module;


    public function __construct()
    {
        parent::__construct();
        $this->Module = ClassRegistry::init('Module');
        $modules = $this->Module->getModules('Enrichment');
        $moduleOptions = [];
        if (is_array($modules)) {
            $moduleOptions = array_merge([''], Hash::combine($modules, '{n}.name', '{n}.name'));
        } else {
            $moduleOptions[] = $modules;
        }
        sort($moduleOptions);
        $this->params = [
            [
                'id' => 'modules',
                'label' => 'Modules',
                'type' => 'select',
                'options' => $moduleOptions,
            ],
        ];
    }

    public function exec(array $node, WorkflowRoamingData $roamingData, array &$errors = []): bool
    {
        parent::exec($node, $roamingData, $errors);

	$params = $this->getParamsWithValues($node);

        if (empty($params['modules']['value'])) {
            $errors[] = __('No enrichmnent module selected');
            return false;
        }
        $rData = $roamingData->getData();
	$event_id = $rData['Event']['id'];
	$attribute_uuid = $rData['Event']['_AttributeFlattened'][0]['uuid'];

	$options = [
            'user' => $roamingData->getUser(),
            'event_id' => $event_id,
            'modules' => [$params['modules']['value']],
            'attribute_uuids' => [$attribute_uuid]
        ];
	$this->Event = ClassRegistry::init('Event');
        $result = $this->Event->enrichment($options);

	if ($result === true) {
            $this->push_zmq([
                'Warning' => __('Error while trying to reach enrichment service or no module available'),
                'Attribute added' => 0
            ]);
        } else {
	    $this->push_zmq([
                'Enriching event' => $event_id,
                'Attribute added' => $result
            ]);
        }
	
        return true;
    }
}
