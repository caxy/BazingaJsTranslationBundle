<?php

namespace Bazinga\ExposeTranslationBundle\Finder;

use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Finder\Finder;

use Symfony\Component\Translation\MessageCatalogue,
    Symfony\Component\DependencyInjection\ContainerAwareInterface,
    Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * @author William DURAND <william.durand1@gmail.com>
 * @author Markus Poerschke <markus@eluceo.de>
 */
class TranslationFinder implements ContainerAwareInterface
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * Default constructor.
     * @param KernelInterface $kernel The kernel.
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Returns an array of translation files for a given domain and a given locale.
     *
     * @param  string $domainName A domain translation name.
     * @param  string $locale     A locale.
     * @return array  An array of translation files.
     */
    public function getResources($domainName, $locale)
    {
        $finder = new Finder();

        return $finder->files()->name($domainName . '.' . $locale . '.*')->followLinks()->in($this->getLocations());
    }

    /**
     * Returns an array of all translation files .
     *
     * @return array  An array of translation files.
     */
    public function getAllResources()
    {
        $finder = new Finder();

        return $finder->files()->followLinks()->in($this->getLocations());
    }

    /**
     * Returns an array of (unique) locales and their fallback.
     *
     * @param  array $locales An array of locales.
     * @return array An array of unique locales.
     */
    public function createLocalesArray(array $locales)
    {
        $returnLocales = array();

        foreach ($locales as $locale) {
            if (empty($locale)) {
                continue;
            }

            if (strpos($locale, '_') === 2 && strlen($locale) === 5) {
                $returnLocales[] = substr($locale, 0, 2);
            }

            $returnLocales[] = $locale;
        }

        return array_values(array_unique($returnLocales));
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Gets the Container.
     *
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Gets translation files location.
     *
     * @return array
     */
    private function getLocations()
    {
        foreach ($this->kernel->getBundles() as $bundle) {
            if (is_dir($bundle->getPath() . '/Resources/translations')) {
                $locations[] = $bundle->getPath() . '/Resources/translations';
            }
        }

        if (is_dir($this->kernel->getRootDir() . '/Resources/translations')) {
            $locations[] = $this->kernel->getRootDir() . '/Resources/translations';
        }

        return $locations;
    }
}