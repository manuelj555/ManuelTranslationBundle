# ManuelTranslationBundle

Bundle que permite la edición de etiquetas de traducción desde la Base de datos

<img src="https://raw.githubusercontent.com/manuelj555/ManuelTranslationBundle/master/Resources/doc/trans_page.png" alt="ManuelTranslation Backend" title="ManuelTranslation" width="100%" style="border: 1px solid #aaa" />


|  Hola          |     que      |
---------------------------------
|   si          |     claro        |


<img src="https://raw.githubusercontent.com/manuelj555/ManuelTranslationBundle/master/Resources/doc/form.png" alt="ManuelTranslation Form" title="ManuelTranslation" width="40%" align="right" />

**Puedes crear y Modificar etiquetas de manera simple y sin tocar archivo xml, yml, php...**

* Establecer el dominio y el nombre de la etiqueta
* Establecer las traducciones en base a los locales que estes implementando

------

**Y además crear facilmente desde el Profiler las traducciónes que no se han creado aun:**

------
<img src="https://raw.githubusercontent.com/manuelj555/ManuelTranslationBundle/master/Resources/doc/profiler.png" alt="ManuelTranslation Profiler" title="ManuelTranslation" width="60%" style="border: 1px solid #aaa" />

------

# Instalación
  
Agregar al composer.json:

```json
"require" : {
    "manuelj555/manuel-translation-bundle": "dev-master"
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



