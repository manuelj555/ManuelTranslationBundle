# ManuelTranslationBundle


Bundle que permite la creación y edición de etiquetas de traducción desde la Base de datos 
Puedes Crear y Modificar traducciones de etiquetas de manera simple y sin tocar archivos xml, yml, php...
Y además crear facilmente desde el Profiler las etiquetas sin traducir aun.

------

# Instalación
  
Ejecutar 

```
composer require manuelj555/manuel-translation-bundle @dev
```

Luego de ello, registrar los bundles en el **config/bundles.php**:

```php
return [
    ...
    ManuelAguirre\Bundle\TranslationBundle\ManuelTranslationBundle::class => ['all' => true],
    ...
];
```

Configurando el bundle ManuelTranslationBundle:
___________

Este bundle nos permite editar y manejar traducciones desde la base de datos, pudiendo editarlas desde la página web, además nos permite sincronizar las traducciones entre el servidor y local del proyecto.

Para agregar su configuración se debe crear un archivo llamado **config/packages/manuel_translation.yaml**.

```yaml

manuel_translation:
    locales: [en, es] # Se deben definir los locales que el translator usará.
    security_role: ROLE_SUPER_ADMIN # Valor por defecto si no se define la opción.
```

En el **config/routes.yaml** agregar:

```yaml
manuel_translation:
    resource: "@ManuelTranslationBundle/Controller/"
    type:     annotation
    prefix:   /{_locale}/admin/trans
#    requirements:
#        _locale: "%locales_pattern%"
```

En el **config/packages/security.yaml** ajustar el access_control para el admin de traducciones:

```yaml
    access_control:
        - ...
        - { path: "^/[a-z]{2,4}/admin/trans", allow_if: 'is_granted("manage_translations")' }
        - ...
```

El `is_granted("manage_translations")` permite entrar en el administrador de traducciones cuando
se tiene el rol configurado en el `manuel_translation.security_role` o si se está ejecutando
el proyecto en un servidor local, en dado caso se puede acceder al admin de traducciones sin
necesidad de estar autenticado.

<hr/>

Por último se debe crear la base de datos (si no se ha hecho aun) y agregar a la bd las tablas competentes al bundle, por lo que se deben ejecutar los siguientes comandos de consola:

    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force

Además ejecutar el comando:
    
    php app/console assets:install

Con esto ya se ha instalado correctamente el bundle.


