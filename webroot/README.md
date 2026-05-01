# README #

### Setup ###
Copiar index.php.dist y renombrar a index.php # éste sirve para saber si estás en producción, pruebas o desarrollo.
Copiar application/config/database.php.dist y renombrar a application/config/database.php # éste sirve para conectarte a la base de datos.
Copiar assets/img/logo.png.dist y renombrar a logo.png # éste sirve para tener la imagen del sistema.
Cambiar el environment en index y los datos de conexión a los de la base.
Verificar que las carpetas upload y files pertenezcan a www-data, asterisk o el respectivo usuario de servidor web y que tenga permisos de escritura

### Funcionamiento ###
Dentro de la carpeta ~/gitrepos ejecutar:
    git init --bare assertive.git

Dentro de la carpeta ~/gitrepos/assertive.git/hooks crear el archivo post-receive y copiar contenido del 34.

Dentro de var/www crear la carpeta upload con grupo y propietario asterisk y permiso 755.

### Extensiones Apache necesarias ###
* rewrite
* ssl

### Extensiones necesarias PHP 5.6 - 8.1 ###
* curl
* gd2
* json
* mcrypt (deprecada, pero todavía se usa en algunas contraseñas, si el usuario no puede entrar con su propia contraseña, entrar con admin y cambiarla)
* mbstring
* xml

### Planner ###
* Luis Orozco

### Project Manager ###
* Gabriel Muñoz

### Developers ###
* Yaroslav Soriano
* Cristopher Rustrian

### Puente Asterisk, FreePBX ###
* Aldo Martínez

### Soporte Técnico ###
* Genaro González
