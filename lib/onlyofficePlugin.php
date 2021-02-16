<?php

/**
 * Plugin class for the Onlyoffice plugin.
 *
 * @author Asensio System SIA
 */
class OnlyofficePlugin extends Plugin implements HookPluginInterface
{
    /**
     * OnlyofficePlugin constructor.
     */
    protected function __construct()
    {
        parent::__construct(
            "1.0",
            "Asensio System SIA",
            [
                "enableOnlyofficePlugin" => "boolean",
                "documentServerUrl" => "text"
            ]
        );
    }

    /**
     * Create OnlyofficePlugin
     * 
     * @return OnlyofficePlugin
     */
    public static function create()
    {
        static $result = null;

        return $result ?: $result = new self();
    }

    /**
     * This method install the plugin tables.
     */
    public function install()
    {
        $this->installHook();
    }

    /**
     * This method drops the plugin tables.
     */
    public function uninstall()
    {
        $this->uninstallHook();
    }

    /**
     * Install the create hooks.
     */
    public function installHook()
    {
        $itemActionObserver = OnlyofficeItemActionObserver::create();
        HookDocumentItemAction::create()->attach($itemActionObserver);

        $actionObserver = OnlyofficeActionObserver::create();
        HookDocumentAction::create()->attach($actionObserver);
    }

    /**
     * Uninstall the create hooks.
     */
    public function uninstallHook()
    {
        $itemActionObserver = OnlyofficeItemActionObserver::create();
        HookDocumentItemAction::create()->detach($itemActionObserver);

        $actionObserver = OnlyofficeActionObserver::create();
        HookDocumentAction::create()->detach($actionObserver);
    }
}
