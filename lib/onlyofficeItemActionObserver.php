<?php

class OnlyofficeItemActionObserver extends HookObserver implements HookDocumentItemActionObserverInterface
{
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'plugin/onlyoffice/onlyoffice_plugin.php',
            'onlyoffice'
        );
    }

    /**
     * Create a Onlyoffice edit tools when the Chamilo loads document items
     *
     * @param HookDocumentItemActionEventInterface $event - the hook event
     */
    public function notifyDocumentItemAction(HookDocumentItemActionEventInterface $event) {
        $data = $event->getEventData();

        if ($data['type'] === HOOK_EVENT_TYPE_PRE) {
            $data['actions'][] = OnlyofficeTools::getButtonEdit($data);
            return $data;
        }
    }
}