# Chamilo ONLYOFFICE integration plugin

This app enables users to edit office documents from [Chamilo](https://chamilo.org) using ONLYOFFICE Docs packaged as Document Server - [Community or Enterprise Edition](#onlyoffice-docs-editions).

The app is compatible with Chamilo v1.11.14 or newer. 

## Features

The plugin allows to:

* Create and edit text documents, spreadsheets, and presentations.
* Co-edit documents in real-time: use two co-editing modes (Fast and Strict), Track Changes, comments, and built-in chat.

Supported formats:

* For editing: DOCX, XLSX, PPTX.

## Installing ONLYOFFICE Docs

You will need an instance of ONLYOFFICE Docs (Document Server) that is resolvable and connectable both from Chamilo and any end clients. ONLYOFFICE Document Server must also be able to POST to Chamilo directly.

ONLYOFFICE Document Server and Chamilo can be installed either on different computers, or on the same machine. If you use one machine, set up a custom port for Document Server as by default both ONLYOFFICE Document Server and Chamilo work on port 80.

You can install free Community version of ONLYOFFICE Docs or scalable Enterprise Edition with pro features.

To install free Community version, use [Docker](https://github.com/onlyoffice/Docker-DocumentServer) (recommended) or follow [these instructions](https://helpcenter.onlyoffice.com/server/linux/document/linux-installation.aspx) for Debian, Ubuntu, or derivatives.  

To install Enterprise Edition, follow instructions [here](https://helpcenter.onlyoffice.com/server/integration-edition/index.aspx).

Community Edition vs Enterprise Edition comparison can be found [here](#onlyoffice-docs-editions).

To use ONLYOFFICE behind a proxy, please refer to [this article](https://helpcenter.onlyoffice.com/server/document/document-server-proxy.aspx).

## Installing Chamilo ONLYOFFICE integration plugin

To install the plugin, go to Chamilo Administration -> Plugins -> Upload plugin. 

Upload `onlyoffice.zip` (you'll find it in the Releases section in this repo). You'll see the plugin list.

Then launch `composer install` from the Chamilo root folder. 

Return to the plugin list, select the ONLYOFFICE plugin, and click Enable the selected plugins.

## Configuring Chamilo ONLYOFFICE integration plugin

On the Plugins page, find ONLYOFFICE and click Configure. You'll see the Settings page. Enable the plugin and specify Document Server address. 

## How it works

* To create a new file, the user opens the necessesary folder and clicks the ONLYOFFICE icon "Create new".
* The user is redirected to the file creation page where they need to enter the file name and format (text document, spreadsheet, or presentation). The browser calls `/plugin/onlyoffice/create.php` method. It adds the copy of the empty file to the user folder.
* To open an existing file, the user chooses the Open with ONLYOFFICE icon.
* The request is being sent to `/plugin/onlyoffice/editor.php?docId=«document identificator»`. The server processes the request, generates the editor initialization configuration with the properties:

  * **url** - the URL that ONLYOFFICE Document Server uses to download the document;
  * **callbackUrl** - the URL that ONLYOFFICE Document Server informs about status of the document editing;
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

More information on integration ONLYOFFICE Docs can be found in the [API documentation](https://api.onlyoffice.com/editors/basic). 

## ONLYOFFICE Docs editions

ONLYOFFICE offers different versions of its online document editors that can be deployed on your own servers.

* Community Edition (`onlyoffice-documentserver` package)
* Enterprise Edition (`onlyoffice-documentserver-ee` package)

The table below will help you to make the right choice.

| Pricing and licensing | Community Edition | Enterprise Edition |
| ------------- | ------------- | ------------- |
| | [Get it now](https://www.onlyoffice.com/download.aspx?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo)  | [Start Free Trial](https://www.onlyoffice.com/enterprise-edition-free.aspx?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo)  |
| Cost  | FREE  | [Go to the pricing page](https://www.onlyoffice.com/enterprise-edition.aspx?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo)  |
| Simultaneous connections | up to 20 maximum  | As in chosen pricing plan |
| Number of users | up to 20 recommended | As in chosen pricing plan |
| License | GNU AGPL v.3 | Proprietary |
| **Support** | **Community Edition** | **Enterprise Edition** |
| Documentation | [Help Center](https://helpcenter.onlyoffice.com/server/docker/opensource/index.aspx) | [Help Center](https://helpcenter.onlyoffice.com/server/integration-edition/index.aspx) |
| Standard support | [GitHub](https://github.com/ONLYOFFICE/DocumentServer/issues) or paid | One year support included |
| Premium support | [Buy Now](https://www.onlyoffice.com/support.aspx?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo) | [Buy Now](https://www.onlyoffice.com/support.aspx?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo) |
| **Services** | **Community Edition** | **Enterprise Edition** |
| Conversion Service                | + | + |
| Document Builder Service          | + | + |
| **Interface** | **Community Edition** | **Enterprise Edition** |
| Tabbed interface                       | + | + |
| White Label                            | - | - |
| Integrated test example (node.js)*     | - | + |
| Mobile web editors | - | + |
| Access to pro features via desktop     | - | + |
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
| Adding Content control          | - | + | 
| Editing Content control         | + | + | 
| Layout tools                    | + | + |
| Table of contents               | + | + |
| Navigation panel                | + | + |
| Mail Merge                      | + | + |
| Comparing Documents             | - | +* |
| **Spreadsheet Editor features** | **Community Edition** | **Enterprise Edition** |
| Font and paragraph formatting   | + | + |
| Object insertion                | + | + |
| Functions, formulas, equations  | + | + |
| Table templates                 | + | + |
| Pivot tables                    | + | + |
| Conditional formatting  for viewing | +** | +** |
| Sheet Views                    | - | + |
| **Presentation Editor features** | **Community Edition** | **Enterprise Edition** |
| Font and paragraph formatting   | + | + |
| Object insertion                | + | + |
| Animations                      | + | + |
| Presenter mode                  | + | + |
| Notes                           | + | + |
| | [Get it now](https://www.onlyoffice.com/download.aspx?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo)  | [Start Free Trial](https://www.onlyoffice.com/enterprise-edition-free.aspx?utm_source=github&utm_medium=cpc&utm_campaign=GitHubChamilo)  |

\*  It's possible to add documents for comparison from your local drive, from URL and from Chamilo storage.

\** Support for all conditions and gradient. Adding/Editing capabilities are coming soon

