<?php
/*
 * This file is part of the Manuel Aguirre Project.
 *
 * (c) Manuel Aguirre <programador.manuel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManuelAguirre\Bundle\TranslationBundle\DataCollector;

use ManuelAguirre\Bundle\TranslationBundle\Translation\DebugTranslator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;


/**
 * @autor Manuel Aguirre <programador.manuel@gmail.com>
 */
class TranslationDataCollector extends DataCollector
{
    /**
     * @var DebugTranslator
     */
    private $debugTranslator;
    private $locales;

    function __construct($debugTranslator, $locales)
    {
        $this->debugTranslator = $debugTranslator;
        $this->locales = $locales;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request    $request   A Request instance
     * @param Response   $response  A Response instance
     * @param \Exception $exception An Exception instance
     *
     * @api
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $total = 0;

        foreach ($this->debugTranslator->getMissingTranslations() as $domain => $items) {
            $total += count($items);
        }

        $this->data = array(
            'trans' => $this->debugTranslator->getMissingTranslations(),
            'count' => $total,
            'locales' => $this->locales,
        );
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     *
     * @api
     */
    public function getName()
    {
        return 'manuel_translations';
    }

    public function getTranslations()
    {
        return $this->data['trans'];
    }

    public function getLocales()
    {
        return $this->data['locales'];
    }

    public function getCount()
    {
        return $this->data['count'];
    }

    public function isLocalhost()
    {
        static $localhost = null;

        if(null !== $localhost){
            return $localhost;
        }

        return $localhost = !isset($_SERVER['HTTP_CLIENT_IP'])
            && !isset($_SERVER['HTTP_X_FORWARDED_FOR'])
            && in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1')) 
            && php_sapi_name() !== 'cli-server';   
    }

    public function reset()
    {
        $this->data = [];
    }

}