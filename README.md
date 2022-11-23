# Collection Manager

This is a web-based collection manager.
It was designed to catalog a collection of dice, pens, books, games, and related items.
The base unit is an "Item" which can have a name, description, and cost.
Each item can have 0 or more photos.

Each item can also have 0 or more "Tags," which are types of item.
A tag could be "Pen," "Book," or "Game."
So, the user could search fo all pens in the collection.

Each item can be given 0 or more "Locations."
These are similar to items, but are meant to represent the physical location of the item.
This could be a "Closet," "Bedroom," or "Shelf #15."


## Technology

The app has a web client made with Vue 3 using the Quasar 2 control library.
The server REST API is written in PHP.
The database is MySQL.
There are configuration files and scripts to build and run the system in Docker.
The original Docker host used by the author is Windows, so there are PowerShell scripts used to clean up old images on the Windows server.


## Setting Up the Database

You'll need a MySQL (or comparable MariaDB) server.
To run the system, you'll need one user with CRUD rights to one schema.
To create the schema and the initial application user, you'll need superuser access to the database server.
There are two scripts in the `database` folder to help:

* The `Accounts.sql` script will create a schema called `Collections` and a user called `collections`.  This should be run on the database server first.
* The `Collections.sql` script will create the tables for the app.


## Buliding the Web Client

The web client can be developed locally or packaged for deployment with the Quasar CLI.
Here are the steps to run the client locally.

1. Install node on your development machine. Make sure it has npm as well.
2. Install quasar cli by running `npm i -g @quasar/cli`
3. Install the required node packages by running:
```
cd client
npm install
```
4. You'll need to edit the `client/src/utils/api.js` file to point to the URL of the API server.  The default is `http://localhost:8087/api/`, which is probably not what you'll use.  If you're hosting this locally in XAMPP, for example, you might use port 80.  The web server setup is below.
5. Run the Quasar development server in the `client` folder by running `quasar dev`

You can build the web client for deployment by running:
```
cd client
quasar build
```

The contents of the `dist/spa` folder will be ready to host by any web server.


## Building the API Server

The web API requires PHP 7.4 with Composer.
You can configure it by:

1. Get a PHP server.  You can rent one, get XAMPP for macOS, Apache for GNU/Linux, or figure something out on your smart watch.  You'll need the PHP CLI, so maybe the watch won't work well... unless you get an SSH server for it.
2. Install Composer.
3. in the `server` folder, run `composer install`
4. Edit the `server/Configuration.php` file to reflect the MySQL server you'll be using.  There's a `Configuration-Docker.php` file that has the same format.  That' required to build a Docker image, but you can copy that configuration file to `Configuration.ohp` and make it work.

Now, the content of the `server` folder can be served by a web server with PHP.


## Building a Docker Image

The `docker` folder has what's needed to build a Docker image.
There's a node script that can build the client and server, and then deploy those to a server running Docker.


## Running the Program

Once everything is built, you'll need to:

1. Go to the client URL in a web browser.  The Quasar development server usually runs on your computer using port 8080, so the URL might be `http://localhost:8080/`
2. Hit the login button in the upper right corner of the screen.  The default username is `admin` and the default password is `abc123`
3. You can now use the application.
