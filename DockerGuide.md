# Documentación de instalación, uso y problemáticas con *Docker*

1. [¿Qué es _docker_? Uso y principales conceptos](#intro)
2. [Preparando el sistema](#settint-up-the-system)
3. [Instalando Docker](#installation)


## <a name="intro"></a>¿Qué es *docker*? Uso y principales conceptos
**Docker**: Es un proyecto open source para empaquetar, transportar y ejecutar cualquier aplicación como un
contenedor ligero.
**Imagen**: Una imagen está formada por capas (layers) que se montan unas encima de otras. Todas en modo sólo lectura.
**Dockerfile**: fichero de instrucciones para construir una imagen.
**Docker-compose**: Herramienta para definir en un fichero yaml los diversos contenedores para realizar un despliegue de aplicaciones.
**Contenedores**: Hacen uso de una imagen como base y pueden contener uno o más procesos.

## <a name="settint-up-the-system"></a>Preparando el sistema

1. Desinstalar viejas versiones en caso de que estén instaladas:
 `$ sudo apt-get remove docker docker-engine`

2. Instalando linux-image-extra- packages correspondientes a la versión instalada:
 `$ sudo apt-get update`
 `$ sudo apt-get install linux-image-extra-$(uname -r) linux-image-extra-virtual`

3. Instalamos los paquetes necesarios para permitir el uso de repositorios sobre HTTPS:
 ```
 $ sudo apt-get install \
    apt-transport-https \
    ca-certificates \
    curl \
    software-properties-common
 ```

4. Añadimos la clave GPG oficial:
 ```
 $ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo apt-key add -
 ```

5. Establecemos el reposotorio *stable*. **Siempre** hay que instalar **_stable_**:
 ```
 $ sudo add-apt-repository \
   "deb [arch=amd64] https://download.docker.com/linux/ubuntu \
   $(lsb_release -cs) \
   stable"
 ```

## <a name="installation"></a>Instalando docker

1. Instalamos el paquete desde los repositorios (para desarrollo):
 ```
 $ sudo apt-get update
 $ sudo apt-get install docker-ce
 ```

2. Verificamos que está correctamente instalado:
 ```
 $ sudo docker run hello-world
 ```

[Posibles problemas (Configuración Proxy)](#proxy)

# Configuracion y recomendaciones post-instalación

## Configurando el Proxy

### Para systemd:
1. Creamos un directorio para dejar las configuraciones de systemd:
 ```
 $ sudo mkdir -p /etc/systemd/system/docker.service.d
 ```

2. Creamos el fichero de configuración del proxy http y otro para https:
 ```
 $ sudo vim /etc/systemd/system/docker.service.d/http-proxy.conf
 ```
 Pegamos el contenido:
 ```
 [Service]
 Environment="HTTP_PROXY=http://proxy.seap.minhap.es:8080/"
 Environment="HTTPS_PROXY=https://proxy.seap.minhap.es:8080/"
 Environment="NO_PROXY=localhost,127.0.0.0/8"
 ```

3. Flusheamos cambios y reiniciamos el servicio:
 ```
 $ sudo systemctl daemon-reload
 $ sudo systemctl restart docker
 ```

4. Verificamos que se haya cargado con éxito la configuración:
 ```
 $ systemctl show --property=Environment docker
 ```

### Para SysVinit(*service*, Ubuntu **14**):

1. Añadiremos las siguientes líneas al fichero `/etc/default/docker`:
 ```
 export http_proxy="http://proxy.seap.minhap.es:8080/"
 export https_proxy="https://proxy.seap.minhap.es:8080/"
 ```

2. Reiniciamos Docker:
 ```
 $ sudo service docker restart
 ```

## Docker compose
Compose is a tool for defining and running multi-container Docker applications. With Compose, you use a Compose file to configure your application’s services. Then, using a single command, you create and start all the services from your configuration. To learn more about all the features of Compose see the list of features.

Compose is great for development, testing, and staging environments, as well as CI workflows. You can learn more about each case in Common Use Cases.

Using Compose is basically a three-step process.

1. Define your app’s environment with a Dockerfile so it can be reproduced anywhere.

2. Define the services that make up your app in docker-compose.yml so they can be run together in an isolated environment.

3. Lastly, run `docker-compose up` and Compose will start and run your entire app.

### Instalación
1. Descargarmos el ejecutable por ejemplo en `/usr/local/bin`:
 `$ curl -L https://github.com/docker/compose/releases/download/1.13.0/docker-compose-`uname -s`-`uname -m` > /usr/local/bin/docker-compose`

2. Otorgamos permisos de ejecución
 `sudo chmod +x /usr/local/bin/docker-compose`

## Docker sin sudo (opcional)

1. Creamos el grupo `docker`:
 ```
 $ sudo groupadd docker
 ```

2. Añadimos nuestro usuario a dicho grupo:
 ```
 $ sudo usermod -aG docker $USER
 ```

3. Cerramos y abrimos sesión para reevaluar los permisos.

4. Confirmamos que podemos lanzar *docker* sin sudo lanzando, por ejemplo, `$ docker run hello-world`.

## Portainer

Portainer es un una interfaz de gestión de Docker, desde el se pueden gestionar los contenedores e, incluso,
ofrece acceso mediante terminal desde la propia web. Portainer es en si mismo un contenedor de Docker con lo que para
correrlo simplemente haremos lo siguiente.

1. Lanzamos el contenedor:
 `sudo docker run -d -p puerto_host:9000 -v /var/run/docker.sock:/var/run/docker.sock portainer/portainer`

2. Accedemos a portainer que estará accesible en nuestro localhost bajo el puerto que hayamos indicado `localhost:puerto_host`
3. Configuramos una clave.
4. Una vez logados seleccionamos *Manage the Docker instance where Portainer is running*.
5. Profit.

**_TODO:_** Esto es muy básico por lo que cualquiera es bienvenido a mejorar la información. La [Documentación está aquí](https://portainer.readthedocs.io/en/stable/)

# Info y miscelanea

### Logs
`Ubuntu (old using upstart ) - /var/log/upstart/docker.log`
`Ubuntu (new using systemd ) - journalctl -u docker.service`

### Principales comandos
- `$ docker info   #Información general de contenedores e imagenes`
- `$ docker images #Información acerca de las imágenes`
- `$ docker ps		  #Información de los contenedores que están corriendo`
- `$ docker run 		#Crear contenedor a partir de una imagen`
- `$ docker login -u sgadpsf -pKFy8giA32`
- `$ docker ps -a`
- `$ docker stop  id_contenedor`
- `$ docker images`

### Run:
`$ docker run -d -p 80:80 --name my-apache-php-app -v "$PWD":/var/www/html php:7.0-apache`

### Run with compose:
`$ docker-compose up -d`

### Build Image:
`$ docker build -t php:5.5 .`

### Container's ips:
`$ docker inspect -f '{{.Name}} - {{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' $(docker ps -aq)`

### Delete all images:
`$ docker rmi $(docker images -q) --force`

### Delete all containers:
`$ docker rm $(docker ps â€“a -q)`

### Remove stopped containers:
`$ docker rm `docker ps --no-trunc -aq``

### List all untagged images:
`$ docker images -q --filter "dangling=true"`

### Remove images unnused
`$ docker rmi $(docker images --filter "dangling=true" -q --no-trunc)`

### Remove all untagged images:
`$ docker images -q --filter "dangling=true" | xargs docker rmi`

### Bash form image (not running container)
`$ docker run -i -t --entrypoint /bin/bash <imageID>`

### Bash attach to running cotainer
`$ docker exec -it <containerIdOrName> /bin/bash`

### Redis-cli for goal (i.e.)
`$ docker-compose run goal-redis redis-cli -h goal-redis`