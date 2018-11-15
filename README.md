# DTN ownCloud plugin application

This application adds large file transfer capabilities to your ownCloud instance.
<br>
<br>

### <span style="">[Getting started](#getting-started)</span><span style="margin-left: 20%;">[Development](#development)</span>
##### <span style="margin-left: 6%;">[Installing](#installing) | [App settings](#app-settings) | [Usage](#usage)</span><span style="margin-left: 12%;">[ConfigProvider service](#configprovider) | [Notification service](#notification)</span>
<br>

## <span id="getting-started">Getting Started</span>

### <span id="installing">Installing</span>
Create a new 'dtn' directory in the ownCloud instance 'apps' dir and add the 'apps/dtn' files to it.

Add the following setting to the server config/config.php file: 
> 'filesystem_check_changes' => 1

This makes sure that transfered files will be discovered by ownCloud.

#### <span id="app-settings">App settings</span>
When ownCloud is up and running the administrator should activate the app from within ownCloud via Settings -> Admin -> Apps

When the app is activated each user should go to Settings -> Personal -> Additional and set the following user specific app value:
* Your DTN user id. This is the id that the agent uses to identify you for this ownCloud instance.

The administrator should set the the DTN agent server address. Go to Settings -> Admin -> Additional and set the following app specific value:
* The DTN agent ip and optional port (eg. 10.345.67.89:3000). This is used by the app to contact the DTN agent to do the file transfers.

### <span id="usage">Usage</span>
Select a file in the main file view. In the file option bar above the file list the 'DTN transfer' is shown. If you click it the DTN file transfer dialog will appear.

Fill in the receiver's DTN user id which should be know (it's the id the receiver has filled in as his/her DTN user id in the app settings, see 'App settings' above).

## <span id="development">Development</span>

The app has 2 REST services running, ConfigProvider and Notification

### <span id="configprovider">ConfigProvider</span>
This service provides user data path information, ie. the path where files of a user are stored relative to the ownCloud data directory (as set in ownCloud config.php).

Call (GET) it through endpoint: http://...server-ownCloud.../apps/dtn/config/datalocationinfo/{receiverDTNUID} where {receiverDTNUID} is the DTN user id that the receiver has set.

This endpoint requires authentication by an ownCloud user with admin rights.

Call example:
>curl -u admin:admin -b cookies.txt -c cookies.txt -H "Accept: application/json" http://127.0.0.1/owncloud/index.php/apps/dtn/config/datalocationinfo/user01@dtn.example.com

The response is in JSON, eg: 
>{  
>&nbsp;&nbsp;&nbsp;&nbsp;"userDataPath": "\/theusername\/files",  
>}


### <span id="notification">Notification</span>
This service accepts notification posts.

Call (POST) it through endpoint http://...server-ownCloud.../apps/dtn/notifier/notification

The post requires json notification data. Eg.:
>{  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"senderDTNUID": "sender@dtn.example.com",  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"receiverDTNUID": "receiver@dtn.example.com",  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"message": "A notification message"  
>}  

This endpoint requires authentication by an ownCloud user with admin rights.

Call example:
>curl -u admin:admin -b cookies.txt -c cookies.txt -H "Accept: application/json" -H "Content-type: application/json" -X POST -d {\"receiverDTNUID\":\"user01@dtn-agent.com\",\"senderDTNUID\":\"sender01@dtn-agent.com\",\"message\":\"A_notification_message\"} http://127.0.0.1/owncloud/index.php/apps/dtn/notifier/notification