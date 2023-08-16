<?php
/**
 *
 * (c) Copyright Ascensio System SIA 2023
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

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
            "1.2.0",
            "Asensio System SIA",
            [
                "enable_onlyoffice_plugin" => "boolean",
                "document_server_url" => "text",
                "jwt_secret" => "text",
                "connect_demo" => "checkbox"
            ]
        );
    }

    /**
     * Create OnlyofficePlugin object
     */
    public static function create(): OnlyofficePlugin
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
     * Install the "create" hooks.
     */
    public function installHook()
    {
        $itemActionObserver = OnlyofficeItemActionObserver::create();
        HookDocumentItemAction::create()->attach($itemActionObserver);

        $actionObserver = OnlyofficeActionObserver::create();
        HookDocumentAction::create()->attach($actionObserver);

        $viewObserver = OnlyofficeItemViewObserver::create();
        HookDocumentItemView::create()->attach($viewObserver);
    }

    /**
     * Uninstall the "create" hooks.
     */
    public function uninstallHook()
    {
        $itemActionObserver = OnlyofficeItemActionObserver::create();
        HookDocumentItemAction::create()->detach($itemActionObserver);

        $actionObserver = OnlyofficeActionObserver::create();
        HookDocumentAction::create()->detach($actionObserver);

        $viewObserver = OnlyofficeItemViewObserver::create();
        HookDocumentItemView::create()->detach($viewObserver);
    }

    /**
     * Get the connect demo setting
     *
     * @param bool $origin - take origin
     *
     * @return string
     */
    public function useDemo()
    {
        return (bool)$this->get("connect_demo");
    }

    /**
     * Get the document server url
     *
     * @param bool $origin - take origin
     *
     * @return string
     */
    public function getDocumentServerUrl($origin = false) 
    {
        if (!$origin && $this->useDemo()) {
            return AppConfig::GetDemoParams()["ADDR"];
        }

        $url = $this->get("document_server_url");
        if ($url !== null && $url !== "/") {
            $url = rtrim($url, "/");
            if (strlen($url) > 0) {
                $url = $url . "/";
            }
        }
        return $url;
    }

    /**
     * Get the document service secret key from the application configuration
     *
     * @param bool $origin - take origin
     *
     * @return string
     */
    public function getDocumentServerSecret($origin = false) {
        if (!$origin && $this->useDemo()) {
            return AppConfig::GetDemoParams()["SECRET"];
        }
        return $this->get("jwt_secret");
    }

    /**
     * Get the jwt header setting
     *
     * @param bool $origin - take origin
     *
     * @return string
     */
    public function getJwtHeader($origin = false) {
        if (!$origin && $this->useDemo()) {
            return AppConfig::GetDemoParams()["HEADER"];
        }
        return AppConfig::JwtHeader();
    }
}
