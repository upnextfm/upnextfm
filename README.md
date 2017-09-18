upnext.fm
=========
The new and improved upnext.fm website.

## Requirements
* PHP 7.0 with Composer
* MySQL 5.6
* Nginx 1.1 (Apache may work as well)
* Node 8.0
* Redis 3.2

## Installing

Clone the repo to your web root and install the dependencies. You will need to get a copy of the parameters.yml from @headzoo, which needs to be saved to the project directory after cloning.

```
git clone git@github.com:upnextfm/upnextfm.git
cd upnextfm
npm install
composer install
```

Setup Nginx using the [example configuration](docs/nginx.md).

## Running
The app has a socket server which needs to be running in order for the chat rooms to work. It can be started using the following command:

```
npm run server
```
