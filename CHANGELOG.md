Doolox Changelog
================

### v1.2.1 ###

* WordPress Template Viewer
* Put Zopim on Doolox website
* Lock PHP to user's home folder
* Error reporting to mail
* Fix documentation
* Bug - check strlen for subdomain when installing new website
* Bug - change the way of WordPress installation when new domain in question
* Automatically activate Doolox Node plugin when adding new website (or connecting existing one)
* Put link on Synkee
* Repair new users email subscription
* Remove hardcoded wp-login.php
* Make usage stats more decent

### v1.2.0 (Feb 13 2014) ###

* Create connecting tutorial
* WordPress download caching
* Setup newsletter
* Subscribe new users to newsletter
* FastSpring Google Analytics
* Setup real Name.com API credentials
* Migrate doolox.com to PostgreSQL
* Facebook like box in sidebar on doolox.com
* Disable registration on naklikaj.com
* Setup FTP server for Doolox users
* Name.com API configuration
* Automatic domain activation
* Upgrade - make more visible
* Fix WordPress serialized database data
* Fix pricing tooltips
* Automatic plugin installation
* Fix tab indexes on all forms
* Self-hosted - private/public key scheme
* Self-hosted - installation wizard
* Bug - remember me
* Packaging script
* Fb & WP cover image
* Report Doolox Node installation success and error
* Populate dooloxpkg on site install and add (general public key)
* Check requirements in installation wizard (php5-curl, php5-mcrypt, php5-sqlite | php5-mysql | php5-pgsql)

### v1.1.0 (Feb 2 2014) ###

* Option to remember a logged in user
* Integrated Laravel Cpanel
* Self hosted WordPress installation
* Add "Install New" button on dashboard with existing sites
* Fill in md5password for user when creating
* Create user directory when creating user (and delete when deleting)
* Remove all sensitive data from the Git
* Put local / remote site status on dasboard and single site display
* New site install form doesn't get re-populated if doesn't pass validation
* Add favicon
* Registration
* Bug - new site install passes validation without the subdomain (when not system domain)
* Missing domain functions (+ name.com API)
* Install new version on the server as a SAAS
* Owner of the domain - passes when not checked and doesn't work with submiting the form
* Connected callback needs Doolox app URL in connect API call
* WordPress move tool
* Move - disable for remote sites
* Changing email should change user folder and all his website links
* Finish plugin - function names
* Switch features on and off depending on the doolox settings parameters
* SaaS - hosted models, packaging, payment
* Fix FS emails
* SaaS - upgrade and payment + upgrade notification
* Remove email activation (there's no need for it with our free plan)
* Google Analytics + Goals
* Donation page
* Bug - deleting user deletes websites that it shouldn't
* Forms validation checkup (leading/trailing slashes, http://, ...)
* Downloads page

### v1.0.0 (Jan 19 2014) ###

* First version
* Custom filter for checking ownership
* Account management
* User management on project
* User management for superuser
* User management security
* User management settings
* Fix dropdown menu - show when it needs to be shown
* Bootbox dialogs
* WordPress login credentials security