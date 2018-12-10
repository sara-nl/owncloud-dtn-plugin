# DTN ownCloud plugin application

This application adds large file transfer capabilities to your ownCloud instance by making use of a Data Transfer Network. 

Data Transfer Nodes (DTNs) are a hardware and software system that allows for fast and efficient transport of data over long distances. This application was developed in an effort to build a user-friendly frontend for file transfer using DTNs.  

##### How it works

An(each) ownCloud instance is connected to the DTN through a DTN agent. This agent is called by the ownCloud app with a request to transfer (a set of chosen, currently max 1 file) files to a receiver which is also linked to the DTN. The receiver may be hooked up with another type of file system, but through a DTN agent. 

![Accelerated Cross Domain Transfer](https://raw.githubusercontent.com/sara-nl/owncloud-dtn-plugin/master/acdt.PNG "Accelerated Cross Domain Transfer")

1. The user commands a file transfer through the app's file transfer display.  
2. The DTN agent on the sending end requests the path of the file to be transfered using the [location service](#configprovider) that is available in the app. The agent uses this path to copy the file so it can transfer it, using its transfer mechanism, to the receiving DTN agent. 
3. The receiving DTN agent makes a requests to the receiving application for the files' base location of the specified receiver, ie. where the file needs to be placed.
4. The DTN transfer mechanism performs the actual file transfer end to end.
5. If the receiving end is also an ownCloud instance the DTN agent at the receiving end may choose to notify the receiver about the transfer using the app's [notification service](#notification).

_Important note: The status of this application is proof of concept. It is by no means production ready._

---

### [Getting started](#getting-started)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Services](#services)
##### &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Installing](#installing) | [App settings](#app-settings) | [Usage](#usage)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[ConfigProvider service](#configprovider) | [Notification service](#notification)  
---

### [Development Guideline](#dev-guide)
##### &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[Setting up the development environment](#dev-guide-setup) | [Unit testing](#unit-testing) | [Continuous Integration](#ci)

---
## <span id="getting-started">Getting Started</span>

### Prerequisites
The application is an ownCloud App running in an ownCloud instance and communicates with a DTN agent. ownCloud can be obtained [here](https://www.owncloud.org), the DTN agent and installation instructions for it can found [here](https://bitbucket.org/uva-sne/dtn_agent).

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

## <span id="services">Services</span>

The app has 2 REST services running, ConfigProvider and Notification. 
It is with these 2 services that the 

### <span id="configprovider">ConfigProvider</span>
This service provides user data path information, ie. the path where files of a user are stored relative to the ownCloud data directory (as set in ownCloud config.php).

(GET) user data path information through endpoint: https://...server-ownCloud.../apps/dtn/config/datalocationinfo/{receiverDTNUID} where {receiverDTNUID} is the DTN user id that the receiver has set.

This endpoint requires authentication by an ownCloud user with admin rights.

Call example:
>curl -u admin:passw -b cookies.txt -c cookies.txt -H "Accept: application/json" https://127.0.0.1/owncloud/index.php/apps/dtn/config/datalocationinfo/user01@dtn.example.com

The response is in JSON, eg: 
>{  
>&nbsp;&nbsp;&nbsp;&nbsp;"userDataPath": "\/theusername\/files",  
>}


### <span id="notification">Notification</span>
This service accepts notification posts.

(POST) a notification through endpoint https://...server-ownCloud.../apps/dtn/notifier/notification

The post requires json notification data. Eg.:
>{  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"senderDTNUID": "sender@dtn.example.com",  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"receiverDTNUID": "receiver@dtn.example.com",  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"message": "A notification message"  
>}  

This endpoint requires authentication by an ownCloud user with admin rights.

Call example:
>curl -u admin:passw -b cookies.txt -c cookies.txt -H "Accept: application/json" -H "Content-type: application/json" -X POST -d {\"receiverDTNUID\":\"user01@dtn.example.com\",\"senderDTNUID\":\"sender01@dtn.example.com\",\"message\":\"A notification message\"} https://127.0.0.1/owncloud/index.php/apps/dtn/notifier/notification
---
## <span id="dev-guide">Development Guideline</span>

### <span id="dev-guide-setup">Setting up the development environment</span>

The DTN plugin is developed as an ownCloud application according to ownCloud’s [application development guide](https://doc.owncloud.org/server/10.0/developer_manual/app/).
 
The application’s source code is separated from ownCloud’s source code. It resides as a separate directory inside the ownCloud app directory and is immediately recognized as an App by ownCloud when the server runs.

Our development setup makes use of this code separation for it adds the necessary ownCloud classes as dependencies through the [Composer dependency manager](https://getcomposer.org/) and that way it can be developed as an individual project. 

#### <span id="dev-env-setup">Development environment setup</span>
_Prerequisites: Composer dependency manager must be [installed](https://getcomposer.org/) on your machine_

Git clone (https://github.com/sara-nl/owncloud-dtn-plugin.git) the source code into the IDE of your choice as a separate project. Run Composer, referring to the composer.json file in the root dir of the project, to build all class dependencies.

Eg. run from project root dir:
>$ php composer.phar install

<span id="include-unit-tests-src">Next we need the ownCloud tests source in order to run our unit tests. This test code dependency is included in the repository (tests/ dir).</span>

The resulting minimal project directory structure should be like:
DTN-ownCloud-plugin project root/
>&nbsp;&nbsp;&nbsp;&nbsp;apps/  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;dtn/  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;... plugin source and test code  
>&nbsp;&nbsp;&nbsp;&nbsp;tests/  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;... ownCloud tests source  

Next, setup the development runtime environment.

#### Runtime environment setup: docker-compose
_Prerequisites: Docker Compose must be installed on your machine_

We set up a development runtime environment using the docker compose setup that is [made available](https://github.com/owncloud-docker/base#launch-with-docker-compose) by ownCloud. This setup consists of 2 files: an .env file with the variables specified and the docker-compose.yml file. These files can also be found in the repository (resources/ dir).

Next to the regular data volume we add two source volumes from local to the container: 

1. A mapping to our application source code.
2. A mapping to ownCloud’s tests source. The ownCloud Docker container does not contain the unit test files that we need as a unit test requirement. However we already [included them](#include-unit-tests-src) into our project so we simply use a source volume for that as well.

From the docker-compose.yml file: 
>&nbsp;&nbsp;&nbsp;&nbsp;volumes:  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- files:/mnt/data  
>  <br>
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# dev plugin code  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- {...local path to...}/ownCloud-DTN-plugin/apps/dtn:/var/www/owncloud/apps/dtn  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;# tests requirements  
>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- {...local path to...}/ownCloud-DTN-plugin/tests:/var/www/owncloud/tests  

Now start the containers in the background:
>docker-compose up –d

Follow the server logging:
>docker-compose logs --follow owncloud

Go inside the ownCloud container:
>docker-compose exec owncloud bash

Now you can start developing. Any code change will have an immediate effect on the running ownCloud instance.

#### <span id="unit-testing">Unit testing</span>
Unit testing needs a running ownCloud environment (see [ownCloud doc](https://doc.owncloud.org/server/10.0/developer_manual/core/unit-testing.html)) which means in our case that it’s easiest to run the tests in the same ownCloud docker container that is used for development. 

The tests require the ownCloud specific test classes to be available. We already tackled this by making the ownCloud tests src [available](#include-unit-tests-src) within the docker container. This has the advantage that we can use the ownCloud unit tests bootstrap mechanism for our tests.

For that our tests bootstrap file refers to the ownCloud tests bootstrap file. 
>Run a specific DTN test like so:
>$ /var/www/owncloud/apps/dtn/tests php phpunit --colors --bootstrap bootstrap.php Controller/DtnControllerTest.php

Run all tests in the namespace starting with OCA\DTN\Tests (starting from the current directory going downwards):
>php phpunit --colors --bootstrap bootstrap.php --filter 'OCA\\\\DTN\\\\Tests' ./

For using this setup in the CI process see [Continuous Integration](#CI).

##### Frontend (javascript) testing
The ownCloud frontend testing environment configuration itself contains quite a lot of packages and tools that are either old, deprecated, not maintained or near to being abandoned.
We suggest to setup a contemporary separate frontend testing configuration for the application alone. Currently this has not been configured for this app.

#### <span id="ci">Continuous integration</span>
For running the tests in a continuous integration process we use a similar setup. Then a script is needed that starts up the ownCloud (container) instance through Docker Composer, runs the tests inside this container, stops the container and cleans up containers and their associated volumes.

The CI tests runners windows and unix scripts inside the resources/jenkins dir use such a mechanisme. They have been tested in a [Jenkins CI](https://jenkins.io/) setup.

_Note: Running the CI server itself inside a container would complicate things somewhat, you would need Docker inside Docker to run the tests. Such a setup has not been tested._
