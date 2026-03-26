# ONLYOFFICE plugin for Chamilo

Welcome to the official repository for the **ONLYOFFICE plugin for Chamilo**!

This plugin brings the power of [ONLYOFFICE Docs](https://www.onlyoffice.com/docs) to your [Chamilo](https://chamilo.org/) e-learning platform. It allows teachers and students to create, edit, and collaborate on documents, spreadsheets, and presentations directly within their courses.

It's designed to make collaboration in e-learning more effective, allowing users to work together in real time without ever needing to download a file.

> **Note:** This plugin is compatible with Chamilo v1.11.16 or newer.

## Features ✨

The plugin allows teachers to:

* ✍️ Create, edit, and view [text documents](https://www.onlyoffice.com/word-processor), [spreadsheets](https://www.onlyoffice.com/sheets), and [presentations](https://www.onlyoffice.com/slides) right inside Chamilo.
* 👥 Collaborate on documents in real time using features like two co-editing modes (Fast and Strict), Track Changes, comments, built-in chat.

**Supported formats** 🗂️

* For editing: DOCX, XLSX, PPTX, ODT, ODS, ODP.

<p align="center">
  <a href="https://www.onlyoffice.com/office-for-chamilo">
    <img width="800" src="https://static-site.onlyoffice.com/public/images/templates/office-for-chamilo/documents/screenshot4@2x.png" alt="ONLYOFFICE for Chamilo">
  </a>
</p>

## Installing ONLYOFFICE Docs

You will need an instance of [ONLYOFFICE Docs](https://www.onlyoffice.com/docs) (Document Server) that is resolvable and connectable both from Chamilo and any end clients. ONLYOFFICE Document Server must also be able to POST to Chamilo directly.

ONLYOFFICE Document Server and Chamilo can be installed either on different computers, or on the same machine.
If you use one machine, set up a custom port for Document Server as by default both ONLYOFFICE Document Server and Chamilo work on port 80.

The ONLYOFFICE server needs to be able to resolve your Chamilo portal's URL.

You can install free Community version of ONLYOFFICE Docs or scalable Enterprise Edition.

To install **free Community version**, use [Docker](https://github.com/onlyoffice/Docker-DocumentServer) (recommended) or follow [these instructions](https://helpcenter.onlyoffice.com/docs/installation/docs-community-install-ubuntu.aspx) for Debian, Ubuntu, or derivatives.

To install **Enterprise Edition**, follow instructions [here](https://helpcenter.onlyoffice.com/docs/installation/enterprise).

The Community Edition vs Enterprise Edition comparison can be found [here](#onlyoffice-docs-editions).

To use ONLYOFFICE behind a proxy, please refer to [this article](https://helpcenter.onlyoffice.com/docs/installation/docs-community-proxy.aspx).

Alternatively, you can opt for **ONLYOFFICE Docs Cloud** which doesn't require downloading and installation. To get ONLYOFFICE Docs Cloud, get started [here](https://www.onlyoffice.com/docs-registration).

## Downloading a more recent version of the plugin

When approved by Chamilo and integrated as official plugin, the Chamilo team strives to provide the latest stable version of the plugin within the Chamilo package. Downloading another version of the plugin might have negative effects on your installation. However, if you believe you need to download a more recent version from the third party, here is the recommended procedure:

1. Get the latest version of this repository running the command:
    ```
    git clone https://github.com/ONLYOFFICE/onlyoffice-chamilo
    cd onlyoffice-chamilo
    ```

2. Get submodules:
    ```
    git submodule update --init --recursive
    ```

3. Get plugin dependencies:
    ```
    composer install
    ```

4. Collect all files
    ```
    mkdir /tmp/onlyoffice-deploy
    mkdir /tmp/onlyoffice-deploy/onlyoffice
    cp -r ./ /tmp/onlyoffice-deploy/onlyoffice
    cd /tmp/onlyoffice-deploy/onlyoffice
    rm -rf ./.git*
    rm -rf */.git*
    ```

5. Archive
    ```
    cd ../
    zip onlyoffice.zip -r onlyoffice
    ```

## Installing ONLYOFFICE plugin for Chamilo 📥

The plugin has been integrated into Chamilo since version 1.11.16.

To enable it, go to the plugins list, select the ONLYOFFICE plugin, and click _Enable_ for the selected plugins.

If you want more up-to-date versions of the plugin, you need to replace the pre-installed default plugin folder with the newly collected plugin:

`/var/www/html/chamilo-1.11.16/plugin/onlyoffice`

where `chamilo-1.11.16` is your current Chamilo version.

If your Chamilo version is lower than 1.11.16, go to *Chamilo Administration -> Plugins -> Upload plugin*.

Upload `onlyoffice.zip` (you'll find it in the [Releases](https://github.com/ONLYOFFICE/onlyoffice-chamilo/releases) section). You'll see the plugin list.

Then launch `composer install` from the Chamilo root folder.

Return to the plugin list, select the ONLYOFFICE plugin, and click *Enable*.

## Configuring ONLYOFFICE plugin for Chamilo 🛠️

On the Plugins page, find ONLYOFFICE and click _Configure_. You'll see the _Settings_ page. Enable the plugin and specify the _Document Server address_ (the public URL of your running ONLYOFFICE Docs instance).

JWT is enabled by default and the secret key is generated automatically to restrict the access to ONLYOFFICE Docs and for security reasons and data integrity. If needed, specify your own **Secret key** on the Chamilo **Settings** page. The key can be found on your ONLYOFFICE server, depending on the type of server. See the ONLYOFFICE Docs [config file](https://api.onlyoffice.com/docs/docs-api/additional-api/signature/) page for more details.
Specify the same secret key (search for a long hash string next to "secret") and save.

The plugin will tell you if anything is wrong.

## How it works 📖

### For teachers/trainers

* To create a new file, teachers can open the documents folder and click the ONLYOFFICE icon "Create new".
* The user is redirected to the file creation page where they need to enter the file name and format (text document, spreadsheet, or presentation). The browser calls `/plugin/onlyoffice/create.php` method. It adds the copy of the empty file to the course folder.
* To open an existing file, the user chooses the _Open with ONLYOFFICE_ icon next to the normal edit icon.
* The request is being sent to `/plugin/onlyoffice/editor.php?docId=«document identificator»`. The server processes the request, generates the editor initialization configuration with the properties:

  * **url** - the URL that ONLYOFFICE Document Server uses to download the document;
  * **callbackUrl** - the URL at which ONLYOFFICE Document Server informs Chamilo about the status of the document editing;
  * **documentServerUrl** - the URL that the client needs to respond to ONLYOFFICE Document Server (can be set at the administrative settings page);
  * **key** - the etag to instruct ONLYOFFICE Document Server whether to download the document again or not;

* The server returns a page with a script to open the editor.
* The browser opens this page and loads the editor.
* The browser makes a request to Document Server and passes the document configuration to it.
* Document Server loads the document and the user starts editing.
* Document Server sends a POST request to **callbackUrl** to inform Chamilo that the user is editing the document.
* When all users have finished editing, they close the editor window.
* After 10 seconds, Document Server makes a POST request to **callbackUrl** with the information that editing has ended and sends a link to the new document version.
* Chamilo loads a new version of the document and overwrites the file.

### For learners

* Learners have access to a new ONLYOFFICE icon next to all documents supported by ONLYOFFICE in the documents tool.
* In the learning paths, the viewer seamlessly integrates with Chamilo to open the supported documents.

> 🔗 **Learn more:**  For a full breakdown of ONLYOFFICE's document editing and callback flow, see the [ONLYOFFICE Docs API documentation](https://api.onlyoffice.com/docs/docs-api/get-started/basic-concepts/).

## ONLYOFFICE Docs editions

ONLYOFFICE offers different versions of its online document editors that can be deployed on your own servers.

* Community Edition 🆓 (`onlyoffice-documentserver` package)
* Enterprise Edition 🏢 (`onlyoffice-documentserver-ee` package)

The table below will help you to make the right choice.

| Pricing and licensing | Community Edition | Enterprise Edition |
| ------------- | ------------- | ------------- |
| | [Get it now](https://www.onlyoffice.com/download-community?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo#docs-community)  | [Start Free Trial](https://www.onlyoffice.com/download?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo#docs-enterprise)  |
| Cost  | FREE  | [Go to the pricing page](https://www.onlyoffice.com/docs-enterprise-prices?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo)  |
| Simultaneous connections | up to 20 maximum  | As in chosen pricing plan |
| Number of users | up to 20 recommended | As in chosen pricing plan |
| License | GNU AGPL v.3 | Proprietary |
| **Support** | **Community Edition** | **Enterprise Edition** |
| Documentation | [Help Center](https://helpcenter.onlyoffice.com/docs/installation/community) | [Help Center](https://helpcenter.onlyoffice.com/docs/installation/enterprise) |
| Standard support | [GitHub](https://github.com/ONLYOFFICE/DocumentServer/issues) or paid | 1 or 3 years support included |
| Premium support | [Contact us](mailto:sales@onlyoffice.com) | [Contact us](mailto:sales@onlyoffice.com) |
| **Services** | **Community Edition** | **Enterprise Edition** |
| Conversion Service                | + | + |
| Document Builder Service          | + | + |
| **Interface** | **Community Edition** | **Enterprise Edition** |
| Tabbed interface                  | + | + |
| Dark theme                        | + | + |
| 125%, 150%, 175%, 200% scaling    | + | + |
| White Label                       | - | - |
| Integrated test example (node.js) | + | + |
| Mobile web editors                | - | +* |
| **Plugins & Macros** | **Community Edition** | **Enterprise Edition** |
| Plugins                           | + | + |
| Macros                            | + | + |
| **Collaborative capabilities** | **Community Edition** | **Enterprise Edition** |
| Two co-editing modes              | + | + |
| Comments                          | + | + |
| Built-in chat                     | + | + |
| Review and tracking changes       | + | + |
| Display modes of tracking changes | + | + |
| Version history                   | + | + |
| **Document Editor features** | **Community Edition** | **Enterprise Edition** |
| Font and paragraph formatting   | + | + |
| Object insertion                | + | + |
| Adding Content control          | + | + |
| Editing Content control         | + | + |
| Layout tools                    | + | + |
| Table of contents               | + | + |
| Navigation panel                | + | + |
| Mail Merge                      | + | + |
| Comparing Documents             | + | + |
| **Spreadsheet Editor features** | **Community Edition** | **Enterprise Edition** |
| Font and paragraph formatting   | + | + |
| Object insertion                | + | + |
| Functions, formulas, equations  | + | + |
| Table templates                 | + | + |
| Pivot tables                    | + | + |
| Data validation                 | + | + |
| Conditional formatting          | + | + |
| Sparklines                      | + | + |
| Sheet Views                     | + | + |
| **Presentation Editor features** | **Community Edition** | **Enterprise Edition** |
| Font and paragraph formatting   | + | + |
| Object insertion                | + | + |
| Transitions                     | + | + |
| Animations                      | + | + |
| Presenter mode                  | + | + |
| Notes                           | + | + |
| **Form creator features** | **Community Edition** | **Enterprise Edition** |
| Adding form fields              | + | + |
| Form preview                    | + | + |
| Saving as PDF                   | + | + |
| **PDF Editor features**      | **Community Edition** | **Enterprise Edition** |
| Text editing and co-editing                                | + | + |
| Work with pages (adding, deleting, rotating)               | + | + |
| Inserting objects (shapes, images, hyperlinks, etc.)       | + | + |
| Text annotations (highlight, underline, cross out, stamps) | + | + |
| Comments                        | + | + |
| Freehand drawings               | + | + |
| Form filling                    | + | + |
| | [Get it now](https://www.onlyoffice.com/download-community?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo#docs-community)  | [Start Free Trial](https://www.onlyoffice.com/download?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo#docs-enterprise)  |

\* If supported by DMS.

## Need help? User Feedback and Support 💡

* **🐞 Found a bug?** Please report it by creating an [issue](https://github.com/ONLYOFFICE/onlyoffice-chamilo/issues).
* **❓ Have a question?** Ask our community and developers on the [ONLYOFFICE Forum](https://community.onlyoffice.com).
* **👨‍💻 Need help for developers?** Check our [API documentation](https://api.onlyoffice.com).
* **💡 Want to suggest a feature?** Share your ideas on our [feedback platform](https://feedback.onlyoffice.com/forums/966080-your-voice-matters).