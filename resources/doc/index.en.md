Description
--------------

Genius Referrals in an attempt to improve the integration process with its services has created the GRPHPSandbox application. Which allows, through PHP, to show customers the integration process with the Genius Referrals platform in a sample application.

Instalation:
------------

The installation process of this application is very simple and can be done in several ways.

# 1- First download the zip of the application using this link [GRPHPSandbox](https://github.com/GeniusReferrals/GRPHPSandbox/archive/master.zip), 
unzip the package and keep it within your web server. 

# 2- Install vendor GRAPIPHPClient with it's dependencies, needed for the GRPHPSandbox app development.

### Using Composer

We recomend Composer for installing the app.

#### 1- Install Composer

```cd``` in the app directory (eg: my_project) and execute:

```
curl -sS https://getcomposer.org/installer | php
```

#### 2- Add the package GRAPIPHPClient as a dependency executing:  

```
php composer.phar require geniusreferrals/gr-api-php-client:dev-master
```

### Using Git

#### 1- Clone the repository 

If you don't want to use Composer, you can install the package by cloning the repository. 

```cd``` in the directory where you want to clone the package and execute: 

```
git clone git@github.com:GeniusReferrals/GRAPIPHPClient.git
```

### Downloading the client GRAPIPHPClient

#### 1- Download the package manually

Download the client zip using this link [GRAPIPHPClient](https://github.com/GeniusReferrals/GRAPIPHPClient/archive/master.zip), 
unzip the package and save it in your project directory.


App structure
-------------

The app has 2 pages to show all the integration process:

### 1- Manage advocate, where you can do the following:

1- List advocate
2- Search advocate
3- Create advocate

Per advocate you can to the following:

1- Refer a friend program
2- Create referrer
3- Process bonus
4- Checkup bonus

### 2- Refer a friend program (4 tabs)

1- Overview
2- Referral tools
3- Bonuses earned
4- Redeem bonuses


To report issues use [Github issue tracker.](https://github.com/GeniusReferrals/GRPHPSandbox/issues)
