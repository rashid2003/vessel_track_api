# Vessels Track API

## After Cloning

**Install all the dependencies using composer**
````
  composer install

````
**Copy the distribute env file and make the required configuration changes in the .env file (you should change the sertificate key and database connection)**
````
  cp .env.example .env

````
**Generate a new application key**
````
  php artisan key:generate

````

**Run this command to create database**
````
  php artisan mysql:createdb
  
````

**Update your connection creds in .env**
````
  DB_DATABASE=vessel_track
  .....
  
````

**Run the database migrations (Set the database connection in .env before migrating)**
````
  php artisan migrate

````
**Start the local development server**
````
  php artisan serve

````

**Route for retrieving data in different contentTypes**
````
  http://{{base_url}}/api/vessel-list  {with mmsi, time range filters}, also if csv flag passed as true, result comes in csv

````

**Route for uploading json file, sampleFile.json is the template used**
````
  http://{{base_url}}/api/upload {with request limit per user to 10/hour}
  
````
