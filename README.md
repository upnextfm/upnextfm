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
git clone https://github.com/upnextfm/upnextfm.git
cd upnextfm
npm install
composer install
npm run build
```

Install the parameters.yml file per @headzoo's instructions, and then run the database migrations.

```
php bin/console doctrine:migrations:migrate
```

Out of the box the dev site is configured to run on the domain dev.upnext.fm. You can create the domain on your PC by adding `127.0.0.1 dev.upnext.fm` to your _hosts_ file. See [the instructions here](https://support.rackspace.com/how-to/modify-your-hosts-file/) for editing the file.

## Running
Setup Nginx using the [example configuration](docs/nginx.md) or run the Symfony dev web server using the following command.

```
php bin/console server:run
```

The app has a socket server which needs to be running in order for the chat rooms to work. It can be started using the following command:

```
npm run server
```
