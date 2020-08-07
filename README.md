# ComAct (dnyComAct) developed by Daniel Brendel

(C) 2019 - 2020 by Daniel Brendel

**Version**: 1.0\
**Contact**: dbrendel1988(at)gmail(dot)com\
**GitHub**: https://github.com/danielbrendel

Released under the MIT license

## Description:
ComAct is a software system that lets users create and join activities. Users
can set themselves interested in an activity or join them. Activities whose
activity date is in the future are listed in a feed. Users can comment on activities.
For a full feature list see the list below.

## Features:
+ Create and join activities
+ Add comments to activities
+ Reply to comments
+ Filter activities
+ Activity tags
+ Actual and potential participation
+ Activity management
+ User profiles
+ Profile management
+ Favorites system
+ Notification system
+ Messaging system
+ E-Mail system
+ Administration
+ Maintainer system
+ Theme system
+ Twitter news integration
+ HelpRealm integration
+ Installer
+ Security
+ Responsive design
+ Testcases
+ Documentation

## System requirements
The product is being developed with the following engine versions:
+ PHP 7.4.6
+ MySQL 10.4.11-MariaDB
+ Default XAMPP 3.2.4 enabled PHP extensions

## Installation:
Place a file 'do_install' in the root directory of the project.
Then go to /install. The setup wizard will guide you through the
installation process.

## Testing
In order to run the tests successfully you need to ensure that the following test data is valid:
+ TEST_USERID: ID of an existing user
+ TEST_USERID2: ID of another existing user. May not be the same as the above user
+ TEST_USEREMAIL: E-Mail of an existing user
+ TEST_USERPW: Password for login used together with TEST_USEREMAIL
+ TEST_ACTIVITYID: ID of an existing activity
+ TEST_MESSAGEID: ID of an existing message
+ TEST_THREADID: ID of an existing thread (parent)

