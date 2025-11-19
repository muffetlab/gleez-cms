# Introduction

The __Gleez Log__ component monitors your website, capturing system events in a log to be reviewed by an authorized individual at a later time. The log is simply a list of recorded events containing usage data, performance data, errors, warnings and operational information. It is vital to check the log report on a regular basis as it is often the only way to tell what is going on.

## Supported Log Writers

+ __File__ - Writes out messages and stores them in a YYYY/MM directory
+ __STDERR__ - Writes out messages to STDERR
+ __STDOUT__ - Writes out messages to STDOUT
+ __Syslog__ - Writes out messages to syslog

## Usage

You can specify the log writer at bootstrap time in `/application/bootstrap.php` file:

~~~
// Using file log writer
Kohana::$log->attach(new Log_File(APPPATH.'logs'));

// Using STDERR log writer
Kohana::$log->attach(new Log_StdErr());

// Using STDOUT log writer
Kohana::$log->attach(new Log_StdOut());

// Using Syslog log writer
Kohana::$log->attach(new Log_Syslog());
~~~

Multiple writers are supported.
