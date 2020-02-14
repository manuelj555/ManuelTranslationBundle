# ManuelTranslationBundle


Bundle que permite la creación y edición de etiquetas de traducción desde la Base de datos 

<img src="https://raw.githubusercontent.com/manuelj555/ManuelTranslationBundle/master/Resources/doc/trans_page.png" alt="ManuelTranslation Backend" title="ManuelTranslation" width="70%" align="middle" />

---------------

**Puedes Crear y Modificar traducciones de etiquetas de manera simple y sin tocar archivos xml, yml, php...**

<img src="https://raw.githubusercontent.com/manuelj555/ManuelTranslationBundle/master/Resources/doc/form.png" alt="ManuelTranslation Form" title="ManuelTranslation" width="50%" align="middle" />

-------------

**Y además crear facilmente desde el Profiler las etiquetas sin traducir aun:**

<img src="https://raw.githubusercontent.com/manuelj555/ManuelTranslationBundle/master/Resources/doc/profiler.png" alt="ManuelTranslation Profiler" title="ManuelTranslation" width="80%" align="middle"  />

------

# Instalación
  
Agregar al composer.json:

```json
"require" : {
    "manuelj555/manuel-translation-bundle": "~5.0@dev"
}
```

Y ejecutar 

```
composer update 
```

Luego de ello, registrar los bundles en el **config/bundles.php**:

```php
return [
    ...
    ManuelAguirre\Bundle\TranslationBundle\ManuelTranslationBundle::class => ['all' => true],
    ...
];
```

En el **config/routes.yml** agregar:

```yaml
manuel_translation:
    resource: "@ManuelTranslationBundle/Controller/"
    type:     annotation
    prefix:   /{_locale}/admin/trans
#    requirements:
#        _locale: "%locales_pattern%"
``` 

Por último se debe crear la base de datos (si no se ha hecho aun) y agregar a la bd las tablas competentes al bundle, por lo que se deben ejecutar los siguientes comandos de consola:

    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

Además ejecutar el comando:
    
    php app/console assets:install

Con esto ya se ha instalado correctamente el bundle.


Configurando el bundle ManuelTranslationBundle:
___________

Este bundle nos permite editar y manejar traducciones desde la base de datos, pudiendo editarlas desde la página web, además nos permite sincronizar las traducciones entre el servidor y local del proyecto.

Para agregar su configuración se debe crear un archivo llamado **config/packages/manuel_translation.yaml**.

```yaml

manuel_translation:
    locales: [en, es] # Se deben definir los locales que el translator usará
    bundles:          # Los bundles de donde serán leidas las traducciones para pasarlas a la Base de Datos.
#        - AppBundle
```


