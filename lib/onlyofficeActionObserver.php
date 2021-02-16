<?php

class OnlyofficeActionObserver extends HookObserver implements HookDocumentActionObserverInterface
{
    /**
     * Constructor.
     */
    public function __construct() {
        parent::__construct(
            'plugin/onlyoffice/onlyoffice_plugin.php',
            'onlyoffice'
        );
    }

    /**
     * Create a Onlyoffice edit tools when the Chamilo loads document tools
     *
     * @param HookDocumentActionEventInterface $event - the hook event
     */
    public function notifyDocumentAction(HookDocumentActionEventInterface $event) {
        $data = $event->getEventData();

        if ($data['type'] === HOOK_EVENT_TYPE_PRE) {
            $data['actions'][] = OnlyofficeTools::getButtonCreateNew();
            return $data;
        }
    }
}