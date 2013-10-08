#Logger Plugin for Vanilla Logger Plugin for Vanilla 2.0

##Description

This plugins implements a Logger in Vanilla. It is based on [Log4php](http://logging.apache.org/log4php/) and it offers great flexibility.

##Installation
Follow standard plugin installation procedure.

##Default configuration
Logger plugin has been designed to work out of the box in both Advanced and Basic versions. In its default configuration, it logs all messages to `LoggerSysLog` table in the same database used by your instance of Vanilla Forums. Such table is created the first time a message is logged.


The default Log Level is **INFO**. This means that log messages with a lower level will be ignored. To change it, use the User Interface (Advanced Logger), or modify the `config.xml` file by adding the line `<level value="{new_log_level}" />` in the `<root>` node, replacing `{new_log_level}` with your desired Log Level.

To view the allowed values of the Log Level, please visit [Log4php website](http://logging.apache.org/log4php/docs/configuration.html).

##Configuration

###Advanced Version
Advanced Logger for Vanilla offers a convenient User Interface to configure the various settings for the Plugin. To access it, simply open Vanilla Administration page and click on `Plugins -> Logger`. The interface has been designed to be very intuitive, and all its parts are thoroughly documented.

One of the best features of the Advanced Version over the Basic is the ability of sending log messages to remote Log Servers, such as [PaperTrail](http://papertrailapp.com), [Loggly](http://loggly.com) or even a Remote SysLog server.

This allows to centralize all the logs, removing the need to fetch files from each website and saving a significant amount of time. This feature alone is invaluable for any Administrator who manages several communities.

###Basic Version

Basic Version provides a reduced set of features, and doesn't come with a User Interface for the configuration. If you wish to change the configuration, you have to modify file `config.xml`, which can be found in plugin's directory. Information on how to write a configuration file can be found on [Log4php website](http://logging.apache.org/log4php/docs/configuration.html).

*Important*
Basic Version only supports the appenders provided by standard Log4php, plus an additional one to write log messages to a table in Vanilla database.

##Usage
This plugin has been designed to be used by other plugins, or even by Vanilla Core libraries. Using it is straightforward (see the example below):

###Example - Using the Logger

~~~
// Get the Logger instance
$Logger = LoggerPlugin::GetLogger();

// Log several messages, one for each available level
$Logger->trace('This is a TRACE message');
$Logger->debug('This is a DEBUG message');
$Logger->info('This is an INFO message');
$Logger->warn('This is an WARNING message');
$Logger->error('This is an ERROR message');
$Logger->fatal('This is an FATAL message');
~~~

##Support
If you have any question or suggestion, [please contact us](http://dev.pathtoenlightenment.net/contact/). To receive support, please open a ticket [using our support portal](https://aelia.freshdesk.com/support/home), and we will get back to you as soon as possible.
