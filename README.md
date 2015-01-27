# ManuelTranslationBundle
Bundle que permite la edición de etiquetas de traducción desde la Base de datos

# Instalación
  
Agregar al composer.json:

```json
"require" : {
    "manuelj555/upload-data-bundle": "dev-master"
}
```

Y ejecutar 

```
composer update 
```

Luego de ello, registrar los bundles en el **AppKernel.php**:

```php
public function registerBundles()
{
    $bundles = array(
        ...
        new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(), //solo si no esta antes agregado
        new ManuelAguirre\Bundle\TranslationBundle\ManuelTranslationBundle(),
    );
    
    ...
}
```

En el **app/config/routing.yml** agregar:

```yaml
manuel_translation:
    resource: "@ManuelTranslationBundle/Controller/"
    type:     annotation
    prefix:   /{_locale}/_trans

manuel_translation_api:
    resource: "@ManuelTranslationBundle/Controller/Api"
    type:     annotation
    prefix:   /api
``` 

Por ultimo se debe crear la base de datos (si no se ha hecho aun) y agregar a la bd las tablas competentes al bundle, por lo que se deben ejecutar los siguientes comandos de consola:

    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

Además ejecutar el comando:
    
    php app/console assets:install

Con esto ya se ha instalado correctamente el bundle.


Configurando el bundle ManuelTranslationBundle:
___________

Este bundle nos permite editar y manejar traducciones desde la base de datos, pudiendo editarlas desde la página web, además nos permite sincronizar las traducciones entre el servidor y local del proyecto.

```yaml
manuel_translation:
    bundles:          # Los bundles de donde serán leidas las traducciones para pasarlas a la Base de Datos.
#        - AppBundle
```



