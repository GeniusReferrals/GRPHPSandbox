Descripción:
------------

Genius Referrals en un intento de mejorar el proceso de integración con sus servicios ha creado esta aplicación GRPHPSandbox. La cual permite, a través de PHP, mostrarle a los clientes el proceso de integración con la plataforma de GR en una aplicación de ejemplo.

Instalación:
------------

El proceso de instalación de esta aplicación es muy sencillo y puede hacerse de varias formas.

# 1- Primeramente descargue el zip de la aplicación usando este vínculo [GRPHPSandbox](https://github.com/GeniusReferrals/GRPHPSandbox/archive/master.zip), 
unzip el paquete y guardelo dentro de su servidor web.

# 2- Instalar vendor GRAPIPHPClient con sus dependencias, necesarias para el desarrollo de la aplicación GRPHPSandbox.

### Usando Composer

Recomendamos composer para intallar la aplicación.

#### 1- Installar Composer

```cd``` en el directorio de la aplicación (ej: my_project) y ejecute:

```
curl -sS https://getcomposer.org/installer | php
```

#### 2- Adicionar el packete GRAPIPHPClient como una dependencia ejecutando:  

```
php composer.phar require geniusreferrals/gr-api-php-client:dev-master
```

### Usando Git

#### 1- Clonar el repositorio 

Si usted no quiere usar composer, puede instalar el paquete clonando el repositorio. 

```cd``` en la carpeta donde quiere clonar el paquete y ejecute: 

```
git clone git@github.com:GeniusReferrals/GRAPIPHPClient.git
```

### Descargando el cliente GRAPIPHPClient

#### 1- Descargar el paquete manualmente

Descargue el zip del cliente usando este vínculo [GRAPIPHPClient](https://github.com/GeniusReferrals/GRAPIPHPClient/archive/master.zip), 
unzip el paquete y guardelo dentro del directorio de su projecto.


Estructura de la aplicación
---------------------------

La aplicación consta de 2 páginas que se describen a continuación:

### 1- Manage advocate, en la cual se pueden realizar las siguientes funcionalidades:

- List advocate: Listar los promotores 
- Search advocate: Buscar promotores
- Create advocate: Crear nuevos promotores

Por cada promotor (advocate) se pueden realizar las siguientes funcionalidades:

- Refer friends: Referir amigos
- Create referrer: Adicionar el referente al promotor
- Process bonus: Procesar bonos
- Checkup bonus: Chequer nuevos bonos

### 2- Programa de referencia de amigos (Refer a friend program) (Consta de 4 tabs)

- Overview: Resumen del programa de referencia
- Referral tools: Herramientas para referir
- Bonuses earned: Bonificaciones optenidas
- Redeem bonuses: Canjear bonificaciones


Para reportar un problema utilice [Github issue tracker.](https://github.com/GeniusReferrals/GRPHPSandbox/issues)
